# Agentes creados para ITSI — Sistema de Bienestar Estudiantil

## Resumen del análisis

### Problemas críticos de seguridad
| # | Problema | Archivo | Gravedad |
|---|----------|---------|----------|
| 1 | `credenciales.txt` con contraseñas en texto plano | `/credenciales.txt` | **CRÍTICO** |
| 2 | CSRF deshabilitado ("DESHABILITADO TEMPORALMENTE") | `app/Config/Filters.php:78` | **CRÍTICO** |
| 3 | Clave de cifrado vacía (`$key = ''`) | `app/Config/Encryption.php:24` | **CRÍTICO** |
| 4 | HTTPS no forzado | `app/Config/App.php:160` | **ALTO** |
| 5 | CORS sin restricciones | `app/Config/Cors.php` | **ALTO** |
| 6 | Consultas SQL con concatenación de variables (8 sitios) | `AdminBienestarController.php:1053,2621,2636,2961,3162,3449,3782,4373` | **ALTO** |
| 7 | SMTP con placeholders (`tu-email@gmail.com`) | `app/Config/Email.php:36-41` | **MEDIO** |
| 8 | Google Drive client_secret expuesto en vistas | `app/Views/*/configuracion_sistema.php:143-145` | **MEDIO** |
| 9 | mysqldump con password en línea de comandos | `app/Models/GlobalAdmin/BackupModel.php:52,124` | **MEDIO** |

### Problemas de código y arquitectura
| # | Problema | Archivo | Impacto |
|---|----------|---------|---------|
| 1 | God Class: 4105 líneas, ~50 responsabilidades | `app/Controllers/AdminBienestarController.php` | Mantenibilidad |
| 2 | God Class: 2271 líneas | `app/Controllers/GlobalAdmin/GlobalAdminController.php` | Mantenibilidad |
| 3 | God Class: 1245 líneas | `app/Controllers/EstudianteController.php` | Mantenibilidad |
| 4 | N+1 queries en listado de estudiantes | `AdminBienestarController.php:1010-1065` | Rendimiento |
| 5 | N+1 queries en documentos de solicitudes | `EstudianteBecasService.php:377-398` | Rendimiento |
| 6 | 3 consultas COUNT separadas en getAlertas() | `AdminBienestarService.php:96-130` | Rendimiento |
| 7 | ProfileController legacy duplicado | `app/Controllers/ProfileController.php` (78 líneas) | Duplicación |
| 8 | EmailConfig.php duplicado | `app/Config/EmailConfig.php` (44 líneas) | Duplicación |
| 9 | 5 pares de vistas "original" vs "mejorada" | `app/Views/AdminBienestar/` | Duplicación |
| 10 | Stubs vacíos (solo redirect) | `BecaController.php`, `FichaController.php`, `ReporteController.php` | Código muerto |
| 11 | 86 `console.log()` en vistas PHP | `app/Views/*.php` | Debug leak |
| 12 | `md5(uniqid())` para códigos de verificación | `PdfCodigoVerificacionModel.php:34` | Criptografía débil |
| 13 | Error handling silenciado | `AdminBienestarController.php:2976` | Bugs ocultos |
| 14 | Tests existentes mínimos (8 tests) | `tests/` | Cobertura |

## Agentes instalados

| Nombre | Modelo | Propósito | Cuándo usarlo |
|--------|--------|-----------|---------------|
| `@seguridad` | `opencode/nemotron-3-super-free` | Auditor y corrector de seguridad: credenciales, CSRF, cifrado, SQLi, CORS, HTTPS | Inmediatamente — primero, antes de tocar cualquier otro código |
| `@corrector` | `groq/llama-3.3-70b-versatile` | Corrige bugs runtime, validación inconsistente, mixed return types, error handling silenciado, stubs vacíos | Después de seguridad, para estabilizar la aplicación |
| `@unificador` | `opencode/deepseek-v4-flash-free` | Refactoriza y unifica: fusiona controladores duplicados, vistas "mejorada/original", divide god-classes en controladores por módulo | Tercero, después de corregir errores |
| `@optimizador` | `groq/qwen/qwen3-32b` | Elimina N+1 queries, fusiona consultas COUNT, agrega paginación, reemplaza crypto débil | Cuarto, cuando la arquitectura ya esté limpia |
| `@limpiador` | `opencode/minimax-m3-free` | Elimina console.log, código muerto, archivos basura, stubs, código comentado legacy | Puede ejecutarse en paralelo con corrector |
| `@tester` | `opencode/mimo-v2.5-free` | Crea tests unitarios e integración para Services, Filters, Auth, Models siguiendo patrón PHPUnit existente | Al final, cuando el código ya esté corregido y refactorizado |

### Detalle de comandos de instalación

Cada agente se creó como archivo Markdown en `.opencode/agents/`:

```
.opencode/agents/
├── seguridad.md    → opencode/nemotron-3-super-free
├── corrector.md    → groq/llama-3.3-70b-versatile
├── unificador.md   → opencode/deepseek-v4-flash-free
├── optimizador.md  → groq/qwen/qwen3-32b
├── limpiador.md    → opencode/minimax-m3-free
└── tester.md       → opencode/mimo-v2.5-free
```

## Cómo invocarlos

Usa `@nombre` en cualquier mensaje para invocar un agente específico:

```bash
# Ejemplo: invocar al agente de seguridad
@seguridad Analiza y corrige todas las vulnerabilidades del proyecto

# Ejemplo: invocar al agente unificador
@unificador Fusiona ProfileController en PerfilController y unifica las vistas mejorada/original
```

Los agentes también son invocados automáticamente por el agente principal cuando detecta tareas que coinciden con su `description`.

### Resumen rápido por agente

| @mención | Qué hace |
|----------|----------|
| `@seguridad` | Corrige las 12 vulnerabilidades críticas/altas encontradas |
| `@corrector` | Arregla los 10 tipos de bugs identificados en controladores y servicios |
| `@unificador` | Realiza las 8 unificaciones/refactorizaciones de código duplicado y god-classes |
| `@optimizador` | Aplica las 8 optimizaciones de rendimiento identificadas |
| `@limpiador` | Ejecuta las 8 tareas de limpieza de código muerto y debug leaks |
| `@tester` | Crea los 6 grupos de tests siguiendo el patrón PHPUnit existente |

## Próximos pasos recomendados

### Fase 1 — Seguridad (URGENTE)
```bash
@seguridad Ejecuta todas las correcciones de seguridad en orden prioritario
```
Esto debe ejecutarse **inmediatamente**. Hay credenciales expuestas y CSRF deshabilitado.

### Fase 2 — Corrección y limpieza (paralelo)
```bash
@corrector Corrige todos los bugs identificados en controladores y servicios
@limpiador Elimina console.log, stubs vacíos, código muerto y archivos basura
```
Ambos pueden ejecutarse en paralelo. El corrector estabiliza la app, el limpiador elimina artefactos.

### Fase 3 — Unificación y refactorización
```bash
@unificador Fusiona controladores duplicados y unifica vistas mejorada/original
```
Ejecutar después de Fase 2 para evitar conflictos con archivos que serán eliminados.

### Fase 4 — Optimización
```bash
@optimizador Elimina todos los N+1 queries y optimiza consultas
```
Ejecutar cuando la estructura de archivos ya esté estable.

### Fase 5 — Testing
```bash
@tester Crea tests unitarios y de integración para todos los módulos
```
**Siempre al final**, después de todas las modificaciones.

---

> **Nota**: Todos los agentes usan modelos gratuitos de OpenCode Zen. Los agentes `corrector` y `optimizador` usan modelos Groq (70B y 32B respectivamente) con rate limits más restrictivos, mientras que `seguridad`, `unificador`, `limpiador` y `tester` usan modelos nativos de OpenCode sin rate limits adicionales.
