<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">💰 Sistema de Becas</h2>
                    <p class="text-muted mb-0">Gestiona tus solicitudes de becas y oportunidades disponibles</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="actualizarEstado()">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                    <?php if (isset($puede_solicitar) && $puede_solicitar): ?>
                        <button class="btn btn-success" onclick="mostrarModalSolicitud()">
                            <i class="bi bi-plus-circle"></i> Nueva Solicitud
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de Habilitación -->
    <div class="row mb-4">
        <div class="col-12">
            <?php if ($puede_solicitar): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <h5 class="alert-heading mb-1">¡Habilitado para solicitar becas!</h5>
                        <p class="mb-0"><?= $habilitacion['motivo'] ?></p>
                        <?php if (isset($detalles_habilitacion['solicitudes_restantes'])): ?>
                            <small class="text-muted">
                                Solicitudes restantes: <?= $detalles_habilitacion['solicitudes_restantes'] ?> | 
                                Período: <?= $detalles_habilitacion['periodo_nombre'] ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <h5 class="alert-heading mb-1">No habilitado para solicitar becas</h5>
                        <p class="mb-0"><?= $motivo_deshabilitacion ?></p>
                        
                        <?php if (isset($detalles_habilitacion['accion_requerida'])): ?>
                            <div class="mt-2">
                                <a href="<?= base_url('estudiante/ficha-socioeconomica') ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-arrow-right"></i> <?= $detalles_habilitacion['accion_requerida'] ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($detalles_habilitacion['estado_ficha']) && $detalles_habilitacion['estado_ficha'] !== 'Aprobada'): ?>
                            <small class="text-muted d-block mt-1">
                                Estado actual de tu ficha: <strong><?= $detalles_habilitacion['estado_ficha'] ?></strong>
                                <?php if (isset($detalles_habilitacion['fecha_envio'])): ?>
                                    | Enviada: <?= date('d/m/Y H:i', strtotime($detalles_habilitacion['fecha_envio'])) ?>
                                <?php endif; ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estadísticas Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-1"><?= $estadisticas['fichas']['total'] ?? 0 ?></h5>
                    <p class="text-muted mb-0 small">Fichas Creadas</p>
                    <small class="text-success">
                        <?= $estadisticas['fichas']['aprobadas'] ?? 0 ?> aprobadas
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-award" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-1"><?= $estadisticas['solicitudes_becas']['total'] ?? 0 ?></h5>
                    <p class="text-muted mb-0 small">Solicitudes Totales</p>
                    <small class="text-primary">
                        <?= $estadisticas['solicitudes_becas']['pendientes'] ?? 0 ?> pendientes
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-1"><?= $estadisticas['solicitudes_becas']['aprobadas'] ?? 0 ?></h5>
                    <p class="text-muted mb-0 small">Becas Aprobadas</p>
                    <small class="text-success">¡Felicitaciones!</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-1"><?= count($becas_disponibles) ?></h5>
                    <p class="text-muted mb-0 small">Becas Disponibles</p>
                    <small class="text-info">Para solicitar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Solicitudes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>Mis Solicitudes de Becas
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Botón de prueba temporal -->
                        <button class="btn btn-outline-warning btn-sm" onclick="probarFuncion()" title="Probar función subirDocumentos">
                            <i class="bi bi-bug"></i> Probar Función
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($solicitudes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Beca</th>
                                        <th>Estado</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Progreso Documentos</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($solicitudes as $solicitud): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?= esc($solicitud['beca_nombre']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($solicitud['tipo_beca']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = match($solicitud['estado']) {
                                                    'Pendiente' => 'bg-warning',
                                                    'Aprobada' => 'bg-success',
                                                    'Rechazada' => 'bg-danger',
                                                    'Documentos Aprobados' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= esc($solicitud['estado']) ?></span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                                <br>
                                                <small class="text-muted"><?= date('H:i', strtotime($solicitud['fecha_solicitud'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if (isset($solicitud['progreso_documentos'])): ?>
                                                    <div class="progress mb-1" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: <?= $solicitud['progreso_documentos']['porcentaje_aprobados'] ?? 0 ?>%"
                                                             title="Aprobados: <?= $solicitud['progreso_documentos']['aprobados'] ?>">
                                                        </div>
                                                        <?php 
                                                        $pctEnRevision = $solicitud['progreso_documentos']['total'] > 0 
                                                            ? round(($solicitud['progreso_documentos']['en_revision'] / $solicitud['progreso_documentos']['total']) * 100, 1) 
                                                            : 0;
                                                        ?>
                                                        <div class="progress-bar bg-warning" role="progressbar" 
                                                             style="width: <?= $pctEnRevision ?>%"
                                                             title="En revisión: <?= $solicitud['progreso_documentos']['en_revision'] ?>">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= $solicitud['progreso_documentos']['subidos'] ?>/<?= $solicitud['progreso_documentos']['total'] ?> subidos
                                                        <?php if ($solicitud['progreso_documentos']['aprobados'] > 0): ?>
                                                            <span class="text-success">(<?= $solicitud['progreso_documentos']['aprobados'] ?> aprobados)</span>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin documentos</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($solicitud['monto_beca'], 0, ',', '.') ?></strong>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="verDetalleSolicitud(<?= $solicitud['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <!-- Debug: Estado actual: <?= $solicitud['estado'] ?> -->
                                                    <?php if (in_array($solicitud['estado'], ['Postulada', 'En Revisión'])): ?>
                                                        <button class="btn btn-outline-secondary" onclick="subirDocumentos(<?= $solicitud['id'] ?>)">
                                                            <i class="bi bi-upload"></i>
                                                        </button>
                                                        <?php if ($solicitud['estado'] === 'Postulada'): ?>
                                                            <button class="btn btn-outline-danger" onclick="cancelarSolicitud(<?= $solicitud['id'] ?>)">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <!-- Botón de prueba para debug -->
                                                        <button class="btn btn-outline-warning btn-sm" onclick="subirDocumentos(<?= $solicitud['id'] ?>)" title="Debug: Estado actual: <?= $solicitud['estado'] ?>">
                                                            <i class="bi bi-bug"></i> Debug
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No tienes solicitudes de becas aún</h5>
                            <p class="text-muted">¡Solicita tu primera beca cuando estés habilitado!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Becas Disponibles -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-gift me-2"></i>Becas Disponibles
                    </h5>
                    <?php if (isset($becas_disponibles) && !empty($becas_disponibles)): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary fs-6">
                                <?= count($becas_disponibles) ?> becas disponibles
                            </span>
                            <?php if (isset($becas_disponibles[0]['periodo_actual'])): ?>
                                <small class="text-muted">
                                    Período actual: <strong><?= $becas_disponibles[0]['periodo_actual']['nombre'] ?? 'N/A' ?></strong>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($becas_disponibles)): ?>
                        <div class="row">
                            <?php foreach ($becas_disponibles as $beca): ?>
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm beca-card <?= $beca['puede_solicitar_esta_beca'] ? '' : 'border-warning' ?>">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0"><?= esc($beca['nombre']) ?></h6>
                                                <div class="d-flex flex-column align-items-end">
                                                    <span class="badge bg-primary mb-1"><?= esc($beca['tipo_beca']) ?></span>
                                                    <small class="text-muted">Período: <?= esc($beca['periodo_nombre']) ?></small>
                                                </div>
                                            </div>
                                            
                                            <p class="card-text text-muted small mb-3">
                                                <?= esc(substr($beca['descripcion'], 0, 100)) ?>...
                                            </p>
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small">Monto:</span>
                                                    <strong class="text-success">$<?= number_format($beca['monto_beca'] ?? 0, 0, ',', '.') ?></strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small">Cupos:</span>
                                                    <span class="badge bg-info"><?= $beca['cupos_disponibles'] ?? 'N/A' ?> disponibles</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small">Documentos:</span>
                                                    <span class="text-muted small"><?= $beca['total_documentos'] ?? 'N/A' ?> requeridos</span>
                                                </div>
                                                <?php if (!empty($beca['documentos_requisitos'])): ?>
                                                    <div class="mt-2">
                                                        <small class="text-muted d-block">
                                                            <strong>Documentos requeridos:</strong>
                                                        </small>
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            <?php 
                                                            $documentos = json_decode($beca['documentos_requisitos'], true);
                                                            if (is_array($documentos)):
                                                                foreach ($documentos as $doc): ?>
                                                                    <span class="badge bg-warning text-dark small"><?= esc($doc) ?></span>
                                                                <?php endforeach;
                                                            endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (!$beca['puede_solicitar_esta_beca']): ?>
                                                <div class="alert alert-warning py-2 mb-3" role="alert">
                                                    <small class="d-block">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <strong>Requisito pendiente:</strong>
                                                    </small>
                                                    <small class="d-block mt-1">
                                                        <?= esc($beca['motivo_no_habilitado']) ?>
                                                    </small>
                                                    <?php if (isset($beca['requisitos_pendientes']['accion_requerida'])): ?>
                                                        <div class="mt-2">
                                                            <a href="<?= base_url('estudiante/ficha-socioeconomica') ?>" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-arrow-right"></i> <?= esc($beca['requisitos_pendientes']['accion_requerida']) ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-primary btn-sm" onclick="verDetallesBeca(<?= $beca['id'] ?>)">
                                                    <i class="bi bi-info-circle"></i> Ver Detalles
                                                </button>
                                                <?php if ($beca['puede_solicitar_esta_beca']): ?>
                                                    <button class="btn btn-success btn-sm" onclick="solicitarBeca(<?= $beca['id'] ?>)">
                                                        <i class="bi bi-plus-circle"></i> Solicitar
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="bi bi-lock"></i> Requisitos Pendientes
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-gift display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay becas disponibles</h5>
                            <p class="text-muted">
                                <?php if (isset($detalles_habilitacion['accion_requerida'])): ?>
                                    Para ver las becas disponibles, primero debes: <strong><?= $detalles_habilitacion['accion_requerida'] ?></strong>
                                <?php else: ?>
                                    Las becas aparecerán aquí cuando estén activas y puedas solicitarlas.
                                <?php endif; ?>
                            </p>
                            <?php if (isset($detalles_habilitacion['accion_requerida'])): ?>
                                <div class="mt-3">
                                    <a href="<?= base_url('estudiante/ficha-socioeconomica') ?>" class="btn btn-warning">
                                        <i class="bi bi-arrow-right"></i> <?= $detalles_habilitacion['accion_requerida'] ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Solicitud de Beca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaSolicitud">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="beca_id" class="form-label">Seleccionar Beca *</label>
                            <select class="form-select" id="beca_id" name="beca_id" required>
                                <option value="">Seleccione una beca...</option>
                                <?php foreach ($becas_disponibles as $beca): ?>
                                    <option value="<?= $beca['id'] ?>" 
                                            data-monto="<?= $beca['monto_beca'] ?>"
                                            data-periodo-id="<?= $beca['periodo_id'] ?? '' ?>">
                                        <?= esc($beca['nombre']) ?> - $<?= number_format($beca['monto_beca'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto de la Beca</label>
                            <input type="text" id="monto_display" class="form-control" readonly placeholder="Seleccione una beca">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                      placeholder="Agregue cualquier comentario adicional sobre su solicitud..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Documentos Requeridos -->
                    <div class="row mt-3" id="documentos_requeridos_section" style="display: none;">
                        <div class="col-12">
                            <label class="form-label">Documentos Requeridos</label>
                            <div class="alert alert-warning py-2" id="documentos_requeridos_content">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Advertencia Importante -->
                    <div class="alert alert-danger mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>⚠️ ADVERTENCIA IMPORTANTE:</strong>
                        <div class="mt-2">
                            <p class="mb-2"><strong>Solicita la beca únicamente si realmente consideras que cumples con las condiciones y previamente leíste los requerimientos.</strong></p>
                            <p class="mb-2">El entorpecimiento del proceso de becas puede ponerte como:</p>
                            <ul class="mb-2">
                                <li>Persona no prioritaria para beca</li>
                                <li>Sanciones dentro del propio instituto</li>
                            </ul>
                            <p class="mb-0"><strong>AL SOLICITAR LA BECA LO HACES ENTENDIENDO QUE ES UN PROCESO ÉTICO Y LEGAL QUE SE DEBE CUMPLIR ADECUADAMENTE.</strong></p>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Importante:</strong> Después de crear la solicitud, deberá subir los documentos requeridos para completar el proceso.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="crearSolicitud()">
                    <i class="bi bi-plus-circle"></i> Crear Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles de Beca -->
<div class="modal fade" id="modalDetallesBeca" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Beca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetallesBeca">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
function mostrarModalSolicitud() {
    new bootstrap.Modal(document.getElementById('modalNuevaSolicitud')).show();
}

function solicitarBeca(becaId) {
    // Pre-seleccionar la beca en el modal
    document.getElementById('beca_id').value = becaId;
    document.getElementById('beca_id').dispatchEvent(new Event('change'));
    mostrarModalSolicitud();
}

function crearSolicitud() {
    const form = document.getElementById('formNuevaSolicitud');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Obtener el período de la beca seleccionada
    const becaSelect = document.getElementById('beca_id');
    const becaOption = becaSelect.options[becaSelect.selectedIndex];
    const periodoId = becaOption.getAttribute('data-periodo-id');
    
    if (!periodoId) {
        mostrarNotificacion('Error: No se pudo determinar el período de la beca', 'error');
        return;
    }
    
    const datos = {
        beca_id: formData.get('beca_id'),
        periodo_id: periodoId,
        observaciones: formData.get('observaciones')
    };
    
    fetch('<?= base_url('estudiante/solicitar-beca') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('Solicitud creada exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevaSolicitud')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion(data.error || 'Error creando la solicitud', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    });
}

function verDetallesBeca(becaId) {
    // Implementar carga de detalles de beca
    fetch(`<?= base_url('estudiante/detalleBeca') ?>/${becaId}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('contenidoDetallesBeca').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalDetallesBeca')).show();
    })
    .catch(error => {
        mostrarNotificacion('Error cargando detalles de la beca', 'error');
    });
}

function verDetalleSolicitud(solicitudId) {
    window.location.href = `<?= base_url('estudiante/solicitud-beca') ?>/${solicitudId}`;
}

function subirDocumentos(solicitudId) {
    // Mostrar modal de confirmación personalizado
    mostrarModalConfirmacion(solicitudId);
}

function mostrarModalConfirmacion(solicitudId) {
    // Crear el modal dinámicamente
    const modalHTML = `
        <div class="modal fade" id="modalConfirmacionDocumentos" tabindex="-1" aria-labelledby="modalConfirmacionDocumentosLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title" id="modalConfirmacionDocumentosLabel">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>
                            Gestionar Documentos
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="mb-3">¿Deseas ir a la página de gestión de documentos?</h6>
                        <div class="alert alert-info mb-0">
                            <strong>Solicitud #${solicitudId}</strong>
                            <br>
                            <small class="text-muted">Podrás subir, revisar y gestionar todos los documentos requeridos para esta beca.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary px-4" onclick="confirmarRedireccion(${solicitudId})">
                            <i class="bi bi-arrow-right me-2"></i>Continuar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modalConfirmacionDocumentos');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmacionDocumentos'));
    modal.show();
}

function confirmarRedireccion(solicitudId) {
    const url = `<?= base_url('estudiante/documentos-beca') ?>/${solicitudId}`;
    // Cerrar el modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacionDocumentos'));
    modal.hide();
    
    // Mostrar mensaje de redirección
    mostrarNotificacion('Redirigiendo a gestión de documentos...', 'info');
    
    // Redirigir después de un pequeño delay
    setTimeout(() => {
        window.location.href = url;
    }, 1000);
}

function probarFuncion() {
    alert('Función JavaScript funcionando correctamente');
    
    // Probar la función subirDocumentos con un ID de prueba
    const solicitudId = 1; // ID de prueba
    subirDocumentos(solicitudId);
}

function cancelarSolicitud(solicitudId) {
    if (confirm('¿Está seguro de que desea cancelar esta solicitud de beca?')) {
        fetch('<?= base_url('estudiante/cancelar-solicitud-beca') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: solicitudId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Solicitud cancelada', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacion(data.error || 'Error cancelando la solicitud', 'error');
            }
        });
    }
}

function actualizarEstado() {
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar monto y documentos cuando se selecciona una beca
    document.getElementById('beca_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const monto = selectedOption.getAttribute('data-monto');
        const montoDisplay = document.getElementById('monto_display');
        const documentosSection = document.getElementById('documentos_requeridos_section');
        const documentosContent = document.getElementById('documentos_requeridos_content');
        
        if (monto) {
            montoDisplay.value = '$' + new Intl.NumberFormat('es-CO').format(monto);
            
            // Mostrar documentos requeridos
            const becaId = this.value;
            if (becaId) {
                mostrarDocumentosRequeridos(becaId);
            }
        } else {
            montoDisplay.value = '';
            documentosSection.style.display = 'none';
        }
    });
});

// Función para mostrar documentos requeridos
function mostrarDocumentosRequeridos(becaId) {
    const documentosSection = document.getElementById('documentos_requeridos_section');
    const documentosContent = document.getElementById('documentos_requeridos_content');
    
    // Buscar la beca seleccionada en los datos
    const beca = <?= json_encode($becas_disponibles ?? []) ?>.find(b => b.id == becaId);
    
    if (beca && beca.documentos_requisitos) {
        try {
            // Parsear el JSON de documentos
            const documentos = JSON.parse(beca.documentos_requisitos);
            if (Array.isArray(documentos)) {
                const documentosHTML = documentos.map(doc => `<span class="badge bg-warning text-dark me-1 mb-1">${doc}</span>`).join('');
                
                documentosContent.innerHTML = `
                    <div class="d-flex flex-wrap">
                        ${documentosHTML}
                    </div>
                `;
                documentosSection.style.display = 'block';
            } else {
                documentosSection.style.display = 'none';
            }
        } catch (e) {
            console.error('Error parseando documentos:', e);
            documentosSection.style.display = 'none';
        }
    } else {
        documentosSection.style.display = 'none';
    }
}
</script>

<style>
.beca-card {
    transition: transform 0.2s ease-in-out;
}

.beca-card:hover {
    transform: translateY(-2px);
}

.progress {
    background-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

@media (max-width: 768px) {
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Estilos para el modal de confirmación */
#modalConfirmacionDocumentos .modal-content {
    border-radius: 15px;
    overflow: hidden;
}

#modalConfirmacionDocumentos .modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    padding: 1.5rem;
}

#modalConfirmacionDocumentos .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

#modalConfirmacionDocumentos .modal-body {
    padding: 2rem 1.5rem;
}

#modalConfirmacionDocumentos .bi-question-circle {
    animation: pulse 2s infinite;
}

#modalConfirmacionDocumentos .alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #bee5eb;
    border-radius: 10px;
}

#modalConfirmacionDocumentos .modal-footer {
    padding: 1.5rem;
    gap: 1rem;
}

#modalConfirmacionDocumentos .btn {
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

#modalConfirmacionDocumentos .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<?= $this->endSection() ?>
