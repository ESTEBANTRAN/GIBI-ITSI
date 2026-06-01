# ITSI - Sistema de Bienestar Estudiantil

## 📋 Visión General
Sistema web para la gestión integral del bienestar estudiantil. Permite a estudiantes solicitar becas, fichas socioeconómicas y ayudas; y a administradores gestionar, revisar y aprobar dichas solicitudes.

## 🏗️ Stack Tecnológico
- **Backend:** PHP 8.x, CodeIgniter 4 (MVC)
- **Base de datos:** MySQL 8.0 (MariaDB) — `bienestar_estudiantil_db`
- **Frontend:** Bootstrap 5, Bootstrap Icons, jQuery
- **Entorno:** XAMPP en Windows (`C:\xampp\htdocs\ITSI`)
- **URL base:** `http://localhost/ITSI/public/`
- **Autenticación:** Sesiones PHP, roles: Estudiante(1), AdminBienestar(2), SuperAdmin(4)

## 📁 Estructura del Proyecto

```
app/
├── Config/           # Configuración (Database, Routes, Filters, etc.)
├── Controllers/      # Controladores MVC
│   ├── GlobalAdmin/  # Controladores del SuperAdmin
│   ├── AdminBienestarController.php  # Controlador principal de Bienestar
│   ├── AuthController.php           # Login, registro, recuperación
│   ├── EstudianteController.php     # Funcionalidades del estudiante
│   ├── BecaController.php           # Gestión de becas
│   ├── FichaController.php          # Fichas socioeconómicas
│   ├── SolicitudController.php      # Solicitudes de ayuda
│   ├── DashboardController.php      # Dashboard principal
│   ├── PlantillasController.php     # Plantillas PDF
│   ├── ReporteController.php        # Reportes y exportaciones
│   ├── DocumentoBecaController.php  # Subida/gestión de documentos
│   ├── PerfilController.php         # Perfil de usuario
│   ├── CuentaController.php         # Configuración de cuenta
│   ├── ProfileController.php        # Perfil (legacy)
│   └── ...
├── Models/           # Modelos de datos
│   ├── GlobalAdmin/  # Modelos del SuperAdmin
│   ├── BecaModel.php
│   ├── SolicitudBecaModel.php
│   ├── FichaSocioeconomicaModel.php
│   ├── UsuarioModel.php
│   ├── PeriodoAcademicoModel.php
│   └── ...
├── Views/            # Vistas (PHP + Bootstrap)
│   ├── layouts/      # Layouts principales (mainAdmin, mainEstudiante, mainGlobalAdmin)
│   ├── partials/     # Navbar, footer, componentes reutilizables
│   ├── estudiante/   # Vistas del rol estudiante
│   ├── AdminBienestar/  # Vistas del admin de bienestar
│   ├── GlobalAdmin/     # Vistas del superadmin
│   ├── perfil/       # Perfiles por rol
│   ├── plantillas/   # Gestión de plantillas PDF
│   └── ...
├── Services/         # Lógica de negocio
│   ├── AdminBienestarService.php
│   ├── EstudianteBecasService.php
│   └── PlantillaPDFService.php
├── Helpers/          # Helpers personalizados
│   ├── EmailHelper.php
│   └── RecaptchaHelper.php
├── Security/         # Seguridad
│   └── InputSanitizer.php
└── Database/         # Migraciones y seeds
```

## 🔐 Roles del Sistema
| Rol          | ID | Descripción                              |
|-------------|----|------------------------------------------|
| Estudiante  | 1  | Solicita becas, fichas, ayudas           |
| Admin Bienestar | 2 | Gestiona solicitudes, revisa documentos  |
| Super Admin | 4  | Gestiona usuarios, roles, configuración  |

## 🧭 Módulos Principales

### 1. Autenticación (`AuthController`)
- Login con rate limiting (10 intentos/15min)
- Recuperación de contraseña con token
- Logout

### 2. Dashboard (`DashboardController`, `AdminBienestarController`)
- Estadísticas en tiempo real
- Actividad reciente
- Tarjetas resumen

### 3. Becas (flujo completo)
- **Estudiante:** `becas_mejorado.php` → solicita, sube documentos, consulta estado
- **Admin:** `solicitudes_becas_mejorada.php` → revisa, aprueba/rechaza
- **Admin:** `revision_documentos.php` → revisa documentos individuales
- **Archivos:** `EstudianteController`, `AdminBienestarController`, `DocumentoBecaController`, `EstudianteBecasService`

### 4. Fichas Socioeconómicas (`FichaController`)
- Estudiantes completan ficha
- Admin revisa y valida

### 5. Solicitudes de Ayuda (`SolicitudController`)
- Estudiantes crean solicitudes de ayuda
- Admin responde y gestiona

### 6. Perfil y Cuenta
- `PerfilController` - Editar perfil, foto
- `CuentaController` - Cambiar password, notificaciones, exportar datos

### 7. Plantillas PDF (`PlantillasController`)
- Subir y gestionar plantillas
- Vista previa

### 8. GlobalAdmin (SuperAdmin)
- Gestión de usuarios y roles
- Respaldos de base de datos
- Configuración del sistema
- Logs del sistema

## 🌐 Convenciones de Código
- **PHP:** CodeIgniter 4 MVC estándar (controladores en `Controllers/`, modelos en `Models/`)
- **Rutas:** Definidas en `app/Config/Routes.php`, agrupadas por rol con filtros `auth` y `role:X`
- **Vistas:** PHP embebido con Bootstrap 5. Los nombres en kebab-case para archivos
- **JS:** JavaScript vanilla con fetch() para llamadas AJAX, Bootstrap para modales
- **Estilo:** Bootstrap 5 + Bootstrap Icons, diseño responsivo
- **Base de datos:** MySQLi, consultas principalmente con Query Builder de CI4
- **Seguridad:** Filtros XSS, CSRF (deshabilitado temporalmente), rate limiting en login

## 🚦 Reglas Importantes
1. **SIEMPRE** leer los archivos relacionados antes de hacer cambios
2. **NO** modificar la estructura de la base de datos sin consultar primero
3. **USAR** `str_replace` para cambios pequeños, `write_file` solo para archivos nuevos
4. **VERIFICAR** sintaxis PHP con `php -l` después de cambios
5. **MANTENER** compatibilidad con Bootstrap 5 y jQuery
6. **VALIDAR** que las rutas tengan los filtros de autenticación adecuados
7. **NO** instalar paquetes globales sin confirmación
8. **Las vistas JS** deben coincidir exactamente con las rutas registradas en Routes.php

## 🔗 Referencias Útiles
- **Base de datos:** MySQL localhost:3306, user: root, pass: (vacío), db: bienestar_estudiantil_db
- **Config DB:** `app/Config/Database.php`
- **Rutas:** `app/Config/Routes.php`
- **Filtros:** `app/Config/Filters.php`
