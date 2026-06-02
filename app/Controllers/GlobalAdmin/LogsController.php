<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Security\InputSanitizerTrait;

class LogsController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Métodos para logs del sistema
    public function logs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        return view('GlobalAdmin/logs');
    }

    public function obtenerLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $pagina = (int)($this->request->getGet('pagina') ?? 1);
            $porPagina = 30;
            $offset = ($pagina - 1) * $porPagina;

            $builder = $this->db->table('logs l');
            $builder->select("
                l.id,
                l.id_usuario,
                l.accion,
                COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sistema') AS usuario,
                l.tabla,
                l.registro_id,
                COALESCE(l.datos, l.accion) AS mensaje,
                l.fecha_creacion AS fecha,
                'INFO' AS nivel,
                'N/A' AS ip
            ");
            $builder->join('usuarios u', 'u.id = l.id_usuario', 'left');

            // Filtros desde la vista
            $nivel = $this->request->getGet('nivel');
            $fecha = $this->request->getGet('fecha');
            $usuario = $this->request->getGet('usuario');

            if (!empty($nivel)) {
                $builder->where('l.accion', $nivel);
            }
            if (!empty($fecha)) {
                $builder->where('DATE(l.fecha_creacion)', $fecha);
            }
            if (!empty($usuario)) {
                $builder->like('u.nombre', $usuario);
                $builder->orLike('u.apellido', $usuario);
            }

            $builder->orderBy('l.fecha_creacion', 'DESC');

            $total = (clone $builder)->countAllResults(false);
            $logs = $builder->limit($porPagina, $offset)->get()->getResultArray();

            $totalPaginas = max(1, (int)ceil($total / $porPagina));

            // Obtener logs de archivos (writable/logs)
            $logFiles = [];
            $logDir = WRITEPATH . 'logs/';
            
            if (is_dir($logDir)) {
                $files = glob($logDir . '*.log');
                foreach ($files as $file) {
                    $filename = basename($file);
                    $filesize = filesize($file);
                    $logFiles[] = [
                        'nombre' => $filename,
                        'tamaño' => $this->formatBytes($filesize),
                        'fecha_modificacion' => date('d/m/Y H:i', filemtime($file)),
                        'ruta' => $file
                    ];
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'logs' => $logs,
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas
                ],
                'logs_archivos' => $logFiles
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener logs: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al obtener logs'
            ]);
        }
    }

    public function obtenerLog($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $log = $this->db->table('logs l')
                ->select("
                    l.id,
                    l.id_usuario,
                    l.accion,
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sistema') AS usuario,
                    l.tabla,
                    l.registro_id,
                    COALESCE(l.datos, l.accion) AS mensaje,
                    l.fecha_creacion AS fecha,
                    'INFO' AS nivel,
                    'N/A' AS ip
                ")
                ->join('usuarios u', 'u.id = l.id_usuario', 'left')
                ->where('l.id', $id)
                ->get()
                ->getRowArray();
            
            if (!$log) {
                return $this->response->setJSON(['success' => false, 'error' => 'Log no encontrado']);
            }

            return $this->response->setJSON(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo log: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al obtener el log']);
        }
    }

    public function eliminarLog()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->getPostInt('id');
        
        try {
            $this->db->table('logs')->where('id', $id)->delete();
            return $this->response->setJSON(['success' => true, 'message' => 'Log eliminado exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error eliminando log: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar el log']);
        }
    }

    public function limpiarLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $dias = $this->getPostInt('dias') ?: 90;
            $fechaLimite = date('Y-m-d', strtotime("-{$dias} days"));
            
            $this->db->table('logs')->where('fecha_creacion <', $fechaLimite)->delete();
            
            return $this->response->setJSON(['success' => true, 'message' => "Logs anteriores a {$dias} días eliminados exitosamente"]);
        } catch (\Exception $e) {
            log_message('error', 'Error limpiando logs: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al limpiar los logs']);
        }
    }

    public function exportarLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Descargar archivo de log específico
            $archivo = $this->request->getGet('archivo');
            if ($archivo) {
                $filepath = WRITEPATH . 'logs/' . basename($archivo);
                if (is_file($filepath)) {
                    return $this->response->download($filepath, null);
                }
                return redirect()->back()->with('error', 'Archivo no encontrado');
            }

            // Exportar logs de BD como CSV
            $logs = $this->db->table('logs l')
                ->select("
                    l.accion,
                    l.tabla,
                    l.registro_id,
                    COALESCE(l.datos, l.accion) AS mensaje,
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sistema') AS usuario,
                    l.fecha_creacion
                ")
                ->join('usuarios u', 'u.id = l.id_usuario', 'left')
                ->orderBy('l.fecha_creacion', 'DESC')
                ->get()
                ->getResultArray();

            $filename = 'logs_sistema_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = WRITEPATH . 'backups/' . $filename;

            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }

            $file = fopen($filepath, 'w');
            fputcsv($file, ['Acción', 'Tabla', 'Registro ID', 'Mensaje', 'Usuario', 'Fecha']);

            foreach ($logs as $log) {
                fputcsv($file, $log);
            }

            fclose($file);

            return $this->response->download($filepath, null)->deleteFile();

        } catch (\Exception $e) {
            log_message('error', 'Error al exportar logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar logs');
        }
    }

    public function estadisticasLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $total = $this->db->table('logs')->countAllResults();
            $ultimas24h = $this->db->table('logs')
                ->where('fecha_creacion >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->countAllResults();

            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => [
                    'errores' => 0,
                    'warnings' => 0,
                    'info' => $total,
                    'total' => $total,
                    'ultimas24h' => $ultimas24h
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas logs: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al obtener estadísticas']);
        }
    }
}
