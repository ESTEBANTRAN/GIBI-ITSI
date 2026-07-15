# 📚 GIBI-ITSI — Sistema de Gestión Integral de Bienestar Institucional

## 📋 Descripción General

**GIBI-ITSI** es un sistema integral de gestión de bienestar estudiantil desarrollado con **CodeIgniter 4**, diseñado para el Instituto Tecnológico Superior. El sistema permite administrar fichas socioeconómicas, gestionar becas, procesar solicitudes de ayuda estudiantil, generar reportes analíticos y mantener un control completo sobre los períodos académicos.

### Niveles de Acceso

| ID | Rol | Constante | Acceso |
|----|-----|-----------|--------|
| 1 | **Estudiante** | `ROLE_ESTUDIANTE` | `/estudiante/*` |
| 2 | **Admin Bienestar** | `ROLE_ADMIN_BIENESTAR` | `/admin-bienestar/*` |
| 3 | Docente | — | Limitado |
| 4 | **Super Admin** | `ROLE_SUPER_ADMIN` | `/global-admin/*` |

---

## 🛠️ Stack Tecnológico

### Backend
| Tecnología | Versión | Descripción |
|-----------|---------|-------------|
| **PHP** | ^8.1 | Lenguaje de programación principal |
| **CodeIgniter 4** | ^4.0 | Framework MVC para PHP |
| **MariaDB** | 10.4.32 | Sistema de gestión de base de datos |
| **TCPDF** | ^6.10 | Generación de documentos PDF |
| **PHPWord** | ^1.3 | Generación de documentos Word |

### Frontend
| Tecnología | Descripción |
|-----------|-------------|
| **Bootstrap 5.3** | Framework CSS responsive |
| **JavaScript / jQuery 3.7** | Interactividad y AJAX |
| **Chart.js** | Gráficos y estadísticas visuales |
| **SweetAlert2** | Diálogos y notificaciones interactivas |
| **DataTables 1.13** | Tablas avanzadas con búsqueda y paginación |
| **Bootstrap Icons** | Iconografía del sistema |

### Infraestructura
| Componente | Descripción |
|-----------|-------------|
| **XAMPP** | Stack de desarrollo local (Apache + MariaDB) |

---

## 📁 Estructura del Proyecto

```
ITSI/
├── app/
│   ├── Config/                         # Configuración del sistema (41 archivos)
│   │   ├── App.php                     # Configuración principal
│   │   ├── Constants.php               # Constantes de roles y sistema
│   │   ├── Database.php                # Conexión a base de datos
│   │   ├── Filters.php                 # Configuración de filtros HTTP
│   │   ├── Routes.php                  # Definición de rutas (~29KB)
│   │   ├── PlantillasPDF.php           # Plantillas para generación PDF
│   │   └── ...
│   │
│   ├── Controllers/                    # Controladores (15 archivos + 3 subdirectorios)
│   │   ├── AuthController.php          # Autenticación, login, reCAPTCHA
│   │   ├── AdminBienestarController.php # Panel Admin Bienestar (~200KB)
│   │   ├── EstudianteController.php    # Panel de estudiantes
│   │   ├── DashboardController.php     # Dashboards y estadísticas
│   │   ├── DocumentoBecaController.php # Gestión de documentos de becas
│   │   ├── SolicitudController.php     # Gestión de solicitudes
│   │   ├── PlantillasController.php    # Plantillas PDF/Word
│   │   ├── PerfilController.php        # Gestión de perfil
│   │   ├── CuentaController.php        # Configuración de cuenta
│   │   ├── Admin/                      # Sub-controladores Admin
│   │   │   ├── DashboardController.php
│   │   │   └── FichasController.php
│   │   ├── Estudiante/                 # Sub-controladores Estudiante
│   │   │   ├── BecasController.php
│   │   │   ├── DocumentosController.php
│   │   │   ├── FichasController.php
│   │   │   ├── InformacionController.php
│   │   │   ├── PerfilController.php
│   │   │   └── SolicitudesController.php
│   │   └── GlobalAdmin/               # Sub-controladores Super Admin
│   │       ├── GlobalAdminController.php
│   │       ├── UsuariosController.php
│   │       ├── RolesController.php
│   │       ├── BackupsController.php
│   │       ├── LogsController.php
│   │       └── EstadisticasController.php
│   │
│   ├── Models/                         # Modelos de datos (11 archivos)
│   │   ├── UsuarioModel.php
│   │   ├── FichaSocioeconomicaModel.php
│   │   ├── BecaModel.php
│   │   ├── SolicitudBecaModel.php
│   │   ├── SolicitudBecaDocumentoModel.php
│   │   ├── SolicitudAyudaModel.php
│   │   ├── PeriodoAcademicoModel.php
│   │   ├── PdfCodigoVerificacionModel.php
│   │   ├── CategoriaSolicitudAyudaModel.php
│   │   ├── RespuestaSolicitudModel.php
│   │   └── GlobalAdmin/
│   │       ├── UsuarioGlobalModel.php
│   │       ├── RolModel.php
│   │       ├── SistemaModel.php
│   │       └── BackupModel.php
│   │
│   ├── Services/                       # Lógica de negocio
│   │   ├── AdminBienestarService.php   # Servicios administrativos (~33KB)
│   │   ├── EstudianteBecasService.php  # Servicios de becas (~23KB)
│   │   ├── PlantillaPDFService.php     # Generación de PDFs (~17KB)
│   │   └── PlantillaWordService.php    # Generación de Word (~14KB)
│   │
│   ├── Filters/                        # Filtros HTTP de seguridad
│   │   ├── AuthFilter.php              # Autenticación obligatoria
│   │   ├── RoleFilter.php              # Verificación de rol
│   │   ├── RateLimitFilter.php         # Limitación de peticiones
│   │   ├── SecurityHeadersFilter.php   # Cabeceras de seguridad
│   │   └── XssFilter.php              # Protección XSS
│   │
│   ├── Helpers/                        # Funciones auxiliares
│   │   ├── EmailHelper.php             # Envío de correos (SMTP dinámico)
│   │   ├── GoogleDriveHelper.php       # Respaldos en Google Drive
│   │   └── RecaptchaHelper.php         # Validación reCAPTCHA v2
│   │
│   └── Views/                          # Vistas del sistema (11 subdirectorios)
│       ├── layouts/                    # Layouts principales
│       │   ├── mainAdmin.php           # Layout Admin Bienestar
│       │   ├── mainEstudiante.php      # Layout Estudiante
│       │   └── mainGlobalAdmin.php     # Layout Super Admin
│       ├── AdminBienestar/             # Vistas Admin (22 archivos)
│       ├── estudiante/                 # Vistas Estudiante (8 archivos)
│       ├── GlobalAdmin/                # Vistas Super Admin (11 archivos)
│       ├── auth/                       # Login
│       ├── partials/                   # Navbar, footer
│       ├── perfil/                     # Perfil de usuario
│       ├── cuenta/                     # Configuración de cuenta
│       └── plantillas/                 # Plantillas PDF/Word
│
├── public/                             # Archivos públicos
│   ├── index.php                       # Punto de entrada
│   ├── login/                          # Assets del login
│   ├── sistema/                        # Assets del sistema
│   │   └── assets/
│   │       ├── css/
│   │       │   ├── styles.min.css      # CSS del template Modernize
│   │       │   └── custom.css          # Personalizaciones
│   │       ├── js/
│   │       │   ├── app.min.js          # Lógica del sidebar
│   │       │   └── sidebarmenu.js      # Menú del sidebar
│   │       └── images/
│   └── uploads/                        # Archivos subidos por usuarios
│
├── writable/                           # Archivos escribibles
│   ├── cache/
│   ├── logs/
│   └── session/
│
├── composer.json                       # Dependencias PHP
└── README.md                           # Esta documentación
```

---

## 🗄️ Base de Datos

### Información General
- **Nombre**: `bienestar_estudiantil_db`
- **Motor**: MariaDB 10.4.32
- **Charset**: utf8mb4 / utf8mb4_unicode_ci

### Diagrama de Tablas Principales

```
┌─────────────────────┐     ┌─────────────────────┐
│      usuarios       │     │        roles         │
├─────────────────────┤     ├─────────────────────┤
│ id (PK)             │────▶│ id (PK)             │
│ rol_id (FK)         │     │ nombre              │
│ carrera_id (FK)     │     │ descripcion         │
│ nombre, apellido    │     │ permisos (JSON)     │
│ cedula (UNIQUE)     │     │ estado              │
│ email (UNIQUE)      │     └─────────────────────┘
│ password_hash       │
│ telefono, direccion │     ┌─────────────────────┐
│ semestre            │     │      carreras       │
│ foto_perfil         │     ├─────────────────────┤
│ estado              │────▶│ id (PK)             │
│ ultimo_acceso       │     │ nombre              │
│ intentos_fallidos   │     │ codigo (UNIQUE)     │
│ bloqueado_hasta     │     │ semestres, activa   │
└─────────────────────┘     └─────────────────────┘
         │
         │ 1:N
         ▼
┌────────────────────────────┐     ┌────────────────────────┐
│  fichas_socioeconomicas    │     │   periodos_academicos  │
├────────────────────────────┤     ├────────────────────────┤
│ id (PK)                    │     │ id (PK)                │
│ estudiante_id (FK)         │────▶│ nombre                 │
│ periodo_id (FK)            │     │ estado                 │
│ json_data (JSON)           │     │ fecha_inicio/fin       │
│ estado                     │     │ activo_fichas/becas    │
│ revisada_por_admin         │     │ limite_fichas/becas    │
│ fecha_revision_admin       │     │ vigente_estudiantes    │
│ observaciones_admin        │     └────────────────────────┘
│ puntaje_calculado          │
│ relacionada_beca           │
└────────────────────────────┘
         │
         │ M:N (a través de solicitudes)
         ▼
┌────────────────────────────┐     ┌────────────────────────┐
│        becas               │     │   solicitudes_becas    │
├────────────────────────────┤     ├────────────────────────┤
│ id (PK)                    │◀────│ id (PK)                │
│ nombre, descripcion        │     │ estudiante_id (FK)     │
│ requisitos                 │     │ beca_id (FK)           │
│ puntaje_minimo_requerido   │     │ periodo_id (FK)        │
│ tipo_beca                  │     │ estado                 │
│ monto_beca                 │     │ observaciones          │
│ cupos_disponibles          │     │ fecha_solicitud        │
│ estado, activa             │     │ revisado_por           │
│ periodo_vigente_id (FK)    │     │ motivo_rechazo         │
│ documentos_requisitos      │     │ porcentaje_avance      │
└────────────────────────────┘     └────────────────────────┘
```

### Tablas del Sistema

#### Tablas Principales
| Tabla | Descripción |
|-------|-------------|
| `usuarios` | Todos los usuarios del sistema (~503 estudiantes, 5 admins, 1 docente) |
| `roles` | Roles del sistema (Estudiante, Admin Bienestar, Docente, Super Admin) |
| `carreras` | Carreras académicas disponibles |
| `periodos_academicos` | Períodos académicos para fichas y becas |
| `fichas_socioeconomicas` | Fichas socioeconómicas con datos JSON flexibles |
| `becas` | Catálogo de becas disponibles |
| `solicitudes_becas` | Solicitudes de becas realizadas |
| `solicitudes_ayuda` | Solicitudes de ayuda estudiantil |
| `configuracion_sistema` | Configuración dinámica (SMTP, Google Drive, etc.) |

#### Tablas de Soporte
| Tabla | Descripción |
|-------|-------------|
| `becas_documentos_requisitos` | Documentos requeridos por cada beca |
| `documentos_solicitud_becas` | Documentos subidos para solicitudes |
| `estudiantes_habilitacion_becas` | Estado de habilitación para becas |
| `historial_estados_becas` | Historial de cambios de estado |
| `notificaciones_becas` | Notificaciones de becas |
| `observaciones_fichas` | Observaciones administrativas |
| `flujo_aprobacion_documentos` | Tracking del flujo de aprobación |
| `categorias_solicitud_ayuda` | Categorías de ayuda |
| `respuestas_solicitudes_ayuda` | Respuestas a solicitudes |
| `respuestas_predefinidas` | Plantillas de respuestas rápidas |
| `citas` | Citas programadas con estudiantes |
| `pdf_codigos_verificacion` | Códigos QR para verificación de PDFs |
| `logs` | Bitácora de seguridad y auditoría |

#### Vistas de Base de Datos
| Vista | Descripción |
|-------|-------------|
| `v_becas_completas` | Becas con estadísticas de solicitudes |
| `v_dashboard_admin_bienestar` | Datos para dashboard administrativo |
| `v_estadisticas_sistema` | Estadísticas globales del sistema |
| `v_fichas_admin` | Fichas con datos de estudiante y período |
| `v_fichas_socioeconomicas_completa` | Fichas con información completa |
| `v_solicitudes_becas_completas` | Solicitudes con datos detallados |
| `v_solicitudes_becas_detallada` | Vista detallada de solicitudes |

#### Triggers
| Trigger | Descripción |
|---------|-------------|
| `tr_ficha_completada_habilitar_becas` | Habilita solicitud de becas al aprobar ficha |
| `validar_comentario_rechazo` | Obliga comentario al rechazar ficha |
| `tr_actualizar_documentos_revisados` | Actualiza contadores al aprobar documentos |
| `actualizar_porcentaje_avance_beca` | Calcula avance de verificación |

### Estados del Sistema

#### Fichas Socioeconómicas
`Borrador` → `Enviada` → `Revisada` → `Aprobada` / `Rechazada`

#### Solicitudes de Beca
`Postulada` → `En Revisión` → `Aprobada` / `Rechazada` / `Lista de Espera`

#### Solicitudes de Ayuda
`Pendiente` → `En Proceso` → `Resuelta` / `Cerrada`

#### Categorías Socioeconómicas (Evaluación Automática)
| Categoría | Puntaje | Descripción |
|-----------|---------|-------------|
| **A** | ≥ 8.00 ó 3.00 | Menor nivel de recursos — prioridad alta |
| **B** | ≥ 6.00 ó 2.00 | Nivel medio de recursos |
| **C** | < 6.00 ó 1.00 | Mayor nivel de recursos |

---

## 🚀 Módulos del Sistema

### 1. Autenticación y Seguridad
- Login con cédula o email + contraseña
- **Google reCAPTCHA v2** para prevención de bots
- Bloqueo por intentos fallidos (5 intentos = 30 min bloqueo)
- **Rate Limiting** por IP
- Cabeceras de seguridad HTTP (SecurityHeadersFilter)
- Protección XSS (XssFilter)
- Tokens CSRF en todas las peticiones POST/AJAX
- Contraseñas hasheadas con bcrypt

### 2. Fichas Socioeconómicas
- Formulario dinámico de múltiples secciones (datos JSON flexibles)
- Flujo de trabajo con estados (Borrador → Aprobada)
- **Evaluación socioeconómica automática masiva** con persistencia en BD
- Clasificación por categorías A, B, C
- Exportación a PDF con código QR de verificación
- Trigger automático para habilitar becas al aprobar
- Una ficha por estudiante por período

### 3. Gestión de Becas
- CRUD completo de tipos de becas (Académica, Económica, Deportiva, Cultural, etc.)
- Flujo de solicitud con verificación de documentos
- Revisión individual de documentos con porcentaje de avance
- Notificaciones automáticas por cambios de estado
- Configuración de programas de becas por período

### 4. Solicitudes de Ayuda
- Categorías: Académicas, Económicas, Salud, Vivienda, Sociales, Técnicas
- Niveles de prioridad: Baja, Media, Alta, Urgente
- Sistema de respuestas con plantillas predefinidas
- Historial de comunicación

### 5. Períodos Académicos
- Definición de fechas de inicio/fin
- Límites configurables de fichas y becas
- Activación independiente para fichas y becas
- Visibilidad configurable para estudiantes

### 6. Reportes y Analítica
- Dashboard con gráficos interactivos (Chart.js)
- Estadísticas por estado, período, carrera
- Exportación a **PDF** (TCPDF), **Excel/CSV** y **Word** (PHPWord)
- Actualización automática cada 5 minutos
- Gráficos exportables como PNG

### 7. Información Estudiantil
- Listado completo de estudiantes con estadísticas agregadas
- Modal de historial completo (fichas, becas, ayudas, documentos)
- Exportación de listados a Excel y PDF
- Rutas AJAX dinámicas

### 8. Super Administrador (GlobalAdmin)
- Dashboard global con métricas KPI
- Gestión completa de usuarios (CRUD)
- Gestión de roles y permisos
- **Respaldos de BD** manuales con descarga SQL
- Visor de logs de seguridad con filtros
- **Configuración centralizada** del sistema (SMTP, Google Drive)
- Estadísticas avanzadas con gráficos

### 9. Generación de Documentos
- **PDF**: Fichas socioeconómicas con sección de evaluación, código QR y diseño institucional
- **Word**: Reportes exportables en formato .docx
- Códigos de verificación almacenados en BD para validación de autenticidad

---

## 🔐 Seguridad

| Capa | Implementación |
|------|----------------|
| **Autenticación** | Bcrypt + sesiones CI4 + reCAPTCHA v2 |
| **Autorización** | `AuthFilter`, `RoleFilter` por rutas |
| **Anti-Fuerza Bruta** | `RateLimitFilter` + bloqueo temporal |
| **Cabeceras** | `SecurityHeadersFilter` (X-Frame-Options, CSP, etc.) |
| **XSS** | `XssFilter` en entradas |
| **CSRF** | Tokens en meta tags + inyección automática en AJAX |
| **Documentos** | Códigos QR de verificación en PDFs |
| **Auditoría** | Bitácora de seguridad en tabla `logs` |
| **Email** | Configuración SMTP dinámica desde BD |

---

## ⚙️ Instalación y Configuración

### Requisitos Previos
- PHP 8.1 o superior
- MariaDB/MySQL 10.4+
- Composer
- XAMPP o servidor Apache equivalente

### Pasos de Instalación

1. **Clonar/Copiar el proyecto**
   ```bash
   cd C:\xampp\htdocs
   git clone <repositorio> ITSI
   ```

2. **Instalar dependencias**
   ```bash
   cd ITSI
   composer install
   ```

3. **Crear e importar base de datos**
   ```sql
   CREATE DATABASE bienestar_estudiantil_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   ```bash
   mysql -u root bienestar_estudiantil_db < bienestar_estudiantil_db.sql
   ```

4. **Configurar el entorno** — Editar `.env`:
   ```ini
   app.baseURL = 'http://localhost/ITSI/public/'
   database.default.hostname = localhost
   database.default.database = bienestar_estudiantil_db
   database.default.username = root
   database.default.password =
   ```

5. **Permisos de escritura**
   ```bash
   chmod -R 755 writable/
   ```

6. **Acceder al sistema**
   ```
   http://localhost/ITSI/public/
   ```



### Credenciales de Prueba

| Rol | Cédula | Contraseña |
|-----|--------|------------|
| **Admin Bienestar** | `0001` | `password` |
| **Estudiante** | `0003` | `mmmm` |
| **Super Admin** | `0004` | `password` |

---

## 📡 Rutas Principales

### Públicas
```
GET  /                              # Login
POST /auth/attemptLogin             # Procesar login (reCAPTCHA)
GET  /auth/logout                   # Cerrar sesión
```

### Estudiante (`/estudiante/*`)
```
GET  /estudiante                           # Dashboard
GET  /estudiante/ficha-socioeconomica      # Gestión de fichas
POST /estudiante/crear-ficha               # Crear ficha
POST /estudiante/enviar-ficha              # Enviar para revisión
GET  /estudiante/exportar-ficha-pdf/:id    # Descargar PDF

GET  /estudiante/becas                     # Becas disponibles
POST /estudiante/solicitar-beca            # Solicitar beca
GET  /estudiante/documentos-beca/:id       # Gestionar documentos

GET  /estudiante/solicitudes-ayuda         # Solicitudes de ayuda
POST /estudiante/crear-solicitud-ayuda     # Crear solicitud

GET  /estudiante/perfil                    # Perfil
POST /estudiante/actualizar-perfil         # Actualizar perfil
```

### Admin Bienestar (`/admin-bienestar/*`)
```
GET  /admin-bienestar                              # Dashboard
GET  /admin-bienestar/fichas-socioeconomicas       # Listar fichas
POST /admin-bienestar/aprobar-ficha/:id            # Aprobar ficha
POST /admin-bienestar/rechazar-ficha/:id           # Rechazar ficha
POST /admin-bienestar/guardar-evaluacion-masiva    # Evaluación automática

GET  /admin-bienestar/becas                        # Listar becas
POST /admin-bienestar/crear-beca                   # Crear beca
GET  /admin-bienestar/solicitudes-becas            # Solicitudes de becas
GET  /admin-bienestar/revision-documentos/:id      # Revisar documentos

GET  /admin-bienestar/gestion-periodos             # Períodos académicos
GET  /admin-bienestar/solicitudes-ayuda            # Solicitudes de ayuda
GET  /admin-bienestar/estudiantes                  # Información estudiantil
GET  /admin-bienestar/reportes                     # Reportes y analítica
```

### Super Admin (`/global-admin/*`)
```
GET  /global-admin/dashboard              # Dashboard global
GET  /global-admin/usuarios               # Gestión de usuarios
GET  /global-admin/roles                  # Gestión de roles
GET  /global-admin/respaldos              # Respaldos de BD
GET  /global-admin/logs                   # Logs del sistema
GET  /global-admin/estadisticas           # Estadísticas avanzadas
GET  /global-admin/configuracion          # Configuración del sistema
```

---

## 🔧 Mantenimiento

### Respaldos
- Respaldos manuales desde panel Super Admin
- Descarga de archivos SQL
- Integración con Google Drive (GoogleDriveHelper)

### Logs y Auditoría
- Bitácora de seguridad con registro de acciones críticas
- Filtrado por fecha, tipo y usuario
- Exportación de logs

### Configuración Centralizada
- Tabla `configuracion_sistema` para parámetros dinámicos
- SMTP configurable desde panel (EmailHelper)
- Credenciales de Google Drive desde BD (GoogleDriveHelper)

---

## 📝 Convenciones de Código

| Elemento | Convención |
|----------|------------|
| Clases | PascalCase |
| Métodos | camelCase |
| Tablas BD | snake_case |
| Variables | camelCase |
| Constantes | UPPER_SNAKE_CASE |

### Arquitectura
- **Layouts base** en `Views/layouts/`
- **Vistas por módulo** en carpetas dedicadas
- **Parciales reutilizables** en subcarpetas `partials/`
- **Lógica de negocio** separada en `Services/`
- **Helpers** para funcionalidades transversales (Email, reCAPTCHA, Google Drive)
- **Filtros HTTP** en cascada para seguridad

---

## 🐛 Troubleshooting

| Problema | Solución |
|----------|----------|
| Error 500 | Verificar permisos de `writable/` y revisar `writable/logs/` |
| Login no funciona | Verificar conexión a BD y que existan usuarios |
| reCAPTCHA falla | Verificar claves de Google reCAPTCHA en configuración |
| PDFs no se generan | Ejecutar `composer require tecnickcom/tcpdf` |
| Sesión se pierde | Revisar `app/Config/Session.php` |
| Datos no cargan correctamente | Las vistas usan rutas dinámicas (`SITE_ROOT`, `ADMIN_BASE`) |
| CORS / Mixed Content | Verificar que `app.baseURL` coincida con el dominio de acceso |

---

## 📞 Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Versión** | 2.0.0 |
| **Última actualización** | 2026-06-08 |
| **Framework** | CodeIgniter 4 |
| **Base de datos** | MariaDB 10.4.32 |
| **Template UI** | Modernize (Wrappixel) |
| **Licencia** | MIT |

---

## 📜 Licencia

MIT License — Ver archivo `LICENSE` para más detalles.
