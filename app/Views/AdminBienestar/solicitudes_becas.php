<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Solicitudes de Becas</h4>
                <p class="text-muted mb-0">Revisa y gestiona las solicitudes de becas de los estudiantes</p>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" onclick="exportarSolicitudes('excel')">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="exportarSolicitudes('pdf')">
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
                <h5 class="card-title mb-0">Estadísticas de Solicitudes</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportarGrafico('chartGeneral', 'Estadisticas_Solicitudes')">
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

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-primary border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Total Solicitudes</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['total']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3">
                        <i class="bi bi-file-earmark-text"></i>
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
                        <h6 class="text-uppercase text-muted mb-1">Aprobadas</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['aprobadas']) ?></h3>
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
                        <h6 class="text-uppercase text-muted mb-1">Pendientes</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['pendientes']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-warning text-warning rounded-3">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-start border-danger border-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Rechazadas</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['rechazadas']) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-danger text-danger rounded-3">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtroTipo" class="font-weight-bold text-dark">
                            <i class="fas fa-tag mr-1"></i>Tipo
                        </label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($tiposBecas as $tipo): ?>
                                <option value="<?= $tipo ?>"><?= $tipo ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtroEstado" class="font-weight-bold text-dark">
                            <i class="fas fa-toggle-on mr-1"></i>Estado
                        </label>
                        <select class="form-control" id="filtroEstado">
                            <option value="">Todos los estados</option>
                        <option value="Pendiente" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Revisión" <?= (isset($filtros['estado']) && $filtros['estado'] == 'En Revisión') ? 'selected' : '' ?>>En Revisión</option>
                        <option value="Aprobada" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Aprobada') ? 'selected' : '' ?>>Aprobada</option>
                        <option value="Rechazada" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Rechazada') ? 'selected' : '' ?>>Rechazada</option>
                        <option value="En espera" <?= (isset($filtros['estado']) && $filtros['estado'] == 'En espera') ? 'selected' : '' ?>>En espera</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtroPeriodo" class="font-weight-bold text-dark">
                            <i class="fas fa-calendar mr-1"></i>Período
                        </label>
                        <select class="form-control" id="filtroPeriodo">
                            <option value="">Todos los períodos</option>
                            <?php foreach ($periodos as $periodo): ?>
                                <option value="<?= $periodo['id'] ?>" <?= (isset($filtros['periodo_id']) && $filtros['periodo_id'] == $periodo['id']) ? 'selected' : '' ?>>
                                    <?= $periodo['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" onclick="aplicarFiltros()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                        <i class="fas fa-times mr-1"></i>Limpiar Filtros
                    </button>
                    <span class="ml-2 text-muted" id="filtrosActivos"></span>
                </div>
            </div>
        </div>
    </div>

<!-- Tabla de Solicitudes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-check me-2"></i>Solicitudes de Becas
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
            <table class="table table-hover table-sm" id="tablaSolicitudes">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Estudiante</th>
                        <th>Beca</th>
                        <th>Tipo</th>
                        <th>Período</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodySolicitudes">
                    <?php if (!empty($solicitudes)): ?>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?= $solicitud['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <?= strtoupper(substr($solicitud['nombre'], 0, 1) . substr($solicitud['apellido'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= $solicitud['nombre'] . ' ' . $solicitud['apellido'] ?></h6>
                                            <small class="text-muted"><?= $solicitud['cedula'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $solicitud['nombre_beca'] ?? 'N/A' ?></td>
                                <td>
                                    <span class="badge bg-soft-info text-info">
                                        <?= $solicitud['tipo_beca'] ?? 'N/A' ?>
                                    </span>
                                </td>
                                <td><?= $solicitud['periodo_academico'] ?? 'N/A' ?></td>
                                <td class="text-center">
                                    <?php 
                                    $estadoClass = [
                                        'Pendiente' => 'warning',
                                        'En Revisión' => 'info',
                                        'Aprobada' => 'success',
                                        'Rechazada' => 'danger',
                                        'En espera' => 'secondary'
                                    ][$solicitud['estado']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-soft-<?= $estadoClass ?> text-<?= $estadoClass ?>">
                                        <?= $solicitud['estado'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" title="Ver detalles" onclick="verSolicitud(<?= $solicitud['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if ($solicitud['estado'] == 'Pendiente' || $solicitud['estado'] == 'En Revisión'): ?>
                                            <button type="button" class="btn btn-outline-success" title="Aprobar" onclick="cambiarEstado(<?= $solicitud['id'] ?>, 'Aprobada')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Rechazar" onclick="mostrarModalRechazo(<?= $solicitud['id'] ?>)">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($solicitud['estado'] == 'Aprobada'): ?>
                                            <button type="button" class="btn btn-outline-secondary" title="Generar constancia" onclick="generarConstancia(<?= $solicitud['id'] ?>)">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    No se encontraron solicitudes de becas
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <?php if ($pager['total'] > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <span class="fw-semibold"><?= (($pager['currentPage'] - 1) * $pager['perPage']) + 1 ?></span> a 
                    <span class="fw-semibold"><?= min($pager['currentPage'] * $pager['perPage'], $pager['total']) ?></span> de 
                    <span class="fw-semibold"><?= number_format($pager['total']) ?></span> registros
                </div>
                <nav aria-label="Paginación de solicitudes">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($pager['currentPage'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pager['currentPage'] - 1 ?><?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pager['totalPages']; $i++): ?>
                            <li class="page-item <?= $i == $pager['currentPage'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pager['currentPage'] < $pager['totalPages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pager['currentPage'] + 1 ?><?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>" aria-label="Siguiente">
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
</div>

<style>
/* CSS para asegurar texto negro en toda la tabla */
#tablaSolicitudes tbody td,
#tablaSolicitudes tbody td *,
#tablaSolicitudes tbody td span,
#tablaSolicitudes tbody td div,
#tablaSolicitudes tbody td strong,
#tablaSolicitudes tbody td .badge,
#tablaSolicitudes tbody td .badge *,
#tablaSolicitudes tbody td .progress,
#tablaSolicitudes tbody td .progress * {
    color: #000 !important;
    font-weight: 700 !important;
}

/* Excepción para badges de estado */
#tablaSolicitudes tbody td .badge.badge-success,
#tablaSolicitudes tbody td .badge.badge-warning,
#tablaSolicitudes tbody td .badge.badge-danger,
#tablaSolicitudes tbody td .badge.badge-info,
#tablaSolicitudes tbody td .badge.badge-secondary {
    color: #fff !important;
}

/* Estilos para filtros */
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Animaciones */
.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .card-body .row > div {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Variables globales
let solicitudesData = [];
let solicitudesFiltradas = [];
let paginaActual = 1;
const solicitudesPorPagina = 10;
let filtrosAplicados = {};

$(document).ready(function() {
    const table = $('#tablaSolicitudes').DataTable({
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "responsive": true,
        "order": [[0, "desc"]],
        "pageLength": 10,
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "initComplete": function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    function aplicarFiltros() {
        var filtroEstudiante = $('#filtroEstudiante').val().toLowerCase();
        var filtroBeca = $('#filtroBeca').val();
        var filtroTipo = $('#filtroTipo').val();
        var filtroEstado = $('#filtroEstado').val();
        var filtroPeriodo = $('#filtroPeriodo').val();
        
        var params = new URLSearchParams();
        if (filtroEstudiante) params.set('estudiante', filtroEstudiante);
        if (filtroBeca) params.set('beca', filtroBeca);
        if (filtroTipo) params.set('tipo', filtroTipo);
        if (filtroEstado) params.set('estado', filtroEstado);
        if (filtroPeriodo) params.set('periodo', filtroPeriodo);
        
        window.location.href = '?' + params.toString();
    }

    $('#filtroEstudiante').on('keyup', function(e) {
        if (e.key === 'Enter') aplicarFiltros();
    });
    
    $('#filtroBeca, #filtroTipo, #filtroEstado, #filtroPeriodo').on('change', aplicarFiltros);
    
    $('#busquedaRapida').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    $('#btnBuscar').on('click', function() {
        table.search($('#busquedaRapida').val()).draw();
    });
    
    $('#btnLimpiarFiltros').on('click', function() {
        window.location.href = '<?= current_url() ?>';
    });
});

// Cargar solicitudes desde el servidor
function cargarSolicitudes() {
    console.log('Cargando solicitudes...');
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor espere mientras se cargan las solicitudes',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '<?= base_url('index.php/admin-bienestar/obtener-solicitudes-becas') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta solicitudes:', response);
            Swal.close();
            
            if (response.success) {
                // Actualizar la tabla con los nuevos datos
                actualizarTablaSolicitudes(response.data);
                // Actualizar estadísticas
                actualizarEstadisticas(response.estadisticas);
                // Inicializar gráficos
                inicializarGraficos(response.estadisticas);
            } else {
                console.error('Error al cargar solicitudes:', response.message);
                mostrarError('Error al cargar solicitudes: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX solicitudes:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
            Swal.close();
            mostrarError('Error de conexión al cargar las solicitudes');
        }
    });
}

// Cargar becas para filtro
function cargarBecas() {
    $.ajax({
        url: '<?= base_url('index.php/admin-bienestar/obtener-becas') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#filtroBeca');
                select.empty().append('<option value="">Todas las becas</option>');
                
                response.data.forEach(function(beca) {
                    const option = `<option value="${beca.id}">${beca.nombre}</option>`;
                    select.append(option);
                });
            }
        },
        error: function() {
            console.error('Error de conexión al cargar becas');
        }
    });
}

// Cargar períodos para filtro
function cargarPeriodos() {
    $.ajax({
        url: '<?= base_url('index.php/admin-bienestar/obtener-periodos-academicos') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#filtroPeriodo');
                select.empty().append('<option value="">Todos los períodos</option>');
                
                response.data.forEach(function(periodo) {
                    const option = `<option value="${periodo.id}">${periodo.nombre}</option>`;
                    select.append(option);
                });
            }
        },
        error: function() {
            console.error('Error de conexión al cargar períodos');
        }
    });
}

function aplicarFiltros() {
    const params = new URLSearchParams();
    const estudiante = $('#filtroEstudiante').val();
    const beca = $('#filtroBeca').val();
    const tipo = $('#filtroTipo').val();
    const estado = $('#filtroEstado').val();
    const periodo = $('#filtroPeriodo').val();
    if (estudiante) params.set('busqueda', estudiante);
    if (beca) params.set('beca_id', beca);
    if (tipo) params.set('tipo_beca', tipo);
    if (estado) params.set('estado', estado);
    if (periodo) params.set('periodo_id', periodo);
    window.location.href = '?' + params.toString();
}

function limpiarFiltros() {
    window.location.href = '<?= current_url() ?>';
}

// Formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Formatear hora
function formatearHora(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    return d.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Obtener clase de badge según estado
function getBadgeClass(estado) {
    switch (estado) {
        case 'Aprobada': return 'success';
        case 'En Revisión': return 'warning';
        case 'Rechazada': return 'danger';
        case 'Postulada': return 'info';
        case 'Lista de Espera': return 'secondary';
        default: return 'secondary';
    }
}

// Obtener icono según estado
function getEstadoIcon(estado) {
    switch (estado) {
        case 'Aprobada': return 'check-circle';
        case 'En Revisión': return 'clock';
        case 'Rechazada': return 'times-circle';
        case 'Postulada': return 'file-alt';
        case 'Lista de Espera': return 'hourglass-half';
        default: return 'question-circle';
    }
}

// Obtener color de progreso
function getProgressColor(porcentaje) {
    if (porcentaje >= 80) return 'success';
    if (porcentaje >= 50) return 'warning';
    return 'danger';
}

// Cargar estadísticas
function cargarEstadisticas() {
    console.log('Cargando estadísticas de solicitudes...');
    console.log('solicitudesData disponible:', typeof solicitudesData !== 'undefined' ? solicitudesData.length : 'undefined');
    
    if (typeof solicitudesData === 'undefined' || !solicitudesData) {
        console.error('solicitudesData no está disponible para estadísticas');
        return;
    }
    
    const totalSolicitudes = solicitudesData.length;
    const solicitudesAprobadas = solicitudesData.filter(s => s.estado === 'Aprobada').length;
    const solicitudesEnRevision = solicitudesData.filter(s => s.estado === 'En Revisión').length;
    const solicitudesRechazadas = solicitudesData.filter(s => s.estado === 'Rechazada').length;
    
    $('#totalSolicitudes').text(totalSolicitudes);
    $('#solicitudesAprobadas').text(solicitudesAprobadas);
    $('#solicitudesEnRevision').text(solicitudesEnRevision);
    $('#solicitudesRechazadas').text(solicitudesRechazadas);
}

// Funciones auxiliares
function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

function verSolicitud(id) {
    // Implementar vista de solicitud
    Swal.fire({
        icon: 'info',
        title: 'Función en desarrollo',
        text: 'La vista detallada de solicitudes estará disponible próximamente'
    });
}

function aprobarSolicitud(id) {
    Swal.fire({
        title: '¿Aprobar solicitud?',
        text: '¿Está seguro de que desea aprobar esta solicitud de beca?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('index.php/admin-bienestar/aprobar-solicitud-beca') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        cargarSolicitudes();
                        cargarEstadisticas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                }
            });
        }
    });
}

function rechazarSolicitud(id) {
    Swal.fire({
        title: '¿Rechazar solicitud?',
        text: '¿Está seguro de que desea rechazar esta solicitud de beca?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('index.php/admin-bienestar/rechazar-solicitud-beca') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        cargarSolicitudes();
                        cargarEstadisticas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                }
            });
        }
    });
}

function exportarSolicitudes(formato) {
    Swal.fire({
        icon: 'info',
        title: 'Función en desarrollo',
        text: `La exportación a ${formato.toUpperCase()} estará disponible próximamente`
    });
}
</script>

<?= $this->endSection() ?>
