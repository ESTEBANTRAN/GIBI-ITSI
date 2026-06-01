<?php

namespace App\Models\GlobalAdmin;

use CodeIgniter\Model;

class UsuarioGlobalModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'rol_id',
        'nombre',
        'apellido',
        'cedula',
        'email',
        'password_hash',
        'telefono',
        'direccion',
        'carrera',
        'semestre',
        'foto_perfil',
        'estado',
        'ultimo_acceso',
        'intentos_login',
        'bloqueado_hasta'
    ];

    /**
     * Obtiene usuarios con información de roles
     */
    public function getUsuariosConRoles()
    {
        return $this->select('usuarios.*, roles.nombre as nombre_rol')
                    ->join('roles', 'roles.id = usuarios.rol_id')
                    ->orderBy('usuarios.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene usuarios por rol
     */
    public function getUsuariosPorRol($rol_id)
    {
        return $this->where('rol_id', $rol_id)->findAll();
    }

    /**
     * Obtiene estadísticas de usuarios
     */
    public function getEstadisticasUsuarios()
    {
        $stats = [];
        
        // Total de usuarios por rol
        $roles = $this->db->table('roles')->get()->getResultArray();
        foreach ($roles as $rol) {
            $stats['por_rol'][$rol['nombre']] = $this->where('rol_id', $rol['id'])->countAllResults();
        }
        
        // Usuarios activos vs bloqueados
        $stats['activos'] = $this->where('estado', 'Activo')->countAllResults();
        $stats['bloqueados'] = $this->where('estado', 'Bloqueado')->countAllResults();
        
        // Usuarios nuevos este mes
        $stats['nuevos_mes'] = $this->where('created_at >=', date('Y-m-01'))->countAllResults();
        
        return $stats;
    }

    /**
     * Obtiene actividad reciente de usuarios
     */
    public function getActividadReciente($limite = 10)
    {
        return $this->select('usuarios.id, usuarios.nombre, usuarios.apellido, usuarios.ultimo_acceso, roles.nombre as rol')
                    ->join('roles', 'roles.id = usuarios.rol_id')
                    ->where('usuarios.ultimo_acceso IS NOT NULL')
                    ->orderBy('usuarios.ultimo_acceso', 'DESC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Actualiza último acceso
     */
    public function actualizarUltimoAcceso($id)
    {
        return $this->update($id, ['ultimo_acceso' => date('Y-m-d H:i:s')]);
    }

    /**
     * Obtiene usuarios con información de roles y paginación
     */
    public function getUsuariosConRolesPaginados($page = 1, $perPage = 30, $search = '')
    {
        $offset = ($page - 1) * $perPage;
        
        // Debug: Log de parámetros
        log_message('info', 'UsuarioGlobalModel::getUsuariosConRolesPaginados - Page: ' . $page . ', PerPage: ' . $perPage . ', Search: "' . $search . '"');
        
        // Construir la consulta base
        $builder = $this->db->table('usuarios');
        $builder->select('usuarios.*, roles.nombre as nombre_rol');
        $builder->join('roles', 'roles.id = usuarios.rol_id');
        
        // Aplicar búsqueda si se proporciona
        if (!empty($search)) {
            log_message('info', 'UsuarioGlobalModel::getUsuariosConRolesPaginados - Aplicando búsqueda: "' . $search . '"');
            $builder->groupStart();
            $builder->like('usuarios.nombre', $search);
            $builder->orLike('usuarios.apellido', $search);
            $builder->orLike('usuarios.email', $search);
            $builder->orLike('usuarios.cedula', $search);
            $builder->orLike('roles.nombre', $search);
            $builder->groupEnd();
        }
        
        // Obtener el total de registros (sin LIMIT)
        $totalQuery = $this->db->table('usuarios');
        $totalQuery->select('COUNT(*) as total');
        $totalQuery->join('roles', 'roles.id = usuarios.rol_id');
        
        if (!empty($search)) {
            $totalQuery->groupStart();
            $totalQuery->like('usuarios.nombre', $search);
            $totalQuery->orLike('usuarios.apellido', $search);
            $totalQuery->orLike('usuarios.email', $search);
            $totalQuery->orLike('usuarios.cedula', $search);
            $totalQuery->orLike('roles.nombre', $search);
            $totalQuery->groupEnd();
        }
        
        $totalResult = $totalQuery->get()->getRow();
        $total = $totalResult->total;
        
        // Debug: Log del total
        log_message('info', 'UsuarioGlobalModel::getUsuariosConRolesPaginados - Total usuarios en búsqueda: ' . $total);
        
        // Obtener los usuarios con LIMIT
        $builder->orderBy('usuarios.created_at', 'DESC');
        $builder->limit($perPage, $offset);
        $usuarios = $builder->get()->getResultArray();
        
        // Debug: Log de usuarios encontrados
        log_message('info', 'UsuarioGlobalModel::getUsuariosConRolesPaginados - Usuarios encontrados en esta página: ' . count($usuarios));
        
        return [
            'usuarios' => $usuarios,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'search' => $search
        ];
    }

    /**
     * Obtiene todos los usuarios con roles para exportar
     */
    public function getTodosLosUsuariosConRoles()
    {
        return $this->select('usuarios.*, roles.nombre as nombre_rol')
                    ->join('roles', 'roles.id = usuarios.rol_id')
                    ->orderBy('usuarios.created_at', 'DESC')
                    ->findAll();
    }
} 