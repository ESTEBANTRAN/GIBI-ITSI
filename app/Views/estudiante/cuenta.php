<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-12 d-flex align-items-center justify-content-between mx-auto" style="max-width: 1000px;">
        <div class="d-flex align-items-center">
            <div>
                <h4 class="mb-1">Mi Cuenta</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('estudiante') ?>">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mi Cuenta</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 d-flex align-items-center justify-content-between mx-auto" style="max-width: 1000px;">
        <div class="row w-100">
            <!-- Cambiar Contraseña -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock me-2"></i>Cambiar Contraseña
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formCambiarPassword">
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña Actual *</label>
                                <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_nuevo" class="form-label">Nueva Contraseña *</label>
                                <input type="password" class="form-control" id="password_nuevo" name="password_nuevo" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña *</label>
                                <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Configuración de Notificaciones -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bell me-2"></i>Configuración de Notificaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formNotificaciones">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notif_email" name="notif_email" checked>
                                    <label class="form-check-label" for="notif_email">
                                        Notificaciones por Email
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notif_becas" name="notif_becas" checked>
                                    <label class="form-check-label" for="notif_becas">
                                        Notificaciones de Becas
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notif_solicitudes" name="notif_solicitudes" checked>
                                    <label class="form-check-label" for="notif_solicitudes">
                                        Notificaciones de Solicitudes
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notif_documentos" name="notif_documentos" checked>
                                    <label class="form-check-label" for="notif_documentos">
                                        Notificaciones de Documentos
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Guardar Configuración
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Exportar Datos -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-download me-2"></i>Exportar Mis Datos
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Descarga toda tu información personal, fichas socioeconómicas, solicitudes y documentos en formato JSON.</p>
                        <a href="<?= base_url('estudiante/exportar-datos') ?>" class="btn btn-info">
                            <i class="bi bi-download me-2"></i>Exportar Datos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Cambiar contraseña
    $('#formCambiarPassword').on('submit', function(e) {
        e.preventDefault();
        
        var passwordNuevo = $('#password_nuevo').val();
        var passwordConfirmar = $('#password_confirmar').val();
        
        if (passwordNuevo !== passwordConfirmar) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden'
            });
            return;
        }
        
        $.ajax({
            url: '<?= base_url('estudiante/cambiar-password') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    });
                    $('#formCambiarPassword')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cambiar la contraseña'
                });
            }
        });
    });
    
    // Configurar notificaciones
    $('#formNotificaciones').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('estudiante/configurar-notificaciones') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la configuración'
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 