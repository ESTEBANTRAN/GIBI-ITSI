<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Períodos Académicos</h4>
                <p class="text-muted mb-0">Administra y configura los períodos académicos del sistema</p>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPeriodo">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Período
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="exportarPeriodos('excel')">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="exportarPeriodos('pdf')">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Estadísticas de Períodos</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportarGrafico('chartGeneral', 'Estadisticas_Periodos')">
                    <i class="bi bi-download"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartGeneral" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Distribución por Estado</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportarGrafico('chartEstados', 'Distribucion_Estados')">
                    <i class="bi bi-download"></i> Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartEstados" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-primary border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Total Períodos</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['total']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-success border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Activos</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['activos']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-success text-success rounded-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-warning border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Inactivos</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['inactivos']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-warning text-warning rounded-3">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-info border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Próximos</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['proximos']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-info text-info rounded-3">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="filtroBusqueda" class="form-label">
                        <i class="bi bi-search me-1"></i>Buscar
                    </label>
                    <input type="text" class="form-control" id="filtroBusqueda" placeholder="Buscar por nombre o descripción..." value="<?= $filtros['busqueda'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filtroEstado" class="form-label">
                        <i class="bi bi-flag me-1"></i>Estado
                    </label>
                    <select class="form-control" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="Activo" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                        <option value="Próximo" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Próximo') ? 'selected' : '' ?>>Próximo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                            <i class="bi bi-x-lg me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Períodos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-calendar-event me-2"></i>Períodos Académicos
        </h5>
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm ms-2" style="width: 250px;">
                <input type="text" class="form-control" id="busquedaRapida" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="tablaPeriodos">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Período</th>
                        <th>Fechas</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Fichas Activas</th>
                        <th class="text-center">Becas Activas</th>
                        <th class="text-center">Contadores</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyPeriodos">
                    <?php if (!empty($periodos)): ?>
                        <?php foreach ($periodos as $periodo): ?>
                            <tr>
                                <td><?= $periodo['id'] ?></td>
                                <td>
                                    <div>
                                        <h6 class="mb-0"><?= $periodo['nombre'] ?></h6>
                                        <?php if (!empty($periodo['descripcion'])): ?>
                                            <small class="text-muted"><?= $periodo['descripcion'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small class="text-muted">Inicio:</small> <?= date('d/m/Y', strtotime($periodo['fecha_inicio'])) ?><br>
                                        <small class="text-muted">Fin:</small> <?= date('d/m/Y', strtotime($periodo['fecha_fin'])) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $hoy = date('Y-m-d');
                                    $fechaInicio = $periodo['fecha_inicio'];
                                    $fechaFin = $periodo['fecha_fin'];
                                    
                                    if ($hoy < $fechaInicio) {
                                        $estado = 'Próximo';
                                        $estadoClass = 'info';
                                    } elseif ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
                                        $estado = 'Activo';
                                        $estadoClass = 'success';
                                    } else {
                                        $estado = 'Finalizado';
                                        $estadoClass = 'secondary';
                                    }
                                    ?>
                                    <span class="badge bg-soft-<?= $estadoClass ?> text-<?= $estadoClass ?>">
                                        <?= $estado ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" 
                                            id="fichas_<?= $periodo['id'] ?>" 
                                            <?= (!empty($periodo['activo_fichas'])) ? 'checked' : '' ?>
                                            onchange="toggleConfiguracion(<?= $periodo['id'] ?>, 'activo_fichas', this.checked)">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" 
                                            id="becas_<?= $periodo['id'] ?>" 
                                            <?= (!empty($periodo['activo_becas'])) ? 'checked' : '' ?>
                                            onchange="toggleConfiguracion(<?= $periodo['id'] ?>, 'activo_becas', this.checked)">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary me-1" title="Fichas">
                                        <i class="bi bi-file-earmark-text"></i> <?= $periodo['total_fichas'] ?? 0 ?>
                                    </span>
                                    <span class="badge bg-soft-warning text-warning" title="Solicitudes">
                                        <i class="bi bi-award"></i> <?= $periodo['total_solicitudes'] ?? 0 ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" title="Ver detalles" onclick="verPeriodo(<?= $periodo['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" title="Editar" onclick="editarPeriodo(<?= $periodo['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Eliminar" onclick="eliminarPeriodo(<?= $periodo['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    No se encontraron períodos académicos
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <?php if (isset($paginacion) && $paginacion['total_registros'] > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <span class="fw-semibold"><?= ($paginacion['offset'] + 1) ?></span> a 
                    <span class="fw-semibold"><?= min($paginacion['offset'] + $paginacion['por_pagina'], $paginacion['total_registros']) ?></span> de 
                    <span class="fw-semibold"><?= number_format($paginacion['total_registros']) ?></span> registros
                </div>
                <nav aria-label="Paginación de períodos">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($paginacion['pagina_actual'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $paginacion['pagina_actual'] - 1 ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                            <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $paginacion['pagina_actual'] + 1 ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Crear/Editar Período -->
<div class="modal fade" id="modalPeriodo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Crear Período Académico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPeriodo">
                <div class="modal-body">
                    <input type="hidden" id="periodo_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Período *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="limite_fichas" class="form-label">Límite de Fichas</label>
                                <input type="number" class="form-control" id="limite_fichas" name="limite_fichas" min="0">
                                <small class="text-muted">Dejar vacío para sin límite</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="limite_becas" class="form-label">Límite de Becas</label>
                                <input type="number" class="form-control" id="limite_becas" name="limite_becas" min="0">
                                <small class="text-muted">Dejar vacío para sin límite</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="vigente_estudiantes" name="vigente_estudiantes">
                                <label class="form-check-label" for="vigente_estudiantes">
                                    Vigente para Estudiantes
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo_fichas" name="activo_fichas">
                                <label class="form-check-label" for="activo_fichas">
                                    Fichas Activas
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo_becas" name="activo_becas">
                                <label class="form-check-label" for="activo_becas">
                                    Becas Activas
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let modalPeriodo;
let isEditing = false;

// Configurar CSRF token para todas las peticiones AJAX
$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});

$(document).ready(function() {
    modalPeriodo = new bootstrap.Modal(document.getElementById('modalPeriodo'));
    
    $('#formPeriodo').on('submit', function(e) {
        e.preventDefault();
        guardarPeriodo();
    });

    // Filtro de búsqueda rápida
    $('#btnBuscar').on('click', function() {
        const query = $('#busquedaRapida').val().toLowerCase().trim();
        $('#tbodyPeriodos tr').each(function() {
            const row = $(this);
            const text = row.text().toLowerCase();
            if (!query || text.includes(query)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });
    
    $('#busquedaRapida').on('keyup', function(e) {
        if (e.key === 'Enter') {
            $('#btnBuscar').click();
        }
    });
});

function aplicarFiltros() {
    const busqueda = $('#filtroBusqueda').val().toLowerCase().trim();
    const estado = $('#filtroEstado').val().toLowerCase().trim();
    
    $('#tbodyPeriodos tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const estadoCol = row.find('td').eq(3).text().toLowerCase().trim();
        
        let matchBusqueda = true;
        let matchEstado = true;
        
        if (busqueda && !text.includes(busqueda)) {
            matchBusqueda = false;
        }
        
        if (estado) {
            if (!estadoCol.includes(estado)) {
                matchEstado = false;
            }
        }
        
        if (matchBusqueda && matchEstado) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function limpiarFiltros() {
    $('#filtroBusqueda').val('');
    $('#filtroEstado').val('');
    $('#busquedaRapida').val('');
    $('#tbodyPeriodos tr').show();
}

function abrirModalCrear() {
    isEditing = false;
    $('#modalTitle').text('Crear Período Académico');
    $('#formPeriodo')[0].reset();
    $('#periodo_id').val('');
    modalPeriodo.show();
}

function editarPeriodo(id) {
    isEditing = true;
    $('#modalTitle').text('Editar Período Académico');
    
    $.ajax({
        url: '<?= base_url('admin-bienestar/obtener-periodo/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const periodo = response.periodo;
                $('#periodo_id').val(periodo.id);
                $('#nombre').val(periodo.nombre);
                $('#descripcion').val(periodo.descripcion || '');
                $('#fecha_inicio').val(periodo.fecha_inicio);
                $('#fecha_fin').val(periodo.fecha_fin);
                $('#limite_fichas').val(periodo.limite_fichas || '');
                $('#limite_becas').val(periodo.limite_becas || '');
                $('#vigente_estudiantes').prop('checked', periodo.vigente_estudiantes == 1);
                $('#activo_fichas').prop('checked', periodo.activo_fichas == 1);
                $('#activo_becas').prop('checked', periodo.activo_becas == 1);
                modalPeriodo.show();
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

function verPeriodo(id) {
    $.ajax({
        url: '<?= base_url('admin-bienestar/ver-periodo/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const periodo = response.periodo;
                const estadisticas = response.estadisticas;
                const estudiantesConFichas = response.estudiantes_con_fichas;
                const estudiantesConBecas = response.estudiantes_con_becas;
                const resumenCarreras = response.resumen_carreras;
                
                // Generar HTML para estudiantes con fichas
                let fichasHTML = '';
                if (estudiantesConFichas.length > 0) {
                    fichasHTML = estudiantesConFichas.map(f => 
                        `<tr>
                            <td>${f.nombre} ${f.apellido}</td>
                            <td>${f.cedula}</td>
                            <td>${f.carrera_nombre || 'Sin carrera'}</td>
                            <td><span class="badge bg-${f.estado === 'Aprobada' ? 'success' : f.estado === 'Rechazada' ? 'danger' : 'warning'}">${f.estado}</span></td>
                            <td>${f.email || 'Sin email'}</td>
                        </tr>`
                    ).join('');
                } else {
                    fichasHTML = '<tr><td colspan="5" class="text-center text-muted">No hay fichas registradas en este período</td></tr>';
                }
                
                // Generar HTML para estudiantes con becas
                let becasHTML = '';
                if (estudiantesConBecas.length > 0) {
                    becasHTML = estudiantesConBecas.map(b => 
                        `<tr>
                            <td>${b.nombre} ${b.apellido}</td>
                            <td>${b.cedula}</td>
                            <td>${b.carrera_nombre || 'Sin carrera'}</td>
                            <td><span class="badge bg-${b.estado === 'Aprobada' ? 'success' : b.estado === 'Rechazada' ? 'danger' : 'warning'}">${b.estado}</span></td>
                            <td>${b.nombre_beca || 'Sin beca'}</td>
                        </tr>`
                    ).join('');
                } else {
                    becasHTML = '<tr><td colspan="5" class="text-center text-muted">No hay becas registradas en este período</td></tr>';
                }
                
                // Generar HTML para resumen por carreras
                let carrerasHTML = '';
                if (resumenCarreras.length > 0) {
                    carrerasHTML = resumenCarreras.map(c => 
                        `<tr>
                            <td>${c.carrera || 'Sin carrera'}</td>
                            <td class="text-center">${c.total_fichas}</td>
                            <td class="text-center">${c.aprobadas}</td>
                            <td class="text-center">${c.total_fichas - c.aprobadas}</td>
                        </tr>`
                    ).join('');
                } else {
                    carrerasHTML = '<tr><td colspan="4" class="text-center text-muted">No hay datos de carreras</td></tr>';
                }
                
                Swal.fire({
                    title: `<i class="bi bi-calendar-event me-2"></i>${periodo.nombre}`,
                    html: `
                        <div class="text-start">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Información del Período</h6>
                                    <p><strong>Descripción:</strong> ${periodo.descripcion || 'Sin descripción'}</p>
                                    <p><strong>Fechas:</strong> ${new Date(periodo.fecha_inicio).toLocaleDateString('es-ES')} - ${new Date(periodo.fecha_fin).toLocaleDateString('es-ES')}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-${periodo.activo ? 'success' : 'secondary'}">${periodo.activo ? 'Activo' : 'Inactivo'}</span></p>
                                    <p><strong>Fichas Activas:</strong> <span class="badge bg-${periodo.activo_fichas ? 'success' : 'secondary'}">${periodo.activo_fichas ? 'Sí' : 'No'}</span></p>
                                    <p><strong>Becas Activas:</strong> <span class="badge bg-${periodo.activo_becas ? 'success' : 'secondary'}">${periodo.activo_becas ? 'Sí' : 'No'}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Estadísticas Generales</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded border">
                                                <div class="h5 mb-0 text-primary">${estadisticas.total_fichas}</div>
                                                <small class="text-muted">Total Fichas</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded border">
                                                <div class="h5 mb-0 text-success">${estadisticas.total_becas}</div>
                                                <small class="text-muted">Total Becas</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded border">
                                                <div class="h5 mb-0 text-info">${estadisticas.fichas_pendientes}</div>
                                                <small class="text-muted">Fichas Pendientes</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded border">
                                                <div class="h5 mb-0 text-warning">${estadisticas.becas_pendientes}</div>
                                                <small class="text-muted">Becas Pendientes</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-info"><i class="bi bi-graph-up me-2"></i>Resumen por Carreras</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Carrera</th>
                                                    <th class="text-center">Total Fichas</th>
                                                    <th class="text-center">Aprobadas</th>
                                                    <th class="text-center">Pendientes</th>
                                                </tr>
                                            </thead>
                                            <tbody>${carrerasHTML}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-info"><i class="bi bi-file-earmark-text me-2"></i>Estudiantes con Fichas (${estudiantesConFichas.length})</h6>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Estudiante</th>
                                                    <th>Cédula</th>
                                                    <th>Carrera</th>
                                                    <th>Estado</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>${fichasHTML}</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-warning"><i class="bi bi-award me-2"></i>Estudiantes con Becas (${estudiantesConBecas.length})</h6>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Estudiante</th>
                                                    <th>Cédula</th>
                                                    <th>Carrera</th>
                                                    <th>Estado</th>
                                                    <th>Beca</th>
                                                </tr>
                                            </thead>
                                            <tbody>${becasHTML}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    width: '1200px',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#6c757d'
                });
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

function guardarPeriodo() {
    // Construir objeto JSON con los datos del formulario
    const datos = {
        nombre: $('#nombre').val(),
        descripcion: $('#descripcion').val(),
        fecha_inicio: $('#fecha_inicio').val(),
        fecha_fin: $('#fecha_fin').val(),
        limite_fichas: $('#limite_fichas').val() || null,
        limite_becas: $('#limite_becas').val() || null,
        vigente_estudiantes: $('#vigente_estudiantes').is(':checked') ? 1 : 0,
        activo_fichas: $('#activo_fichas').is(':checked') ? 1 : 0,
        activo_becas: $('#activo_becas').is(':checked') ? 1 : 0,
        activo: 1
    };

    // Para edición, usar periodo_id (lo que espera el backend)
    if (isEditing) {
        datos.periodo_id = $('#periodo_id').val();
    }
    
    $.ajax({
        url: isEditing ? '<?= base_url('admin-bienestar/actualizar-periodo') ?>' : '<?= base_url('admin-bienestar/crear-periodo') ?>',
        type: 'POST',
        data: JSON.stringify(datos),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                modalPeriodo.hide();
                Swal.fire('Éxito', response.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    });
}

function toggleConfiguracion(periodoId, campo, valor) {
    $.ajax({
        url: '<?= base_url('admin-bienestar/toggleConfiguracionPeriodo') ?>',
        type: 'POST',
        data: JSON.stringify({
            periodo_id: periodoId,
            campo: campo,
            valor: valor ? 1 : 0
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar toast de éxito
                const label = campo === 'activo_fichas' ? 'Fichas' : 'Becas';
                const estado = valor ? 'activadas' : 'desactivadas';
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `${label} ${estado} exitosamente`,
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                // Revertir el checkbox si falla
                $(`#${campo === 'activo_fichas' ? 'fichas' : 'becas'}_${periodoId}`).prop('checked', !valor);
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function() {
            // Revertir el checkbox si falla
            $(`#${campo === 'activo_fichas' ? 'fichas' : 'becas'}_${periodoId}`).prop('checked', !valor);
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

function eliminarPeriodo(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin-bienestar/eliminar-periodo') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }
    });
}

function actualizarContadores() {
    Swal.fire({
        title: 'Actualizando contadores...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url('admin-bienestar/actualizar-contadores-periodos') ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

function exportarPeriodos(tipo) {
    Swal.fire({
        title: 'Exportando...',
        text: 'Preparando archivo de exportación',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear enlace temporal para descarga
    const url = '<?= base_url('admin-bienestar/exportar-periodos') ?>?tipo=' + tipo;
    const link = document.createElement('a');
    link.href = url;
    link.download = 'periodos_academicos.' + (tipo === 'excel' ? 'csv' : 'pdf');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    Swal.fire('Éxito', 'Archivo exportado exitosamente', 'success');
}
</script>
<?= $this->endSection() ?> 