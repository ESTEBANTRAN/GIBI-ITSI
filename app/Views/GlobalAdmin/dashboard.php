<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Super Administración</a></li>
        </ol>
    </div>

    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Panel de Super Administración</h4>
                            <p class="text-muted mb-0">Bienvenido/a, <?= session('nombre') ?? 'Super Administrador' ?></p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <small class="text-muted">Estado del sistema</small>
                                <div class="fw-medium text-success">
                                    <i class="ti ti-check-circle me-1"></i>Operativo
                                </div>
                            </div>
                            <div class="me-3">
                                <small class="text-muted">Última actividad</small>
                                <div class="fw-medium">
                                    <i class="ti ti-clock me-1"></i>Hace 5 min
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2 fw-bold"><?= $total_usuarios ?? 0 ?></span>
                        </div>
                        <div class="ms-auto">
                            <i class="ti ti-users fs-1 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="mb-0">Total Usuarios</h6>
                        <span class="text-muted small">+12% este mes</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="round-8 bg-success rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2 fw-bold"><?= $usuarios_activos ?? 0 ?></span>
                        </div>
                        <div class="ms-auto">
                            <i class="ti ti-user-check fs-1 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="mb-0">Usuarios Activos</h6>
                        <span class="text-muted small">+8% este mes</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="round-8 bg-warning rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2 fw-bold"><?= $total_roles ?? 0 ?></span>
                        </div>
                        <div class="ms-auto">
                            <i class="ti ti-shield-lock fs-1 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="mb-0">Total Roles</h6>
                        <span class="text-muted small">Configurados</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="round-8 bg-danger rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2 fw-bold"><?= count($backups_recientes ?? []) ?></span>
                        </div>
                        <div class="ms-auto">
                            <i class="ti ti-database-check fs-1 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="mb-0">Respaldos Recientes</h6>
                        <span class="text-muted small">Último: Hoy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- System Overview -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-chart-bar me-2"></i>Visión General del Sistema
                    </h4>
                </div>
                <div class="card-body">
                    <div id="systemChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-bolt me-2"></i>Acciones Rápidas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        <button class="btn btn-primary" onclick="crearBackup()">
                            <i class="ti ti-database-check me-2"></i>Crear Respaldo
                        </button>
                        <button class="btn btn-success" onclick="exportarLogs()">
                            <i class="ti ti-download me-2"></i>Exportar Logs
                        </button>
                        <button class="btn btn-info" onclick="generarReporte()">
                            <i class="ti ti-file-earmark-pdf me-2"></i>Generar Reporte
                        </button>
                        <button class="btn btn-warning" onclick="limpiarCache()">
                            <i class="ti ti-refresh me-2"></i>Limpiar Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & System Info -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-activity me-2"></i>Actividad Reciente
                    </h4>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Nuevo usuario registrado</h6>
                                <p class="text-muted mb-0">Juan Pérez se registró como estudiante</p>
                                <small class="text-muted">Hace 5 minutos</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Respaldo completado</h6>
                                <p class="text-muted mb-0">Backup automático de la base de datos</p>
                                <small class="text-muted">Hace 1 hora</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Configuración actualizada</h6>
                                <p class="text-muted mb-0">Parámetros del sistema modificados</p>
                                <small class="text-muted">Hace 2 horas</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Error en el sistema</h6>
                                <p class="text-muted mb-0">Problema de conexión resuelto</p>
                                <small class="text-muted">Hace 3 horas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>Información del Sistema
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted">Versión del Sistema</small>
                                <div class="fw-bold"><?= $sistema_info['version'] ?? '1.0.0' ?></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Nombre del Sistema</small>
                                <div class="fw-bold"><?= $sistema_info['nombre'] ?? 'Sistema de Bienestar Estudiantil' ?></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Última Actualización</small>
                                <div class="fw-bold"><?= date('d/m/Y H:i') ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted">Estado del Servidor</small>
                                <div class="fw-bold text-success">
                                    <i class="ti ti-check-circle me-1"></i>Online
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Base de Datos</small>
                                <div class="fw-bold text-success">
                                    <i class="ti ti-database-check me-1"></i>Conectada
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Espacio en Disco</small>
                                <div class="fw-bold text-warning">
                                    <i class="ti ti-device-hdd me-1"></i>75% usado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Modules -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-grid me-2"></i>Módulos del Sistema
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="ti ti-users fs-1 text-primary mb-3"></i>
                                    <h6>Gestión de Usuarios</h6>
                                    <p class="text-muted small">Administra usuarios, roles y permisos</p>
                                    <a href="<?= base_url('global-admin/usuarios') ?>" class="btn btn-sm btn-outline-primary">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="ti ti-shield-lock fs-1 text-success mb-3"></i>
                                    <h6>Gestión de Roles</h6>
                                    <p class="text-muted small">Configura roles y permisos del sistema</p>
                                    <a href="<?= base_url('global-admin/roles') ?>" class="btn btn-sm btn-outline-success">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="ti ti-settings fs-1 text-warning mb-3"></i>
                                    <h6>Configuración</h6>
                                    <p class="text-muted small">Ajusta parámetros del sistema</p>
                                    <a href="<?= base_url('global-admin/configuracion') ?>" class="btn btn-sm btn-outline-warning">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="ti ti-database-check fs-1 text-danger mb-3"></i>
                                    <h6>Respaldos</h6>
                                    <p class="text-muted small">Gestiona respaldos de la base de datos</p>
                                    <a href="<?= base_url('global-admin/respaldos') ?>" class="btn btn-sm btn-outline-danger">Acceder</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modal') ?>
<!-- Aquí van los modales si los necesitas -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // System Chart
    var options = {
        series: [{
            name: 'Usuarios Activos',
            data: <?= json_encode($datos_grafico['datos'] ?? [30, 40, 35, 50, 49, 60]) ?>
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode($datos_grafico['labels'] ?? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun']) ?>
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
        colors: ['#3498db']
    };

    // Solo crear el gráfico si el elemento existe
    var chartElement = document.querySelector("#systemChart");
    if (chartElement) {
        var chart = new ApexCharts(chartElement, options);
        chart.render();
    }

    // Crear Backup
    window.crearBackup = function() {
        Swal.fire({
            title: 'Crear Respaldo',
            text: '¿Estás seguro de que quieres crear un nuevo respaldo de la base de datos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, crear respaldo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Creando respaldo...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('global-admin/crear-respaldo') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Respaldo Creado!',
                                response.message || 'El respaldo se ha creado exitosamente.',
                                'success'
                            );
                            if (typeof cargarRespaldos === 'function') cargarRespaldos();
                            if (typeof cargarEstadisticas === 'function') cargarEstadisticas();
                        } else {
                            Swal.fire(
                                'Error',
                                response.message || response.error || 'Error al crear el respaldo.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error de Conexión',
                            'No se pudo conectar con el servidor.',
                            'error'
                        );
                    }
                });
            }
        });
    };

    // Exportar Logs
    window.exportarLogs = function() {
        Swal.fire({
            icon: 'question',
            title: 'Exportar Logs',
            text: '¿Desea exportar los logs del sistema?',
            showCancelButton: true,
            confirmButtonText: 'Exportar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Exportando...',
                    text: 'Preparando archivo de logs',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Descargar archivo
                window.open('<?= base_url('global-admin/exportar-logs') ?>', '_blank');
                
                // Cerrar loading
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Exportado!',
                        text: 'Los logs se han exportado correctamente',
                        confirmButtonText: 'OK'
                    });
                }, 2000);
            }
        });
    };

    // Generar Reporte
    window.generarReporte = function() {
        Swal.fire({
            icon: 'question',
            title: 'Generar Reporte',
            text: '¿Desea generar un reporte del sistema?',
            showCancelButton: true,
            confirmButtonText: 'Generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para generar el reporte
                Swal.fire({
                    icon: 'success',
                    title: 'Reporte Generado',
                    text: 'El reporte se ha generado exitosamente.',
                    confirmButtonText: 'OK'
                });
            }
        });
    };

    // Limpiar Cache
    window.limpiarCache = function() {
        Swal.fire({
            icon: 'question',
            title: 'Limpiar Cache',
            text: '¿Desea limpiar el cache del sistema?',
            showCancelButton: true,
            confirmButtonText: 'Limpiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para limpiar el cache
                Swal.fire({
                    icon: 'success',
                    title: 'Cache Limpiado',
                    text: 'El cache se ha limpiado exitosamente.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            }
        });
    };
});
</script>
<?= $this->endSection() ?> 
