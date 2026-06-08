<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Security\InputSanitizerTrait;

class BackupsController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // Métodos para gestión de respaldos
    public function respaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        return view('GlobalAdmin/respaldos');
    }

    /**
     * Crea un archivo temporal de credenciales para mysqldump/mysql
     * evitando exponer la contraseña en procesos del sistema (--password=).
     */
    private function getDbCredentialsFile(): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mycnf_');
        $content = "[client]\n";
        $content .= "user=\"{$this->db->username}\"\n";
        if (!empty($this->db->password)) {
            $content .= "password=\"{$this->db->password}\"\n";
        }
        $content .= "host=\"{$this->db->hostname}\"\n";
        file_put_contents($tmpFile, $content);
        @chmod($tmpFile, 0600);
        return $tmpFile;
    }

    private function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function crearRespaldo()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // -------------------------------------------------------
            // VERIFICACIÓN: exec() puede estar deshabilitado en hosting
            // compartido (InfinityFree, cPanel, etc.)
            // -------------------------------------------------------
            $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
            if (!function_exists('exec') || in_array('exec', $disabledFunctions)) {
                log_message('warning', 'BackupsController::crearRespaldo - exec() está deshabilitado en este servidor.');
                return $this->response->setJSON([
                    'success' => false,
                    'error'   => 'La función de respaldo automático no está disponible en este servidor de hosting. Por favor, descarga el respaldo manualmente desde phpMyAdmin (panel de InfinityFree → MySQL Databases → phpMyAdmin → Exportar).'
                ]);
            }

            $database = $this->db->database;

            // Crear nombre del archivo con fecha y hora
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = WRITEPATH . 'backups/' . $filename;

            // Crear directorio si no existe
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }

            // Usar ruta absoluta de mysqldump si existe
            $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            if (!file_exists($mysqldump_path)) {
                $mysqldump_path = 'mysqldump';
            }
            
            $credentialsFile = $this->getDbCredentialsFile();
            $command = '"' . $mysqldump_path . '" --defaults-extra-file=' . escapeshellarg($credentialsFile) . ' ' . escapeshellarg($database) . ' > "' . $filepath . '" 2>&1';

            // Ejecutar comando y capturar salida y código de retorno
            exec($command, $output, $return_var);
            @unlink($credentialsFile);

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
                log_message('info', 'Backup creado exitosamente local: ' . $filename);
                
                // Mirror obligatorio a Google Drive
                $driveId = \App\Helpers\GoogleDriveHelper::subirArchivo(
                    $filepath,
                    $filename,
                    'application/sql',
                    'backups' // Subcarpeta destino
                );
                
                $cloudMsg = $driveId ? " y sincronizado con Google Drive" : "";
                $fileSizeFormatted = $this->formatBytes($tamaño);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Respaldo creado exitosamente en servidor{$cloudMsg}.\nTamaño: {$fileSizeFormatted}\n\n¿Deseas descargar una copia adicional?",
                    'filename' => $filename,
                    'download_url' => base_url('index.php/global-admin/descargar-respaldo/' . $respaldoId),
                    'respaldo_id' => $respaldoId,
                    'auto_download' => false, // Cambiado a false para mostrar opción
                    'file_size' => $fileSizeFormatted
                ]);
            } else {
                log_message('error', 'Error al crear respaldo. Código: ' . $return_var);
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al crear el respaldo. Verifique que mysqldump esté disponible.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear respaldo: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al crear el respaldo'
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

        $id = $this->getPostInt('id');
        
        try {
            $respaldo = $this->db->table('respaldos')->where('id', $id)->get()->getRowArray();
            
            if ($respaldo && file_exists($respaldo['ruta_archivo'])) {
                $file = $respaldo['ruta_archivo'];
                $database = $this->db->database;
                
                $mysql_path = 'C:\\xampp\\mysql\\bin\\mysql.exe';
                if (!file_exists($mysql_path)) {
                    $mysql_path = 'mysql';
                }
                
                $credentialsFile = $this->getDbCredentialsFile();
                $command = '"' . $mysql_path . '" --defaults-extra-file=' . escapeshellarg($credentialsFile) . ' ' . escapeshellarg($database) . ' < "' . $file . '" 2>&1';
                
                exec($command, $output, $return_var);
                @unlink($credentialsFile);
                
                if ($return_var === 0) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Respaldo restaurado exitosamente']);
                } else {
                    log_message('error', 'Error al restaurar respaldo. Código: ' . $return_var);
                    return $this->response->setJSON(['success' => false, 'error' => 'Error al restaurar el respaldo']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Respaldo no encontrado o archivo no existe']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al restaurar respaldo: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al restaurar el respaldo']);
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
            
            return $this->response->download($filepath, null)->setFileName($respaldo['nombre_archivo']);
            
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

        $id = $this->getPostInt('id');
        
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
            log_message('error', 'Error al eliminar respaldo: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar el respaldo']);
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
            log_message('error', 'Error al limpiar respaldos: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al limpiar los respaldos']);
        }
    }

    public function guardarConfiguracionRespaldos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $data = [
                'frecuencia' => $this->getPostString('frecuencia'),
                'retener_dias' => $this->getPostString('retener_dias'),
                'automatico' => $this->getPostBool('automatico') ? 1 : 0,
                'comprimir' => $this->getPostBool('comprimir') ? 1 : 0
            ];
            
            foreach ($data as $clave => $valor) {
                $exists = $this->db->table('configuracion_sistema')
                    ->where('clave', 'backup_' . $clave)
                    ->countAllResults();
                if ($exists > 0) {
                    $this->db->table('configuracion_sistema')
                        ->where('clave', 'backup_' . $clave)
                        ->update(['valor' => $valor]);
                } else {
                    $this->db->table('configuracion_sistema')
                        ->insert(['clave' => 'backup_' . $clave, 'valor' => $valor]);
                }
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Configuración guardada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error guardando config respaldos: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al guardar la configuración']);
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
            log_message('error', 'Error obteniendo estadísticas respaldos: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al obtener estadísticas']);
        }
    }

    // Método para crear respaldo
    public function crearBackup()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $database = $this->db->database;
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = WRITEPATH . 'backups/' . $filename;
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }
            // -------------------------------------------------------
            // VERIFICACIÓN: shell_exec() puede estar deshabilitado
            // -------------------------------------------------------
            $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
            if (!function_exists('shell_exec') || in_array('shell_exec', $disabledFunctions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error'   => 'La función de respaldo no está disponible en este servidor. Usa phpMyAdmin para exportar la base de datos manualmente.'
                ]);
            }

            $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            if (!file_exists($mysqldump_path)) {
                $mysqldump_path = 'mysqldump';
            }
            $credentialsFile = $this->getDbCredentialsFile();
            $command = '"' . $mysqldump_path . '" --defaults-extra-file=' . escapeshellarg($credentialsFile) . ' ' . escapeshellarg($database);
            $dump = shell_exec($command);
            @unlink($credentialsFile);
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
                    'message' => 'Respaldo creado exitosamente',
                    'filename' => $filename,
                    'download_url' => base_url('index.php/global-admin/descargar-respaldo/' . $this->db->insertID()),
                    'file_size' => $this->formatBytes($tamaño)
                ]);
            } else {
                log_message('error', 'Error al crear backup.');
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al crear el respaldo. Verifique que mysqldump esté disponible y que el usuario tenga permisos.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear backup: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al crear el respaldo'
            ]);
        }
    }

    public function restaurarBackup()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        return $this->response->setJSON(['success' => true, 'mensaje' => 'Backup restaurado exitosamente']);
    }

    /**
     * Enviar respaldo por correo electrónico
     */
    public function enviarRespaldoPorEmail()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $respaldoId = $this->getPostInt('respaldo_id');
        $emailDestino = $this->getPostString('email');
        
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

            $mensaje = "Hola,<br><br>";
            $mensaje .= "Se ha generado un respaldo de la base de datos del Sistema de Bienestar Estudiantil.<br><br>";
            $mensaje .= "<b>Detalles del respaldo:</b><br>";
            $mensaje .= "- Nombre del archivo: " . $respaldo['nombre_archivo'] . "<br>";
            $mensaje .= "- Fecha de creación: " . $respaldo['fecha_creacion'] . "<br>";
            $mensaje .= "- Tamaño: " . $this->formatBytes($respaldo['tamano_bytes']) . "<br>";
            $mensaje .= "- Tipo: " . ucfirst($respaldo['tipo']) . "<br><br>";
            $mensaje .= "El archivo se encuentra adjunto a este correo.<br><br>";
            $mensaje .= "Saludos,<br>Sistema de Bienestar Estudiantil";
            
            // Usar el helper centralizado para asegurar que se usen las credenciales correctas de la DB
            $enviado = \App\Helpers\EmailHelper::enviarCorreo(
                $emailDestino,
                'Respaldo de Base de Datos - ' . $respaldo['nombre_archivo'],
                $mensaje,
                [$filepath]
            );
            
            if ($enviado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respaldo enviado por correo electrónico exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al enviar el correo electrónico (Verifique las credenciales SMTP en Configuración)'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar respaldo por email: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al enviar el respaldo por correo'
            ]);
        }
    }
}
