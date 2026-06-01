#!/usr/bin/env node

/**
 * OpenCode API Bridge — Connects Codebuff to the OpenCode API on port 4096.
 * 
 * Usage:
 *   node opencode-api.js session:create [title]
 *   node opencode-api.js session:list
 *   node opencode-api.js session:get <id>
 *   node opencode-api.js session:delete <id>
 *   node opencode-api.js prompt <session-id> <text>
 *   node opencode-api.js messages <session-id>
 *   node opencode-api.js status
 *   node opencode-api.js check-mcp
 */

const BASE = 'http://127.0.0.1:4096';

const args = process.argv.slice(2);
const cmd = args[0];

async function api(method, path, body) {
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json' },
  };
  if (body) opts.body = JSON.stringify(body);
  const res = await fetch(`${BASE}${path}`, opts);
  const text = await res.text();
  try { return JSON.parse(text); } catch { return text; }
}

async function main() {
  if (!cmd || cmd === '--help' || cmd === 'help') {
    console.log(JSON.stringify({
      commands: {
        'session:create [title]': 'Create a new OpenCode session',
        'session:list': 'List all sessions',
        'session:get <id>': 'Get session details',
        'session:delete <id>': 'Delete a session',
        'prompt <session-id> <text>': 'Send a prompt to a session',
        'messages <session-id>': 'Get session messages',
        'status': 'Check API server status',
        'check-mcp': 'Check MCP server status',
      }
    }));
    return;
  }

  switch (cmd) {
    case 'status': {
      // Check if the API is alive
      try {
        const sessions = await api('GET', '/session');
        console.log(JSON.stringify({ success: true, apiAlive: true, sessions: Array.isArray(sessions) ? sessions.length : 'unknown' }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: `API not reachable: ${e.message}` }));
      }
      break;
    }

    case 'session:create': {
      const title = args[1] || `Codebuff-Bridge-${Date.now()}`;
      try {
        const result = await api('POST', '/session', { title });
        console.log(JSON.stringify({ success: true, session: result.data || result }));
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
          id: s.id, title: s.title, slug: s.slug,
          created: s.time?.created,
          cost: s.cost,
        }));
        console.log(JSON.stringify({ success: true, sessions: list, count: list.length }));
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
        console.log(JSON.stringify({ success: true, session: result.data || result }));
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
        console.log(JSON.stringify({ success: true, result: result.data || result }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'prompt': {
      const id = args[1];
      const text = args.slice(2).join(' ');
      if (!id || !text) { console.log(JSON.stringify({ success: false, error: 'Session ID and prompt text required' })); return; }
      try {
        const result = await api('POST', `/session/${id}/prompt`, {
          parts: [{ type: 'text', text }],
        });
        console.log(JSON.stringify({ success: true, result: result.data || result }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'messages': {
      const id = args[1];
      if (!id) { console.log(JSON.stringify({ success: false, error: 'Session ID required' })); return; }
      try {
        const result = await api('GET', `/session/${id}/messages`);
        console.log(JSON.stringify({ success: true, messages: result.data?.messages || result?.messages || result }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    case 'check-mcp': {
      try {
        const result = await api('GET', '/mcp');
        const mcpStatus = {};
        if (typeof result === 'object' && result !== null) {
          for (const [name, status] of Object.entries(result)) {
            mcpStatus[name] = status?.status || 'unknown';
          }
        }
        console.log(JSON.stringify({ success: true, mcp: mcpStatus, raw: result }));
      } catch (e) {
        console.log(JSON.stringify({ success: false, error: e.message }));
      }
      break;
    }

    default:
      console.log(JSON.stringify({ success: false, error: `Unknown command: ${cmd}` }));
  }
}

main().catch(e => console.log(JSON.stringify({ success: false, error: e.message })));
