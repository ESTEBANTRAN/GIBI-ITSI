<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AdminBienestarService;

class DashboardController extends BaseController
{
    protected $adminService;

    public function __construct()
    {
        $this->adminService = new AdminBienestarService();
    }

    public function dashboard()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return redirect()->to('/login');
        }

        try {
            $estadisticas = $this->adminService->getEstadisticasCompletas();
            return view('AdminBienestar/dashboard', [
                'estadisticas' => $estadisticas,
                'usuario' => $this->getUsuarioActual()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en dashboard: ' . $e->getMessage());
            return view('AdminBienestar/dashboard', [
                'estadisticas' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando estadísticas'
            ]);
        }
    }

    public function getEstadisticas()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $estadisticas = $this->adminService->getEstadisticasCompletas();
            return $this->response->setJSON(['success' => true, 'data' => $estadisticas]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error obteniendo estadísticas']);
        }
    }

    private function getUsuarioActual()
    {
        return [
            'id' => session('id'),
            'nombre' => session('nombre'),
            'email' => session('email'),
            'rol_id' => session('rol_id')
        ];
    }
}
