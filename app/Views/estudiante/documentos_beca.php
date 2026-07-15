<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Gestión de Documentos</h2>
                    <p class="text-muted mb-0">
                        Beca: <strong><?= esc($beca['nombre']) ?></strong> | 
                        Período: <strong><?= esc($solicitud['periodo_nombre']) ?></strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('estudiante/becas') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Becas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de la Solicitud -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-2">Estado de la Solicitud</h5>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-<?= $solicitud['estado'] === 'Postulada' ? 'warning' : 'success' ?> fs-6">
                                    <?= esc($solicitud['estado']) ?>
                                </span>
                                <small class="text-muted">
                                    Fecha: <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-end">
                                <h6 class="mb-1">Progreso de Documentos</h6>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $estadisticas['porcentaje'] ?>%"
                                         aria-valuenow="<?= $estadisticas['porcentaje'] ?>" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?= $estadisticas['aprobados'] ?>/<?= $estadisticas['total'] ?> documentos aprobados 
                                    (<?= $estadisticas['porcentaje'] ?>%)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos Requeridos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>Documentos Requeridos
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($documentos)): ?>
                        <div class="row">
                            <?php foreach ($documentos as $documento): ?>
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm documento-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1"><?= esc($documento['nombre_documento']) ?></h6>
                                                    <small class="text-muted d-block">
                                                        <?= esc($documento['descripcion']) ?>
                                                    </small>
                                                    <?php if ($documento['obligatorio']): ?>
                                                        <span class="badge bg-danger small">Obligatorio</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-end">
                                                    <?php
                                                    $badgeClass = match($documento['estado']) {
                                                        'Pendiente' => 'bg-secondary',
                                                        'En Revision' => 'bg-warning',
                                                        'Aprobado' => 'bg-success',
                                                        'Rechazado' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> mb-2">
                                                        <?= esc($documento['estado']) ?>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Orden: <?= $documento['orden_revision'] ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Estado del Documento -->
                                            <div class="mb-3">
                                                <?php if ($documento['estado'] === 'Pendiente'): ?>
                                                    <div class="alert alert-warning py-2 mb-0" role="alert">
                                                        <small>
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            Documento pendiente de subida
                                                        </small>
                                                    </div>
                                                <?php elseif ($documento['estado'] === 'En Revision'): ?>
                                                    <div class="alert alert-info py-2 mb-0" role="alert">
                                                        <small>
                                                            <i class="bi bi-clock me-1"></i>
                                                            En revisión por administrador
                                                        </small>
                                                        <?php if ($documento['fecha_subida']): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                Subido: <?= date('d/m/Y H:i', strtotime($documento['fecha_subida'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($documento['estado'] === 'Aprobado'): ?>
                                                    <div class="alert alert-success py-2 mb-0" role="alert">
                                                        <small>
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Documento aprobado
                                                        </small>
                                                        <?php if ($documento['fecha_revision']): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                Aprobado: <?= date('d/m/Y H:i', strtotime($documento['fecha_revision'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($documento['estado'] === 'Rechazado'): ?>
                                                    <div class="alert alert-danger py-2 mb-0" role="alert">
                                                        <small>
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            Documento rechazado
                                                        </small>
                                                        <?php if ($documento['observaciones']): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                Motivo: <?= esc($documento['observaciones']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Acciones -->
                                            <div class="d-grid gap-2">
                                                <?php if ($documento['estado'] === 'Pendiente'): ?>
                                                    <button class="btn btn-primary btn-sm" onclick="mostrarModalSubida(<?= $documento['id'] ?>, '<?= esc($documento['nombre_documento']) ?>')">
                                                        <i class="bi bi-upload"></i> Subir Documento
                                                    </button>
                                                <?php elseif (in_array($documento['estado'], ['En Revision', 'Aprobado', 'Rechazado'])): ?>
                                                    <div class="btn-group btn-group-sm w-100">
                                                        <button class="btn btn-outline-primary" onclick="descargarDocumento(<?= $documento['id'] ?>)">
                                                            <i class="bi bi-download"></i> Descargar
                                                        </button>
                                                        <?php if (in_array($documento['estado'], ['En Revision'])): ?>
                                                            <button class="btn btn-outline-danger" onclick="eliminarDocumento(<?= $documento['id'] ?>)">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay documentos requeridos</h5>
                            <p class="text-muted">Esta beca no requiere documentos adicionales.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Subir Documento -->
<div class="modal fade" id="modalSubirDocumento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSubirDocumento" enctype="multipart/form-data">
                    <input type="hidden" id="documento_id" name="documento_id">
                    <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Documento a subir</label>
                        <div class="alert alert-info py-2 mb-2">
                            <small>
                                <strong id="nombre_documento_modal"></strong>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="archivo" class="form-label">Seleccionar archivo *</label>
                        <input type="file" class="form-control" id="archivo" name="archivo" accept=".pdf" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Solo archivos PDF, máximo 2MB
                        </div>
                    </div>

                    <div class="alert alert-warning py-2">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Importante:</strong> Asegúrate de que el documento sea legible y esté completo antes de subirlo.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="subirDocumento()">
                    <i class="bi bi-upload"></i> Subir Documento
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarModalSubida(documentoId, nombreDocumento) {
    document.getElementById('documento_id').value = documentoId;
    document.getElementById('nombre_documento_modal').textContent = nombreDocumento;
    new bootstrap.Modal(document.getElementById('modalSubirDocumento')).show();
}

function subirDocumento() {
    const form = document.getElementById('formSubirDocumento');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Validar archivo
    const archivo = document.getElementById('archivo').files[0];
    if (!archivo) {
        mostrarNotificacion('Debe seleccionar un archivo', 'error');
        return;
    }

    if (archivo.type !== 'application/pdf') {
        mostrarNotificacion('Solo se permiten archivos PDF', 'error');
        return;
    }

    if (archivo.size > 2 * 1024 * 1024) {
        mostrarNotificacion('El archivo no puede superar 2MB', 'error');
        return;
    }

    // Mostrar indicador de carga
    const btnSubir = document.querySelector('#modalSubirDocumento .btn-primary');
    const btnOriginal = btnSubir.innerHTML;
    btnSubir.innerHTML = '<i class="bi bi-hourglass-split"></i> Subiendo...';
    btnSubir.disabled = true;

    fetch('<?= base_url('estudiante/subir-documento') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalSubirDocumento')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error de conexión', 'error');
    })
    .finally(() => {
        btnSubir.innerHTML = btnOriginal;
        btnSubir.disabled = false;
    });
}

function descargarDocumento(documentoId) {
    window.location.href = `<?= base_url('estudiante/descargar-documento') ?>/${documentoId}`;
}

function eliminarDocumento(documentoId) {
    if (confirm('¿Está seguro de que desea eliminar este documento? Esta acción no se puede deshacer.')) {
        const formData = new FormData();
        formData.append('documento_id', documentoId);

        fetch('<?= base_url('estudiante/eliminar-documento') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacion(data.message, 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion('Error de conexión', 'error');
        });
    }
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
</script>

<style>
.documento-card {
    transition: transform 0.2s ease-in-out;
}

.documento-card:hover {
    transform: translateY(-2px);
}

.progress {
    background-color: #e9ecef;
}

.alert {
    border-radius: 8px;
}

@media (max-width: 768px) {
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>

<?= $this->endSection() ?>
