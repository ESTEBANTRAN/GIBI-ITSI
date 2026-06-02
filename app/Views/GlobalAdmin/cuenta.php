<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('index.php/global-admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Mi Cuenta</a></li>
        </ol>
    </div>

    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Mi Cuenta - Super Administrador</h4>
                    <p class="text-muted">Gestiona la configuración de tu cuenta y seguridad</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Content -->
    <div class="row">
        <!-- Change Password -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-lock me-2"></i>Cambiar Contraseña
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('index.php/global-admin/cuenta/cambiarPassword') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual *</label>
                            <input type="password" class="form-control" name="password_actual" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña *</label>
                            <input type="password" class="form-control" name="password_nueva" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña *</label>
                            <input type="password" class="form-control" name="password_confirmar" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-key me-2"></i>Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-bell me-2"></i>Configuración de Notificaciones
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('index.php/global-admin/cuenta/configuracionNotificaciones') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notif_email" id="notif_email" checked>
                                <label class="form-check-label" for="notif_email">
                                    Notificaciones por Email
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notif_sistema" id="notif_sistema" checked>
                                <label class="form-check-label" for="notif_sistema">
                                    Notificaciones del Sistema
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notif_seguridad" id="notif_seguridad" checked>
                                <label class="form-check-label" for="notif_seguridad">
                                    Alertas de Seguridad
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notif_respaldos" id="notif_respaldos">
                                <label class="form-check-label" for="notif_respaldos">
                                    Notificaciones de Respaldos
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-settings me-2"></i>Guardar Configuración
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Actions -->
    <div class="row">
        <!-- Export Data -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-download me-2"></i>Exportar Datos
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Descarga todos tus datos personales en formato JSON o CSV.</p>
                    <a href="<?= base_url('index.php/global-admin/cuenta/exportarDatos') ?>" class="btn btn-outline-primary">
                        <i class="ti ti-download me-2"></i>Exportar Datos
                    </a>
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-danger">
                        <i class="ti ti-trash me-2"></i>Eliminar Cuenta
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Esta acción eliminará permanentemente tu cuenta y todos los datos asociados.</p>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
                        <i class="ti ti-trash me-2"></i>Eliminar Cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Cuenta -->
<div class="modal fade" id="modalEliminarCuenta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
                </div>
                <p>¿Está seguro de que desea eliminar su cuenta?</p>
                <p class="text-muted">Todos sus datos serán eliminados permanentemente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= base_url('index.php/global-admin/cuenta/eliminarCuenta') ?>" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-2"></i>Eliminar Cuenta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?> 