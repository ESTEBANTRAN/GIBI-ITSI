#!/usr/bin/env node

/**
 * OpenCode Bridge — Freebuff ↔ OpenCode Integration
 * 
 * Se conecta al servidor OpenCode en http://127.0.0.1:4096
 * y envía prompts a los modelos AI configurados.
 * 
 * Usage:
 *   node bridge.js "tu prompt aquí"              # prompt directo
 *   node bridge.js --model codestral "prompt"    # modelo específico
 *   node bridge.js --stdin                       # prompt desde stdin
 *   node bridge.js --list-sessions               # listar sesiones
 *   node bridge.js --list-models                  # listar modelos
 */

import { createOpencodeClient } from '@opencode-ai/sdk';

const OPENCODE_API_URL = 'http://127.0.0.1:4096';
const TIMEOUT_MS = 180_000;
const DEFAULT_MODEL = 'opencode/deepseek-v4-flash-free';

async function main() {
  const args = process.argv.slice(2);
  
  if (args.length === 0) {
    console.log(JSON.stringify({ success: false, error: 'Uso: node bridge.js "prompt"' }));
    process.exit(1);
  }

  const client = createOpencodeClient({ baseUrl: OPENCODE_API_URL });

  // --- LISTAR SESIONES ---
  if (args[0] === '--list-sessions') {
    try {
      const { data } = await client.session.list();
      console.log(JSON.stringify({ success: true, sessions: data }, null, 2));
    } catch (err) {
      console.log(JSON.stringify({ success: false, error: err.message }));
    }
    return;
  }

  // --- LISTAR MODELOS ---
  if (args[0] === '--list-models') {
    try {
      // Intentar obtener modelos de la sesión por defecto
      const { data: session } = await client.session.create({ body: { title: 'model-check' } });
      const models = session?.models || [];
      console.log(JSON.stringify({ success: true, defaultModel: session?.model, models }, null, 2));
    } catch (err) {
      console.log(JSON.stringify({ success: false, error: err.message }));
    }
    return;
  }

  // --- CLEANUP ---
  if (args[0] === '--cleanup') {
    try {
      const { data: sessions } = await client.session.list();
      const deleted = [];
      for (const s of (sessions || [])) {
        await client.session.delete({ path: { id: s.id } });
        deleted.push(s.id);
      }
      console.log(JSON.stringify({ success: true, deleted }));
    } catch (err) {
      console.log(JSON.stringify({ success: false, error: err.message }));
    }
    return;
  }

  // --- PARSEAR ARGUMENTOS ---
  let model = DEFAULT_MODEL;
  let promptArgs = args;

  if (args[0] === '--model' && args.length > 1) {
    model = args[1];
    promptArgs = args.slice(2);
  }

  let prompt = promptArgs.join(' ');
  if (promptArgs[0] === '--stdin') {
    const chunks = [];
    for await (const chunk of process.stdin) chunks.push(chunk);
    prompt = Buffer.concat(chunks).toString('utf-8').trim();
  }

  if (!prompt) {
    console.log(JSON.stringify({ success: false, error: 'Prompt vacío' }));
    process.exit(1);
  }

  // --- EJECUTAR PROMPT ---
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), TIMEOUT_MS);

  try {
    // 1. Crear sesión con modelo específico
    const { data: session } = await client.session.create({
      body: { 
        title: `Bridge: ${prompt.substring(0, 80)}`,
        model: model,
      },
    });
    const sessionId = session.id;

    // 2. Inicializar la sesión (opcional - analiza el proyecto)
    try {
      await client.session.init({ path: { id: sessionId }, body: {} });
    } catch (e) {
      // init puede fallar si no hay AGENTS.md, continuamos
    }

    // 3. Enviar prompt
    const { data: result } = await client.session.prompt({
      path: { id: sessionId },
      body: {
        parts: [{ type: 'text', text: prompt }],
      },
    });

    // 4. Esperar procesamiento y obtener mensajes
    await new Promise(r => setTimeout(r, 5000));

    // 5. Obtener mensajes de la sesión
    const { data: msgsData } = await client.session.messages({
      path: { id: sessionId },
    });

    const messages = msgsData?.messages || result?.messages || [];
    const assistantMsgs = messages.filter(m => m.role === 'assistant');
    const lastAssistant = assistantMsgs[assistantMsgs.length - 1];

    let responseText = '';
    const toolsUsed = [];

    if (lastAssistant?.parts) {
      for (const part of lastAssistant.parts) {
        if (part.type === 'text') responseText += part.text + '\n';
        if (part.type === 'tool') {
          toolsUsed.push({
            name: part.name,
            status: part.state?.status,
            output: part.state?.output?.substring(0, 500),
          });
        }
      }
    }

    const output = {
      success: true,
      sessionId,
      model: session?.model || model,
      response: responseText.trim() || '(sin respuesta de texto)',
      tools: toolsUsed,
      messageCount: messages.length,
      tokenUsage: lastAssistant?.token,
      cost: lastAssistant?.cost,
    };

    console.log(JSON.stringify(output, null, 2));

  } catch (err) {
    console.log(JSON.stringify({ 
      success: false, 
      error: err.message,
      code: err.code || 'UNKNOWN',
      stack: err.stack?.split('\n').slice(0, 3).join('\n'),
    }));
  } finally {
    clearTimeout(timeout);
  }
}

main();
