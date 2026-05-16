<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Security\RateLimiter;
use App\Security\SecurityLogger;
use App\Security\SessionGuard;
use App\Security\SecurityHelper;

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

        // ====== 2. Validar los datos de entrada ======
        $rules = [
            'identificador' => 'required',
            'password'      => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor complete todos los campos.');
        }

        // ====== 3. Obtener datos del formulario ======
        $identifier = trim($this->request->getPost('identificador'));
        $password = $this->request->getPost('password');

        log_message('debug', 'AuthController::attemptLogin - Intentando login con identificador: ' . SecurityHelper::maskEmail($identifier));

        // ====== 4. Buscar usuario ======
        $model = new UsuarioModel();
        $user = $model->findUserByIdentifier($identifier);

        if (!$user) {
            // Registrar intento en rate limiter
            $rateLimiter->hit($ip);
            $securityLogger->logLoginFailed($identifier, 'Usuario no encontrado');
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        // ====== 5. Verificar si la cuenta está bloqueada ======
        if ($model->usuarioBloqueado((int)$user['id'])) {
            $securityLogger->logLoginFailed($identifier, 'Cuenta bloqueada');
            return redirect()->back()->withInput()->with('error', 
                'Su cuenta está temporalmente bloqueada por múltiples intentos fallidos. Intente más tarde.'
            );
        }

        // ====== 6. Verificar contraseña ======
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

        // ====== 7. Login exitoso ======
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
        
        // ====== 8. Redirigir según el rol ======
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
}