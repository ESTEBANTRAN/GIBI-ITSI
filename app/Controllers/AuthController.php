<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Security\RateLimiter;
use App\Security\SecurityLogger;
use App\Security\SessionGuard;
use App\Security\SecurityHelper;
use App\Helpers\RecaptchaHelper;
use App\Helpers\EmailHelper;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya está logueado, redirigir según el rol
        if (session('id')) {
            $rol_id = session('rol_id');
            
            if ($rol_id == 1) {
                return redirect()->to('/estudiante');
            } elseif ($rol_id == 2) {
                return redirect()->to('/admin-bienestar');
            } elseif ($rol_id == 4) {
                return redirect()->to('/global-admin/dashboard');
            } else {
                // Rol no válido, destruir sesión
                session()->destroy();
                return view('auth/login');
            }
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $securityLogger = new SecurityLogger();
        $ip = SecurityHelper::getClientIp();

        // ====== 1. Rate Limiting por IP ======
        $rateLimiter = new RateLimiter(10, 900, 'login_rate'); // 10 intentos en 15 min
        if ($rateLimiter->tooManyAttempts($ip)) {
            $waitTime = $rateLimiter->getFormattedWaitTime($ip);
            $securityLogger->logRateLimited($ip, $rateLimiter->getAttempts($ip));
            return redirect()->back()->withInput()->with('error', 
                "Demasiados intentos de inicio de sesión. Intente de nuevo en {$waitTime}."
            );
        }

        // ====== 2. Validar reCAPTCHA ======
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!RecaptchaHelper::validar($recaptchaResponse)) {
            return redirect()->back()->withInput()->with('error', 
                'Verificación de seguridad fallida. Por favor, complete el CAPTCHA nuevamente.'
            );
        }

        // ====== 3. Validar los datos de entrada ======
        $rules = [
            'identificador' => 'required',
            'password'      => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor complete todos los campos.');
        }

        // ====== 4. Obtener datos del formulario ======
        $identifier = trim($this->request->getPost('identificador'));
        $password = $this->request->getPost('password');

        log_message('debug', 'AuthController::attemptLogin - Intentando login con identificador: ' . SecurityHelper::maskEmail($identifier));

        // ====== 5. Buscar usuario ======
        $model = new UsuarioModel();
        $user = $model->findUserByIdentifier($identifier);

        if (!$user) {
            // Registrar intento en rate limiter
            $rateLimiter->hit($ip);
            $securityLogger->logLoginFailed($identifier, 'Usuario no encontrado');
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        // ====== 6. Verificar si la cuenta está bloqueada ======
        if ($model->usuarioBloqueado((int)$user['id'])) {
            $securityLogger->logLoginFailed($identifier, 'Cuenta bloqueada');
            return redirect()->back()->withInput()->with('error', 
                'Su cuenta está temporalmente bloqueada por múltiples intentos fallidos. Intente más tarde.'
            );
        }

        // ====== 7. Verificar contraseña ======
        if (!password_verify($password, $user['password_hash'])) {
            // Incrementar intentos fallidos
            $rateLimiter->hit($ip);
            $model->incrementarIntentosFallidos((int)$user['id']);
            $securityLogger->logLoginFailed($identifier, 'Contraseña incorrecta');

            // Verificar si se acaba de bloquear
            $remainingAttempts = 5 - (int)($user['intentos_fallidos'] ?? 0) - 1;
            if ($remainingAttempts <= 0) {
                $securityLogger->logAccountLocked($identifier, (int)$user['id']);
                return redirect()->back()->withInput()->with('error', 
                    'Cuenta bloqueada por 30 minutos debido a múltiples intentos fallidos.'
                );
            }

            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        log_message('debug', 'AuthController::attemptLogin - Credenciales correctas, configurando sesión');

        // ====== 8. Login exitoso ======
        // Resetear intentos fallidos y rate limiter
        $model->resetearIntentosFallidos((int)$user['id']);
        $model->actualizarUltimoAcceso((int)$user['id']);
        $rateLimiter->clear($ip);

        // Configurar sesión
        $this->setSession($user);

        // Protección avanzada de sesión
        $sessionGuard = new SessionGuard();
        $sessionGuard->initializeSession();

        // Registrar login exitoso
        $securityLogger->logLoginSuccess($identifier, (int)$user['id']);
        
        // ====== 9. Redirigir según el rol ======
        $rol_id = $user['rol_id'];
        
        if ($rol_id == 1) {
            return redirect()->to('/estudiante');
        } elseif ($rol_id == 2) {
            return redirect()->to('/admin-bienestar');
        } elseif ($rol_id == 4) {
            return redirect()->to('/global-admin/dashboard');
        } else {
            session()->destroy();
            return redirect()->back()->withInput()->with('error', 'Rol de usuario no válido.');
        }
    }

    /**
     * Guarda los datos del usuario en la sesión.
     */
    private function setSession($user)
    {
        session()->set([
            'id'        => $user['id'],
            'rol_id'    => $user['rol_id'],
            'nombre'    => $user['nombre'],
            'apellido'  => $user['apellido'],
            'cedula'    => $user['cedula'],
            'email'     => $user['email'],
            'telefono'  => $user['telefono'],
            'direccion' => $user['direccion'],
            'carrera'   => $user['carrera'],
            'semestre'  => $user['semestre'],
            'foto_perfil' => $user['foto_perfil'],
            'isLoggedIn' => true,
        ]);
    }
    
    /**
     * Cierra la sesión del usuario.
     */
    public function logout()
    {
        // Registrar logout
        $userId = session()->get('id');
        if ($userId) {
            $securityLogger = new SecurityLogger();
            $securityLogger->logLogout((int)$userId);
        }

        // Destruir sesión de forma segura
        $sessionGuard = new SessionGuard();
        $sessionGuard->destroySession();

        return redirect()->to('/login');
    }

    // ========================================================================
    //  FUNCIONALIDAD: RECUPERACIÓN DE CONTRASEÑA
    // ========================================================================

    /**
     * Muestra el formulario de "¿Olvidó su contraseña?"
     */
    public function forgotPassword()
    {
        // Si ya está logueado, redirigir
        if (session('id')) {
            return redirect()->to('/');
        }

        return view('auth/forgot_password');
    }

    /**
     * Verifica la identidad del usuario (cédula + email).
     * Si los datos coinciden, genera un token temporal y redirige al formulario de reset.
     */
    public function verifyIdentity()
    {
        // Rate limiting para prevenir abuso
        $ip = SecurityHelper::getClientIp();
        $rateLimiter = new RateLimiter(5, 900, 'forgot_pw_rate'); // 5 intentos en 15 min
        if ($rateLimiter->tooManyAttempts($ip)) {
            return redirect()->back()->withInput()->with('error', 
                'Demasiados intentos. Espere 15 minutos antes de intentar de nuevo.'
            );
        }

        // Validar reCAPTCHA
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!RecaptchaHelper::validar($recaptchaResponse)) {
            return redirect()->back()->withInput()->with('error', 
                'Verificación de seguridad fallida. Complete el CAPTCHA.'
            );
        }

        // Validar campos
        $rules = [
            'cedula' => 'required|min_length[4]|max_length[10]',
            'email'  => 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor complete todos los campos correctamente.');
        }

        $cedula = trim($this->request->getPost('cedula'));
        $email  = trim($this->request->getPost('email'));

        // Buscar usuario con cédula Y email coincidentes
        $model = new UsuarioModel();
        $user = $model->findByEmailAndCedula($email, $cedula);

        if (!$user) {
            $rateLimiter->hit($ip);
            log_message('warning', "Intento fallido de recuperación de contraseña - Cédula: {$cedula}, Email: {$email}, IP: {$ip}");
            
            // Mensaje genérico para no revelar información
            return redirect()->back()->withInput()->with('error', 
                'No se encontró una cuenta activa con la combinación de cédula y correo electrónico proporcionados.'
            );
        }

        // Generar token temporal seguro
        $token = bin2hex(random_bytes(32));
        $expiration = time() + 600; // 10 minutos

        // Guardar token en sesión (no en BD para simplicidad)
        session()->set([
            'reset_token'       => $token,
            'reset_user_id'     => $user['id'],
            'reset_user_name'   => $user['nombre'] . ' ' . $user['apellido'],
            'reset_expiration'  => $expiration,
        ]);

        log_message('info', "Token de recuperación generado para usuario ID: {$user['id']}, IP: {$ip}");

        // Construir enlace de recuperación
        $resetLink = base_url("reset-password/{$token}");

        // Construir plantilla de correo HTML premium
        $subject = 'Recuperación de Contraseña - Sistema GIBI-ITSI';
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f6f9; color: #333333; margin: 0; padding: 20px; }
                .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #4e73df, #224abe); padding: 30px; text-align: center; color: #ffffff; }
                .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
                .body { padding: 40px 30px; line-height: 1.6; }
                .btn { display: inline-block; padding: 12px 30px; background: #4e73df; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; text-align: center; box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2); }
                .footer { background: #f8f9fc; padding: 20px; text-align: center; font-size: 12px; color: #858796; border-top: 1px solid #e3e6f0; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='header'>
                    <h1>Recuperación de Contraseña</h1>
                </div>
                <div class='body'>
                    <p>Hola, <strong>" . esc($user['nombre']) . " " . esc($user['apellido']) . "</strong>,</p>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta en el <strong>Sistema Web de Bienestar Institucional (GIBI-ITSI)</strong>.</p>
                    <p>Para continuar con el proceso, por favor haga clic en el siguiente enlace de un solo uso:</p>
                    <div style='text-align: center;'>
                        <a href='{$resetLink}' class='btn' style='color: #ffffff;'>Restablecer Contraseña</a>
                    </div>
                    <p style='font-size: 13px; color: #e74a3b;'><strong>Nota importante:</strong> Este enlace es de uso único y expirará automáticamente en 10 minutos por razones de seguridad.</p>
                    <p>Si usted no solicitó este cambio, puede ignorar este mensaje de forma segura. Su contraseña actual no se verá afectada.</p>
                </div>
                <div class='footer'>
                    &copy; " . date('Y') . " Unidad de Bienestar Estudiantil - ITSI<br>
                    Este es un correo automático, por favor no responda a este mensaje.
                </div>
            </div>
        </body>
        </html>
        ";

        // Enviar el correo usando EmailHelper
        if (EmailHelper::enviarCorreo($email, $subject, $message)) {
            return redirect()->to('/forgot-password')->with('success', 
                'Se ha enviado un enlace de recuperación de contraseña a su correo electrónico registrado. Por favor, revise su bandeja de entrada (y su carpeta de spam).'
            );
        } else {
            return redirect()->back()->withInput()->with('error', 
                'No se pudo enviar el correo de recuperación debido a un problema técnico. Intente de nuevo o contacte al administrador.'
            );
        }
    }

    /**
     * Muestra el formulario para establecer nueva contraseña.
     */
    public function resetPasswordForm(string $token)
    {
        // Validar token
        $sessionToken = session()->get('reset_token');
        $expiration   = session()->get('reset_expiration');
        $userName     = session()->get('reset_user_name');

        if (!$sessionToken || $sessionToken !== $token) {
            return redirect()->to('/forgot-password')->with('error', 
                'Enlace inválido o expirado. Solicite una nueva verificación.'
            );
        }

        if (time() > $expiration) {
            $this->limpiarTokenReset();
            return redirect()->to('/forgot-password')->with('error', 
                'El enlace ha expirado. Solicite una nueva verificación.'
            );
        }

        $tiempoRestante = $expiration - time();

        return view('auth/reset_password', [
            'reset_token'     => $token,
            'nombre_usuario'  => $userName,
            'tiempo_restante' => $tiempoRestante,
        ]);
    }

    /**
     * Procesa el cambio de contraseña.
     */
    public function resetPassword()
    {
        $token = $this->request->getPost('reset_token');

        // Validar token
        $sessionToken = session()->get('reset_token');
        $expiration   = session()->get('reset_expiration');
        $userId       = session()->get('reset_user_id');

        if (!$sessionToken || $sessionToken !== $token || !$userId) {
            return redirect()->to('/forgot-password')->with('error', 
                'Enlace inválido. Solicite una nueva verificación.'
            );
        }

        if (time() > $expiration) {
            $this->limpiarTokenReset();
            return redirect()->to('/forgot-password')->with('error', 
                'El enlace ha expirado. Solicite una nueva verificación.'
            );
        }

        // Validar nueva contraseña
        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($newPassword) || strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        if (!preg_match('/[A-Z]/', $newPassword)) {
            return redirect()->back()->with('error', 'La contraseña debe contener al menos una letra mayúscula.');
        }

        if (!preg_match('/[a-z]/', $newPassword)) {
            return redirect()->back()->with('error', 'La contraseña debe contener al menos una letra minúscula.');
        }

        if (!preg_match('/[0-9]/', $newPassword)) {
            return redirect()->back()->with('error', 'La contraseña debe contener al menos un número.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Las contraseñas no coinciden.');
        }

        // Actualizar contraseña en la base de datos
        $model = new UsuarioModel();
        $success = $model->updatePassword((int)$userId, $newPassword);

        if (!$success) {
            return redirect()->back()->with('error', 'Error al actualizar la contraseña. Intente nuevamente.');
        }

        // Limpiar datos de reset
        $this->limpiarTokenReset();

        // Registrar el evento
        $ip = SecurityHelper::getClientIp();
        log_message('info', "Contraseña restablecida exitosamente para usuario ID: {$userId}, IP: {$ip}");

        return redirect()->to('/login')->with('success', 
            '¡Contraseña actualizada exitosamente! Inicie sesión con su nueva contraseña.'
        );
    }

    /**
     * Limpia los datos del token de reset de la sesión.
     */
    private function limpiarTokenReset(): void
    {
        session()->remove('reset_token');
        session()->remove('reset_user_id');
        session()->remove('reset_user_name');
        session()->remove('reset_expiration');
    }
}