<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Estadísticas Globales</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Estadísticas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Fecha -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicio" value="2025-01-01">
                            </div>
                            <div class="col-md-3">
                                <label for="fechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFin" value="2025-01-31">
                            </div>
                            <div class="col-md-3">
                                <label for="periodoEstadisticas" class="form-label">Período</label>
                                <select class="form-select" id="periodoEstadisticas">
                                    <option value="mes">Este Mes</option>
                                    <option value="trimestre">Este Trimestre</option>
                                    <option value="año">Este Año</option>
                                    <option value="personalizado" selected>Personalizado</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary" onclick="cargarEstadisticas()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Usuarios</h6>
                                <h3 class="mb-0" id="totalUsuarios">0</h3>
                                <small id="cambioUsuarios">+0% este mes</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Usuarios Activos</h6>
                                <h3 class="mb-0" id="usuariosActivos">0</h3>
                                <small id="cambioActivos">+0% este mes</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-person-check fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Roles</h6>
                                <h3 class="mb-0" id="totalRoles">0</h3>
                                <small>Configurados</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-shield-check fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Respaldos Recientes</h6>
                                <h3 class="mb-0" id="respaldosRecientes">0</h3>
                                <small id="ultimoRespaldo">Último: Hoy</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-database fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Principales -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Actividad del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartActividad" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribución de Usuarios por Rol</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartRoles" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Secundarios -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Registros por Mes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartRegistros" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Actividad de Logs</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartLogs" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas de Datos -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top 5 Usuarios Más Activos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th>Último Acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyUsuariosActivos">
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resumen de Roles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Usuarios</th>
                                        <th>Estado</th>
                                        <th>Última Actividad</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyResumenRoles">
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores de Rendimiento -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Indicadores de Rendimiento (KPIs)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <h3 class="text-primary" id="kpiUsuarios">0%</h3>
                                    <p class="text-muted mb-0">Crecimiento de Usuarios</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <h3 class="text-success" id="kpiActividad">0%</h3>
                                    <p class="text-muted mb-0">Tasa de Actividad</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <h3 class="text-info" id="kpiSeguridad">0%</h3>
                                    <p class="text-muted mb-0">Índice de Seguridad</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <h3 class="text-warning" id="kpiRespaldos">0%</h3>
                                    <p class="text-muted mb-0">Cobertura de Respaldos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tendencias y Análisis -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tendencias de Registro</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTendencias" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Análisis de Seguridad</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Accesos Exitosos</span>
                                <span id="accesosExitosos">0%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" id="barAccesosExitosos" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Intentos Fallidos</span>
                                <span id="intentosFallidos">0%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" id="barIntentosFallidos" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Usuarios Bloqueados</span>
                                <span id="usuariosBloqueados">0%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" id="barUsuariosBloqueados" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Respaldos Automáticos</span>
                                <span id="respaldosAutomaticos">0%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" id="barRespaldosAutomaticos" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const charts = {};

$(document).ready(function() {
    cargarEstadisticas();
    inicializarGraficos();
});

function cargarEstadisticas() {
    const filtros = {
        fecha_inicio: $('#fechaInicio').val(),
        fecha_fin: $('#fechaFin').val(),
        periodo: $('#periodoEstadisticas').val()
    };
    
    $.ajax({
        url: '<?= base_url('global-admin/obtener-estadisticas-globales') ?>',
        type: 'GET',
        data: filtros,
        success: function(response) {
                if (response.success) {
                    renderEstadisticas(response.estadisticas);
                    actualizarGraficos(response.graficos);
                    actualizarTablas(response.tablas);
                    actualizarKPIs(response.kpis);
            } else {
            }
        },
        error: function() {
        }
    });
}

function renderEstadisticas(estadisticas) {
    $('#totalUsuarios').text(estadisticas.total_usuarios);
    $('#usuariosActivos').text(estadisticas.usuarios_activos);
    $('#totalRoles').text(estadisticas.total_roles);
    $('#respaldosRecientes').text(estadisticas.respaldos_recientes);
    
    $('#cambioUsuarios').text(estadisticas.cambio_usuarios);
    $('#cambioActivos').text(estadisticas.cambio_activos);
    $('#ultimoRespaldo').text(estadisticas.ultimo_respaldo || 'Último: Hoy');
}

function actualizarGraficos(graficos) {
    // Gráfico de Actividad
    if (charts.actividad) {
        charts.actividad.destroy();
    }
    
    const ctxActividad = document.getElementById('chartActividad').getContext('2d');
    charts.actividad = new Chart(ctxActividad, {
        type: 'line',
        data: {
            labels: graficos.actividad.labels,
            datasets: graficos.actividad.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de Roles
    if (charts.roles) {
        charts.roles.destroy();
    }
    
    const ctxRoles = document.getElementById('chartRoles').getContext('2d');
    charts.roles = new Chart(ctxRoles, {
        type: 'doughnut',
        data: {
            labels: graficos.roles.labels,
            datasets: [{
                data: graficos.roles.data,
                backgroundColor: graficos.roles.colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Gráfico de Registros
    if (charts.registros) {
        charts.registros.destroy();
    }
    
    const ctxRegistros = document.getElementById('chartRegistros').getContext('2d');
    charts.registros = new Chart(ctxRegistros, {
        type: 'bar',
        data: {
            labels: graficos.registros.labels,
            datasets: [{
                label: 'Nuevos Usuarios',
                data: graficos.registros.data,
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de Logs
    if (charts.logs) {
        charts.logs.destroy();
    }
    
    const ctxLogs = document.getElementById('chartLogs').getContext('2d');
    charts.logs = new Chart(ctxLogs, {
        type: 'line',
        data: {
            labels: graficos.logs.labels,
            datasets: graficos.logs.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de Tendencias
    if (charts.tendencias) {
        charts.tendencias.destroy();
    }
    
    const ctxTendencias = document.getElementById('chartTendencias').getContext('2d');
    charts.tendencias = new Chart(ctxTendencias, {
        type: 'line',
        data: {
            labels: graficos.tendencias.labels,
            datasets: graficos.tendencias.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function actualizarTablas(tablas) {
    // Tabla de usuarios activos
    const tbodyUsuarios = $('#tbodyUsuariosActivos');
    tbodyUsuarios.empty();
    
    tablas.usuarios_activos.forEach(usuario => {
        const row = `
            <tr>
                <td>${usuario.nombre}</td>
                <td><span class="badge bg-primary">${usuario.rol}</span></td>
                <td>${formatearFecha(usuario.ultimo_acceso)}</td>
                <td>${usuario.acciones}</td>
            </tr>
        `;
        tbodyUsuarios.append(row);
    });
    
    // Tabla de resumen de roles
    const tbodyRoles = $('#tbodyResumenRoles');
    tbodyRoles.empty();
    
    tablas.resumen_roles.forEach(rol => {
        const row = `
            <tr>
                <td>${rol.nombre}</td>
                <td>${rol.usuarios}</td>
                <td><span class="badge bg-success">${rol.estado}</span></td>
                <td>${formatearFecha(rol.ultima_actividad)}</td>
            </tr>
        `;
        tbodyRoles.append(row);
    });
}

function actualizarKPIs(kpis) {
    $('#kpiUsuarios').text(kpis.crecimiento_usuarios + '%');
    $('#kpiActividad').text(kpis.tasa_actividad + '%');
    $('#kpiSeguridad').text(kpis.indice_seguridad + '%');
    $('#kpiRespaldos').text(kpis.cobertura_respaldos + '%');
    
    // Actualizar barras de progreso
    $('#barAccesosExitosos').css('width', kpis.accesos_exitosos + '%');
    $('#barIntentosFallidos').css('width', kpis.intentos_fallidos + '%');
    $('#barUsuariosBloqueados').css('width', kpis.usuarios_bloqueados + '%');
    $('#barRespaldosAutomaticos').css('width', kpis.respaldos_automaticos + '%');
    
    $('#accesosExitosos').text(kpis.accesos_exitosos + '%');
    $('#intentosFallidos').text(kpis.intentos_fallidos + '%');
    $('#usuariosBloqueados').text(kpis.usuarios_bloqueados + '%');
    $('#respaldosAutomaticos').text(kpis.respaldos_automaticos + '%');
}

function inicializarGraficos() {
    // Inicializar con datos vacíos
    const datosVacios = {
        actividad: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Usuarios Activos',
                data: [0, 0, 0, 0, 0, 0],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        },
        roles: {
            labels: ['Estudiantes', 'AdminBienestar', 'SuperAdmin'],
            data: [0, 0, 0],
            colors: ['#007bff', '#28a745', '#dc3545']
        },
        registros: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            data: [0, 0, 0, 0, 0, 0]
        },
        logs: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
            datasets: [{
                label: 'Errores',
                data: [0, 0, 0, 0, 0, 0],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }]
        },
        tendencias: {
            labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            datasets: [{
                label: 'Nuevos Usuarios',
                data: [0, 0, 0, 0],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        }
    };
    
    actualizarGraficos(datosVacios);
}

function formatearFecha(fecha) {
    if (!fecha || fecha === '0000-00-00 00:00:00') return 'N/A';
    const date = new Date(fecha);
    if (isNaN(date.getTime()) || date.getFullYear() < 2000) {
        return 'N/A';
    }
    return date.toLocaleString('es-EC');
}

// Cambiar período automáticamente
$('#periodoEstadisticas').on('change', function() {
    const periodo = $(this).val();
    const hoy = new Date();
    
    switch (periodo) {
        case 'mes':
            $('#fechaInicio').val(hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0') + '-01');
            $('#fechaFin').val(hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0') + '-' + String(hoy.getDate()).padStart(2, '0'));
            break;
        case 'trimestre':
            const trimestre = Math.floor(hoy.getMonth() / 3);
            const inicioTrimestre = new Date(hoy.getFullYear(), trimestre * 3, 1);
            $('#fechaInicio').val(inicioTrimestre.toISOString().split('T')[0]);
            $('#fechaFin').val(hoy.toISOString().split('T')[0]);
            break;
        case 'año':
            $('#fechaInicio').val(hoy.getFullYear() + '-01-01');
            $('#fechaFin').val(hoy.getFullYear() + '-12-31');
            break;
    }
    
    if (periodo !== 'personalizado') {
        cargarEstadisticas();
    }
});
</script>
<?= $this->endSection() ?> 
