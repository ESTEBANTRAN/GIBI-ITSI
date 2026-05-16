<?php

declare(strict_types=1);

namespace App\Security;

/**
 * SessionGuard - Protección avanzada de sesiones.
 * 
 * Maneja regeneración de IDs, detección de hijacking mediante
 * fingerprinting, timeout por inactividad, y validación de integridad.
 */
class SessionGuard
{
    /** Tiempo máximo de inactividad en segundos (30 min) */
    private int $inactivityTimeout = 1800;

    /** Clave de sesión para el fingerprint */
    private const FINGERPRINT_KEY = '_security_fingerprint';

    /** Clave de sesión para última actividad */
    private const LAST_ACTIVITY_KEY = '_security_last_activity';

    /** Clave de sesión para IP de login */
    private const LOGIN_IP_KEY = '_security_login_ip';

    /**
     * Inicializa la protección de sesión después del login.
     * Debe llamarse justo después de autenticar al usuario.
     */
    public function initializeSession(): void
    {
        $session = session();
        
        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);
        
        // Guardar fingerprint
        $session->set(self::FINGERPRINT_KEY, SecurityHelper::generateSessionFingerprint());
        
        // Registrar IP de login
        $session->set(self::LOGIN_IP_KEY, SecurityHelper::getClientIp());
        
        // Registrar timestamp de actividad
        $session->set(self::LAST_ACTIVITY_KEY, time());
    }

    /**
     * Valida la sesión actual.
     * Retorna true si la sesión es válida, false si fue comprometida.
     */
    public function validateSession(): bool
    {
        $session = session();

        // Verificar que exista el fingerprint
        if (!$session->get(self::FINGERPRINT_KEY)) {
            return true; // Sesiones antiguas sin fingerprint son válidas
        }

        // Validar fingerprint (detecta cambio de navegador)
        $currentFingerprint = SecurityHelper::generateSessionFingerprint();
        $storedFingerprint = $session->get(self::FINGERPRINT_KEY);
        
        if ($currentFingerprint !== $storedFingerprint) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si la sesión ha expirado por inactividad.
     */
    public function isSessionExpired(): bool
    {
        $session = session();
        $lastActivity = $session->get(self::LAST_ACTIVITY_KEY);

        if (!$lastActivity) {
            return false; // Sin timestamp, no se puede determinar
        }

        return (time() - (int)$lastActivity) > $this->inactivityTimeout;
    }

    /**
     * Actualiza el timestamp de última actividad.
     * Llamar en cada request autenticado.
     */
    public function touchActivity(): void
    {
        session()->set(self::LAST_ACTIVITY_KEY, time());
    }

    /**
     * Destruye la sesión de forma segura.
     */
    public function destroySession(): void
    {
        $session = session();
        
        // Limpiar todos los datos
        $session->destroy();
        
        // Regenerar para evitar reutilización del ID
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Obtiene la IP con la que se inició sesión.
     */
    public function getLoginIp(): ?string
    {
        return session()->get(self::LOGIN_IP_KEY);
    }

    /**
     * Obtiene los segundos desde la última actividad.
     */
    public function getIdleTime(): int
    {
        $lastActivity = session()->get(self::LAST_ACTIVITY_KEY);
        if (!$lastActivity) {
            return 0;
        }
        return time() - (int)$lastActivity;
    }

    /**
     * Obtiene los segundos restantes antes de expirar.
     */
    public function getRemainingTime(): int
    {
        return max(0, $this->inactivityTimeout - $this->getIdleTime());
    }
}
