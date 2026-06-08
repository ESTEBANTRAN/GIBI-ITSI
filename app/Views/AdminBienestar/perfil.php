<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Mi Perfil</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <?php 
                            $foto_perfil = session('foto_perfil');
                            if ($foto_perfil && file_exists(FCPATH . 'sistema/assets/images/profile/' . $foto_perfil)) {
                                $foto_url = base_url('sistema/assets/images/profile/' . $foto_perfil);
                            } else {
                                $foto_url = base_url('sistema/assets/images/profile/user-1.jpg');
                            }
                            ?>
                            <img src="<?= $foto_url ?>" alt="Foto de perfil" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover; background-color: white; border: 3px solid #ffffff;">
                            
                            <h5 class="mb-1"><?= session('nombre') ?> <?= session('apellido') ?></h5>
                            <p class="text-muted mb-3">Administrador Bienestar</p>
                            
                            <div class="mb-3">
                                <label for="foto_perfil" class="form-label">Cambiar foto de perfil</label>
                                <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept=".jpg,.jpeg,.png,.gif">
                                <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 1MB.</div>
                            </div>
                            
                            <button type="button" class="btn btn-primary btn-sm" onclick="cambiarFotoPerfil()">
                                <i class="ti ti-upload me-1"></i>Subir foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <form id="perfilForm">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= session('nombre') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?= session('apellido') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= session('email') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?= session('telefono') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="3"><?= session('direccion') ?></textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function cambiarFotoPerfil() {
    const fileInput = document.getElementById('foto_perfil');
    const file = fileInput.files[0];
    
    if (!file) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona una imagen',
            text: 'Por favor selecciona una imagen para subir'
        });
        return;
    }
    
    // Validar tipo de archivo
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Tipo de archivo no válido',
            text: 'Solo se permiten archivos JPG, PNG o GIF'
        });
        return;
    }
    
    // Validar tamaño (1MB)
    if (file.size > 1048576) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El archivo no puede pesar más de 1MB'
        });
        return;
    }
    
    const formData = new FormData();
    formData.append('foto_perfil', file);
    
    // Mostrar loading
    Swal.fire({
        title: 'Subiendo foto...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= base_url('admin-bienestar/perfil/cambiarFoto') ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message
                    }).then(() => {
                        // Recargar la página para mostrar la nueva foto
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la respuesta del servidor'
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al subir la foto'
            });
        }
    };
    
    xhr.onerror = function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al subir la foto'
        });
    };
    
    xhr.send(formData);
}

// Manejar envío del formulario de perfil
document.getElementById('perfilForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Mostrar loading
    Swal.fire({
        title: 'Actualizando perfil...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= base_url('admin-bienestar/perfil/actualizar') ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message
                    }).then(() => {
                        // Recargar la página para mostrar los cambios
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la respuesta del servidor'
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el perfil'
            });
        }
    };
    
    xhr.onerror = function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al actualizar el perfil'
        });
    };
    
    xhr.send(formData);
});
</script>
<?= $this->endSection() ?> 