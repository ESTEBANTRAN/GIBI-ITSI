<?= $this->extend('layouts/mainEstudiante') ?>

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
                            <p class="text-muted mb-3">Estudiante</p>
                            
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
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="carrera" class="form-label">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" name="carrera" value="<?= session('carrera') ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="semestre" class="form-label">Semestre</label>
                                        <input type="text" class="form-control" id="semestre" name="semestre" value="<?= session('semestre') ?>" readonly>
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

        <!-- Sección de Información y Servicios -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Información y Servicios de Bienestar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ti ti-lightbulb me-2"></i>
                            <strong>Conoce todos los servicios:</strong> Accede a información detallada sobre cada dependencia de bienestar institucional.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-award text-primary" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Dependencia de Becas</h6>
                                        <p class="card-text small">Información sobre becas, ayudas económicas y programas de apoyo.</p>
                                        <a href="<?= base_url('estudiante/informacion/becas') ?>" class="btn btn-primary btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-heart text-success" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Apoyo Psicológico</h6>
                                        <p class="card-text small">Servicios de atención psicológica y apoyo emocional.</p>
                                        <a href="<?= base_url('estudiante/informacion/psicologia') ?>" class="btn btn-success btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-danger h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-heart-pulse text-danger" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Servicios de Salud</h6>
                                        <p class="card-text small">Atención médica, prevención y promoción de la salud.</p>
                                        <a href="<?= base_url('estudiante/informacion/salud') ?>" class="btn btn-danger btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-people text-warning" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Trabajo Social</h6>
                                        <p class="card-text small">Apoyo socioeconómico y orientación social.</p>
                                        <a href="<?= base_url('estudiante/informacion/trabajo-social') ?>" class="btn btn-warning btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-mortarboard text-info" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Orientación Académica</h6>
                                        <p class="card-text small">Asesoramiento académico y apoyo para el desarrollo estudiantil.</p>
                                        <a href="<?= base_url('estudiante/informacion/orientacion-academica') ?>" class="btn btn-info btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-secondary h-100">
                                    <div class="card-body text-center">
                                        <i class="ti ti-building text-secondary" style="font-size: 2.5rem;"></i>
                                        <h6 class="card-title mt-2">Unidad de Bienestar</h6>
                                        <p class="card-text small">Información general sobre la Unidad de Bienestar Institucional.</p>
                                        <a href="<?= base_url('estudiante/informacion/servicios') ?>" class="btn btn-secondary btn-sm">
                                            <i class="ti ti-arrow-right me-1"></i>Ver más
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Comentarios de Rechazo -->
        <?php if (!empty($fichasRechazadas)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Comentarios de Rechazo de Fichas Socioeconómicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Importante:</strong> Las siguientes fichas socioeconómicas han sido rechazadas. 
                            Por favor, revisa los comentarios y corrige la información antes de volver a enviar.
                        </div>
                        
                        <?php foreach ($fichasRechazadas as $ficha): ?>
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">
                                    <i class="ti ti-calendar me-1"></i>
                                    Período: <?= $ficha['periodo_nombre'] ?>
                                </h6>
                                <span class="badge bg-danger">Rechazada</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="ti ti-clock me-1"></i>
                                    Fecha de revisión: <?= date('d/m/Y H:i', strtotime($ficha['fecha_revision_admin'])) ?>
                                </small>
                            </div>
                            <div class="alert alert-danger mb-0">
                                <strong>Motivo del rechazo:</strong><br>
                                <?= nl2br(htmlspecialchars($ficha['observaciones_admin'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
    xhr.open('POST', '<?= base_url('profile/cambiar-foto') ?>', true);
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

// Manejar envío del formulario de perfil con AJAX real
document.getElementById('perfilForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'Guardando cambios...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?= base_url("index.php/estudiante/actualizar-perfil") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Perfil actualizado',
                text: data.message
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al actualizar el perfil'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor'
        });
    });
});
</script>
<?= $this->endSection() ?> 
