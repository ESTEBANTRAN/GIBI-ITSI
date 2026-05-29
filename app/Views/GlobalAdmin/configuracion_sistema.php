<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Encabezado de la página -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-gear me-2 text-primary"></i>Configuración del Sistema</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('index.php/global-admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </nav>
        </div>
        <span class="badge bg-info p-2 shadow-sm"><i class="bi bi-arrow-repeat me-1"></i> Sincronizado en Tiempo Real</span>
    </div>

    <!-- Alertas y Mensajes -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Configuraciones del sistema -->
    <div class="row">
        
        <!-- Configuración General -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 border-start border-primary border-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-building me-2"></i>Configuración General</h6>
                    <span class="text-muted small">Datos institucionales básicos</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionGeneral">
                        <div class="mb-3">
                            <label for="nombre_institucion" class="form-label fw-bold">Nombre de la Institución</label>
                            <input type="text" class="form-control" id="nombre_institucion" name="nombre_institucion" value="<?= esc($configuracion['nombre_institucion'] ?? 'Instituto Tecnológico Superior de Informática') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_contacto" class="form-label fw-bold">Email de Contacto</label>
                            <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?= esc($configuracion['email_contacto'] ?? 'bienestar@itsi.edu.ec') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono_contacto" class="form-label fw-bold">Teléfono de Contacto</label>
                            <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" value="<?= esc($configuracion['telefono_contacto'] ?? '+593 2 1234567') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2" required><?= esc($configuracion['direccion'] ?? 'Av. Principal 123, Quito, Ecuador') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar General
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Correo SMTP (Gmail) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 border-start border-danger border-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-danger"><i class="bi bi-envelope me-2"></i>Configuración de Correo (SMTP)</h6>
                    <span class="text-muted small">Cuenta bienestar.itsi.info@gmail.com</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionCorreo">
                        <div class="mb-3">
                            <label for="gmail_correo" class="form-label fw-bold">Correo SMTP Institucional</label>
                            <input type="email" class="form-control" id="gmail_correo" name="gmail_correo" value="<?= esc($configuracion['gmail_correo'] ?? 'bienestar.itsi.info@gmail.com') ?>" required>
                            <small class="form-text text-muted">Todos los correos del sistema saldrán desde esta cuenta.</small>
                        </div>
                        <div class="mb-3">
                            <label for="gmail_clave" class="form-label fw-bold">Clave de Aplicación / Contraseña SMTP</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="gmail_clave" name="gmail_clave" value="<?= esc($configuracion['gmail_clave'] ?? '') ?>" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('gmail_clave')">
                                    <i class="bi bi-eye" id="eye-gmail_clave"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Use contraseñas de aplicación si utiliza verificación en dos pasos de Google.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="gmail_smtp_host" class="form-label fw-bold">Host SMTP</label>
                                    <input type="text" class="form-control" id="gmail_smtp_host" name="gmail_smtp_host" value="<?= esc($configuracion['gmail_smtp_host'] ?? 'smtp.gmail.com') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="gmail_smtp_port" class="form-label fw-bold">Puerto SMTP</label>
                                    <input type="number" class="form-control" id="gmail_smtp_port" name="gmail_smtp_port" value="<?= esc($configuracion['gmail_smtp_port'] ?? '587') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="gmail_smtp_crypto" class="form-label fw-bold">Cifrado SMTP</label>
                            <select class="form-select" id="gmail_smtp_crypto" name="gmail_smtp_crypto">
                                <option value="tls" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recomendado para puerto 587)</option>
                                <option value="ssl" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL (Recomendado para puerto 465)</option>
                                <option value="" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === '' ? 'selected' : '' ?>>Sin Cifrado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-save me-2"></i>Guardar Configuración SMTP
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <!-- Configuración de Respaldo en la Nube (Google Drive) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 border-start border-success border-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-success"><i class="bi bi-cloud-arrow-up me-2"></i>Almacenamiento en Google Drive</h6>
                    <span class="text-muted small">Sincronización en la nube híbrida</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionDrive">
                        <div class="mb-3">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" class="form-check-input" id="drive_activo" name="drive_activo" value="1" <?= ($configuracion['drive_activo'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold text-success" for="drive_activo">Activar Google Drive Mirroring</label>
                            </div>
                            <small class="form-text text-muted mb-3 d-block">Al activarlo, los documentos se guardarán de manera local en el servidor de la institución y simultáneamente en el almacenamiento de Google Drive.</small>
                        </div>
                        <div class="mb-3">
                            <label for="drive_client_id" class="form-label fw-bold">Google API Client ID</label>
                            <input type="text" class="form-control" id="drive_client_id" name="drive_client_id" value="<?= esc($configuracion['drive_client_id'] ?? '') ?>" placeholder="Ej: 12345678-abc.apps.googleusercontent.com">
                        </div>
                        <div class="mb-3">
                            <label for="drive_client_secret" class="form-label fw-bold">Google API Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="drive_client_secret" name="drive_client_secret" value="<?= esc($configuracion['drive_client_secret'] ?? '') ?>" placeholder="Ingrese su Client Secret">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('drive_client_secret')">
                                    <i class="bi bi-eye" id="eye-drive_client_secret"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="drive_refresh_token" class="form-label fw-bold">Google OAuth 2.0 Refresh Token</label>
                            <input type="text" class="form-control" id="drive_refresh_token" name="drive_refresh_token" value="<?= esc($configuracion['drive_refresh_token'] ?? '') ?>" placeholder="Refresh Token para bienestar.itsi.info@gmail.com">
                            <small class="form-text text-muted">Token persistente para la generación de sesiones de Google Drive sin caducidad.</small>
                        </div>
                        <div class="mb-3">
                            <label for="drive_folder_id" class="form-label fw-bold">Google Drive Folder ID (Opcional)</label>
                            <input type="text" class="form-control" id="drive_folder_id" name="drive_folder_id" value="<?= esc($configuracion['drive_folder_id'] ?? '') ?>" placeholder="ID de la carpeta destino en Google Drive">
                            <small class="form-text text-muted">Si se deja vacío, las subidas se guardarán directamente en la raíz de su Google Drive.</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-2"></i>Guardar Configuración Drive
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Seguridad -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 border-start border-warning border-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-warning"><i class="bi bi-shield-lock me-2"></i>Seguridad y Sesión</h6>
                    <span class="text-muted small">Directivas de protección del sistema</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionSeguridad">
                        <div class="mb-3">
                            <label for="tiempo_sesion" class="form-label fw-bold">Tiempo de Sesión Activa (minutos)</label>
                            <input type="number" class="form-control" id="tiempo_sesion" name="tiempo_sesion" value="<?= esc($configuracion['tiempo_sesion'] ?? '30') ?>" min="5" max="480">
                        </div>
                        <div class="mb-3">
                            <label for="intentos_login" class="form-label fw-bold">Intentos de Login Máximos (Bloqueo)</label>
                            <input type="number" class="form-control" id="intentos_login" name="intentos_login" value="<?= esc($configuracion['intentos_login'] ?? '3') ?>" min="1" max="10">
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="requerir_cambio_password" name="requerir_cambio_password" value="1" <?= ($configuracion['requerir_cambio_password'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold text-warning" for="requerir_cambio_password">Requerir Cambio Periódico de Contraseña</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dias_cambio_password" class="form-label fw-bold">Días para Expiración de Contraseña</label>
                            <input type="number" class="form-control" id="dias_cambio_password" name="dias_cambio_password" value="<?= esc($configuracion['dias_cambio_password'] ?? '90') ?>" min="30" max="365">
                        </div>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="bi bi-save me-2"></i>Guardar Seguridad
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Eventos para guardar configuración mediante AJAX dinámico
    $('#formConfiguracionGeneral').submit(function(e) {
        e.preventDefault();
        guardarConfiguracion('General');
    });

    $('#formConfiguracionCorreo').submit(function(e) {
        e.preventDefault();
        guardarConfiguracion('Correo');
    });

    $('#formConfiguracionDrive').submit(function(e) {
        e.preventDefault();
        guardarConfiguracion('Drive');
    });

    $('#formConfiguracionSeguridad').submit(function(e) {
        e.preventDefault();
        guardarConfiguracion('Seguridad');
    });

    // Confirmación modal al cambiar el switch de Google Drive
    $('#drive_activo').on('change', function(e) {
        const isChecked = $(this).is(':checked');
        const estadoTexto = isChecked ? 'activar' : 'desactivar';
        const checkbox = $(this);

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas ${estadoTexto} la sincronización con Google Drive?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isChecked ? '#1cc88a' : '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: `Sí, ${estadoTexto}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si confirma, guardamos la configuración de Drive automáticamente
                guardarConfiguracion('Drive');
            } else {
                // Si cancela, revertimos el estado del switch
                checkbox.prop('checked', !isChecked);
            }
        });
    });
});

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-' + fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}

function guardarConfiguracion(tipo) {
    const formElement = document.getElementById('formConfiguracion' + tipo);
    const formData = new FormData(formElement);
    
    // Si es un Switch/Checkbox que no está marcado, asegurarnos de enviar 0 en el AJAX
    $(formElement).find('input[type="checkbox"]').each(function() {
        if (!this.checked) {
            formData.append(this.name, '0');
        }
    });

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Aplicando cambios de ' + tipo,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url('index.php/global-admin/guardar-configuracion') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: '¡Excelente! La configuración de ' + tipo + ' se ha guardado y aplicado correctamente.',
                    timer: 3500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Configuración',
                    text: 'Error al guardar configuración: ' + (response.error || response.message),
                    confirmButtonColor: '#e74a3b'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'Error en la comunicación con el servidor. Por favor, intente nuevamente.',
                confirmButtonColor: '#e74a3b'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>