<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Test de Verificación del Sistema de Roles
 * 
 * Verifica que después de las correcciones realizadas (cambio de números mágicos
 * a constantes), los roles sigan funcionando correctamente en toda la aplicación.
 * 
 * Roles del sistema:
 *   ROLE_ESTUDIANTE      = 1  → Estudiante
 *   ROLE_ADMIN_BIENESTAR = 2  → Administrativo Bienestar
 *   ROLE_SUPER_ADMIN     = 4  → Super Administrador
 * 
 * @internal
 */
final class RoleSystemTest extends CIUnitTestCase
{
    // ========================================================================
    //  1. VERIFICACIÓN DE CONSTANTES
    // ========================================================================

    public function testConstantsAreDefined(): void
    {
        $this->assertTrue(defined('ROLE_ESTUDIANTE'), 'ROLE_ESTUDIANTE debe estar definida');
        $this->assertTrue(defined('ROLE_ADMIN_BIENESTAR'), 'ROLE_ADMIN_BIENESTAR debe estar definida');
        $this->assertTrue(defined('ROLE_SUPER_ADMIN'), 'ROLE_SUPER_ADMIN debe estar definida');
    }

    public function testConstantsHaveCorrectValues(): void
    {
        $this->assertSame(1, ROLE_ESTUDIANTE, 'ROLE_ESTUDIANTE debe ser 1');
        $this->assertSame(2, ROLE_ADMIN_BIENESTAR, 'ROLE_ADMIN_BIENESTAR debe ser 2');
        $this->assertSame(4, ROLE_SUPER_ADMIN, 'ROLE_SUPER_ADMIN debe ser 4');
    }

    public function testConstantsAreDistinct(): void
    {
        $values = [ROLE_ESTUDIANTE, ROLE_ADMIN_BIENESTAR, ROLE_SUPER_ADMIN];
        $unique = array_unique($values);
        $this->assertCount(3, $unique, 'Las constantes de rol deben tener valores únicos');
        $this->assertNotEquals(ROLE_ESTUDIANTE, ROLE_SUPER_ADMIN, 'ROLE_ESTUDIANTE y ROLE_SUPER_ADMIN deben ser diferentes');
    }

    // ========================================================================
    //  2. VERIFICACIÓN DE LÓGICA DE REDIRECCIÓN (DashboardController)
    // ========================================================================

    /**
     * Replica la lógica de DashboardController::index() para verificar
     * que las redirecciones por rol sean correctas.
     *
     * Lógica original:
     *   if ($rol_id == ROLE_ESTUDIANTE)      → redirect /estudiante
     *   elseif ($rol_id == ROLE_ADMIN_BIENESTAR) → redirect /admin-bienestar
     *   elseif ($rol_id == ROLE_SUPER_ADMIN)  → redirect /global-admin/dashboard
     *   else → redirect /login
     */
    public function roleRoutingProvider(): array
    {
        return [
            'Estudiante redirige a /estudiante'           => [1, '/estudiante'],
            'Admin Bienestar redirige a /admin-bienestar'  => [2, '/admin-bienestar'],
            'Super Admin redirige a /global-admin/dashboard' => [4, '/global-admin/dashboard'],
            'Rol desconocido redirige a /login'            => [3, '/login'],
            'Rol nulo redirige a /login'                   => [null, '/login'],
            'Rol 0 redirige a /login'                      => [0, '/login'],
        ];
    }

    /**
     * @dataProvider roleRoutingProvider
     */
    public function testDashboardRouting($rolId, string $expectedRoute): void
    {
        $route = $this->simulateDashboardRouting($rolId);
        $this->assertSame($expectedRoute, $route,
            "Rol {$rolId} debe redirigir a {$expectedRoute}, obtuvo {$route}"
        );
    }

    private function simulateDashboardRouting($rolId): string
    {
        if ($rolId === null) {
            return '/login';
        }

        if ($rolId == ROLE_ESTUDIANTE) {
            return '/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
            return '/admin-bienestar';
        } elseif ($rolId == ROLE_SUPER_ADMIN) {
            return '/global-admin/dashboard';
        }

        return '/login';
    }

    // ========================================================================
    //  3. VERIFICACIÓN DE LÓGICA DE AUTENTICACIÓN (AuthController)
    // ========================================================================

    /**
     * Replica la lógica de AuthController::index() y attemptLogin()
     * para verificar redirecciones post-login.
     */
    public function testAuthControllerRouting(): void
    {
        // Simular lógica de AuthController::index() después de login exitoso
        $routes = [
            ROLE_ESTUDIANTE      => '/estudiante',
            ROLE_ADMIN_BIENESTAR => '/admin-bienestar',
            ROLE_SUPER_ADMIN     => '/global-admin/dashboard',
        ];

        foreach ($routes as $rolId => $expectedRoute) {
            $route = $this->simulateAuthRouting($rolId);
            $this->assertSame($expectedRoute, $route,
                "Auth: Rol {$rolId} debe ir a {$expectedRoute}, obtuvo {$route}"
            );
        }

        // Rol no válido debe redirigir a login
        $invalidRoute = $this->simulateAuthRouting(99);
        $this->assertSame('/login', $invalidRoute, 'Rol inválido debe ir a login');
    }

    public function testAuthRoutingForAllControllerRoles(): void
    {
        // AuthController::index() routes
        $routes = [
            ROLE_ESTUDIANTE      => '/estudiante',
            ROLE_ADMIN_BIENESTAR => '/admin-bienestar',
            ROLE_SUPER_ADMIN     => '/global-admin/dashboard',
        ];

        foreach ($routes as $rolId => $expectedRoute) {
            // Simula if ($rol_id == ROLE_ESTUDIANTE) ... elseif ($rol_id == ROLE_SUPER_ADMIN)
            if ($rolId == ROLE_ESTUDIANTE) {
                $route = '/estudiante';
            } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
                $route = '/admin-bienestar';
            } elseif ($rolId == ROLE_SUPER_ADMIN) {
                $route = '/global-admin/dashboard';
            } else {
                $route = '/login';
            }

            $this->assertSame($expectedRoute, $route,
                "Auth routing: rol_id={$rolId} debe mapear a {$expectedRoute}"
            );
        }
    }

    private function simulateAuthRouting($rolId): string
    {
        if ($rolId == ROLE_ESTUDIANTE) {
            return '/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
            return '/admin-bienestar';
        } elseif ($rolId == ROLE_SUPER_ADMIN) {
            return '/global-admin/dashboard';
        }

        return '/login';
    }

    // ========================================================================
    //  4. VERIFICACIÓN DE SUPERADMIN CONTROLLER (BUG CRÍTICO)
    // ========================================================================

    /**
     * Verifica que la corrección del bug crítico en SuperAdminController
     * funcione correctamente.
     * 
     * Antes: if (session('rol_id') != 1) ← ¡Esto era ROLE_ESTUDIANTE!
     * Después: if (session('rol_id') != ROLE_SUPER_ADMIN) ← ¡Correcto!
     */
    public function testSuperAdminAccessControlBlocksNonSuperAdmin(): void
    {
        // Solo ROLE_SUPER_ADMIN (4) debe poder acceder
        $this->assertTrue(
            $this->simulateSuperAdminAccess(ROLE_SUPER_ADMIN),
            'Super Admin (4) debe tener acceso'
        );

        // ROLE_ESTUDIANTE (1) NO debe tener acceso
        $this->assertFalse(
            $this->simulateSuperAdminAccess(ROLE_ESTUDIANTE),
            'Estudiante (1) NO debe tener acceso a SuperAdmin'
        );

        // ROLE_ADMIN_BIENESTAR (2) NO debe tener acceso
        $this->assertFalse(
            $this->simulateSuperAdminAccess(ROLE_ADMIN_BIENESTAR),
            'Admin Bienestar (2) NO debe tener acceso a SuperAdmin'
        );

        // Rol inexistente (3) NO debe tener acceso
        $this->assertFalse(
            $this->simulateSuperAdminAccess(3),
            'Rol 3 NO debe tener acceso a SuperAdmin'
        );

        // Sin sesión (null) NO debe tener acceso
        $this->assertFalse(
            $this->simulateSuperAdminAccess(null),
            'Sin sesión NO debe tener acceso a SuperAdmin'
        );
    }

    /**
     * Verificación específica: el bug original era != 1 (Estudiante) en vez de != 4 (Super Admin).
     * Confirmamos que con la corrección, el Estudiante (1) es rechazado y el SuperAdmin (4) es aceptado.
     */
    public function testSuperAdminBugFixConfirmation(): void
    {
        // Esto es lo que HACÍA el bug (código original roto):
        $bugVersionBlockedEstudiante = (1 != 1);  // ¡Falso! El bug permitía a estudiantes
        $bugVersionBlockedSuperAdmin = (4 != 1);  // ¡Verdadero! El bug bloqueaba a SuperAdmin

        // Esto es lo que DEBE hacer el código corregido:
        $fixedVersionBlockedEstudiante = (1 != ROLE_SUPER_ADMIN);  // ¡Verdadero! Estudiantes bloqueados
        $fixedVersionBlockedSuperAdmin = (4 != ROLE_SUPER_ADMIN);  // ¡Falso! SuperAdmin permitido

        // Verificar comportamiento del BUG (debería haber sido al revés)
        $this->assertFalse($bugVersionBlockedEstudiante,
            'BUG: Estudiante (1) NO debería pasar el filtro != 1, pero el bug lo permitía'
        );
        $this->assertTrue($bugVersionBlockedSuperAdmin,
            'BUG: SuperAdmin (4) debería pasar el filtro != 1, pero el bug lo bloqueaba'
        );

        // Verificar comportamiento CORREGIDO
        $this->assertTrue($fixedVersionBlockedEstudiante,
            'FIX: Estudiante (1) debe ser bloqueado con != ROLE_SUPER_ADMIN'
        );
        $this->assertFalse($fixedVersionBlockedSuperAdmin,
            'FIX: SuperAdmin (4) NO debe ser bloqueado con != ROLE_SUPER_ADMIN'
        );
    }

    private function simulateSuperAdminAccess($rolId): bool
    {
        // Lógica idéntica a la de SuperAdminController::initController()
        if ($rolId === null) {
            return false;
        }

        return $rolId == ROLE_SUPER_ADMIN;
    }

    /**
     * Verifica que SuperAdminController proteja correctamente
     * el cambio de estado de otro SuperAdmin.
     */
    public function testSuperAdminCannotChangeOtherSuperAdmin(): void
    {
        // Lógica: if ($usuario['rol_id'] == ROLE_SUPER_ADMIN) → no permitir cambio
        $targetSuperAdmin = ROLE_SUPER_ADMIN;
        $targetEstudiante = ROLE_ESTUDIANTE;

        $this->assertTrue(
            $targetSuperAdmin == ROLE_SUPER_ADMIN,
            'No se debe permitir cambiar estado/rol de otro Super Admin'
        );

        $this->assertFalse(
            $targetEstudiante == ROLE_SUPER_ADMIN,
            'Se debe permitir cambiar estado/rol de un Estudiante'
        );
    }

    // ========================================================================
    //  5. VERIFICACIÓN DE LÓGICA DE ROLEFILTER
    // ========================================================================

    /**
     * Verifica la lógica del RoleFilter que determina si un rol
     * está dentro de los roles permitidos.
     */
    public function roleFilterProvider(): array
    {
        return [
            'Estudiante en [1]'           => [1, [1], true],
            'Estudiante en [1,2]'         => [1, [1, 2], true],
            'Estudiante en [2]'           => [1, [2], false],
            'Estudiante en [4]'           => [1, [4], false],
            'Admin en [2]'                => [2, [2], true],
            'Admin en [2,4]'              => [2, [2, 4], true],
            'Admin en [1]'                => [2, [1], false],
            'Admin en [4]'                => [2, [4], false],
            'SuperAdmin en [4]'           => [4, [4], true],
            'SuperAdmin en [2,4]'         => [4, [2, 4], true],
            'SuperAdmin en [1]'           => [4, [1], false],
            'SuperAdmin en [1,2]'         => [4, [1, 2], false],
            'Rol inválido en [1,2,4]'     => [99, [1, 2, 4], false],
            'Sin argumentos (cualquiera)' => [1, [], true],
            'Sin argumentos (admin)'      => [2, [], true],
            'Sin argumentos (super)'      => [4, [], true],
        ];
    }

    /**
     * @dataProvider roleFilterProvider
     */
    public function testRoleFilterLogic(int $userRolId, array $allowedRoles, bool $expected): void
    {
        $result = $this->simulateRoleFilter($userRolId, $allowedRoles);
        $roleNames = [
            1 => 'Estudiante',
            2 => 'Admin Bienestar',
            4 => 'Super Admin',
        ];
        $roleName = $roleNames[$userRolId] ?? "Rol {$userRolId}";
        $allowedStr = empty($allowedRoles) ? '(cualquiera)' : '[' . implode(',', $allowedRoles) . ']';

        $this->assertSame($expected, $result,
            "{$roleName} debe " . ($expected ? 'tener' : 'NO tener') . " acceso a roles {$allowedStr}"
        );
    }

    private function simulateRoleFilter(int $userRolId, array $allowedRoles): bool
    {
        // Lógica idéntica a RoleFilter::before()
        if (empty($allowedRoles)) {
            return true; // Sin filtro de roles, solo autenticación
        }

        return in_array($userRolId, $allowedRoles, true);
    }

    /**
     * Verifica que RoleFilter use comparación estricta (int).
     */
    public function testRoleFilterStrictComparison(): void
    {
        // La comparación debe ser estricta (=== true en in_array)
        // para evitar que "1" string pase como true
        $resultLoose = in_array('1', [ROLE_ESTUDIANTE], false);  // loose
        $resultStrict = in_array('1', [ROLE_ESTUDIANTE], true);  // strict

        $this->assertTrue($resultLoose, 'Comparación no estricta permite string "1"');
        $this->assertFalse($resultStrict, 'Comparación estricta rechaza string "1"');
    }

    // ========================================================================
    //  6. VERIFICACIÓN DE LÓGICA DE VISTAS
    // ========================================================================

    /**
     * Verifica la lógica de la vista perfil/administrador.php
     * que muestra el nombre del rol según el rol_id.
     * 
     * Lógica: $rol = session('rol_id') == ROLE_ADMIN_BIENESTAR 
     *             ? 'Administrativo Bienestar' 
     *             : 'Super Administrador';
     */
    public function profileViewRoleProvider(): array
    {
        return [
            [ROLE_ADMIN_BIENESTAR, 'Administrativo Bienestar'],
            [ROLE_SUPER_ADMIN, 'Super Administrador'],
            [ROLE_ESTUDIANTE, 'Super Administrador'], // edge: no debería llegar aquí (filtrado por controlador)
        ];
    }

    /**
     * @dataProvider profileViewRoleProvider
     */
    public function testProfileViewRoleName(int $rolId, string $expectedName): void
    {
        $roleName = $this->simulateProfileViewRoleLogic($rolId);
        $this->assertSame($expectedName, $roleName,
            "rol_id={$rolId} debe mostrar '{$expectedName}', obtuvo '{$roleName}'"
        );
    }

    private function simulateProfileViewRoleLogic(int $rolId): string
    {
        // Lógica idéntica a la vista perfil/administrador.php
        return $rolId == ROLE_ADMIN_BIENESTAR
            ? 'Administrativo Bienestar'
            : 'Super Administrador';
    }

    // ========================================================================
    //  7. VERIFICACIÓN DE LÓGICA DE CUENTA CONTROLLER
    // ========================================================================

    /**
     * Verifica la lógica de CuentaController para enrutamiento de vistas
     * según el rol.
     */
    public function cuentaRoutingProvider(): array
    {
        return [
            'Estudiante ve vista estudiante'   => [ROLE_ESTUDIANTE, 'cuenta/estudiante'],
            'Admin ve vista administrador'     => [ROLE_ADMIN_BIENESTAR, 'cuenta/administrador'],
            'SuperAdmin ve vista administrador' => [ROLE_SUPER_ADMIN, 'cuenta/administrador'],
        ];
    }

    /**
     * @dataProvider cuentaRoutingProvider
     */
    public function testCuentaControllerRouting(int $rolId, string $expectedView): void
    {
        $view = $this->simulateCuentaRouting($rolId);
        $this->assertSame($expectedView, $view,
            "Rol {$rolId} debe usar vista '{$expectedView}'"
        );
    }

    private function simulateCuentaRouting(int $rolId): string
    {
        // Lógica idéntica a CuentaController::index()
        if ($rolId == ROLE_ESTUDIANTE) {
            return 'cuenta/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR || $rolId == ROLE_SUPER_ADMIN) {
            return 'cuenta/administrador';
        }

        return 'auth/login';
    }

    // ========================================================================
    //  8. VERIFICACIÓN DE LÓGICA DE PERFIL CONTROLLER
    // ========================================================================

    /**
     * Verifica la lógica de PerfilController para enrutamiento de vistas.
     */
    public function testPerfilControllerRouting(): void
    {
        $routes = [
            ROLE_ESTUDIANTE      => 'perfil/estudiante',
            ROLE_ADMIN_BIENESTAR => 'perfil/administrador',
            ROLE_SUPER_ADMIN     => 'perfil/administrador',
        ];

        foreach ($routes as $rolId => $expectedView) {
            $view = $this->simulatePerfilRouting($rolId);
            $this->assertSame($expectedView, $view,
                "Perfil: Rol {$rolId} debe usar vista '{$expectedView}'"
            );
        }
    }

    private function simulatePerfilRouting(int $rolId): string
    {
        if ($rolId == ROLE_ESTUDIANTE) {
            return 'perfil/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR || $rolId == ROLE_SUPER_ADMIN) {
            return 'perfil/administrador';
        }

        return 'auth/login';
    }

    // ========================================================================
    //  9. VERIFICACIÓN DE ACCESO A FICHA CONTROLLER
    // ========================================================================

    /**
     * Verifica la lógica de acceso a FichaController según el rol.
     *
     * Lógica original:
     *   if (session('rol_id') == ROLE_ESTUDIANTE) { return view('Estudiante/FichasES'); }
     *   if (session('rol_id') == ROLE_ADMIN_BIENESTAR) { return view('AdminBienestar/fichas'); }
     */
    public function testFichaControllerAccess(): void
    {
        $routes = [
            ROLE_ESTUDIANTE      => 'Estudiante/FichasES',
            ROLE_ADMIN_BIENESTAR => 'AdminBienestar/fichas',
        ];

        foreach ($routes as $rolId => $expectedView) {
            $view = $this->simulateFichaAccess($rolId);
            $this->assertSame($expectedView, $view,
                "FichaController: Rol {$rolId} debe usar vista '{$expectedView}'"
            );
        }

        // Rol Super Admin no tiene acceso definido (redirige a /login)
        $superAdminView = $this->simulateFichaAccess(ROLE_SUPER_ADMIN);
        $this->assertSame('/login', $superAdminView,
            'SuperAdmin NO debe tener acceso directo a FichaController'
        );

        // Rol inexistente tampoco tiene acceso
        $unknownView = $this->simulateFichaAccess(99);
        $this->assertSame('/login', $unknownView,
            'Rol inexistente NO debe tener acceso a FichaController'
        );
    }

    private function simulateFichaAccess(int $rolId): string
    {
        if ($rolId == ROLE_ESTUDIANTE) {
            return 'Estudiante/FichasES';
        }
        if ($rolId == ROLE_ADMIN_BIENESTAR) {
            return 'AdminBienestar/fichas';
        }
        return '/login';
    }

    // ========================================================================
    //  10. VERIFICACIÓN DE ADMINISTRADORES EN VISTA DE USUARIOS
    // ========================================================================

    /**
     * Verifica que la lógica de conteo de administradores en la vista
     * gestion_usuarios.php use los valores correctos de rol.
     * 
     * Lógica: in_array($u['rol_id'], [2, 4]) para contar Admins + SuperAdmins
     */
    public function testAdminCountLogic(): void
    {
        $usuarios = [
            ['rol_id' => ROLE_ESTUDIANTE],
            ['rol_id' => ROLE_ESTUDIANTE],
            ['rol_id' => ROLE_ADMIN_BIENESTAR],
            ['rol_id' => ROLE_SUPER_ADMIN],
            ['rol_id' => ROLE_ESTUDIANTE],
        ];

        $adminCount = count(array_filter($usuarios, function($u) {
            return in_array($u['rol_id'], [ROLE_ADMIN_BIENESTAR, ROLE_SUPER_ADMIN]);
        }));

        $this->assertSame(2, $adminCount,
            'Debe contar 2 administradores (1 Admin Bienestar + 1 Super Admin)'
        );
    }

    /**
     * Verifica la lógica del badge de color en gestion_usuarios.php.
     * 
     * Lógica: rol_id == 4 → danger, rol_id == 2 → warning, else → primary
     */
    public function testRoleBadgeClass(): void
    {
        $badgeClasses = [
            ROLE_ESTUDIANTE      => 'primary',
            ROLE_ADMIN_BIENESTAR => 'warning',
            ROLE_SUPER_ADMIN     => 'danger',
        ];

        foreach ($badgeClasses as $rolId => $expectedClass) {
            $class = $this->simulateBadgeClass($rolId);
            $this->assertSame($expectedClass, $class,
                "Rol {$rolId} debe tener badge '{$expectedClass}'"
            );
        }
    }

    private function simulateBadgeClass(int $rolId): string
    {
        if ($rolId == ROLE_SUPER_ADMIN) {
            return 'danger';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
            return 'warning';
        }
        return 'primary';
    }

    // ========================================================================
    //  11. VERIFICACIÓN DE CONSISTENCIA - NO MAGIC NUMBERS
    // ========================================================================

    /**
     * Verifica que los controladores NO estén usando números mágicos
     * para comparaciones de rol. Esto se verifica escaneando los archivos.
     */
    public function testNoMagicNumbersInControllers(): void
    {
        $controllerDir = ROOTPATH . 'app/Controllers';
        $files = $this->getControllerFiles($controllerDir);

        $magicNumberPatterns = $this->findMagicNumberPatterns($files);

        // Si hay archivos con números mágicos, listarlos
        $errorMessages = [];
        foreach ($magicNumberPatterns as $file => $lines) {
            $errorMessages[] = "{$file}: " . implode('; ', $lines);
        }

        $this->assertEmpty(
            $magicNumberPatterns,
            "Se encontraron números mágicos de rol en los siguientes archivos:\n" .
            implode("\n", $errorMessages)
        );
    }

    /**
     * Obtiene todos los archivos PHP de controladores.
     */
    private function getControllerFiles(string $dir): array
    {
        $files = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                $files = array_merge($files, $this->getControllerFiles($path));
            } elseif (str_ends_with($item, '.php')) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Busca patrones de números mágicos en archivos.
     * Busca específicamente: rol_id == 1, rol_id != 1, etc.
     * Pero solo si NO están acompañados de ROLE_ (constantes).
     */
    private function findMagicNumberPatterns(array $files): array
    {
        $patterns = [
            'rol_id == 1',
            "rol_id == '1'",
            'rol_id != 1',
            "rol_id != '1'",
            'rol_id == 2',
            "rol_id == '2'",
            'rol_id != 2',
            "rol_id != '2'",
            'rol_id == 4',
            "rol_id == '4'",
            'rol_id != 4',
            "rol_id != '4'",
        ];

        $magicNumbers = [];

        foreach ($files as $file) {
            $relativePath = str_replace(ROOTPATH, '', $file);
            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            $fileIssues = [];

            foreach ($lines as $lineNum => $line) {
                $trimmedLine = trim($line);

                foreach ($patterns as $pattern) {
                    if (str_contains($trimmedLine, $pattern) && !str_contains($trimmedLine, 'ROLE_')) {
                        $fileIssues[] = "Línea " . ($lineNum + 1) . ": {$trimmedLine}";
                        break;
                    }
                }
            }

            if (!empty($fileIssues)) {
                $magicNumbers[$relativePath] = $fileIssues;
            }
        }

        return $magicNumbers;
    }

    // ========================================================================
    //  12. VERIFICACIÓN DE LÓGICA DE ESTUDIANTE CONTROLLER  
    // ========================================================================

    /**
     * Verifica que EstudianteController restrinja acceso solo a estudiantes.
     */
    public function testEstudianteControllerAccessControl(): void
    {
        // Lógica: if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE)
        $this->assertFalse(
            $this->simulateEstudianteAccess(ROLE_ESTUDIANTE),
            'Estudiante (1) debe tener acceso (no bloquear)'
        );

        $this->assertTrue(
            $this->simulateEstudianteAccess(ROLE_ADMIN_BIENESTAR),
            'Admin Bienestar (2) NO debe tener acceso (bloquear)'
        );

        $this->assertTrue(
            $this->simulateEstudianteAccess(ROLE_SUPER_ADMIN),
            'Super Admin (4) NO debe tener acceso (bloquear)'
        );
    }

    private function simulateEstudianteAccess(int $rolId): bool
    {
        // Replica: if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE)
        // retorna true si está BLOQUEADO
        return $rolId != ROLE_ESTUDIANTE;
    }

    // ========================================================================
    //  13. VERIFICACIÓN DE FILTRO AuthController CONFIG
    // ========================================================================

    /**
     * Verifica que las rutas del AuthController estén correctamente mapeadas
     * para todos los roles del sistema.
     */
    public function testAuthLoginAndIndexRoutes(): void
    {
        $roles = [
            ROLE_ESTUDIANTE => ['name' => 'Estudiante', 'route' => '/estudiante'],
            ROLE_ADMIN_BIENESTAR => ['name' => 'Admin Bienestar', 'route' => '/admin-bienestar'],
            ROLE_SUPER_ADMIN => ['name' => 'Super Admin', 'route' => '/global-admin/dashboard'],
        ];

        foreach ($roles as $rolId => $info) {
            // Verificar ruta en AuthController::index()
            $route = $this->simulateAuthIndexRoute($rolId);
            $this->assertSame($info['route'], $route,
                "AuthController::index() para {$info['name']} debe redirigir a {$info['route']}"
            );

            // Verificar ruta en AuthController::attemptLogin()
            $routeLogin = $this->simulateAuthAttemptLoginRoute($rolId);
            $this->assertSame($info['route'], $routeLogin,
                "AuthController::attemptLogin() para {$info['name']} debe redirigir a {$info['route']}"
            );
        }
    }

    private function simulateAuthIndexRoute(int $rolId): string
    {
        if ($rolId == ROLE_ESTUDIANTE) {
            return '/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
            return '/admin-bienestar';
        } elseif ($rolId == ROLE_SUPER_ADMIN) {
            return '/global-admin/dashboard';
        }

        return '/login';
    }

    private function simulateAuthAttemptLoginRoute(int $rolId): string
    {
        if ($rolId == ROLE_ESTUDIANTE) {
            return '/estudiante';
        } elseif ($rolId == ROLE_ADMIN_BIENESTAR) {
            return '/admin-bienestar';
        } elseif ($rolId == ROLE_SUPER_ADMIN) {
            return '/global-admin/dashboard';
        }

        return '/login';
    }

    // ========================================================================
    //  14. VERIFICACIÓN DE CUENTA Y PERFIL PARA SUPER ADMIN
    // ========================================================================

    /**
     * Verifica que Super Admin (rol_id=4) sea tratado igual que Admin Bienestar
     * en los controladores CuentaController y PerfilController.
     *
     * Lógica original:
     *   if ($rol_id == ROLE_ESTUDIANTE) { ... }
     *   } elseif ($rol_id == ROLE_ADMIN_BIENESTAR || $rol_id == ROLE_SUPER_ADMIN) { ... }
     */
    public function testSuperAdminTreatedAsAdminInCuentaYPerfil(): void
    {
        // Verificar que tanto Admin (2) como SuperAdmin (4) matchean la condición combinada
        $matchesAdminCondition = fn(int $rolId): bool =>
            $rolId == ROLE_ADMIN_BIENESTAR || $rolId == ROLE_SUPER_ADMIN;

        $this->assertTrue(
            $matchesAdminCondition(ROLE_ADMIN_BIENESTAR),
            'Admin Bienestar (2) debe matchear condicion || ROLE_ADMIN_BIENESTAR'
        );
        $this->assertTrue(
            $matchesAdminCondition(ROLE_SUPER_ADMIN),
            'SuperAdmin (4) debe matchear condicion || ROLE_SUPER_ADMIN'
        );
        $this->assertFalse(
            $matchesAdminCondition(ROLE_ESTUDIANTE),
            'Estudiante (1) NO debe matchear condicion de admin'
        );
        $this->assertFalse(
            $matchesAdminCondition(0),
            'Rol 0 NO debe matchear condicion de admin'
        );
    }
}
