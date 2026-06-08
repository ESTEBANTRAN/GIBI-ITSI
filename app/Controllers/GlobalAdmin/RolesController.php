<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Models\GlobalAdmin\RolModel;
use App\Security\InputSanitizerTrait;

class RolesController extends BaseController
{
    use InputSanitizerTrait;

    protected $rolModel;
    protected $db;

    public function __construct()
    {
        $this->rolModel = new RolModel();
        $this->db = \Config\Database::connect();
    }

    public function gestionRoles()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        $search = $this->request->getGet('search') ?? '';
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 15;
        
        if (!empty($search)) {
            $roles = $this->rolModel->buscarRoles($search);
        } else {
            $roles = $this->rolModel->getRolesConUsuarios();
        }

        $total = count($roles);
        $totalPages = max(1, (int)ceil($total / $perPage));

        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;
        $rolesPaginados = array_slice($roles, $offset, $perPage);

        $data = [
            'roles' => $rolesPaginados,
            'search' => $search,
            'estadisticas' => $this->rolModel->getEstadisticasRoles(),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total' => $total
        ];

        return view('GlobalAdmin/gestion_roles', $data);
    }

    public function crearRol()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $nombre = $this->getPostString('nombre');
        $descripcion = $this->getPostString('descripcion', '');
        $codigo = $this->getPostString('codigo', '');
        $activo = $this->getPostBool('activo') ? 1 : 0;

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
            log_message('error', 'Error creando rol: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al crear el rol']);
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

        $id = $this->getPostInt('id');
        $nombre = $this->getPostString('nombre');
        $descripcion = $this->getPostString('descripcion', '');
        $codigo = $this->getPostString('codigo', '');
        $activo = $this->getPostBool('activo') ? 1 : 0;

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
            log_message('error', 'Error actualizando rol: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar el rol']);
        }
    }

    public function eliminarRol()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->getPostInt('id');

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
            log_message('error', 'Error eliminando rol: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar el rol']);
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

        $permisos = $this->getPermisosFromDatabase($id);

        return $this->response->setJSON([
            'success' => true, 
            'rol' => $rol,
            'permisos' => $permisos
        ]);
    }

    private function getPermisosFromDatabase($rol_id)
    {
        $permisosBase = [
            'dashboard' => false,
            'usuarios' => false,
            'roles' => false,
            'configuracion' => false,
            'fichas' => false,
            'becas' => false,
            'solicitudes' => false,
            'reportes' => false
        ];

        $rol = $this->rolModel->find($rol_id);
        if ($rol && !empty($rol['permisos'])) {
            $permisosJson = json_decode($rol['permisos'], true);
            if (is_array($permisosJson)) {
                return array_merge($permisosBase, $permisosJson);
            }
        }

        return $permisosBase;
    }
}
