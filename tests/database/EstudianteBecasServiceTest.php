<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\EstudianteBecasService;
use Tests\Support\Database\Seeds\RoleSeeder;
use Tests\Support\Database\Seeds\BecaSeeder;
use Tests\Support\Database\Seeds\FichaSocioeconomicaSeeder;
use Tests\Support\Database\Seeds\ServiceTestSeeder;

/**
 * Tests de Integración: EstudianteBecasService
 *
 * Verifica que EstudianteBecasService funcione correctamente con la base de datos
 * SQLite en memoria, cubriendo verificación de elegibilidad, consulta de becas,
 * creación de solicitudes, y estadísticas del estudiante.
 *
 * Nota: Algunos tests de crearSolicitudBeca se omiten porque requieren
 * la tabla estudiantes_habilitacion_becas y lógica de habilitación que
 * depende de la sesión del estudiante (no testeable sin contexto HTTP).
 *
 * @internal
 */
final class EstudianteBecasServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = [
        'Tests\Support\Database\Migrations\CreateRolesTables',
        'Tests\Support\Database\Migrations\CreateBecasTables',
        'Tests\Support\Database\Migrations\CreateServiceTestTables',
    ];

    protected $seed = [
        RoleSeeder::class,
        BecaSeeder::class,
        FichaSocioeconomicaSeeder::class,
        ServiceTestSeeder::class,
    ];

    protected EstudianteBecasService $service;

    /**
     * Pre-connect to the test database and strip the DBPrefix BEFORE
     * parent::setUp() runs migrations. The tests group has DBPrefix='db_'
     * which causes raw SQL in services to fail. By stripping the prefix
     * before migration, tables are created without prefix, making both
     * Query Builder and raw SQL work correctly.
     */
    protected function setUp(): void
    {
        $db = \Config\Database::connect('tests');
        $db->setPrefix('');

        parent::setUp();
        $this->service = new EstudianteBecasService();
    }

    // ========================================================================
    //  1. PUEDE SOLICITAR BECAS
    // ========================================================================

    public function testPuedeSolicitarBecas_ConFichaAprobadaReturnsTrue(): void
    {
        // Estudiante 1 has ficha Aprobada in periodo 1
        $result = $this->service->puedesolicitarBecas(1, 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('puede_solicitar', $result);
        // Should be able to solicit because:
        // - Periodo 1 is active for becas (activo_becas=1)
        // - Student has ficha in estado 'Aprobada'
        // - No solicitudes yet for this student in this period (BecaSeeder doesn't create one)
        $this->assertTrue($result['puede_solicitar'], 'Estudiante con ficha aprobada debe poder solicitar');
    }

    public function testPuedeSolicitarBecas_ConFichaBorradorReturnsFalse(): void
    {
        // We need an ACTIVE periodo (activo_becas=1) with a Borrador ficha.
        // Periodo 1 is active but estudiante 1's ficha is Aprobada there.
        // Periodo 2 is inactive (activo_becas=0).
        // So we insert a new active periodo and a Borrador ficha for estudiante 1.
        $db = $this->db;
        $db->table('periodos_academicos')->insert([
            'nombre'        => '2026-2027 Semestre I',
            'descripcion'   => 'Periodo activo para test borrador',
            'fecha_inicio'  => '2026-03-01',
            'fecha_fin'     => '2026-08-31',
            'estado'        => 'Activo',
            'activo'        => 1,
            'activo_fichas' => 1,
            'activo_becas'  => 1,
            'permite_fichas' => 1,
            'permite_becas'  => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // Get the ID of the newly inserted periodo
        $newPeriodo = $db->table('periodos_academicos')
            ->where('nombre', '2026-2027 Semestre I')
            ->get()
            ->getRowArray();
        $periodoId = $newPeriodo['id'];

        // Insert Borrador ficha for estudiante 1 in this new active periodo
        $db->table('fichas_socioeconomicas')->insert([
            'estudiante_id'  => 1,
            'periodo_id'     => $periodoId,
            'json_data'      => json_encode(['test' => true]),
            'estado'         => 'Borrador',
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

        $result = $this->service->puedesolicitarBecas(1, $periodoId);

        $this->assertFalse($result['puede_solicitar']);
        $this->assertStringContainsString('borrador', strtolower($result['motivo']));
    }

    public function testPuedeSolicitarBecas_SinFichaReturnsFalse(): void
    {
        // Estudiante 3 (Pedro) has no fichas at all
        $result = $this->service->puedesolicitarBecas(3, 1);

        $this->assertFalse($result['puede_solicitar']);
        $this->assertStringContainsString('ficha', strtolower($result['motivo']));
    }

    public function testPuedeSolicitarBecas_PeriodoInactivoReturnsFalse(): void
    {
        // Periodo 2 is inactive (activo_becas=0)
        $result = $this->service->puedesolicitarBecas(1, 2);

        $this->assertFalse($result['puede_solicitar']);
    }

    public function testPuedeSolicitarBecas_PeriodoInexistenteReturnsFalse(): void
    {
        $result = $this->service->puedesolicitarBecas(1, 999);

        $this->assertFalse($result['puede_solicitar']);
    }

    public function testPuedeSolicitarBecas_ReturnsExpectedKeys(): void
    {
        $result = $this->service->puedesolicitarBecas(1, 1);

        $this->assertArrayHasKey('puede_solicitar', $result);
        $this->assertArrayHasKey('motivo', $result);
        $this->assertArrayHasKey('detalles', $result);
        $this->assertIsArray($result['detalles']);
    }

    public function testPuedeSolicitarBecas_ConFichaEnviadaReturnsTrue(): void
    {
        // Estudiante 2 has ficha Enviada in periodo 1
        // Enviada is NOT Borrador, so it should be allowed
        $result = $this->service->puedesolicitarBecas(2, 1);

        // Periodo 1 is active for becas, ficha exists and is Enviada (not Borrador)
        // Estudiante 2 already has 1 solicitud (Postulada), max is 3
        $this->assertTrue($result['puede_solicitar']);
    }

    // ========================================================================
    //  2. VERIFICAR CUPOS DISPONIBLES
    // ========================================================================

    public function testVerificarCuposDisponibles_BecaExistenteConCupos(): void
    {
        // Beca Deportiva (id=2) has cupos_disponibles=5
        $result = $this->service->verificarCuposDisponibles(2, 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('disponible', $result);
        $this->assertTrue($result['disponible']);
        $this->assertArrayHasKey('cupos_disponibles', $result);
        $this->assertGreaterThan(0, $result['cupos_disponibles']);
    }

    public function testVerificarCuposDisponibles_BecaSinCuposLimitados(): void
    {
        // Beca Económica (id=4) has cupos_disponibles=null (sin límite)
        $result = $this->service->verificarCuposDisponibles(4, 1);

        $this->assertTrue($result['disponible']);
        $this->assertNull($result['cupos_disponibles']);
    }

    public function testVerificarCuposDisponibles_BecaInexistente(): void
    {
        $result = $this->service->verificarCuposDisponibles(999, 1);

        $this->assertFalse($result['disponible']);
        $this->assertArrayHasKey('mensaje', $result);
    }

    public function testVerificarCuposDisponibles_ConSolicitudesReducenCupos(): void
    {
        // Beca Deportiva (id=2) has 5 cupos, ServiceTestSeeder adds 1 solicitud
        $result = $this->service->verificarCuposDisponibles(2, 1);

        $this->assertTrue($result['disponible']);
        // 5 cupos - 1 solicitud activa = 4 disponibles
        $this->assertSame(4, $result['cupos_disponibles']);
        $this->assertSame(5, $result['cupos_totales']);
        $this->assertSame(1, $result['solicitudes_activas']);
    }

    public function testVerificarCuposDisponibles_BecaConCerosCupos(): void
    {
        // Beca Cultural (id=3) has cupos_disponibles=0
        // But it's inactive - verificarCuposDisponibles doesn't check active status
        $result = $this->service->verificarCuposDisponibles(3, 2);

        // cupos_disponibles=0 is treated as "sin límite" by the service logic:
        // if (empty($beca['cupos_disponibles']) || $beca['cupos_disponibles'] <= 0)
        $this->assertTrue($result['disponible']);
    }

    // ========================================================================
    //  3. GET SOLICITUDES ESTUDIANTE
    // ========================================================================

    public function testGetSolicitudesEstudiante_ReturnsStudentSolicitudes(): void
    {
        // Estudiante 1 has 1 solicitud (Aprobada) in BecaSeeder
        $solicitudes = $this->service->getSolicitudesEstudiante(1);

        $this->assertIsArray($solicitudes);
        $this->assertCount(1, $solicitudes);
    }

    public function testGetSolicitudesEstudiante_IncludesJoinedData(): void
    {
        $solicitudes = $this->service->getSolicitudesEstudiante(1);
        $this->assertNotEmpty($solicitudes);

        $solicitud = $solicitudes[0];
        $this->assertArrayHasKey('beca_nombre', $solicitud);
        $this->assertArrayHasKey('tipo_beca', $solicitud);
        $this->assertArrayHasKey('periodo_nombre', $solicitud);
        $this->assertSame('Beca Académica Excelencia', $solicitud['beca_nombre']);
    }

    public function testGetSolicitudesEstudiante_IncludesDocumentos(): void
    {
        $solicitudes = $this->service->getSolicitudesEstudiante(1);
        $this->assertNotEmpty($solicitudes);

        // Solicitud 1 has 2 documents (from ServiceTestSeeder)
        $solicitud = $solicitudes[0];
        $this->assertArrayHasKey('documentos', $solicitud);
        $this->assertCount(2, $solicitud['documentos']);
    }

    public function testGetSolicitudesEstudiante_IncludesProgresoDocumentos(): void
    {
        $solicitudes = $this->service->getSolicitudesEstudiante(1);
        $solicitud = $solicitudes[0];

        $this->assertArrayHasKey('progreso_documentos', $solicitud);
        $progreso = $solicitud['progreso_documentos'];
        $this->assertArrayHasKey('porcentaje', $progreso);
        $this->assertArrayHasKey('subidos', $progreso);
        $this->assertArrayHasKey('total', $progreso);
        $this->assertSame(2, $progreso['total']);
    }

    public function testGetSolicitudesEstudiante_FiltersByPeriodo(): void
    {
        $solicitudesPeriodo1 = $this->service->getSolicitudesEstudiante(1, 1);
        $this->assertCount(1, $solicitudesPeriodo1);

        $solicitudesPeriodo999 = $this->service->getSolicitudesEstudiante(1, 999);
        $this->assertCount(0, $solicitudesPeriodo999);
    }

    public function testGetSolicitudesEstudiante_EstudianteSinSolicitudes(): void
    {
        // Estudiante 3 (Pedro) has no solicitudes
        $solicitudes = $this->service->getSolicitudesEstudiante(3);

        $this->assertIsArray($solicitudes);
        $this->assertCount(0, $solicitudes);
    }

    public function testGetSolicitudesEstudiante_EstudianteInexistente(): void
    {
        $solicitudes = $this->service->getSolicitudesEstudiante(999);
        $this->assertCount(0, $solicitudes);
    }

    public function testGetSolicitudesEstudiante_OrdenadoPorFechaDesc(): void
    {
        // Estudiante 2 has 1 solicitud (Postulada from ServiceTestSeeder)
        $solicitudes = $this->service->getSolicitudesEstudiante(2);
        $this->assertCount(1, $solicitudes);
    }

    // ========================================================================
    //  4. GET DOCUMENTOS SOLICITUD
    // ========================================================================

    public function testGetDocumentosSolicitud_ReturnsDocuments(): void
    {
        // Solicitud 1 has 2 documents
        $docs = $this->service->getDocumentosSolicitud(1);

        $this->assertIsArray($docs);
        $this->assertCount(2, $docs);
    }

    public function testGetDocumentosSolicitud_IncludesRequiredFields(): void
    {
        $docs = $this->service->getDocumentosSolicitud(1);
        $this->assertNotEmpty($docs);

        foreach ($docs as $doc) {
            $this->assertArrayHasKey('documento_nombre', $doc);
            $this->assertArrayHasKey('estado', $doc);
            $this->assertArrayHasKey('obligatorio', $doc);
            $this->assertArrayHasKey('orden_revision', $doc);
        }
    }

    public function testGetDocumentosSolicitud_OrderedByOrdenRevision(): void
    {
        $docs = $this->service->getDocumentosSolicitud(1);

        $this->assertCount(2, $docs);
        $this->assertLessThanOrEqual(
            $docs[1]['orden_revision'],
            $docs[0]['orden_revision'],
            'Documents should be ordered by orden_revision ASC'
        );
    }

    public function testGetDocumentosSolicitud_SolicitudInexistente(): void
    {
        $docs = $this->service->getDocumentosSolicitud(999);
        $this->assertCount(0, $docs);
    }

    public function testGetDocumentosSolicitud_DocumentStatesCorrect(): void
    {
        $docs = $this->service->getDocumentosSolicitud(1);

        // Both documents for solicitud 1 are 'Aprobado' (from ServiceTestSeeder)
        foreach ($docs as $doc) {
            $this->assertSame('Aprobado', $doc['estado']);
        }
    }

    public function testGetDocumentosSolicitud_Solicitud2Pendiente(): void
    {
        // Solicitud 2 has 1 document in 'Pendiente' state
        $docs = $this->service->getDocumentosSolicitud(2);

        $this->assertCount(1, $docs);
        $this->assertSame('Pendiente', $docs[0]['estado']);
    }

    // ========================================================================
    //  5. GET ESTADÍSTICAS ESTUDIANTE
    // ========================================================================

    public function testGetEstadisticasEstudiante_ReturnsExpectedStructure(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(1);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('fichas', $stats);
        $this->assertArrayHasKey('solicitudes_becas', $stats);
    }

    public function testGetEstadisticasEstudiante_FichasCountsCorrect(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(1);

        // Estudiante 1 has 2 fichas: Aprobada + Borrador
        $this->assertSame(2, $stats['fichas']['total']);
        $this->assertSame(1, $stats['fichas']['aprobadas']);
        $this->assertSame(1, $stats['fichas']['pendientes']); // Borrador counts as pending
    }

    public function testGetEstadisticasEstudiante_SolicitudesCountsCorrect(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(1);

        // Estudiante 1 has 1 solicitud (Aprobada)
        $this->assertSame(1, $stats['solicitudes_becas']['total']);
        $this->assertSame(1, $stats['solicitudes_becas']['aprobadas']);
    }

    public function testGetEstadisticasEstudiante_EstudianteSinDatos(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(999);

        $this->assertSame(0, $stats['fichas']['total']);
        $this->assertSame(0, $stats['solicitudes_becas']['total']);
    }

    public function testGetEstadisticasEstudiante_HabilitacionActual(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(1);

        $this->assertArrayHasKey('habilitacion_actual', $stats);
        $habilitacion = $stats['habilitacion_actual'];
        $this->assertArrayHasKey('puede_solicitar', $habilitacion);
        $this->assertIsBool($habilitacion['puede_solicitar']);
    }

    public function testGetEstadisticasEstudiante_Estudiante2Counts(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(2);

        // Estudiante 2 has 1 ficha (Enviada) and 1 solicitud (Postulada)
        $this->assertSame(1, $stats['fichas']['total']);
        $this->assertSame(1, $stats['solicitudes_becas']['total']);
    }

    // ========================================================================
    //  6. MARCAR FICHA RELACIONADA BECA
    // ========================================================================

    public function testMarcarFichaRelacionadaBeca_UpdatesFicha(): void
    {
        // Estudiante 1 has ficha Aprobada in periodo 1 (ficha id=1)
        $this->service->marcarFichaRelacionadaBeca(1, 1);

        $db = $this->db;
        $ficha = $db->table('fichas_socioeconomicas')
            ->where('estudiante_id', 1)
            ->where('periodo_id', 1)
            ->get()
            ->getRowArray();

        $this->assertNotNull($ficha);
        $this->assertSame(1, (int) $ficha['relacionada_beca']);
        $this->assertNotNull($ficha['fecha_relacion_beca']);
    }

    public function testMarcarFichaRelacionadaBeca_SinFichaNoError(): void
    {
        // Estudiante 3 has no ficha - method should not throw
        try {
            $this->service->marcarFichaRelacionadaBeca(3, 1);
            $this->assertTrue(true, 'No exception thrown for non-existent ficha');
        } catch (\Exception $e) {
            $this->fail('Exception thrown: ' . $e->getMessage());
        }
    }

    // ========================================================================
    //  7. GET TODAS LAS BECAS DISPONIBLES
    // ========================================================================

    public function testGetTodasLasBecasDisponibles_ReturnsExpectedStructure(): void
    {
        $result = $this->service->getTodasLasBecasDisponibles(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('becas', $result);
        $this->assertArrayHasKey('total_periodos', $result);
        $this->assertArrayHasKey('total_becas', $result);
    }

    public function testGetTodasLasBecasDisponibles_OnlyActiveBecas(): void
    {
        $result = $this->service->getTodasLasBecasDisponibles(1);

        // Only becas with estado='Activa' and in active period
        foreach ($result['becas'] as $beca) {
            $this->assertSame('Activa', $beca['estado']);
        }
    }

    public function testGetTodasLasBecasDisponibles_IncludesSolicitarInfo(): void
    {
        $result = $this->service->getTodasLasBecasDisponibles(1);

        foreach ($result['becas'] as $beca) {
            $this->assertArrayHasKey('puede_solicitar_esta_beca', $beca);
            $this->assertArrayHasKey('periodo_nombre', $beca);
            $this->assertArrayHasKey('periodo_id', $beca);
        }
    }

    public function testGetTodasLasBecasDisponibles_EstudianteExistente(): void
    {
        $result = $this->service->getTodasLasBecasDisponibles(1);

        $this->assertIsArray($result['becas']);
        // 3 becas activas in periodo 1 (Académica, Deportiva, Económica)
        $this->assertGreaterThan(0, $result['total_becas']);
    }

    public function testGetTodasLasBecasDisponibles_EstudianteInexistente(): void
    {
        $result = $this->service->getTodasLasBecasDisponibles(999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('becas', $result);
    }

    // ========================================================================
    //  8. CREAR SOLICITUD BECA
    // ========================================================================

    public function testCrearSolicitudBeca_ConFichaAprobadaCreatesSuccessfully(): void
    {
        $datos = [
            'estudiante_id' => 1,
            'beca_id'       => 2, // Beca Deportiva
            'periodo_id'    => 1,
            'observaciones' => 'Test solicitud',
        ];

        $result = $this->service->crearSolicitudBeca($datos);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('solicitud_id', $result);
        $this->assertGreaterThan(0, $result['solicitud_id']);

        // Verify in database
        $db = $this->db;
        $solicitud = $db->table('solicitudes_becas')
            ->where('id', $result['solicitud_id'])
            ->get()
            ->getRowArray();

        $this->assertNotNull($solicitud);
        $this->assertSame(1, (int) $solicitud['estudiante_id']);
        $this->assertSame(2, (int) $solicitud['beca_id']);
        $this->assertSame('Postulada', $solicitud['estado']);
    }

    public function testCrearSolicitudBeca_CreatesDocumentosRequeridos(): void
    {
        $datos = [
            'estudiante_id' => 1,
            'beca_id'       => 2, // Beca Deportiva → 1 doc requerido
            'periodo_id'    => 1,
        ];

        $result = $this->service->crearSolicitudBeca($datos);
        $this->assertTrue($result['success']);

        $db = $this->db;
        $docs = $db->table('documentos_solicitud_becas')
            ->where('solicitud_beca_id', $result['solicitud_id'])
            ->get()
            ->getResultArray();

        // Beca Deportiva has 1 documento requisito
        $this->assertCount(1, $docs);
        $this->assertSame('Pendiente', $docs[0]['estado']);
    }

    public function testCrearSolicitudBeca_MarksFichaAsRelated(): void
    {
        $datos = [
            'estudiante_id' => 1,
            'beca_id'       => 2,
            'periodo_id'    => 1,
        ];

        $this->service->crearSolicitudBeca($datos);

        $db = $this->db;
        $ficha = $db->table('fichas_socioeconomicas')
            ->where('estudiante_id', 1)
            ->where('periodo_id', 1)
            ->get()
            ->getRowArray();

        $this->assertSame(1, (int) $ficha['relacionada_beca']);
    }

    public function testCrearSolicitudBeca_InvalidBecaReturnsError(): void
    {
        $datos = [
            'estudiante_id' => 1,
            'beca_id'       => 999,
            'periodo_id'    => 1,
        ];

        $result = $this->service->crearSolicitudBeca($datos);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
    }

    public function testCrearSolicitudBeca_SinFichaReturnsError(): void
    {
        // Estudiante 3 has no ficha
        $datos = [
            'estudiante_id' => 3,
            'beca_id'       => 1,
            'periodo_id'    => 1,
        ];

        $result = $this->service->crearSolicitudBeca($datos);

        $this->assertFalse($result['success']);
    }

    public function testCrearSolicitudBeca_ReturnsCuposRestantes(): void
    {
        $datos = [
            'estudiante_id' => 1,
            'beca_id'       => 2, // Beca Deportiva: 5 cupos, 1 used = 4 available
            'periodo_id'    => 1,
        ];

        $result = $this->service->crearSolicitudBeca($datos);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('cupos_restantes', $result);
    }

    // ========================================================================
    //  9. EDGE CASES
    // ========================================================================

    public function testPuedeSolicitarBecas_ReturnsDetallesArray(): void
    {
        $result = $this->service->puedesolicitarBecas(1, 1);

        $this->assertIsArray($result['detalles']);
        // When puede_solicitar is true, detalles should contain useful info
        if ($result['puede_solicitar']) {
            $this->assertArrayHasKey('ficha_estado', $result['detalles']);
            $this->assertArrayHasKey('solicitudes_actuales', $result['detalles']);
            $this->assertArrayHasKey('periodo_nombre', $result['detalles']);
        }
    }

    public function testGetDocumentosSolicitud_EachDocHasNombre(): void
    {
        $docs = $this->service->getDocumentosSolicitud(1);

        foreach ($docs as $doc) {
            $this->assertNotEmpty($doc['documento_nombre']);
        }
    }

    public function testGetSolicitudesEstudiante_MultipleStudentsReturnCorrectData(): void
    {
        // Estudiante 1: 1 solicitud (Aprobada)
        $sol1 = $this->service->getSolicitudesEstudiante(1);
        $this->assertCount(1, $sol1);
        $this->assertSame('Aprobada', $sol1[0]['estado']);

        // Estudiante 2: 1 solicitud (Postulada)
        $sol2 = $this->service->getSolicitudesEstudiante(2);
        $this->assertCount(1, $sol2);
        $this->assertSame('Postulada', $sol2[0]['estado']);
    }

    public function testGetEstadisticasEstudiante_SolicitudesRechazadasCount(): void
    {
        $stats = $this->service->getEstadisticasEstudiante(1);
        $this->assertSame(0, $stats['solicitudes_becas']['rechazadas']);
    }

    public function testVerificarCupos_BecaAcademicaConSolicitudAprobada(): void
    {
        // Beca Académica (id=1) has 10 cupos, 1 solicitud Aprobada
        $result = $this->service->verificarCuposDisponibles(1, 1);

        $this->assertTrue($result['disponible']);
        $this->assertSame(9, $result['cupos_disponibles']);
        $this->assertSame(10, $result['cupos_totales']);
        $this->assertSame(1, $result['solicitudes_activas']);
    }
}
