<?php

namespace App\Models\GlobalAdmin;

use CodeIgniter\Model;

class BackupModel extends Model
{
    protected $table = 'backups';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'nombre',
        'archivo',
        'tamaño',
        'tipo',
        'descripcion',
        'estado',
        'creado_por'
    ];

    /**
     * Crea un respaldo de la base de datos
     */
    public function crearBackup()
    {
        try {
            $fecha = date('Y-m-d_H-i-s');
            $nombreArchivo = "backup_{$fecha}.sql";
            $rutaBackup = WRITEPATH . 'backups/' . $nombreArchivo;
            
            // Crear directorio si no existe
            if (!is_dir(dirname($rutaBackup))) {
                mkdir(dirname($rutaBackup), 0755, true);
            }
            
            // Obtener configuración de base de datos
            $dbConfig = config('Database');
            $host = $dbConfig->default['hostname'];
            $usuario = $dbConfig->default['username'];
            $password = $dbConfig->default['password'];
            $database = $dbConfig->default['database'];
            
            // Comando mysqldump
            $comando = "mysqldump --host={$host} --user={$usuario}";
            
            if (!empty($password)) {
                $comando .= " --password={$password}";
            }
            
            $comando .= " --single-transaction --routines --triggers {$database} > {$rutaBackup}";
            
            // Ejecutar comando
            exec($comando, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Error al crear el respaldo de la base de datos');
            }
            
            // Verificar que el archivo se creó
            if (!file_exists($rutaBackup)) {
                throw new \Exception('No se pudo crear el archivo de respaldo');
            }
            
            $tamaño = filesize($rutaBackup);
            
            // Guardar registro en la base de datos
            $data = [
                'nombre' => "Respaldo {$fecha}",
                'archivo' => $nombreArchivo,
                'tamaño' => $tamaño,
                'tipo' => 'completo',
                'descripcion' => 'Respaldo completo de la base de datos',
                'estado' => 'completado',
                'creado_por' => session('id')
            ];
            
            $this->insert($data);
            
            return [
                'success' => true,
                'archivo' => $nombreArchivo,
                'tamaño' => $this->formatBytes($tamaño),
                'mensaje' => 'Respaldo creado exitosamente'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error al crear respaldo: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Restaura un respaldo
     */
    public function restaurarBackup($archivo)
    {
        try {
            $rutaBackup = WRITEPATH . 'backups/' . $archivo;
            
            if (!file_exists($rutaBackup)) {
                throw new \Exception('Archivo de respaldo no encontrado');
            }
            
            // Obtener configuración de base de datos
            $dbConfig = config('Database');
            $host = $dbConfig->default['hostname'];
            $usuario = $dbConfig->default['username'];
            $password = $dbConfig->default['password'];
            $database = $dbConfig->default['database'];
            
            // Comando mysql para restaurar
            $comando = "mysql --host={$host} --user={$usuario}";
            
            if (!empty($password)) {
                $comando .= " --password={$password}";
            }
            
            $comando .= " {$database} < {$rutaBackup}";
            
            // Ejecutar comando
            exec($comando, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Error al restaurar el respaldo');
            }
            
            // Actualizar estado del backup
            $backup = $this->where('archivo', $archivo)->first();
            if ($backup) {
                $this->update($backup['id'], [
                    'estado' => 'restaurado',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return [
                'success' => true,
                'mensaje' => 'Respaldo restaurado exitosamente'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error al restaurar respaldo: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene todos los respaldos
     */
    public function getBackups()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Obtiene respaldos recientes
     */
    public function getBackupsRecientes($limite = 5)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Elimina un respaldo
     */
    public function eliminarBackup($id)
    {
        $backup = $this->find($id);
        
        if (!$backup) {
            return false;
        }
        
        $rutaArchivo = WRITEPATH . 'backups/' . $backup['archivo'];
        
        // Eliminar archivo físico
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        
        // Eliminar registro de la base de datos
        return $this->delete($id);
    }

    /**
     * Obtiene estadísticas de respaldos
     */
    public function getEstadisticasBackups()
    {
        $stats = [];
        
        // Total de respaldos
        $stats['total'] = $this->countAllResults();
        
        // Respaldos por estado
        $stats['completados'] = $this->where('estado', 'completado')->countAllResults();
        $stats['fallidos'] = $this->where('estado', 'fallido')->countAllResults();
        $stats['restaurados'] = $this->where('estado', 'restaurado')->countAllResults();
        
        // Tamaño total de respaldos
        $backups = $this->select('SUM(tamaño) as total_tamaño')->first();
        $stats['tamaño_total'] = $this->formatBytes($backups['total_tamaño'] ?? 0);
        
        // Respaldos del último mes
        $stats['ultimo_mes'] = $this->where('created_at >=', date('Y-m-01'))
                                    ->countAllResults();
        
        return $stats;
    }

    /**
     * Formatea bytes a formato legible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

} 