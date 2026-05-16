<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Security\SecurityLogger;

/**
 * RoleFilter - Filtro de autorización por rol.
 * 
 * Verifica que el usuario autenticado tenga el rol necesario
 * para acceder a una ruta protegida.
 * 
 * Uso en rutas:
 *   'filter' => 'role:1'        // Solo estudiantes
 *   'filter' => 'role:2'        // Solo admin bienestar
 *   'filter' => 'role:4'        // Solo super admin
 *   'filter' => 'role:2,4'      // Admin bienestar o super admin
 */
class RoleFilter implements FilterInterface
{
    /**
     * Mapa de nombres de roles legibles.
     */
    private array $roleNames = [
        1 => 'Estudiante',
        2 => 'Admin Bienestar',
        3 => 'Administrativo',
        4 => 'Super Administrador',
    ];

    /**
     * Verifica el rol del usuario antes de permitir acceso.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Primero verificar que está logueado
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        // Si no se especifican roles, solo verificar autenticación
        if (empty($arguments)) {
            return;
        }

        $userRolId = (int) session()->get('rol_id');
        $allowedRoles = array_map('intval', $arguments);

        // Verificar si el rol del usuario está en los roles permitidos
        if (!in_array($userRolId, $allowedRoles, true)) {
            // Registrar intento de acceso no autorizado
            $logger = new SecurityLogger();
            $userId = (int) session()->get('id');
            $logger->logAccessDenied(
                $userId,
                $request->getPath(),
                $userRolId,
                implode(',', $allowedRoles)
            );

            // Redirigir al dashboard correcto del usuario
            $redirectUrl = $this->getDashboardByRole($userRolId);
            
            return redirect()->to($redirectUrl)->with('error', 
                'No tiene permisos para acceder a esa sección.'
            );
        }
    }

    /**
     * No hace nada después de la respuesta.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Sin procesamiento posterior
    }

    /**
     * Obtiene la URL del dashboard según el rol.
     */
    private function getDashboardByRole(int $rolId): string
    {
        switch ($rolId) {
            case 1:
                return '/estudiante';
            case 2:
                return '/admin-bienestar';
            case 4:
                return '/global-admin/dashboard';
            default:
                return '/login';
        }
    }
}
