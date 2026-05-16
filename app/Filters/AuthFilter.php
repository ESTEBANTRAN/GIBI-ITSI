<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Security\SessionGuard;
use App\Security\SecurityLogger;

class AuthFilter implements FilterInterface
{
    /**
     * Verifica si el usuario está logueado y la sesión es válida.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está logueado
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $sessionGuard = new SessionGuard();

        // Verificar si la sesión expiró por inactividad
        if ($sessionGuard->isSessionExpired()) {
            $userId = session()->get('id');
            if ($userId) {
                $logger = new SecurityLogger();
                $logger->log(
                    SecurityLogger::LEVEL_INFO,
                    'SESSION_EXPIRED',
                    'Sesión expirada por inactividad',
                    ['user_id' => $userId]
                );
            }
            $sessionGuard->destroySession();
            return redirect()->to('/login')->with('error', 'Su sesión ha expirado por inactividad.');
        }

        // Verificar integridad de sesión (fingerprint)
        if (!$sessionGuard->validateSession()) {
            $userId = session()->get('id');
            if ($userId) {
                $logger = new SecurityLogger();
                $logger->logSessionHijack((int)$userId);
            }
            $sessionGuard->destroySession();
            return redirect()->to('/login')->with('error', 'Sesión inválida. Por favor inicie sesión nuevamente.');
        }

        // Actualizar timestamp de actividad
        $sessionGuard->touchActivity();

        return $request;
    }

    /**
     * No hacer nada después de la respuesta.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
