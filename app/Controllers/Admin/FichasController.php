<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AdminBienestarService;

class FichasController extends BaseController
{
    protected $adminService;

    public function __construct()
    {
        $this->adminService = new AdminBienestarService();
    }

    public function fichasSocioeconomicas()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return redirect()->to('/login');
        }

        try {
            $filtros = [
                'estado' => $this->request->getGet('estado'),
                'periodo_id' => $this->request->getGet('periodo_id'),
                'carrera_id' => $this->request->getGet('carrera_id'),
                'busqueda' => $this->request->getGet('busqueda'),
            ];

            $fichas = $this->adminService->getFichasConFiltros($filtros);
            $periodos = $this->adminService->getPeriodosActivos();
            $carreras = $this->adminService->getCarrerasActivas();

            return view('AdminBienestar/fichas_socioeconomicas', [
                'fichas' => $fichas,
                'periodos' => $periodos,
                'carreras' => $carreras,
                'filtros' => $filtros,
                'usuario' => $this->getUsuarioActual()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en fichas: ' . $e->getMessage());
            return view('AdminBienestar/fichas_socioeconomicas', [
                'fichas' => [],
                'periodos' => [],
                'carreras' => [],
                'filtros' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando fichas'
            ]);
        }
    }

    public function verFicha($id)
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $ficha = $this->adminService->getFichaCompleta($id);
            return $this->response->setJSON(['success' => true, 'ficha' => $ficha]);
        } catch (\Exception $e) {
            log_message('error', 'Error viendo ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    public function actualizarEstadoFicha()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $fichaId = $this->request->getPost('ficha_id');
            $estado = $this->request->getPost('estado');
            $comentario = $this->request->getPost('comentario');

            if (empty($fichaId) || empty($estado)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos incompletos']);
            }

            $resultado = $this->adminService->actualizarEstadoFicha($fichaId, $estado, $comentario, session('id'));
            return $this->response->setJSON(['success' => $resultado]);
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando estado ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
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
