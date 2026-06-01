#!/usr/bin/env node

/**
 * OpenCode Orchestrator — Unifies OpenCode API + MCP Bridge + MySQL
 * 
 * This is the MAIN script Codebuff uses to interact with OpenCode.
 * 
 * Usage:
 *   node opencode.js session:create
 *   node opencode.js session:list
 *   node opencode.js mysql <sql-query>
 *   node opencode.js file:read <path>
 *   node opencode.js file:ls <path>
 *   node opencode.js mcp:list <server-pkg> [args...]
 *   node opencode.js mcp:call <server-pkg> <tool> <json-args> [server-args...]
 *   node opencode.js status
 *   node opencode.js help
 */

import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import { spawn } from 'child_process';
import { createRequire } from 'module';

const __dirname = dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);

const OPENCODE_API = 'http://127.0.0.1:4096';
const PROJECT_ROOT = 'C:/xampp/htdocs/ITSI';

const args = process.argv.slice(2);
const cmd = args[0];

// ─── Helpers ──────────────────────────────────────────────────────

function runScript(scriptName, scriptArgs, timeoutMs = 70000) {
  return new Promise((resolve, reject) => {
    const child = spawn('node', [join(__dirname, scriptName), ...scriptArgs], {
      stdio: ['pipe', 'pipe', 'pipe'],
      shell: true,
    });
    let stdout = '';
    let stderr = '';
    const timeout = setTimeout(() => {
      child.kill();
      resolve({ success: false, error: `Timeout after ${timeoutMs}ms`, stderr });
    }, timeoutMs);
    child.stdout.on('data', d => stdout += d.toString());
    child.stderr.on('data', d => stderr += d.toString());
    child.on('close', code => {
      clearTimeout(timeout);
      try {
        const parsed = JSON.parse(stdout);
        resolve(parsed);
      } catch {
        resolve({ success: code === 0, raw: stdout, stderr, exitCode: code });
      }
    });
    child.on('error', e => { clearTimeout(timeout); reject(e); });
  });
}

async function api(method, path, body) {
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json' },
  };
  if (body) opts.body = JSON.stringify(body);
  const res = await fetch(`${OPENCODE_API}${path}`, opts);
  const text = await res.text();
  try { return JSON.parse(text); } catch { return text; }
}

// ─── MySQL Direct (via mysql2) ────────────────────────────────────

async function queryMySQL(sql) {
  try {
    const mysql2 = require('mysql2/promise');
    const conn = await mysql2.createConnection({
      host: '127.0.0.1',
      user: 'root',
      password: '',
      database: 'bienestar_estudiantil_db',
      port: 3306,
      connectTimeout: 10000,
    });
    const [rows, fields] = await conn.execute(sql);
    await conn.end();
    return {
      success: true,
      rows: rows.length,
      total: rows.length,
      data: rows.slice(0, 50),
      truncated: rows.length > 50,
      fields: fields.map(f => f.name),
      sql,
    };
  } catch (e) {
    return { success: false, error: e.message, sql };
  }
}

// ─── MCP Bridge (via mcp-bridge.js) ──────────────────────────────

async function mcpCommand(subCmd, ...mcpArgs) {
  return runScript('mcp-bridge.js', [subCmd, ...mcpArgs]);
}

// ─── Generic AI Caller (provider-agnostic) ────────────────────────

const AI_PROVIDERS = {
  groq: {
    hostname: 'api.groq.com',
    path: '/openai/v1/chat/completions',
    modelsPath: '/openai/v1/models',
    defaultModel: 'llama-3.3-70b-versatile',
    envKey: 'GROQ_API_KEY',
    envModel: 'GROQ_MODEL',
  },
  deepseek: {
    hostname: 'api.deepseek.com',
    path: '/v1/chat/completions',
    defaultModel: 'deepseek-chat',
    envKey: 'DEEPSEEK_API_KEY',
    envModel: 'DEEPSEEK_MODEL',
  },
};

async function callAIProvider(provider, prompt, options = {}) {
  const config = AI_PROVIDERS[provider];
  if (!config) return { success: false, error: `Unknown provider: ${provider}` };

  const apiKey = process.env[config.envKey];
  if (!apiKey) {
    return { success: false, error: `${config.envKey} no configurada. Ejecuta: export ${config.envKey}=tu_key` };
  }

  const model = options.model || process.env[config.envModel] || config.defaultModel;
  const maxTokens = options.maxTokens || 1024;
  const temperature = options.temperature ?? 0.7;

  try {
    const https = require('https');
    const body = JSON.stringify({
      model,
      messages: [
        { role: 'system', content: options.systemPrompt || 'Eres un asistente de codificación experto. Responde de manera concisa y directa.' },
        { role: 'user', content: prompt }
      ],
      max_tokens: maxTokens,
      temperature,
    });

    const response = await new Promise((resolve, reject) => {
      const req = https.request({
        hostname: config.hostname,
        path: config.path,
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${apiKey}`,
          'Content-Length': Buffer.byteLength(body),
        },
        timeout: 60000,
      }, res => {
        let data = '';
        res.on('data', d => data += d);
        res.on('end', () => {
          try { resolve(JSON.parse(data)); }
          catch { resolve({ error: data.substring(0, 500) }); }
        });
      });
      req.on('error', reject);
      req.on('timeout', () => { req.destroy(); reject(new Error(`${provider} API timeout after 60s`)); });
      req.write(body);
      req.end();
    });

    if (response.error) {
      const errMsg = response.error.message || JSON.stringify(response.error);
      // Friendly messages for common DeepSeek errors
      if (errMsg.includes('Insufficient Balance')) {
        return { success: false, error: 'DeepSeek: saldo insuficiente. Agrega fondos en https://platform.deepseek.com', provider };
      }
      if (errMsg.includes('invalid') || errMsg.includes('Authentication Fails')) {
        return { success: false, error: `DeepSeek: API key inválida. Verifica en https://platform.deepseek.com/api_keys`, provider };
      }
      return { success: false, error: errMsg };
    }

    const content = response.choices?.[0]?.message?.content || '';
    const usage = response.usage || {};

    return {
      success: true,
      provider,
      model: response.model,
      response: content,
      usage: {
        prompt: usage.prompt_tokens || 0,
        completion: usage.completion_tokens || 0,
        total: usage.total_tokens || 0,
      },
      finishReason: response.choices?.[0]?.finish_reason,
    };
  } catch (e) {
    return { success: false, error: `${provider} API error: ${e.message}` };
  }
}

async function callAIProviderChat(provider, messages, options = {}) {
  const config = AI_PROVIDERS[provider];
  if (!config) return { success: false, error: `Unknown provider: ${provider}` };

  const apiKey = process.env[config.envKey];
  if (!apiKey) {
    return { success: false, error: `${config.envKey} no configurada` };
  }

  const model = options.model || process.env[config.envModel] || config.defaultModel;
  const maxTokens = options.maxTokens || 2048;
  const temperature = options.temperature ?? 0.7;

  try {
    const https = require('https');
    const body = JSON.stringify({ model, messages, max_tokens: maxTokens, temperature });

    const response = await new Promise((resolve, reject) => {
      const req = https.request({
        hostname: config.hostname, path: config.path, method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${apiKey}`,
          'Content-Length': Buffer.byteLength(body),
        },
        timeout: 120000,
      }, res => {
        let data = '';
        res.on('data', d => data += d);
        res.on('end', () => {
          try { resolve(JSON.parse(data)); } catch { resolve({ error: data.substring(0, 500) }); }
        });
      });
      req.on('error', reject);
      req.on('timeout', () => { req.destroy(); reject(new Error(`${provider} API timeout`)); });
      req.write(body); req.end();
    });

    if (response.error) return { success: false, error: response.error.message || JSON.stringify(response.error) };

    return {
      success: true,
      provider,
      model: response.model,
      response: response.choices?.[0]?.message?.content || '',
      finishReason: response.choices?.[0]?.finish_reason,
    };
  } catch (e) {
    return { success: false, error: `${provider} API error: ${e.message}` };
  }
}

// ─── Convenience wrappers (keep backward compat) ──────────────────

async function groqPrompt(prompt, options = {}) {
  return callAIProvider('groq', prompt, options);
}

async function groqChat(messages, options = {}) {
  return callAIProviderChat('groq', messages, options);
}

async function deepseekPrompt(prompt, options = {}) {
  return callAIProvider('deepseek', prompt, options);
}

async function deepseekChat(messages, options = {}) {
  return callAIProviderChat('deepseek', messages, options);
}

// ─── Help ─────────────────────────────────────────────────────────

function showHelp() {
  console.log(JSON.stringify({
    description: 'OpenCode Orchestrator — Codebuff ↔ OpenCode + Groq AI + DeepSeek',
    commands: {
      'ai <prompt>': 'Send prompt to default AI provider (Groq, or set DEFAULT_AI_PROVIDER=deepseek)',
      'ai:chat <json>': 'Send chat messages to default AI provider',
      'groq <prompt>': 'Send a prompt to Groq AI (Llama 3.3 70B)',
      'groq:chat <json-messages>': 'Send chat messages to Groq (advanced)',
      'groq:models': 'List available Groq models',
      'deepseek <prompt>': 'Send a prompt to DeepSeek AI (deepseek-chat)',
      'deepseek:chat <json-messages>': 'Send chat messages to DeepSeek (advanced)',
      'deepseek:models': 'Check DeepSeek connectivity (no public /models endpoint)',
      'session:create [title]': 'Create a new OpenCode session',
      'session:list': 'List OpenCode sessions',
      'session:get <id>': 'Get session details',
      'session:delete <id>': 'Delete a session',
      'mysql <sql>': 'Execute MySQL query against bienestar_estudiantil_db',
      'file:ls <path>': 'List a directory via MCP filesystem',
      'file:read <path>': 'Read a file via MCP filesystem',
      'prompt <session-id> <text>': 'Send a prompt to OpenCode session',
      'mcp:list <server>': 'List tools of an MCP server',
      'mcp:call <server> <tool>': 'Call a tool on an MCP server',
      'status': 'Check all services (API, MySQL, Groq, DeepSeek)',
    },
    examples: [
      'node opencode.js ai "Explica este código"',
      'node opencode.js groq "Escribe un SQL query"',
      'node opencode.js deepseek "Analiza este controlador"',
      'node opencode.js mysql "SHOW TABLES"',
      'node opencode.js file:read app/Controllers/AuthController.php',
      'node opencode.js status',
    ],
  }), null, 2);
}

// ─── Main ─────────────────────────────────────────────────────────

async function main() {
  if (!cmd || cmd === 'help' || cmd === '--help') {
    showHelp();
    return;
  }

  switch (cmd) {
    // ── Session Management (via OpenCode API) ────────────────────
    case 'session:create': {
      const title = args[1] || `Codebuff-${Date.now()}`;
      try {
        const result = await api('POST', '/session', { title });
        const session = result.data || result;
        console.log(JSON.stringify({
          success: true,
          session: {
            id: session.id,
            slug: session.slug,
            title: session.title,
            version: session.version,
          },
        }, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'session:list': {
      try {
        const result = await api('GET', '/session');
        const sessions = result?.data || result || [];
        const list = (Array.isArray(sessions) ? sessions : []).map(s => ({
          id: s.id, title: s.title, slug: s.slug, cost: s.cost,
        }));
        console.log(JSON.stringify({ success: true, sessions: list, count: list.length }, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'session:get': {
      const id = args[1];
      if (!id) { console.log(JSON.stringify({ success: false, error: 'Session ID required' })); return; }
      try {
        const result = await api('GET', `/session/${id}`);
        console.log(JSON.stringify({ success: true, session: result.data || result }, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'session:delete': {
      const id = args[1];
      if (!id) { console.log(JSON.stringify({ success: false, error: 'Session ID required' })); return; }
      try {
        const result = await api('DELETE', `/session/${id}`);
        console.log(JSON.stringify({ success: true, deleted: true }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    // ── MySQL Queries ────────────────────────────────────────────
    case 'mysql': {
      const sql = args.slice(1).join(' ');
      if (!sql) { console.log(JSON.stringify({ success: false, error: 'SQL query required' })); return; }
      const result = await queryMySQL(sql);
      console.log(JSON.stringify(result, null, 2));
      break;
    }

    // ── File Operations (via MCP) ────────────────────────────────
    case 'file:ls': {
      const dirpath = args[1] || PROJECT_ROOT;
      const result = await mcpCommand('ls', dirpath);
      // Simplify output
      if (result.success && result.text) {
        const lines = result.text.split('\n').filter(l => l.trim());
        console.log(JSON.stringify({ success: true, directory: dirpath, entries: lines.length, items: lines.slice(0, 50) }, null, 2));
      } else {
        console.log(JSON.stringify(result, null, 2));
      }
      break;
    }

    case 'file:read': {
      const filepath = args[1];
      if (!filepath) { console.log(JSON.stringify({ success: false, error: 'File path required' })); return; }
      const result = await mcpCommand('read', filepath);
      console.log(JSON.stringify(result, null, 2));
      break;
    }

    // ── Prompt (via OpenCode API) ────────────────────────────────
    case 'prompt': {
      const sessionId = args[1];
      const text = args.slice(2).join(' ');
      if (!sessionId || !text) {
        console.log(JSON.stringify({ success: false, error: 'Session ID and prompt text required' }));
        return;
      }
      try {
        const result = await api('POST', `/session/${sessionId}/prompt`, {
          parts: [{ type: 'text', text }],
        });
        console.log(JSON.stringify({ success: true, sent: true, sessionId, result: result.data || result }, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    // ── MCP Generic ─────────────────────────────────────────────
    case 'mcp:list': {
      const serverPkg = args.slice(1).join(' ');
      if (!serverPkg) { console.log(JSON.stringify({ success: false, error: 'MCP server package required' })); return; }
      const result = await mcpCommand('list', ...args.slice(1));
      console.log(JSON.stringify(result, null, 2));
      break;
    }

    case 'mcp:call': {
      const [, serverPkg, toolName, ...rest] = args;
      if (!serverPkg || !toolName) {
        console.log(JSON.stringify({ success: false, error: 'Server package and tool name required' }));
        return;
      }
      const result = await mcpCommand('call', serverPkg, toolName, ...rest);
      console.log(JSON.stringify(result, null, 2));
      break;
    }

    // ── Groq AI ──────────────────────────────────────────────────
    case 'groq': {
      const prompt = args.slice(1).join(' ');
      if (!prompt) { console.log(JSON.stringify({ success: false, error: 'Prompt text required' })); return; }
      const result = await groqPrompt(prompt);
      console.log(JSON.stringify(result, null, 2));
      break;
    }

    case 'groq:chat': {
      const messagesJson = args[1];
      if (!messagesJson) { console.log(JSON.stringify({ success: false, error: 'JSON messages required' })); return; }
      try {
        const messages = JSON.parse(messagesJson);
        const result = await groqChat(messages);
        console.log(JSON.stringify(result, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: `Invalid JSON: ${e.message}` }));
      }
      break;
    }

    case 'groq:models': {
      const groqKey = process.env.GROQ_API_KEY;
      if (!groqKey) {
        console.log(JSON.stringify({ success: false, error: 'GROQ_API_KEY no configurada. Ejecuta: export GROQ_API_KEY=tu_key' }));
        return;
      }
      try {
        const https = require('https');
        const data = await new Promise((resolve, reject) => {
          const req = https.get({
            hostname: 'api.groq.com', path: '/openai/v1/models',
            headers: { 'Authorization': `Bearer ${groqKey}` },
            timeout: 15000,
          }, res => {
            let d = '';
            res.on('data', chunk => d += chunk);
            res.on('end', () => { try { resolve(JSON.parse(d)); } catch { resolve({ error: d }); } });
          });
          req.on('error', reject);
        });
        const models = (data.data || []).map(m => m.id).filter(id => !id.includes('whisper'));
        console.log(JSON.stringify({ success: true, models, count: models.length }, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    // ── DeepSeek AI ────────────────────────────────────────────────
    case 'deepseek': {
      const dsPrompt = args.slice(1).join(' ');
      if (!dsPrompt) { console.log(JSON.stringify({ success: false, error: 'Prompt text required' })); return; }
      const dsResult = await deepseekPrompt(dsPrompt);
      console.log(JSON.stringify(dsResult, null, 2));
      break;
    }

    case 'deepseek:chat': {
      const dsMessagesJson = args[1];
      if (!dsMessagesJson) { console.log(JSON.stringify({ success: false, error: 'JSON messages required' })); return; }
      try {
        const dsMessages = JSON.parse(dsMessagesJson);
        const dsChatResult = await deepseekChat(dsMessages);
        console.log(JSON.stringify(dsChatResult, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: `Invalid JSON: ${e.message}` }));
      }
      break;
    }

    case 'deepseek:models': {
      const dsKey = process.env.DEEPSEEK_API_KEY;
      if (!dsKey) {
        console.log(JSON.stringify({ success: false, error: 'DEEPSEEK_API_KEY no configurada. Ejecuta: export DEEPSEEK_API_KEY=tu_key' }));
        return;
      }
      console.log(JSON.stringify({
        success: true,
        note: 'DeepSeek no expone endpoint público /models. Modelos recomendados:',
        models: ['deepseek-chat (deepseek-v4-flash)', 'deepseek-reasoner (deepseek-v4-flash razonamiento)'],
        config: 'Usar DEEPSEEK_MODEL=deepseek-reasoner para cambiar modelo',
      }, null, 2));
      break;
    }

    // ── Generic AI (provider-agnostic) ────────────────────────────
    case 'ai': {
      // Parse optional --provider=deepseek flag
      const providerFlag = args.find(a => a.startsWith('--provider='))?.split('=')[1]
        || process.env.DEFAULT_AI_PROVIDER || 'groq';
      const aiPrompt = args.filter(a => !a.startsWith('--')).slice(1).join(' ');
      if (!aiPrompt) { console.log(JSON.stringify({ success: false, error: 'Prompt text required' })); return; }
      const aiResult = await callAIProvider(providerFlag, aiPrompt);
      console.log(JSON.stringify(aiResult, null, 2));
      break;
    }

    case 'ai:chat': {
      const providerFlag = args.find(a => a.startsWith('--provider='))?.split('=')[1]
        || process.env.DEFAULT_AI_PROVIDER || 'groq';
      const nonFlagArgs = args.filter(a => !a.startsWith('--'));
      const aiMessagesJson = nonFlagArgs[1];
      if (!aiMessagesJson) { console.log(JSON.stringify({ success: false, error: 'JSON messages required' })); return; }
      try {
        const aiMessages = JSON.parse(aiMessagesJson);
        const aiChatResult = await callAIProviderChat(providerFlag, aiMessages);
        console.log(JSON.stringify(aiChatResult, null, 2));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: `Invalid JSON: ${e.message}` }));
      }
      break;
    }

    // ── Status ────────────────────────────────────────────────────
    case 'status': {
      const results = {};

      // Check OpenCode API
      try {
        const apiResult = await api('GET', '/session');
        results.api = { alive: true, sessions: Array.isArray(apiResult?.data || apiResult) ? (apiResult.data || apiResult).length : 0 };
      } catch (e) {
        results.api = { alive: false, error: e.message };
      }

      // Check MCPs
      try {
        const mcpResult = await api('GET', '/mcp');
        if (typeof mcpResult === 'object' && mcpResult !== null) {
          results.mcp = {};
          for (const [name, status] of Object.entries(mcpResult)) {
            results.mcp[name] = status?.status || 'unknown';
          }
          results.mcp._note = 'MCPs del API server (usar mcp-bridge.js para acceso directo: node opencode.js file:read/fs)';
        }
      } catch (e) {
        results.mcp = { error: e.message, _note: 'Usar mcp-bridge.js para acceso directo a MCPs' };
      }

      // Check MySQL
      try {
        const mysql2 = require('mysql2/promise');
        const conn = await mysql2.createConnection({
          host: '127.0.0.1', user: 'root', password: '',
          database: 'bienestar_estudiantil_db', port: 3306,
          connectTimeout: 5000,
        });
        await conn.execute('SELECT 1');
        await conn.end();
        results.mysql = { alive: true };
      } catch (e) {
        results.mysql = { alive: false, error: e.message };
      }

      // Check Groq
      try {
        const https = require('https');
        const groqKeyExists = !!process.env.GROQ_API_KEY;
        if (groqKeyExists) {
          const data = await new Promise((resolve, reject) => {
            const req = https.get({
              hostname: 'api.groq.com', path: '/openai/v1/models',
              headers: { 'Authorization': `Bearer ${process.env.GROQ_API_KEY}` },
              timeout: 10000,
            }, res => {
              let d = '';
              res.on('data', chunk => d += chunk);
              res.on('end', () => { try { resolve(JSON.parse(d)); } catch { resolve(null); } });
            });
            req.on('error', () => resolve(null));
          });
          results.groq = { alive: !!data?.data, models: data?.data?.length || 0 };
        } else {
          results.groq = { alive: false, error: 'No GROQ_API_KEY in environment' };
        }
      } catch (e) {
        results.groq = { alive: false, error: e.message };
      }

      // Check DeepSeek
      const dsKeyExists = !!process.env.DEEPSEEK_API_KEY;
      if (dsKeyExists) {
        try {
          const https = require('https');
          const testResult = await new Promise((resolve) => {
            const body = JSON.stringify({ model: 'deepseek-chat', messages: [{ role: 'user', content: 'test' }], max_tokens: 1 });
            const req = https.request({
              hostname: 'api.deepseek.com', path: '/v1/chat/completions', method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${process.env.DEEPSEEK_API_KEY}`,
                'Content-Length': Buffer.byteLength(body),
              },
              timeout: 10000,
            }, res => {
              let d = '';
              res.on('data', chunk => d += chunk);
              res.on('end', () => {
                try {
                  const parsed = JSON.parse(d);
                  if (parsed.error) resolve({ error: parsed.error.message });
                  else resolve({ alive: true });
                } catch { resolve({ error: 'Parse error' }); }
              });
            });
            req.on('error', () => resolve({ error: 'Connection failed' }));
            req.on('timeout', () => { req.destroy(); resolve({ error: 'Timeout' }); });
            req.write(body); req.end();
          });
          results.deepseek = {
            alive: !!testResult.alive,
            keyConfigured: true,
            model: process.env.DEEPSEEK_MODEL || 'deepseek-chat (default)',
            error: testResult.error || null,
          };
        } catch (e) {
          results.deepseek = { alive: false, keyConfigured: true, error: e.message };
        }
      } else {
        results.deepseek = { alive: false, keyConfigured: false, error: 'No DEEPSEEK_API_KEY in environment' };
      }

      // Default provider info
      results.defaultAI = process.env.DEFAULT_AI_PROVIDER || 'groq';

      console.log(JSON.stringify({ success: true, status: results }, null, 2));
      break;
    }

    default:
      console.log(JSON.stringify({
        success: false, error: `Unknown command: ${cmd}`,
        hint: 'Try: node opencode.js help',
      }, null, 2));
  }
}

main().catch(e => console.log(JSON.stringify({ success: false, error: e.message })));
