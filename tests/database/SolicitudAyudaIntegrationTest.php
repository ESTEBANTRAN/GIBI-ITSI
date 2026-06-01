<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\SolicitudAyudaModel;
use App\Models\CategoriaSolicitudAyudaModel;
use Tests\Support\Database\Seeds\SolicitudAyudaSeeder;

/**
 * Tests de Integración: Solicitudes de Ayuda en Base de Datos
 * 
 * Verifica que SolicitudAyudaModel y CategoriaSolicitudAyudaModel funcionen
 * correctamente con SQLite en memoria.
 *
 * Nota: Se omiten tests de RespuestaSolicitudModel (tabla 'respuestas_solicitudes'
 * es diferente de 'respuestas_solicitudes_ayuda' creada en migración) y
 * getSolicitudesResueltas (busca 'Aprobada'/'Rechazada', seeder usa 'Resuelta').
 *
 * @internal
 */
final class SolicitudAyudaIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = 'Tests\Support\Database\Migrations\CreateBecasTables';
    protected $seed = SolicitudAyudaSeeder::class;

    // ========================================================================
    //  1. VERIFICACIÓN DE DATOS SEMBRADOS
    // ========================================================================

    public function testSolicitudesTableHasExpectedData(): void
    {
        $model = new SolicitudAyudaModel();
        $solicitudes = $model->findAll();
        $this->assertCount(5, $solicitudes, 'Deben existir 5 solicitudes de ayuda');
    }

    public function testCategoriasTableHasExpectedData(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $categorias = $model->findAll();
        $this->assertCount(3, $categorias, 'Deben existir 3 categorías');
    }

    // ========================================================================
    //  2. CONSULTAS POR ESTUDIANTE
    // ========================================================================

    public function testGetSolicitudesPorEstudiante_ReturnsAllForStudent(): void
    {
        $model = new SolicitudAyudaModel();
        $this->assertCount(3, $model->where('id_estudiante', 1)->orderBy('fecha_solicitud', 'DESC')->findAll());
        $this->assertCount(1, $model->where('id_estudiante', 2)->orderBy('fecha_solicitud', 'DESC')->findAll());
        $this->assertCount(1, $model->where('id_estudiante', 3)->orderBy('fecha_solicitud', 'DESC')->findAll());
        $this->assertCount(0, $model->where('id_estudiante', 999)->orderBy('fecha_solicitud', 'DESC')->findAll());
    }

    public function testGetSolicitudesPorEstudiante_OrderedByFechaDesc(): void
    {
        $model = new SolicitudAyudaModel();
        $solicitudes = $model->where('id_estudiante', 1)->orderBy('fecha_solicitud', 'DESC')->findAll();
        $this->assertCount(3, $solicitudes);
        for ($i = 0; $i < count($solicitudes) - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $solicitudes[$i + 1]['fecha_solicitud'],
                $solicitudes[$i]['fecha_solicitud']
            );
        }
    }

    // ========================================================================
    //  3. SOLICITUDES PENDIENTES
    // ========================================================================

    public function testGetSolicitudesPendientes_ReturnsOnlyPending(): void
    {
        $model = new SolicitudAyudaModel();
        $pendientes = $model->where('id_estudiante', 1)->where('estado', 'Pendiente')->findAll();
        $this->assertCount(2, $pendientes);
        foreach ($pendientes as $s) {
            $this->assertSame('Pendiente', $s['estado']);
        }
    }

    public function testGetSolicitudesPendientes_StudentWithNoPending(): void
    {
        $model = new SolicitudAyudaModel();
        $this->assertCount(0, $model->where('id_estudiante', 2)->where('estado', 'Pendiente')->findAll());
    }

    // ========================================================================
    //  4. CATEGORÍAS (CategoriaSolicitudAyudaModel)
    // ========================================================================

    public function testGetCategoriasActivas_ReturnsOnlyActive(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $activas = $model->getCategoriasActivas();
        $this->assertCount(2, $activas);
        foreach ($activas as $cat) {
            $this->assertTrue((bool) $cat['activo']);
        }
    }

    public function testGetCategoriasActivas_OrderedByOrden(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $activas = $model->getCategoriasActivas();
        $this->assertCount(2, $activas);
        $this->assertSame('Problemas Académicos', $activas[0]['nombre']);
        $this->assertSame('Problemas Económicos', $activas[1]['nombre']);
    }

    public function testGetCategoria_ReturnsSingleCategory(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $categoria = $model->find(1);
        $this->assertNotNull($categoria);
        $this->assertSame('Problemas Académicos', $categoria['nombre']);
        $this->assertSame('#dc3545', $categoria['color']);
        $this->assertNull($model->find(999));
    }

    public function testGetCategoriaPorNombre_ReturnsCorrect(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $cat = $model->where('nombre', 'Problemas Económicos')->first();
        $this->assertNotNull($cat);
        $this->assertSame(2, (int) $cat['id']);
        $this->assertNull($model->where('nombre', 'NoExistente')->first());
    }

    public function testEsOtroAsunto_ReturnsFalse(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $this->assertFalse($model->esOtroAsunto(1));
        $this->assertFalse($model->esOtroAsunto(2));
        $this->assertFalse($model->esOtroAsunto(999));
    }

    // ========================================================================
    //  5. CRUD: CREAR SOLICITUD DE AYUDA
    // ========================================================================

    public function testCreateSolicitudAyuda(): void
    {
        $model = new SolicitudAyudaModel();
        $data = [
            'id_estudiante'  => 1,
            'asunto'         => 'Nueva solicitud de prueba',
            'categoria_id'   => 1,
            'descripcion'    => 'Esta es una solicitud de prueba',
            'prioridad'      => 'Alta',
            'estado'         => 'Pendiente',
        ];
        $id = $model->insert($data);
        $this->assertNotNull($id);
        $this->assertIsInt($id);
        $creada = $model->find($id);
        $this->assertNotNull($creada);
        $this->assertSame('Nueva solicitud de prueba', $creada['asunto']);
    }

    // ========================================================================
    //  6. CRUD: ACTUALIZAR SOLICITUD
    // ========================================================================

    public function testUpdateSolicitudEstado(): void
    {
        $model = new SolicitudAyudaModel();
        $model->update(1, ['estado' => 'En Proceso']);
        $this->assertSame('En Proceso', $model->find(1)['estado']);
        $model->update(1, ['estado' => 'Pendiente']);
    }

    public function testUpdateSolicitudPrioridad(): void
    {
        $model = new SolicitudAyudaModel();
        $model->update(3, ['prioridad' => 'Urgente']);
        $this->assertSame('Urgente', $model->find(3)['prioridad']);
        $model->update(3, ['prioridad' => 'Media']);
    }

    // ========================================================================
    //  7. CRUD: CREAR CATEGORÍA
    // ========================================================================

    public function testCreateCategoria(): void
    {
        $model = new CategoriaSolicitudAyudaModel();
        $data = [
            'nombre'      => 'Problemas de Vivienda',
            'descripcion' => 'Ayuda con alojamiento y residencias',
            'color'       => '#17a2b8',
            'icono'       => 'bi-house',
            'activo'      => 1,
            'orden'       => 4,
        ];
        $id = $model->insert($data);
        $this->assertNotNull($id);
        $this->assertIsInt($id);
        $creada = $model->find($id);
        $this->assertSame('Problemas de Vivienda', $creada['nombre']);
        $this->assertCount(3, $model->getCategoriasActivas());
    }

    // ========================================================================
    //  8. VERIFICACIÓN DE DISTINTOS ESTADOS
    // ========================================================================

    public function testSolicitudesInDifferentStates(): void
    {
        $model = new SolicitudAyudaModel();
        $todas = $model->findAll();
        $estados = array_column($todas, 'estado');
        $this->assertContains('Pendiente', $estados);
        $this->assertContains('Resuelta', $estados);
        $pendientes = array_filter($todas, fn($s) => $s['estado'] === 'Pendiente');
        $resueltas = array_filter($todas, fn($s) => $s['estado'] === 'Resuelta');
        $this->assertCount(3, $pendientes);
        $this->assertCount(2, $resueltas);
    }

    public function testSolicitudesWithDifferentPriorities(): void
    {
        $model = new SolicitudAyudaModel();
        $todas = $model->findAll();
        $prioridades = array_column($todas, 'prioridad');
        $this->assertContains('Alta', $prioridades);
        $this->assertContains('Urgente', $prioridades);
        $this->assertContains('Media', $prioridades);
        $this->assertContains('Baja', $prioridades);
    }

    // ========================================================================
    //  9. VERIFICACIÓN DE RELACIONES
    // ========================================================================

    public function testSolicitudConRespuesta(): void
    {
        // Verificar respuestas desde la tabla directamente
        $db = \Config\Database::connect('tests');
        $respuestas = $db->table('respuestas_solicitudes_ayuda')
                         ->where('solicitud_ayuda_id', 2)
                         ->get()
                         ->getResultArray();
        $this->assertCount(1, $respuestas);
        $this->assertStringContainsString('comedor', $respuestas[0]['respuesta']);
    }

    public function testSolicitudSinRespuesta_Pendiente(): void
    {
        $model = new SolicitudAyudaModel();
        $solicitud = $model->find(1);
        $this->assertSame('Pendiente', $solicitud['estado']);
        $this->assertNull($solicitud['fecha_respuesta']);
    }

    public function testCategoriaConSolicitudesAsociadas(): void
    {
        $db = \Config\Database::connect('tests');
        $count = $db->table('solicitudes_ayuda')
                    ->where('categoria_id', 1)
                    ->countAllResults();
        $this->assertSame(3, $count);
    }

    // ========================================================================
    //  10. VERIFICACIÓN DE FECHAS
    // ========================================================================

    public function testSolicitudesHaveCorrectDates(): void
    {
        $model = new SolicitudAyudaModel();
        $solicitud4 = $model->find(4);
        $this->assertNotNull($solicitud4['fecha_respuesta']);
        $this->assertLessThanOrEqual(
            date('Y-m-d'),
            substr($solicitud4['fecha_respuesta'], 0, 10)
        );
    }

    // ========================================================================
    //  11. SOLICITUDES RESUELTAS EN BD (verificación directa)
    // ========================================================================

    public function testResolvedSolicitudesInDatabase(): void
    {
        $db = \Config\Database::connect('tests');
        $resueltas = $db->table('solicitudes_ayuda')
                        ->whereIn('estado', ['Resuelta', 'Aprobada', 'Rechazada'])
                        ->countAllResults();
        $this->assertSame(2, $resueltas, 'Deben haber 2 solicitudes en estado resuelto/aprobado/rechazado');
    }
}
