<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Security\InputSanitizerTrait;

class EstadisticasController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // Métodos para estadísticas globales
    public function estadisticas()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        return view('GlobalAdmin/estadisticas');
    }

    public function obtenerEstadisticasGlobales()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Obtener estadísticas reales de la base de datos
            $totalUsuarios = $this->db->table('usuarios')->countAllResults();
            $usuariosActivos = $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults();
            $totalRoles = $this->db->table('roles')->countAllResults();
            
            // Calcular cambios porcentuales
            $cambioUsuarios = $this->calcularCambioUsuarios();
            $cambioActivos = $this->calcularCambioActivos();
            
            // Obtener respaldos recientes
            $backupDir = WRITEPATH . 'backups/';
            $respaldosRecientes = is_dir($backupDir) ? count(glob($backupDir . '*.sql')) : 0;
            
            // Obtener datos para gráficos
            $datosGraficos = $this->obtenerDatosGraficosEstadisticas();
            
            // Obtener datos de tablas
            $datosTablas = $this->obtenerDatosTablasEstadisticas();
            
            // Obtener KPIs
            $kpis = $this->obtenerKPIsEstadisticas();

            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => [
                    'total_usuarios' => $totalUsuarios,
                    'usuarios_activos' => $usuariosActivos,
                    'total_roles' => $totalRoles,
                    'respaldos_recientes' => $respaldosRecientes,
                    'cambio_usuarios' => $cambioUsuarios,
                    'cambio_activos' => $cambioActivos
                ],
                'graficos' => $datosGraficos,
                'tablas' => $datosTablas,
                'kpis' => $kpis
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas globales: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error al obtener estadísticas'
            ]);
        }
    }

    /**
     * Obtener métricas de rendimiento del sistema
     */
    public function getMetricasRendimiento()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $metricas = [
                'usuarios_activos_24h' => $this->db->table('usuarios')
                    ->where('ultimo_acceso >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults(),
                
                'fichas_creadas_semana' => $this->db->table('fichas_socioeconomicas')
                    ->where('fecha_creacion >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                    ->countAllResults(),
                
                'solicitudes_procesadas_semana' => $this->db->table('solicitudes_becas')
                    ->where('fecha_solicitud >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                    ->whereNotIn('estado', ['Pendiente'])
                    ->countAllResults(),
                
                'tiempo_promedio_aprobacion' => $this->calcularTiempoPromedioAprobacion(),
                
                'satisfaccion_usuarios' => $this->calcularSatisfaccionUsuarios()
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'metricas' => $metricas
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo métricas de rendimiento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error obteniendo métricas'
            ]);
        }
    }

    // ──────────────────────────────────────────────
    //  Private helper methods
    // ──────────────────────────────────────────────

    private function calcularCambioUsuarios()
    {
        try {
            // Usuarios del mes actual
            $usuariosMesActual = $this->db->table('usuarios')
                ->where('MONTH(fecha_registro)', date('n'))
                ->where('YEAR(fecha_registro)', date('Y'))
                ->countAllResults();
            
            // Usuarios del mes anterior
            $usuariosMesAnterior = $this->db->table('usuarios')
                ->where('MONTH(fecha_registro)', date('n', strtotime('-1 month')))
                ->where('YEAR(fecha_registro)', date('Y', strtotime('-1 month')))
                ->countAllResults();
            
            if ($usuariosMesAnterior > 0) {
                return round((($usuariosMesActual - $usuariosMesAnterior) / $usuariosMesAnterior) * 100);
            }
            
            return $usuariosMesActual > 0 ? 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calcularCambioActivos()
    {
        try {
            // Usuarios activos del mes actual
            $activosMesActual = $this->db->table('usuarios')
                ->where('estado', 'Activo')
                ->where('MONTH(ultimo_acceso)', date('n'))
                ->where('YEAR(ultimo_acceso)', date('Y'))
                ->countAllResults();
            
            // Usuarios activos del mes anterior
            $activosMesAnterior = $this->db->table('usuarios')
                ->where('estado', 'Activo')
                ->where('MONTH(ultimo_acceso)', date('n', strtotime('-1 month')))
                ->where('YEAR(ultimo_acceso)', date('Y', strtotime('-1 month')))
                ->countAllResults();
            
            if ($activosMesAnterior > 0) {
                return round((($activosMesActual - $activosMesAnterior) / $activosMesAnterior) * 100);
            }
            
            return $activosMesActual > 0 ? 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function obtenerDatosGraficosEstadisticas()
    {
        try {
            // Gráfico de actividad del sistema (últimos 6 meses)
            $actividad = [];
            $labels = [];
            for ($i = 5; $i >= 0; $i--) {
                $mes = date('n', strtotime("-$i months"));
                $año = date('Y', strtotime("-$i months"));
                $labels[] = date('M', strtotime("-$i months"));
                
                $usuariosActivos = $this->db->table('usuarios')
                    ->where('estado', 'Activo')
                    ->where('MONTH(ultimo_acceso)', $mes)
                    ->where('YEAR(ultimo_acceso)', $año)
                    ->countAllResults();
                
                $actividad[] = $usuariosActivos;
            }
            
            // Distribución por roles
            $roles = $this->db->table('usuarios u')
                ->select('r.nombre, COUNT(u.id) as total')
                ->join('roles r', 'r.id = u.rol_id')
                ->groupBy('u.rol_id')
                ->get()
                ->getResultArray();
            
            $rolesLabels = [];
            $rolesData = [];
            $rolesColors = ['#007bff', '#28a745', '#dc3545', '#ffc107'];
            
            foreach ($roles as $index => $rol) {
                $rolesLabels[] = $rol['nombre'];
                $rolesData[] = $rol['total'];
            }
            
            // Registros por mes
            $registros = [];
            $registrosLabels = [];
            for ($i = 5; $i >= 0; $i--) {
                $mes = date('n', strtotime("-$i months"));
                $año = date('Y', strtotime("-$i months"));
                $registrosLabels[] = date('M', strtotime("-$i months"));
                
                $nuevosUsuarios = $this->db->table('usuarios')
                    ->where('MONTH(fecha_registro)', $mes)
                    ->where('YEAR(fecha_registro)', $año)
                    ->countAllResults();
                
                $registros[] = $nuevosUsuarios;
            }
            
            // Actividad de logs (simulado)
            $logsLabels = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'];
            $logsData = [5, 3, 8, 12, 6, 4];
            
            // Tendencias
            $tendenciasLabels = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];
            $tendenciasData = [25, 32, 28, 35];

            return [
                'actividad' => [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => 'Usuarios Activos',
                        'data' => $actividad,
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'tension' => 0.4
                    ]]
                ],
                'roles' => [
                    'labels' => $rolesLabels,
                    'data' => $rolesData,
                    'colors' => array_slice($rolesColors, 0, count($rolesLabels))
                ],
                'registros' => [
                    'labels' => $registrosLabels,
                    'data' => $registros
                ],
                'logs' => [
                    'labels' => $logsLabels,
                    'datasets' => [[
                        'label' => 'Errores',
                        'data' => $logsData,
                        'borderColor' => '#dc3545',
                        'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                        'tension' => 0.4
                    ]]
                ],
                'tendencias' => [
                    'labels' => $tendenciasLabels,
                    'datasets' => [[
                        'label' => 'Nuevos Usuarios',
                        'data' => $tendenciasData,
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'tension' => 0.4
                    ]]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'actividad' => ['labels' => [], 'datasets' => []],
                'roles' => ['labels' => [], 'data' => [], 'colors' => []],
                'registros' => ['labels' => [], 'data' => []],
                'logs' => ['labels' => [], 'datasets' => []],
                'tendencias' => ['labels' => [], 'datasets' => []]
            ];
        }
    }

    private function obtenerDatosTablasEstadisticas()
    {
        try {
            // Top 5 usuarios más activos
            $usuariosActivos = $this->db->table('usuarios u')
                ->select('u.nombre, r.nombre as rol, u.ultimo_acceso, COUNT(l.id) as acciones')
                ->join('roles r', 'r.id = u.rol_id', 'left')
                ->join('logs l', 'l.id_usuario = u.id', 'left')
                ->where('u.estado', 'Activo')
                ->groupBy('u.id')
                ->orderBy('acciones', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            // Resumen de roles
            $resumenRoles = $this->db->table('roles r')
                ->select('r.nombre, COUNT(u.id) as usuarios, r.estado, MAX(u.ultimo_acceso) as ultima_actividad')
                ->join('usuarios u', 'u.rol_id = r.id', 'left')
                ->groupBy('r.id')
                ->get()
                ->getResultArray();

            return [
                'usuarios_activos' => $usuariosActivos,
                'resumen_roles' => $resumenRoles
            ];
        } catch (\Exception $e) {
            return [
                'usuarios_activos' => [],
                'resumen_roles' => []
            ];
        }
    }

    private function obtenerKPIsEstadisticas()
    {
        try {
            $totalUsuarios = $this->db->table('usuarios')->countAllResults();
            $usuariosActivos = $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults();
            $usuariosBloqueados = $this->db->table('usuarios')->where('estado', 'Suspendido')->countAllResults();
            $usuariosInactivos = $this->db->table('usuarios')->where('estado', 'Inactivo')->countAllResults();

            $crecimientoUsuarios = $this->calcularCambioUsuarios();
            $tasaActividad = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100, 1) : 0;

            $backupDir = WRITEPATH . 'backups/';
            $backupFiles = is_dir($backupDir) ? glob($backupDir . '*.sql') : [];
            $totalRespaldos = count($backupFiles);
            $respaldosRecientes = count(array_filter($backupFiles, function($f) {
                return filemtime($f) >= strtotime('-30 days');
            }));
            $coberturaRespaldos = $totalRespaldos > 0 ? round(($respaldosRecientes / max($totalRespaldos, 1)) * 100, 1) : 0;

            $totalLogs = $this->db->table('logs')->countAllResults();
            $loginsExitosos = $this->db->table('logs')
                ->like('accion', 'login_exitoso')
                ->where('fecha_creacion >=', date('Y-m-d', strtotime('-30 days')))
                ->countAllResults();
            $intentosFallidos = $this->db->table('logs')
                ->like('accion', 'login_fallido')
                ->where('fecha_creacion >=', date('Y-m-d', strtotime('-30 days')))
                ->countAllResults();

            return [
                'crecimiento_usuarios' => $crecimientoUsuarios,
                'tasa_actividad' => $tasaActividad,
                'indice_seguridad' => $totalLogs > 0 ? round(($loginsExitosos / max($totalLogs, 1)) * 100, 1) : 0,
                'cobertura_respaldos' => $coberturaRespaldos,
                'accesos_exitosos' => $loginsExitosos,
                'intentos_fallidos' => $intentosFallidos,
                'usuarios_bloqueados' => $usuariosBloqueados,
                'respaldos_automaticos' => $respaldosRecientes
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error calculando KPIs: ' . $e->getMessage());
            return [
                'crecimiento_usuarios' => 0,
                'tasa_actividad' => 0,
                'indice_seguridad' => 0,
                'cobertura_respaldos' => 0,
                'accesos_exitosos' => 0,
                'intentos_fallidos' => 0,
                'usuarios_bloqueados' => 0,
                'respaldos_automaticos' => 0
            ];
        }
    }

    /**
     * Calcular tiempo promedio de aprobación de solicitudes
     */
    private function calcularTiempoPromedioAprobacion()
    {
        try {
            $resultado = $this->db->table('solicitudes_becas')
                ->select('AVG(TIMESTAMPDIFF(HOUR, fecha_solicitud, fecha_aprobacion)) as promedio_horas')
                ->where('estado', 'Aprobada')
                ->where('fecha_aprobacion IS NOT NULL')
                ->get()
                ->getRowArray();
            
            return round($resultado['promedio_horas'] ?? 0, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calcular satisfacción promedio de usuarios
     */
    private function calcularSatisfaccionUsuarios()
    {
        try {
            $resultado = $this->db->table('solicitudes_ayuda_mejorada')
                ->select("AVG(CAST(satisfaccion_usuario AS DECIMAL(10,2))) as promedio_satisfaccion")
                ->where('satisfaccion_usuario IS NOT NULL')
                ->where('satisfaccion_usuario !=', '')
                ->get()
                ->getRowArray();
            
            return round($resultado['promedio_satisfaccion'] ?? 0, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
