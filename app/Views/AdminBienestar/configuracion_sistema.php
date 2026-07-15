<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Encabezado de la página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-cog text-primary mr-2"></i>Configuración del Sistema
        </h1>
        <span class="badge badge-info p-2 shadow-sm"><i class="fas fa-sync mr-1"></i> Sincronizado en Tiempo Real</span>
    </div>

    <!-- Alertas y Mensajes -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i><?= esc($error) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Configuraciones del sistema -->
    <div class="row">
        
        <!-- Configuración General -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-university mr-2"></i>Configuración General</h6>
                    <span class="text-xs text-muted">Datos institucionales básicos</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionGeneral">
                        <div class="form-group">
                            <label for="nombre_institucion" class="font-weight-bold">Nombre de la Institución</label>
                            <input type="text" class="form-control border-left-info" id="nombre_institucion" name="nombre_institucion" value="<?= esc($configuracion['nombre_institucion'] ?? 'Instituto Tecnologico Superior Ibarra') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email_contacto" class="font-weight-bold">Email de Contacto</label>
                            <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?= esc($configuracion['email_contacto'] ?? 'bienestar@itsi.edu.ec') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono_contacto" class="font-weight-bold">Teléfono de Contacto</label>
                            <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" value="<?= esc($configuracion['telefono_contacto'] ?? '+593 2 1234567') ?>">
                        </div>
                        <div class="form-group">
                            <label for="direccion" class="font-weight-bold">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2" required><?= esc($configuracion['direccion'] ?? 'Av. Principal 123, Quito, Ecuador') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-icon-split shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                            <span class="text">Guardar General</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Correo SMTP (Gmail) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-envelope mr-2"></i>Configuración de Correo (SMTP)</h6>
                    <span class="text-xs text-muted">Cuenta de correo institucional</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionCorreo">
                        <div class="form-group">
                            <label for="gmail_correo" class="font-weight-bold">Correo SMTP Institucional</label>
                            <input type="email" class="form-control border-left-danger" id="gmail_correo" name="gmail_correo" value="<?= esc($configuracion['gmail_correo'] ?? '') ?>" placeholder="correo@institucion.edu.ec" required>
                            <small class="form-text text-muted">Todos los correos del sistema saldrán desde esta cuenta.</small>
                        </div>
                        <div class="form-group">
                            <label for="gmail_clave" class="font-weight-bold">Clave de Aplicación / Contraseña SMTP</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="gmail_clave" name="gmail_clave" value="<?= esc($configuracion['gmail_clave'] ?? 'itsi1234bienestar') ?>" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('gmail_clave')">
                                        <i class="fas fa-eye" id="eye-gmail_clave"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Use contraseñas de aplicación si utiliza verificación en dos pasos de Google.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="gmail_smtp_host" class="font-weight-bold">Host SMTP</label>
                                    <input type="text" class="form-control" id="gmail_smtp_host" name="gmail_smtp_host" value="<?= esc($configuracion['gmail_smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="gmail_smtp_port" class="font-weight-bold">Puerto SMTP</label>
                                    <input type="number" class="form-control" id="gmail_smtp_port" name="gmail_smtp_port" value="<?= esc($configuracion['gmail_smtp_port'] ?? '587') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gmail_smtp_crypto" class="font-weight-bold">Cifrado SMTP</label>
                            <select class="form-control" id="gmail_smtp_crypto" name="gmail_smtp_crypto">
                                <option value="tls" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recomendado para puerto 587)</option>
                                <option value="ssl" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL (Recomendado para puerto 465)</option>
                                <option value="" <?= ($configuracion['gmail_smtp_crypto'] ?? 'tls') === '' ? 'selected' : '' ?>>Sin Cifrado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger btn-icon-split shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                            <span class="text">Guardar Configuración SMTP</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <!-- Configuración de Respaldo en la Nube (Google Drive) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fab fa-google-drive mr-2"></i>Almacenamiento en Google Drive</h6>
                    <span class="text-xs text-muted">Sincronización en la nube híbrida</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionDrive">
                        <div class="form-group">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="drive_activo" name="drive_activo" value="1" <?= ($configuracion['drive_activo'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold text-success" for="drive_activo">Activar Google Drive Mirroring</label>
                            </div>
                            <small class="form-text text-muted mb-3">Al activarlo, los documentos se guardarán de manera local en el servidor de la institución y simultáneamente en el almacenamiento de Google Drive.</small>
                        </div>
                        <div class="form-group">
                            <label for="drive_client_id" class="font-weight-bold">Google API Client ID</label>
                            <input type="text" class="form-control" id="drive_client_id" name="drive_client_id" value="<?= esc($configuracion['drive_client_id'] ?? '') ?>" placeholder="Ej: 12345678-abc.apps.googleusercontent.com">
                        </div>
                        <div class="form-group">
                            <label for="drive_client_secret" class="font-weight-bold">Google API Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="drive_client_secret" name="drive_client_secret" value="<?= !empty($configuracion['drive_client_secret']) ? '********' : '' ?>" placeholder="Ingrese su Client Secret">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('drive_client_secret')">
                                        <i class="fas fa-eye" id="eye-drive_client_secret"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="drive_refresh_token" class="font-weight-bold">Google OAuth 2.0 Refresh Token</label>
                            <input type="text" class="form-control border-left-success" id="drive_refresh_token" name="drive_refresh_token" value="<?= esc($configuracion['drive_refresh_token'] ?? '') ?>" placeholder="Refresh Token para la cuenta de Google Drive">
                            <small class="form-text text-muted">Token persistente para la generación de sesiones de Google Drive sin caducidad.</small>
                        </div>
                        <div class="form-group">
                            <label for="drive_folder_id" class="font-weight-bold">Google Drive Folder ID (Opcional)</label>
                            <input type="text" class="form-control" id="drive_folder_id" name="drive_folder_id" value="<?= esc($configuracion['drive_folder_id'] ?? '') ?>" placeholder="ID de la carpeta destino en Google Drive">
                            <small class="form-text text-muted">Si se deja vacío, las subidas se guardarán directamente en la raíz de su Google Drive.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-icon-split shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                            <span class="text">Guardar Configuración Drive</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Seguridad -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-shield-alt mr-2"></i>Seguridad y Sesión</h6>
                    <span class="text-xs text-muted">Directivas de protección del sistema</span>
                </div>
                <div class="card-body">
                    <form id="formConfiguracionSeguridad">
                        <div class="form-group">
                            <label for="tiempo_sesion" class="font-weight-bold">Tiempo de Sesión Activa (minutos)</label>
                            <input type="number" class="form-control" id="tiempo_sesion" name="tiempo_sesion" value="<?= esc($configuracion['tiempo_sesion'] ?? '30') ?>" min="5" max="480">
                        </div>
                        <div class="form-group">
                            <label for="intentos_login" class="font-weight-bold">Intentos de Login Máximos (Bloqueo)</label>
                            <input type="number" class="form-control" id="intentos_login" name="intentos_login" value="<?= esc($configuracion['intentos_login'] ?? '3') ?>" min="1" max="10">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="requerir_cambio_password" name="requerir_cambio_password" value="1" <?= ($configuracion['requerir_cambio_password'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold text-warning" for="requerir_cambio_password">Requerir Cambio Periódico de Contraseña</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dias_cambio_password" class="font-weight-bold">Días para Expiración de Contraseña</label>
                            <input type="number" class="form-control" id="dias_cambio_password" name="dias_cambio_password" value="<?= esc($configuracion['dias_cambio_password'] ?? '90') ?>" min="30" max="365">
                        </div>
                        <button type="submit" class="btn btn-warning btn-icon-split text-white shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                            <span class="text">Guardar Seguridad</span>
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
});

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-' + fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
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

    $.ajax({
        url: '<?= base_url('admin-bienestar/guardar-configuracion') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                mostrarExito('¡Excelente! La configuración de ' + tipo + ' se ha guardado y aplicado correctamente.');
            } else {
                mostrarError('Error al guardar configuración: ' + (response.error || response.message));
            }
        },
        error: function(xhr, status, error) {
            mostrarError('Error en la comunicación con el servidor. Por favor, intente nuevamente.');
        }
    });
}

function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Operación Exitosa!',
        text: mensaje,
        timer: 3500,
        showConfirmButton: false,
        background: '#ffffff',
        customClass: {
            title: 'text-success font-weight-bold',
            popup: 'shadow-lg border-radius-15'
        }
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error de Configuración',
        text: mensaje,
        background: '#ffffff',
        confirmButtonColor: '#e74a3b',
        customClass: {
            title: 'text-danger font-weight-bold',
            popup: 'shadow-lg border-radius-15'
        }
    });
}
</script>
<?= $this->endSection() ?>