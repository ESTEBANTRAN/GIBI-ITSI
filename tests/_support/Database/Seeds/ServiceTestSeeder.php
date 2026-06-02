<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder de soporte para tests de Services.
 *
 * Inserta datos adicionales necesarios para AdminBienestarService
 * y EstudianteBecasService: carreras, logs, documentos de solicitud,
 * y actualiza usuarios con carrera_id.
 *
 * Debe ejecutarse DESPUÉS de RoleSeeder, BecaSeeder y FichaSocioeconomicaSeeder.
 */
class ServiceTestSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // === CARRERAS ===
        $this->db->table('carreras')->insert([
            'id'         => 1,
            'nombre'     => 'Ingeniería en Sistemas',
            'codigo'     => 'IS',
            'descripcion' => 'Carrera de Ingeniería en Sistemas Computacionales',
            'estado'     => 'Activo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->db->table('carreras')->insert([
            'id'         => 2,
            'nombre'     => 'Administración',
            'codigo'     => 'AD',
            'descripcion' => 'Carrera de Administración de Empresas',
            'estado'     => 'Activo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // === ACTUALIZAR USUARIOS CON CARRERA_ID ===
        // Juan Pérez (id=1) → Ingeniería en Sistemas (carrera_id=1)
        $this->db->table('usuarios')->where('id', 1)->update(['carrera_id' => 1]);
        // María Gómez (id=2) → Administración (carrera_id=2)
        $this->db->table('usuarios')->where('id', 2)->update(['carrera_id' => 2]);
        // Carlos Mendoza (id=4) → Ingeniería en Sistemas (carrera_id=1)
        $this->db->table('usuarios')->where('id', 4)->update(['carrera_id' => 1]);

        // === SOLICITUDES DE BECAS ADICIONALES ===
        // Solicitud pendiente para testing de alertas
        $this->db->table('solicitudes_becas')->insert([
            'estudiante_id'       => 2,
            'beca_id'             => 2,
            'periodo_id'          => 1,
            'estado'              => 'Postulada',
            'fecha_solicitud'     => $now,
            'puede_solicitar_beca' => 1,
        ]);

        // === LOGS ===
        $this->db->table('logs')->insert([
            'id_usuario'    => 4,
            'accion'        => 'aprobar_solicitud_beca',
            'tabla'         => 'solicitudes_becas',
            'registro_id'   => 1,
            'datos'         => json_encode(['estado_anterior' => 'Pendiente', 'estado_nuevo' => 'Aprobada']),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        ]);

        $this->db->table('logs')->insert([
            'id_usuario'    => 4,
            'accion'        => 'crear_beca',
            'tabla'         => 'becas',
            'registro_id'   => 3,
            'datos'         => json_encode(['nombre' => 'Beca Cultural (Inactiva)']),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-5 days')),
        ]);

        $this->db->table('logs')->insert([
            'id_usuario'    => 1,
            'accion'        => 'crear_solicitud_beca',
            'tabla'         => 'solicitudes_becas',
            'registro_id'   => 1,
            'datos'         => json_encode(['beca_id' => 1]),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-10 days')),
        ]);

        // === DOCUMENTOS SOLICITUD BECAS ===
        // Documentos para la solicitud 1 (beca académica → 2 docs requeridos)
        $this->db->table('documentos_solicitud_becas')->insert([
            'solicitud_beca_id'      => 1,
            'documento_requerido_id' => 1,
            'orden_revision'         => 1,
            'nombre_archivo'         => 'certificado_notas.pdf',
            'ruta_archivo'           => '/uploads/solicitudes/1/certificado_notas.pdf',
            'estado'                 => 'Aprobado',
            'fecha_subida'           => date('Y-m-d H:i:s', strtotime('-18 days')),
            'revisado_por'           => 4,
            'fecha_revision'         => date('Y-m-d H:i:s', strtotime('-15 days')),
        ]);

        $this->db->table('documentos_solicitud_becas')->insert([
            'solicitud_beca_id'      => 1,
            'documento_requerido_id' => 2,
            'orden_revision'         => 2,
            'nombre_archivo'         => 'carta_motivacion.pdf',
            'ruta_archivo'           => '/uploads/solicitudes/1/carta_motivacion.pdf',
            'estado'                 => 'Aprobado',
            'fecha_subida'           => date('Y-m-d H:i:s', strtotime('-18 days')),
            'revisado_por'           => 4,
            'fecha_revision'         => date('Y-m-d H:i:s', strtotime('-15 days')),
        ]);

        // Documento para la solicitud 2 (beca deportiva → 1 doc requerido)
        $this->db->table('documentos_solicitud_becas')->insert([
            'solicitud_beca_id'      => 2,
            'documento_requerido_id' => 3,
            'orden_revision'         => 1,
            'nombre_archivo'         => 'certificado_deportivo.pdf',
            'ruta_archivo'           => '/uploads/solicitudes/2/certificado_deportivo.pdf',
            'estado'                 => 'Pendiente',
            'fecha_subida'           => null,
        ]);
    }
}
