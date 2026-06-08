<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">📅 Gestión de Períodos Académicos</h4>
                    <p class="text-muted mb-0">Administra los períodos académicos, sus configuraciones y límites</p>
                </div>
                <div class="page-title-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="mostrarModalNuevoPeriodo()">
                            <i class="bi bi-plus-circle"></i> Nuevo Período
                        </button>
                        <button type="button" class="btn btn-info" onclick="exportarPeriodos()">
                            <i class="bi bi-file-excel"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="actualizarVista()">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">Total Períodos</p>
                            <h5 class="mb-0" id="totalPeriodos"><?= count($periodos ?? []) ?></h5>
                            <p class="text-muted mb-0">
                                <span class="text-success">
                                    <i class="bi bi-arrow-up"></i> 
                                    <?= count(array_filter($periodos ?? [], fn($p) => $p['activo'])) ?> activos
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bi bi-calendar"></i>
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
                            <p class="text-muted fw-medium mb-1">Fichas Activas</p>
                            <h5 class="mb-0" id="fichasActivas">
                                <?= array_sum(array_column($periodos ?? [], 'fichas_creadas')) ?>
                            </h5>
                            <p class="text-muted mb-0">
                                <span class="text-info">
                                    En períodos activos
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
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
                            <p class="text-muted fw-medium mb-1">Becas Asignadas</p>
                            <h5 class="mb-0" id="becasAsignadas">
                                <?= array_sum(array_column($periodos ?? [], 'becas_asignadas')) ?>
                            </h5>
                            <p class="text-muted mb-0">
                                <span class="text-warning">
                                    Total histórico
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
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
                            <p class="text-muted fw-medium mb-1">Utilización</p>
                            <h5 class="mb-0" id="utilizacion">
                                <?php
                                $totalLimites = array_sum(array_column($periodos ?? [], 'limite_fichas'));
                                $totalCreadas = array_sum(array_column($periodos ?? [], 'fichas_creadas'));
                                echo $totalLimites > 0 ? round(($totalCreadas / $totalLimites) * 100, 1) : 0;
                                ?>%
                            </h5>
                            <p class="text-muted mb-0">
                                <span class="text-primary">
                                    Promedio general
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bi bi-graph-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" onchange="aplicarFiltros()">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="filtroFecha" class="form-label">Rango de Fechas</label>
                    <select class="form-select" id="filtroFecha" onchange="aplicarFiltros()">
                        <option value="">Todas las fechas</option>
                        <option value="actual">Período Actual</option>
                        <option value="proximo">Próximos</option>
                        <option value="pasado">Pasados</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="busquedaPeriodo" class="form-label">Buscar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="busquedaPeriodo" 
                               placeholder="Buscar por nombre..." onkeyup="aplicarFiltros()">
                        <button class="btn btn-outline-secondary" type="button" onclick="limpiarFiltros()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Períodos -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Lista de Períodos Académicos
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0" id="tablaPeriodos">
                    <thead class="table-light">
                        <tr>
                            <th>Período</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Límites</th>
                            <th>Uso Actual</th>
                            <th>Configuración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($periodos)): ?>
                            <?php foreach ($periodos as $periodo): ?>
                                <tr data-periodo-id="<?= $periodo['id'] ?>">
                                    <td>
                                        <div>
                                            <h6 class="mb-1"><?= esc($periodo['nombre']) ?></h6>
                                            <p class="text-muted mb-0 small">
                                                ID: <?= $periodo['id'] ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Inicio:</strong> <?= date('d/m/Y', strtotime($periodo['fecha_inicio'])) ?><br>
                                            <strong>Fin:</strong> <?= date('d/m/Y', strtotime($periodo['fecha_fin'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge <?= $periodo['activo'] ? 'bg-success' : 'bg-secondary' ?> mb-1">
                                                <?= $periodo['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            <?php if ($periodo['vigente_estudiantes']): ?>
                                                <span class="badge bg-info small">Vigente</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="d-flex justify-content-between">
                                                <span>Fichas:</span>
                                                <strong><?= $periodo['limite_fichas'] ?? 'Sin límite' ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Becas:</span>
                                                <strong><?= $periodo['limite_becas'] ?? 'Sin límite' ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="d-flex justify-content-between">
                                                <span>Fichas:</span>
                                                <strong class="<?= $periodo['fichas_creadas'] >= ($periodo['limite_fichas'] ?? PHP_INT_MAX) ? 'text-danger' : 'text-success' ?>">
                                                    <?= $periodo['fichas_creadas'] ?>
                                                </strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Becas:</span>
                                                <strong class="<?= $periodo['becas_asignadas'] >= ($periodo['limite_becas'] ?? PHP_INT_MAX) ? 'text-danger' : 'text-success' ?>">
                                                    <?= $periodo['becas_asignadas'] ?>
                                                </strong>
                                            </div>
                                            <!-- Barra de progreso para fichas -->
                                            <?php if ($periodo['limite_fichas']): ?>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <?php $porcentajeFichas = min(($periodo['fichas_creadas'] / $periodo['limite_fichas']) * 100, 100); ?>
                                                    <div class="progress-bar <?= $porcentajeFichas >= 90 ? 'bg-danger' : ($porcentajeFichas >= 70 ? 'bg-warning' : 'bg-success') ?>" 
                                                         style="width: <?= $porcentajeFichas ?>%"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input" type="checkbox" 
                                                       <?= $periodo['activo_fichas'] ? 'checked' : '' ?> 
                                                       onchange="toggleConfiguracion(<?= $periodo['id'] ?>, 'activo_fichas', this.checked)"
                                                       id="fichas_<?= $periodo['id'] ?>">
                                                <label class="form-check-label" for="fichas_<?= $periodo['id'] ?>">
                                                    Fichas activas
                                                </label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input" type="checkbox" 
                                                       <?= $periodo['activo_becas'] ? 'checked' : '' ?> 
                                                       onchange="toggleConfiguracion(<?= $periodo['id'] ?>, 'activo_becas', this.checked)"
                                                       id="becas_<?= $periodo['id'] ?>">
                                                <label class="form-check-label" for="becas_<?= $periodo['id'] ?>">
                                                    Becas activas
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="editarPeriodo(<?= $periodo['id'] ?>)" 
                                                    title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="verDetallesPeriodo(<?= $periodo['id'] ?>)" 
                                                    title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="configurarLimites(<?= $periodo['id'] ?>)" 
                                                    title="Configurar límites">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No hay períodos académicos registrados</h5>
                                    <p class="text-muted">Crea el primer período académico para comenzar</p>
                                    <button class="btn btn-success" onclick="mostrarModalNuevoPeriodo()">
                                        <i class="bi bi-plus-circle"></i> Crear Primer Período
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo/Editar Período -->
<div class="modal fade" id="modalPeriodo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalPeriodo">Nuevo Período Académico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodo">
                    <input type="hidden" id="periodo_id" name="periodo_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre del Período *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   placeholder="Ej: 2024-2025 Primer Semestre">
                        </div>
                        <div class="col-md-6">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="limite_fichas" class="form-label">Límite de Fichas</label>
                            <input type="number" class="form-control" id="limite_fichas" name="limite_fichas" 
                                   min="0" placeholder="Sin límite si está vacío">
                        </div>
                        <div class="col-md-6">
                            <label for="limite_becas" class="form-label">Límite de Becas</label>
                            <input type="number" class="form-control" id="limite_becas" name="limite_becas" 
                                   min="0" placeholder="Sin límite si está vacío">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción opcional del período académico..."></textarea>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="activo_fichas" name="activo_fichas" checked>
                                <label class="form-check-label" for="activo_fichas">
                                    Fichas activas
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="activo_becas" name="activo_becas" checked>
                                <label class="form-check-label" for="activo_becas">
                                    Becas activas
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="vigente_estudiantes" name="vigente_estudiantes">
                                <label class="form-check-label" for="vigente_estudiantes">
                                    Vigente para estudiantes
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarPeriodo()">
                    <i class="bi bi-save"></i> Guardar Período
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Configurar Límites -->
<div class="modal fade" id="modalLimites" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Límites</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formLimites">
                    <input type="hidden" id="periodo_id_limites" name="periodo_id">
                    
                    <div class="mb-3">
                        <label for="nuevo_limite_fichas" class="form-label">Límite de Fichas</label>
                        <input type="number" class="form-control" id="nuevo_limite_fichas" name="limite_fichas" min="0">
                        <small class="text-muted">Deje en blanco para sin límite</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevo_limite_becas" class="form-label">Límite de Becas</label>
                        <input type="number" class="form-control" id="nuevo_limite_becas" name="limite_becas" min="0">
                        <small class="text-muted">Deje en blanco para sin límite</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Cambiar los límites puede afectar las validaciones existentes.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="actualizarLimites()">
                    <i class="bi bi-gear"></i> Actualizar Límites
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let periodos = <?= json_encode($periodos ?? []) ?>;

function mostrarModalNuevoPeriodo() {
    document.getElementById('formPeriodo').reset();
    document.getElementById('periodo_id').value = '';
    document.getElementById('tituloModalPeriodo').textContent = 'Nuevo Período Académico';
    new bootstrap.Modal(document.getElementById('modalPeriodo')).show();
}

function editarPeriodo(periodoId) {
    const periodo = periodos.find(p => p.id == periodoId);
    if (!periodo) return;
    
    document.getElementById('periodo_id').value = periodo.id;
    document.getElementById('nombre').value = periodo.nombre;
    document.getElementById('fecha_inicio').value = periodo.fecha_inicio;
    document.getElementById('fecha_fin').value = periodo.fecha_fin;
    document.getElementById('limite_fichas').value = periodo.limite_fichas || '';
    document.getElementById('limite_becas').value = periodo.limite_becas || '';
    document.getElementById('descripcion').value = periodo.descripcion || '';
    document.getElementById('activo').value = periodo.activo;
    document.getElementById('activo_fichas').checked = periodo.activo_fichas;
    document.getElementById('activo_becas').checked = periodo.activo_becas;
    document.getElementById('vigente_estudiantes').checked = periodo.vigente_estudiantes;
    
    document.getElementById('tituloModalPeriodo').textContent = 'Editar Período Académico';
    new bootstrap.Modal(document.getElementById('modalPeriodo')).show();
}

function guardarPeriodo() {
    const form = document.getElementById('formPeriodo');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const datos = Object.fromEntries(formData);
    
    // Convertir checkboxes
    datos.activo_fichas = document.getElementById('activo_fichas').checked ? 1 : 0;
    datos.activo_becas = document.getElementById('activo_becas').checked ? 1 : 0;
    datos.vigente_estudiantes = document.getElementById('vigente_estudiantes').checked ? 1 : 0;
    
    const url = datos.periodo_id ? 
        `<?= base_url('admin-bienestar/actualizarPeriodo') ?>` : 
        `<?= base_url('admin-bienestar/crearPeriodo') ?>`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message || 'Período guardado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalPeriodo')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion(data.error || 'Error guardando el período', 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error de conexión', 'error');
    });
}



function configurarLimites(periodoId) {
    const periodo = periodos.find(p => p.id == periodoId);
    if (!periodo) return;
    
    document.getElementById('periodo_id_limites').value = periodo.id;
    document.getElementById('nuevo_limite_fichas').value = periodo.limite_fichas || '';
    document.getElementById('nuevo_limite_becas').value = periodo.limite_becas || '';
    
    new bootstrap.Modal(document.getElementById('modalLimites')).show();
}

function actualizarLimites() {
    const form = document.getElementById('formLimites');
    const formData = new FormData(form);
    const datos = Object.fromEntries(formData);
    
    fetch(`<?= base_url('admin-bienestar/actualizarLimitesPeriodo') ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('Límites actualizados exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalLimites')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion(data.error || 'Error actualizando límites', 'error');
        }
    });
}

function toggleConfiguracion(periodoId, campo, valor) {
    fetch(`<?= base_url('admin-bienestar/toggleConfiguracionPeriodo') ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            periodo_id: periodoId,
            campo: campo,
            valor: valor ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(`Configuración actualizada`, 'success');
        } else {
            mostrarNotificacion(data.error || 'Error actualizando configuración', 'error');
            // Revertir el checkbox
            document.getElementById(`${campo.replace('activo_', '')}_${periodoId}`).checked = !valor;
        }
    });
}

function verDetallesPeriodo(periodoId) {
    const periodo = periodos.find(p => p.id == periodoId);
    if (!periodo) {
        mostrarNotificacion('Período no encontrado', 'error');
        return;
    }

    // Calcular estado del período
    const hoy = new Date();
    const inicio = new Date(periodo.fecha_inicio);
    const fin = new Date(periodo.fecha_fin);
    let estadoPeriodo = '';
    
    if (hoy < inicio) {
        estadoPeriodo = 'Próximo';
    } else if (hoy >= inicio && hoy <= fin) {
        estadoPeriodo = 'Activo';
    } else {
        estadoPeriodo = 'Finalizado';
    }

    // Crear HTML para el modal
    const modalHTML = `
        <div class="text-start">
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="bi bi-calendar-event me-2"></i>${periodo.nombre}
                    </h6>
                    <p class="text-muted mb-2">ID: ${periodo.id}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-success">Información General</h6>
                    <div class="mb-2">
                        <strong>Descripción:</strong><br>
                        <span class="text-muted">${periodo.descripcion || 'Sin descripción'}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Estado:</strong><br>
                        <span class="badge bg-${periodo.activo ? 'success' : 'secondary'}">${periodo.activo ? 'Activo' : 'Inactivo'}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Estado del Período:</strong><br>
                        <span class="badge bg-${estadoPeriodo === 'Activo' ? 'success' : (estadoPeriodo === 'Próximo' ? 'info' : 'secondary')}">${estadoPeriodo}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info">Fechas</h6>
                    <div class="mb-2">
                        <strong>Inicio:</strong><br>
                        <span class="text-muted">${new Date(periodo.fecha_inicio).toLocaleDateString('es-ES')}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Fin:</strong><br>
                        <span class="text-muted">${new Date(periodo.fecha_fin).toLocaleDateString('es-ES')}</span>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-warning">Configuración</h6>
                    <div class="mb-2">
                        <span class="badge bg-${periodo.activo_fichas ? 'success' : 'secondary'} me-1">
                            Fichas ${periodo.activo_fichas ? 'Activas' : 'Inactivas'}
                        </span>
                        <span class="badge bg-${periodo.activo_becas ? 'success' : 'secondary'}">
                            Becas ${periodo.activo_becas ? 'Activas' : 'Inactivas'}
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-${periodo.vigente_estudiantes ? 'success' : 'secondary'}">
                            Estudiantes ${periodo.vigente_estudiantes ? 'Vigentes' : 'No Vigentes'}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Límites</h6>
                    <div class="mb-2">
                        <strong>Fichas:</strong> ${periodo.limite_fichas || 'Sin límite'}<br>
                        <strong>Becas:</strong> ${periodo.limite_becas || 'Sin límite'}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">Uso Actual</h6>
                    <div class="mb-2">
                        <strong>Fichas Creadas:</strong> ${periodo.fichas_creadas || 0}<br>
                        <strong>Becas Asignadas:</strong> ${periodo.becas_asignadas || 0}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info">Acciones</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="verPeriodoCompleto(${periodo.id})">
                            <i class="bi bi-eye me-1"></i>Ver Detalles Completos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="configurarLimites(${periodo.id})">
                            <i class="bi bi-gear me-1"></i>Configurar Límites
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: '<i class="bi bi-calendar-check me-2"></i>Detalles del Período',
        html: modalHTML,
        width: '800px',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#6c757d',
        showCloseButton: true
    });
}

function verPeriodoCompleto(periodoId) {
    // Cerrar el modal actual
    Swal.close();
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo información completa del período',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Hacer petición AJAX para obtener detalles completos
    fetch(`<?= base_url('admin-bienestar/ver-periodo') ?>/${periodoId}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                mostrarDetallesCompletosPeriodo(data);
            } else {
                mostrarNotificacion(data.error || 'Error cargando detalles', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            mostrarNotificacion('Error de conexión', 'error');
        });
}

function mostrarDetallesCompletosPeriodo(data) {
    const { periodo, estadisticas, estudiantes_con_fichas, estudiantes_con_becas, resumen_carreras } = data;
    
    // Crear HTML para estudiantes con fichas
    let fichasHTML = '';
    if (estudiantes_con_fichas && estudiantes_con_fichas.length > 0) {
        estudiantes_con_fichas.forEach(ficha => {
            fichasHTML += `
                <tr>
                    <td>${ficha.nombre} ${ficha.apellido}</td>
                    <td>${ficha.cedula}</td>
                    <td>${ficha.carrera_nombre || 'Sin carrera'}</td>
                    <td><span class="badge bg-${ficha.estado === 'Aprobada' ? 'success' : (ficha.estado === 'Rechazada' ? 'danger' : 'warning')}">${ficha.estado}</span></td>
                    <td>${ficha.email || 'Sin email'}</td>
                </tr>
            `;
        });
    } else {
        fichasHTML = '<tr><td colspan="5" class="text-center text-muted">No hay fichas registradas</td></tr>';
    }
    
    // Crear HTML para estudiantes con becas
    let becasHTML = '';
    if (estudiantes_con_becas && estudiantes_con_becas.length > 0) {
        estudiantes_con_becas.forEach(beca => {
            becasHTML += `
                <tr>
                    <td>${beca.nombre} ${beca.apellido}</td>
                    <td>${beca.cedula}</td>
                    <td>${beca.carrera_nombre || 'Sin carrera'}</td>
                    <td><span class="badge bg-${beca.estado === 'Aprobada' ? 'success' : (beca.estado === 'Rechazada' ? 'danger' : 'warning')}">${beca.estado}</span></td>
                    <td>${beca.nombre_beca || 'Sin especificar'}</td>
                </tr>
            `;
        });
    } else {
        becasHTML = '<tr><td colspan="5" class="text-center text-muted">No hay becas registradas</td></tr>';
    }
    
    // Crear HTML para resumen de carreras
    let carrerasHTML = '';
    if (resumen_carreras && resumen_carreras.length > 0) {
        resumen_carreras.forEach(carrera => {
            carrerasHTML += `
                <tr>
                    <td>${carrera.carrera || 'Sin carrera'}</td>
                    <td class="text-center"><span class="badge bg-primary">${carrera.total_fichas}</span></td>
                    <td class="text-center"><span class="badge bg-success">${carrera.aprobadas}</span></td>
                    <td class="text-center"><span class="badge bg-warning">${carrera.total_fichas - carrera.aprobadas}</span></td>
                </tr>
            `;
        });
    } else {
        carrerasHTML = '<tr><td colspan="4" class="text-center text-muted">No hay datos de carreras</td></tr>';
    }
    
    const modalHTML = `
        <div class="text-start">
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="text-primary">
                        <i class="bi bi-calendar-event me-2"></i>${periodo.nombre}
                    </h6>
                    <p class="text-muted mb-0">ID: ${periodo.id}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-success">Información General</h6>
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
                    <h6 class="text-info"><i class="bi bi-file-earmark-text me-2"></i>Estudiantes con Fichas (${estudiantes_con_fichas.length})</h6>
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
                    <h6 class="text-warning"><i class="bi bi-award me-2"></i>Estudiantes con Becas (${estudiantes_con_becas.length})</h6>
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
    `;
    
    Swal.fire({
        title: '<i class="bi bi-calendar-check me-2"></i>Vista Completa del Período',
        html: modalHTML,
        width: '1200px',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#6c757d',
        showCloseButton: true
    });
}

function aplicarFiltros() {
    const estado = document.getElementById('filtroEstado').value;
    const fecha = document.getElementById('filtroFecha').value;
    const busqueda = document.getElementById('busquedaPeriodo').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tablaPeriodos tbody tr');
    
    filas.forEach(fila => {
        if (fila.cells.length === 1) return; // Skip empty message row
        
        let mostrar = true;
        const periodoId = fila.getAttribute('data-periodo-id');
        const periodo = periodos.find(p => p.id == periodoId);
        
        if (!periodo) return;
        
        // Filtro por estado
        if (estado && periodo.activo != estado) {
            mostrar = false;
        }
        
        // Filtro por fecha
        if (fecha) {
            const hoy = new Date();
            const inicio = new Date(periodo.fecha_inicio);
            const fin = new Date(periodo.fecha_fin);
            
            switch (fecha) {
                case 'actual':
                    if (!(hoy >= inicio && hoy <= fin)) mostrar = false;
                    break;
                case 'proximo':
                    if (inicio <= hoy) mostrar = false;
                    break;
                case 'pasado':
                    if (fin >= hoy) mostrar = false;
                    break;
            }
        }
        
        // Filtro por búsqueda
        if (busqueda && !periodo.nombre.toLowerCase().includes(busqueda)) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroFecha').value = '';
    document.getElementById('busquedaPeriodo').value = '';
    aplicarFiltros();
}

function exportarPeriodos() {
    window.location.href = `<?= base_url('admin-bienestar/exportar-periodos') ?>`;
}

function actualizarVista() {
    location.reload();
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const alertClass = tipo === 'success' ? 'alert-success' : 
                      tipo === 'error' ? 'alert-danger' : 'alert-info';
    
    const alerta = document.createElement('div');
    alerta.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.remove();
        }
    }, 5000);
}

// Validación de fechas
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
    });
    
    fechaFin.addEventListener('change', function() {
        if (this.value < fechaInicio.value) {
            fechaInicio.value = this.value;
        }
    });
});
</script>

<style>
.mini-stats-wid .card-body {
    padding: 1.25rem;
}

.avatar-title {
    align-items: center;
    background-color: #556ee6;
    border-radius: 50%;
    color: #fff;
    display: flex;
    font-size: 16px;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.table-responsive {
    border-radius: 0.375rem;
}

.form-check-sm .form-check-input {
    margin-top: 0.1rem;
}

.progress {
    background-color: rgba(0,0,0,.1);
}

@media (max-width: 768px) {
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .mini-stats-wid {
        margin-bottom: 1rem;
    }
}
</style>

<?= $this->endSection() ?>
