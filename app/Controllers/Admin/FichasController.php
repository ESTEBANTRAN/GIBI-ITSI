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
            $porPagina = 15;
            $pagina = (int)($this->request->getGet('page') ?? 1);
            $offset = ($pagina - 1) * $porPagina;

            $filtros = [
                'estado' => $this->request->getGet('estado'),
                'periodo_id' => $this->request->getGet('periodo_id'),
                'carrera_id' => $this->request->getGet('carrera_id'),
                'busqueda' => $this->request->getGet('busqueda'),
                'evaluacion' => $this->request->getGet('evaluacion'),
                'per_page' => $porPagina,
                'page' => $pagina
            ];

            $fichas = $this->adminService->getFichasConFiltros($filtros);
            $periodos = $this->adminService->getPeriodosActivos();
            $carreras = $this->adminService->getCarrerasActivas();

            $db = \Config\Database::connect();
            $countBuilder = $db->table('v_fichas_admin');
            if (!empty($filtros['estado'])) {
                $countBuilder->where('estado', $filtros['estado']);
            }
            if (!empty($filtros['periodo_id'])) {
                $countBuilder->where('periodo_id', $filtros['periodo_id']);
            }
            if (!empty($filtros['carrera_id'])) {
                $countBuilder->where('estudiante_id IN (SELECT id FROM usuarios WHERE carrera_id = ' . (int)$filtros['carrera_id'] . ')');
            }
            if (!empty($filtros['busqueda'])) {
                $countBuilder->groupStart()
                    ->like('estudiante_nombre', $filtros['busqueda'])
                    ->orLike('cedula', $filtros['busqueda'])
                    ->orLike('email', $filtros['busqueda'])
                    ->groupEnd();
            }
            if (isset($filtros['evaluacion']) && $filtros['evaluacion'] !== '') {
                if ($filtros['evaluacion'] === 'con') {
                    $countBuilder->where('revisada_por_admin', 1);
                } elseif ($filtros['evaluacion'] === 'sin') {
                    $countBuilder->where('revisada_por_admin', 0);
                }
            }
            $totalRegistros = $countBuilder->countAllResults();
            $totalPaginas = (int)ceil($totalRegistros / $porPagina);

            $solicitudesStats = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'Enviada' THEN 1 ELSE 0 END) as enviadas,
                    SUM(CASE WHEN estado = 'Revisada' THEN 1 ELSE 0 END) as revisadas,
                    SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas
                FROM v_fichas_admin
            ")->getRowArray();

            return view('AdminBienestar/fichas_socioeconomicas', [
                'fichas' => $fichas,
                'periodos' => $periodos,
                'carreras' => $carreras,
                'filtros' => $filtros,
                'usuario' => $this->getUsuarioActual(),
                'estadisticasBecados' => $solicitudesStats,
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalRegistros,
                    'offset' => $offset
                ]
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
