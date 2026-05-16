<?php

declare(strict_types=1);

namespace App\Security;

/**
 * SecurityHelper - Funciones helper de seguridad.
 * 
 * Proporciona funciones utilitarias estáticas para sanitización,
 * validación, generación de tokens y ofuscación de datos sensibles.
 */
class SecurityHelper
{
    /**
     * Genera un token criptográficamente seguro.
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Sanitiza un string para prevenir XSS.
     */
    public static function sanitizeString(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $input;
    }

    /**
     * Sanitiza un email preservando su formato.
     */
    public static function sanitizeEmail(string $email): string
    {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return strtolower($email);
    }

    /**
     * Sanitiza un nombre de archivo eliminando caracteres peligrosos.
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Eliminar path traversal
        $filename = basename($filename);
        // Solo permitir alfanuméricos, guiones, puntos y guiones bajos
        $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename);
        // Evitar doble extensión peligrosa
        $filename = preg_replace('/\.{2,}/', '.', $filename);
        return $filename;
    }

    /**
     * Valida si una IP es válida (IPv4 o IPv6).
     */
    public static function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Obtiene la IP real del usuario considerando proxies.
     */
    public static function getClientIp(): string
    {
        $request = \Config\Services::request();
        
        // Intentar obtener IP de headers de proxy
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP'];
        foreach ($headers as $header) {
            $ip = $request->getServer($header);
            if ($ip) {
                // Tomar la primera IP en caso de cadenas
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
                if (self::isValidIp($ip)) {
                    return $ip;
                }
            }
        }

        return $request->getIPAddress();
    }

    /**
     * Ofusca una dirección de email para logs.
     * maria.gonzalez@itsi.edu.ec -> m***z@itsi.edu.ec
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***';
        }

        $local = $parts[0];
        $domain = $parts[1];

        if (strlen($local) <= 2) {
            $masked = str_repeat('*', strlen($local));
        } else {
            $masked = $local[0] . str_repeat('*', strlen($local) - 2) . $local[strlen($local) - 1];
        }

        return $masked . '@' . $domain;
    }

    /**
     * Ofusca una cédula para logs.
     * 1005183399 -> 100****399
     */
    public static function maskCedula(string $cedula): string
    {
        if (strlen($cedula) <= 4) {
            return str_repeat('*', strlen($cedula));
        }
        return substr($cedula, 0, 3) . str_repeat('*', strlen($cedula) - 6) . substr($cedula, -3);
    }

    /**
     * Verifica si un User-Agent parece un bot.
     */
    public static function isSuspiciousUserAgent(string $userAgent): bool
    {
        $suspiciousPatterns = [
            '/curl/i', '/wget/i', '/python/i', '/scrapy/i',
            '/httpclient/i', '/java\//i', '/libwww/i',
            '/nikto/i', '/sqlmap/i', '/nmap/i', '/masscan/i',
            '/dirbuster/i', '/gobuster/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        // User-Agent vacío es sospechoso
        return empty(trim($userAgent));
    }

    /**
     * Genera un hash para fingerprint de sesión.
     */
    public static function generateSessionFingerprint(): string
    {
        $request = \Config\Services::request();
        $userAgent = $request->getUserAgent()->getAgentString();
        $acceptLanguage = $request->getHeaderLine('Accept-Language');
        
        return hash('sha256', $userAgent . '|' . $acceptLanguage);
    }

    /**
     * Valida tipos MIME contra una lista permitida.
     */
    public static function isAllowedMimeType(string $mimeType, array $allowedTypes): bool
    {
        return in_array(strtolower($mimeType), array_map('strtolower', $allowedTypes), true);
    }

    /**
     * Lista de extensiones de archivo permitidas para uploads.
     */
    public static function getAllowedUploadExtensions(): array
    {
        return ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
    }

    /**
     * Verifica la fortaleza de una contraseña y retorna un score 0-100.
     */
    public static function getPasswordStrength(string $password): int
    {
        $score = 0;
        $length = strlen($password);

        // Longitud
        if ($length >= 8)  $score += 20;
        if ($length >= 12) $score += 10;
        if ($length >= 16) $score += 10;

        // Complejidad
        if (preg_match('/[a-z]/', $password)) $score += 15;
        if (preg_match('/[A-Z]/', $password)) $score += 15;
        if (preg_match('/[0-9]/', $password)) $score += 15;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15;

        return min(100, $score);
    }
}
