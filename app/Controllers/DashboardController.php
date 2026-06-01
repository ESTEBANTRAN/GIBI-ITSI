<?php

namespace App\Controllers;

use App\Models\FichaSocioeconomicaModel;
use App\Models\BecaModel;
use App\Models\SolicitudAyudaModel;
use App\Models\UsuarioModel;

class DashboardController extends BaseController
{
    protected $fichaModel;
    protected $becaModel;
    protected $solicitudModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->becaModel = new BecaModel();
        $this->solicitudModel = new SolicitudAyudaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        if (!session('id')) {
            return redirect()->to('/login');
        }

        $rol_id = session('rol_id');
        
        if ($rol_id == ROLE_ESTUDIANTE) {
            // Estudiante
            return redirect()->to('/estudiante');
        } elseif ($rol_id == ROLE_ADMIN_BIENESTAR) {
            // Administrativo Bienestar
            return redirect()->to('/admin-bienestar');
        } elseif ($rol_id == ROLE_SUPER_ADMIN) {
            // Super Administrador
            return redirect()->to('/global-admin/dashboard');
        }
        
        return redirect()->to('/login');
    }

    public function adminBienestar()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return redirect()->to('/login');
        }

        // Estadísticas de formularios (estados reales de la BD: Borrador, Enviada, Revisada, Aprobada, Rechazada)
        $totalFormularios = $this->fichaModel->countAllResults();
        $formulariosPendientes = $this->fichaModel->where('estado', 'Enviada')->countAllResults();
        $formulariosAprobados = $this->fichaModel->where('estado', 'Aprobada')->countAllResults();
        $formulariosRechazados = $this->fichaModel->where('estado', 'Rechazada')->countAllResults();

        // Estadísticas de becas
        $totalBecas = $this->becaModel->countAllResults();
        $becasActivas = $this->becaModel->where('estado', 'Activa')->countAllResults();
        $solicitudesBecas = \Config\Database::connect()->table('solicitudes_becas')->where('estado', 'Postulada')->countAllResults();

        // Estadísticas de solicitudes de ayuda (estados: Pendiente, En Proceso, Resuelta, Cerrada)
        $totalSolicitudes = $this->solicitudModel->countAllResults();
        $solicitudesPendientes = $this->solicitudModel->where('estado', 'Pendiente')->countAllResults();

        // Estadísticas de estudiantes
        $totalEstudiantes = $this->usuarioModel->where('rol_id', ROLE_ESTUDIANTE)->countAllResults();

        // Obtener actividad reciente
        $actividadRecienteLogs = \Config\Database::connect()->table('logs l')
            ->select('l.accion, l.fecha_creacion as fecha, u.nombre, u.apellido')
            ->join('usuarios u', 'u.id = l.id_usuario', 'left')
            ->orderBy('l.fecha_creacion', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $actividadFormateada = [];
        foreach ($actividadRecienteLogs as $log) {
            $estado = 'Completado';
            if (strpos(strtolower($log['accion']), 'rechaz') !== false || strpos(strtolower($log['accion']), 'eliminar') !== false) {
                $estado = 'Rechazado';
            } elseif (strpos(strtolower($log['accion']), 'pendient') !== false || strpos(strtolower($log['accion']), 'solicitud') !== false) {
                $estado = 'Pendiente';
            }

            $actividadFormateada[] = [
                'accion' => ucfirst(str_replace('_', ' ', $log['accion'])),
                'usuario' => ($log['nombre'] && $log['apellido']) ? $log['nombre'] . ' ' . $log['apellido'] : 'Sistema',
                'fecha' => 'Hace ' . \CodeIgniter\I18n\Time::parse($log['fecha'])->humanize(),
                'estado' => $estado
            ];
        }

        $data = [
            'formularios' => [
                'total' => $totalFormularios,
                'pendientes' => $formulariosPendientes
            ],
            'becas' => [
                'total' => $totalBecas,
                'activas' => $becasActivas,
                'solicitudes_activas' => $solicitudesBecas
            ],
            'solicitudes' => [
                'total' => $totalSolicitudes,
                'pendientes' => $solicitudesPendientes
            ],
            'estudiantes' => [
                'total' => $totalEstudiantes
            ],
            'actividad_reciente' => $actividadFormateada
        ];

        return view('AdminBienestar/administrativo', $data);
    }

    /**
     * Obtiene estadísticas para el dashboard
     */
    public function getEstadisticas()
    {
        if (!session('id') || session('rol_id') != 2) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            // Estadísticas de formularios (estados reales: Borrador, Enviada, Revisada, Aprobada, Rechazada)
            $totalFormularios = $this->fichaModel->countAllResults();
            $formulariosPendientes = $this->fichaModel->where('estado', 'Enviada')->countAllResults();
            $formulariosAprobados = $this->fichaModel->where('estado', 'Aprobada')->countAllResults();
            $formulariosRechazados = $this->fichaModel->where('estado', 'Rechazada')->countAllResults();

            // Estadísticas de becas
            $totalBecas = $this->becaModel->countAllResults();
            $becasActivas = $this->becaModel->where('estado', 'Activa')->countAllResults();
            $solicitudesBecas = \Config\Database::connect()->table('solicitudes_becas')->where('estado', 'Postulada')->countAllResults();

            // Estadísticas de solicitudes de ayuda (estados: Pendiente, En Proceso, Resuelta, Cerrada)
            $totalSolicitudes = $this->solicitudModel->countAllResults();
            $solicitudesPendientes = $this->solicitudModel->where('estado', 'Pendiente')->countAllResults();
            $solicitudesEnProceso = $this->solicitudModel->where('estado', 'En Proceso')->countAllResults();
            $solicitudesResueltas = $this->solicitudModel->where('estado', 'Resuelta')->countAllResults();

            // Estadísticas de estudiantes
            $totalEstudiantes = $this->usuarioModel->where('rol_id', ROLE_ESTUDIANTE)->countAllResults();

            $estadisticas = [
                'formularios' => [
                    'total' => $totalFormularios,
                    'pendientes' => $formulariosPendientes,
                    'aprobados' => $formulariosAprobados,
                    'rechazados' => $formulariosRechazados
                ],
                'becas' => [
                    'total' => $totalBecas,
                    'activas' => $becasActivas,
                    'solicitudes' => $solicitudesBecas
                ],
                'solicitudes' => [
                    'total' => $totalSolicitudes,
                    'pendientes' => $solicitudesPendientes,
                    'en_proceso' => $solicitudesEnProceso,
                    'resueltas' => $solicitudesResueltas
                ],
                'estudiantes' => [
                    'total' => $totalEstudiantes
                ]
            ];

            return $this->response->setJSON($estadisticas);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error al obtener estadísticas'])->setStatusCode(500);
        }
    }

    public function getActividadReciente()
    {
        if (!session('id') || session('rol_id') != 2) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            // Obtener actividad reciente desde los logs
            $actividadRecienteLogs = \Config\Database::connect()->table('logs l')
                ->select('l.accion, l.fecha_creacion as fecha, u.nombre, u.apellido')
                ->join('usuarios u', 'u.id = l.id_usuario', 'left')
                ->orderBy('l.fecha_creacion', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $actividadFormateada = [];
            foreach ($actividadRecienteLogs as $log) {
                $estado = 'Completado';
                if (strpos(strtolower($log['accion']), 'rechaz') !== false || strpos(strtolower($log['accion']), 'eliminar') !== false) {
                    $estado = 'Rechazado';
                } elseif (strpos(strtolower($log['accion']), 'pendient') !== false || strpos(strtolower($log['accion']), 'solicitud') !== false) {
                    $estado = 'Pendiente';
                }

                $actividadFormateada[] = [
                    'accion' => ucfirst(str_replace('_', ' ', $log['accion'])),
                    'usuario' => ($log['nombre'] && $log['apellido']) ? $log['nombre'] . ' ' . $log['apellido'] : 'Sistema',
                    'fecha' => 'Hace ' . \CodeIgniter\I18n\Time::parse($log['fecha'])->humanize(),
                    'estado' => $estado
                ];
            }

            return $this->response->setJSON($actividadFormateada);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error al obtener actividad reciente'])->setStatusCode(500);
        }
    }

    /**
     * Obtiene datos de estadísticas como array (sin Response wrapper)
     */
    private function obtenerDatosEstadisticas(): array
    {
        // Estadísticas de formularios (estados reales: Borrador, Enviada, Revisada, Aprobada, Rechazada)
        $totalFormularios = $this->fichaModel->countAllResults();
        $formulariosPendientes = $this->fichaModel->where('estado', 'Enviada')->countAllResults();
        $formulariosAprobados = $this->fichaModel->where('estado', 'Aprobada')->countAllResults();
        $formulariosRechazados = $this->fichaModel->where('estado', 'Rechazada')->countAllResults();

        $totalBecas = $this->becaModel->countAllResults();
        $becasActivas = $this->becaModel->where('estado', 'Activa')->countAllResults();
        $solicitudesBecas = \Config\Database::connect()->table('solicitudes_becas')->where('estado', 'Postulada')->countAllResults();

        $totalSolicitudes = $this->solicitudModel->countAllResults();
        $solicitudesPendientes = $this->solicitudModel->where('estado', 'Pendiente')->countAllResults();
        $solicitudesEnProceso = $this->solicitudModel->where('estado', 'En Proceso')->countAllResults();
        $solicitudesResueltas = $this->solicitudModel->where('estado', 'Resuelta')->countAllResults();

        $totalEstudiantes = $this->usuarioModel->where('rol_id', ROLE_ESTUDIANTE)->countAllResults();

        return [
            'formularios' => [
                'total' => $totalFormularios,
                'pendientes' => $formulariosPendientes,
                'aprobados' => $formulariosAprobados,
                'rechazados' => $formulariosRechazados
            ],
            'becas' => [
                'total' => $totalBecas,
                'activas' => $becasActivas,
                'solicitudes' => $solicitudesBecas
            ],
            'solicitudes' => [
                'total' => $totalSolicitudes,
                'pendientes' => $solicitudesPendientes,
                'en_proceso' => $solicitudesEnProceso,
                'resueltas' => $solicitudesResueltas
            ],
            'estudiantes' => [
                'total' => $totalEstudiantes
            ]
        ];
    }

    /**
     * Obtiene datos de actividad reciente como array (sin Response wrapper)
     */
    private function obtenerDatosActividadReciente(): array
    {
        $actividadRecienteLogs = \Config\Database::connect()->table('logs l')
            ->select('l.accion, l.fecha_creacion as fecha, u.nombre, u.apellido')
            ->join('usuarios u', 'u.id = l.id_usuario', 'left')
            ->orderBy('l.fecha_creacion', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $actividadFormateada = [];
        foreach ($actividadRecienteLogs as $log) {
            $estado = 'Completado';
            if (strpos(strtolower($log['accion']), 'rechaz') !== false || strpos(strtolower($log['accion']), 'eliminar') !== false) {
                $estado = 'Rechazado';
            } elseif (strpos(strtolower($log['accion']), 'pendient') !== false || strpos(strtolower($log['accion']), 'solicitud') !== false) {
                $estado = 'Pendiente';
            }

            $actividadFormateada[] = [
                'accion' => ucfirst(str_replace('_', ' ', $log['accion'])),
                'usuario' => ($log['nombre'] && $log['apellido']) ? $log['nombre'] . ' ' . $log['apellido'] : 'Sistema',
                'fecha' => 'Hace ' . \CodeIgniter\I18n\Time::parse($log['fecha'])->humanize(),
                'estado' => $estado
            ];
        }

        return $actividadFormateada;
    }

    /**
     * Actualiza el dashboard (usa métodos privados para datos directos)
     */
    public function actualizarDashboard()
    {
        if (!session('id') || session('rol_id') != 2) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $estadisticas = $this->obtenerDatosEstadisticas();
            $actividad = $this->obtenerDatosActividadReciente();

            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => $estadisticas,
                'actividad' => $actividad,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error al actualizar dashboard'])->setStatusCode(500);
        }
    }
}