<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Encabezado de la página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-graduation-cap mr-2"></i>Gestión de Becas
        </h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrearBeca">
            <i class="fas fa-plus mr-1"></i>Nueva Beca
        </button>
</div>

    <!-- Tarjetas de estadísticas -->
<div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Becas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBecas">0</div>
                    </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Becas Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="becasActivas">0</div>
                    </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Solicitudes Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="solicitudesPendientes">0</div>
                    </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Becas por Período
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="becasPeriodo">0</div>
                    </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
            <div class="card-body">
            <div class="row">
                    <div class="col-md-3">
                    <label for="filtroEstado">Estado</label>
                    <select class="form-control" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="Activa">Activa</option>
                        <option value="Inactiva">Inactiva</option>
                        <option value="Cerrada">Cerrada</option>
                    </select>
                    </div>
                <div class="col-md-3">
                    <label for="filtroTipo">Tipo de Beca</label>
                    <select class="form-control" id="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <option value="Académica">Académica</option>
                        <option value="Económica">Económica</option>
                            <option value="Deportiva">Deportiva</option>
                            <option value="Cultural">Cultural</option>
                        <option value="Investigación">Investigación</option>
                        <option value="Otros">Otros</option>
                        </select>
                    </div>
                <div class="col-md-3">
                    <label for="filtroPeriodo">Período Académico</label>
                    <select class="form-control" id="filtroPeriodo">
                        <option value="">Todos los períodos</option>
                        </select>
                    </div>
                <div class="col-md-3">
                    <label for="filtroBuscar">Buscar</label>
                    <input type="text" class="form-control" id="filtroBuscar" placeholder="Nombre de beca...">
                </div>
                    </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary" id="btnAplicarFiltros">
                        <i class="fas fa-search mr-1"></i>Aplicar Filtros
                        </button>
                    <button class="btn btn-secondary ml-2" id="btnLimpiarFiltros">
                        <i class="fas fa-times mr-1"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
    </div>

    <!-- Tabla de becas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Becas</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Acciones:</div>
                    <a class="dropdown-item" href="#" id="btnExportarPDF">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar a PDF
                    </a>
                    <a class="dropdown-item" href="#" id="btnExportarExcel">
                        <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
                    </a>
        </div>
    </div>
</div>
    <div class="card-body">
        <div class="table-responsive">
                <table class="table table-bordered" id="tablaBecas" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Monto</th>
                            <th>Cupos</th>
                        <th>Estado</th>
                            <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                    <tbody id="tbodyBecas">
                    <?php if (!empty($becas)): ?>
                        <?php foreach ($becas as $beca): ?>
                        <tr>
                            <td><?= $beca['id'] ?></td>
                            <td><?= esc($beca['nombre']) ?></td>
                            <td><span class="badge badge-info"><?= esc($beca['tipo_beca']) ?></span></td>
                            <td><?= esc($beca['periodo_vigente_id'] ?? 'No asignado') ?></td>
                            <td><?= $beca['monto_beca'] ? '$' . number_format($beca['monto_beca'], 2) : 'No especificado' ?></td>
                            <td><?= $beca['cupos_disponibles'] ?? 'No especificado' ?></td>
                            <td><span class="badge badge-<?= $beca['estado'] == 'Activa' ? 'success' : ($beca['estado'] == 'Inactiva' ? 'secondary' : 'danger') ?>"><?= esc($beca['estado']) ?></span></td>
                            <td><?= $beca['fecha_creacion'] ?? '' ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="verBeca(<?= $beca['id'] ?>)" title="Ver detalles"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" onclick="editarBeca(<?= $beca['id'] ?>)" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-warning" onclick="cambiarEstadoBeca(<?= $beca['id'] ?>)" title="Cambiar estado"><i class="fas fa-toggle-on"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarBeca(<?= $beca['id'] ?>)" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No se encontraron becas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <small class="text-muted" id="infoPaginacionBecas">
                        Mostrando <?= (($current_page ?? 1) - 1) * ($per_page ?? 15) + 1 ?> a 
                        <?= min(($current_page ?? 1) * ($per_page ?? 15), $total ?? 0) ?> 
                        de <?= $total ?? 0 ?> registros
                    </small>
                </div>
                <div class="col-md-6">
                    <nav id="paginacionBecas">
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item <?= ($current_page ?? 1) <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, ($current_page ?? 1) - 1) ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i >= ($current_page ?? 1) - 2 && $i <= ($current_page ?? 1) + 2): ?>
                                <li class="page-item <?= $i == ($current_page ?? 1) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <li class="page-item <?= ($current_page ?? 1) >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($total_pages, ($current_page ?? 1) + 1) ?>">Siguiente</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar beca -->
<div class="modal fade" id="modalCrearBeca" tabindex="-1" role="dialog" aria-labelledby="modalCrearBecaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearBecaLabel">Nueva Beca</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBeca">
                <div class="modal-body">
                    <input type="hidden" id="idBeca" name="id">
                    
                <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombreBeca">Nombre de la Beca *</label>
                                <input type="text" class="form-control" id="nombreBeca" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipoBeca">Tipo de Beca *</label>
                                <select class="form-control" id="tipoBeca" name="tipo_beca" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="Académica">Académica</option>
                                    <option value="Económica">Económica</option>
                                    <option value="Deportiva">Deportiva</option>
                                    <option value="Cultural">Cultural</option>
                                    <option value="Investigación">Investigación</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcionBeca">Descripción</label>
                        <textarea class="form-control" id="descripcionBeca" name="descripcion" rows="3"></textarea>
                            </div>

                    <div class="form-group">
                        <label for="requisitosBeca">Requisitos</label>
                        <textarea class="form-control" id="requisitosBeca" name="requisitos" rows="3"></textarea>
                        </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="puntajeMinimo">Puntaje Mínimo</label>
                                <input type="number" class="form-control" id="puntajeMinimo" name="puntaje_minimo_requerido" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="montoBeca">Monto de la Beca</label>
                                <input type="number" class="form-control" id="montoBeca" name="monto_beca" step="0.01" min="0">
                    </div>
                </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cuposDisponibles">Cupos Disponibles</label>
                                <input type="number" class="form-control" id="cuposDisponibles" name="cupos_disponibles" min="1">
        </div>
    </div>
</div>

                <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="periodoVigente">Período Académico</label>
                                <select class="form-control" id="periodoVigente" name="periodo_vigente_id">
                                    <option value="">Seleccionar período</option>
                                </select>
                            </div>
                    </div>
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="estadoBeca">Estado</label>
                                <select class="form-control" id="estadoBeca" name="estado">
                                    <option value="Activa">Activa</option>
                                    <option value="Inactiva">Inactiva</option>
                                    <option value="Cerrada">Cerrada</option>
                                </select>
                            </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="fechaInicio">Fecha de Inicio de Vigencia</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio_vigencia">
                        </div>
                    </div>
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="fechaFin">Fecha de Fin de Vigencia</label>
                                <input type="date" class="form-control" id="fechaFin" name="fecha_fin_vigencia">
                        </div>
                    </div>
                </div>
                
                    <!-- Sección de documentos requisitos -->
                    <div class="form-group">
                        <label>Documentos Requisitos</label>
                        <div id="documentosRequisitos">
                            <div class="documento-requisito mb-2">
                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="documentos_requisitos[0][nombre]" placeholder="Nombre del documento" required>
                                </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="documentos_requisitos[0][descripcion]" placeholder="Descripción">
                                </div>
                                    <div class="col-md-2">
                                        <select class="form-control" name="documentos_requisitos[0][tipo]">
                                            <option value="PDF">PDF</option>
                                            <option value="Imagen">Imagen</option>
                                            <option value="Documento">Documento</option>
                                        </select>
                                </div>
                                    <div class="col-md-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="obligatorio0" name="documentos_requisitos[0][obligatorio]" value="1" checked>
                                            <label class="custom-control-label" for="obligatorio0">Obligatorio</label>
            </div>
        </div>
    </div>
</div>
            </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarDocumento">
                            <i class="fas fa-plus mr-1"></i>Agregar Documento
                        </button>
                            </div>
                        </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarBeca">
                        <i class="fas fa-save mr-1"></i>Guardar Beca
                    </button>
                    </div>
                </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar esta beca? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminacion">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" role="dialog" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambiarEstadoLabel">Cambiar Estado de Beca</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nuevoEstado">Nuevo Estado</label>
                    <select class="form-control" id="nuevoEstado">
                        <option value="Activa">Activa</option>
                        <option value="Inactiva">Inactiva</option>
                        <option value="Cerrada">Cerrada</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarCambioEstado">
                    <i class="fas fa-check mr-1"></i>Confirmar Cambio
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let becaSeleccionada = null;
let contadorDocumentos = 1;

$(document).ready(function() {
    cargarBecas();
    cargarPeriodosAcademicos();
    cargarEstadisticas();

    // Eventos de filtros
    $('#btnAplicarFiltros').click(function() {
        cargarBecas();
    });

    $('#btnLimpiarFiltros').click(function() {
        limpiarFiltros();
        cargarBecas();
    });

    // Evento del formulario
    $('#formBeca').submit(function(e) {
        e.preventDefault();
        guardarBeca();
    });

    // Evento para agregar documento requisito
    $('#btnAgregarDocumento').click(function() {
        agregarDocumentoRequisito();
    });

    // Eventos de modales
    $('#modalCrearBeca').on('hidden.bs.modal', function() {
        limpiarFormularioBeca();
    });
});

let paginaActualBecas = 1;

function cargarBecas(pagina) {
    pagina = pagina || paginaActualBecas;
    paginaActualBecas = pagina;

    const filtros = {
        page: pagina,
        estado: $('#filtroEstado').val(),
        tipo: $('#filtroTipo').val(),
        periodo: $('#filtroPeriodo').val(),
        buscar: $('#filtroBuscar').val()
    };

    $.ajax({
        url: '<?= base_url('admin-bienestar/obtener-becas') ?>',
        type: 'GET',
        data: filtros,
        success: function(response) {
            if (response.success) {
                mostrarBecas(response.data);
                mostrarPaginacionBecas(response.pagination);
            } else {
                mostrarError('Error al cargar las becas: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al cargar las becas');
        }
    });
}

function mostrarPaginacionBecas(pagination) {
    if (!pagination || pagination.total_pages <= 1) {
        $('#paginacionBecas').empty();
        $('#infoPaginacionBecas').text('Mostrando ' + pagination.total + ' registros');
        return;
    }

    const desde = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const hasta = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    $('#infoPaginacionBecas').text('Mostrando ' + desde + ' a ' + hasta + ' de ' + pagination.total + ' registros');

    let html = '<ul class="pagination justify-content-end mb-0">';
    html += '<li class="page-item ' + (pagination.current_page <= 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="cargarBecas(' + (pagination.current_page - 1) + ')">Anterior</a></li>';

    for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.total_pages, pagination.current_page + 2); i++) {
        html += '<li class="page-item ' + (i == pagination.current_page ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="cargarBecas(' + i + ')">' + i + '</a></li>';
    }

    html += '<li class="page-item ' + (pagination.current_page >= pagination.total_pages ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="cargarBecas(' + (pagination.current_page + 1) + ')">Siguiente</a></li>';
    html += '</ul>';

    $('#paginacionBecas').html(html);
}

function mostrarBecas(becas) {
    const tbody = $('#tbodyBecas');
    tbody.empty();

    if (becas.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">No se encontraron becas</td></tr>');
        return;
    }

    becas.forEach(function(beca) {
        const tr = `
            <tr>
                <td>${beca.id}</td>
                <td>${beca.nombre}</td>
                <td><span class="badge badge-info">${beca.tipo_beca}</span></td>
                <td>${beca.periodo_nombre || 'No asignado'}</td>
                <td>${beca.monto_beca ? '$' + beca.monto_beca : 'No especificado'}</td>
                <td>${beca.cupos_disponibles || 'No especificado'}</td>
                <td>
                    <span class="badge badge-${getBadgeClass(beca.estado)}">${beca.estado}</span>
                </td>
                <td>${formatearFecha(beca.fecha_creacion)}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info" onclick="verBeca(${beca.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="editarBeca(${beca.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="cambiarEstadoBeca(${beca.id})" title="Cambiar estado">
                            <i class="fas fa-toggle-on"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarBeca(${beca.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
}

function getBadgeClass(estado) {
    switch (estado) {
        case 'Activa': return 'success';
        case 'Inactiva': return 'secondary';
        case 'Cerrada': return 'danger';
        default: return 'secondary';
    }
}

function formatearFecha(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-ES');
}

function cargarPeriodosAcademicos() {
    $.ajax({
        url: '<?= base_url('admin-bienestar/obtener-periodos-academicos') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#periodoVigente');
                select.empty().append('<option value="">Seleccionar período</option>');
                
                response.data.forEach(function(periodo) {
                    select.append(`<option value="${periodo.id}">${periodo.nombre}</option>`);
                });
            }
        }
    });
}

function cargarEstadisticas() {
    $.ajax({
        url: '<?= base_url('admin-bienestar/obtener-estadisticas-becas') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalBecas').text(response.data.total_becas);
                $('#becasActivas').text(response.data.becas_por_tipo.find(b => b.tipo_beca === 'Activa')?.total || 0);
                $('#solicitudesPendientes').text(response.data.solicitudes_por_estado.find(s => s.estado === 'Postulada')?.total || 0);
                $('#becasPeriodo').text(response.data.becas_por_tipo.length);
            }
        }
    });
}

function guardarBeca() {
    const formData = new FormData($('#formBeca')[0]);
    
    $.ajax({
        url: '<?= base_url('admin-bienestar/crear-beca') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                mostrarExito('Beca guardada exitosamente');
                $('#modalCrearBeca').modal('hide');
                cargarBecas();
                cargarEstadisticas();
            } else {
                mostrarError('Error al guardar la beca: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al guardar la beca');
        }
    });
}

function editarBeca(id) {
    $.ajax({
        url: `<?= base_url('admin-bienestar/obtener-beca/') ?>${id}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                llenarFormularioBeca(response.data);
                $('#modalCrearBecaLabel').text('Editar Beca');
                $('#modalCrearBeca').modal('show');
            } else {
                mostrarError('Error al obtener la beca: ' + response.message);
            }
        }
    });
}

function llenarFormularioBeca(beca) {
    $('#idBeca').val(beca.id);
    $('#nombreBeca').val(beca.nombre);
    $('#tipoBeca').val(beca.tipo_beca);
    $('#descripcionBeca').val(beca.descripcion);
    $('#requisitosBeca').val(beca.requisitos);
    $('#puntajeMinimo').val(beca.puntaje_minimo_requerido);
    $('#montoBeca').val(beca.monto_beca);
    $('#cuposDisponibles').val(beca.cupos_disponibles);
    $('#periodoVigente').val(beca.periodo_vigente_id);
    $('#estadoBeca').val(beca.estado);
    $('#fechaInicio').val(beca.fecha_inicio_vigencia);
    $('#fechaFin').val(beca.fecha_fin_vigencia);

    // Llenar documentos requisitos
    if (beca.documentos_requisitos) {
        $('#documentosRequisitos').empty();
        beca.documentos_requisitos.forEach(function(doc, index) {
            agregarDocumentoRequisito(doc, index);
        });
        contadorDocumentos = beca.documentos_requisitos.length;
    }
}

function agregarDocumentoRequisito(documento = null, index = null) {
    const indice = index !== null ? index : contadorDocumentos;
    const nombre = documento ? documento.nombre_documento : '';
    const descripcion = documento ? documento.descripcion : '';
    const tipo = documento ? documento.tipo_documento : 'PDF';
    const obligatorio = documento ? documento.obligatorio : 1;

    const html = `
        <div class="documento-requisito mb-2">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="documentos_requisitos[${indice}][nombre]" 
                           placeholder="Nombre del documento" value="${nombre}" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="documentos_requisitos[${indice}][descripcion]" 
                           placeholder="Descripción" value="${descripcion}">
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="documentos_requisitos[${indice}][tipo]">
                        <option value="PDF" ${tipo === 'PDF' ? 'selected' : ''}>PDF</option>
                        <option value="Imagen" ${tipo === 'Imagen' ? 'selected' : ''}>Imagen</option>
                        <option value="Documento" ${tipo === 'Documento' ? 'selected' : ''}>Documento</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" 
                               id="obligatorio${indice}" name="documentos_requisitos[${indice}][obligatorio]" 
                               value="1" ${obligatorio ? 'checked' : ''}>
                        <label class="custom-control-label" for="obligatorio${indice}">Obligatorio</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDocumentoRequisito(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#documentosRequisitos').append(html);
    contadorDocumentos++;
}

function eliminarDocumentoRequisito(btn) {
    $(btn).closest('.documento-requisito').remove();
}

function cambiarEstadoBeca(id) {
    becaSeleccionada = id;
    $('#modalCambiarEstado').modal('show');
}

function confirmarCambioEstado() {
    const nuevoEstado = $('#nuevoEstado').val();
    
    $.ajax({
        url: '<?= base_url('admin-bienestar/activar-desactivar-beca') ?>',
        type: 'POST',
        data: {
            id: becaSeleccionada,
            estado: nuevoEstado
        },
        success: function(response) {
            if (response.success) {
                mostrarExito('Estado de beca actualizado exitosamente');
                $('#modalCambiarEstado').modal('hide');
                cargarBecas();
                cargarEstadisticas();
            } else {
                mostrarError('Error al actualizar el estado: ' + response.message);
            }
        }
    });
}

function eliminarBeca(id) {
    becaSeleccionada = id;
    $('#modalConfirmarEliminacion').modal('show');
}

function confirmarEliminacion() {
    $.ajax({
        url: '<?= base_url('admin-bienestar/eliminar-beca') ?>',
        type: 'POST',
        data: { id: becaSeleccionada },
        success: function(response) {
            if (response.success) {
                mostrarExito('Beca eliminada exitosamente');
                $('#modalConfirmarEliminacion').modal('hide');
                cargarBecas();
                cargarEstadisticas();
            } else {
                mostrarError('Error al eliminar la beca: ' + response.message);
            }
        }
    });
}

function verBeca(id) {
    // Redirigir a la vista de detalles de la beca
    window.location.href = `<?= base_url('admin-bienestar/ver-beca/') ?>${id}`;
}

function limpiarFiltros() {
    $('#filtroEstado').val('');
    $('#filtroTipo').val('');
    $('#filtroPeriodo').val('');
    $('#filtroBuscar').val('');
}

function limpiarFormularioBeca() {
    $('#formBeca')[0].reset();
    $('#idBeca').val('');
    $('#documentosRequisitos').empty();
    contadorDocumentos = 1;
    agregarDocumentoRequisito();
    $('#modalCrearBecaLabel').text('Nueva Beca');
}

// Eventos de modales
$('#btnConfirmarCambioEstado').click(confirmarCambioEstado);
$('#btnConfirmarEliminacion').click(confirmarEliminacion);

// Funciones de utilidad
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}
</script> 
<?= $this->endSection() ?> 