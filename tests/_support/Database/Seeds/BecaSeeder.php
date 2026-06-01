<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BecaSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // === PERIODOS ACADÉMICOS ===
        $this->db->table('periodos_academicos')->insert([
            'nombre'      => '2025-2026 Semestre I',
            'descripcion' => 'Primer semestre del año académico 2025-2026',
            'fecha_inicio' => '2025-03-01',
            'fecha_fin'   => '2025-08-31',
            'estado'      => 'Activo',
            'activo'      => 1,
            'activo_fichas' => 1,
            'activo_becas'  => 1,
            'vigente_estudiantes' => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('periodos_academicos')->insert([
            'nombre'      => '2024-2025 Semestre II',
            'descripcion' => 'Segundo semestre del año académico 2024-2025 (cerrado)',
            'fecha_inicio' => '2024-09-01',
            'fecha_fin'   => '2025-02-28',
            'estado'      => 'Inactivo',
            'activo'      => 0,
            'activo_fichas' => 0,
            'activo_becas'  => 0,
            'vigente_estudiantes' => 0,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // === BECAS ===
        $this->db->table('becas')->insert([
            'nombre'          => 'Beca Académica Excelencia',
            'descripcion'     => 'Beca para estudiantes con alto rendimiento académico',
            'tipo_beca'       => 'Académica',
            'monto_beca'      => 500.00,
            'cupos_disponibles' => 10,
            'requisitos'      => json_encode(['Promedio >= 9.0', 'Matrícula activa', 'Carga académica completa']),
            'activa'          => 1,
            'periodo_vigente_id' => 1,
            'puntaje_minimo_requerido' => 85.00,
            'estado'          => 'Activa',
            'prioridad'       => 1,
            'fecha_creacion'  => $now,
            'creado_por'      => 1,
            'fecha_actualizacion' => $now,
            'actualizado_por' => 1,
        ]);

        $this->db->table('becas')->insert([
            'nombre'          => 'Beca Deportiva',
            'descripcion'     => 'Beca para estudiantes con logros deportivos destacados',
            'tipo_beca'       => 'Deportiva',
            'monto_beca'      => 300.00,
            'cupos_disponibles' => 5,
            'requisitos'      => json_encode(['Participar en equipo universitario', 'Promedio >= 7.0']),
            'activa'          => 1,
            'periodo_vigente_id' => 1,
            'puntaje_minimo_requerido' => 70.00,
            'estado'          => 'Activa',
            'prioridad'       => 2,
            'fecha_creacion'  => $now,
            'creado_por'      => 1,
            'fecha_actualizacion' => $now,
            'actualizado_por' => 1,
        ]);

        $this->db->table('becas')->insert([
            'nombre'          => 'Beca Cultural (Inactiva)',
            'descripcion'     => 'Beca para actividades culturales',
            'tipo_beca'       => 'Cultural',
            'monto_beca'      => 200.00,
            'cupos_disponibles' => 0,
            'requisitos'      => json_encode(['Portafolio artístico']),
            'activa'          => 0,
            'periodo_vigente_id' => 2,
            'estado'          => 'Inactiva',
            'prioridad'       => 3,
            'fecha_creacion'  => $now,
            'creado_por'      => 1,
            'fecha_actualizacion' => $now,
            'actualizado_por' => 1,
        ]);

        $this->db->table('becas')->insert([
            'nombre'          => 'Beca Económica',
            'descripcion'     => 'Beca por situación económica',
            'tipo_beca'       => 'Económica',
            'monto_beca'      => 400.00,
            'cupos_disponibles' => null,
            'requisitos'      => json_encode(['Estudio socioeconómico', 'Promedio >= 6.0']),
            'activa'          => 1,
            'periodo_vigente_id' => 1,
            'puntaje_minimo_requerido' => 60.00,
            'estado'          => 'Activa',
            'prioridad'       => 1,
            'fecha_creacion'  => $now,
            'creado_por'      => 1,
            'fecha_actualizacion' => $now,
            'actualizado_por' => 1,
        ]);

        // === DOCUMENTOS REQUISITOS ===
        $this->db->table('becas_documentos_requisitos')->insert([
            'beca_id'            => 1,
            'nombre_documento'   => 'Certificado de notas',
            'descripcion'        => 'Certificado oficial de notas del último período',
            'tipo_documento'     => 'PDF',
            'obligatorio'        => 1,
            'orden_verificacion' => 1,
            'estado'             => 'Activo',
        ]);

        $this->db->table('becas_documentos_requisitos')->insert([
            'beca_id'            => 1,
            'nombre_documento'   => 'Carta de motivación',
            'descripcion'        => 'Carta explicando por qué merece la beca',
            'tipo_documento'     => 'PDF',
            'obligatorio'        => 1,
            'orden_verificacion' => 2,
            'estado'             => 'Activo',
        ]);

        $this->db->table('becas_documentos_requisitos')->insert([
            'beca_id'            => 2,
            'nombre_documento'   => 'Certificado deportivo',
            'descripcion'        => 'Certificado que avala logros deportivos',
            'tipo_documento'     => 'PDF',
            'obligatorio'        => 1,
            'orden_verificacion' => 1,
            'estado'             => 'Activo',
        ]);

        // === SOLICITUDES BECAS (para BecaModel::getBecasCompletas) ===
        $this->db->table('solicitudes_becas')->insert([
            'estudiante_id'       => 1,
            'beca_id'             => 1,
            'periodo_id'          => 1,
            'estado'              => 'Aprobada',
            'fecha_solicitud'     => $now,
            'puede_solicitar_beca' => 1,
        ]);
    }
}
