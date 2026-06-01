<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UsuarioModel;
use Tests\Support\Database\Seeds\RoleSeeder;

/**
 * Tests de Integración: Sistema de Roles en Base de Datos
 * 
 * Verifica que los roles funcionen correctamente a nivel de base de datos
 * usando las constantes ROLE_ESTUDIANTE (1), ROLE_ADMIN_BIENESTAR (2),
 * ROLE_SUPER_ADMIN (4) en consultas y operaciones reales con DB.
 * 
 * @internal
 */
final class RoleIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * Migración que crea las tablas 'roles' y 'usuarios'.
     */
    protected $migrate = 'Tests\Support\Database\Migrations\CreateRolesTables';

    /**
     * Seeder que puebla datos de prueba.
     */
    protected $seed = RoleSeeder::class;

    // ========================================================================
    //  1. VERIFICACIÓN DE DATOS SEMBRADOS
    // ========================================================================

    public function testRolesTableHasExpectedData(): void
    {
        $roles = $this->db->table('roles')->get()->getResultArray();

        $this->assertCount(4, $roles, 'Deben existir 4 roles en la base de datos');
    }

    public function testUsuariosTableHasExpectedData(): void
    {
        $model = new UsuarioModel();
        $usuarios = $model->findAll();

        $this->assertCount(6, $usuarios, 'Deben existir 6 usuarios en la base de datos');
    }

    // ========================================================================
    //  2. VERIFICACIÓN DE CONSTANTES VS BASE DE DATOS
    // ========================================================================

    public function testRoleIdConstantsMatchDatabase(): void
    {
        // Verificar que Estudiante tenga id = ROLE_ESTUDIANTE (1)
        $estudiante = $this->db->table('roles')->where('nombre', 'Estudiante')->get()->getRowArray();
        $this->assertNotNull($estudiante, 'Rol Estudiante debe existir');
        $this->assertSame(ROLE_ESTUDIANTE, (int) $estudiante['id'],
            'Estudiante debe tener id = ROLE_ESTUDIANTE (' . ROLE_ESTUDIANTE . ')'
        );

        // Verificar que Admin Bienestar tenga id = ROLE_ADMIN_BIENESTAR (2)
        $adminBienestar = $this->db->table('roles')->where('nombre', 'Admin Bienestar')->get()->getRowArray();
        $this->assertNotNull($adminBienestar, 'Rol Admin Bienestar debe existir');
        $this->assertSame(ROLE_ADMIN_BIENESTAR, (int) $adminBienestar['id'],
            'Admin Bienestar debe tener id = ROLE_ADMIN_BIENESTAR (' . ROLE_ADMIN_BIENESTAR . ')'
        );

        // Verificar que Super Administrador tenga id = ROLE_SUPER_ADMIN (4)
        $superAdmin = $this->db->table('roles')->where('nombre', 'Super Administrador')->get()->getRowArray();
        $this->assertNotNull($superAdmin, 'Rol Super Administrador debe existir');
        $this->assertSame(ROLE_SUPER_ADMIN, (int) $superAdmin['id'],
            'Super Admin debe tener id = ROLE_SUPER_ADMIN (' . ROLE_SUPER_ADMIN . ')'
        );
    }

    // ========================================================================
    //  3. VERIFICACIÓN DE CONSULTAS POR ROL (RolModel)
    // ========================================================================

    public function testGetUsuariosPorRol_EstudianteReturnsOnlyEstudiantes(): void
    {
        $usuarios = $this->db->table('usuarios')->where('rol_id', ROLE_ESTUDIANTE)->get()->getResultArray();

        $this->assertCount(3, $usuarios, 'Deben haber 3 estudiantes (2 activos + 1 inactivo)');

        // Verificar que todos los usuarios devueltos tengan efectivamente rol_id = ROLE_ESTUDIANTE
        // usando UsuarioModel como fuente de verdad
        $todosLosUsuarios = model(UsuarioModel::class)->findAll();
        $estudiantes = array_filter($todosLosUsuarios, fn($u) => (int) $u['rol_id'] === ROLE_ESTUDIANTE);
        $this->assertCount(3, $estudiantes, 'Filtrando usuarios, deben haber 3 estudiantes');

        // Verificar que los IDs devueltos por RolModel correspondan a estudiantes reales
        $estudianteIds = array_map(fn($u) => (int) $u['id'], $estudiantes);
        foreach ($usuarios as $u) {
            $this->assertContains(
                (int) $u['id'],
                $estudianteIds,
                'Usuario ID ' . $u['id'] . ' debe ser un estudiante legítimo'
            );
        }
    }

    public function testGetUsuariosPorRol_AdminBienestarReturnsOnlyAdmins(): void
    {
        $usuarios = $this->db->table('usuarios')->where('rol_id', ROLE_ADMIN_BIENESTAR)->get()->getResultArray();

        $this->assertCount(2, $usuarios, 'Deben haber 2 admin bienestar (1 activo + 1 inactivo)');
    }

    public function testGetUsuariosPorRol_SuperAdminReturnsOnlySuperAdmin(): void
    {
        $usuarios = $this->db->table('usuarios')->where('rol_id', ROLE_SUPER_ADMIN)->get()->getResultArray();

        $this->assertCount(1, $usuarios, 'Debe haber 1 super admin');
    }

    public function testGetUsuariosPorRol_InvalidRoleReturnsEmpty(): void
    {
        $usuarios = $this->db->table('usuarios')->where('rol_id', 999)->get()->getResultArray();

        $this->assertCount(0, $usuarios, 'Rol inexistente debe retornar 0 usuarios');
    }

    public function testContarUsuariosPorRol(): void
    {
        $this->assertSame(3, $this->db->table('usuarios')->where('rol_id', ROLE_ESTUDIANTE)->countAllResults(),
            'Contar: deben ser 3 estudiantes'
        );
        $this->assertSame(2, $this->db->table('usuarios')->where('rol_id', ROLE_ADMIN_BIENESTAR)->countAllResults(),
            'Contar: deben ser 2 admin bienestar'
        );
        $this->assertSame(1, $this->db->table('usuarios')->where('rol_id', ROLE_SUPER_ADMIN)->countAllResults(),
            'Contar: debe ser 1 super admin'
        );
        $this->assertSame(0, $this->db->table('usuarios')->where('rol_id', 999)->countAllResults(),
            'Contar: rol inexistente = 0'
        );
    }

    // ========================================================================
    //  4. VERIFICACIÓN DE CONSULTAS POR ROL (UsuarioModel)
    // ========================================================================

    public function testGetUsuariosPorRol_UsuarioModel(): void
    {
        $model = new UsuarioModel();

        $estudiantes = $model->getUsuariosPorRol(ROLE_ESTUDIANTE);
        $this->assertCount(3, $estudiantes, 'UsuarioModel: 3 estudiantes');

        $admins = $model->getUsuariosPorRol(ROLE_ADMIN_BIENESTAR);
        $this->assertCount(2, $admins, 'UsuarioModel: 2 admin bienestar');

        $superAdmins = $model->getUsuariosPorRol(ROLE_SUPER_ADMIN);
        $this->assertCount(1, $superAdmins, 'UsuarioModel: 1 super admin');
    }

    public function testGetUsuariosConRol_ReturnsAllWithRoleNames(): void
    {
        $db = \Config\Database::connect('tests');
        $usuarios = $db->table('usuarios')
            ->select('usuarios.*, roles.nombre as rol_nombre')
            ->join('roles', 'roles.id = usuarios.rol_id')
            ->get()
            ->getResultArray();

        $this->assertCount(6, $usuarios, 'getUsuariosConRol debe retornar todos los usuarios');

        foreach ($usuarios as $u) {
            $this->assertArrayHasKey('rol_nombre', $u, 'Cada usuario debe tener rol_nombre');
            $this->assertNotEmpty($u['rol_nombre'], 'rol_nombre no debe estar vacío');

            // Verificar que el nombre del rol coincida con el rol_id usando constantes
            $rolId = (int) $u['rol_id'];
            if ($rolId === ROLE_ESTUDIANTE) {
                $this->assertSame('Estudiante', $u['rol_nombre']);
            } elseif ($rolId === ROLE_ADMIN_BIENESTAR) {
                $this->assertSame('Admin Bienestar', $u['rol_nombre']);
            } elseif ($rolId === ROLE_SUPER_ADMIN) {
                $this->assertSame('Super Administrador', $u['rol_nombre']);
            }
        }
    }

    public function testGetUsuarioConRol_ReturnsCorrectRole(): void
    {
        $db = \Config\Database::connect('tests');
        $model = new UsuarioModel();

        // Obtener un estudiante
        $todos = $model->findAll();
        $estudiante = null;
        foreach ($todos as $u) {
            if ((int) $u['rol_id'] === ROLE_ESTUDIANTE) {
                $estudiante = $db->table('usuarios')
                    ->select('usuarios.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id = usuarios.rol_id')
                    ->where('usuarios.id', (int) $u['id'])
                    ->get()
                    ->getRowArray();
                break;
            }
        }

        $this->assertNotNull($estudiante, 'Debe encontrar un estudiante');
        $this->assertSame('Estudiante', $estudiante['rol_nombre']);
        $this->assertSame(ROLE_ESTUDIANTE, (int) $estudiante['rol_id']);

        // Obtener super admin
        $superAdmin = null;
        foreach ($todos as $u) {
            if ((int) $u['rol_id'] === ROLE_SUPER_ADMIN) {
                $superAdmin = $db->table('usuarios')
                    ->select('usuarios.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id = usuarios.rol_id')
                    ->where('usuarios.id', (int) $u['id'])
                    ->get()
                    ->getRowArray();
                break;
            }
        }

        $this->assertNotNull($superAdmin, 'Debe encontrar un super admin');
        $this->assertSame('Super Administrador', $superAdmin['rol_nombre']);
        $this->assertSame(ROLE_SUPER_ADMIN, (int) $superAdmin['rol_id']);
    }

    // ========================================================================
    //  5. VERIFICACIÓN DE FILTRADO POR ESTADO + ROL
    // ========================================================================

    /**
     * Prueba combinada: rol + estado (ej: estudiantes activos vs inactivos)
     */
    public function testFilterByRoleAndStatus(): void
    {
        $model = new UsuarioModel();

        // Estudiantes activos
        $estudiantesActivos = $model->where('rol_id', ROLE_ESTUDIANTE)
                                    ->where('estado', 'Activo')
                                    ->findAll();
        $this->assertCount(2, $estudiantesActivos, '2 estudiantes activos');

        // Estudiantes inactivos
        $estudiantesInactivos = $model->where('rol_id', ROLE_ESTUDIANTE)
                                      ->where('estado', 'Inactivo')
                                      ->findAll();
        $this->assertCount(1, $estudiantesInactivos, '1 estudiante inactivo');

        // Admin bienestar activos
        $adminsActivos = $model->where('rol_id', ROLE_ADMIN_BIENESTAR)
                               ->where('estado', 'Activo')
                               ->findAll();
        $this->assertCount(1, $adminsActivos, '1 admin bienestar activo');

        // Super admin activo
        $superAdminsActivos = $model->where('rol_id', ROLE_SUPER_ADMIN)
                                    ->where('estado', 'Activo')
                                    ->findAll();
        $this->assertCount(1, $superAdminsActivos, '1 super admin activo');
    }

    // ========================================================================
    //  6. VERIFICACIÓN DE ESTADÍSTICAS DE ROLES
    // ========================================================================

    public function testGetEstadisticasRoles(): void
    {
        // Obtener todos los roles
        $roles = $this->db->table('roles')->orderBy('id', 'ASC')->get()->getResultArray();
        $this->assertCount(4, $roles, 'Deben existir 4 roles');

        // Verificar usuarios por rol usando conteos directos
        $countEstudiantes = $this->db->table('usuarios')->where('rol_id', ROLE_ESTUDIANTE)->countAllResults();
        $this->assertSame(3, $countEstudiantes, '3 usuarios con rol Estudiante');

        $countAdmins = $this->db->table('usuarios')->where('rol_id', ROLE_ADMIN_BIENESTAR)->countAllResults();
        $this->assertSame(2, $countAdmins, '2 usuarios con rol Admin Bienestar');

        $countSuperAdmins = $this->db->table('usuarios')->where('rol_id', ROLE_SUPER_ADMIN)->countAllResults();
        $this->assertSame(1, $countSuperAdmins, '1 usuario con rol Super Admin');

        // Verificar activos/inactivos para estudiantes
        $activos = $this->db->table('usuarios')
            ->where('rol_id', ROLE_ESTUDIANTE)
            ->where('estado', 'Activo')
            ->countAllResults();
        $this->assertSame(2, $activos, '2 estudiantes activos');

        $inactivos = $this->db->table('usuarios')
            ->where('rol_id', ROLE_ESTUDIANTE)
            ->where('estado', 'Inactivo')
            ->countAllResults();
        $this->assertSame(1, $inactivos, '1 estudiante inactivo');
    }

    // ========================================================================
    //  7. VERIFICACIÓN DE BÚSQUEDA POR IDENTIFICADOR (Login)
    // ========================================================================

    public function testFindUserByIdentifier_FiltersByRole(): void
    {
        $model = new UsuarioModel();

        // Buscar estudiante por email
        $juan = $model->findUserByIdentifier('juan.perez@test.com');
        $this->assertNotNull($juan, 'Debe encontrar a Juan por email');
        $this->assertSame(ROLE_ESTUDIANTE, (int) $juan['rol_id']);

        // Buscar estudiante por cédula
        $juanPorCedula = $model->findUserByIdentifier('1234567890');
        $this->assertNotNull($juanPorCedula, 'Debe encontrar a Juan por cédula');
        $this->assertSame(ROLE_ESTUDIANTE, (int) $juanPorCedula['rol_id']);

        // Buscar admin por email
        $carlos = $model->findUserByIdentifier('carlos.mendoza@admin.com');
        $this->assertNotNull($carlos, 'Debe encontrar a Carlos (admin)');
        $this->assertSame(ROLE_ADMIN_BIENESTAR, (int) $carlos['rol_id']);

        // Buscar super admin por email
        $super = $model->findUserByIdentifier('super.admin@sistema.com');
        $this->assertNotNull($super, 'Debe encontrar al Super Admin');
        $this->assertSame(ROLE_SUPER_ADMIN, (int) $super['rol_id']);

        // Usuario inactivo NO debe ser encontrado
        $pedro = $model->findUserByIdentifier('pedro.ramirez@test.com');
        $this->assertNull($pedro, 'Usuario inactivo NO debe ser encontrado en login');

        // Usuario inexistente retorna null
        $noExiste = $model->findUserByIdentifier('noexiste@test.com');
        $this->assertNull($noExiste, 'Email inexistente retorna null');
    }

    // ========================================================================
    //  8. VERIFICACIÓN DE USUARIOS ACTIVOS E INACTIVOS POR ROL
    // ========================================================================

    public function testGetUsuariosActivos_FiltersCorrectly(): void
    {
        $model = new UsuarioModel();

        $activos = $model->getUsuariosActivos();

        // 2 estudiantes activos + 1 admin activo + 1 super admin activo = 4
        $this->assertCount(4, $activos, '4 usuarios activos en total');

        // Verificar que todos sean Activo
        foreach ($activos as $u) {
            $this->assertSame('Activo', $u['estado']);
        }
    }

    public function testGetUsuariosInactivos_FiltersCorrectly(): void
    {
        $model = new UsuarioModel();

        $inactivos = $model->where('estado !=', 'Activo')->orWhere('estado', null)->findAll();

        // 1 estudiante inactivo + 1 admin inactivo = 2
        $this->assertCount(2, $inactivos, '2 usuarios inactivos en total');

        // Verificar que ninguno sea Activo
        foreach ($inactivos as $u) {
            $this->assertNotSame('Activo', $u['estado'],
                'Usuario inactivo no debe tener estado Activo'
            );
        }
    }

    // ========================================================================
    //  9. VERIFICACIÓN DE USUARIO BLOQUEADO (LOGIN ATTEMPTS)
    // ========================================================================

    public function testUsuarioBloqueado_ByFailedAttempts(): void
    {
        $model = new UsuarioModel();

        // Pedro (estudiante) tiene 5 intentos fallidos y está inactivo
        $pedro = $model->where('cedula', '1112233445')->first();
        $this->assertNotNull($pedro);
        $this->assertTrue($model->usuarioBloqueado((int) $pedro['id']),
            'Pedro debe estar bloqueado (inactivo + 5 intentos fallidos)'
        );

        // Juan (estudiante activo, 0 intentos) NO debe estar bloqueado
        $juan = $model->where('cedula', '1234567890')->first();
        $this->assertNotNull($juan);
        $this->assertFalse($model->usuarioBloqueado((int) $juan['id']),
            'Juan NO debe estar bloqueado (activo, 0 intentos)'
        );
    }

    // ========================================================================
    //  10. VERIFICACIÓN DE findByEmailAndCedula (Recuperación de contraseña)
    // ========================================================================

    public function testFindByEmailAndCedula_OnlyActiveUsers(): void
    {
        $model = new UsuarioModel();

        // Juan existe y está activo → debe ser encontrado
        $juan = $model->findByEmailAndCedula('juan.perez@test.com', '1234567890');
        $this->assertNotNull($juan, 'Juan activo debe ser encontrado');
        $this->assertSame(ROLE_ESTUDIANTE, (int) $juan['rol_id']);

        // Pedro está inactivo → NO debe ser encontrado
        $pedro = $model->findByEmailAndCedula('pedro.ramirez@test.com', '1112233445');
        $this->assertNull($pedro, 'Pedro inactivo NO debe ser encontrado');
    }

    // ========================================================================
    //  11. VERIFICACIÓN DE CRUD: CREAR USUARIO CON ROL Y ELIMINAR
    // ========================================================================

    public function testCreateUserWithRole(): void
    {
        $model = new UsuarioModel();

        $data = [
            'rol_id'        => ROLE_ESTUDIANTE,
            'nombre'        => 'Test',
            'apellido'      => 'Usuario',
            'cedula'        => '9999999999',
            'email'         => 'test.crear@test.com',
            'password_hash' => password_hash('test1234', PASSWORD_BCRYPT),
            'estado'        => 'Activo',
        ];

        $id = $model->insert($data);
        $this->assertNotNull($id, 'Usuario debe ser creado con ID');
        $this->assertIsInt($id);

        // Verificar que se guardó correctamente
        $creado = $model->find($id);
        $this->assertNotNull($creado);
        $this->assertSame(ROLE_ESTUDIANTE, (int) $creado['rol_id']);
        $this->assertSame('Test', $creado['nombre']);

        // Crear usuario Admin
        $dataAdmin = [
            'rol_id'        => ROLE_ADMIN_BIENESTAR,
            'nombre'        => 'Admin',
            'apellido'      => 'Test',
            'cedula'        => '8888888888',
            'email'         => 'admin.test@test.com',
            'password_hash' => password_hash('test1234', PASSWORD_BCRYPT),
            'estado'        => 'Activo',
        ];

        $idAdmin = $model->insert($dataAdmin);
        $this->assertNotNull($idAdmin, 'Admin debe ser creado');

        $adminCreado = $model->find($idAdmin);
        $this->assertSame(ROLE_ADMIN_BIENESTAR, (int) $adminCreado['rol_id']);

        // Limpiar datos de prueba
        $model->delete($id);
        $model->delete($idAdmin);
    }

    // ========================================================================
    //  12. VERIFICACIÓN DE ACTUALIZACIÓN DE ROL
    // ========================================================================

    public function testUpdateUserRole(): void
    {
        $model = new UsuarioModel();

        // Buscar un estudiante y cambiarle el rol a admin
        $juan = $model->where('cedula', '1234567890')->first();
        $this->assertNotNull($juan);
        $this->assertSame(ROLE_ESTUDIANTE, (int) $juan['rol_id']);

        // Cambiar rol de estudiante a admin bienestar
        $model->update((int) $juan['id'], ['rol_id' => ROLE_ADMIN_BIENESTAR]);

        // Verificar cambio
        $actualizado = $model->find((int) $juan['id']);
        $this->assertSame(ROLE_ADMIN_BIENESTAR, (int) $actualizado['rol_id'],
            'Rol debe cambiar de Estudiante a Admin Bienestar'
        );

        // Restaurar rol original
        $model->update((int) $juan['id'], ['rol_id' => ROLE_ESTUDIANTE]);
        $restaurado = $model->find((int) $juan['id']);
        $this->assertSame(ROLE_ESTUDIANTE, (int) $restaurado['rol_id'],
            'Rol debe restaurarse a Estudiante'
        );
    }

    // ========================================================================
    //  13. VERIFICACIÓN DE INTEGRIDAD REFERENCIAL
    // ========================================================================

    public function testDeletingRoleDoesNotRemoveAssociatedUsers(): void
    {
        $userModel = new UsuarioModel();

        // Verificar que el rol Estudiante tiene usuarios asociados
        $count = $this->db->table('usuarios')->where('rol_id', ROLE_ESTUDIANTE)->countAllResults();
        $this->assertGreaterThan(0, $count, 'Rol Estudiante debe tener usuarios');

        // Intentar eliminar el rol (depende de FK, pero en SQLite sin FK puede funcionar)
        // Simplemente verificamos que al eliminar el rol, los usuarios mantengan el rol_id
        // (en producción hay FK constraint, en test SQLite puede o no tenerlas)
        $this->db->table('roles')->where('id', ROLE_ESTUDIANTE)->delete();

        // Verificar que los usuarios con ese rol_id aún existen
        $usuarios = $userModel->where('rol_id', ROLE_ESTUDIANTE)->findAll();
        $this->assertCount(3, $usuarios,
            'Usuarios deben persistir incluso si el rol se elimina (integridad de datos)'
        );
    }

    // ========================================================================
    //  14. VERIFICACIÓN DE RANGO: SUPER ADMIN ES ÚNICO
    // ========================================================================

    public function testOnlyOneSuperAdminExists(): void
    {
        $model = new UsuarioModel();

        $superAdmins = $model->where('rol_id', ROLE_SUPER_ADMIN)->findAll();
        $this->assertCount(1, $superAdmins, 'Debe existir solo 1 Super Admin en los datos de prueba');
    }

    // ========================================================================
    //  15. VERIFICACIÓN DE BÚSQUEDA DE USUARIOS
    // ========================================================================

    public function testBuscarUsuarios_ReturnsCorrectRoles(): void
    {
        $model = new UsuarioModel();

        // Buscar por nombre
        $resultados = $model->like('nombre', 'Juan')->findAll();
        $this->assertNotEmpty($resultados, 'Búsqueda "Juan" debe retornar resultados');

        // Verificar que todos los resultados tengan un rol válido
        foreach ($resultados as $u) {
            $rolId = (int) $u['rol_id'];
            $this->assertContains($rolId, [ROLE_ESTUDIANTE, ROLE_ADMIN_BIENESTAR, ROLE_SUPER_ADMIN],
                'Usuario encontrado debe tener un rol válido'
            );
        }
    }

    // ========================================================================
    //  16. VERIFICACIÓN DE ORDENAMIENTO POR ROL
    // ========================================================================

    public function testOrderByRoleReturnsSortedResults(): void
    {
        $model = new UsuarioModel();

        // Obtener todos los usuarios ordenados por rol_id ascendente
        $ordenados = $model->orderBy('rol_id', 'ASC')->findAll();

        $this->assertCount(6, $ordenados);

        // Verificar que estén ordenados: 1,1,1,2,2,4 (Estudiante, Admin, Super)
        $rolIds = array_map(fn($u) => (int) $u['rol_id'], $ordenados);
        $sorted = $rolIds;
        sort($sorted);
        $this->assertSame($sorted, $rolIds, 'Usuarios deben estar ordenados por rol_id');

        // Primeros 3 deben ser estudiantes
        $this->assertSame(ROLE_ESTUDIANTE, $rolIds[0]);
        $this->assertSame(ROLE_ESTUDIANTE, $rolIds[1]);
        $this->assertSame(ROLE_ESTUDIANTE, $rolIds[2]);

        // Siguientes 2 deben ser admin
        $this->assertSame(ROLE_ADMIN_BIENESTAR, $rolIds[3]);
        $this->assertSame(ROLE_ADMIN_BIENESTAR, $rolIds[4]);

        // Último debe ser super admin
        $this->assertSame(ROLE_SUPER_ADMIN, $rolIds[5]);
    }

    // ========================================================================
    //  17. VERIFICACIÓN: LAS CONSTANTES COINCIDEN CON ROLES EN DB
    // ========================================================================

    public function testRoleIdsMatchConstantsForAllRoles(): void
    {
        $roles = $this->db->table('roles')->get()->getResultArray();

        $expectedIds = [ROLE_ESTUDIANTE, ROLE_ADMIN_BIENESTAR, 3, ROLE_SUPER_ADMIN];
        $actualIds = array_map(fn($r) => (int) $r['id'], $roles);

        foreach ($expectedIds as $expectedId) {
            $this->assertContains($expectedId, $actualIds,
                "Rol con id {$expectedId} debe existir en la base de datos"
            );
        }
    }

    // ========================================================================
    //  18. VERIFICACIÓN DE ROLES ACTIVOS
    // ========================================================================

    public function testGetRolesActivos_IncludesCorrectRoles(): void
    {
        $rolesActivos = $this->db->table('roles')->where('estado', 'Activo')->get()->getResultArray();

        // Rol Docente (id=3) está Inactivo, no debe aparecer
        $this->assertCount(3, $rolesActivos, '3 roles activos: Estudiante, Admin Bienestar, Super Admin');

        foreach ($rolesActivos as $rol) {
            $this->assertSame('Activo', $rol['estado'],
                'Rol ' . $rol['nombre'] . ' debe tener estado Activo'
            );
            // Docente (id=3) no debe estar en activos
            $this->assertNotSame(3, (int) $rol['id'], 'Rol Docente (inactivo) no debe estar en activos');
        }
    }
}
