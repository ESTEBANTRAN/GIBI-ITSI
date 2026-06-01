#!/usr/bin/env node

/**
 * MCP Bridge — Freebuff (Codebuff) ↔ MCP Server Integration
 * 
 * Connects to MCP servers via stdio JSON-RPC and makes their tools 
 * available to Codebuff.
 * 
 * Usage:
 *   node mcp-bridge.js list <server-package> [server-args...]
 *   node mcp-bridge.js call <server-package> <tool-name> <json-args> [server-args...]
 *   node mcp-bridge.js mysql <query>
 *   node mcp-bridge.js filesystem <action> <path>
 *   node mcp-bridge.js ls <path>
 *   node mcp-bridge.js read <path>
 * 
 * Examples:
 *   node mcp-bridge.js list @modelcontextprotocol/server-filesystem C:/xampp/htdocs/ITSI
 *   node mcp-bridge.js call @modelcontextprotocol/server-filesystem read_file '{"path":"C:/xampp/htdocs/ITSI/AGENTS.md"}' C:/xampp/htdocs/ITSI
 *   node mcp-bridge.js mysql "SHOW TABLES"
 *   node mcp-bridge.js read C:/xampp/htdocs/ITSI/AGENTS.md
 *   node mcp-bridge.js ls C:/xampp/htdocs/ITSI/app/Controllers
 */

import { spawn } from 'child_process';

const MYSQL_SERVER = 'mcp-server-mysql';
const MYSQL_DSN = 'mysql://root:@127.0.0.1:3306/bienestar_estudiantil_db';
const FILESYSTEM_SERVER = '@modelcontextprotocol/server-filesystem';
const PROJECT_ROOT = 'C:/xampp/htdocs/ITSI';

/**
 * Connect to an MCP server via stdio, send JSON-RPC messages, and return responses.
 */
async function connectAndCall(serverPkg, serverArgs, method, params) {
  return new Promise((resolve, reject) => {
    const child = spawn('npx.cmd', ['-y', serverPkg, ...(serverArgs || [])], {
      shell: true,
      stdio: ['pipe', 'pipe', 'pipe'],
    });

    let stdoutBuffer = '';
    let stderrLog = '';
    let msgId = 1;
    const responses = [];
    let initialized = false;
    let timeout;

    const timeoutMs = 60000;
    timeout = setTimeout(() => {
      child.kill();
      reject(new Error(`Timeout after ${timeoutMs}ms waiting for MCP response`));
    }, timeoutMs);

    child.stdout.on('data', (data) => {
      stdoutBuffer += data.toString();
      const lines = stdoutBuffer.split('\n');
      stdoutBuffer = lines.pop() || '';

      for (const line of lines) {
        const trimmed = line.trim();
        if (!trimmed) continue;
        try {
          const parsed = JSON.parse(trimmed);
          responses.push(parsed);
        } catch (e) {
          // Skip non-JSON output (e.g. server startup messages)
        }
      }

      // If we got a response to our last request, process it
      checkDone();
    });

    child.stderr.on('data', (data) => {
      const msg = data.toString();
      stderrLog += msg;
      // Don't log stderr - MCP servers print startup messages there
    });

    child.on('error', (err) => {
      clearTimeout(timeout);
      reject(new Error(`Failed to spawn MCP server: ${err.message}`));
    });

    child.on('exit', (code) => {
      clearTimeout(timeout);
      // Process any remaining buffer
      if (stdoutBuffer.trim()) {
        try {
          const parsed = JSON.parse(stdoutBuffer.trim());
          responses.push(parsed);
        } catch (e) {}
      }
      checkDone(true);
    });

    function checkDone(force = false) {
      // Find the response matching our request ID
      const lastResponse = responses.find(r => r.id === msgId || r.id === msgId - 1);
      
      // Check if we have all the responses we need
      if (force || (lastResponse && (lastResponse.result !== undefined || lastResponse.error !== undefined))) {
        // We have the final response
        if (responses.some(r => r.id === 1 && (r.result || r.error))) {
          clearTimeout(timeout);
          child.kill();
          resolve({ responses, stderr: stderrLog });
        }
      }
    }

    // Wait for server to start, then send messages
    let stepCount = 0;
    
    function sendStep(delayMs) {
      stepCount++;
      setTimeout(() => {
        if (stepCount === 1) {
          const initMsg = JSON.stringify({
            jsonrpc: '2.0',
            id: msgId++,
            method: 'initialize',
            params: {
              protocolVersion: '0.1.0',
              capabilities: {},
              clientInfo: { name: 'codebuff-mcp-bridge', version: '1.0.0' },
            },
          }) + '\n';
          child.stdin.write(initMsg);
        } else if (stepCount === 2) {
          const notifMsg = JSON.stringify({
            jsonrpc: '2.0',
            method: 'notifications/initialized',
          }) + '\n';
          child.stdin.write(notifMsg);
        } else if (stepCount === 3 && method === 'tools/list') {
          const listMsg = JSON.stringify({
            jsonrpc: '2.0',
            id: msgId++,
            method: 'tools/list',
          }) + '\n';
          child.stdin.write(listMsg);
        } else if (stepCount === 3 && method === 'tools/call') {
          const callMsg = JSON.stringify({
            jsonrpc: '2.0',
            id: msgId++,
            method: 'tools/call',
            params: params,
          }) + '\n';
          child.stdin.write(callMsg);
          // Close stdin after sending the last message
          setTimeout(() => { child.stdin.end(); }, 1000);
        }
      }, delayMs);
    }
    
    sendStep(3000);  // Initialize at 3s
    sendStep(6000);  // Initialized notification at 6s
    sendStep(10000); // tools/list or tools/call at 10s
  });
}

/**
 * Extract the tool result content from MCP responses.
 */
function extractResult(responses) {
  // Find the last response with a result or error
  const lastResp = [...responses].reverse().find(r => r.result || r.error);
  if (!lastResp) {
    return { success: false, error: 'No response received from MCP server', raw: responses };
  }
  if (lastResp.error) {
    return { success: false, error: lastResp.error.message || JSON.stringify(lastResp.error), raw: responses };
  }
  
  const result = lastResp.result;
  
  // For tool calls, extract content
  if (result.content) {
    const textParts = result.content
      .filter(c => c.type === 'text')
      .map(c => c.text);
    const resourceParts = result.content
      .filter(c => c.type === 'resource')
      .map(c => ({ uri: c.resource?.uri, text: c.resource?.text, mimeType: c.resource?.mimeType }));
    
    return {
      success: true,
      text: textParts.join('\n'),
      resources: resourceParts,
      raw: responses,
    };
  }
  
  // For tools list
  if (result.tools) {
    return {
      success: true,
      tools: result.tools.map(t => ({
        name: t.name,
        description: t.description,
        inputSchema: t.inputSchema,
      })),
      raw: responses,
    };
  }
  
  return { success: true, data: result, raw: responses };
}

/**
 * Query MySQL database via MCP.
 */
async function queryMySQL(sql) {
  try {
    const result = await connectAndCall(MYSQL_SERVER, [MYSQL_DSN], 'tools/call', {
      name: 'execute_query',
      arguments: { query: sql },
    });
    return extractResult(result.responses);
  } catch (e) {
    return { success: false, error: `MySQL query failed: ${e.message}` };
  }
}

/**
 * Read a file via filesystem MCP.
 */
async function readFile(filepath) {
  try {
    const result = await connectAndCall(FILESYSTEM_SERVER, [PROJECT_ROOT], 'tools/call', {
      name: 'read_file',
      arguments: { path: filepath },
    });
    return extractResult(result.responses);
  } catch (e) {
    return { success: false, error: `Read file failed: ${e.message}` };
  }
}

/**
 * List directory via filesystem MCP.
 */
async function listDirectory(dirpath) {
  try {
    const result = await connectAndCall(FILESYSTEM_SERVER, [PROJECT_ROOT], 'tools/call', {
      name: 'list_directory',
      arguments: { path: dirpath },
    });
    return extractResult(result.responses);
  } catch (e) {
    return { success: false, error: `List directory failed: ${e.message}` };
  }
}

/**
 * Main CLI handler.
 */
async function main() {
  const args = process.argv.slice(2);
  
  if (args.length === 0) {
    console.log(JSON.stringify({
      success: false,
      error: 'Usage: node mcp-bridge.js <command> [args...]',
      commands: {
        list: 'List tools of an MCP server',
        call: 'Call a tool on an MCP server',
        mysql: 'Execute a MySQL query',
        read: 'Read a file',
        ls: 'List a directory',
      },
    }));
    process.exit(1);
  }

  const command = args[0];

  // ── Built-in shortcuts ──────────────────────────────────────────
  
  if (command === 'mysql') {
    const sql = args.slice(1).join(' ');
    if (!sql) {
      console.log(JSON.stringify({ success: false, error: 'No SQL query provided' }));
      process.exit(1);
    }
    const result = await queryMySQL(sql);
    console.log(JSON.stringify(result, null, 2));
    return;
  }

  if (command === 'read') {
    const filepath = args[1];
    if (!filepath) {
      console.log(JSON.stringify({ success: false, error: 'No file path provided' }));
      process.exit(1);
    }
    const result = await readFile(filepath);
    console.log(JSON.stringify(result, null, 2));
    return;
  }

  if (command === 'ls') {
    const dirpath = args[1] || PROJECT_ROOT;
    const result = await listDirectory(dirpath);
    console.log(JSON.stringify(result, null, 2));
    return;
  }

  // ── Generic MCP commands ────────────────────────────────────────

  if (command === 'list') {
    const serverPkg = args[1];
    const serverArgs = args.slice(2);
    if (!serverPkg) {
      console.log(JSON.stringify({ success: false, error: 'No server package specified' }));
      process.exit(1);
    }
    try {
      const result = await connectAndCall(serverPkg, serverArgs, 'tools/list');
      const extracted = extractResult(result.responses);
      console.log(JSON.stringify(extracted, null, 2));
    } catch (e) {
      console.log(JSON.stringify({ success: false, error: e.message }));
    }
    return;
  }

  if (command === 'call') {
    const serverPkg = args[1];
    const toolName = args[2];
    let toolArgs = {};
    const serverArgs = args.slice(4);
    
    if (!serverPkg || !toolName) {
      console.log(JSON.stringify({ success: false, error: 'Usage: call <server-pkg> <tool-name> [json-args] [server-args...]' }));
      process.exit(1);
    }
    
    // Parse tool arguments JSON (arg 3 is optional JSON)
    if (args[3]) {
      try {
        toolArgs = JSON.parse(args[3]);
      } catch (e) {
        // Not JSON, treat as first server arg
        serverArgs.unshift(args[3]);
      }
    }
    
    try {
      const result = await connectAndCall(serverPkg, serverArgs, 'tools/call', {
        name: toolName,
        arguments: toolArgs,
      });
      const extracted = extractResult(result.responses);
      console.log(JSON.stringify(extracted, null, 2));
    } catch (e) {
      console.log(JSON.stringify({ success: false, error: e.message }));
    }
    return;
  }

  // Unknown command
  console.log(JSON.stringify({
    success: false,
    error: `Unknown command: ${command}`,
    usage: 'Try: node mcp-bridge.js mysql "SELECT 1" | read <path> | ls <path> | list <server-pkg> | call <server-pkg> <tool> [args]',
  }));
}

main().catch(e => {
  console.log(JSON.stringify({ success: false, error: e.message }));
  process.exit(1);
});
