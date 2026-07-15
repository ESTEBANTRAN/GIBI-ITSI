<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Dashboard Administrativo</h4>
                <p class="text-muted mb-0">Panel de control y estadísticas del sistema de bienestar estudiantil</p>
            </div>
            <div class="page-title-right">
                <button type="button" class="btn btn-primary" onclick="actualizarDashboard()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Fichas</p>
                        <h4 class="mb-0" id="total-fichas"><?= $estadisticas['fichas']['total'] ?? 0 ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                            <span class="avatar-title">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Becas</p>
                        <h4 class="mb-0" id="total-becas"><?= $estadisticas['becas']['total_becas'] ?? 0 ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                            <span class="avatar-title">
                                <i class="bi bi-award"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Solicitudes</p>
                        <h4 class="mb-0" id="total-solicitudes"><?= $estadisticas['solicitudes']['total'] ?? 0 ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning align-self-center">
                            <span class="avatar-title">
                                <i class="bi bi-envelope"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Usuarios</p>
                        <h4 class="mb-0" id="total-usuarios"><?= $estadisticas['usuarios']['total'] ?? 0 ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info align-self-center">
                            <span class="avatar-title">
                                <i class="bi bi-people"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos de Estadísticas -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Estado de Fichas</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartFichas', 'Estado_Fichas')">
                            <i class="bi bi-download me-2"></i>Exportar PNG
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartFichas', 'Estado_Fichas', 'pdf')">
                            <i class="bi bi-file-pdf me-2"></i>Exportar PDF
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartFichas"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Solicitudes por Estado</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartSolicitudes', 'Solicitudes_Estado')">
                            <i class="bi bi-download me-2"></i>Exportar PNG
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartSolicitudes', 'Solicitudes_Estado', 'pdf')">
                            <i class="bi bi-file-pdf me-2"></i>Exportar PDF
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartSolicitudes"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Adicionales -->
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Tendencias por Período</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartTendencias', 'Tendencias_Periodo')">
                            <i class="bi bi-download me-2"></i>Exportar PNG
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartTendencias', 'Tendencias_Periodo', 'pdf')">
                            <i class="bi bi-file-pdf me-2"></i>Exportar PDF
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartTendencias"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Distribución por Carrera</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartCarreras', 'Distribucion_Carreras')">
                            <i class="bi bi-download me-2"></i>Exportar PNG
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarGrafico('chartCarreras', 'Distribucion_Carreras', 'pdf')">
                            <i class="bi bi-file-pdf me-2"></i>Exportar PDF
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartCarreras"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Acción</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="actividad-reciente">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>Alertas y Notificaciones
                </h5>
            </div>
            <div class="card-body">
                <div id="alertas-notificaciones">
                    <!-- Se llena dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('admin-bienestar/fichas-socioeconomicas') ?>" class="btn btn-outline-primary w-100">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Gestionar Fichas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('admin-bienestar/becas') ?>" class="btn btn-outline-success w-100">
                            <i class="bi bi-award me-2"></i>
                            Gestionar Becas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('admin-bienestar/solicitudes-ayuda') ?>" class="btn btn-outline-warning w-100">
                            <i class="bi bi-envelope me-2"></i>
                            Ver Solicitudes
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('admin-bienestar/reportes') ?>" class="btn btn-outline-info w-100">
                            <i class="bi bi-graph-up me-2"></i>
                            Generar Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
// Variables globales para los gráficos
let chartFichas, chartSolicitudes, chartTendencias, chartCarreras;

// Colores para los gráficos
const colors = {
    primary: '#556ee6',
    success: '#34c38f',
    warning: '#f1b44c',
    danger: '#f46a6a',
    info: '#50a5f1',
    secondary: '#74788d'
};

// Inicializar gráficos cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficos();
    cargarActividadReciente();
    cargarAlertasNotificaciones();
});

// Función para inicializar todos los gráficos
function inicializarGraficos() {
    inicializarGraficoFichas();
    inicializarGraficoSolicitudes();
    inicializarGraficoTendencias();
    inicializarGraficoCarreras();
}

// Gráfico de estado de fichas
function inicializarGraficoFichas() {
    const ctx = document.getElementById('chartFichas').getContext('2d');
    
    const data = {
        labels: ['<?= implode("', '", array_column($estadisticas['fichas']['estados'] ?? [], 'estado')) ?>'],
        datasets: [{
            data: [<?= implode(', ', array_column($estadisticas['fichas']['estados'] ?? [], 'total')) ?>],
            backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    chartFichas = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Gráfico de solicitudes por estado
function inicializarGraficoSolicitudes() {
    const ctx = document.getElementById('chartSolicitudes').getContext('2d');
    
    const data = {
        labels: ['<?= implode("', '", array_column($estadisticas['solicitudes']['por_estado'] ?? [], 'estado')) ?>'],
        datasets: [{
            label: 'Solicitudes',
            data: [<?= implode(', ', array_column($estadisticas['solicitudes']['por_estado'] ?? [], 'total')) ?>],
            backgroundColor: colors.primary,
            borderColor: colors.primary,
            borderWidth: 2,
            borderRadius: 5
        }]
    };

    chartSolicitudes = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Gráfico de tendencias por período
function inicializarGraficoTendencias() {
    const ctx = document.getElementById('chartTendencias').getContext('2d');
    
    const data = {
        labels: ['<?= implode("', '", array_column($estadisticas['fichas']['periodos'] ?? [], 'periodo')) ?>'],
        datasets: [{
            label: 'Fichas',
            data: [<?= implode(', ', array_column($estadisticas['fichas']['periodos'] ?? [], 'total')) ?>],
            backgroundColor: colors.success,
            borderColor: colors.success,
            borderWidth: 2,
            fill: false,
            tension: 0.4
        }]
    };

    chartTendencias = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Gráfico de distribución por carrera
function inicializarGraficoCarreras() {
    const ctx = document.getElementById('chartCarreras').getContext('2d');
    
    const data = {
        labels: ['<?= implode("', '", array_column($estadisticas['fichas']['carreras'] ?? [], 'carrera')) ?>'],
        datasets: [{
            data: [<?= implode(', ', array_column($estadisticas['fichas']['carreras'] ?? [], 'total')) ?>],
            backgroundColor: [
                colors.primary, colors.success, colors.warning, 
                colors.danger, colors.info, colors.secondary
            ],
            borderWidth: 1,
            borderColor: '#fff'
        }]
    };

    chartCarreras = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });
}

// Función para actualizar el dashboard
function actualizarDashboard() {
    // Mostrar indicador de carga
    const btnActualizar = document.querySelector('button[onclick="actualizarDashboard()"]');
    const iconoOriginal = btnActualizar.innerHTML;
    btnActualizar.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Actualizando...';
    btnActualizar.disabled = true;

    // Hacer petición AJAX para obtener datos actualizados
    fetch('<?= base_url('admin-bienestar/getEstadisticas') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar estadísticas en las tarjetas
                document.getElementById('total-fichas').textContent = data.data.fichas.total || 0;
                document.getElementById('total-becas').textContent = data.data.becas.total_becas || 0;
                document.getElementById('total-solicitudes').textContent = data.data.solicitudes.total || 0;
                document.getElementById('total-usuarios').textContent = data.data.usuarios.total || 0;

                // Actualizar gráficos
                actualizarGraficos(data.data);
                
                // Actualizar tablas dinámicas
                cargarActividadReciente(data.data.actividad_reciente);
                cargarAlertasNotificaciones(data.data.alertas);
                
                // Mostrar mensaje de éxito
                mostrarNotificacion('Dashboard actualizado correctamente', 'success');
            } else {
                mostrarNotificacion('Error actualizando dashboard: ' + data.error, 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion('Error de conexión', 'error');
        })
        .finally(() => {
            // Restaurar botón
            btnActualizar.innerHTML = iconoOriginal;
            btnActualizar.disabled = false;
        });
}

// Función para actualizar los gráficos con nuevos datos
function actualizarGraficos(estadisticas) {
    // Actualizar gráfico de fichas
    if (chartFichas && estadisticas.fichas.estados) {
        chartFichas.data.labels = estadisticas.fichas.estados.map(e => e.estado);
        chartFichas.data.datasets[0].data = estadisticas.fichas.estados.map(e => e.total);
        chartFichas.update();
    }

    // Actualizar gráfico de solicitudes
    if (chartSolicitudes && estadisticas.solicitudes.por_estado) {
        chartSolicitudes.data.labels = estadisticas.solicitudes.por_estado.map(s => s.estado);
        chartSolicitudes.data.datasets[0].data = estadisticas.solicitudes.por_estado.map(s => s.total);
        chartSolicitudes.update();
    }

    // Actualizar gráfico de tendencias
    if (chartTendencias && estadisticas.fichas.periodos) {
        chartTendencias.data.labels = estadisticas.fichas.periodos.map(p => p.periodo);
        chartTendencias.data.datasets[0].data = estadisticas.fichas.periodos.map(p => p.total);
        chartTendencias.update();
    }

    // Actualizar gráfico de carreras
    if (chartCarreras && estadisticas.fichas.carreras) {
        chartCarreras.data.labels = estadisticas.fichas.carreras.map(c => c.carrera);
        chartCarreras.data.datasets[0].data = estadisticas.fichas.carreras.map(c => c.total);
        chartCarreras.update();
    }
}

// Función para cargar actividad reciente
function cargarActividadReciente(actividades = null) {
    const tbody = document.getElementById('actividad-reciente');
    
    // Si no se proporcionan actividades, usar las iniciales inyectadas por PHP
    if (!actividades) {
        actividades = <?= json_encode($estadisticas['actividad_reciente'] ?? []) ?>;
    }

    if (actividades.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay actividad reciente.</td></tr>';
        return;
    }

    tbody.innerHTML = actividades.map(act => `
        <tr>
            <td>${act.accion}</td>
            <td>${act.usuario}</td>
            <td>${act.fecha}</td>
            <td><span class="badge bg-${act.estado}">${act.estado}</span></td>
        </tr>
    `).join('');
}

// Función para cargar alertas y notificaciones
function cargarAlertasNotificaciones(alertas = null) {
    const contenedor = document.getElementById('alertas-notificaciones');
    
    // Si no se proporcionan alertas, usar las iniciales inyectadas por PHP
    if (!alertas) {
        alertas = <?= json_encode($estadisticas['alertas'] ?? []) ?>;
    }

    if (alertas.length === 0) {
        contenedor.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Todo al día.</div>';
        return;
    }

    contenedor.innerHTML = alertas.map(alerta => `
        <div class="alert alert-${alerta.tipo} alert-dismissible fade show" role="alert">
            <i class="bi ${alerta.icono} me-2"></i>
            ${alerta.mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `).join('');
}

// Función para exportar gráficos
function exportarGrafico(canvasId, nombre, formato = 'png') {
    const canvas = document.getElementById(canvasId);
    const link = document.createElement('a');
    
    if (formato === 'png') {
        link.download = `${nombre}.png`;
        link.href = canvas.toDataURL('image/png');
    } else if (formato === 'pdf') {
        // Implementar exportación a PDF
        mostrarNotificacion('Exportación a PDF en desarrollo', 'info');
        return;
    }
    
    link.click();
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.remove();
        }
    }, 5000);
}

// Actualizar dashboard cada 5 minutos
setInterval(actualizarDashboard, 300000);

// Agregar estilos CSS para la animación de spin
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
`;
document.head.appendChild(style);

</script>

<?= $this->endSection() ?>

