<?php

namespace App\Models\GlobalAdmin;

use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'descripcion', 'permisos', 'estado'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Obtiene todos los roles con información de usuarios asignados
     */
    public function getRolesConUsuarios()
    {
        $builder = $this->db->table('roles');
        $builder->select('roles.*, COUNT(usuarios.id) as usuarios_count');
        $builder->join('usuarios', 'usuarios.rol_id = roles.id', 'left');
        $builder->groupBy('roles.id');
        $builder->orderBy('roles.id', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene un rol específico con información de usuarios
     */
    public function getRolConUsuarios($id)
    {
        $builder = $this->db->table('roles');
        $builder->select('roles.*, COUNT(usuarios.id) as usuarios_count');
        $builder->join('usuarios', 'usuarios.rol_id = roles.id', 'left');
        $builder->where('roles.id', $id);
        $builder->groupBy('roles.id');
        
        return $builder->get()->getRowArray();
    }

    /**
     * Obtiene usuarios asignados a un rol específico
     */
    public function getUsuariosPorRol($rol_id)
    {
        $builder = $this->db->table('usuarios');
        $builder->select('usuarios.*');
        $builder->where('usuarios.rol_id', $rol_id);
        $builder->orderBy('usuarios.nombre', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Verifica si un rol puede ser eliminado (no tiene usuarios asignados)
     */
    public function puedeEliminarRol($id)
    {
        $usuarios = $this->getUsuariosPorRol($id);
        return count($usuarios) === 0;
    }

    /**
     * Obtiene estadísticas de roles
     */
    public function getEstadisticasRoles()
    {
        $builder = $this->db->table('roles');
        $builder->select('roles.*, COUNT(usuarios.id) as usuarios_count');
        $builder->join('usuarios', 'usuarios.rol_id = roles.id', 'left');
        $builder->groupBy('roles.id');
        
        $roles = $builder->get()->getResultArray();
        
        $totalRoles = count($roles);
        $totalUsuarios = array_sum(array_column($roles, 'usuarios_count'));
        $rolesActivos = $totalRoles; // Todos los roles están activos por defecto
        
        return [
            'total_roles' => $totalRoles,
            'total_usuarios' => $totalUsuarios,
            'roles_activos' => $rolesActivos,
            'roles' => $roles
        ];
    }

    /**
     * Verifica si existe un rol con el mismo nombre
     */
    public function existeRolConNombre($nombre, $excluir_id = null)
    {
        $builder = $this->db->table('roles');
        $builder->where('nombre', $nombre);
        
        if ($excluir_id) {
            $builder->where('id !=', $excluir_id);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obtiene roles disponibles para asignar a usuarios
     */
    public function getRolesDisponibles()
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }

    /**
     * Busca roles por nombre (usado en gestión de roles del GlobalAdmin)
     */
    public function buscarRoles($termino)
    {
        $builder = $this->db->table('roles');
        $builder->select('roles.*, COUNT(usuarios.id) as usuarios_count');
        $builder->join('usuarios', 'usuarios.rol_id = roles.id', 'left');
        $builder->like('roles.nombre', $termino);
        $builder->groupBy('roles.id');
        $builder->orderBy('roles.id', 'ASC');

        return $builder->get()->getResultArray();
    }
} 