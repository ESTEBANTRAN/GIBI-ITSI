<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Security\RateLimiter;
use App\Security\SecurityHelper;
use App\Security\SecurityLogger;

/**
 * RateLimitFilter - Filtro de Rate Limiting.
 * 
 * Limita la cantidad de peticiones por IP a rutas protegidas.
 * Configurable: máximo 10 intentos en 15 minutos.
 */
class RateLimitFilter implements FilterInterface
{
    /**
     * Verifica si la IP ha excedido el límite de intentos.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $maxAttempts = 30;       // Aumentado para testing con túnel
        $decaySeconds = 900;     // 15 minutos

        // Permitir personalización desde los argumentos del filtro
        if (!empty($arguments)) {
            if (isset($arguments[0])) $maxAttempts = (int)$arguments[0];
            if (isset($arguments[1])) $decaySeconds = (int)$arguments[1];
        }

        $limiter = new RateLimiter($maxAttempts, $decaySeconds, 'http_rate');
        $ip = SecurityHelper::getClientIp();

        // IMPORTANTE: Con túnel (Serveo/Cloudflare) todos comparten la misma IP.
        // Incluir un hash del User-Agent para diferenciar usuarios detrás del proxy.
        // Esto NO es un fingerprint de seguridad, solo un discriminador de tráfico.
        $uaHash = substr(md5($request->getUserAgent()->getAgentString()), 0, 8);
        $key = $ip . ':' . $uaHash . ':' . $request->getPath();

        if ($limiter->tooManyAttempts($key)) {
            // Registrar en log de seguridad
            $logger = new SecurityLogger();
            $logger->logRateLimited($key, $limiter->getAttempts($key));

            $waitTime = $limiter->getFormattedWaitTime($key);

            // Si es AJAX, responder con JSON
            if ($request->isAJAX()) {
                return \Config\Services::response()
                    ->setStatusCode(429)
                    ->setJSON([
                        'error' => true,
                        'message' => "Demasiados intentos. Intente de nuevo en {$waitTime}.",
                    ]);
            }

            // Para requests normales, redirigir
            return redirect()->back()->with('error', 
                "Demasiados intentos. Por favor espere {$waitTime} antes de intentar de nuevo."
            );
        }

        // Registrar el intento
        $limiter->hit($key);
    }

    /**
     * No hace nada después de la respuesta.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Sin procesamiento posterior
    }
}
