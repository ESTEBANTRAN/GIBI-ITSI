<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header de la Solicitud -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-check me-2"></i>
                        Revisión de Documentos - Solicitud #<?= $solicitud['id'] ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-person me-2"></i>Estudiante</h6>
                            <p class="mb-1"><strong><?= esc($solicitud['estudiante_nombre'] . ' ' . $solicitud['estudiante_apellido']) ?></strong></p>
                            <p class="mb-1 text-muted">Cédula: <?= esc($solicitud['estudiante_cedula']) ?></p>
                            <p class="mb-1 text-muted">Carrera: <?= esc($solicitud['carrera_nombre'] ?? 'Sin carrera') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-award me-2"></i>Beca</h6>
                            <p class="mb-1"><strong><?= esc($solicitud['beca_nombre']) ?></strong></p>
                            <p class="mb-1 text-muted">Tipo: <?= esc($solicitud['tipo_beca']) ?></p>
                            <p class="mb-1 text-muted">Período: <?= esc($solicitud['periodo_nombre']) ?></p>
                        </div>
                    </div>
                    
                    <!-- Estado de la Ficha Socioeconómica -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="bi bi-clipboard-data me-2"></i>Estado de la Ficha Socioeconómica</h6>
                            <?php if ($ficha): ?>
                                <?php
                                $fichaClass = match($ficha['estado']) {
                                    'Aprobada' => 'success',
                                    'Enviada', 'Revisada' => 'warning',
                                    'Rechazada' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $fichaClass ?> fs-6">
                                    <?= esc($ficha['estado']) ?>
                                </span>
                                <?php if ($ficha['estado'] === 'Rechazada' && !empty($ficha['observaciones'])): ?>
                                    <p class="text-muted mt-1">Observaciones: <?= esc($ficha['observaciones']) ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">No presentada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Documentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-files me-2"></i>
                        Documentos de la Solicitud
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($documentos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Documento</th>
                                        <th width="15%">Estado</th>
                                        <th width="20%">Archivo</th>
                                        <th width="15%">Fecha Subida</th>
                                        <th width="20%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documentos as $index => $documento): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($documento['documento_requerido_nombre']) ?></strong>
                                                    <?php if (!empty($documento['documento_requerido_descripcion'])): ?>
                                                        <br><small class="text-muted"><?= esc($documento['documento_requerido_descripcion']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $estadoClass = match($documento['estado']) {
                                                    'Aprobado' => 'success',
                                                    'En Revision' => 'info',
                                                    'Rechazado' => 'danger',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $estadoClass ?>">
                                                    <?= esc($documento['estado']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($documento['estado'] !== 'Pendiente' && !empty($documento['ruta_archivo'])): ?>
                                                    <a href="<?= base_url('admin-bienestar/ver-documento/' . $documento['id']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Ver
                                                    </a>
                                                    <?php if (!empty($documento['tamano_archivo'])): ?>
                                                        <br><small class="text-muted">
                                                            <?= number_format($documento['tamano_archivo'] / 1024, 1) ?> KB
                                                        </small>
                                                    <?php endif; ?>
                                                <?php elseif ($documento['estado'] === 'Pendiente'): ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>Pendiente de subida
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>Archivo no disponible
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($documento['fecha_subida']): ?>
                                                    <?= date('d/m/Y H:i', strtotime($documento['fecha_subida'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($documento['estado'] === 'En Revision'): ?>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" 
                                                                class="btn btn-success" 
                                                                onclick="aprobarDocumento(<?= $documento['id'] ?>)"
                                                                title="Aprobar documento">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-danger" 
                                                                onclick="rechazarDocumento(<?= $documento['id'] ?>)"
                                                                title="Rechazar documento">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                <?php elseif ($documento['estado'] === 'Aprobado'): ?>
                                                    <div class="text-center">
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Aprobado
                                                        </span>
                                                        <?php if (!empty($documento['observaciones'])): ?>
                                                            <br><small class="text-muted"><?= esc($documento['observaciones']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($documento['estado'] === 'Rechazado'): ?>
                                                    <div class="text-center">
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Rechazado
                                                        </span>
                                                        <?php if (!empty($documento['observaciones'])): ?>
                                                            <br><small class="text-muted"><?= esc($documento['observaciones']) ?></small>
                                                        <?php endif; ?>
                                                        <br>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-warning mt-1" 
                                                                onclick="revisarNuevamente(<?= $documento['id'] ?>)">
                                                            <i class="bi bi-arrow-clockwise me-1"></i>Revisar Nuevamente
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-hourglass-split me-1"></i>Esperando subida
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay documentos para revisar</h5>
                            <p class="text-muted">Los documentos aparecerán aquí cuando el estudiante los suba</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones de la Solicitud -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Acciones de la Solicitud
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Aprobar Beca</h6>
                            <p class="text-muted small">
                                Solo se puede aprobar cuando todos los documentos estén aprobados 
                                y la ficha socioeconómica esté aprobada.
                            </p>
                            <button type="button" 
                                    class="btn btn-success" 
                                    onclick="aprobarSolicitudBeca(<?= $solicitud['id'] ?>)"
                                    <?= ($ficha && $ficha['estado'] === 'Aprobada' && count(array_filter($documentos, fn($d) => $d['estado'] === 'Aprobado')) === count($documentos)) ? '' : 'disabled' ?>>
                                <i class="bi bi-check-circle me-2"></i>Aprobar Beca
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Rechazar Beca</h6>
                            <p class="text-muted small">
                                Se puede rechazar en cualquier momento del proceso. 
                                Se debe proporcionar un motivo del rechazo.
                            </p>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="rechazarSolicitudBeca(<?= $solicitud['id'] ?>)">
                                <i class="bi bi-x-circle me-2"></i>Rechazar Beca
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprobar Documento -->
<div class="modal fade" id="modalAprobarDocumento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Aprobar Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAprobarDocumento">
                    <input type="hidden" id="documento_id_aprobar" name="documento_id">
                    <div class="mb-3">
                        <label for="observaciones_aprobar" class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" id="observaciones_aprobar" name="observaciones" rows="3" 
                                  placeholder="Escriba observaciones adicionales si es necesario..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarAprobarDocumento()">
                    <i class="bi bi-check-lg me-2"></i>Aprobar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Documento -->
<div class="modal fade" id="modalRechazarDocumento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Rechazar Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRechazarDocumento">
                    <input type="hidden" id="documento_id_rechazar" name="documento_id">
                    <div class="mb-3">
                        <label for="motivo_rechazo" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="motivo_rechazo" name="motivo_rechazo" rows="4" 
                                  placeholder="Explique detalladamente por qué se rechaza este documento..." required></textarea>
                        <div class="form-text">
                            Esta información será enviada al estudiante para que pueda corregir el documento.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazarDocumento()">
                    <i class="bi bi-x-lg me-2"></i>Rechazar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprobar Solicitud de Beca -->
<div class="modal fade" id="modalAprobarSolicitudBeca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-award me-2"></i>Aprobar Solicitud de Beca
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAprobarSolicitudBeca">
                    <input type="hidden" id="solicitud_id_aprobar" name="solicitud_id">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Verificación:</strong> Todos los documentos están aprobados y la ficha socioeconómica está aprobada.
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_beca" class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" id="observaciones_beca" name="observaciones" rows="3" 
                                  placeholder="Escriba observaciones adicionales si es necesario..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarAprobarSolicitudBeca()">
                    <i class="bi bi-check-lg me-2"></i>Aprobar Beca
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Solicitud de Beca -->
<div class="modal fade" id="modalRechazarSolicitudBeca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Rechazar Solicitud de Beca
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRechazarSolicitudBeca">
                    <input type="hidden" id="solicitud_id_rechazar" name="solicitud_id">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Esta acción no se puede deshacer. El estudiante será notificado del rechazo.
                    </div>
                    <div class="mb-3">
                        <label for="motivo_rechazo_beca" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="motivo_rechazo_beca" name="motivo_rechazo" rows="4" 
                                  placeholder="Explique detalladamente por qué se rechaza esta solicitud de beca..." required></textarea>
                        <div class="form-text">
                            Esta información será enviada al estudiante para su conocimiento.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazarSolicitudBeca()">
                    <i class="bi bi-x-lg me-2"></i>Rechazar Beca
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para aprobar documentos
function aprobarDocumento(documentoId) {
    document.getElementById('documento_id_aprobar').value = documentoId;
    document.getElementById('observaciones_aprobar').value = '';
    new bootstrap.Modal(document.getElementById('modalAprobarDocumento')).show();
}

function confirmarAprobarDocumento() {
    const form = document.getElementById('formAprobarDocumento');
    const formData = new FormData(form);
    
    fetch('<?= base_url('admin-bienestar/aprobar-documento') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error del sistema', 'error');
    });
}

// Funciones para rechazar documentos
function rechazarDocumento(documentoId) {
    document.getElementById('documento_id_rechazar').value = documentoId;
    document.getElementById('motivo_rechazo').value = '';
    new bootstrap.Modal(document.getElementById('modalRechazarDocumento')).show();
}

function confirmarRechazarDocumento() {
    const form = document.getElementById('formRechazarDocumento');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    fetch('<?= base_url('admin-bienestar/rechazar-documento') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error del sistema', 'error');
    });
}

// Funciones para aprobar solicitud de beca
function aprobarSolicitudBeca(solicitudId) {
    document.getElementById('solicitud_id_aprobar').value = solicitudId;
    document.getElementById('observaciones_beca').value = '';
    new bootstrap.Modal(document.getElementById('modalAprobarSolicitudBeca')).show();
}

function confirmarAprobarSolicitudBeca() {
    const form = document.getElementById('formAprobarSolicitudBeca');
    const formData = new FormData(form);
    
    fetch('<?= base_url('admin-bienestar/aprobar-solicitud-beca') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error del sistema', 'error');
    });
}

// Funciones para rechazar solicitud de beca
function rechazarSolicitudBeca(solicitudId) {
    document.getElementById('solicitud_id_rechazar').value = solicitudId;
    document.getElementById('motivo_rechazo_beca').value = '';
    new bootstrap.Modal(document.getElementById('modalRechazarSolicitudBeca')).show();
}

function confirmarRechazarSolicitudBeca() {
    const form = document.getElementById('formRechazarSolicitudBeca');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    fetch('<?= base_url('admin-bienestar/rechazar-solicitud-beca') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error del sistema', 'error');
    });
}

// Función para revisar nuevamente un documento rechazado
function revisarNuevamente(documentoId) {
    if (confirm('¿Desea marcar este documento para revisión nuevamente?')) {
        fetch('<?= base_url('admin-bienestar/revisar-nuevamente-documento') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ documento_id: documentoId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Documento marcado para revisión', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarNotificacion(data.message, 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion('Error del sistema', 'error');
        });
    }
}

// Función para mostrar notificaciones
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
</script>

<style>
.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

/* Mejoras en la tabla de documentos */
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Centrado de contenido en columnas */
.text-center {
    text-align: center !important;
}

/* Mejoras en badges */
.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

/* Mejoras en botones */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Iconos consistentes */
.bi {
    margin-right: 0.25rem;
}

/* Estados de documentos */
.documento-pendiente {
    color: #6c757d;
}

.documento-revision {
    color: #0dcaf0;
}

.documento-aprobado {
    color: #198754;
}

.documento-rechazado {
    color: #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
}
</style>

<?= $this->endSection() ?>
