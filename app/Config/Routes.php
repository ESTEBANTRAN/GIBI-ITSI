<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('auth/attemptLogin', 'AuthController::attemptLogin', ['filter' => 'ratelimit:10,900']);

// Rutas para recuperación de contraseña
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/verifyIdentity', 'AuthController::verifyIdentity');
$routes->get('reset-password/(:alphanum)', 'AuthController::resetPasswordForm/$1');
$routes->post('auth/resetPassword', 'AuthController::resetPassword');

// Ruta para limpiar sesiones (solo en desarrollo)
// $routes->get('clear-session', 'ClearSessionController::index');

// Rutas de prueba DESHABILITADAS en producción
// $routes->get('test', 'TestController::index');
// $routes->get('test-global-admin', 'TestGlobalAdminController::index');

$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('admin-bienestar', 'DashboardController::adminBienestar', ['filter' => 'auth']);
$routes->get('ficha', 'FichaController::index', ['filter' => 'auth']);
$routes->get('becas', 'BecaController::index', ['filter' => 'auth']);
$routes->get('solicitudes', 'SolicitudController::index', ['filter' => 'auth']);
$routes->get('fichas', 'FichaController::adminIndex', ['filter' => 'auth']);
$routes->get('solicitudes-becas', 'BecaController::adminIndex', ['filter' => 'auth']);
$routes->get('reportes', 'ReporteController::index', ['filter' => 'auth']);

// Rutas para estadísticas del dashboard
$routes->get('dashboard/estadisticas', 'DashboardController::getEstadisticas');
$routes->get('dashboard/actividad', 'DashboardController::getActividadReciente');
$routes->post('dashboard/actualizar', 'DashboardController::actualizarDashboard');

// Rutas para AdminBienestar - Dashboard y Estadísticas
$routes->get('admin-bienestar/dashboard', 'AdminBienestarController::dashboard');
$routes->get('admin-bienestar/getEstadisticas', 'AdminBienestarController::getEstadisticas');

// Rutas para AdminBienestar - Páginas principales mejoradas
$routes->get('admin-bienestar/fichas-socioeconomicas', 'AdminBienestarController::fichasSocioeconomicas');
$routes->get('admin-bienestar/gestion-periodos', 'AdminBienestarController::gestionPeriodos');
$routes->get('admin-bienestar/becas', 'AdminBienestarController::becas');
$routes->get('admin-bienestar/estudiantes', 'AdminBienestarController::usuarios');
$routes->get('admin-bienestar/usuarios', 'AdminBienestarController::usuarios');
$routes->get('admin-bienestar/solicitudes-becas', 'AdminBienestarController::solicitudesBecasMejorada');
$routes->get('admin-bienestar/reportes', 'AdminBienestarController::reportes');

// Rutas para gestión de períodos académicos
$routes->post('admin-bienestar/crearPeriodo', 'AdminBienestarController::crearPeriodo');
$routes->post('admin-bienestar/actualizarPeriodo', 'AdminBienestarController::actualizarPeriodo');
$routes->post('admin-bienestar/duplicarPeriodo', 'AdminBienestarController::duplicarPeriodo');
$routes->post('admin-bienestar/actualizarLimitesPeriodo', 'AdminBienestarController::actualizarLimitesPeriodo');
$routes->post('admin-bienestar/toggleConfiguracionPeriodo', 'AdminBienestarController::toggleConfiguracionPeriodo');
$routes->get('admin-bienestar/exportarPeriodos', 'AdminBienestarController::exportarPeriodos');
$routes->get('admin-bienestar/detallePeriodo/(:num)', 'AdminBienestarController::detallePeriodo/$1');

// Rutas para revisión de documentos y solicitudes de becas
$routes->get('admin-bienestar/revision-documentos/(:num)', 'AdminBienestarController::revisionDocumentos/$1');
$routes->post('admin-bienestar/aprobar-documento', 'AdminBienestarController::aprobarDocumento');
$routes->post('admin-bienestar/rechazar-documento', 'AdminBienestarController::rechazarDocumento');
$routes->post('admin-bienestar/aprobar-solicitud-beca', 'AdminBienestarController::aprobarSolicitudBeca');
$routes->post('admin-bienestar/rechazar-solicitud-beca', 'AdminBienestarController::rechazarSolicitudBeca');
$routes->get('admin-bienestar/ver-documento/(:num)', 'AdminBienestarController::verDocumento/$1');

// Rutas del Super Admin
$routes->group('super-admin', ['filter' => ['auth', 'role:4']], function($routes) {
    $routes->get('/', 'SuperAdminController::index');
    $routes->get('dashboard', 'SuperAdminController::index');
    $routes->get('gestion-roles', 'SuperAdminController::gestionRoles');
    $routes->get('ver-rol/(:num)', 'SuperAdminController::verRol/$1');
    $routes->get('gestion-usuarios', 'SuperAdminController::gestionUsuarios');
    $routes->get('ver-usuario/(:num)', 'SuperAdminController::verUsuario/$1');
    $routes->post('cambiar-estado-usuario', 'SuperAdminController::cambiarEstadoUsuario');
    $routes->post('cambiar-rol-usuario', 'SuperAdminController::cambiarRolUsuario');
    $routes->get('reportes', 'SuperAdminController::reportes');
    $routes->get('configuracion', 'SuperAdminController::configuracion');
});

// Rutas para gestión de solicitudes de ayuda
$routes->get('admin-bienestar/solicitudes-ayuda', 'AdminBienestarController::solicitudesAyudaMejorada');
$routes->get('admin-bienestar/detalle-solicitud-ayuda/(:num)', 'AdminBienestarController::detalleSolicitudAyuda/$1');
$routes->post('admin-bienestar/responder-solicitud-ayuda', 'AdminBienestarController::responderSolicitudAyuda');
$routes->post('admin-bienestar/marcar-solicitud-resuelta', 'AdminBienestarController::marcarSolicitudResuelta');
$routes->post('admin-bienestar/asignar-solicitud', 'AdminBienestarController::asignarSolicitud');
$routes->post('admin-bienestar/cambiar-prioridad', 'AdminBienestarController::cambiarPrioridad');
$routes->post('admin-bienestar/cerrar-solicitud', 'AdminBienestarController::cerrarSolicitud');
$routes->get('admin-bienestar/historial-solicitudes-estudiante/(:num)', 'AdminBienestarController::historialSolicitudesEstudiante/$1');
$routes->get('admin-bienestar/exportar-solicitudes', 'AdminBienestarController::exportarSolicitudes');
$routes->post('admin-bienestar/crear-respuesta-rapida', 'AdminBienestarController::crearRespuestaRapida');
$routes->post('admin-bienestar/guardar-respuesta-predefinida', 'AdminBienestarController::guardarRespuestaPredefinida');
$routes->get('admin-bienestar/obtener-respuestas-predefinidas', 'AdminBienestarController::obtenerRespuestasPredefinidas');
$routes->post('admin-bienestar/eliminar-respuesta-predefinida', 'AdminBienestarController::eliminarRespuestaPredefinida');

// Rutas para gestión de períodos académicos
$routes->get('admin-bienestar/obtener-periodo/(:num)', 'AdminBienestarController::obtenerPeriodo/$1');
$routes->post('admin-bienestar/crear-periodo', 'AdminBienestarController::crearPeriodo');
$routes->post('admin-bienestar/actualizar-periodo', 'AdminBienestarController::actualizarPeriodo');
$routes->post('admin-bienestar/eliminar-periodo', 'AdminBienestarController::eliminarPeriodo');
$routes->get('admin-bienestar/ver-periodo/(:num)', 'AdminBienestarController::verPeriodo/$1');
$routes->get('admin-bienestar/exportar-periodos', 'AdminBienestarController::exportarPeriodos');
$routes->post('admin-bienestar/actualizar-contadores-periodos', 'AdminBienestarController::actualizarContadoresPeriodos');

// Rutas para gestión de becas
$routes->get('admin-bienestar/configuracion-becas', 'AdminBienestarController::configuracionBecas');
$routes->post('admin-bienestar/crear-beca', 'AdminBienestarController::crearBeca');
$routes->get('admin-bienestar/obtener-beca/(:num)', 'AdminBienestarController::obtenerBeca/$1');
$routes->post('admin-bienestar/actualizar-beca', 'AdminBienestarController::actualizarBeca');
$routes->post('admin-bienestar/eliminar-beca', 'AdminBienestarController::eliminarBeca');
$routes->post('admin-bienestar/toggle-estado-beca', 'AdminBienestarController::toggleEstadoBeca');

$routes->get('admin-bienestar/exportar-becas', 'AdminBienestarController::exportarBecas');
$routes->get('admin-bienestar/estadisticas-becas', 'AdminBienestarController::getEstadisticasBecas');

// Rutas para gestión de usuarios
$routes->get('admin-bienestar/usuario/(:num)', 'AdminBienestarController::verUsuario/$1');
$routes->post('admin-bienestar/usuario/crear', 'AdminBienestarController::crearUsuario');
$routes->post('admin-bienestar/usuario/cambiar-estado', 'AdminBienestarController::cambiarEstadoUsuario');
$routes->post('admin-bienestar/usuario/resetear-password', 'AdminBienestarController::resetearPasswordUsuario');
$routes->get('admin-bienestar/usuarios/exportar', 'AdminBienestarController::exportarUsuarios');

// Rutas legacy para compatibilidad
$routes->get('estudiantes', 'AdminBienestarController::usuarios');
$routes->get('usuarios/admin', 'AdminBienestarController::usuarios');
$routes->get('usuarios/roles', 'AdminBienestarController::usuarios');
$routes->get('configuracion/periodos', 'AdminBienestarController::gestionPeriodosAcademicos');
// $routes->get('configuracion/becas', 'AdminBienestarController::becas'); // RUTA CONFLICTIVA - COMENTADA
// $routes->get('configuracion/sistema', 'AdminBienestarController::configuracionSistema'); // MOVIDO AL SUPERADMIN
$routes->get('solicitudes/comunicacion', 'AdminBienestarController::solicitudesBecas');
$routes->get('solicitudes/integracion', 'AdminBienestarController::solicitudesBecas');

// Rutas específicas para perfil y cuenta de AdminBienestar
$routes->get('admin-bienestar/perfil', 'AdminBienestarController::perfil');
$routes->post('admin-bienestar/perfil/actualizar', 'AdminBienestarController::actualizarPerfil');
$routes->post('admin-bienestar/perfil/cambiarFoto', 'AdminBienestarController::cambiarFotoPerfil');
$routes->get('admin-bienestar/cuenta', 'AdminBienestarController::cuenta');
$routes->post('admin-bienestar/cuenta/cambiarPassword', 'AdminBienestarController::cambiarPassword');
$routes->post('admin-bienestar/cuenta/configuracionNotificaciones', 'AdminBienestarController::configuracionNotificaciones');
$routes->post('admin-bienestar/cuenta/eliminarCuenta', 'AdminBienestarController::eliminarCuenta');
$routes->get('admin-bienestar/cuenta/exportarDatos', 'AdminBienestarController::exportarDatos');

// Rutas para GlobalAdmin (Super Administrador)
$routes->group('global-admin', ['filter' => ['auth', 'role:4']], function($routes) {
    $routes->get('dashboard', 'GlobalAdmin\GlobalAdminController::dashboard');
    $routes->get('usuarios', 'GlobalAdmin\GlobalAdminController::gestionUsuarios');
    $routes->get('roles', 'GlobalAdmin\GlobalAdminController::gestionRoles');
    $routes->get('configuracion', 'GlobalAdmin\GlobalAdminController::configuracionSistema');
    $routes->get('respaldos', 'GlobalAdmin\GlobalAdminController::respaldos');
    $routes->get('logs', 'GlobalAdmin\GlobalAdminController::logs');
    $routes->get('estadisticas', 'GlobalAdmin\GlobalAdminController::estadisticas');

    // Rutas específicas para perfil y cuenta del Super Administrador
    $routes->get('perfil', 'GlobalAdmin\GlobalAdminController::perfil');
    $routes->post('perfil/actualizar', 'GlobalAdmin\GlobalAdminController::actualizarPerfil');
    $routes->post('perfil/cambiarFoto', 'GlobalAdmin\GlobalAdminController::cambiarFotoPerfil');
    $routes->get('cuenta', 'GlobalAdmin\GlobalAdminController::cuenta');
    $routes->post('cuenta/cambiarPassword', 'GlobalAdmin\GlobalAdminController::cambiarPassword');
    $routes->post('cuenta/configuracionNotificaciones', 'GlobalAdmin\GlobalAdminController::configuracionNotificaciones');
    $routes->post('cuenta/eliminarCuenta', 'GlobalAdmin\GlobalAdminController::eliminarCuenta');
    $routes->get('cuenta/exportarDatos', 'GlobalAdmin\GlobalAdminController::exportarDatos');

    // Rutas AJAX para GlobalAdmin
    $routes->post('crear-backup', 'GlobalAdmin\GlobalAdminController::crearBackup');
    $routes->post('restaurar-backup', 'GlobalAdmin\GlobalAdminController::restaurarBackup');
    $routes->post('actualizar-configuracion', 'GlobalAdmin\GlobalAdminController::actualizarConfiguracion');

    // Rutas para gestión de usuarios (GlobalAdmin)
    $routes->post('crear-usuario', 'GlobalAdmin\GlobalAdminController::crearUsuario');
    $routes->post('actualizar-usuario', 'GlobalAdmin\GlobalAdminController::actualizarUsuario');
    $routes->post('eliminar-usuario', 'GlobalAdmin\GlobalAdminController::eliminarUsuario');
    $routes->get('obtener-usuario/(:num)', 'GlobalAdmin\GlobalAdminController::obtenerUsuario/$1');
    $routes->get('exportar-usuarios-pdf', 'GlobalAdmin\GlobalAdminController::exportarUsuariosPDF');
    // Rutas de test DESHABILITADAS
    // $routes->get('test-busqueda', 'GlobalAdmin\GlobalAdminController::testBusqueda');
    // $routes->get('test-busqueda-detallada', 'GlobalAdmin\GlobalAdminController::testBusquedaDetallada');

    // Rutas para métricas avanzadas de SuperAdmin
    $routes->get('metricas-rendimiento', 'GlobalAdmin\GlobalAdminController::getMetricasRendimiento');
    $routes->get('supervision-bienestar', 'GlobalAdmin\GlobalAdminController::supervisionBienestar');

    // Rutas para gestión de roles (GlobalAdmin)
    $routes->post('crear-rol', 'GlobalAdmin\GlobalAdminController::crearRol');
    $routes->post('actualizar-rol', 'GlobalAdmin\GlobalAdminController::actualizarRol');
    $routes->post('eliminar-rol', 'GlobalAdmin\GlobalAdminController::eliminarRol');
    $routes->get('obtener-rol/(:num)', 'GlobalAdmin\GlobalAdminController::obtenerRol/$1');
    $routes->get('permisos-rol/(:num)', 'GlobalAdmin\GlobalAdminController::obtenerPermisosRol/$1');

    // Rutas adicionales para respaldos
    $routes->get('descargar-backup/(:num)', 'GlobalAdmin\GlobalAdminController::descargarBackup/$1');
    $routes->post('eliminar-backup', 'GlobalAdmin\GlobalAdminController::eliminarBackup');
    $routes->post('limpiar-backups', 'GlobalAdmin\GlobalAdminController::limpiarBackups');
    $routes->post('guardar-configuracion-backup', 'GlobalAdmin\GlobalAdminController::guardarConfiguracionBackup');

    // Rutas para SuperAdmin - Configuración del Sistema
    $routes->get('configuracion', 'GlobalAdmin\GlobalAdminController::configuracionSistema');
    $routes->post('guardar-configuracion', 'GlobalAdmin\GlobalAdminController::guardarConfiguracion');

    // Rutas para SuperAdmin - Respaldos
    $routes->post('crear-respaldo', 'GlobalAdmin\GlobalAdminController::crearRespaldo');
    $routes->get('obtener-lista-respaldos', 'GlobalAdmin\GlobalAdminController::obtenerRespaldos');
    $routes->get('obtener-respaldos', 'GlobalAdmin\GlobalAdminController::obtenerRespaldos'); // Para cache antiguo
    $routes->post('restaurar-respaldo', 'GlobalAdmin\GlobalAdminController::restaurarRespaldo');
    $routes->get('descargar-respaldo/(:num)', 'GlobalAdmin\GlobalAdminController::descargarRespaldo/$1');
    $routes->post('enviar-respaldo-email', 'GlobalAdmin\GlobalAdminController::enviarRespaldoPorEmail');
    $routes->post('eliminar-respaldo', 'GlobalAdmin\GlobalAdminController::eliminarRespaldo');
    $routes->post('limpiar-respaldos', 'GlobalAdmin\GlobalAdminController::limpiarRespaldos');
    $routes->post('guardar-configuracion-respaldos', 'GlobalAdmin\GlobalAdminController::guardarConfiguracionRespaldos');
    $routes->get('estadisticas-respaldos', 'GlobalAdmin\GlobalAdminController::estadisticasRespaldos');

    // Rutas para SuperAdmin - Logs
    $routes->get('obtener-logs', 'GlobalAdmin\GlobalAdminController::obtenerLogs');
    $routes->get('obtener-log/(:num)', 'GlobalAdmin\GlobalAdminController::obtenerLog/$1');
    $routes->post('eliminar-log', 'GlobalAdmin\GlobalAdminController::eliminarLog');
    $routes->post('limpiar-logs', 'GlobalAdmin\GlobalAdminController::limpiarLogs');
    $routes->get('exportar-logs', 'GlobalAdmin\GlobalAdminController::exportarLogs');
    $routes->get('estadisticas-logs', 'GlobalAdmin\GlobalAdminController::estadisticasLogs');

    // Rutas para SuperAdmin - Estadísticas
    $routes->get('obtener-estadisticas-globales', 'GlobalAdmin\GlobalAdminController::obtenerEstadisticasGlobales');

    // Rutas para acceso rápido a vistas de perfiles
    $routes->get('vista-estudiante', 'GlobalAdmin\GlobalAdminController::vistaEstudiante');
    $routes->get('vista-admin-bienestar', 'GlobalAdmin\GlobalAdminController::vistaAdminBienestar');
});

// Rutas genéricas para perfil (redirigen según rol)
$routes->get('perfil/editar', 'PerfilController::editar');
$routes->post('perfil/actualizar', 'PerfilController::actualizar');
$routes->post('perfil/cambiarFoto', 'PerfilController::cambiarFoto');

// Ruta para cambiar foto de perfil
$routes->post('profile/cambiar-foto', 'ProfileController::cambiarFotoPerfil');

// Rutas genéricas para cuenta (redirigen según rol)
$routes->get('cuenta/configuracion', 'CuentaController::configuracion');
$routes->post('cuenta/cambiarPassword', 'CuentaController::cambiarPassword');
$routes->post('cuenta/configuracionNotificaciones', 'CuentaController::configuracionNotificaciones');
$routes->post('cuenta/eliminarCuenta', 'CuentaController::eliminarCuenta');
$routes->get('cuenta/exportarDatos', 'CuentaController::exportarDatos');

// ¡ESTA ES LA LÍNEA QUE DEBES CAMBIAR!
// Ahora la ruta coincide con la URL generada por base_url('auth/logout')
$routes->get('auth/logout', 'AuthController::logout');

//Estudiante Routes
$routes->get('estudiante', 'EstudianteController::index');
$routes->get('estudiante/ficha-socioeconomica', 'EstudianteController::fichaSocioeconomica');
$routes->get('estudiante/becas', 'EstudianteController::becas');
$routes->get('estudiante/solicitudes-ayuda', 'EstudianteController::solicitudesAyuda');
$routes->get('estudiante/documentos', 'EstudianteController::documentos');
$routes->get('estudiante/perfil', 'EstudianteController::perfil');
$routes->get('estudiante/cuenta', 'EstudianteController::cuenta');

// Rutas adicionales para Estudiante
$routes->post('estudiante/crear-ficha', 'EstudianteController::crearFicha');
$routes->post('estudiante/enviar-ficha', 'EstudianteController::enviarFicha');
$routes->get('estudiante/ver-ficha/(:num)', 'EstudianteController::verFicha/$1');
$routes->get('estudiante/editar-ficha/(:num)', 'EstudianteController::editarFicha/$1');
$routes->post('estudiante/actualizar-ficha', 'EstudianteController::actualizarFicha');
$routes->get('estudiante/exportar-ficha-pdf/(:num)', 'EstudianteController::exportarFichaPDF/$1');
$routes->post('estudiante/solicitar-beca', 'EstudianteController::solicitarBeca');
$routes->post('estudiante/cancelar-solicitud-beca', 'EstudianteController::cancelarSolicitudBeca');
$routes->post('estudiante/crear-solicitud-ayuda', 'EstudianteController::crearSolicitudAyuda');
$routes->post('estudiante/editar-solicitud-ayuda', 'EstudianteController::editarSolicitudAyuda');
$routes->post('estudiante/cancelar-solicitud-ayuda', 'EstudianteController::cancelarSolicitudAyuda');
$routes->post('estudiante/subir-documento', 'EstudianteController::subirDocumento');
$routes->get('estudiante/descargar-documento/(:num)', 'EstudianteController::descargarDocumento/$1');
$routes->post('estudiante/eliminar-documento', 'EstudianteController::eliminarDocumento');
$routes->post('estudiante/actualizar-perfil', 'EstudianteController::actualizarPerfil');
$routes->post('estudiante/cambiar-foto', 'EstudianteController::cambiarFoto');
$routes->post('estudiante/cambiar-password', 'EstudianteController::cambiarPassword');
$routes->post('estudiante/configurar-notificaciones', 'EstudianteController::configurarNotificaciones');
$routes->get('estudiante/exportar-datos', 'EstudianteController::exportarDatos');
$routes->post('estudiante/eliminar-cuenta', 'EstudianteController::eliminarCuenta');

// Rutas para Estudiante - Sección de Información
$routes->get('estudiante/informacion/servicios', 'EstudianteController::informacionServicios');
$routes->get('estudiante/informacion/becas', 'EstudianteController::informacionBecas');
$routes->get('estudiante/informacion/psicologia', 'EstudianteController::informacionPsicologia');
$routes->get('estudiante/informacion/salud', 'EstudianteController::informacionSalud');
$routes->get('estudiante/informacion/trabajo-social', 'EstudianteController::informacionTrabajoSocial');
$routes->get('estudiante/informacion/orientacion-academica', 'EstudianteController::informacionOrientacionAcademica');

// Rutas para AdminBienestar - Gestión de Fichas Socioeconómicas (Mejoradas)
$routes->post('admin-bienestar/actualizar-estado-ficha', 'AdminBienestarController::actualizarEstadoFicha');
$routes->get('admin-bienestar/verFicha/(:num)', 'AdminBienestarController::verFicha/$1');
$routes->post('admin-bienestar/marcar-revisada/(:num)', 'AdminBienestarController::marcarComoRevisada/$1');
$routes->post('admin-bienestar/aprobar-ficha/(:num)', 'AdminBienestarController::aprobarFicha/$1');
$routes->post('admin-bienestar/rechazar-ficha/(:num)', 'AdminBienestarController::rechazarFicha/$1');
$routes->post('admin-bienestar/agregar-observacion-ficha', 'AdminBienestarController::agregarObservacionFicha');
$routes->get('admin-bienestar/exportar-ficha-pdf/(:num)', 'AdminBienestarController::exportarFichaPDF/$1');
$routes->post('admin-bienestar/exportarDatos', 'AdminBienestarController::exportarDatos');
$routes->post('admin-bienestar/generarReporte', 'AdminBienestarController::generarReporte');

// Rutas para AdminBienestar - Vista previa de fichas
$routes->get('admin-bienestar/ver-ficha/(:num)', 'AdminBienestarController::verFicha/$1');
$routes->get('admin-bienestar/ver-ficha-socioeconomica/(:num)', 'AdminBienestarController::verFichaSocioeconomica/$1');

// Rutas para AdminBienestar - Gestión de Períodos Académicos
$routes->post('admin-bienestar/crear-periodo', 'AdminBienestarController::crearPeriodo');
$routes->post('admin-bienestar/actualizar-periodo', 'AdminBienestarController::actualizarPeriodo');
$routes->post('admin-bienestar/eliminar-periodo', 'AdminBienestarController::eliminarPeriodo');
$routes->get('admin-bienestar/obtener-periodo/(:num)', 'AdminBienestarController::obtenerPeriodo/$1');
$routes->post('admin-bienestar/actualizar-contadores-periodos', 'AdminBienestarController::actualizarContadoresPeriodos');

// Rutas para AdminBienestar - Configuración de Becas
$routes->get('admin-bienestar/configuracion-becas', 'AdminBienestarController::configuracionBecas');
$routes->post('admin-bienestar/configurar-documentos-beca', 'AdminBienestarController::configurarDocumentosBeca');
$routes->post('admin-bienestar/toggle-estado-beca', 'AdminBienestarController::toggleEstadoBeca');

// Rutas para AdminBienestar - Estadísticas
$routes->get('admin-bienestar/obtener-estadisticas-fichas', 'AdminBienestarController::obtenerEstadisticasFichas');

// Rutas para AdminBienestar - Sistema de Becas
$routes->post('admin-bienestar/crear-beca', 'AdminBienestarController::crearBeca');
$routes->post('admin-bienestar/actualizar-beca', 'AdminBienestarController::actualizarBeca');
$routes->post('admin-bienestar/eliminar-beca', 'AdminBienestarController::eliminarBeca');
$routes->post('admin-bienestar/toggle-estado-beca', 'AdminBienestarController::toggleEstadoBeca');
$routes->get('admin-bienestar/obtener-beca/(:num)', 'AdminBienestarController::obtenerBeca/$1');
$routes->post('admin-bienestar/activar-desactivar-beca', 'AdminBienestarController::activarDesactivarBeca');
$routes->get('admin-bienestar/obtener-becas', 'AdminBienestarController::obtenerBecas');
$routes->get('admin-bienestar/exportar-becas-pdf', 'AdminBienestarController::exportarBecasPDF');

// Rutas para AdminBienestar - Gestión de Solicitudes de Becas
$routes->get('admin-bienestar/solicitudes-becas', 'AdminBienestarController::solicitudesBecas');
$routes->get('admin-bienestar/obtener-solicitudes-becas', 'AdminBienestarController::obtenerSolicitudesBecas');
// Rutas de test/debug DESHABILITADAS en producción
// $routes->get('admin-bienestar/test-solicitudes', 'AdminBienestarController::testSolicitudes');
// $routes->get('admin-bienestar/test-solicitudes-becas', 'AdminBienestarController::testSolicitudesBecas');
// $routes->get('admin-bienestar/test-periodos-academicos', 'AdminBienestarController::testPeriodosAcademicos');
// $routes->get('admin-bienestar/insertar-datos-prueba', 'AdminBienestarController::insertarDatosPrueba');
// $routes->get('admin-bienestar/test-simple-view', 'AdminBienestarController::testSimpleView');
// $routes->get('admin-bienestar/test-publico', 'AdminBienestarController::testPublico');
// $routes->get('admin-bienestar/test-publico-view', 'AdminBienestarController::testPublicoView');
// $routes->get('admin-bienestar/test-correcciones', 'AdminBienestarController::testCorrecciones');
$routes->post('admin-bienestar/aprobar-solicitud-beca', 'AdminBienestarController::aprobarSolicitudBeca');
$routes->post('admin-bienestar/rechazar-solicitud-beca', 'AdminBienestarController::rechazarSolicitudBeca');
$routes->post('admin-bienestar/colocar-lista-espera', 'AdminBienestarController::colocarListaEspera');
$routes->get('admin-bienestar/ver-solicitud-beca/(:num)', 'AdminBienestarController::verSolicitudBeca/$1');
$routes->get('admin-bienestar/exportar-solicitud-beca-pdf/(:num)', 'AdminBienestarController::exportarSolicitudBecaPDF/$1');

// Rutas para AdminBienestar - Gestión de Documentos de Becas
$routes->post('admin-bienestar/verificar-documento-beca', 'AdminBienestarController::verificarDocumentoBeca');
$routes->post('admin-bienestar/rechazar-documento-beca', 'AdminBienestarController::rechazarDocumentoBeca');
$routes->get('admin-bienestar/descargar-documento-beca/(:num)', 'AdminBienestarController::descargarDocumentoBeca/$1');
$routes->get('admin-bienestar/ver-documento-beca/(:num)', 'AdminBienestarController::verDocumentoBeca/$1');

// Rutas para AdminBienestar - Gestión de Estudiantes
$routes->get('admin-bienestar/historial-estudiante/(:num)', 'AdminBienestarController::historialEstudiante/$1');
$routes->get('admin-bienestar/estudiantes-sin-beca', 'AdminBienestarController::estudiantesSinBeca');

    // Rutas para gestión de plantillas de PDF
    $routes->get('plantillas', 'PlantillasController::index');
    $routes->get('plantillas/gestionar', 'PlantillasController::gestionar');
    $routes->get('plantillas/probar-personalizada', 'PlantillasController::probarPlantillaPersonalizada');
    $routes->get('plantillas/probar-word', 'PlantillasController::probarPlantillaWord');
    $routes->post('plantillas/subir', 'PlantillasController::subirPlantilla');
    $routes->delete('plantillas/eliminar/(:any)', 'PlantillasController::eliminarPlantilla/$1');
    $routes->get('plantillas/vista-previa/(:any)', 'PlantillasController::vistaPrevia/$1');
    
    // Ruta para verificar códigos de PDF
    $routes->post('verificar-codigo-pdf', 'AdminBienestarController::verificarCodigoPDF');

// Rutas para AdminBienestar - Notificaciones de Becas
$routes->get('admin-bienestar/notificaciones-becas', 'AdminBienestarController::notificacionesBecas');
$routes->post('admin-bienestar/enviar-notificacion-beca', 'AdminBienestarController::enviarNotificacionBeca');
$routes->post('admin-bienestar/marcar-notificacion-leida', 'AdminBienestarController::marcarNotificacionLeida');

// Rutas para Estudiante - Sistema de Becas
$routes->post('estudiante/obtener-becas-disponibles', 'EstudianteController::obtenerBecasDisponibles');
$routes->post('estudiante/verificar-elegibilidad-beca', 'EstudianteController::verificarElegibilidadBeca');
$routes->get('estudiante/estado-solicitud-beca/(:num)', 'EstudianteController::estadoSolicitudBeca/$1');
$routes->post('estudiante/actualizar-documentos-beca', 'EstudianteController::actualizarDocumentosBeca');
$routes->get('estudiante/descargar-documento-beca/(:num)', 'EstudianteController::descargarDocumentoBeca/$1');

// Rutas para AdminBienestar - Reportes de Becas
$routes->get('admin-bienestar/reportes-becas', 'AdminBienestarController::reportesBecas');
$routes->get('admin-bienestar/obtener-estadisticas-becas', 'AdminBienestarController::obtenerEstadisticasBecas');
$routes->get('admin-bienestar/exportar-reporte-becas', 'AdminBienestarController::exportarReporteBecas');

// Rutas de prueba DESHABILITADAS
// $routes->get('estudiante/test-crear-ficha', 'EstudianteController::testCrearFicha');

// Rutas para sistema mejorado de becas estudiantiles
$routes->get('estudiante/verificar-habilitacion-becas', 'EstudianteController::verificarHabilitacionBecas');
$routes->get('estudiante/solicitud-beca/(:num)', 'EstudianteController::detalleSolicitudBeca/$1');
$routes->get('estudiante/documentos-beca/(:num)', 'EstudianteController::documentosBeca/$1');
$routes->get('estudiante/detalleBeca/(:num)', 'EstudianteController::detalleBeca/$1');
$routes->get('estudiante/solicitudes-ayuda-mejorada', 'EstudianteController::solicitudesAyudaMejorada');
// $routes->get('admin-bienestar/test-layout', 'AdminBienestarController::testLayout');
// $routes->get('admin-bienestar/test-simple', 'AdminBienestarController::testSimple');

// Rutas adicionales para el sistema de becas
$routes->get('admin-bienestar/obtener-periodos-academicos', 'AdminBienestarController::obtenerPeriodosAcademicos');
$routes->get('admin-bienestar/obtener-periodo/(:num)', 'AdminBienestarController::obtenerPeriodo/$1');
$routes->get('admin-bienestar/obtener-estadisticas-periodos', 'AdminBienestarController::obtenerEstadisticasPeriodos');
$routes->post('admin-bienestar/toggle-campo-periodo', 'AdminBienestarController::toggleCampoPeriodo');
$routes->get('admin-bienestar/obtener-becas-con-filtros', 'AdminBienestarController::obtenerBecasConFiltros');
$routes->get('admin-bienestar/obtener-becas', 'AdminBienestarController::obtenerBecas');

// Rutas adicionales para configuración (MOVIDO AL SUPERADMIN - global-admin/configuracion)
// $routes->get('admin-bienestar/configuracion-sistema', 'AdminBienestarController::configuracionSistema');
// $routes->post('admin-bienestar/guardar-configuracion', 'AdminBienestarController::guardarConfiguracion');
$routes->post('admin-bienestar/crear-respaldo-manual', 'AdminBienestarController::crearRespaldoManual');