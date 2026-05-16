<?php

declare(strict_types=1);

namespace App\Security;

/**
 * SecurityLogger - Sistema de logging de eventos de seguridad.
 * 
 * Registra eventos como intentos de login, bloqueos, cambios de contraseña,
 * accesos denegados, etc. en archivos dedicados de seguridad.
 */
class SecurityLogger
{
    /** Directorio de logs */
    private string $logPath;

    /** Niveles de severidad */
    public const LEVEL_INFO    = 'INFO';
    public const LEVEL_WARNING = 'WARNING';
    public const LEVEL_DANGER  = 'DANGER';
    public const LEVEL_CRITICAL = 'CRITICAL';

    /** Tipos de evento */
    public const EVENT_LOGIN_SUCCESS     = 'LOGIN_SUCCESS';
    public const EVENT_LOGIN_FAILED      = 'LOGIN_FAILED';
    public const EVENT_LOGOUT            = 'LOGOUT';
    public const EVENT_ACCOUNT_LOCKED    = 'ACCOUNT_LOCKED';
    public const EVENT_ACCOUNT_UNLOCKED  = 'ACCOUNT_UNLOCKED';
    public const EVENT_PASSWORD_CHANGED  = 'PASSWORD_CHANGED';
    public const EVENT_ACCESS_DENIED     = 'ACCESS_DENIED';
    public const EVENT_RATE_LIMITED      = 'RATE_LIMITED';
    public const EVENT_CSRF_FAILURE      = 'CSRF_FAILURE';
    public const EVENT_XSS_ATTEMPT       = 'XSS_ATTEMPT';
    public const EVENT_SQL_INJECTION     = 'SQL_INJECTION_ATTEMPT';
    public const EVENT_SESSION_HIJACK    = 'SESSION_HIJACK_ATTEMPT';
    public const EVENT_SUSPICIOUS_AGENT  = 'SUSPICIOUS_USER_AGENT';
    public const EVENT_FILE_UPLOAD       = 'FILE_UPLOAD';
    public const EVENT_CONFIG_CHANGED    = 'CONFIG_CHANGED';

    public function __construct()
    {
        $this->logPath = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR;
        
        // Asegurar que el directorio existe
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Registra un evento de seguridad.
     */
    public function log(string $level, string $event, string $message, array $context = []): void
    {
        $logFile = $this->logPath . 'security-' . date('Y-m-d') . '.log';
        
        $logEntry = $this->formatEntry($level, $event, $message, $context);
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Registra un intento de login exitoso.
     */
    public function logLoginSuccess(string $identifier, int $userId): void
    {
        $this->log(self::LEVEL_INFO, self::EVENT_LOGIN_SUCCESS, "Login exitoso para el usuario ID: {$userId}", [
            'identifier' => SecurityHelper::maskEmail($identifier),
            'user_id'    => $userId,
            'ip'         => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un intento de login fallido.
     */
    public function logLoginFailed(string $identifier, string $reason = 'Credenciales inválidas'): void
    {
        $this->log(self::LEVEL_WARNING, self::EVENT_LOGIN_FAILED, "Login fallido: {$reason}", [
            'identifier' => SecurityHelper::maskEmail($identifier),
            'reason'     => $reason,
            'ip'         => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un bloqueo de cuenta.
     */
    public function logAccountLocked(string $identifier, int $userId): void
    {
        $this->log(self::LEVEL_DANGER, self::EVENT_ACCOUNT_LOCKED, "Cuenta bloqueada por múltiples intentos fallidos", [
            'identifier' => SecurityHelper::maskEmail($identifier),
            'user_id'    => $userId,
            'ip'         => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un acceso denegado por rol.
     */
    public function logAccessDenied(int $userId, string $route, int $userRole, string $requiredRole): void
    {
        $this->log(self::LEVEL_WARNING, self::EVENT_ACCESS_DENIED, "Acceso denegado a ruta protegida", [
            'user_id'       => $userId,
            'route'         => $route,
            'user_role'     => $userRole,
            'required_role' => $requiredRole,
            'ip'            => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un rate limiting.
     */
    public function logRateLimited(string $key, int $attempts): void
    {
        $this->log(self::LEVEL_WARNING, self::EVENT_RATE_LIMITED, "Rate limit alcanzado", [
            'key'      => $key,
            'attempts' => $attempts,
            'ip'       => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un intento de XSS o SQL injection.
     */
    public function logInjectionAttempt(string $type, string $input): void
    {
        $event = $type === 'xss' ? self::EVENT_XSS_ATTEMPT : self::EVENT_SQL_INJECTION;
        $this->log(self::LEVEL_DANGER, $event, "Intento de inyección detectado", [
            'type'     => $type,
            'input'    => mb_substr($input, 0, 200), // Truncar para no llenar el log
            'ip'       => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un intento de hijack de sesión.
     */
    public function logSessionHijack(int $userId): void
    {
        $this->log(self::LEVEL_CRITICAL, self::EVENT_SESSION_HIJACK, "Posible intento de hijack de sesión detectado", [
            'user_id' => $userId,
            'ip'      => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un cierre de sesión.
     */
    public function logLogout(int $userId): void
    {
        $this->log(self::LEVEL_INFO, self::EVENT_LOGOUT, "Cierre de sesión", [
            'user_id' => $userId,
            'ip'      => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Registra un cambio de contraseña.
     */
    public function logPasswordChanged(int $userId): void
    {
        $this->log(self::LEVEL_INFO, self::EVENT_PASSWORD_CHANGED, "Contraseña cambiada", [
            'user_id' => $userId,
            'ip'      => SecurityHelper::getClientIp(),
        ]);
    }

    /**
     * Formatea una entrada de log.
     */
    private function formatEntry(string $level, string $event, string $message, array $context): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        return "[{$timestamp}] [{$level}] [{$event}] {$message} {$contextJson}" . PHP_EOL;
    }
}
