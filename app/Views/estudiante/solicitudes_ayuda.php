<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('breadcrumb') ?>Solicitudes de Ayuda<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Solicitudes de Ayuda</h4>
                <p class="text-muted mb-0">Gestiona tus solicitudes de ayuda y apoyo</p>
            </div>
            <div class="page-title-right">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaSolicitud">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mis Solicitudes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-question-circle me-2"></i>Mis Solicitudes de Ayuda
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($solicitudes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Asunto</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha de Solicitud</th>
                                    <th>Responsable</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $categoria = null;
                                        if (!empty($solicitud['categoria_id'])) {
                                            foreach ($categorias as $cat) {
                                                if ($cat['id'] == $solicitud['categoria_id']) {
                                                    $categoria = $cat;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <?php if ($categoria): ?>
                                            <span class="badge" style="background-color: <?= $categoria['color'] ?>; color: white;">
                                                <i class="bi <?= $categoria['icono'] ?> me-1"></i>
                                                <?= htmlspecialchars($categoria['nombre']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin categoría</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($solicitud['asunto']) ?></strong>
                                        <?php if (!empty($solicitud['asunto_personalizado'])): ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($solicitud['asunto_personalizado']) ?></small>
                                        <?php else: ?>
                                            <br>
                                            <small class="text-muted"><?= substr(htmlspecialchars($solicitud['descripcion']), 0, 50) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $prioridadClass = '';
                                        switch ($solicitud['prioridad']) {
                                            case 'Baja':
                                                $prioridadClass = 'bg-secondary';
                                                break;
                                            case 'Media':
                                                $prioridadClass = 'bg-warning';
                                                break;
                                            case 'Alta':
                                                $prioridadClass = 'bg-danger';
                                                break;
                                            case 'Urgente':
                                                $prioridadClass = 'bg-dark';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $prioridadClass ?>"><?= $solicitud['prioridad'] ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $estadoClass = '';
                                        switch ($solicitud['estado']) {
                                            case 'Pendiente':
                                                $estadoClass = 'bg-warning';
                                                break;
                                            case 'En Proceso':
                                                $estadoClass = 'bg-info';
                                                break;
                                            case 'Resuelta':
                                                $estadoClass = 'bg-success';
                                                break;
                                            case 'Cerrada':
                                                $estadoClass = 'bg-secondary';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $estadoClass ?>"><?= $solicitud['estado'] ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></td>
                                    <td>
                                        <?= $solicitud['id_responsable'] ? 'Asignado' : 'Sin asignar' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="verSolicitud(<?= $solicitud['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <?php if ($solicitud['estado'] == 'Pendiente'): ?>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editarSolicitud(<?= $solicitud['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="cancelarSolicitud(<?= $solicitud['id'] ?>)">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Mostrando <?= (($current_page - 1) * $per_page) + 1 ?> a 
                                <?= min($current_page * $per_page, $total) ?> 
                                de <?= $total ?> registros
                            </small>
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page - 1 ?>">Anterior</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page + 1 ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-question-circle fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No tienes solicitudes de ayuda registradas</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaSolicitud">
                            <i class="bi bi-plus-circle me-2"></i>Crear Primera Solicitud
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Solicitud de Ayuda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevaSolicitud">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" name="categoria_id" id="categoriaSelect" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" 
                                                data-es-otro="<?= $categoria['nombre'] === 'Otro Asunto' ? '1' : '0' ?>">
                                            <i class="bi <?= $categoria['icono'] ?>"></i>
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" name="prioridad" required>
                                    <option value="">Seleccionar prioridad</option>
                                    <option value="Baja">Baja</option>
                                    <option value="Media">Media</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Asunto *</label>
                                <input type="text" class="form-control" name="asunto" id="asuntoInput" required 
                                       placeholder="Breve descripción del motivo de la solicitud">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="asuntoPersonalizadoDiv" style="display: none;">
                        <label class="form-label">Asunto Personalizado *</label>
                        <textarea class="form-control" name="asunto_personalizado" id="asuntoPersonalizadoInput" 
                                  rows="3" placeholder="Describe específicamente tu asunto personalizado..."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Como seleccionaste "Otro Asunto", por favor describe detalladamente tu solicitud.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción Detallada *</label>
                        <textarea class="form-control" name="descripcion" rows="5" required placeholder="Describe detalladamente tu solicitud de ayuda..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Información:</strong> Tu solicitud será revisada por el personal de Bienestar Estudiantil. Te notificaremos sobre cualquier actualización.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Solicitud -->
<div class="modal fade" id="modalVerSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Detalles de la Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="solicitudContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Solicitud -->
<div class="modal fade" id="modalEditarSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Solicitud de Ayuda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarSolicitud">
                <div class="modal-body">
                    <input type="hidden" id="editSolicitudId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" name="categoria_id" id="editCategoriaSelect" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" 
                                                data-es-otro="<?= $categoria['nombre'] === 'Otro Asunto' ? '1' : '0' ?>">
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" name="prioridad" id="editPrioridadSelect" required>
                                    <option value="">Seleccionar prioridad</option>
                                    <option value="Baja">Baja</option>
                                    <option value="Media">Media</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Asunto *</label>
                                <input type="text" class="form-control" name="asunto" id="editAsuntoInput" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="editAsuntoPersonalizadoDiv" style="display: none;">
                        <label class="form-label">Asunto Personalizado *</label>
                        <textarea class="form-control" name="asunto_personalizado" id="editAsuntoPersonalizadoInput" 
                                  rows="3" placeholder="Describe específicamente tu asunto personalizado..."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Como seleccionaste "Otro Asunto", por favor describe detalladamente tu solicitud.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción Detallada *</label>
                        <textarea class="form-control" name="descripcion" id="editDescripcionInput" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Animaciones para los modales */
.animate__animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -100%, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@keyframes fadeOutUp {
    from {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
    to {
        opacity: 0;
        transform: translate3d(0, -100%, 0);
    }
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}

.animate__fadeOutUp {
    animation-name: fadeOutUp;
}

/* Mejoras visuales para los modales */
.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border-radius: 15px;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    border-bottom: none;
}

.modal-header .btn-close {
    filter: invert(1);
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0 0 15px 15px;
}

/* Estilos para los botones de acción */
.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Mejoras para la tabla */
.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Estilos para los badges de categoría */
.badge {
    transition: all 0.3s ease;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 0.85em;
}

.badge:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Manejar cambio de categoría
document.getElementById('categoriaSelect').addEventListener('change', function() {
    const asuntoPersonalizadoDiv = document.getElementById('asuntoPersonalizadoDiv');
    const asuntoPersonalizadoInput = document.getElementById('asuntoPersonalizadoInput');
    const asuntoInput = document.getElementById('asuntoInput');
    
    const selectedOption = this.options[this.selectedIndex];
    const esOtroAsunto = selectedOption.getAttribute('data-es-otro') === '1';
    
    if (esOtroAsunto) {
        asuntoPersonalizadoDiv.style.display = 'block';
        asuntoPersonalizadoInput.required = true;
        asuntoInput.placeholder = 'Título general de tu solicitud';
    } else {
        asuntoPersonalizadoDiv.style.display = 'none';
        asuntoPersonalizadoInput.required = false;
        asuntoInput.placeholder = 'Breve descripción del motivo de la solicitud';
    }
});

// Crear nueva solicitud
document.getElementById('formNuevaSolicitud').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Mostrar loading
    Swal.fire({
        title: 'Enviando Solicitud...',
        text: 'Procesando tu solicitud de ayuda',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?= base_url('estudiante/crear-solicitud-ayuda') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud Enviada!',
                text: data.message,
                confirmButtonColor: '#28a745',
                confirmButtonText: '¡Perfecto!',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then(() => {
                // Cerrar modal y recargar página
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevaSolicitud'));
                modal.hide();
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al Enviar',
                text: data.error,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo enviar la solicitud. Verifique su conexión e intente nuevamente.',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Entendido'
        });
    });
});

// Ver solicitud
function verSolicitud(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalVerSolicitud'));
    document.getElementById('solicitudContent').innerHTML = `
        <div class="text-center">
            <i class="bi bi-hourglass-split fs-1 text-muted mb-3"></i>
            <p>Cargando detalles de la solicitud...</p>
        </div>
    `;
    modal.show();
    
    // Simular carga de datos
    setTimeout(() => {
        document.getElementById('solicitudContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información de la Solicitud</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Asunto:</strong></td><td>Ayuda económica para materiales</td></tr>
                        <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-warning">Media</span></td></tr>
                        <tr><td><strong>Estado:</strong></td><td><span class="badge bg-info">En Proceso</span></td></tr>
                        <tr><td><strong>Fecha Solicitud:</strong></td><td>01/10/2024</td></tr>
                        <tr><td><strong>Responsable:</strong></td><td>Ana Gomez</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Descripción</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">Necesito ayuda para comprar materiales de estudio para este semestre. Mi situación económica es complicada.</p>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <h6>Respuestas</h6>
                <div class="bg-light p-3 rounded">
                    <p class="mb-0"><strong>Ana Gomez:</strong> Hemos recibido tu solicitud y está siendo procesada. Te contactaremos pronto.</p>
                    <small class="text-muted">15/10/2024 14:30</small>
                </div>
            </div>
        `;
    }, 1000);
}

// Manejar cambio de categoría en modal de edición
document.getElementById('editCategoriaSelect').addEventListener('change', function() {
    const asuntoPersonalizadoDiv = document.getElementById('editAsuntoPersonalizadoDiv');
    const asuntoPersonalizadoInput = document.getElementById('editAsuntoPersonalizadoInput');
    const asuntoInput = document.getElementById('editAsuntoInput');
    
    const selectedOption = this.options[this.selectedIndex];
    const esOtroAsunto = selectedOption.getAttribute('data-es-otro') === '1';
    
    if (esOtroAsunto) {
        asuntoPersonalizadoDiv.style.display = 'block';
        asuntoPersonalizadoInput.required = true;
        asuntoInput.placeholder = 'Título general de tu solicitud';
    } else {
        asuntoPersonalizadoDiv.style.display = 'none';
        asuntoPersonalizadoInput.required = false;
        asuntoInput.placeholder = 'Breve descripción del motivo de la solicitud';
    }
});

// Editar solicitud
function editarSolicitud(id) {
    // Buscar la solicitud en el array de solicitudes
    const solicitud = <?= json_encode($solicitudes) ?>.find(s => s.id == id);
    if (!solicitud) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo encontrar la solicitud'
        });
        return;
    }
    
    // Llenar el formulario de edición
    document.getElementById('editSolicitudId').value = solicitud.id;
    document.getElementById('editCategoriaSelect').value = solicitud.categoria_id || '';
    document.getElementById('editPrioridadSelect').value = solicitud.prioridad;
    document.getElementById('editAsuntoInput').value = solicitud.asunto;
    document.getElementById('editAsuntoPersonalizadoInput').value = solicitud.asunto_personalizado || '';
    document.getElementById('editDescripcionInput').value = solicitud.descripcion;
    
    // Mostrar/ocultar campo personalizado según categoría
    const categoriaSelect = document.getElementById('editCategoriaSelect');
    const asuntoPersonalizadoDiv = document.getElementById('editAsuntoPersonalizadoDiv');
    const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
    const esOtroAsunto = selectedOption && selectedOption.getAttribute('data-es-otro') === '1';
    
    if (esOtroAsunto) {
        asuntoPersonalizadoDiv.style.display = 'block';
    } else {
        asuntoPersonalizadoDiv.style.display = 'none';
    }
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarSolicitud'));
    modal.show();
}

// Enviar formulario de edición
document.getElementById('formEditarSolicitud').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        id: formData.get('id'),
        categoria_id: formData.get('categoria_id'),
        asunto: formData.get('asunto'),
        asunto_personalizado: formData.get('asunto_personalizado'),
        descripcion: formData.get('descripcion'),
        prioridad: formData.get('prioridad')
    };
    
    Swal.fire({
        title: 'Guardando Cambios...',
        text: 'Actualizando solicitud de ayuda',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?= base_url('estudiante/editar-solicitud-ayuda') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.message,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Perfecto'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo actualizar la solicitud. Intente nuevamente.',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Entendido'
        });
    });
});

// Cancelar solicitud con confirmación mejorada
function cancelarSolicitud(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: '¿Desea cancelar esta solicitud de ayuda? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, Cancelar',
        cancelButtonText: 'No, Mantener',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Cancelando...',
                text: 'Procesando la cancelación',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('<?= base_url('estudiante/cancelar-solicitud-ayuda') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({id: id})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cancelada!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Perfecto'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo cancelar la solicitud. Intente nuevamente.',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
            });
        }
    });
}
</script>
<?= $this->endSection() ?> 
