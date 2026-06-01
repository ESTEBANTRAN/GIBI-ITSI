<?php

namespace App\Models\GlobalAdmin;

use CodeIgniter\Model;

class SistemaModel extends Model
{
    protected $table = 'configuracion_sistema';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
        'categoria'
    ];

    /**
     * Obtiene configuración del sistema
     */
    public function getConfiguracion()
    {
        $config = $this->findAll();
        $configuracion = [];
        
        foreach ($config as $item) {
            $configuracion[$item['clave']] = [
                'valor' => $item['valor'],
                'descripcion' => $item['descripcion'],
                'tipo' => $item['tipo'],
                'categoria' => $item['categoria']
            ];
        }
        
        return $configuracion;
    }

    /**
     * Obtiene valor de configuración
     */
    public function getValor($clave, $default = null)
    {
        $config = $this->where('clave', $clave)->first();
        return $config ? $config['valor'] : $default;
    }

    /**
     * Establece valor de configuración
     */
    public function setValor($clave, $valor, $descripcion = '', $tipo = 'text', $categoria = 'general')
    {
        $config = $this->where('clave', $clave)->first();
        
        if ($config) {
            return $this->update($config['id'], [
                'valor' => $valor,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'categoria' => $categoria
            ]);
        } else {
            return $this->insert([
                'clave' => $clave,
                'valor' => $valor,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'categoria' => $categoria
            ]);
        }
    }

    /**
     * Obtiene información del sistema
     */
    public function getSistemaInfo()
    {
        return [
            'version' => $this->getValor('version_sistema', '1.0.0'),
            'nombre' => $this->getValor('nombre_sistema', 'Sistema de Bienestar Estudiantil'),
            'email_admin' => $this->getValor('email_admin', 'admin@instituto.edu.ec'),
            'telefono_admin' => $this->getValor('telefono_admin', ''),
            'direccion_admin' => $this->getValor('direccion_admin', ''),
            'fecha_instalacion' => $this->getValor('fecha_instalacion', date('Y-m-d')),
            'ultima_actualizacion' => $this->getValor('ultima_actualizacion', date('Y-m-d H:i:s')),
            'php_version' => PHP_VERSION,
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'database_version' => $this->getDatabaseVersion()
        ];
    }

    /**
     * Obtiene versión de la base de datos
     */
    private function getDatabaseVersion()
    {
        try {
            $result = $this->db->query('SELECT VERSION() as version')->getRow();
            return $result ? $result->version : 'Desconocida';
        } catch (\Exception $e) {
            return 'Error al obtener versión';
        }
    }

    /**
     * Obtiene logs del sistema
     */
    public function getLogs($limite = 100)
    {
        $logFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $logs = [];
        $lines = file($logFile);
        
        // Obtener las últimas líneas
        $lines = array_slice($lines, -$limite);
        
        foreach ($lines as $line) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+) - (.+)/', $line, $matches)) {
                $logs[] = [
                    'fecha' => $matches[1],
                    'nivel' => $matches[2],
                    'mensaje' => trim($matches[3])
                ];
            }
        }
        
        return array_reverse($logs);
    }

    /**
     * Obtiene estadísticas globales del sistema
     */
    public function getEstadisticasGlobales()
    {
        $stats = [];
        
        // Estadísticas de usuarios
        $stats['usuarios'] = [
            'total' => $this->db->table('usuarios')->countAllResults(),
            'activos' => $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults(),
            'bloqueados' => $this->db->table('usuarios')->where('estado', 'Bloqueado')->countAllResults(),
            'nuevos_mes' => $this->db->table('usuarios')
                ->where('created_at >=', date('Y-m-01'))
                ->countAllResults()
        ];
        
        // Estadísticas de roles
        $stats['roles'] = [
            'total' => $this->db->table('roles')->countAllResults(),
            'activos' => $this->db->table('roles')->where('estado', 'Activo')->countAllResults()
        ];
        
        // Estadísticas de bienestar (si existen las tablas)
        if ($this->db->tableExists('fichas_socioeconomicas')) {
            $stats['formularios'] = [
                'total' => $this->db->table('fichas_socioeconomicas')->countAllResults(),
                'pendientes' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Pendiente')->countAllResults()
            ];
        }
        
        if ($this->db->tableExists('becas')) {
            $stats['becas'] = [
                'total' => $this->db->table('becas')->countAllResults(),
                'activas' => $this->db->table('becas')->where('estado', 'Activo')->countAllResults()
            ];
        }
        
        if ($this->db->tableExists('solicitudes_ayuda')) {
            $stats['solicitudes'] = [
                'total' => $this->db->table('solicitudes_ayuda')->countAllResults(),
                'pendientes' => $this->db->table('solicitudes_ayuda')->where('estado', 'Pendiente')->countAllResults()
            ];
        }
        
        // Información del sistema
        $stats['sistema'] = [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_free_space' => $this->formatBytes(disk_free_space(ROOTPATH)),
            'disk_total_space' => $this->formatBytes(disk_total_space(ROOTPATH))
        ];
        
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