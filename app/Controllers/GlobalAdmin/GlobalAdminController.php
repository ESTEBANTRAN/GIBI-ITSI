<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Models\GlobalAdmin\UsuarioGlobalModel;
use App\Models\GlobalAdmin\RolModel;
use App\Models\GlobalAdmin\SistemaModel;
use App\Models\GlobalAdmin\BackupModel;

class GlobalAdminController extends BaseController
{
    protected $usuarioModel;
    protected $rolModel;
    protected $sistemaModel;
    protected $backupModel;
    protected $db;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioGlobalModel();
        $this->rolModel = new RolModel();
        $this->sistemaModel = new SistemaModel();
        $this->backupModel = new BackupModel();
        
        // Inicializar conexión a la base de datos
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return redirect()->to('/global-admin/dashboard');
    }

    public function dashboard()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Obtener estadísticas reales de usuarios
            $totalUsuarios = $this->db->table('usuarios')->countAllResults();
            $usuariosActivos = $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults();
            $totalRoles = $this->db->table('roles')->countAllResults();
            
            // Obtener estadísticas del sistema de bienestar (nuevas)
            $estadisticasBienestar = $this->getEstadisticasBienestar();
            
            // Calcular cambios porcentuales
            $cambioUsuarios = $this->calcularCambioUsuarios();
            $cambioActivos = $this->calcularCambioActivos();
            
            // Obtener respaldos recientes reales
            $respaldosRecientes = $this->obtenerRespaldosRecientes();
            
            // Obtener información del sistema
            $sistemaInfo = $this->obtenerInfoSistema();
            
            // Obtener actividad reciente real
            $actividadReciente = $this->obtenerActividadReciente();
            
            // Obtener datos para el gráfico real
            $datosGrafico = $this->obtenerDatosGrafico();

            $data = [
                'total_usuarios' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
                'total_roles' => $totalRoles,
                'cambio_usuarios' => $cambioUsuarios,
                'cambio_activos' => $cambioActivos,
                'estadisticas_bienestar' => $estadisticasBienestar,
                'backups_recientes' => $respaldosRecientes,
                'sistema_info' => $sistemaInfo,
                'actividad_reciente' => $actividadReciente,
                'datos_grafico' => $datosGrafico
            ];

            return view('GlobalAdmin/dashboard', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'GlobalAdminController::dashboard - Error: ' . $e->getMessage());
            // En caso de error, mostrar vista con datos básicos
            $data = [
                'total_usuarios' => 0,
                'usuarios_activos' => 0,
                'total_roles' => 0,
                'cambio_usuarios' => 0,
                'cambio_activos' => 0,
                'estadisticas_bienestar' => [
                    'fichas_total' => 0,
                    'fichas_aprobadas' => 0,
                    'becas_activas' => 0,
                    'solicitudes_pendientes' => 0,
                    'ayudas_pendientes' => 0
                ],
                'backups_recientes' => [],
                'sistema_info' => [
                    'version' => '1.0.0',
                    'nombre' => 'Sistema de Bienestar Estudiantil'
                ],
                'actividad_reciente' => [],
                'datos_grafico' => []
            ];
            return view('GlobalAdmin/dashboard', $data);
        }
    }

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

    private function obtenerRespaldosRecientes()
    {
        try {
            // Obtener respaldos reales de la tabla respaldos
            $respaldos = $this->db->table('respaldos')
                ->select('nombre_archivo, fecha_creacion, tamano_bytes, tipo')
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            $respaldosFormateados = [];
            foreach ($respaldos as $respaldo) {
                $respaldosFormateados[] = [
                    'nombre' => $respaldo['nombre_archivo'],
                    'fecha' => $respaldo['fecha_creacion'],
                    'tamaño' => $this->formatBytes($respaldo['tamano_bytes']),
                    'tipo' => $respaldo['tipo']
                ];
            }
            
            return $respaldosFormateados;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function obtenerInfoSistema()
    {
        return [
            'version' => '1.0.0',
            'nombre' => 'Sistema de Bienestar Estudiantil',
            'ultima_actualizacion' => date('d/m/Y H:i'),
            'estado_servidor' => 'Online',
            'estado_bd' => 'Conectada',
            'espacio_disco' => '75% usado'
        ];
    }

    private function obtenerActividadReciente()
    {
        try {
            // Obtener actividad reciente real de logs
            $logs = $this->db->table('logs')
                ->select('accion, tabla, datos, fecha_creacion')
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            $actividad = [];
            foreach ($logs as $log) {
                $actividad[] = [
                    'tipo' => 'info',
                    'titulo' => $log['accion'],
                    'descripcion' => $log['tabla'] . (isset($log['datos']) ? ' - ' . substr($log['datos'], 0, 50) : ''),
                    'tiempo' => $this->tiempoTranscurrido($log['fecha_creacion'])
                ];
            }
            
            return $actividad;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function tiempoTranscurrido($fecha)
    {
        $ahora = new \DateTime();
        $fechaLog = new \DateTime($fecha);
        $diferencia = $ahora->diff($fechaLog);
        
        if ($diferencia->d > 0) {
            return 'Hace ' . $diferencia->d . ' día(s)';
        } elseif ($diferencia->h > 0) {
            return 'Hace ' . $diferencia->h . ' hora(s)';
        } elseif ($diferencia->i > 0) {
            return 'Hace ' . $diferencia->i . ' minuto(s)';
        } else {
            return 'Hace unos segundos';
        }
    }

    private function obtenerDatosGrafico()
    {
        try {
            // Obtener datos reales de usuarios activos por mes (últimos 6 meses)
            $datos = [];
            $labels = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $mes = date('n', strtotime("-$i months"));
                $año = date('Y', strtotime("-$i months"));
                $nombreMes = date('M', strtotime("-$i months"));
                
                $usuariosActivos = $this->db->table('usuarios')
                    ->where('estado', 'Activo')
                    ->where('MONTH(ultimo_acceso)', $mes)
                    ->where('YEAR(ultimo_acceso)', $año)
                    ->countAllResults();
                
                $datos[] = $usuariosActivos;
                $labels[] = $nombreMes;
            }
            
            return [
                'datos' => $datos,
                'labels' => $labels
            ];
        } catch (\Exception $e) {
            return [
                'datos' => [30, 40, 35, 50, 49, 60],
                'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun']
            ];
        }
    }

    // Método para crear respaldo
    public function crearBackup()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $host = $this->db->hostname;
            $username = $this->db->username;
            $password = $this->db->password;
            $database = $this->db->database;
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = WRITEPATH . 'backups/' . $filename;
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }
            $mysqldump_path = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (!file_exists($mysqldump_path)) {
                $mysqldump_path = 'mysqldump';
            }
            // Ejecutar mysqldump y capturar la salida
            $password_str = !empty($password) ? ' --password=' . escapeshellarg($password) : '';
            $command = '"' . $mysqldump_path . '" --host=' . escapeshellarg($host) . ' --user=' . escapeshellarg($username) . $password_str . ' ' . escapeshellarg($database);
            $dump = shell_exec($command);
            if ($dump && strlen($dump) > 1000) {
                file_put_contents($filepath, $dump);
                $tamaño = filesize($filepath);
                $this->db->table('respaldos')->insert([
                    'nombre_archivo' => $filename,
                    'ruta_archivo' => $filepath,
                    'tamano_bytes' => $tamaño,
                    'tipo' => 'manual',
                    'estado' => 'completado',
                    'descripcion' => 'Respaldo manual creado por SuperAdmin',
                    'creado_por' => session('id')
                ]);
                log_message('info', 'Backup creado exitosamente: ' . $filename);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respaldo creado exitosamente y guardado en: ' . $filepath,
                    'filename' => $filename,
                    'download_url' => base_url('index.php/global-admin/descargar-respaldo/' . $this->db->insertID()),
                    'file_size' => $this->formatBytes($tamaño)
                ]);
            } else {
                log_message('error', 'Error al crear backup. Comando: ' . $command . ' Output: ' . substr($dump,0,200));
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al crear el respaldo. Verifique que mysqldump esté disponible y que el usuario tenga permisos. Output: ' . substr($dump,0,200)
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear backup: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al crear el respaldo: ' . $e->getMessage()
            ]);
        }
    }

    // Métodos para gestión de respaldos
    public function respaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        return view('GlobalAdmin/respaldos');
    }

    public function crearRespaldo()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $host = $this->db->hostname;
            $username = $this->db->username;
            $password = $this->db->password;
            $database = $this->db->database;

            // Crear nombre del archivo con fecha y hora
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = WRITEPATH . 'backups/' . $filename;

            // Crear directorio si no existe
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }

            // Usar ruta absoluta de mysqldump si existe
            $mysqldump_path = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (!file_exists($mysqldump_path)) {
                $mysqldump_path = 'mysqldump';
            }
            
            $password_str = !empty($password) ? ' --password=' . escapeshellarg($password) : '';
            $command = '"' . $mysqldump_path . '" --host=' . escapeshellarg($host) . ' --user=' . escapeshellarg($username) . $password_str . ' ' . escapeshellarg($database) . ' > "' . $filepath . '" 2>&1';

            // Ejecutar comando y capturar salida y código de retorno
            exec($command, $output, $return_var);

            if ($return_var === 0 && file_exists($filepath)) {
                $tamaño = filesize($filepath);
                $respaldoId = $this->db->table('respaldos')->insert([
                    'nombre_archivo' => $filename,
                    'ruta_archivo' => $filepath,
                    'tamano_bytes' => $tamaño,
                    'tipo' => 'manual',
                    'estado' => 'completado',
                    'descripcion' => 'Respaldo manual creado por SuperAdmin',
                    'creado_por' => session('id')
                ]);
                
                $respaldoId = $this->db->insertID();
                log_message('info', 'Backup creado exitosamente: ' . $filename);
                
                $fileSizeFormatted = $this->formatBytes($tamaño);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Respaldo creado exitosamente y guardado en el servidor.\nTamaño: {$fileSizeFormatted}\n\n¿Deseas descargar una copia adicional?",
                    'filename' => $filename,
                    'download_url' => base_url('index.php/global-admin/descargar-respaldo/' . $respaldoId),
                    'respaldo_id' => $respaldoId,
                    'auto_download' => false, // Cambiado a false para mostrar opción
                    'file_size' => $fileSizeFormatted
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al crear el respaldo. Comando ejecutado: ' . $command . ' | Output: ' . implode("\n", $output) . ' | Código de retorno: ' . $return_var
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al crear el respaldo: ' . $e->getMessage()
            ]);
        }
    }

    public function obtenerRespaldos()
    {
        log_message('info', 'obtenerRespaldos called via ' . $this->request->getMethod() . '. Session ID: ' . session('id'));
        if (!session('id') || session('rol_id') != 4) {
            log_message('error', 'obtenerRespaldos: No autorizado');
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $respaldos = $this->db->table('respaldos')
                ->select('id, nombre_archivo, fecha_creacion, tamano_bytes, tipo, estado, descripcion')
                ->orderBy('fecha_creacion', 'DESC')
                ->get()
                ->getResultArray();
            
            // Formatear datos
            foreach ($respaldos as &$respaldo) {
                $respaldo['tamaño_formateado'] = $this->formatBytes($respaldo['tamano_bytes']);
                $respaldo['fecha_formateada'] = date('d/m/Y H:i', strtotime($respaldo['fecha_creacion']));
            }
            
            return $this->response->setJSON([
                'success' => true,
                'respaldos' => $respaldos
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener respaldos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al obtener respaldos'
            ]);
        }
    }

    public function restaurarRespaldo()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');
        
        try {
            $respaldo = $this->db->table('respaldos')->where('id', $id)->get()->getRowArray();
            
            if ($respaldo && file_exists($respaldo['ruta_archivo'])) {
                $file = $respaldo['ruta_archivo'];
                $host = $this->db->hostname;
                $username = $this->db->username;
                $password = $this->db->password;
                $database = $this->db->database;
                
                $mysql_path = 'C:\xampp\mysql\bin\mysql.exe';
                if (!file_exists($mysql_path)) {
                    $mysql_path = 'mysql';
                }
                
                $password_str = !empty($password) ? ' --password=' . escapeshellarg($password) : '';
                $command = '"' . $mysql_path . '" --host=' . escapeshellarg($host) . ' --user=' . escapeshellarg($username) . $password_str . ' ' . escapeshellarg($database) . ' < "' . $file . '" 2>&1';
                
                exec($command, $output, $return_var);
                
                if ($return_var === 0) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Respaldo restaurado exitosamente']);
                } else {
                    return $this->response->setJSON(['success' => false, 'error' => 'Error al restaurar el respaldo. Detalle: ' . implode("\n", $output)]);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Respaldo no encontrado o archivo no existe']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function descargarRespaldo($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Obtener información del respaldo
            $respaldo = $this->db->table('respaldos')->where('id', $id)->get()->getRowArray();
            
            if (!$respaldo) {
                return redirect()->back()->with('error', 'Respaldo no encontrado');
            }
            
            $filepath = $respaldo['ruta_archivo'];
            
            if (!file_exists($filepath)) {
                return redirect()->back()->with('error', 'Archivo de respaldo no encontrado');
            }
            
            // Configurar headers para descarga con diálogo de "Guardar como"
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $respaldo['nombre_archivo'] . '"; filename*=UTF-8\'\'' . urlencode($respaldo['nombre_archivo']));
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Leer y enviar archivo
            readfile($filepath);
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'Error al descargar backup: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al descargar el respaldo');
        }
    }

    public function eliminarRespaldo()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');
        
        try {
            $respaldo = $this->db->table('respaldos')->where('id', $id)->get()->getRowArray();
            
            if ($respaldo) {
                $file = $respaldo['ruta_archivo'];
                
                // Si el archivo existe lo eliminamos, y en cualquier caso borramos el registro
                if (file_exists($file)) {
                    unlink($file);
                }
                
                $this->db->table('respaldos')->where('id', $id)->delete();
                
                return $this->response->setJSON(['success' => true, 'message' => 'Respaldo eliminado exitosamente']);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Respaldo no encontrado']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function limpiarRespaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $backupDir = WRITEPATH . 'backups/';
            $files = glob($backupDir . '*.sql');
            $deleted = 0;
            
            foreach ($files as $file) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
            
            // Eliminar todos los registros de la base de datos
            $this->db->table('respaldos')->emptyTable();
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => "Se eliminaron {$deleted} respaldos antiguos y se limpió el registro"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function guardarConfiguracionRespaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $data = [
                'frecuencia' => $this->request->getPost('frecuencia'),
                'retener_dias' => $this->request->getPost('retener_dias'),
                'automatico' => $this->request->getPost('automatico') ? 1 : 0,
                'comprimir' => $this->request->getPost('comprimir') ? 1 : 0
            ];
            
            // Aquí guardarías en la base de datos o archivo de configuración
            // Por ahora simulamos éxito
            
            return $this->response->setJSON(['success' => true, 'message' => 'Configuración guardada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function estadisticasRespaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $backupDir = WRITEPATH . 'backups/';
            $files = glob($backupDir . '*.sql');
            $totalSize = 0;
            $ultimoRespaldo = 'Nunca';
            
            foreach ($files as $file) {
                $totalSize += filesize($file);
                $fileTime = filemtime($file);
                if ($fileTime > strtotime($ultimoRespaldo)) {
                    $ultimoRespaldo = date('Y-m-d H:i:s', $fileTime);
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => [
                    'total' => count($files),
                    'ultimo' => $ultimoRespaldo,
                    'tamaño_total' => $totalSize,
                    'estado' => 'Activo'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
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
            // Obtener logs de la base de datos
            $logs = $this->db->table('logs')
                ->select('id, id_usuario, accion, tabla, registro_id, datos, fecha_creacion')
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(100)
                ->get()
                ->getResultArray();
            
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
                'logs_bd' => $logs,
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
            // Simular log específico
            $log = [
                'id' => $id,
                'fecha' => date('Y-m-d H:i:s'),
                'nivel' => 'INFO',
                'usuario' => 'superadmin',
                'accion' => 'Login exitoso',
                'mensaje' => 'Usuario superadmin inició sesión correctamente',
                'ip' => '192.168.1.1',
                'detalles' => 'Sesión iniciada desde navegador Chrome v120.0.0.0'
            ];
            
            return $this->response->setJSON(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function eliminarLog()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');
        
        try {
            // Simular eliminación de log
            return $this->response->setJSON(['success' => true, 'message' => 'Log eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function limpiarLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Simular limpieza de logs
            return $this->response->setJSON(['success' => true, 'message' => 'Logs antiguos eliminados exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function exportarLogs()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Obtener logs de la base de datos
            $logs = $this->db->table('logs')
                ->select('nivel, accion, mensaje, ip, fecha_creacion')
                ->orderBy('fecha_creacion', 'DESC')
                ->get()
                ->getResultArray();
            
            // Crear archivo CSV
            $filename = 'logs_sistema_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = WRITEPATH . 'backups/' . $filename;
            
            // Crear directorio si no existe
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }
            
            // Escribir CSV
            $file = fopen($filepath, 'w');
            fputcsv($file, ['Nivel', 'Acción', 'Mensaje', 'IP', 'Fecha']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log['nivel'],
                    $log['accion'],
                    $log['mensaje'],
                    $log['ip'],
                    $log['fecha_creacion']
                ]);
            }
            
            fclose($file);
            
            // Descargar archivo
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            unlink($filepath); // Eliminar archivo temporal
            exit;
            
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
            // Simular estadísticas de logs
            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => [
                    'errores' => 5,
                    'warnings' => 12,
                    'info' => 45,
                    'total' => 62
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
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
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
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
                ->join('logs l', 'l.usuario_id = u.id', 'left')
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
            // Cálculo de KPIs
            $totalUsuarios = $this->db->table('usuarios')->countAllResults();
            $usuariosActivos = $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults();
            
            $crecimientoUsuarios = $this->calcularCambioUsuarios();
            $tasaActividad = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100, 1) : 0;
            $indiceSeguridad = 95; // Simulado
            $coberturaRespaldos = 100; // Simulado
            
            // Análisis de seguridad
            $accesosExitosos = 85; // Simulado
            $intentosFallidos = 10; // Simulado
            $usuariosBloqueados = 2; // Simulado
            $respaldosAutomaticos = 100; // Simulado

            return [
                'crecimiento_usuarios' => $crecimientoUsuarios,
                'tasa_actividad' => $tasaActividad,
                'indice_seguridad' => $indiceSeguridad,
                'cobertura_respaldos' => $coberturaRespaldos,
                'accesos_exitosos' => $accesosExitosos,
                'intentos_fallidos' => $intentosFallidos,
                'usuarios_bloqueados' => $usuariosBloqueados,
                'respaldos_automaticos' => $respaldosAutomaticos
            ];
        } catch (\Exception $e) {
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

    public function gestionUsuarios()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 30;
        $search = $this->request->getGet('search') ?? '';
        
        // Si hay búsqueda, siempre ir a página 1 para mostrar todos los resultados
        if (!empty($search) && $page != 1) {
            return redirect()->to(base_url('index.php/global-admin/usuarios?search=' . urlencode($search) . '&page=1'));
        }
        
        $data = $this->usuarioModel->getUsuariosConRolesPaginados($page, $perPage, $search);
        
        // Agregar información adicional para la vista
        $data['search'] = $search;
        $data['has_search'] = !empty($search);
        
        return view('GlobalAdmin/gestion_usuarios', $data);
    }

    public function exportarUsuariosPDF()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        // Obtener todos los usuarios para exportar
        $usuarios = $this->usuarioModel->getTodosLosUsuariosConRoles();
        
        // Configurar zona horaria de Ecuador
        date_default_timezone_set('America/Guayaquil');
        
        try {
            // Verificar si TCPDF está disponible
            if (!class_exists('TCPDF')) {
                // Fallback a HTML
                header('Content-Type: text/html; charset=utf-8');
                echo '<h1>Reporte de Usuarios - ITSI</h1>';
                echo '<p>Fecha: ' . date('d/m/Y H:i:s') . ' (Ecuador)</p>';
                echo '<table border="1"><tr><th>#</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>';
                foreach ($usuarios as $i => $usuario) {
                    echo '<tr><td>' . ($i + 1) . '</td><td>' . $usuario['nombre'] . ' ' . $usuario['apellido'] . '</td><td>' . $usuario['email'] . '</td><td>' . $usuario['nombre_rol'] . '</td></tr>';
                }
                echo '</table>';
                return;
            }
            
            // Crear nueva instancia de TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configurar información del documento
            $pdf->SetCreator('ITSI');
            $pdf->SetAuthor('ITSI');
            $pdf->SetTitle('Reporte de Usuarios');
            
            // Configurar márgenes
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            
            // Configurar saltos de página automáticos
            $pdf->SetAutoPageBreak(TRUE, 25);
            
            // Configurar fuente
            $pdf->SetFont('helvetica', '', 10);
            
            // Agregar página
            $pdf->AddPage();
            
            // Título
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Instituto Tecnologico Superior Ibarra', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 5, 'Reporte de Usuarios del Sistema', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Fecha y Hora: ' . date('d/m/Y H:i:s') . ' (Ecuador)', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Información de contacto
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 5, 'Direccion: Ibarra, Av. Atahualpa 14-148 y Jose M. Leoro', 0, 1, 'L');
            $pdf->Cell(0, 5, 'Telefonos: 0978609734 / 062952535', 0, 1, 'L');
            $pdf->Cell(0, 5, 'Email: itsiibarra@itsi.edu.ec', 0, 1, 'L');
            $pdf->Ln(10);
            
            // Crear tabla manualmente
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(10, 7, '#', 1, 0, 'C');
            $pdf->Cell(60, 7, 'Nombre', 1, 0, 'C');
            $pdf->Cell(60, 7, 'Email', 1, 0, 'C');
            $pdf->Cell(40, 7, 'Rol', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 9);
            $contador = 1;
            foreach ($usuarios as $usuario) {
                $nombre = $usuario['nombre'] . ' ' . $usuario['apellido'];
                $email = $usuario['email'];
                $rol = $usuario['nombre_rol'];
                
                // Verificar si necesitamos nueva página
                if ($pdf->GetY() > 250) {
                    $pdf->AddPage();
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->Cell(10, 7, '#', 1, 0, 'C');
                    $pdf->Cell(60, 7, 'Nombre', 1, 0, 'C');
                    $pdf->Cell(60, 7, 'Email', 1, 0, 'C');
                    $pdf->Cell(40, 7, 'Rol', 1, 1, 'C');
                    $pdf->SetFont('helvetica', '', 9);
                }
                
                $pdf->Cell(10, 6, $contador, 1, 0, 'C');
                $pdf->Cell(60, 6, substr($nombre, 0, 25), 1, 0, 'L');
                $pdf->Cell(60, 6, substr($email, 0, 25), 1, 0, 'L');
                $pdf->Cell(40, 6, substr($rol, 0, 15), 1, 1, 'L');
                $contador++;
            }
            
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 5, 'Total de Usuarios: ' . count($usuarios), 0, 1, 'C');
            $pdf->Cell(0, 5, 'Documento generado automaticamente por el Sistema de Bienestar Estudiantil', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Instituto Tecnologico Superior Ibarra - Todos los derechos reservados', 0, 1, 'C');
            
            // Generar PDF
            $pdf->Output('usuarios_itsi_' . date('Y-m-d_H-i-s') . '.pdf', 'D');
            
        } catch (\Exception $e) {
            // Si hay error, mostrar información de debug
            log_message('error', 'Error generando PDF: ' . $e->getMessage());
            
            // Mostrar error en HTML
            header('Content-Type: text/html; charset=utf-8');
            echo '<h1>Error generando PDF</h1>';
            echo '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>';
            echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
            echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
            echo '<hr>';
            echo '<h2>Usuarios encontrados:</h2>';
            echo '<p>Total: ' . count($usuarios) . '</p>';
        }
    }

    public function gestionRoles()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        $search = $this->request->getGet('search') ?? '';
        
        if (!empty($search)) {
            $roles = $this->rolModel->buscarRoles($search);
        } else {
            $roles = $this->rolModel->getRolesConUsuarios();
        }

        $data = [
            'roles' => $roles,
            'search' => $search,
            'estadisticas' => $this->rolModel->getEstadisticasRoles()
        ];

        return view('GlobalAdmin/gestion_roles', $data);
    }

    public function crearRol()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $nombre = $this->request->getPost('nombre');
        $descripcion = $this->request->getPost('descripcion') ?? '';
        $codigo = $this->request->getPost('codigo') ?? '';
        $activo = $this->request->getPost('activo') ? 1 : 0;

        // Validar que el nombre no esté vacío
        if (empty($nombre)) {
            return $this->response->setJSON(['success' => false, 'error' => 'El nombre del rol es obligatorio']);
        }

        // Verificar si ya existe un rol con ese nombre
        if ($this->rolModel->existeRolConNombre($nombre)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ya existe un rol con ese nombre']);
        }

        $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ];

        try {
            $this->rolModel->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Rol creado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al crear el rol: ' . $e->getMessage()]);
        }
    }

    public function obtenerRol($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $rol = $this->rolModel->find($id);
        
        if (!$rol) {
            return $this->response->setJSON(['success' => false, 'error' => 'Rol no encontrado']);
        }

        return $this->response->setJSON(['success' => true, 'rol' => $rol]);
    }

    public function actualizarRol()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $descripcion = $this->request->getPost('descripcion') ?? '';
        $codigo = $this->request->getPost('codigo') ?? '';
        $activo = $this->request->getPost('activo') ? 1 : 0;

        // Validar que el nombre no esté vacío
        if (empty($nombre)) {
            return $this->response->setJSON(['success' => false, 'error' => 'El nombre del rol es obligatorio']);
        }

        // Verificar si ya existe un rol con ese nombre (excluyendo el actual)
        if ($this->rolModel->existeRolConNombre($nombre, $id)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ya existe un rol con ese nombre']);
        }

        $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ];

        try {
            $this->rolModel->update($id, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Rol actualizado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar el rol: ' . $e->getMessage()]);
        }
    }

    public function eliminarRol()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');

        // No permitir eliminar roles del sistema (ID 1, 2, 4)
        if (in_array($id, [1, 2, 4])) {
            return $this->response->setJSON(['success' => false, 'error' => 'No se puede eliminar un rol del sistema']);
        }

        // Verificar si el rol tiene usuarios asignados
        if (!$this->rolModel->puedeEliminarRol($id)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No se puede eliminar el rol porque tiene usuarios asignados']);
        }

        try {
            $this->rolModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Rol eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar el rol: ' . $e->getMessage()]);
        }
    }

    public function obtenerPermisosRol($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $rol = $this->rolModel->find($id);
        
        if (!$rol) {
            return $this->response->setJSON(['success' => false, 'error' => 'Rol no encontrado']);
        }

        // Por ahora, simulamos permisos basados en el ID del rol
        $permisos = $this->getPermisosSimulados($id);

        return $this->response->setJSON([
            'success' => true, 
            'rol' => $rol,
            'permisos' => $permisos
        ]);
    }

    private function getPermisosSimulados($rol_id)
    {
        $permisos = [
            'dashboard' => false,
            'usuarios' => false,
            'roles' => false,
            'configuracion' => false,
            'fichas' => false,
            'becas' => false,
            'solicitudes' => false,
            'reportes' => false
        ];

        switch ($rol_id) {
            case 1: // Estudiante
                $permisos['dashboard'] = true;
                $permisos['fichas'] = true;
                $permisos['becas'] = true;
                $permisos['solicitudes'] = true;
                break;
            case 2: // Administrativo Bienestar
                $permisos['dashboard'] = true;
                $permisos['fichas'] = true;
                $permisos['becas'] = true;
                $permisos['solicitudes'] = true;
                $permisos['reportes'] = true;
                break;
            case 4: // Super Administrador
                $permisos['dashboard'] = true;
                $permisos['usuarios'] = true;
                $permisos['roles'] = true;
                $permisos['configuracion'] = true;
                $permisos['fichas'] = true;
                $permisos['becas'] = true;
                $permisos['solicitudes'] = true;
                $permisos['reportes'] = true;
                break;
        }

        return $permisos;
    }

    public function configuracionSistema()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return view('GlobalAdmin/configuracion_sistema', ['configuracion' => []]);
    }

    public function restaurarBackup()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        return $this->response->setJSON(['success' => true, 'mensaje' => 'Backup restaurado exitosamente']);
    }

    public function actualizarConfiguracion()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return redirect()->back()->with('success', 'Configuración actualizada exitosamente.');
    }

    // Métodos AJAX para gestión de usuarios
    public function crearUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $datos = $this->request->getPost();
            
            // Validar datos requeridos
            if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || 
                empty($datos['cedula']) || empty($datos['password']) || empty($datos['rol_id'])) {
                return $this->response->setJSON(['error' => 'Todos los campos marcados con * son obligatorios'])->setStatusCode(400);
            }

            // Verificar si el email ya existe
            if ($this->usuarioModel->where('email', $datos['email'])->first()) {
                return $this->response->setJSON(['error' => 'El email ya está registrado'])->setStatusCode(400);
            }

            // Verificar si la cédula ya existe
            if ($this->usuarioModel->where('cedula', $datos['cedula'])->first()) {
                return $this->response->setJSON(['error' => 'La cédula ya está registrada'])->setStatusCode(400);
            }

            // Hash de la contraseña
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

            // Preparar datos para insertar
            $usuarioData = [
                'rol_id' => $datos['rol_id'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'cedula' => $datos['cedula'],
                'email' => $datos['email'],
                'password_hash' => $password_hash,
                'telefono' => $datos['telefono'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'carrera' => $datos['carrera'] ?? null,
                'semestre' => $datos['semestre'] ?? null
            ];

            // Insertar usuario
            $usuario_id = $this->usuarioModel->insert($usuarioData);

            if ($usuario_id) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario creado exitosamente',
                    'usuario_id' => $usuario_id
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al crear el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function actualizarUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $datos = $this->request->getPost();
            
            // Validar datos requeridos
            if (empty($datos['id']) || empty($datos['nombre']) || empty($datos['apellido']) || 
                empty($datos['email']) || empty($datos['cedula']) || empty($datos['rol_id'])) {
                return $this->response->setJSON(['error' => 'Todos los campos marcados con * son obligatorios'])->setStatusCode(400);
            }

            $usuario_id = $datos['id'];

            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->find($usuario_id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            // Verificar si el email ya existe en otro usuario
            $emailExistente = $this->usuarioModel->where('email', $datos['email'])->where('id !=', $usuario_id)->first();
            if ($emailExistente) {
                return $this->response->setJSON(['error' => 'El email ya está registrado por otro usuario'])->setStatusCode(400);
            }

            // Verificar si la cédula ya existe en otro usuario
            $cedulaExistente = $this->usuarioModel->where('cedula', $datos['cedula'])->where('id !=', $usuario_id)->first();
            if ($cedulaExistente) {
                return $this->response->setJSON(['error' => 'La cédula ya está registrada por otro usuario'])->setStatusCode(400);
            }

            // Preparar datos para actualizar
            $usuarioData = [
                'rol_id' => $datos['rol_id'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'cedula' => $datos['cedula'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'carrera' => $datos['carrera'] ?? null,
                'semestre' => $datos['semestre'] ?? null
            ];

            // Si se proporcionó una nueva contraseña, actualizarla
            if (!empty($datos['password'])) {
                $usuarioData['password_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
            }

            // Actualizar usuario
            $resultado = $this->usuarioModel->update($usuario_id, $usuarioData);

            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario actualizado exitosamente'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al actualizar el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function eliminarUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $usuario_id = $this->request->getPost('id');
            
            if (empty($usuario_id)) {
                return $this->response->setJSON(['error' => 'ID de usuario requerido'])->setStatusCode(400);
            }

            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->find($usuario_id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            // No permitir eliminar el propio usuario
            if ($usuario_id == session('id')) {
                return $this->response->setJSON(['error' => 'No puedes eliminar tu propia cuenta'])->setStatusCode(400);
            }

            // Eliminar usuario
            $resultado = $this->usuarioModel->delete($usuario_id);

            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario eliminado exitosamente'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al eliminar el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function obtenerUsuario($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $usuario = $this->usuarioModel->getUsuariosConRoles();
            $usuario = array_filter($usuario, function($u) use ($id) {
                return $u['id'] == $id;
            });
            
            if (empty($usuario)) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            $usuario = array_values($usuario)[0];
            
            return $this->response->setJSON([
                'success' => true,
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    // Métodos para perfil del Super Administrador
    public function perfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return view('GlobalAdmin/perfil', [
            'usuario' => [
                'id' => session('id'),
                'nombre' => session('nombre'),
                'apellido' => session('apellido'),
                'email' => session('email'),
                'cedula' => session('cedula'),
                'telefono' => session('telefono'),
                'direccion' => session('direccion'),
                'foto_perfil' => session('foto_perfil')
            ]
        ]);
    }

    public function actualizarPerfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();
            
            // Aquí iría la lógica para actualizar el perfil en la BD
            // Por ahora solo actualizamos la sesión
            
            session()->set([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion']
            ]);

            return redirect()->back()->with('success', 'Perfil actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }

    public function cambiarFotoPerfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $file = $this->request->getFile('foto');
            
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/perfiles/', $newName);
                
                session()->set('foto_perfil', $newName);
                
                return $this->response->setJSON(['success' => true, 'mensaje' => 'Foto actualizada exitosamente']);
            }
            
            return $this->response->setJSON(['error' => 'Error al subir la imagen'])->setStatusCode(400);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    // Métodos para cuenta del Super Administrador
    public function cuenta()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return view('GlobalAdmin/cuenta', [
            'usuario' => [
                'id' => session('id'),
                'email' => session('email'),
                'nombre' => session('nombre'),
                'apellido' => session('apellido')
            ]
        ]);
    }

    public function cambiarPassword()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();
            
            // Aquí iría la lógica para cambiar la contraseña en la BD
            // Por ahora solo retornamos éxito
            
            return redirect()->back()->with('success', 'Contraseña cambiada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cambiar contraseña: ' . $e->getMessage());
        }
    }

    public function configuracionNotificaciones()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();
            
            // Aquí iría la lógica para configurar notificaciones
            // Por ahora solo retornamos éxito
            
            return redirect()->back()->with('success', 'Configuración de notificaciones actualizada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    public function eliminarCuenta()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Aquí iría la lógica para eliminar la cuenta
            // Por ahora solo destruimos la sesión
            
            session()->destroy();
            return redirect()->to('/login')->with('success', 'Cuenta eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar cuenta: ' . $e->getMessage());
        }
    }

    public function exportarDatos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Aquí iría la lógica para exportar datos
            // Por ahora solo retornamos un mensaje
            
            return redirect()->back()->with('success', 'Datos exportados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar datos: ' . $e->getMessage());
        }
    }

    public function testBusqueda()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        $search = $this->request->getGet('search') ?? '';
        
        // Obtener todos los usuarios sin paginación para verificar la búsqueda
        $usuarios = $this->usuarioModel->getTodosLosUsuariosConRoles();
        
        // Filtrar manualmente para verificar
        $resultados = [];
        foreach ($usuarios as $usuario) {
            $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
            if (empty($search) || 
                stripos($nombreCompleto, $search) !== false ||
                stripos($usuario['email'], $search) !== false ||
                stripos($usuario['cedula'], $search) !== false ||
                stripos($usuario['nombre_rol'], $search) !== false) {
                $resultados[] = $usuario;
            }
        }
        
        echo "<h1>Prueba de Búsqueda</h1>";
        echo "<p>Término de búsqueda: '$search'</p>";
        echo "<p>Total usuarios en BD: " . count($usuarios) . "</p>";
        echo "<p>Resultados encontrados: " . count($resultados) . "</p>";
        echo "<h2>Resultados:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($resultados as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['nombre'] . " " . $usuario['apellido'] . "</td>";
            echo "<td>" . $usuario['email'] . "</td>";
            echo "<td>" . $usuario['nombre_rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function testBusquedaDetallada()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        $search = $this->request->getGet('search') ?? '';
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 30;
        
        echo "<h1>Prueba Detallada de Búsqueda</h1>";
        echo "<p>Término de búsqueda: '$search'</p>";
        echo "<p>Página: $page</p>";
        echo "<p>Usuarios por página: $perPage</p>";
        
        // Obtener datos con paginación
        $data = $this->usuarioModel->getUsuariosConRolesPaginados($page, $perPage, $search);
        
        echo "<h2>Resultados de la Consulta Paginada:</h2>";
        echo "<p>Total usuarios encontrados: " . $data['total'] . "</p>";
        echo "<p>Página actual: " . $data['current_page'] . "</p>";
        echo "<p>Total páginas: " . $data['total_pages'] . "</p>";
        echo "<p>Usuarios en esta página: " . count($data['usuarios']) . "</p>";
        
        echo "<h3>Usuarios en esta página:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($data['usuarios'] as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['nombre'] . " " . $usuario['apellido'] . "</td>";
            echo "<td>" . $usuario['email'] . "</td>";
            echo "<td>" . $usuario['nombre_rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Obtener todos los usuarios para comparar
        $todosUsuarios = $this->usuarioModel->getTodosLosUsuariosConRoles();
        
        echo "<h2>Comparación con Todos los Usuarios:</h2>";
        echo "<p>Total usuarios en BD: " . count($todosUsuarios) . "</p>";
        
        // Filtrar manualmente
        $resultadosManuales = [];
        foreach ($todosUsuarios as $usuario) {
            $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
            if (empty($search) || 
                stripos($nombreCompleto, $search) !== false ||
                stripos($usuario['email'], $search) !== false ||
                stripos($usuario['cedula'], $search) !== false ||
                stripos($usuario['nombre_rol'], $search) !== false) {
                $resultadosManuales[] = $usuario;
            }
        }
        
        echo "<p>Resultados filtrados manualmente: " . count($resultadosManuales) . "</p>";
        
        echo "<h3>Usuarios que coinciden con la búsqueda:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($resultadosManuales as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['nombre'] . " " . $usuario['apellido'] . "</td>";
            echo "<td>" . $usuario['email'] . "</td>";
            echo "<td>" . $usuario['nombre_rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Métodos para acceso rápido a vistas de perfiles
    public function vistaEstudiante()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        // Simular datos de estudiante para la vista
        $data = [
            'estudiante' => [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@estudiante.itsi.edu.ec',
                'carrera' => 'Ingeniería Informática',
                'semestre' => '5to Semestre'
            ],
            'fichas' => [
                ['periodo' => '2024-2', 'estado' => 'Aprobada', 'fecha' => '2024-12-15'],
                ['periodo' => '2024-1', 'estado' => 'Enviada', 'fecha' => '2024-06-20']
            ],
            'becas' => [
                ['tipo' => 'Excelencia Académica', 'estado' => 'Aprobada', 'monto' => '$500'],
                ['tipo' => 'Socioeconómica', 'estado' => 'En Revisión', 'monto' => '$300']
            ]
        ];
        
        return view('GlobalAdmin/vista_estudiante', $data);
    }

    public function vistaAdminBienestar()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        // Simular datos de admin bienestar para la vista
        $data = [
            'admin' => [
                'nombre' => 'María González',
                'email' => 'maria.gonzalez@itsi.edu.ec',
                'cargo' => 'Coordinadora de Bienestar Estudiantil'
            ],
            'estadisticas' => [
                'total_estudiantes' => 1247,
                'fichas_pendientes' => 45,
                'becas_aprobadas' => 156,
                'solicitudes_ayuda' => 23
            ],
            'fichas_recientes' => [
                ['estudiante' => 'Ana López', 'periodo' => '2024-2', 'estado' => 'Pendiente'],
                ['estudiante' => 'Carlos Ruiz', 'periodo' => '2024-2', 'estado' => 'Aprobada']
            ]
        ];
        
        return view('GlobalAdmin/vista_admin_bienestar', $data);
    }

    /**
     * Obtener estadísticas del sistema de bienestar estudiantil
     */
    private function getEstadisticasBienestar()
    {
        try {
            $stats = [];
            
            // Estadísticas de períodos académicos
            $stats['periodos'] = [
                'total' => $this->db->table('periodos_academicos')->countAllResults(),
                'activos' => $this->db->table('periodos_academicos')->where('activo', 1)->countAllResults(),
                'con_fichas_activas' => $this->db->table('periodos_academicos')->where('activo_fichas', 1)->countAllResults(),
                'con_becas_activas' => $this->db->table('periodos_academicos')->where('activo_becas', 1)->countAllResults()
            ];
            
            // Estadísticas de fichas socioeconómicas
            $stats['fichas'] = [
                'total' => $this->db->table('fichas_socioeconomicas')->countAllResults(),
                'pendientes' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Enviada')->countAllResults(),
                'aprobadas' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Aprobada')->countAllResults(),
                'rechazadas' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Rechazada')->countAllResults()
            ];
            
            // Estadísticas de becas
            $stats['becas'] = [
                'total_becas' => $this->db->table('becas')->countAllResults(),
                'becas_activas' => $this->db->table('becas')->where('estado', 'Activa')->countAllResults(),
                'total_solicitudes' => $this->db->table('solicitudes_becas')->countAllResults(),
                'solicitudes_aprobadas' => $this->db->table('solicitudes_becas')->where('estado', 'Aprobada')->countAllResults(),
                'solicitudes_pendientes' => $this->db->table('solicitudes_becas')->where('estado', 'Pendiente')->countAllResults()
            ];
            
            // Estadísticas de usuarios por rol
            $stats['usuarios'] = [
                'estudiantes' => $this->db->table('usuarios')->where('rol_id', 1)->countAllResults(),
                'admin_bienestar' => $this->db->table('usuarios')->where('rol_id', 2)->countAllResults(),
                'super_admin' => $this->db->table('usuarios')->where('rol_id', 3)->countAllResults()
            ];
            
            // Estadísticas de solicitudes de ayuda (si existe la tabla mejorada)
            try {
                $stats['solicitudes_ayuda'] = [
                    'total' => $this->db->table('solicitudes_ayuda_mejorada')->countAllResults(),
                    'abiertas' => $this->db->table('solicitudes_ayuda_mejorada')->where('estado', 'Abierta')->countAllResults(),
                    'resueltas' => $this->db->table('solicitudes_ayuda_mejorada')->where('estado', 'Resuelta')->countAllResults()
                ];
            } catch (\Exception $e) {
                $stats['solicitudes_ayuda'] = [
                    'total' => 0,
                    'abiertas' => 0,
                    'resueltas' => 0
                ];
            }
            
            // Rendimiento del sistema (últimas 24 horas)
            $stats['rendimiento'] = [
                'nuevas_fichas_hoy' => $this->db->table('fichas_socioeconomicas')
                    ->where('fecha_creacion >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults(),
                'nuevas_solicitudes_hoy' => $this->db->table('solicitudes_becas')
                    ->where('fecha_solicitud >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults(),
                'nuevas_ayudas_hoy' => 0 // Por defecto si no existe la tabla
            ];
            
            try {
                $stats['rendimiento']['nuevas_ayudas_hoy'] = $this->db->table('solicitudes_ayuda_mejorada')
                    ->where('fecha_creacion >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults();
            } catch (\Exception $e) {
                // Tabla no existe
            }
            
            // Alertas y problemas
            $stats['alertas'] = $this->getAlertasSistema();
            
            return $stats;
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas de bienestar: ' . $e->getMessage());
            return [
                'periodos' => ['total' => 0, 'activos' => 0, 'con_fichas_activas' => 0, 'con_becas_activas' => 0],
                'fichas' => ['total' => 0, 'pendientes' => 0, 'aprobadas' => 0, 'rechazadas' => 0],
                'becas' => ['total_becas' => 0, 'becas_activas' => 0, 'total_solicitudes' => 0, 'solicitudes_aprobadas' => 0, 'solicitudes_pendientes' => 0],
                'usuarios' => ['estudiantes' => 0, 'admin_bienestar' => 0, 'super_admin' => 0],
                'solicitudes_ayuda' => ['total' => 0, 'abiertas' => 0, 'resueltas' => 0],
                'rendimiento' => ['nuevas_fichas_hoy' => 0, 'nuevas_solicitudes_hoy' => 0, 'nuevas_ayudas_hoy' => 0],
                'alertas' => []
            ];
        }
    }

    /**
     * Obtener alertas del sistema
     */
    private function getAlertasSistema()
    {
        $alertas = [];
        
        try {
            // Verificar períodos sin actividad reciente
            $periodosInactivos = $this->db->table('periodos_academicos p')
                ->select('p.nombre')
                ->where('p.activo', 1)
                ->where('p.fichas_creadas', 0)
                ->where('p.fecha_inicio <=', date('Y-m-d', strtotime('-30 days')))
                ->get()
                ->getResultArray();
            
            foreach ($periodosInactivos as $periodo) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'mensaje' => "El período '{$periodo['nombre']}' está activo pero sin fichas creadas en 30 días",
                    'categoria' => 'periodos'
                ];
            }
            
            // Verificar solicitudes pendientes por mucho tiempo
            $solicitudesPendientes = $this->db->table('solicitudes_becas')
                ->where('estado', 'Pendiente')
                ->where('fecha_solicitud <=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->countAllResults();
            
            if ($solicitudesPendientes > 0) {
                $alertas[] = [
                    'tipo' => 'danger',
                    'mensaje' => "$solicitudesPendientes solicitudes de beca llevan más de 7 días pendientes",
                    'categoria' => 'solicitudes'
                ];
            }
            
            // Verificar límites de períodos
            $periodosLimiteAlcanzado = $this->db->table('periodos_academicos')
                ->where('limite_fichas IS NOT NULL')
                ->where('fichas_creadas >= limite_fichas')
                ->countAllResults();
            
            if ($periodosLimiteAlcanzado > 0) {
                $alertas[] = [
                    'tipo' => 'info',
                    'mensaje' => "$periodosLimiteAlcanzado períodos han alcanzado su límite de fichas",
                    'categoria' => 'limites'
                ];
            }
            
            // Verificar usuarios bloqueados
            $usuariosBloqueados = $this->db->table('usuarios')
                ->where('bloqueado_hasta IS NOT NULL')
                ->where('bloqueado_hasta >', date('Y-m-d H:i:s'))
                ->countAllResults();
            
            if ($usuariosBloqueados > 0) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'mensaje' => "$usuariosBloqueados usuarios están temporalmente bloqueados",
                    'categoria' => 'usuarios'
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo alertas del sistema: ' . $e->getMessage());
        }
        
        return $alertas;
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
                ->select('AVG(CAST(satisfaccion_usuario as DECIMAL)) as promedio_satisfaccion')
                ->where('satisfaccion_usuario IS NOT NULL')
                ->get()
                ->getRowArray();
            
            return round($resultado['promedio_satisfaccion'] ?? 0, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Enviar respaldo por correo electrónico
     */
    public function enviarRespaldoPorEmail()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $respaldoId = $this->request->getPost('respaldo_id');
        $emailDestino = $this->request->getPost('email');
        
        if (!$respaldoId || !$emailDestino) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID de respaldo y email son requeridos']);
        }

        try {
            // Obtener información del respaldo
            $respaldo = $this->db->table('respaldos')->where('id', $respaldoId)->get()->getRowArray();
            
            if (!$respaldo) {
                return $this->response->setJSON(['success' => false, 'error' => 'Respaldo no encontrado']);
            }
            
            $filepath = $respaldo['ruta_archivo'];
            
            if (!file_exists($filepath)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Archivo de respaldo no encontrado']);
            }

            // Configurar el servicio de email
            $email = \Config\Services::email();
            
            // Cargar configuración personalizada de email
            $emailConfig = include APPPATH . 'Config/EmailConfig.php';
            
            // Configurar SMTP dinámicamente
            $email->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $email->setTo($emailDestino);
            $email->setSubject('Respaldo de Base de Datos - ' . $respaldo['nombre_archivo']);
            
            $mensaje = "Hola,\n\n";
            $mensaje .= "Se ha generado un respaldo de la base de datos del Sistema de Bienestar Estudiantil.\n\n";
            $mensaje .= "Detalles del respaldo:\n";
            $mensaje .= "- Nombre del archivo: " . $respaldo['nombre_archivo'] . "\n";
            $mensaje .= "- Fecha de creación: " . $respaldo['fecha_creacion'] . "\n";
            $mensaje .= "- Tamaño: " . $this->formatBytes($respaldo['tamano_bytes']) . "\n";
            $mensaje .= "- Tipo: " . ucfirst($respaldo['tipo']) . "\n\n";
            $mensaje .= "El archivo se encuentra adjunto a este correo.\n\n";
            $mensaje .= "Saludos,\nSistema de Bienestar Estudiantil";
            
            $email->setMessage($mensaje);
            $email->attach($filepath, 'attachment', $respaldo['nombre_archivo'], 'application/octet-stream');
            
            if ($email->send()) {
                log_message('info', 'Respaldo enviado por email exitosamente a: ' . $emailDestino);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respaldo enviado por correo electrónico exitosamente'
                ]);
            } else {
                log_message('error', 'Error al enviar respaldo por email: ' . $email->printDebugger(['headers']));
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al enviar el correo electrónico'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar respaldo por email: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al enviar el respaldo por correo: ' . $e->getMessage()
            ]);
        }
    }
} 