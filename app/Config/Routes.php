<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================================================
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('auth/attemptLogin', 'AuthController::attemptLogin', ['filter' => 'ratelimit:10,900']);

// Rutas para recuperación de contraseña
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/verifyIdentity', 'AuthController::verifyIdentity', ['filter' => 'ratelimit:5,900']);
$routes->get('reset-password/(:alphanum)', 'AuthController::resetPasswordForm/$1');
$routes->post('auth/resetPassword', 'AuthController::resetPassword', ['filter' => 'ratelimit:5,900']);

// Logout
$routes->get('auth/logout', 'AuthController::logout');

// ============================================================================
// RUTAS CON AUTENTICACIÓN (sin grupo específico)
// ============================================================================

// Dashboard y páginas principales
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('admin-bienestar', 'DashboardController::adminBienestar', ['filter' => ['auth', 'role:2']]);
$routes->get('ficha', 'FichaController::index', ['filter' => 'auth']);
$routes->get('becas', 'BecaController::index', ['filter' => 'auth']);
$routes->get('solicitudes', 'SolicitudController::index', ['filter' => 'auth']);
$routes->get('fichas', 'FichaController::adminIndex', ['filter' => ['auth', 'role:2']]);
$routes->get('solicitudes-becas', 'BecaController::adminIndex', ['filter' => ['auth', 'role:2']]);
$routes->get('reportes', 'ReporteController::index', ['filter' => ['auth', 'role:2']]);

// Dashboard - AJAX
$routes->get('dashboard/estadisticas', 'DashboardController::getEstadisticas', ['filter' => 'auth']);
$routes->get('dashboard/actividad', 'DashboardController::getActividadReciente', ['filter' => 'auth']);
$routes->post('dashboard/actualizar', 'DashboardController::actualizarDashboard', ['filter' => 'auth']);

// Perfil (genérico, redirige según rol)
$routes->get('perfil/editar', 'PerfilController::editar', ['filter' => 'auth']);
$routes->post('perfil/actualizar', 'PerfilController::actualizar', ['filter' => 'auth']);
$routes->post('perfil/cambiarFoto', 'PerfilController::cambiarFoto', ['filter' => 'auth']);

// Cambio de foto de perfil (legacy)
$routes->post('profile/cambiar-foto', 'ProfileController::cambiarFotoPerfil', ['filter' => 'auth']);

// Cuenta (genérico, redirige según rol)
$routes->get('cuenta/configuracion', 'CuentaController::configuracion', ['filter' => 'auth']);
$routes->post('cuenta/cambiarPassword', 'CuentaController::cambiarPassword', ['filter' => ['auth', 'ratelimit:5,900']]);
$routes->post('cuenta/configuracionNotificaciones', 'CuentaController::configuracionNotificaciones', ['filter' => 'auth']);
$routes->post('cuenta/eliminarCuenta', 'CuentaController::eliminarCuenta', ['filter' => ['auth', 'ratelimit:3,3600']]);
$routes->get('cuenta/exportarDatos', 'CuentaController::exportarDatos', ['filter' => 'auth']);

// Rutas legacy para compatibilidad (Admin Bienestar)
$routes->get('estudiantes', 'AdminBienestarController::usuarios', ['filter' => ['auth', 'role:2']]);
$routes->get('usuarios/admin', 'AdminBienestarController::usuarios', ['filter' => ['auth', 'role:2']]);
$routes->get('usuarios/roles', 'AdminBienestarController::usuarios', ['filter' => ['auth', 'role:2']]);
$routes->get('configuracion/periodos', 'AdminBienestarController::gestionPeriodosAcademicos', ['filter' => ['auth', 'role:2']]);
$routes->get('solicitudes/comunicacion', 'AdminBienestarController::solicitudesBecas', ['filter' => ['auth', 'role:2']]);
$routes->get('solicitudes/integracion', 'AdminBienestarController::solicitudesBecas', ['filter' => ['auth', 'role:2']]);

// Plantillas
$routes->get('plantillas', 'PlantillasController::index', ['filter' => 'auth']);
$routes->get('plantillas/gestionar', 'PlantillasController::gestionar', ['filter' => 'auth']);
$routes->get('plantillas/probar-personalizada', 'PlantillasController::probarPlantillaPersonalizada', ['filter' => 'auth']);
$routes->get('plantillas/probar-word', 'PlantillasController::probarPlantillaWord', ['filter' => 'auth']);
$routes->post('plantillas/subir', 'PlantillasController::subirPlantilla', ['filter' => 'auth']);
$routes->delete('plantillas/eliminar/(:any)', 'PlantillasController::eliminarPlantilla/$1', ['filter' => 'auth']);
$routes->get('plantillas/vista-previa/(:any)', 'PlantillasController::vistaPrevia/$1', ['filter' => 'auth']);

// Verificar código PDF (Admin Bienestar)
$routes->post('verificar-codigo-pdf', 'AdminBienestarController::verificarCodigoPDF', ['filter' => ['auth', 'role:2']]);

// ============================================================================
// RUTAS AdminBienestar (agrupadas con auth — consolidado, sin duplicados)
// ============================================================================
$routes->group('admin-bienestar', ['filter' => ['auth', 'role:2']], function($routes) {

    // ─── Dashboard y Estadísticas ──────────────────────────────────────────
    $routes->get('dashboard', 'AdminBienestarController::dashboard');
    $routes->get('getEstadisticas', 'AdminBienestarController::getEstadisticas');

    // ─── Páginas principales ───────────────────────────────────────────────
    $routes->get('fichas-socioeconomicas', 'AdminBienestarController::fichasSocioeconomicas');
    $routes->get('gestion-periodos', 'AdminBienestarController::gestionPeriodos');
    $routes->get('becas', 'AdminBienestarController::becas');
    $routes->get('estudiantes', 'AdminBienestarController::usuarios');
    $routes->get('usuarios', 'AdminBienestarController::usuarios');
    $routes->get('solicitudes-becas', 'AdminBienestarController::solicitudesBecas');
    $routes->get('reportes', 'AdminBienestarController::reportes');
    $routes->get('solicitudes-ayuda', 'AdminBienestarController::solicitudesAyudaMejorada');
    $routes->get('configuracion-becas', 'AdminBienestarController::configuracionBecas');

    // ─── Gestión de períodos académicos ────────────────────────────────────
    $routes->post('actualizarLimitesPeriodo', 'AdminBienestarController::actualizarLimitesPeriodo');
    $routes->post('toggleConfiguracionPeriodo', 'AdminBienestarController::toggleConfiguracionPeriodo');
    $routes->get('detallePeriodo/(:num)', 'AdminBienestarController::detallePeriodo/$1');

    // ─── Gestión de períodos académicos (kebab-case) ───────────────────────
    $routes->get('obtener-periodo/(:num)', 'AdminBienestarController::obtenerPeriodo/$1');
    $routes->post('crear-periodo', 'AdminBienestarController::crearPeriodo');
    $routes->post('actualizar-periodo', 'AdminBienestarController::actualizarPeriodo');
    $routes->post('eliminar-periodo', 'AdminBienestarController::eliminarPeriodo');
    $routes->get('ver-periodo/(:num)', 'AdminBienestarController::verPeriodo/$1');
    $routes->get('exportar-periodos', 'AdminBienestarController::exportarPeriodos');
    $routes->post('actualizar-contadores-periodos', 'AdminBienestarController::actualizarContadoresPeriodos');

    // ─── Gestión de Becas ──────────────────────────────────────────────────
    $routes->post('crear-beca', 'AdminBienestarController::crearBeca', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('actualizar-beca', 'AdminBienestarController::actualizarBeca', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('eliminar-beca', 'AdminBienestarController::eliminarBeca', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('toggle-estado-beca', 'AdminBienestarController::toggleEstadoBeca');
    $routes->get('obtener-beca/(:num)', 'AdminBienestarController::obtenerBeca/$1');
    $routes->get('exportar-becas', 'AdminBienestarController::exportarBecas');
    $routes->get('estadisticas-becas', 'AdminBienestarController::getEstadisticasBecas');
    $routes->post('configurar-documentos-beca', 'AdminBienestarController::configurarDocumentosBeca');

    // ─── Gestión de Solicitudes de Becas ───────────────────────────────────
    $routes->post('aprobar-solicitud-beca', 'AdminBienestarController::aprobarSolicitudBeca', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('rechazar-solicitud-beca', 'AdminBienestarController::rechazarSolicitudBeca', ['filter' => ['auth', 'ratelimit:10,900']]);

    // ─── Revisión de Documentos ────────────────────────────────────────────
    $routes->get('revision-documentos/(:num)', 'AdminBienestarController::revisionDocumentos/$1');
    $routes->get('ver-documento/(:num)', 'AdminBienestarController::verDocumento/$1');
    $routes->post('aprobar-documento', 'AdminBienestarController::aprobarDocumento');
    $routes->post('rechazar-documento', 'AdminBienestarController::rechazarDocumento');
    $routes->post('revisar-nuevamente-documento', 'AdminBienestarController::revisarNuevamenteDocumento');
    $routes->post('agregar-observacion-solicitud', 'AdminBienestarController::agregarObservacionSolicitud');
    $routes->get('detalle-solicitud-beca/(:num)', 'AdminBienestarController::revisionDocumentos/$1');

    // ─── Solicitudes de Ayuda ──────────────────────────────────────────────
    $routes->get('detalle-solicitud-ayuda/(:num)', 'AdminBienestarController::detalleSolicitudAyuda/$1');
    $routes->post('responder-solicitud-ayuda', 'AdminBienestarController::responderSolicitudAyuda', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('marcar-solicitud-resuelta', 'AdminBienestarController::marcarSolicitudResuelta', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('asignar-solicitud', 'AdminBienestarController::asignarSolicitud', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('cambiar-prioridad', 'AdminBienestarController::cambiarPrioridad', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('cerrar-solicitud', 'AdminBienestarController::cerrarSolicitud', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->get('historial-solicitudes-estudiante/(:num)', 'AdminBienestarController::historialSolicitudesEstudiante/$1');
    $routes->get('exportar-solicitudes', 'AdminBienestarController::exportarSolicitudes');
    $routes->post('crear-respuesta-rapida', 'AdminBienestarController::crearRespuestaRapida');
    $routes->post('guardar-respuesta-predefinida', 'AdminBienestarController::guardarRespuestaPredefinida');
    $routes->get('obtener-respuestas-predefinidas', 'AdminBienestarController::obtenerRespuestasPredefinidas');
    $routes->post('eliminar-respuesta-predefinida', 'AdminBienestarController::eliminarRespuestaPredefinida');

    // ─── Gestión de Usuarios ───────────────────────────────────────────────
    $routes->get('usuario/(:num)', 'AdminBienestarController::verUsuario/$1');
    $routes->post('usuario/crear', 'AdminBienestarController::crearUsuario', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('usuario/cambiar-estado', 'AdminBienestarController::cambiarEstadoUsuario', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('usuario/resetear-password', 'AdminBienestarController::resetearPasswordUsuario', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->get('usuarios/exportar', 'AdminBienestarController::exportarUsuarios');

    // ─── Fichas Socioeconómicas ────────────────────────────────────────────
    $routes->post('actualizar-estado-ficha', 'AdminBienestarController::actualizarEstadoFicha');
    $routes->get('verFicha/(:num)', 'AdminBienestarController::verFicha/$1');
    $routes->post('aprobar-ficha/(:num)', 'AdminBienestarController::aprobarFicha/$1');
    $routes->post('rechazar-ficha/(:num)', 'AdminBienestarController::rechazarFicha/$1');
    $routes->get('exportar-ficha-pdf/(:num)', 'AdminBienestarController::exportarFichaPDF/$1');
    $routes->post('exportarDatos', 'AdminBienestarController::exportarDatos');
    $routes->post('generarReporte', 'AdminBienestarController::generarReporte');
    $routes->get('ver-ficha/(:num)', 'AdminBienestarController::verFicha/$1');

    // ─── Gestión de Estudiantes ────────────────────────────────────────────
    $routes->get('historial-estudiante/(:num)', 'AdminBienestarController::historialEstudiante/$1');
    $routes->get('estudiantes-sin-beca', 'AdminBienestarController::estudiantesSinBeca');

    // ─── Perfil y Cuenta ───────────────────────────────────────────────────
    $routes->get('perfil', 'AdminBienestarController::perfil');
    $routes->post('perfil/actualizar', 'AdminBienestarController::actualizarPerfil');

    $routes->get('cuenta', 'AdminBienestarController::cuenta');
    $routes->post('cuenta/cambiarPassword', 'AdminBienestarController::cambiarPassword', ['filter' => ['auth', 'ratelimit:5,900']]);

    $routes->get('cuenta/exportarDatos', 'AdminBienestarController::exportarDatos');

    // ─── Reportes ──────────────────────────────────────────────────────────
    $routes->get('obtener-estadisticas-becas', 'AdminBienestarController::obtenerEstadisticasBecas');
});

// Rutas de test/debug DESHABILITADAS en producción (comentadas)
// $routes->get('admin-bienestar/test-solicitudes', 'AdminBienestarController::testSolicitudes');
// $routes->get('admin-bienestar/test-solicitudes-becas', 'AdminBienestarController::testSolicitudesBecas');
// $routes->get('admin-bienestar/test-periodos-academicos', 'AdminBienestarController::testPeriodosAcademicos');
// $routes->get('admin-bienestar/insertar-datos-prueba', 'AdminBienestarController::insertarDatosPrueba');
// $routes->get('admin-bienestar/test-simple-view', 'AdminBienestarController::testSimpleView');
// $routes->get('admin-bienestar/test-publico', 'AdminBienestarController::testPublico');
// $routes->get('admin-bienestar/test-publico-view', 'AdminBienestarController::testPublicoView');
// $routes->get('admin-bienestar/test-correcciones', 'AdminBienestarController::testCorrecciones');
// $routes->get('admin-bienestar/test-layout', 'AdminBienestarController::testLayout');
// $routes->get('admin-bienestar/test-simple', 'AdminBienestarController::testSimple');

// Rutas legacy de SuperAdmin (migradas a global-admin)
// $routes->group('super-admin', ['filter' => ['auth', 'role:4']], function($routes) {
//     $routes->get('/', 'SuperAdminController::index');
//     $routes->get('dashboard', 'SuperAdminController::index');
//     $routes->get('gestion-roles', 'SuperAdminController::gestionRoles');
//     $routes->get('ver-rol/(:num)', 'SuperAdminController::verRol/$1');
//     $routes->get('gestion-usuarios', 'SuperAdminController::gestionUsuarios');
//     $routes->get('ver-usuario/(:num)', 'SuperAdminController::verUsuario/$1');
//     $routes->post('cambiar-estado-usuario', 'SuperAdminController::cambiarEstadoUsuario');
//     $routes->post('cambiar-rol-usuario', 'SuperAdminController::cambiarRolUsuario');
//     $routes->get('reportes', 'SuperAdminController::reportes');
//     $routes->get('configuracion', 'SuperAdminController::configuracion');
// });

// ============================================================================
// RUTAS Estudiante (agrupadas con auth)
// ============================================================================
$routes->group('estudiante', ['filter' => ['auth', 'role:1']], function($routes) {

    // Páginas principales
    $routes->get('', 'EstudianteController::index');
    $routes->get('ficha-socioeconomica', 'EstudianteController::fichaSocioeconomica');
    $routes->get('becas', 'EstudianteController::becas');
    $routes->get('solicitudes-ayuda', 'EstudianteController::solicitudesAyuda');
    $routes->get('documentos', 'EstudianteController::documentos');
    $routes->get('perfil', 'EstudianteController::perfil');
    $routes->get('cuenta', 'EstudianteController::cuenta');

    // Fichas
    $routes->post('crear-ficha', 'EstudianteController::crearFicha');
    $routes->post('enviar-ficha', 'EstudianteController::enviarFicha');
    $routes->get('ver-ficha/(:num)', 'EstudianteController::verFicha/$1');
    $routes->get('editar-ficha/(:num)', 'EstudianteController::editarFicha/$1');
    $routes->post('actualizar-ficha', 'EstudianteController::actualizarFicha');
    $routes->get('exportar-ficha-pdf/(:num)', 'EstudianteController::exportarFichaPDF/$1');

    // Becas - solicitudes
    $routes->post('solicitar-beca', 'EstudianteController::solicitarBeca');
    $routes->post('cancelar-solicitud-beca', 'EstudianteController::cancelarSolicitudBeca');
    $routes->post('obtener-becas-disponibles', 'EstudianteController::obtenerBecasDisponibles');
    $routes->post('verificar-elegibilidad-beca', 'DocumentoBecaController::verificarElegibilidadBeca');
    $routes->get('estado-solicitud-beca/(:num)', 'EstudianteController::estadoSolicitudBeca/$1');
    $routes->post('actualizar-documentos-beca', 'DocumentoBecaController::actualizarDocumentosBeca');
    $routes->get('descargar-documento-beca/(:num)', 'DocumentoBecaController::descargarDocumentoBeca/$1');
    $routes->get('documentos-beca/(:num)', 'DocumentoBecaController::documentosBeca/$1');

    // Solicitudes de ayuda
    $routes->post('crear-solicitud-ayuda', 'EstudianteController::crearSolicitudAyuda');
    $routes->post('editar-solicitud-ayuda', 'EstudianteController::editarSolicitudAyuda');
    $routes->post('cancelar-solicitud-ayuda', 'EstudianteController::cancelarSolicitudAyuda');

    // Documentos
    $routes->post('subir-documento', 'DocumentoBecaController::subirDocumento');
    $routes->get('descargar-documento/(:num)', 'DocumentoBecaController::descargarDocumento/$1');
    $routes->post('eliminar-documento', 'DocumentoBecaController::eliminarDocumento');

    // Perfil y cuenta
    $routes->post('actualizar-perfil', 'EstudianteController::actualizarPerfil');
    $routes->post('cambiar-foto', 'EstudianteController::cambiarFoto');
    $routes->post('cambiar-password', 'EstudianteController::cambiarPassword', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('configurar-notificaciones', 'EstudianteController::configurarNotificaciones');
    $routes->get('exportar-datos', 'EstudianteController::exportarDatos');
    $routes->post('eliminar-cuenta', 'EstudianteController::eliminarCuenta', ['filter' => ['auth', 'ratelimit:3,3600']]);

    // Información
    $routes->get('informacion/servicios', 'EstudianteController::informacionServicios');
    $routes->get('informacion/becas', 'EstudianteController::informacionBecas');
    $routes->get('informacion/psicologia', 'EstudianteController::informacionPsicologia');
    $routes->get('informacion/salud', 'EstudianteController::informacionSalud');
    $routes->get('informacion/trabajo-social', 'EstudianteController::informacionTrabajoSocial');
    $routes->get('informacion/orientacion-academica', 'EstudianteController::informacionOrientacionAcademica');

    // Sistema mejorado de becas
    $routes->get('verificar-habilitacion-becas', 'EstudianteController::verificarHabilitacionBecas');
    $routes->get('solicitud-beca/(:num)', 'EstudianteController::detalleSolicitudBeca/$1');
    $routes->get('detalleBeca/(:num)', 'EstudianteController::detalleBeca/$1');
    $routes->get('solicitudes-ayuda-mejorada', 'EstudianteController::solicitudesAyudaMejorada');
});

// Rutas de prueba DESHABILITADAS
// $routes->get('estudiante/test-crear-ficha', 'EstudianteController::testCrearFicha');

// ============================================================================
// RUTAS GlobalAdmin (agrupadas con auth + role:4)
// ============================================================================
$routes->group('global-admin', ['filter' => ['auth', 'role:4']], function($routes) {
    $routes->get('dashboard', 'GlobalAdmin\\GlobalAdminController::dashboard');
    $routes->get('usuarios', 'GlobalAdmin\\GlobalAdminController::gestionUsuarios');
    $routes->get('roles', 'GlobalAdmin\\GlobalAdminController::gestionRoles');
    $routes->get('configuracion', 'GlobalAdmin\\GlobalAdminController::configuracionSistema');
    $routes->get('respaldos', 'GlobalAdmin\\GlobalAdminController::respaldos');
    $routes->get('logs', 'GlobalAdmin\\GlobalAdminController::logs');
    $routes->get('estadisticas', 'GlobalAdmin\\GlobalAdminController::estadisticas');

    // Perfil y cuenta del Super Administrador
    $routes->get('perfil', 'GlobalAdmin\\GlobalAdminController::perfil');
    $routes->post('perfil/actualizar', 'GlobalAdmin\\GlobalAdminController::actualizarPerfil');
    $routes->post('perfil/cambiarFoto', 'GlobalAdmin\\GlobalAdminController::cambiarFotoPerfil');
    $routes->get('cuenta', 'GlobalAdmin\\GlobalAdminController::cuenta');
    $routes->post('cuenta/cambiarPassword', 'GlobalAdmin\\GlobalAdminController::cambiarPassword', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('cuenta/configuracionNotificaciones', 'GlobalAdmin\\GlobalAdminController::configuracionNotificaciones');
    $routes->post('cuenta/eliminarCuenta', 'GlobalAdmin\\GlobalAdminController::eliminarCuenta', ['filter' => ['auth', 'ratelimit:3,3600']]);
    $routes->get('cuenta/exportarDatos', 'GlobalAdmin\\GlobalAdminController::exportarDatos');

    // AJAX
    $routes->post('crear-backup', 'GlobalAdmin\\GlobalAdminController::crearBackup');
    $routes->post('restaurar-backup', 'GlobalAdmin\\GlobalAdminController::restaurarBackup', ['filter' => ['auth', 'ratelimit:3,3600']]);
    $routes->post('actualizar-configuracion', 'GlobalAdmin\\GlobalAdminController::actualizarConfiguracion');

    // Gestión de usuarios
    $routes->post('crear-usuario', 'GlobalAdmin\\GlobalAdminController::crearUsuario', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('actualizar-usuario', 'GlobalAdmin\\GlobalAdminController::actualizarUsuario', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('eliminar-usuario', 'GlobalAdmin\\GlobalAdminController::eliminarUsuario', ['filter' => ['auth', 'ratelimit:5,3600']]);
    $routes->get('obtener-usuario/(:num)', 'GlobalAdmin\\GlobalAdminController::obtenerUsuario/$1');
    $routes->get('exportar-usuarios-pdf', 'GlobalAdmin\\GlobalAdminController::exportarUsuariosPDF');

    // Métricas
    $routes->get('metricas-rendimiento', 'GlobalAdmin\\GlobalAdminController::getMetricasRendimiento');

    // Gestión de roles
    $routes->post('crear-rol', 'GlobalAdmin\\GlobalAdminController::crearRol', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('actualizar-rol', 'GlobalAdmin\\GlobalAdminController::actualizarRol', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('eliminar-rol', 'GlobalAdmin\\GlobalAdminController::eliminarRol', ['filter' => ['auth', 'ratelimit:5,3600']]);
    $routes->get('obtener-rol/(:num)', 'GlobalAdmin\\GlobalAdminController::obtenerRol/$1');
    $routes->get('permisos-rol/(:num)', 'GlobalAdmin\\GlobalAdminController::obtenerPermisosRol/$1');

    // Respaldos (consolidado — se mantienen rutas en español)
    $routes->get('descargar-respaldo/(:num)', 'GlobalAdmin\\GlobalAdminController::descargarRespaldo/$1');
    $routes->post('crear-respaldo', 'GlobalAdmin\\GlobalAdminController::crearRespaldo', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('restaurar-respaldo', 'GlobalAdmin\\GlobalAdminController::restaurarRespaldo', ['filter' => ['auth', 'ratelimit:3,3600']]);
    $routes->post('eliminar-respaldo', 'GlobalAdmin\\GlobalAdminController::eliminarRespaldo', ['filter' => ['auth', 'ratelimit:5,3600']]);
    $routes->post('limpiar-respaldos', 'GlobalAdmin\\GlobalAdminController::limpiarRespaldos', ['filter' => ['auth', 'ratelimit:3,3600']]);
    $routes->post('enviar-respaldo-email', 'GlobalAdmin\\GlobalAdminController::enviarRespaldoPorEmail', ['filter' => ['auth', 'ratelimit:5,900']]);
    $routes->post('guardar-configuracion-respaldos', 'GlobalAdmin\\GlobalAdminController::guardarConfiguracionRespaldos');
    $routes->get('obtener-respaldos', 'GlobalAdmin\\GlobalAdminController::obtenerRespaldos');
    $routes->get('estadisticas-respaldos', 'GlobalAdmin\\GlobalAdminController::estadisticasRespaldos');

    // Logs
    $routes->get('obtener-logs', 'GlobalAdmin\\GlobalAdminController::obtenerLogs');
    $routes->get('obtener-log/(:num)', 'GlobalAdmin\\GlobalAdminController::obtenerLog/$1');
    $routes->post('eliminar-log', 'GlobalAdmin\\GlobalAdminController::eliminarLog', ['filter' => ['auth', 'ratelimit:10,900']]);
    $routes->post('limpiar-logs', 'GlobalAdmin\\GlobalAdminController::limpiarLogs', ['filter' => ['auth', 'ratelimit:3,3600']]);
    $routes->get('exportar-logs', 'GlobalAdmin\\GlobalAdminController::exportarLogs');
    $routes->get('estadisticas-logs', 'GlobalAdmin\\GlobalAdminController::estadisticasLogs');

    // Estadísticas
    $routes->get('obtener-estadisticas-globales', 'GlobalAdmin\\GlobalAdminController::obtenerEstadisticasGlobales');

    // Vistas de perfiles
    $routes->get('vista-estudiante', 'GlobalAdmin\\GlobalAdminController::vistaEstudiante');
    $routes->get('vista-admin-bienestar', 'GlobalAdmin\\GlobalAdminController::vistaAdminBienestar');

    // Configuración del Sistema
    $routes->post('guardar-configuracion', 'GlobalAdmin\\GlobalAdminController::guardarConfiguracion', ['filter' => ['auth', 'ratelimit:10,900']]);
});
