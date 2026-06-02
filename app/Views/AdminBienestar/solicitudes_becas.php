<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gesti&oacute;n de Solicitudes de Becas</h4>
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

<!-- Gr&aacute;ficos de Estad&iacute;sticas -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Estad&iacute;sticas de Solicitudes</h5>
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
                <h5 class="card-title mb-0">Distribuci&oacute;n por Estado</h5>
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
                        <h3 class="mb-0"><?= number_format($estadisticas['total'] ?? 0) ?></h3>
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
                        <h3 class="mb-0"><?= number_format($estadisticas['aprobadas'] ?? 0) ?></h3>
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
                        <h3 class="mb-0"><?= number_format($estadisticas['pendientes'] ?? 0) ?></h3>
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
                        <h3 class="mb-0"><?= number_format($estadisticas['rechazadas'] ?? 0) ?></h3>
                    </div>
                    <div class="icon-shape icon-lg bg-soft-danger text-danger rounded-3">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-funnel me-2"></i>Filtros</h5>
    </div>
    <div class="card-body">
        <form id="formFiltros" method="GET" action="<?= current_url() ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtroEstudiante" class="form-label">Estudiante</label>
                    <input type="text" class="form-control" id="filtroEstudiante" name="busqueda" value="<?= $filtros['busqueda'] ?? '' ?>" placeholder="Nombre, apellido o c&eacute;dula">
                </div>
                <div class="col-md-2">
                    <label for="filtroBeca" class="form-label">Beca</label>
                    <select class="form-select" id="filtroBeca" name="beca_id">
                        <option value="">Todas</option>
                        <?php foreach ($becas as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (isset($filtros['beca_id']) && $filtros['beca_id'] == $b['id']) ? 'selected' : '' ?>><?= $b['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroTipo" class="form-label">Tipo</label>
                    <select class="form-select" id="filtroTipo" name="tipo_beca">
                        <option value="">Todos</option>
                        <?php foreach ($tiposBecas as $tipo): ?>
                            <option value="<?= $tipo ?>" <?= (isset($filtros['tipo_beca']) && $filtros['tipo_beca'] == $tipo) ? 'selected' : '' ?>><?= $tipo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" name="estado">
                        <option value="">Todos</option>
                        <option value="Pendiente" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Revisi&oacute;n" <?= (isset($filtros['estado']) && $filtros['estado'] == 'En Revisión') ? 'selected' : '' ?>>En Revisi&oacute;n</option>
                        <option value="Aprobada" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Aprobada') ? 'selected' : '' ?>>Aprobada</option>
                        <option value="Rechazada" <?= (isset($filtros['estado']) && $filtros['estado'] == 'Rechazada') ? 'selected' : '' ?>>Rechazada</option>
                        <option value="En espera" <?= (isset($filtros['estado']) && $filtros['estado'] == 'En espera') ? 'selected' : '' ?>>En espera</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroPeriodo" class="form-label">Per&iacute;odo</label>
                    <select class="form-select" id="filtroPeriodo" name="periodo_id">
                        <option value="">Todos</option>
                        <?php foreach ($periodos as $periodo): ?>
                            <option value="<?= $periodo['id'] ?>" <?= (isset($filtros['periodo_id']) && $filtros['periodo_id'] == $periodo['id']) ? 'selected' : '' ?>><?= $periodo['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Solicitudes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-check me-2"></i>Solicitudes de Becas
        </h5>
        <div class="input-group input-group-sm" style="width: 250px;">
            <input type="text" class="form-control" id="busquedaRapida" placeholder="Buscar en tabla...">
            <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
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
                        <th>Per&iacute;odo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($solicitudes)): ?>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <?= strtoupper(substr($s['nombre'] ?? '?', 0, 1) . substr($s['apellido'] ?? '?', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= ($s['nombre'] ?? '') . ' ' . ($s['apellido'] ?? '') ?></h6>
                                            <small class="text-muted"><?= $s['cedula'] ?? '' ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $s['beca_nombre'] ?? $s['nombre_beca'] ?? 'N/A' ?></td>
                                <td><span class="badge bg-soft-info text-info"><?= $s['tipo_beca'] ?? 'N/A' ?></span></td>
                                <td><?= $s['periodo_nombre'] ?? $s['periodo_academico'] ?? 'N/A' ?></td>
                                <td class="text-center">
                                    <?php
                                        $estadoRaw = $s['estado'] ?? '';
                                        $estadoDisplay = $estadoRaw ?: 'Sin estado';
                                        $estadoClass = [
                                            'Pendiente' => 'warning',
                                            'En Revisión' => 'info',
                                            'Aprobada' => 'success',
                                            'Rechazada' => 'danger',
                                            'En espera' => 'secondary',
                                            'Postulada' => 'primary',
                                            'Lista de Espera' => 'secondary'
                                        ][$estadoRaw] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-soft-<?= $estadoClass ?> text-<?= $estadoClass ?>">
                                        <?= $estadoDisplay ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($s['fecha_solicitud'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" title="Ver detalles" onclick="verSolicitud(<?= $s['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if ($s['estado'] == 'Pendiente' || $s['estado'] == 'En Revisión'): ?>
                                            <button type="button" class="btn btn-outline-success" title="Aprobar" onclick="cambiarEstado(<?= $s['id'] ?>, 'Aprobada')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Rechazar" onclick="mostrarModalRechazo(<?= $s['id'] ?>)">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($s['estado'] == 'Aprobada'): ?>
                                            <button type="button" class="btn btn-outline-secondary" title="Generar constancia" onclick="generarConstancia(<?= $s['id'] ?>)">
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

        <!-- Paginaci&oacute;n -->
        <?php if (!empty($pager['total'])): 
            $queryFiltros = array_filter($filtros, fn($v, $k) => ($v !== '' && $v !== null && $k !== 'page' && $k !== 'per_page'), ARRAY_FILTER_USE_BOTH);
            $queryString = http_build_query($queryFiltros);
            $queryString = $queryString ? '&' . $queryString : '';
        ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando <span class="fw-semibold"><?= (($pager['currentPage'] - 1) * $pager['perPage']) + 1 ?></span> a
                <span class="fw-semibold"><?= min($pager['currentPage'] * $pager['perPage'], $pager['total']) ?></span> de
                <span class="fw-semibold"><?= number_format($pager['total']) ?></span> registros
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($pager['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $pager['currentPage'] - 1 ?><?= $queryString ?>">&laquo;</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pager['totalPages']; $i++): ?>
                        <li class="page-item <?= $i == $pager['currentPage'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($pager['currentPage'] < $pager['totalPages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $pager['currentPage'] + 1 ?><?= $queryString ?>">&raquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.card-header .btn-outline-primary {
    color: white;
    border-color: rgba(255,255,255,0.5);
}
.card-header .btn-outline-primary:hover {
    background: rgba(255,255,255,0.2);
    color: white;
}
.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
}
</style>

<script>
const estGral = <?= json_encode($estadisticas ?? ['total' => 0, 'aprobadas' => 0, 'pendientes' => 0, 'rechazadas' => 0]) ?>;

$(document).ready(function() {
    inicializarGraficos(estGral);

    $('#busquedaRapida').on('keyup', function() {
        const val = this.value.toLowerCase();
        $('#tablaSolicitudes tbody tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(val));
        });
    });
});

function inicializarGraficos(est) {
    const ctx1 = document.getElementById('chartGeneral');
    const ctx2 = document.getElementById('chartEstados');
    if (!ctx1 || !ctx2) return;

    if (window._chartGral) window._chartGral.destroy();
    if (window._chartEst) window._chartEst.destroy();

    window._chartGral = new Chart(ctx1.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Aprobadas', 'Pendientes', 'Rechazadas'],
            datasets: [{
                label: 'Cantidad',
                data: [est.aprobadas ?? 0, est.pendientes ?? 0, est.rechazadas ?? 0],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    window._chartEst = new Chart(ctx2.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Aprobadas', 'Pendientes', 'Rechazadas'],
            datasets: [{
                data: [est.aprobadas ?? 0, est.pendientes ?? 0, est.rechazadas ?? 0],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
}

function cambiarEstado(id, estado) {
    Swal.fire({
        title: 'Cambiar estado',
        text: '&iquest;Est&aacute; seguro?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'S&iacute;',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('index.php/admin-bienestar/cambiar-estado-solicitud') ?>',
            type: 'POST',
            data: { id, estado },
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Listo', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            }
        });
    });
}

function mostrarModalRechazo(id) {
    Swal.fire({
        title: 'Rechazar solicitud',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo',
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        confirmButtonColor: '#dc3545',
        preConfirm: motivo => {
            if (!motivo) { Swal.showValidationMessage('Debe indicar un motivo'); return false; }
            return motivo;
        }
    }).then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('index.php/admin-bienestar/cambiar-estado-solicitud') ?>',
            type: 'POST',
            data: { id, estado: 'Rechazada', motivo: r.value },
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Rechazada', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            }
        });
    });
}

function verSolicitud(id) {
    window.location.href = '<?= base_url('index.php/admin-bienestar/solicitudes-becas') ?>?detalle=' + id;
}

function generarConstancia(id) {
    window.open('<?= base_url('index.php/admin-bienestar/generar-constancia') ?>?id=' + id, '_blank');
}

function exportarSolicitudes(f) {
    Swal.fire({ icon: 'info', title: 'Exportaci&oacute;n', text: 'La exportaci&oacute;n a ' + f.toUpperCase() + ' estar&aacute; disponible pr&oacute;ximamente' });
}

function exportarGrafico(canvasId, nombre) {
    const link = document.createElement('a');
    link.download = nombre + '.png';
    link.href = document.getElementById(canvasId).toDataURL('image/png');
    link.click();
}
</script>

<?= $this->endSection() ?>
