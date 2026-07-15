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
            $paginaBecados = (int)($this->request->getGet('page_becados') ?? 1);
            $paginaEstudiantes = (int)($this->request->getGet('page_estudiantes') ?? 1);
            $offsetBecados = ($paginaBecados - 1) * $porPagina;
            $offsetEstudiantes = ($paginaEstudiantes - 1) * $porPagina;

            $filtros = [
                'estado' => $this->request->getGet('estado'),
                'periodo_id' => $this->request->getGet('periodo_id'),
                'carrera_id' => $this->request->getGet('carrera_id'),
                'busqueda' => $this->request->getGet('busqueda'),
                'evaluacion' => $this->request->getGet('evaluacion'),
            ];

            $periodos = $this->adminService->getPeriodosActivos();
            $carreras = $this->adminService->getCarrerasActivas();

            $db = \Config\Database::connect();

            // --- Helper: apply shared filters to a builder ---
            $applyFilters = function($builder) use ($filtros) {
                if (!empty($filtros['estado'])) {
                    $builder->where('estado', $filtros['estado']);
                }
                if (!empty($filtros['periodo_id'])) {
                    $builder->where('periodo_id', $filtros['periodo_id']);
                }
                if (!empty($filtros['carrera_id'])) {
                    $builder->where('estudiante_id IN (SELECT id FROM usuarios WHERE carrera_id = ' . (int)$filtros['carrera_id'] . ')');
                }
                if (!empty($filtros['busqueda'])) {
                    $builder->groupStart()
                        ->like('estudiante_nombre', $filtros['busqueda'])
                        ->orLike('cedula', $filtros['busqueda'])
                        ->orLike('email', $filtros['busqueda'])
                        ->groupEnd();
                }
                if (isset($filtros['evaluacion']) && $filtros['evaluacion'] !== '') {
                    if ($filtros['evaluacion'] === 'con') {
                        $builder->where('revisada_por_admin', 1);
                    } elseif ($filtros['evaluacion'] === 'sin') {
                        $builder->where('revisada_por_admin', 0);
                    }
                }
                return $builder;
            };

            // --- BECADOS (relacionada_beca = 1) ---
            $countBecados = $db->table('v_fichas_admin');
            $applyFilters($countBecados);
            $countBecados->where('relacionada_beca', 1);
            $totalBecados = $countBecados->countAllResults();
            $totalPaginasBecados = (int)ceil($totalBecados / $porPagina);

            $builderBecados = $db->table('v_fichas_admin');
            $applyFilters($builderBecados);
            $builderBecados->where('relacionada_beca', 1);
            $builderBecados->orderBy('fecha_creacion', 'DESC');
            $builderBecados->limit($porPagina, $offsetBecados);
            $fichasBecados = $builderBecados->get()->getResultArray();

            // --- ESTUDIANTES (relacionada_beca = 0) ---
            $countEstudiantes = $db->table('v_fichas_admin');
            $applyFilters($countEstudiantes);
            $countEstudiantes->where('relacionada_beca', 0);
            $totalEstudiantes = $countEstudiantes->countAllResults();
            $totalPaginasEstudiantes = (int)ceil($totalEstudiantes / $porPagina);

            $builderEstudiantes = $db->table('v_fichas_admin');
            $applyFilters($builderEstudiantes);
            $builderEstudiantes->where('relacionada_beca', 0);
            $builderEstudiantes->orderBy('fecha_creacion', 'DESC');
            $builderEstudiantes->limit($porPagina, $offsetEstudiantes);
            $fichasEstudiantes = $builderEstudiantes->get()->getResultArray();

            // Stats
            $solicitudesStats = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'Enviada' THEN 1 ELSE 0 END) as enviadas,
                    SUM(CASE WHEN estado = 'Revisada' THEN 1 ELSE 0 END) as revisadas,
                    SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas
                FROM v_fichas_admin
                WHERE relacionada_beca = 1
            ")->getRowArray();

            $statsEstudiantes = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'Enviada' THEN 1 ELSE 0 END) as enviadas,
                    SUM(CASE WHEN estado = 'Revisada' THEN 1 ELSE 0 END) as revisadas,
                    SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas
                FROM v_fichas_admin
                WHERE relacionada_beca = 0
            ")->getRowArray();

            return view('AdminBienestar/fichas_socioeconomicas', [
                'fichasBecados' => $fichasBecados,
                'fichasEstudiantes' => $fichasEstudiantes,
                // Keep backward compat: fichas = all for charts/JS that still use it
                'fichas' => array_merge($fichasBecados, $fichasEstudiantes),
                'periodos' => $periodos,
                'carreras' => $carreras,
                'filtros' => $filtros,
                'usuario' => $this->getUsuarioActual(),
                'estadisticasBecados' => $solicitudesStats,
                'estadisticasEstudiantes' => $statsEstudiantes,
                'paginacionBecados' => [
                    'pagina_actual' => $paginaBecados,
                    'total_paginas' => $totalPaginasBecados,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalBecados,
                    'offset' => $offsetBecados,
                    'param' => 'page_becados'
                ],
                'paginacionEstudiantes' => [
                    'pagina_actual' => $paginaEstudiantes,
                    'total_paginas' => $totalPaginasEstudiantes,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalEstudiantes,
                    'offset' => $offsetEstudiantes,
                    'param' => 'page_estudiantes'
                ],
                // Keep old $paginacion for backward compat
                'paginacion' => [
                    'pagina_actual' => $paginaBecados,
                    'total_paginas' => $totalPaginasBecados,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalBecados,
                    'offset' => $offsetBecados
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en fichas: ' . $e->getMessage());
            return view('AdminBienestar/fichas_socioeconomicas', [
                'fichas' => [],
                'fichasBecados' => [],
                'fichasEstudiantes' => [],
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
