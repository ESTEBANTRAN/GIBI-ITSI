<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Mi Perfil - Estudiante</h4>
                    <div class="page-title-right">
                        <button class="btn btn-primary" onclick="guardarPerfil()">
                            <i class="bi bi-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <!-- Información del Perfil -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="position-relative mb-3">
                            <img src="<?= base_url('uploads/perfiles/' . (session('foto_perfil') ?: 'default.jpg')) ?>" 
                                 alt="Foto de Perfil" 
                                 class="rounded-circle" 
                                 width="120" 
                                 height="120"
                                 style="object-fit: cover;">
                            <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0" 
                                    onclick="document.getElementById('fotoInput').click()">
                                <i class="bi bi-camera"></i>
                            </button>
                            <input type="file" id="fotoInput" accept="image/*" style="display: none;" onchange="cambiarFoto(this)">
                        </div>
                        <h5 class="card-title"><?= session('nombre') ?> <?= session('apellido') ?></h5>
                        <p class="text-muted">Estudiante</p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-primary"><?= session('carrera') ?></span>
                            <span class="badge bg-info">Semestre <?= session('semestre') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-envelope text-primary me-2"></i>
                            <span><?= session('email') ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone text-success me-2"></i>
                            <span><?= session('telefono') ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt text-warning me-2"></i>
                            <span><?= session('direccion') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Formulario de Edición -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Editar Información Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formPerfil" action="<?= base_url('perfil/actualizar') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" value="<?= session('nombre') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" class="form-control" name="apellido" value="<?= session('apellido') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" name="email" value="<?= session('email') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="telefono" value="<?= session('telefono') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control" name="direccion" rows="3" required><?= session('direccion') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Carrera</label>
                                        <select class="form-select" name="carrera" required>
                                            <option value="">Seleccionar carrera</option>
                                            <option value="Ingeniería en Sistemas" <?= session('carrera') == 'Ingeniería en Sistemas' ? 'selected' : '' ?>>Ingeniería en Sistemas</option>
                                            <option value="Ingeniería Industrial" <?= session('carrera') == 'Ingeniería Industrial' ? 'selected' : '' ?>>Ingeniería Industrial</option>
                                            <option value="Ingeniería Mecánica" <?= session('carrera') == 'Ingeniería Mecánica' ? 'selected' : '' ?>>Ingeniería Mecánica</option>
                                            <option value="Ingeniería Eléctrica" <?= session('carrera') == 'Ingeniería Eléctrica' ? 'selected' : '' ?>>Ingeniería Eléctrica</option>
                                            <option value="Administración de Empresas" <?= session('carrera') == 'Administración de Empresas' ? 'selected' : '' ?>>Administración de Empresas</option>
                                            <option value="Contabilidad" <?= session('carrera') == 'Contabilidad' ? 'selected' : '' ?>>Contabilidad</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Semestre</label>
                                        <select class="form-select" name="semestre" required>
                                            <option value="">Seleccionar semestre</option>
                                            <?php for($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?= $i ?>" <?= session('semestre') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Cambiar Contraseña (Opcional)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password" minlength="6">
                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" name="confirm_password" minlength="6">
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function guardarPerfil() {
    document.getElementById('formPerfil').submit();
}

function cambiarFoto(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('foto', input.files[0]);
        const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value;
        if (csrfToken) formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('perfil/cambiarFoto') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar imagen en la página
                const img = document.querySelector('.rounded-circle');
                img.src = data.url;
                
                // Mostrar notificación
                mostrarNotificacion('Foto de perfil actualizada exitosamente', 'success');
            } else {
                mostrarNotificacion(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error al cambiar la foto', 'error');
        });
    }
}

function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notificacion.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?> 