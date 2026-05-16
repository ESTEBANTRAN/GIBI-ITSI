<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SecurityHeadersFilter - Headers HTTP de seguridad.
 * 
 * Agrega headers de seguridad estándar a todas las respuestas
 * para proteger contra clickjacking, sniffing, y otros ataques.
 */
class SecurityHeadersFilter implements FilterInterface
{
    /**
     * No hace nada antes del request.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // No se necesita procesamiento antes
    }

    /**
     * Agrega headers de seguridad a la respuesta.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Prevenir que el navegador intente adivinar el MIME type
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Prevenir clickjacking - solo permitir iframes del mismo origen
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        
        // Activar protección XSS del navegador
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        
        // Controlar qué información de referencia se envía
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Restringir permisos del navegador
        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        
        // Prevenir caching de páginas sensibles
        if (session()->get('isLoggedIn')) {
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
        
        // Eliminar header que revela tecnología del servidor
        $response->removeHeader('X-Powered-By');
        
        return $response;
    }
}
