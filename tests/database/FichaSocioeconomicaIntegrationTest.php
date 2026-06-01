<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use Tests\Support\Database\Seeds\FichaSocioeconomicaSeeder;

/**
 * Tests de Integración: Fichas Socioeconómicas en Base de Datos
 * 
 * Verifica que FichaSocioeconomicaModel PeriodoAcademicoModel funcionen
 * correctamente con consultas de fichas, filtros por estado, y relaciones
 * con períodos académicos en SQLite en memoria.
 *
 * @internal
 */
final class FichaSocioeconomicaIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = 'Tests\Support\Database\Migrations\CreateBecasTables';
    protected $seed = FichaSocioeconomicaSeeder::class;

    // ========================================================================
    //  1. VERIFICACIÓN DE DATOS SEMBRADOS
    // ========================================================================

    public function testFichasTableHasExpectedData(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->findAll();
        $this->assertCount(4, $fichas, 'Deben existir 4 fichas socioeconómicas');
    }

    public function testPeriodosAcademicosSeeded(): void
    {
        $model = new PeriodoAcademicoModel();
        $periodos = $model->findAll();
        $this->assertCount(2, $periodos, 'Deben haber 2 períodos académicos');
    }

    // ========================================================================
    //  2. CONSULTAS POR ESTUDIANTE
    // ========================================================================

    public function testGetFichasPorEstudiante_ReturnsAllForStudent(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertCount(2, $model->where('estudiante_id', 1)->orderBy('fecha_creacion', 'DESC')->findAll());
        $this->assertCount(1, $model->where('estudiante_id', 2)->orderBy('fecha_creacion', 'DESC')->findAll());
        $this->assertCount(0, $model->where('estudiante_id', 3)->orderBy('fecha_creacion', 'DESC')->findAll());
        $this->assertCount(0, $model->where('estudiante_id', 999)->orderBy('fecha_creacion', 'DESC')->findAll());
    }

    public function testGetFichasPorEstudiante_OrderedByFechaDesc(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->where('estudiante_id', 1)->orderBy('fecha_creacion', 'DESC')->findAll();
        $this->assertCount(2, $fichas);
        for ($i = 0; $i < count($fichas) - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $fichas[$i + 1]['fecha_creacion'],
                $fichas[$i]['fecha_creacion']
            );
        }
    }

    // ========================================================================
    //  3. FICHAS CON PERÍODO (JOIN)
    // ========================================================================

    public function testGetFichasConPeriodo_IncludesPeriodName(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->getFichasConPeriodo(1);
        $this->assertCount(2, $fichas);
        foreach ($fichas as $ficha) {
            $this->assertArrayHasKey('nombre_periodo', $ficha);
            $this->assertNotEmpty($ficha['nombre_periodo']);
        }
    }

    public function testGetFichasConPeriodo_CorrectPeriodNames(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->getFichasConPeriodo(1);
        $nombres = array_column($fichas, 'nombre_periodo');
        $this->assertContains('2025-2026 Semestre I', $nombres);
        $this->assertContains('2024-2025 Semestre II', $nombres);
    }

    // ========================================================================
    //  4. FICHA COMPLETA (JOIN INDIVIDUAL CON PERÍODO)
    // ========================================================================

    public function testGetFichaCompleta_ReturnsWithPeriod(): void
    {
        $model = new FichaSocioeconomicaModel();
        $ficha = $model->getFichaCompleta(1, 1);
        $this->assertNotNull($ficha);
        $this->assertArrayHasKey('nombre_periodo', $ficha);
        $this->assertSame('2025-2026 Semestre I', $ficha['nombre_periodo']);
        $this->assertSame('Aprobada', $ficha['estado']);
    }

    public function testGetFichaCompleta_WrongStudentReturnsNull(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertNull($model->getFichaCompleta(1, 2));
    }

    public function testGetFichaCompleta_NonExistentReturnsNull(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertNull($model->getFichaCompleta(999, 1));
    }

    // ========================================================================
    //  5. VERIFICAR FICHA EXISTENTE
    // ========================================================================

    public function testVerificarFichaExistente_ReturnsExisting(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertNotNull($model->where('estudiante_id', 1)->where('periodo_id', 1)->first());
        $this->assertNotNull($model->where('estudiante_id', 1)->where('periodo_id', 2)->first());
        $this->assertSame('Aprobada', $model->where('estudiante_id', 1)->where('periodo_id', 1)->first()['estado']);
    }

    public function testVerificarFichaExistente_ReturnsNullWhenNotExists(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertNull($model->where('estudiante_id', 3)->where('periodo_id', 1)->first());
        $this->assertNull($model->where('estudiante_id', 1)->where('periodo_id', 999)->first());
    }

    // ========================================================================
    //  6. FICHAS APROBADAS
    // ========================================================================

    public function testGetFichasAprobadas_ReturnsOnlyApproved(): void
    {
        $model = new FichaSocioeconomicaModel();
        $aprobadas = $model->where('estudiante_id', 1)->where('estado', 'Aprobada')->findAll();
        $this->assertCount(1, $aprobadas);
        $this->assertSame('Aprobada', $aprobadas[0]['estado']);
        $this->assertCount(0, $model->where('estudiante_id', 2)->where('estado', 'Aprobada')->findAll());
    }

    // ========================================================================
    //  7. FICHAS PENDIENTES (BORRADOR O ENVIADA)
    // ========================================================================

    public function testGetFichasPendientes_ReturnsDraftOrSent(): void
    {
        $model = new FichaSocioeconomicaModel();
        $pendientes = $model->where('estudiante_id', 1)->whereIn('estado', ['Borrador', 'Enviada'])->findAll();
        $this->assertCount(1, $pendientes);
        $this->assertSame('Borrador', $pendientes[0]['estado']);

        $pendientes2 = $model->where('estudiante_id', 2)->whereIn('estado', ['Borrador', 'Enviada'])->findAll();
        $this->assertCount(1, $pendientes2);
        $this->assertSame('Enviada', $pendientes2[0]['estado']);
    }

    // ========================================================================
    //  8. FICHAS PARA ADMIN (VERIFICACIÓN DIRECTA EN BD)
    // ========================================================================

    public function testAllFichasHaveEstudianteIds(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->findAll();
        $this->assertCount(4, $fichas);

        $estudianteIds = array_map(fn($f) => (int) $f['estudiante_id'], $fichas);
        $this->assertContains(1, $estudianteIds);
        $this->assertContains(2, $estudianteIds);
        $this->assertContains(4, $estudianteIds);
    }

    // ========================================================================
    //  9. CRUD: CREAR FICHA
    // ========================================================================

    public function testCreateFicha(): void
    {
        $model = new FichaSocioeconomicaModel();
        $data = [
            'estudiante_id' => 3,
            'periodo_id'    => 1,
            'json_data'     => json_encode(['ingresos_mensuales' => 300, 'miembros_hogar' => 6]),
            'estado'        => 'Borrador',
        ];
        $id = $model->insert($data);
        $this->assertNotNull($id);
        $this->assertIsInt($id);

        $creada = $model->find($id);
        $this->assertNotNull($creada);
        $this->assertSame(3, (int) $creada['estudiante_id']);
        $this->assertSame('Borrador', $creada['estado']);

        $jsonData = json_decode($creada['json_data'], true);
        $this->assertNotNull($jsonData);
        $this->assertSame(300, $jsonData['ingresos_mensuales']);

        $this->assertCount(1, $model->where('estudiante_id', 3)->orderBy('fecha_creacion', 'DESC')->findAll());
    }

    // ========================================================================
    //  10. CRUD: ACTUALIZAR FICHA
    // ========================================================================

    public function testUpdateFichaEstado(): void
    {
        $model = new FichaSocioeconomicaModel();
        $model->update(2, ['estado' => 'Enviada']);
        $this->assertSame('Enviada', $model->find(2)['estado']);
        $model->update(2, ['estado' => 'Borrador']);
    }

    // ========================================================================
    //  11. ESTADOS DE FICHA
    // ========================================================================

    public function testAllFichaStatesPresent(): void
    {
        $model = new FichaSocioeconomicaModel();
        $fichas = $model->findAll();
        $estados = array_column($fichas, 'estado');
        $this->assertContains('Aprobada', $estados);
        $this->assertContains('Borrador', $estados);
        $this->assertContains('Enviada', $estados);
        $this->assertContains('Rechazada', $estados);
    }

    public function testFichaAprobadaHasFechaRevision(): void
    {
        $model = new FichaSocioeconomicaModel();
        $ficha = $model->find(1);
        $this->assertNotNull($ficha['fecha_revision']);
        $this->assertNotEmpty($ficha['observaciones_admin']);
    }

    public function testFichaRechazadaHasObservaciones(): void
    {
        $model = new FichaSocioeconomicaModel();
        $ficha = $model->find(4);
        $this->assertSame('Rechazada', $ficha['estado']);
        $this->assertNotEmpty($ficha['observaciones_admin']);
    }

    public function testFichaBorradorHasNoRevision(): void
    {
        $model = new FichaSocioeconomicaModel();
        $ficha = $model->find(2);
        $this->assertNull($ficha['fecha_revision']);
        $this->assertNull($ficha['observaciones_admin']);
    }

    // ========================================================================
    //  12. DATOS JSON
    // ========================================================================

    public function testJsonDataIsStoredAndRetrievedCorrectly(): void
    {
        $model = new FichaSocioeconomicaModel();
        $ficha = $model->find(1);
        $this->assertNotNull($ficha['json_data']);
        $jsonData = json_decode($ficha['json_data'], true);
        $this->assertNotNull($jsonData);
        $this->assertArrayHasKey('ingresos_mensuales', $jsonData);
        $this->assertArrayHasKey('miembros_hogar', $jsonData);
        $this->assertSame(400, $jsonData['ingresos_mensuales']);
        $this->assertSame(5, $jsonData['miembros_hogar']);
        $this->assertFalse($jsonData['trabaja']);
    }

    public function testJsonDataHasDifferentShapes(): void
    {
        $model = new FichaSocioeconomicaModel();
        $data1 = json_decode($model->find(1)['json_data'], true);
        $data2 = json_decode($model->find(2)['json_data'], true);
        $this->assertArrayHasKey('gastos_vivienda', $data1);
        $this->assertArrayHasKey('hermanos_universidad', $data1);
        $this->assertArrayNotHasKey('gastos_vivienda', $data2);
    }

    // ========================================================================
    //  13. PUNTAJE CALCULADO (verificación directa en BD)
    // ========================================================================

    public function testPuntajeCalculadoInApprovedFicha(): void
    {
        $db = \Config\Database::connect('tests');
        $ficha = $db->table('fichas_socioeconomicas')->where('id', 1)->get()->getRowArray();
        $this->assertNotNull($ficha['puntaje_calculado']);
        $this->assertEqualsWithDelta(85.50, (float) $ficha['puntaje_calculado'], 0.01);
    }

    public function testPuntajeCalculadoNullInBorrador(): void
    {
        $db = \Config\Database::connect('tests');
        $ficha = $db->table('fichas_socioeconomicas')->where('id', 2)->get()->getRowArray();
        $this->assertNull($ficha['puntaje_calculado']);
    }

    // ========================================================================
    //  14. RELACIÓN CON BECA
    // ========================================================================

    public function testRelacionadaBeca_OnlyApprovedFicha(): void
    {
        $model = new FichaSocioeconomicaModel();
        $this->assertSame(1, (int) $model->find(1)['relacionada_beca']);
        $this->assertSame(0, (int) $model->find(2)['relacionada_beca']);
    }

    // ========================================================================
    //  15. PERÍODOS ACADÉMICOS
    // ========================================================================

    public function testGetPeriodoActivoReal_ReturnsCorrectPeriod(): void
    {
        $model = new PeriodoAcademicoModel();
        $periodo = $model->where('vigente_estudiantes', 1)->where('activo', 1)->where('estado', 'Activo')->first();
        $this->assertNotNull($periodo);
        $this->assertSame(1, (int) $periodo['id']);
        $this->assertSame(1, (int) $periodo['vigente_estudiantes']);
    }

    public function testPeriodoActivoFichasDefaults(): void
    {
        $model = new PeriodoAcademicoModel();
        $periodo1 = $model->find(1);
        $this->assertNotNull($periodo1);
        // Migration defaults activo_fichas to 1
        $this->assertSame(1, (int) $periodo1['activo_fichas']);
        $this->assertSame(1, (int) $periodo1['vigente_estudiantes']);
        $periodo2 = $model->find(2);
        $this->assertNotNull($periodo2);
        // Period 2 also defaults activo_fichas to 1 (migration default, seeder doesn't override)
        $this->assertSame(1, (int) $periodo2['activo_fichas']);
        $this->assertNull($model->find(999));
    }

    public function testActualizarContadorFichas(): void
    {
        $model = new PeriodoAcademicoModel();
        $model->actualizarContadorFichas(1, 5);
        $periodo = $model->find(1);
        $this->assertSame(5, (int) $periodo['fichas_creadas']);
        $model->update(1, ['fichas_creadas' => 0]);
    }
}
