<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Mi Cuenta</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('index.php/admin-bienestar') ?>">Inicio</a></li>
                            <li class="breadcrumb-item active">Mi Cuenta</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cambiar Contraseña</h5>
                        <form id="formPassword">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_nuevo" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="password_nuevo" name="password_nuevo" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key"></i> Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Acciones de Cuenta</h5>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-info" onclick="exportarDatos()">
                                <i class="bi bi-download"></i> Exportar Mis Datos
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="configurarNotificaciones()">
                                <i class="bi bi-bell"></i> Configurar Notificaciones
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="eliminarCuenta()">
                                <i class="bi bi-trash"></i> Eliminar Cuenta
                            </button>
                        </div>
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
    $('#formPassword').on('submit', function(e) {
        e.preventDefault();
        
        const passwordNuevo = $('#password_nuevo').val();
        const passwordConfirmar = $('#password_confirmar').val();
        
        if (passwordNuevo !== passwordConfirmar) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('index.php/admin-bienestar/cuenta/cambiarPassword') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        $('#formPassword')[0].reset();
                    });
                } else {
                    Swal.fire('Error', response.error, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });
});

function exportarDatos() {
    Swal.fire({
        title: '¿Exportar datos?',
        text: 'Se descargará un archivo con todos tus datos personales',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, exportar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open('<?= base_url('index.php/admin-bienestar/cuenta/exportarDatos') ?>', '_blank');
        }
    });
}

function configurarNotificaciones() {
    Swal.fire({
        title: 'Configurar Notificaciones',
        text: 'Esta función estará disponible próximamente',
        icon: 'info'
    });
}

function eliminarCuenta() {
    Swal.fire({
        title: '¿Eliminar cuenta?',
        text: 'Esta acción no se puede deshacer. Se eliminarán todos tus datos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('index.php/admin-bienestar/cuenta/eliminarCuenta') ?>',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success').then(() => {
                            window.location.href = '<?= base_url('index.php/auth/logout') ?>';
                        });
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?> 