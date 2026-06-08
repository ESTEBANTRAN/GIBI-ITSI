<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Mi Cuenta - Administrador</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Cambiar Contraseña -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock me-2"></i>Cambiar Contraseña
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('cuenta/cambiarPassword') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Contraseña Actual</label>
                                        <input type="password" class="form-control" name="password_actual" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password_nuevo" minlength="6" required>
                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password_confirmar" minlength="6" required>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-1"></i>Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Configuración de Notificaciones -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bell me-2"></i>Configuración de Notificaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('cuenta/configuracionNotificaciones') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="notificaciones_email" id="notificacionesEmail" checked>
                                            <label class="form-check-label" for="notificacionesEmail">
                                                Notificaciones por Email
                                            </label>
                                            <small class="text-muted d-block">Solicitudes nuevas, actualizaciones de estado</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="notificaciones_sms" id="notificacionesSMS">
                                            <label class="form-check-label" for="notificacionesSMS">
                                                Notificaciones por SMS
                                            </label>
                                            <small class="text-muted d-block">Alertas urgentes por mensaje de texto</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="notificaciones_push" id="notificacionesPush" checked>
                                            <label class="form-check-label" for="notificacionesPush">
                                                Notificaciones Push
                                            </label>
                                            <small class="text-muted d-block">Notificaciones en tiempo real</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="notificacionesReportes" checked>
                                            <label class="form-check-label" for="notificacionesReportes">
                                                Reportes Automáticos
                                            </label>
                                            <small class="text-muted d-block">Reportes semanales y mensuales</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-save me-1"></i>Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Configuración de Acceso -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Configuración de Acceso
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sesión Automática</label>
                                    <select class="form-select">
                                        <option value="0">Desactivada</option>
                                        <option value="1" selected>1 hora</option>
                                        <option value="4">4 horas</option>
                                        <option value="8">8 horas</option>
                                        <option value="24">24 horas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Verificación en Dos Pasos</label>
                                    <select class="form-select">
                                        <option value="0">Desactivada</option>
                                        <option value="1" selected>Activada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Idioma de Interfaz</label>
                                    <select class="form-select">
                                        <option value="es" selected>Español</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Zona Horaria</label>
                                    <select class="form-select">
                                        <option value="America/Guayaquil" selected>Ecuador (GMT-5)</option>
                                        <option value="America/New_York">Nueva York (GMT-5)</option>
                                        <option value="Europe/Madrid">Madrid (GMT+1)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-outline-secondary">
                                <i class="bi bi-save me-1"></i>Guardar Configuración
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Información de Seguridad -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-check me-2"></i>Seguridad de la Cuenta
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title text-white">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Contraseña Segura</h6>
                                <small class="text-muted">Última actualización: Hace 15 días</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title text-white">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Email Verificado</h6>
                                <small class="text-muted"><?= session('email') ?></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title text-white">
                                        <i class="bi bi-clock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Último Acceso</h6>
                                <small class="text-muted">Hace 1 hora</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title text-white">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Verificación 2FA</h6>
                                <small class="text-muted">Activada</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actividad de la Cuenta -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-activity me-2"></i>Actividad Reciente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Sesión iniciada</h6>
                                    <small class="text-muted">Hace 1 hora</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Solicitud aprobada</h6>
                                    <small class="text-muted">Hace 2 horas</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Reporte generado</h6>
                                    <small class="text-muted">Hace 1 día</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Usuario creado</h6>
                                    <small class="text-muted">Hace 3 días</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exportar Datos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-download me-2"></i>Exportar Datos
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Descarga una copia de todos tus datos y actividad.</p>
                        <a href="<?= base_url('cuenta/exportarDatos') ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download me-1"></i>Exportar Datos
                        </a>
                    </div>
                </div>

                <!-- Eliminar Cuenta -->
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-trash me-2"></i>Zona de Peligro
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                        </div>
                        <p class="text-muted small">
                            Al eliminar tu cuenta, perderás acceso a todas las funciones administrativas.
                        </p>
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmarEliminacion()">
                            <i class="bi bi-trash me-1"></i>Eliminar Cuenta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar Cuenta -->
<div class="modal fade" id="eliminarCuentaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar tu cuenta de administrador?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                <form action="<?= base_url('cuenta/eliminarCuenta') ?>" method="post" id="formEliminarCuenta">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Confirma tu contraseña</label>
                        <input type="password" class="form-control" name="password_confirmar" required>
                        <small class="text-muted">Debes escribir tu contraseña para confirmar</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEliminarCuenta" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion() {
    const modal = new bootstrap.Modal(document.getElementById('eliminarCuentaModal'));
    modal.show();
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 12px;
    width: 2px;
    height: calc(100% + 8px);
    background-color: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}
</style>

<?= $this->endSection() ?> 