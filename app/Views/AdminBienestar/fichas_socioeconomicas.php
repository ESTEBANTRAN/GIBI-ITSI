<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Fichas Socioeconómicas</h4>
                <p class="text-muted mb-0">Revisa y gestiona las fichas socioeconómicas de los estudiantes</p>
            </div>
        </div>
    </div>
</div>



<!-- Filtros de Búsqueda -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= current_url() ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="Enviada" <?= (isset($filtros['estado']) && $filtros['estado'] === 'Enviada') ? 'selected' : '' ?>>Enviada</option>
                        <option value="Revisada" <?= (isset($filtros['estado']) && $filtros['estado'] === 'Revisada') ? 'selected' : '' ?>>Revisada</option>
                        <option value="Aprobada" <?= (isset($filtros['estado']) && $filtros['estado'] === 'Aprobada') ? 'selected' : '' ?>>Aprobada</option>
                        <option value="Rechazada" <?= (isset($filtros['estado']) && $filtros['estado'] === 'Rechazada') ? 'selected' : '' ?>>Rechazada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="periodo_id" class="form-label">Período</label>
                    <select class="form-select" id="periodo_id" name="periodo_id">
                        <option value="">Todos los períodos</option>
                        <?php if (isset($periodos) && is_array($periodos)): ?>
                            <?php foreach ($periodos as $periodo): ?>
                            <option value="<?= $periodo['id'] ?? '' ?>" <?= (isset($filtros['periodo_id']) && $filtros['periodo_id'] == ($periodo['id'] ?? '')) ? 'selected' : '' ?>>
                                <?= esc($periodo['nombre'] ?? '') ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="carrera_id" class="form-label">Carrera</label>
                    <select class="form-select" id="carrera_id" name="carrera_id">
                        <option value="">Todas las carreras</option>
                        <?php if (isset($carreras) && is_array($carreras)): ?>
                            <?php foreach ($carreras as $carrera): ?>
                            <option value="<?= $carrera['id'] ?? '' ?>" <?= (isset($filtros['carrera_id']) && $filtros['carrera_id'] == ($carrera['id'] ?? '')) ? 'selected' : '' ?>>
                                <?= esc($carrera['nombre'] ?? '') ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="busqueda" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" 
                           placeholder="Nombre, apellido, cédula o email" 
                           value="<?= esc($filtros['busqueda'] ?? '') ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Aplicar Filtros
                    </button>
                    <a href="<?= current_url() ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sección ESTUDIANTES BECADOS -->
<div class="section-header mb-3">
    <div class="d-flex align-items-center">
        <h4><i class="bi bi-award me-2"></i>ESTUDIANTES BECADOS</h4>
        <div class="section-stats ms-3">
            <span class="stat-item">Total: <?= isset($estadisticasBecados['total']) ? $estadisticasBecados['total'] : count($fichas ?? []) ?></span>
            <span class="stat-item">Enviadas: <?= isset($estadisticasBecados['enviadas']) ? $estadisticasBecados['enviadas'] : 0 ?></span>
            <span class="stat-item">Aprobadas: <?= isset($estadisticasBecados['aprobadas']) ? $estadisticasBecados['aprobadas'] : 0 ?></span>
            <span class="stat-item">Rechazadas: <?= isset($estadisticasBecados['rechazadas']) ? $estadisticasBecados['rechazadas'] : 0 ?></span>
        </div>
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-header">
        <h5>Fichas de Estudiantes que Solicitan Becas</h5>
        <small class="text-muted">Estas fichas pueden ser aprobadas o rechazadas</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Período</th>
                    <th>Estado</th>
                    <th>Estado Beca</th>
                    <th>Fecha Envío</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fichas)): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No se encontraron fichas de estudiantes becados con los filtros aplicados</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($fichas as $ficha): ?>
                <tr>
                    <td>
                        <div>
                            <strong><?= esc($ficha['estudiante_nombre'] ?? ($ficha['nombre'] . ' ' . ($ficha['apellido'] ?? ''))) ?></strong>
                            <br>
                            <small class="text-muted"><?= esc($ficha['cedula'] ?? '') ?></small>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($ficha['carrera_nombre']) && $ficha['carrera_nombre'] !== 'Sin carrera'): ?>
                            <span class="badge bg-info"><?= esc($ficha['carrera_nombre']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">No especificada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($ficha['periodo_nombre'] ?? '') ?></td>
                    <td>
                        <?php
                        $estadoClass = 'bg-secondary';
                        $estadoText = $ficha['estado'] ?? 'Sin estado';
                        switch($ficha['estado'] ?? '') {
                            case 'Enviada': $estadoClass = 'bg-info'; break;
                            case 'Revisada': $estadoClass = 'bg-warning'; break;
                            case 'Aprobada': $estadoClass = 'bg-success'; break;
                            case 'Rechazada': $estadoClass = 'bg-danger'; break;
                        }
                        ?>
                        <span class="badge <?= $estadoClass ?>"><?= esc($estadoText) ?></span>
                    </td>
                    <td>
                        <span class="badge bg-primary">Solicitante</span>
                    </td>
                    <td>
                        <?php if (!empty($ficha['fecha_envio'])): ?>
                            <small><?= date('d/m/Y', strtotime($ficha['fecha_envio'])) ?></small>
                        <?php else: ?>
                            <span class="text-muted">No enviada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="verFicha(<?= $ficha['id'] ?? 0 ?>)" title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                    onclick="evaluacionSocioeconomica(<?= $ficha['id'] ?? 0 ?>)" title="Evaluación Socioeconómica">
                                <i class="bi bi-calculator"></i>
                            </button>
                            <?php if (($ficha['estado'] ?? '') === 'Enviada' || ($ficha['estado'] ?? '') === 'Revisada'): ?>
                            <button type="button" class="btn btn-sm btn-outline-success" 
                                    onclick="aprobarFicha(<?= $ficha['id'] ?? 0 ?>)" title="Aprobar">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="rechazarFicha(<?= $ficha['id'] ?? 0 ?>)" title="Rechazar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Sección ESTUDIANTES -->
<div class="section-header mb-3">
    <div class="d-flex align-items-center">
        <h4><i class="bi bi-people me-2"></i>ESTUDIANTES</h4>
        <div class="section-stats ms-3">
            <span class="stat-item">Total: <?= count($fichas ?? []) ?></span>
            <span class="stat-item">Enviadas: <?= isset($estadisticasBecados['enviadas']) ? $estadisticasBecados['enviadas'] : 0 ?></span>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5>Fichas de Estudiantes (Solo Información)</h5>
                <small class="text-muted">Estas fichas son solo para obtener su evaluación socioeconómica</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-lg" 
                        onclick="evaluacionAutomaticaMasiva()" 
                        title="Evaluación Socioeconómica Automática para Todos los Estudiantes">
                    <i class="bi bi-robot me-2"></i>
                    <strong>Evaluación Automática Masiva</strong>
                    <br><small class="text-white-50">Procesar todos los estudiantes sin beca</small>
                </button>
                
                <button type="button" class="btn btn-info btn-lg" 
                        onclick="verificarCodigo()" 
                        title="Verificar Código de Documento PDF">
                    <i class="bi bi-search me-2"></i>
                    <strong>Verificar Código</strong>
                    <br><small class="text-white-50">Validar autenticidad de documentos</small>
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Período</th>
                    <th>Estado</th>
                    <th>Fecha Envío</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fichas)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No se encontraron fichas de estudiantes con los filtros aplicados</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($fichas as $ficha): ?>
                <tr>
                    <td>
                        <div>
                            <strong><?= esc($ficha['estudiante_nombre'] ?? ($ficha['nombre'] . ' ' . ($ficha['apellido'] ?? ''))) ?></strong>
                            <br>
                            <small class="text-muted"><?= esc($ficha['cedula'] ?? '') ?></small>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($ficha['carrera_nombre']) && $ficha['carrera_nombre'] !== 'Sin carrera'): ?>
                            <span class="badge bg-info"><?= esc($ficha['carrera_nombre']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">No especificada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($ficha['periodo_nombre'] ?? '') ?></td>
                    <td>
                        <?php
                        $estadoClass = 'bg-secondary';
                        $estadoText = $ficha['estado'] ?? 'Sin estado';
                        switch($ficha['estado'] ?? '') {
                            case 'Enviada': $estadoClass = 'bg-info'; break;
                            case 'Revisada': $estadoClass = 'bg-warning'; break;
                            case 'Aprobada': $estadoClass = 'bg-success'; break;
                            case 'Rechazada': $estadoClass = 'bg-danger'; break;
                        }
                        ?>
                        <span class="badge <?= $estadoClass ?>"><?= esc($estadoText) ?></span>
                    </td>
                    <td>
                        <?php if (!empty($ficha['fecha_envio'])): ?>
                            <small><?= date('d/m/Y', strtotime($ficha['fecha_envio'])) ?></small>
                        <?php else: ?>
                            <span class="text-muted">No enviada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="verFicha(<?= $ficha['id'] ?? 0 ?>)" title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                    onclick="evaluacionSocioeconomica(<?= $ficha['id'] ?? 0 ?>)" title="Evaluación Socioeconómica">
                                <i class="bi bi-calculator"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    onclick="descargarFicha(<?= $ficha['id'] ?? 0 ?>)" title="Descargar PDF">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Gráficos de Estadísticas -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Estadísticas Generales
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('chartGeneral', 'Estadisticas_Generales')">
                    <i class="bi bi-download me-1"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="chartGeneral" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Distribución por Estado
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('chartEstados', 'Distribucion_Estados')">
                    <i class="bi bi-download me-1"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="chartEstados" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Estudiantes Becados vs No Becados
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('chartBecados', 'Estudiantes_Becados')">
                    <i class="bi bi-download me-1"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="chartBecados" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Tendencias por Período
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('chartTendencias', 'Tendencias_Periodo')">
                    <i class="bi bi-download me-1"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="chartTendencias" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para secciones */
.section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 0;
}

.section-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-item {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Estilos para contenedores de tabla */
.table-container {
    background: white;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.table-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    display: block;
}

/* Estilos para gráficos */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

/* Responsive */
@media (max-width: 768px) {
    .section-stats {
        justify-content: center;
    }
    
    .stat-item {
        font-size: 0.8rem;
        padding: 4px 10px;
    }
}
</style>

<script>
// Datos para gráficos
const estadisticas = {
    total: <?= count($fichas ?? []) ?>,
    enviadas: <?= isset($estadisticasBecados['enviadas']) ? $estadisticasBecados['enviadas'] : 0 ?>,
    revisadas: <?= isset($estadisticasBecados['revisadas']) ? $estadisticasBecados['revisadas'] : 0 ?>,
    aprobadas: <?= isset($estadisticasBecados['aprobadas']) ? $estadisticasBecados['aprobadas'] : 0 ?>,
    rechazadas: <?= isset($estadisticasBecados['rechazadas']) ? $estadisticasBecados['rechazadas'] : 0 ?>
};

// Inicializar gráficos cuando cargue la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficos();
});

function inicializarGraficos() {
    // Gráfico General
    const ctxGeneral = document.getElementById('chartGeneral').getContext('2d');
    new Chart(ctxGeneral, {
        type: 'doughnut',
        data: {
            labels: ['Total Fichas', 'Enviadas', 'Aprobadas', 'Rechazadas'],
            datasets: [{
                data: [estadisticas.total, estadisticas.enviadas, estadisticas.aprobadas, estadisticas.rechazadas],
                backgroundColor: ['#007bff', '#17a2b8', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico Estados
    const ctxEstados = document.getElementById('chartEstados').getContext('2d');
    new Chart(ctxEstados, {
        type: 'bar',
        data: {
            labels: ['Enviadas', 'Revisadas', 'Aprobadas', 'Rechazadas'],
            datasets: [{
                label: 'Cantidad',
                data: [estadisticas.enviadas, estadisticas.revisadas, estadisticas.aprobadas, estadisticas.rechazadas],
                backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico Becados
    const ctxBecados = document.getElementById('chartBecados').getContext('2d');
    new Chart(ctxBecados, {
        type: 'pie',
        data: {
            labels: ['Con Beca', 'Sin Beca'],
            datasets: [{
                data: [estadisticas.aprobadas, estadisticas.total - estadisticas.aprobadas],
                backgroundColor: ['#28a745', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico Tendencias
    const ctxTendencias = document.getElementById('chartTendencias').getContext('2d');
    new Chart(ctxTendencias, {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
            datasets: [{
                label: 'Fichas Enviadas',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function exportarGrafico(chartId, nombreArchivo) {
    const canvas = document.getElementById(chartId);
    const url = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = nombreArchivo + '.png';
    link.href = url;
    link.click();
}

function verFicha(id) {
    // Mostrar modal con detalles de la ficha
    Swal.fire({
        title: 'Cargando ficha...',
        text: 'Obteniendo información de la ficha',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener detalles de la ficha via AJAX
    fetch(`<?= base_url('admin-bienestar/ver-ficha/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ficha = data.ficha || data.data;
                mostrarDetallesFicha(ficha);
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar la ficha', 'error');
        });
}

function mostrarDetallesFicha(ficha) {
    const contenido = `
        <div class="text-start">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Estudiante:</strong> ${ficha.nombre} ${ficha.apellido}
                </div>
                <div class="col-md-6">
                    <strong>Cédula:</strong> ${ficha.cedula}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Email:</strong> ${ficha.email}
                </div>
                <div class="col-md-6">
                    <strong>Período:</strong> ${ficha.nombre_periodo}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Estado:</strong> 
                    <span class="badge ${ficha.estado === 'Aprobada' ? 'bg-success' : 
                                       ficha.estado === 'Rechazada' ? 'bg-danger' : 
                                       ficha.estado === 'Revisada' ? 'bg-warning' : 'bg-info'}">
                        ${ficha.estado}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Fecha Creación:</strong> ${new Date(ficha.fecha_creacion).toLocaleDateString('es-ES')}
                </div>
            </div>
            ${ficha.fecha_envio ? `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Fecha Envío:</strong> ${new Date(ficha.fecha_envio).toLocaleDateString('es-ES')}
                </div>
            </div>
            ` : ''}
            ${ficha.fecha_revision ? `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Fecha Revisión:</strong> ${new Date(ficha.fecha_revision).toLocaleDateString('es-ES')}
                </div>
            </div>
            ` : ''}
            ${ficha.observaciones ? `
            <div class="row mb-3">
                <div class="col-12">
                    <strong>Observaciones:</strong><br>
                    <small class="text-muted">${ficha.observaciones}</small>
                </div>
            </div>
            ` : ''}
            
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-primary btn-sm" onclick="verVistaPreviaFicha(${ficha.id})">
                        <i class="bi bi-eye me-2"></i>Ver Vista Previa del Formulario
                    </button>
                </div>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Detalles de la Ficha',
        html: contenido,
        width: '600px',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#6c757d'
    });
}

function aprobarFicha(id) {
    Swal.fire({
        title: '¿Aprobar ficha?',
        text: 'Esta acción aprobará la ficha socioeconómica',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar petición AJAX para aprobar
            fetch(`<?= base_url('admin-bienestar/aprobar-ficha') ?>/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Aprobada!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al aprobar la ficha', 'error');
            });
        }
    });
}

function rechazarFicha(id) {
    Swal.fire({
        title: '¿Rechazar ficha?',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo',
        inputPlaceholder: 'Ingresa el motivo del rechazo...',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Rechazar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debes ingresar un motivo para el rechazo'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar petición AJAX para rechazar
            const formData = new FormData();
            formData.append('motivo', result.value);
            
            fetch(`<?= base_url('admin-bienestar/rechazar-ficha') ?>/${id}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Rechazada!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al rechazar la ficha', 'error');
            });
        }
    });
}

function descargarFicha(id) {
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Generando PDF...',
        text: 'Por favor espera mientras se genera el documento',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Realizar descarga
    window.open(`<?= base_url('admin-bienestar/exportar-ficha-pdf/') ?>${id}`, '_blank');
    
    // Cerrar indicador de carga después de un momento
    setTimeout(() => {
        Swal.close();
    }, 2000);
}

function verVistaPreviaFicha(id) {
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Cargando formulario...',
        text: 'Obteniendo vista previa de la ficha',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener datos de la ficha para la vista previa
    fetch(`<?= base_url('admin-bienestar/ver-ficha/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ficha = data.ficha || data.data;
                mostrarVistaPreviaFicha(ficha);
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar la vista previa', 'error');
        });
}

function mostrarVistaPreviaFicha(ficha) {
    // Decodificar datos JSON de la ficha
    let datosFicha = {};
    try {
        datosFicha = JSON.parse(ficha.json_data || '{}');
    } catch (e) {
        datosFicha = {};
    }
    
    // Generar contenido HTML del formulario
    let contenido = `
        <div class="text-start" style="max-height: 500px; overflow-y: auto;">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Vista Previa del Formulario:</strong> Esta es la información que el estudiante llenó en la ficha socioeconómica.
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre:</strong> ${ficha.nombre} ${ficha.apellido}
                        </div>
                        <div class="col-md-6">
                            <strong>Cédula:</strong> ${ficha.cedula}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Email:</strong> ${ficha.email}
                        </div>
                        <div class="col-md-6">
                            <strong>Período:</strong> ${ficha.nombre_periodo}
                        </div>
                    </div>
                </div>
            </div>`;
    
    // Mostrar datos específicos de la ficha si existen
    if (Object.keys(datosFicha).length > 0) {
        contenido += `<div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Datos del Formulario</h6>
            </div>
            <div class="card-body">`;
        
        Object.entries(datosFicha).forEach(([seccion, datos]) => {
            contenido += `<div class="mb-3">
                <h6 class="text-primary">${seccion.charAt(0).toUpperCase() + seccion.slice(1).replace(/_/g, ' ')}</h6>`;
            
            if (typeof datos === 'object' && datos !== null) {
                Object.entries(datos).forEach(([campo, valor]) => {
                    contenido += `<div class="row mb-2">
                        <div class="col-md-4">
                            <strong>${campo.charAt(0).toUpperCase() + campo.slice(1).replace(/_/g, ' ')}:</strong>
                        </div>
                        <div class="col-md-8">
                            ${valor || 'No especificado'}
                        </div>
                    </div>`;
                });
            } else {
                contenido += `<div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Valor:</strong>
                    </div>
                    <div class="col-md-8">
                        ${datos || 'No especificado'}
                    </div>
                </div>`;
            }
            
            contenido += `</div>`;
        });
        
        contenido += `</div></div>`;
    } else {
        contenido += `<div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No hay datos del formulario disponibles para mostrar.
        </div>`;
    }
    
    contenido += `</div>`;
    
    Swal.fire({
        title: 'Vista Previa del Formulario',
        html: contenido,
        width: '800px',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#6c757d',
        showCloseButton: true
    });
}

// Función para abrir la evaluación socioeconómica
function evaluacionSocioeconomica(id) {
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEvaluacionSocioeconomica'));
    modal.show();
    
    // Mostrar loading
    document.getElementById('loadingEvaluacion').style.display = 'block';
    document.getElementById('contenidoEvaluacion').style.display = 'none';
    
    // Obtener datos de la ficha
    fetch(`<?= base_url('admin-bienestar/ver-ficha/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ficha = data.ficha || data.data;
                mostrarInfoEstudianteEvaluacion(ficha);
                
                // Cargar evaluación existente si existe
                cargarEvaluacionExistente(id);
            } else {
                Swal.fire('Error', data.error, 'error');
                modal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar la ficha', 'error');
            modal.hide();
        })
        .finally(() => {
            document.getElementById('loadingEvaluacion').style.display = 'none';
            document.getElementById('contenidoEvaluacion').style.display = 'block';
        });
}

// Función para mostrar información del estudiante en la evaluación
function mostrarInfoEstudianteEvaluacion(ficha) {
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <strong>Nombre:</strong> ${ficha.nombre} ${ficha.apellido}
            </div>
            <div class="col-md-6">
                <strong>Cédula:</strong> ${ficha.cedula}
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Email:</strong> ${ficha.email}
            </div>
            <div class="col-md-6">
                <strong>Período:</strong> ${ficha.nombre_periodo}
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Estado de la Ficha:</strong> 
                <span class="badge ${ficha.estado === 'Aprobada' ? 'bg-success' : 
                                   ficha.estado === 'Rechazada' ? 'bg-danger' : 
                                   ficha.estado === 'Revisada' ? 'bg-warning' : 'bg-info'}">
                    ${ficha.estado}
                </span>
            </div>
            <div class="col-md-6">
                <strong>Fecha de Creación:</strong> ${new Date(ficha.fecha_creacion).toLocaleDateString('es-ES')}
            </div>
        </div>
    `;
    
    document.getElementById('infoEstudianteEvaluacion').innerHTML = contenido;
    
    // Agregar datos socioeconómicos relevantes para la evaluación
    mostrarDatosSocioeconomicosEvaluacion(ficha);
}

// Función para mostrar datos socioeconómicos en la evaluación manual
function mostrarDatosSocioeconomicosEvaluacion(ficha) {
    let datosFicha = {};
    try {
        if (ficha.json_data) {
            datosFicha = JSON.parse(ficha.json_data);
        }
    } catch (e) {
        console.error('Error parseando JSON:', e);
    }
    
    // Crear elemento para mostrar datos socioeconómicos
    let datosContainer = document.getElementById('datosSocioeconomicosEvaluacion');
    if (!datosContainer) {
        // Si no existe, crear después de la información del estudiante
        const infoContainer = document.getElementById('infoEstudianteEvaluacion');
        const datosDiv = document.createElement('div');
        datosDiv.className = 'card mt-3';
        datosDiv.innerHTML = `
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Datos Socioeconómicos para Evaluación</h6>
            </div>
            <div class="card-body" id="datosSocioeconomicosEvaluacion">
                <!-- Se llena dinámicamente -->
            </div>
        `;
        infoContainer.parentNode.insertBefore(datosDiv, infoContainer.nextSibling);
        datosContainer = document.getElementById('datosSocioeconomicosEvaluacion');
    }
    
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Datos de Ingresos Familiares:</h6>
                <div class="mb-2">
                    <strong>Ingresos del Padre:</strong> 
                    <span class="badge bg-info">$${datosFicha.ingresos_padre || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Ingresos de la Madre:</strong> 
                    <span class="badge bg-info">$${datosFicha.ingresos_madre || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Otros Ingresos:</strong> 
                    <span class="badge bg-info">$${datosFicha.otros_ingresos || 'No especificado'}</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-warning">Datos de Egresos Familiares:</h6>
                <div class="mb-2">
                    <strong>Gastos de Vivienda:</strong> 
                    <span class="badge bg-warning">$${datosFicha.gastos_vivienda || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Gastos de Alimentación:</strong> 
                    <span class="badge bg-warning">$${datosFicha.gastos_alimentacion || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Otros Gastos:</strong> 
                    <span class="badge bg-warning">$${datosFicha.otros_gastos || 'No especificado'}</span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-success">
                    <h6><strong>Total de Ingresos Familiares:</strong></h6>
                    <h4 class="text-success mb-0">
                        $${calcularTotalIngresos(datosFicha)}
                    </h4>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><strong>Información Adicional:</strong></h6>
                    <p class="mb-1"><strong>Número de Dependientes:</strong> ${datosFicha.numero_dependientes || 'No especificado'}</p>
                    <p class="mb-1"><strong>Tipo de Vivienda:</strong> ${datosFicha.tipo_vivienda || 'No especificado'}</p>
                    <p class="mb-1"><strong>Zona de Residencia:</strong> ${datosFicha.zona_residencia || 'No especificado'}</p>
                    <p class="mb-0"><strong>Nivel Educativo de los Padres:</strong> ${datosFicha.nivel_educativo_padres || 'No especificado'}</p>
                </div>
            </div>
        </div>
    `;
    
    datosContainer.innerHTML = contenido;
}

// Función para cargar evaluación existente
function cargarEvaluacionExistente(fichaId) {
    // Aquí se cargaría la evaluación existente desde la base de datos
    // Por ahora se deja vacío para implementar después
}

// Función para guardar la evaluación socioeconómica
function guardarEvaluacionSocioeconomica() {
    const categoriaSeleccionada = document.querySelector('input[name="categoria_socioeconomica"]:checked');
    const observaciones = document.getElementById('observacionesEvaluacion').value;
    
    if (!categoriaSeleccionada) {
        Swal.fire('Error', 'Debe seleccionar una categoría socioeconómica', 'error');
        return;
    }
    
    const datosEvaluacion = {
        categoria: categoriaSeleccionada.value,
        observaciones: observaciones,
        fecha_evaluacion: new Date().toISOString(),
        admin_evaluador_id: <?= session('id') ?? 0 ?>
    };
    
    // Mostrar confirmación
    Swal.fire({
        title: '¿Guardar Evaluación?',
        text: `Se guardará la categoría ${datosEvaluacion.categoria} para el estudiante`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí se enviaría la evaluación a la base de datos
            // Por ahora se simula el guardado
            Swal.fire('¡Guardado!', 'La evaluación socioeconómica ha sido guardada exitosamente', 'success').then(() => {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEvaluacionSocioeconomica'));
                modal.hide();
                
                // Limpiar formulario
                document.querySelector('input[name="categoria_socioeconomica"]:checked').checked = false;
                document.getElementById('observacionesEvaluacion').value = '';
            });
        }
    });
}

// Debug
// ===== FUNCIONES PARA EVALUACIÓN AUTOMÁTICA =====

// Función para abrir la evaluación automática masiva
function evaluacionAutomaticaMasiva() {
    const modal = new bootstrap.Modal(document.getElementById('modalEvaluacionAutomatica'));
    modal.show();
    
    // Mostrar información del proceso masivo
    mostrarInfoProcesoMasivo();
    
    // Cargar lista de estudiantes sin beca
    cargarEstudiantesSinBeca();
}

// Función para mostrar información del proceso masivo
function mostrarInfoProcesoMasivo() {
    const contenido = `
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i><strong>Proceso de Evaluación Automática Masiva</strong></h6>
                    <p class="mb-2">Este sistema procesará automáticamente <strong>TODOS los estudiantes que no están relacionados a becas</strong> para determinar su categoría socioeconómica basándose en los ingresos familiares totales.</p>
                    <ul class="mb-0">
                        <li><strong>Alcance:</strong> Solo estudiantes sin solicitudes de beca activas</li>
                        <li><strong>Método:</strong> Cálculo automático basado en ingresos familiares</li>
                        <li><strong>Resultado:</strong> Categorización A, B o C según rangos establecidos</li>
                        <li><strong>Personalización:</strong> Los rangos pueden ajustarse según políticas institucionales</li>
                    </ul>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('infoEstudianteAutomatica').innerHTML = contenido;
}

// Función para cargar estudiantes sin beca
function cargarEstudiantesSinBeca() {
    // Mostrar loading
    document.getElementById('datosSocioeconomicosAutomatica').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando lista de estudiantes sin beca...</p>
        </div>
    `;
    
    // Obtener estudiantes sin beca desde el controlador
    fetch('<?= base_url('admin-bienestar/estudiantes-sin-beca') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarListaEstudiantesSinBeca(data.estudiantes);
            } else {
                document.getElementById('datosSocioeconomicosAutomatica').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${data.error}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('datosSocioeconomicosAutomatica').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> No se pudo cargar la lista de estudiantes
                </div>
            `;
        });
}

// Función para mostrar lista de estudiantes sin beca
function mostrarListaEstudiantesSinBeca(estudiantes) {
    if (!estudiantes || estudiantes.length === 0) {
        document.getElementById('datosSocioeconomicosAutomatica').innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>No hay estudiantes sin beca</strong>
                <p class="mb-0">Todos los estudiantes tienen solicitudes de beca activas o no hay fichas socioeconómicas disponibles.</p>
            </div>
        `;
        return;
    }
    
    let tablaHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Carrera</th>
                        <th>Período</th>
                        <th>Total Ingresos</th>
                        <th>Categoría Sugerida</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    estudiantes.forEach((estudiante, index) => {
        const totalIngresos = calcularTotalIngresosEstudiante(estudiante);
        const categoriaSugerida = calcularCategoriaSugerida(totalIngresos);
        
        tablaHTML += `
            <tr>
                <td>
                    <strong>${estudiante.nombre} ${estudiante.apellido}</strong>
                    <br><small class="text-muted">${estudiante.email}</small>
                </td>
                <td>${estudiante.cedula}</td>
                <td><span class="badge bg-info">${estudiante.carrera_nombre || 'No especificada'}</span></td>
                <td>${estudiante.periodo_nombre}</td>
                <td>
                    <span class="badge bg-success fs-6">$${totalIngresos.toFixed(2)}</span>
                </td>
                <td>
                    <span class="badge ${getBadgeClass(categoriaSugerida)} fs-6">${categoriaSugerida}</span>
                </td>
            </tr>
        `;
    });
    
    tablaHTML += `
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <div class="alert alert-success">
                <h6><strong>Resumen del Proceso:</strong></h6>
                <p class="mb-2">Se encontraron <strong>${estudiantes.length} estudiantes</strong> que pueden ser evaluados automáticamente.</p>
                <p class="mb-0">Haga clic en "Procesar Evaluación Masiva" para aplicar la categorización a todos los estudiantes listados.</p>
            </div>
        </div>
    `;
    
    document.getElementById('datosSocioeconomicosAutomatica').innerHTML = tablaHTML;
}

// Función para calcular total de ingresos de un estudiante
function calcularTotalIngresosEstudiante(estudiante) {
    let datosFicha = {};
    try {
        if (estudiante.json_data) {
            datosFicha = JSON.parse(estudiante.json_data);
        }
    } catch (e) {
        console.error('Error parseando JSON:', e);
    }
    
    const ingresosPadre = parseFloat(datosFicha.ingresos_padre) || 0;
    const ingresosMadre = parseFloat(datosFicha.ingresos_madre) || 0;
    const otrosIngresos = parseFloat(datosFicha.otros_ingresos) || 0;
    
    return ingresosPadre + ingresosMadre + otrosIngresos;
}

// Función para calcular categoría sugerida
function calcularCategoriaSugerida(totalIngresos) {
    if (totalIngresos === 0) return 'Sin datos';
    
    const rangoAInicio = parseFloat(document.getElementById('rangoAInicio').value);
    const rangoAFin = parseFloat(document.getElementById('rangoAFin').value);
    const rangoBInicio = parseFloat(document.getElementById('rangoBInicio').value);
    const rangoBFin = parseFloat(document.getElementById('rangoBFin').value);
    const rangoCInicio = parseFloat(document.getElementById('rangoCInicio').value);
    const rangoCFin = parseFloat(document.getElementById('rangoCFin').value);
    
    if (totalIngresos >= rangoAInicio && totalIngresos <= rangoAFin) return 'A';
    if (totalIngresos >= rangoBInicio && totalIngresos <= rangoBFin) return 'B';
    if (totalIngresos >= rangoCInicio && totalIngresos <= rangoCFin) return 'C';
    
    return 'FUERA DE RANGO';
}

// Función para obtener clase de badge según categoría
function getBadgeClass(categoria) {
    switch(categoria) {
        case 'A': return 'bg-danger';
        case 'B': return 'bg-warning';
        case 'C': return 'bg-success';
        case 'Sin datos': return 'bg-secondary';
        default: return 'bg-secondary';
    }
}

// Función para mostrar datos socioeconómicos relevantes
function mostrarDatosSocioeconomicosAutomatica(ficha) {
    let datosFicha = {};
    try {
        if (ficha.json_data) {
            datosFicha = JSON.parse(ficha.json_data);
        }
    } catch (e) {
        console.error('Error parseando JSON:', e);
    }
    
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Datos de Ingresos Familiares:</h6>
                <div class="mb-2">
                    <strong>Ingresos del Padre:</strong> 
                    <span class="badge bg-info">$${datosFicha.ingresos_padre || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Ingresos de la Madre:</strong> 
                    <span class="badge bg-info">$${datosFicha.ingresos_madre || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Otros Ingresos:</strong> 
                    <span class="badge bg-info">$${datosFicha.otros_ingresos || 'No especificado'}</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-warning">Datos de Egresos Familiares:</h6>
                <div class="mb-2">
                    <strong>Gastos de Vivienda:</strong> 
                    <span class="badge bg-warning">$${datosFicha.gastos_vivienda || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Gastos de Alimentación:</strong> 
                    <span class="badge bg-warning">$${datosFicha.gastos_alimentacion || 'No especificado'}</span>
                </div>
                <div class="mb-2">
                    <strong>Otros Gastos:</strong> 
                    <span class="badge bg-warning">$${datosFicha.otros_gastos || 'No especificado'}</span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-success">
                    <h6><strong>Total de Ingresos Familiares:</strong></h6>
                    <h4 class="text-success mb-0" id="totalIngresosFamiliares">
                        $${calcularTotalIngresos(datosFicha)}
                    </h4>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('datosSocioeconomicosAutomatica').innerHTML = contenido;
}

// Función para calcular total de ingresos
function calcularTotalIngresos(datosFicha) {
    const ingresosPadre = parseFloat(datosFicha.ingresos_padre) || 0;
    const ingresosMadre = parseFloat(datosFicha.ingresos_madre) || 0;
    const otrosIngresos = parseFloat(datosFicha.otros_ingresos) || 0;
    
    return (ingresosPadre + ingresosMadre + otrosIngresos).toFixed(2);
}

// Función para calcular categoría automáticamente
function calcularCategoriaAutomatica(ficha) {
    let datosFicha = {};
    try {
        if (ficha.json_data) {
            datosFicha = JSON.parse(ficha.json_data);
        }
    } catch (e) {
        console.error('Error parseando JSON:', e);
    }
    
    const totalIngresos = parseFloat(calcularTotalIngresos(datosFicha));
    
    if (totalIngresos === 0) {
        document.getElementById('categoriaAutomatica').innerHTML = '<span class="badge bg-secondary fs-5">Sin datos</span>';
        document.getElementById('explicacionAutomatica').textContent = 'No hay datos de ingresos para calcular la categoría';
        return;
    }
    
    // Obtener rangos personalizados o usar los por defecto
    const rangoAInicio = parseFloat(document.getElementById('rangoAInicio').value);
    const rangoAFin = parseFloat(document.getElementById('rangoAFin').value);
    const rangoBInicio = parseFloat(document.getElementById('rangoBInicio').value);
    const rangoBFin = parseFloat(document.getElementById('rangoBFin').value);
    const rangoCInicio = parseFloat(document.getElementById('rangoCInicio').value);
    const rangoCFin = parseFloat(document.getElementById('rangoCFin').value);
    
    let categoria = '';
    let explicacion = '';
    let badgeClass = '';
    
    if (totalIngresos >= rangoAInicio && totalIngresos <= rangoAFin) {
        categoria = 'A';
        explicacion = `Ingresos familiares de $${totalIngresos.toFixed(2)} están en el rango de Categoría A ($${rangoAInicio} - $${rangoAFin}) - Baja capacidad económica`;
        badgeClass = 'bg-danger';
    } else if (totalIngresos >= rangoBInicio && totalIngresos <= rangoBFin) {
        categoria = 'B';
        explicacion = `Ingresos familiares de $${totalIngresos.toFixed(2)} están en el rango de Categoría B ($${rangoBInicio} - $${rangoBFin}) - Media capacidad económica`;
        badgeClass = 'bg-warning';
    } else if (totalIngresos >= rangoCInicio && totalIngresos <= rangoCFin) {
        categoria = 'C';
        explicacion = `Ingresos familiares de $${totalIngresos.toFixed(2)} están en el rango de Categoría C ($${rangoCInicio} - $${rangoCFin}) - Alta capacidad económica`;
        badgeClass = 'bg-success';
    } else {
        categoria = 'FUERA DE RANGO';
        explicacion = `Ingresos familiares de $${totalIngresos.toFixed(2)} están fuera de los rangos establecidos`;
        badgeClass = 'bg-secondary';
    }
    
    document.getElementById('categoriaAutomatica').innerHTML = `<span class="badge ${badgeClass} fs-5">${categoria}</span>`;
    document.getElementById('explicacionAutomatica').textContent = explicacion;
}

// Función para aplicar rangos personalizados
function aplicarRangosPersonalizados() {
    const rangoAInicio = parseFloat(document.getElementById('rangoAInicio').value);
    const rangoAFin = parseFloat(document.getElementById('rangoAFin').value);
    const rangoBInicio = parseFloat(document.getElementById('rangoBInicio').value);
    const rangoBFin = parseFloat(document.getElementById('rangoBFin').value);
    const rangoCInicio = parseFloat(document.getElementById('rangoCInicio').value);
    const rangoCFin = parseFloat(document.getElementById('rangoCFin').value);
    
    // Validar que los rangos no se superpongan
    if (rangoAFin >= rangoBInicio || rangoBFin >= rangoCInicio) {
        Swal.fire('Error', 'Los rangos no pueden superponerse. Ajuste los valores.', 'error');
        return;
    }
    
    // Validar que los rangos sean coherentes
    if (rangoAInicio >= rangoAFin || rangoBInicio >= rangoBFin || rangoCInicio >= rangoCFin) {
        Swal.fire('Error', 'El valor de inicio debe ser menor al valor de fin en cada categoría.', 'error');
        return;
    }
    
    // Recalcular categoría con nuevos rangos
    const modal = document.getElementById('modalEvaluacionAutomatica');
    const fichaId = modal.getAttribute('data-ficha-id');
    
    Swal.fire({
        title: 'Rangos Aplicados',
        text: 'Los rangos personalizados se han aplicado correctamente. La categoría se recalculará automáticamente.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
    
    // Recalcular si hay una ficha cargada
    if (fichaId) {
        fetch(`<?= base_url('admin-bienestar/ver-ficha/') ?>${fichaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calcularCategoriaAutomatica(data.ficha || data.data);
                }
            });
    }
}

// Función para restaurar rangos por defecto
function restaurarRangosDefault() {
    document.getElementById('rangoAInicio').value = 100;
    document.getElementById('rangoAFin').value = 500;
    document.getElementById('rangoBInicio').value = 501;
    document.getElementById('rangoBFin').value = 1000;
    document.getElementById('rangoCInicio').value = 1001;
    document.getElementById('rangoCFin').value = 1500;
    
    Swal.fire({
        title: 'Rangos Restaurados',
        text: 'Se han restaurado los rangos por defecto.',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
    
    // Recalcular si hay una ficha cargada
    const modal = document.getElementById('modalEvaluacionAutomatica');
    const fichaId = modal.getAttribute('data-ficha-id');
    if (fichaId) {
        fetch(`<?= base_url('admin-bienestar/ver-ficha/') ?>${fichaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calcularCategoriaAutomatica(data.ficha || data.data);
                }
            });
    }
}

    // Función para abrir modal de verificación de código
    function verificarCodigo() {
        $('#modalVerificarCodigo').modal('show');
        $('#codigoVerificacion').val('').focus();
        $('#resultadoVerificacion').hide();
    }
    
    // Función para verificar código ingresado
    function verificarCodigoIngresado() {
        const codigo = $('#codigoVerificacion').val().trim();
        
        if (!codigo) {
            Swal.fire({
                icon: 'warning',
                title: 'Código Requerido',
                text: 'Por favor ingrese un código de verificación'
            });
            return;
        }
        
        // Mostrar loading
        Swal.fire({
            title: 'Verificando Código...',
            text: 'Validando autenticidad del documento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Llamada AJAX para verificar
        fetch('<?= base_url('verificar-codigo-pdf') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            mostrarResultadoVerificacion(data);
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo verificar el código. Intente nuevamente.'
            });
        });
    }
    
    // Función para mostrar resultado de verificación
    function mostrarResultadoVerificacion(data) {
        const resultadoDiv = $('#resultadoVerificacion');
        
        if (data.valido && data.datos) {
            // Información del código
            let infoCodigo = `
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Código:</strong> <span class="badge bg-primary">${data.datos.codigo}</span></p>
                        <p><strong>Fecha de Generación:</strong> ${new Date(data.datos.fecha_generacion).toLocaleString()}</p>
                        <p><strong>Usuario Generador:</strong> ID ${data.datos.id_usuario}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>IP de Generación:</strong> ${data.datos.ip_generacion}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-success">Verificado</span></p>
                        <p><strong>Fecha Verificación:</strong> ${new Date().toLocaleString()}</p>
                    </div>
                </div>`;
            
            // Información del documento
            let infoDocumento = '';
            if (data.datos.informacion_documento) {
                const doc = data.datos.informacion_documento;
                infoDocumento = `
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Información del Documento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tipo:</strong> ${doc.tipo}</p>
                                    <p><strong>Estudiante:</strong> ${doc.estudiante || 'N/A'}</p>
                                    <p><strong>Cédula:</strong> ${doc.cedula || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Carrera:</strong> ${doc.carrera || 'N/A'}</p>
                                    <p><strong>Período:</strong> ${doc.periodo || 'N/A'}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-warning">${doc.estado || 'N/A'}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }
            
            resultadoDiv.html(`
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle me-2"></i><strong>¡Código Válido!</strong></h6>
                    <p class="mb-2"><strong>Mensaje:</strong> ${data.mensaje}</p>
                    ${infoCodigo}
                    ${infoDocumento}
                </div>
            `);
        } else {
            // Código inválido o ya verificado
            let mensajeError = data.mensaje || 'Error desconocido';
            let infoAdicional = '';
            
            if (data.datos && data.datos.fecha_verificacion) {
                infoAdicional = `
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="mb-0"><strong>Información Adicional:</strong> Este código ya fue verificado anteriormente.</p>
                        <p class="mb-0"><strong>Fecha de Verificación:</strong> ${new Date(data.datos.fecha_verificacion).toLocaleString()}</p>
                        <p class="mb-0"><strong>IP de Verificación:</strong> ${data.datos.ip_verificacion || 'N/A'}</p>
                    </div>`;
            }
            
            resultadoDiv.html(`
                <div class="alert alert-danger">
                    <h6><i class="bi bi-x-circle me-2"></i><strong>Código Inválido</strong></h6>
                    <p class="mb-0"><strong>Razón:</strong> ${mensajeError}</p>
                    ${infoAdicional}
                </div>
            `);
        }
        
        resultadoDiv.show();
    }
    
    // Función para guardar evaluación automática masiva
    function guardarEvaluacionAutomatica() {
    Swal.fire({
        title: '¿Procesar Evaluación Masiva?',
        text: '¿Desea procesar la evaluación socioeconómica automática para TODOS los estudiantes sin beca listados?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Procesar Masivamente',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar progreso
            Swal.fire({
                title: 'Procesando Evaluación Masiva',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-success mb-3" role="status">
                            <span class="visually-hidden">Procesando...</span>
                        </div>
                        <p>Procesando evaluación socioeconómica para todos los estudiantes...</p>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            // Simular proceso de evaluación masiva
            procesarEvaluacionMasiva();
        }
    });
}

// Función para procesar evaluación masiva
function procesarEvaluacionMasiva() {
    // Obtener estudiantes de la tabla
    const tabla = document.querySelector('#datosSocioeconomicosAutomatica table tbody');
    if (!tabla) {
        Swal.fire('Error', 'No se encontró la lista de estudiantes', 'error');
        return;
    }
    
    const filas = tabla.querySelectorAll('tr');
    const totalEstudiantes = filas.length;
    let procesados = 0;
    
    if (totalEstudiantes === 0) {
        Swal.fire('Error', 'No hay estudiantes para procesar', 'error');
        return;
    }
    
    // Procesar cada estudiante
    const procesarEstudiante = (index) => {
        if (index >= totalEstudiantes) {
            // Proceso completado
            Swal.fire({
                title: '¡Evaluación Masiva Completada!',
                html: `
                    <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Proceso Finalizado Exitosamente</h5>
                        <p>Se han procesado <strong>${totalEstudiantes} estudiantes</strong> con evaluación automática.</p>
                        <div class="alert alert-info">
                            <small><strong>Nota:</strong> Los resultados se han calculado automáticamente. Para guardar permanentemente en la base de datos, implemente la lógica correspondiente en el controlador.</small>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Cerrar'
            }).then(() => {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEvaluacionAutomatica'));
                modal.hide();
            });
            return;
        }
        
        const fila = filas[index];
        const nombreEstudiante = fila.querySelector('td:first-child strong').textContent;
        const categoria = fila.querySelector('td:last-child .badge').textContent;
        
        // Actualizar progreso
        const progreso = ((index + 1) / totalEstudiantes) * 100;
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = progreso + '%';
        }
        
        // Simular procesamiento
        setTimeout(() => {
            procesarEstudiante(index + 1);
        }, 200); // Simular tiempo de procesamiento
    };
    
    // Iniciar procesamiento
    procesarEstudiante(0);
}

// ===== FIN FUNCIONES EVALUACIÓN AUTOMÁTICA =====
</script>

<!-- Modal Evaluación Socioeconómica -->
<div class="modal fade" id="modalEvaluacionSocioeconomica" tabindex="-1" aria-labelledby="modalEvaluacionSocioeconomicaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEvaluacionSocioeconomicaLabel">
                    <i class="bi bi-calculator me-2"></i>EVALUACIÓN SOCIOECONÓMICA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Solo para uso la UNIDAD BIENESTAR INSTITUCIONAL</strong>
                </div>
                
                <div id="loadingEvaluacion" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información de la ficha...</p>
                </div>
                
                <div id="contenidoEvaluacion" style="display: none;">
                    <!-- Información del Estudiante -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                        </div>
                        <div class="card-body" id="infoEstudianteEvaluacion">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>
                    
                    <!-- Tabla de Evaluación Socioeconómica -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Tabla de Categorización</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center" style="width: 20%;">CATEGORÍAS</th>
                                            <th class="text-center" style="width: 50%;">PARÁMETRO ECONÓMICO EN FUNCIÓN DEL TOTAL DE INGRESOS FAMILIARES ($)</th>
                                            <th class="text-center" style="width: 30%;">UBICACIÓN DEL/LA ESTUDIANTE<br><small>(señale con una X según corresponda)</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center fw-bold">A</td>
                                            <td class="text-center">100 – 500</td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="categoria_socioeconomica" id="categoria_a" value="A">
                                                    <label class="form-check-label ms-2" for="categoria_a">
                                                        <strong>X</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">B</td>
                                            <td class="text-center">501 – 1000</td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="categoria_socioeconomica" id="categoria_b" value="B">
                                                    <label class="form-check-label ms-2" for="categoria_b">
                                                        <strong>X</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">C</td>
                                            <td class="text-center">1001 – 1500</td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="categoria_socioeconomica" id="categoria_c" value="C">
                                                    <label class="form-check-label ms-2" for="categoria_c">
                                                        <strong>X</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Observaciones -->
                            <div class="mt-3">
                                <label for="observacionesEvaluacion" class="form-label">
                                    <strong>Observaciones de la Evaluación:</strong>
                                </label>
                                <textarea class="form-control" id="observacionesEvaluacion" rows="3" 
                                          placeholder="Ingrese observaciones adicionales sobre la evaluación socioeconómica..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarEvaluacion" onclick="guardarEvaluacionSocioeconomica()">
                    <i class="bi bi-save me-2"></i>Guardar Evaluación
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Evaluación Automática -->
<div class="modal fade" id="modalEvaluacionAutomatica" tabindex="-1" aria-labelledby="modalEvaluacionAutomaticaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalEvaluacionAutomaticaLabel">
                    <i class="bi bi-robot me-2"></i>Evaluación Socioeconómica Automática Masiva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del Estudiante -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                    </div>
                    <div class="card-body" id="infoEstudianteAutomatica">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <!-- Datos Socioeconómicos para Evaluación -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Datos para Evaluación Automática</h6>
                    </div>
                    <div class="card-body" id="datosSocioeconomicosAutomatica">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <!-- Fórmula de Cálculo -->
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Fórmula de Cálculo Automático</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><strong>Fórmula de Categorización Automática:</strong></h6>
                            <p class="mb-2">La categoría se determina automáticamente basándose en el <strong>Total de Ingresos Familiares</strong>:</p>
                            <ul class="mb-0">
                                <li><strong>Categoría A:</strong> $100 - $500 (Baja capacidad económica)</li>
                                <li><strong>Categoría B:</strong> $501 - $1000 (Media capacidad económica)</li>
                                <li><strong>Categoría C:</strong> $1001 - $1500 (Alta capacidad económica)</li>
                            </ul>
                        </div>
                        
                        <!-- Resultado Automático -->
                        <div class="text-center p-3 bg-light rounded">
                            <h5>Resultado de Evaluación Automática:</h5>
                            <div id="resultadoAutomatico" class="mt-3">
                                <span class="badge bg-secondary fs-5" id="categoriaAutomatica">Pendiente de cálculo</span>
                            </div>
                            <p class="text-muted mt-2" id="explicacionAutomatica">Seleccione los datos del estudiante para calcular automáticamente</p>
                        </div>
                    </div>
                </div>

                <!-- Configuración Personalizada -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-sliders me-2"></i>Configuración Personalizada de Rangos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="rangoAInicio" class="form-label">Categoría A - Inicio ($)</label>
                                <input type="number" class="form-control" id="rangoAInicio" value="100" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="rangoAFin" class="form-label">Categoría A - Fin ($)</label>
                                <input type="number" class="form-control" id="rangoAFin" value="500" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="categoriaAPersonalizada" class="form-label">Categoría A</label>
                                <input type="text" class="form-control" id="categoriaAPersonalizada" value="A" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="rangoBInicio" class="form-label">Categoría B - Inicio ($)</label>
                                <input type="number" class="form-control" id="rangoBInicio" value="501" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="rangoBFin" class="form-label">Categoría B - Fin ($)</label>
                                <input type="number" class="form-control" id="rangoBFin" value="1000" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="categoriaBPersonalizada" class="form-label">Categoría B</label>
                                <input type="text" class="form-control" id="categoriaBPersonalizada" value="B" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="rangoCInicio" class="form-label">Categoría C - Inicio ($)</label>
                                <input type="number" class="form-control" id="rangoCInicio" value="1001" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="rangoCFin" class="form-label">Categoría C - Fin ($)</label>
                                <input type="number" class="form-control" id="rangoCFin" value="1500" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="categoriaCPersonalizada" class="form-label">Categoría C</label>
                                <input type="text" class="form-control" id="categoriaCPersonalizada" value="C" readonly>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary" onclick="aplicarRangosPersonalizados()">
                                <i class="bi bi-check-circle me-2"></i>Aplicar Rangos Personalizados
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="restaurarRangosDefault()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Restaurar Valores por Defecto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarEvaluacionAutomatica" onclick="guardarEvaluacionAutomatica()">
                    <i class="bi bi-play-circle me-2"></i>Procesar Evaluación Masiva
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Verificación de Código -->
<div class="modal fade" id="modalVerificarCodigo" tabindex="-1" aria-labelledby="modalVerificarCodigoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVerificarCodigoLabel">
                    <i class="bi bi-search me-2"></i>Verificar Código de Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="codigoVerificacion" class="form-label">
                                <strong>Código de Verificación:</strong>
                            </label>
                            <input type="text" class="form-control form-control-lg" 
                                   id="codigoVerificacion" 
                                   placeholder="Ej: ITSI-20250820-143052-A7B9C"
                                   style="font-family: monospace; font-size: 16px;">
                            <div class="form-text">
                                Ingrese el código que aparece en el pie de página del documento PDF
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-info btn-lg w-100" 
                                onclick="verificarCodigoIngresado()" 
                                style="margin-top: 32px;">
                            <i class="bi bi-search me-2"></i>Verificar
                        </button>
                    </div>
                </div>
                
                <div id="resultadoVerificacion" class="mt-4" style="display: none;">
                    <!-- Aquí se mostrará el resultado de la verificación -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>