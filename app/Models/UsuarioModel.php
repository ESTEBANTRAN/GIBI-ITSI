<?php declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rol_id', 'carrera_id', 'nombre', 'apellido', 'cedula', 'email', 
        'password_hash', 'telefono', 'direccion', 'carrera', 'semestre', 
        'foto_perfil', 'estado', 'ultimo_acceso', 'intentos_fallidos', 
        'bloqueado_hasta', 'configuraciones_usuario'
    ];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'rol_id' => 'required|integer|greater_than[0]',
        'nombre' => 'required|min_length[2]|max_length[100]',
        'apellido' => 'required|min_length[2]|max_length[100]',
        'cedula' => 'required|min_length[10]|max_length[10]|is_unique[usuarios.cedula,id,{id}]',
        'email' => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'estado' => 'required|in_list[Activo,Inactivo,Suspendido]'
    ];
    
    protected $validationMessages = [
        'rol_id' => [
            'required' => 'El rol es obligatorio',
            'integer' => 'El rol debe ser un número entero',
            'greater_than' => 'El rol debe ser válido'
        ],
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 2 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ],
        'apellido' => [
            'required' => 'El apellido es obligatorio',
            'min_length' => 'El apellido debe tener al menos 2 caracteres',
            'max_length' => 'El apellido no puede exceder 100 caracteres'
        ],
        'cedula' => [
            'required' => 'La cédula es obligatoria',
            'min_length' => 'La cédula debe tener 10 dígitos',
            'max_length' => 'La cédula debe tener 10 dígitos',
            'is_unique' => 'Ya existe un usuario con esa cédula'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'El email debe tener un formato válido',
            'is_unique' => 'Ya existe un usuario con ese email'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser Activo, Inactivo o Suspendido'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    
    /**
     * Buscar usuario por identificador (cédula o email)
     */
    public function findUserByIdentifier(string $identifier): ?array
    {
        $db = \Config\Database::connect();
        
        $usuario = $db->table('usuarios')
            ->where('cedula', $identifier)
            ->orWhere('email', $identifier)
            ->where('estado', 'Activo')
            ->get()
            ->getRowArray();
        
        return $usuario;
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getUsuariosPorRol(int $rolId): array
    {
        return $this->where('rol_id', $rolId)
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }
    
    /**
     * Obtener usuarios activos
     */
    public function getUsuariosActivos(): array
    {
        return $this->where('estado', 'Activo')
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }
    

        /**
     * Obtener estadísticas de usuarios
     */
    public function getEstadisticasUsuarios(): array
    {
        return [
            'total' => $this->countAll(),
            'activos' => $this->where('estado', 'Activo')->countAllResults(),
            'inactivos' => $this->where('estado', 'Inactivo')->countAllResults(),
            'suspendidos' => $this->where('estado', 'Suspendido')->countAllResults(),
            'recientes' => $this->where('fecha_registro >=', date('Y-m-d', strtotime('-30 days')))->countAllResults()
        ];
    }
    
    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso(int $usuarioId): bool
    {
        return $this->update($usuarioId, [
            'ultimo_acceso' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Incrementar intentos fallidos
     */
    public function incrementarIntentosFallidos(int $usuarioId): bool
    {
        $usuario = $this->find($usuarioId);
        if (!$usuario) {
            return false;
        }
        
        $intentos = (int) $usuario['intentos_fallidos'] + 1;
        
        return $this->update($usuarioId, [
            'intentos_fallidos' => $intentos,
            'bloqueado_hasta' => $intentos >= 5 ? date('Y-m-d H:i:s', strtotime('+30 minutes')) : null
        ]);
    }
    
    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentosFallidos(int $usuarioId): bool
    {
        return $this->update($usuarioId, [
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null
        ]);
    }
    
    /**
     * Verificar si usuario está bloqueado
     */
    public function usuarioBloqueado(int $usuarioId): bool
    {
        $usuario = $this->find($usuarioId);
        if (!$usuario) {
            return false;
        }
        
        if ($usuario['estado'] !== 'Activo') {
            return true;
        }
        
        if (!empty($usuario['bloqueado_hasta'])) {
            $bloqueadoHasta = strtotime($usuario['bloqueado_hasta']);
            if ($bloqueadoHasta > time()) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Buscar usuario por email Y cédula (ambos deben coincidir).
     * Usado para verificación de identidad en recuperación de contraseña.
     */
    public function findByEmailAndCedula(string $email, string $cedula): ?array
    {
        $db = \Config\Database::connect();
        
        $usuario = $db->table('usuarios')
            ->where('email', $email)
            ->where('cedula', $cedula)
            ->where('estado', 'Activo')
            ->get()
            ->getRowArray();
        
        return $usuario;
    }

    /**
     * Actualiza la contraseña de un usuario.
     * La contraseña se hashea con bcrypt antes de almacenarse.
     */
    public function updatePassword(int $usuarioId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        return $this->update($usuarioId, [
            'password_hash'     => $passwordHash,
            'intentos_fallidos' => 0,
            'bloqueado_hasta'   => null,
        ]);
    }
}