<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Reportes y Analítica</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" onclick="generarReporte()">
            <i class="bi bi-file-earmark-pdf"></i> Generar Reporte
        </button>
        <button type="button" class="btn btn-info" onclick="exportarDatos()">
            <i class="bi bi-download"></i> Exportar Datos
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filtrosModal">
            <i class="bi bi-funnel"></i> Filtros
        </button>
    </div>
</div>

<!-- Filtros de Fecha -->
<div class="row mb-4">
    <div class="col-md-12">
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
                        <label for="periodoAcademico" class="form-label">Periodo Académico</label>
                        <select class="form-select" id="periodoAcademico">
                            <option value="2024-2">2024-2</option>
                            <option value="2024-1">2024-1</option>
                            <option value="2023-2">2023-2</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" onclick="actualizarReportes()">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
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
                        <h6 class="card-title">Total Estudiantes</h6>
                        <h3 class="mb-0">1,247</h3>
                        <small>+12% vs mes anterior</small>
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
                        <h6 class="card-title">Fichas Completadas</h6>
                        <h3 class="mb-0">892</h3>
                        <small>71.5% de cobertura</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-file-earmark-check fs-1"></i>
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
                        <h6 class="card-title">Becas Aprobadas</h6>
                        <h3 class="mb-0">156</h3>
                        <small>$78,500 total</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-award fs-1"></i>
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
                        <h6 class="card-title">Tickets Resueltos</h6>
                        <h3 class="mb-0">234</h3>
                        <small>94.3% satisfacción</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-ticket-detailed fs-1"></i>
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
                <h5 class="card-title mb-0">Actividad Mensual</h5>
            </div>
            <div class="card-body">
                <canvas id="chartActividad" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Distribución por Carrera</h5>
            </div>
            <div class="card-body">
                <canvas id="chartCarreras" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Secundarios -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estado de Fichas Socioeconómicas</h5>
            </div>
            <div class="card-body">
                <canvas id="chartFichas" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tipos de Becas Solicitadas</h5>
            </div>
            <div class="card-body">
                <canvas id="chartBecas" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de Datos -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 5 Carreras - Participación</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Carrera</th>
                                <th>Estudiantes</th>
                                <th>Fichas</th>
                                <th>% Cobertura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ingeniería Informática</td>
                                <td>245</td>
                                <td>198</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 80.8%"></div>
                                    </div>
                                    <small>80.8%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>Ingeniería Industrial</td>
                                <td>198</td>
                                <td>156</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 78.8%"></div>
                                    </div>
                                    <small>78.8%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>Administración</td>
                                <td>167</td>
                                <td>134</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: 80.2%"></div>
                                    </div>
                                    <small>80.2%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>Contabilidad</td>
                                <td>145</td>
                                <td>112</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: 77.2%"></div>
                                    </div>
                                    <small>77.2%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>Marketing</td>
                                <td>123</td>
                                <td>98</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" style="width: 79.7%"></div>
                                    </div>
                                    <small>79.7%</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Resumen de Becas por Tipo</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tipo de Beca</th>
                                <th>Solicitudes</th>
                                <th>Aprobadas</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Excelencia Académica</td>
                                <td>45</td>
                                <td>38</td>
                                <td>$19,000</td>
                            </tr>
                            <tr>
                                <td>Socioeconómica</td>
                                <td>89</td>
                                <td>67</td>
                                <td>$33,500</td>
                            </tr>
                            <tr>
                                <td>Deportiva</td>
                                <td>23</td>
                                <td>18</td>
                                <td>$9,000</td>
                            </tr>
                            <tr>
                                <td>Cultural</td>
                                <td>15</td>
                                <td>12</td>
                                <td>$6,000</td>
                            </tr>
                            <tr>
                                <td>Otros</td>
                                <td>34</td>
                                <td>21</td>
                                <td>$11,000</td>
                            </tr>
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
                            <h3 class="text-primary">71.5%</h3>
                            <p class="text-muted mb-0">Cobertura de Fichas</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <h3 class="text-success">84.3%</h3>
                            <p class="text-muted mb-0">Tasa de Aprobación Becas</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <h3 class="text-info">94.3%</h3>
                            <p class="text-muted mb-0">Satisfacción Tickets</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <h3 class="text-warning">2.3 días</h3>
                            <p class="text-muted mb-0">Tiempo Promedio Respuesta</p>
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
                <h5 class="card-title mb-0">Tendencias Mensuales</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTendencias" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Análisis de Satisfacción</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Muy Satisfecho</span>
                        <span>65%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 65%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Satisfecho</span>
                        <span>25%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: 25%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Neutral</span>
                        <span>7%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 7%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Insatisfecho</span>
                        <span>3%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: 3%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modal') ?>
<!-- Modal Filtros Avanzados -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros Avanzados para Reportes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Rango de Fechas</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="fechaDesdeModal">
                            <span class="input-group-text">hasta</span>
                            <input type="date" class="form-control" id="fechaHastaModal">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Carrera</label>
                        <select class="form-select" id="filterCarreraModal">
                            <option value="">Todas las carreras</option>
                            <option value="Ingeniería Informática">Ingeniería Informática</option>
                            <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                            <option value="Administración">Administración</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Beca</label>
                        <select class="form-select" id="filterBecaModal">
                            <option value="">Todos los tipos</option>
                            <option value="Excelencia">Excelencia Académica</option>
                            <option value="Socioeconomica">Socioeconómica</option>
                            <option value="Deportiva">Deportiva</option>
                            <option value="Cultural">Cultural</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Estado de Fichas</label>
                        <select class="form-select" id="filterEstadoFichasModal">
                            <option value="">Todos los estados</option>
                            <option value="Borrador">Borrador</option>
                            <option value="Enviada">Enviada</option>
                            <option value="Revisada">Revisada</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semestre</label>
                        <select class="form-select" id="filterSemestreModal">
                            <option value="">Todos los semestres</option>
                            <option value="1">1er Semestre</option>
                            <option value="2">2do Semestre</option>
                            <option value="3">3er Semestre</option>
                            <option value="4">4to Semestre</option>
                            <option value="5">5to Semestre</option>
                            <option value="6">6to Semestre</option>
                            <option value="7">7mo Semestre</option>
                            <option value="8">8vo Semestre</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rango de Ingresos</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="ingresoMinModal" placeholder="Mínimo">
                            <span class="input-group-text">-</span>
                            <input type="number" class="form-control" id="ingresoMaxModal" placeholder="Máximo">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltrosAvanzados()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<script>
// Funciones JavaScript para la funcionalidad
function actualizarReportes() {
    const fechaInicio = $('#fechaInicio').val();
    const fechaFin = $('#fechaFin').val();
    const periodo = $('#periodoAcademico').val();
    
    // Lógica para actualizar reportes con los filtros
    // Simular actualización
    alert('Reportes actualizados exitosamente');
}

function generarReporte() {
    if (confirm('¿Desea generar un reporte PDF con los datos actuales?')) {
        // Lógica para generar reporte PDF
        alert('Reporte PDF generado exitosamente');
    }
}

function exportarDatos() {
    if (confirm('¿Desea exportar los datos a Excel?')) {
        // Lógica para exportar datos
        alert('Datos exportados exitosamente');
    }
}

function aplicarFiltrosAvanzados() {
    // Lógica para aplicar filtros avanzados
    $('#filtrosModal').modal('hide');
    alert('Filtros aplicados exitosamente');
}

// Initialize charts when document is ready
$(document).ready(function() {
    // Gráfico de Actividad Mensual
    const ctxActividad = document.getElementById('chartActividad').getContext('2d');
    new Chart(ctxActividad, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Fichas Completadas',
                data: [65, 78, 92, 85, 98, 112, 89, 95, 103, 87, 94, 108],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }, {
                label: 'Becas Aprobadas',
                data: [12, 15, 18, 14, 22, 25, 19, 21, 24, 17, 20, 23],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'Tickets Resueltos',
                data: [45, 52, 48, 61, 58, 67, 54, 62, 59, 51, 56, 64],
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
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
    
    // Gráfico de Distribución por Carrera
    const ctxCarreras = document.getElementById('chartCarreras').getContext('2d');
    new Chart(ctxCarreras, {
        type: 'doughnut',
        data: {
            labels: ['Ing. Informática', 'Ing. Industrial', 'Administración', 'Contabilidad', 'Marketing', 'Otros'],
            datasets: [{
                data: [245, 198, 167, 145, 123, 369],
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Gráfico de Estado de Fichas
    const ctxFichas = document.getElementById('chartFichas').getContext('2d');
    new Chart(ctxFichas, {
        type: 'bar',
        data: {
            labels: ['Completadas', 'Pendientes', 'En Revisión', 'Rechazadas'],
            datasets: [{
                label: 'Cantidad',
                data: [892, 355, 45, 23],
                backgroundColor: [
                    '#28a745', '#ffc107', '#17a2b8', '#dc3545'
                ]
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
    
    // Gráfico de Tipos de Becas
    const ctxBecas = document.getElementById('chartBecas').getContext('2d');
    new Chart(ctxBecas, {
        type: 'pie',
        data: {
            labels: ['Excelencia', 'Socioeconómica', 'Deportiva', 'Cultural', 'Otros'],
            datasets: [{
                data: [45, 89, 23, 15, 34],
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#6f42c1', '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Gráfico de Tendencias
    const ctxTendencias = document.getElementById('chartTendencias').getContext('2d');
    new Chart(ctxTendencias, {
        type: 'line',
        data: {
            labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            datasets: [{
                label: 'Nuevas Fichas',
                data: [25, 32, 28, 35],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }, {
                label: 'Becas Aprobadas',
                data: [8, 12, 10, 15],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
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
});
</script> 