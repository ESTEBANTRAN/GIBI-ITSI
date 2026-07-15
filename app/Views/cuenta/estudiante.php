<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Mi Cuenta - Estudiante</h4>
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
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notificaciones_email" id="notificacionesEmail" checked>
                                    <label class="form-check-label" for="notificacionesEmail">
                                        Recibir notificaciones por correo electrónico
                                    </label>
                                    <small class="text-muted d-block">Recibirás actualizaciones sobre tus solicitudes y becas</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notificaciones_sms" id="notificacionesSMS">
                                    <label class="form-check-label" for="notificacionesSMS">
                                        Recibir notificaciones por SMS
                                    </label>
                                    <small class="text-muted d-block">Notificaciones urgentes por mensaje de texto</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notificaciones_push" id="notificacionesPush">
                                    <label class="form-check-label" for="notificacionesPush">
                                        Recibir notificaciones push
                                    </label>
                                    <small class="text-muted d-block">Notificaciones en tiempo real en el navegador</small>
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

                <!-- Exportar Datos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-download me-2"></i>Exportar Mis Datos
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Descarga una copia de todos tus datos personales en formato JSON.</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Información incluida:</strong> Datos personales, información académica, historial de solicitudes y becas.
                        </div>
                        <a href="<?= base_url('cuenta/exportarDatos') ?>" class="btn btn-outline-success">
                            <i class="bi bi-download me-1"></i>Exportar Datos
                        </a>
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
                                <small class="text-muted">Última actualización: Hace 30 días</small>
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
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title text-white">
                                        <i class="bi bi-clock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Último Acceso</h6>
                                <small class="text-muted">Hace 2 horas</small>
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
                                    <small class="text-muted">Hace 2 horas</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Solicitud de beca enviada</h6>
                                    <small class="text-muted">Hace 1 día</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Formulario socioeconómico completado</h6>
                                    <small class="text-muted">Hace 3 días</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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