<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FichaSocioeconomicaSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // === PERIODOS ACADÉMICOS (si no existen ya) ===
        // Se verifica si ya hay registros antes de insertar
        $count = $this->db->table('periodos_academicos')->countAll();
        if ($count === 0) {
            $this->db->table('periodos_academicos')->insert([
                'nombre'      => '2025-2026 Semestre I',
                'descripcion' => 'Período activo para fichas',
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
                'descripcion' => 'Período anterior',
                'fecha_inicio' => '2024-09-01',
                'fecha_fin'   => '2025-02-28',
                'estado'      => 'Inactivo',
                'activo'      => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // === FICHAS SOCIOECONÓMICAS ===
        // Estudiante 1: 2 fichas (una aprobada, una en borrador)
        $this->db->table('fichas_socioeconomicas')->insert([
            'estudiante_id'  => 1,
            'periodo_id'     => 1,
            'json_data'      => json_encode([
                'ingresos_mensuales' => 400,
                'miembros_hogar'     => 5,
                'trabaja'            => false,
                'gastos_vivienda'    => 150,
                'hermanos_universidad' => 1,
            ]),
            'estado'         => 'Aprobada',
            'puntaje_calculado' => 85.50,
            'relacionada_beca'  => 1,
            'fecha_envio'    => date('Y-m-d H:i:s', strtotime('-20 days')),
            'fecha_revision' => date('Y-m-d H:i:s', strtotime('-15 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-25 days')),
            'observaciones_admin' => 'Ficha completa. Cumple requisitos.',
            'revisada_por_admin'  => 1,
        ]);

        $this->db->table('fichas_socioeconomicas')->insert([
            'estudiante_id'  => 1,
            'periodo_id'     => 2,
            'json_data'      => json_encode([
                'ingresos_mensuales' => 450,
                'miembros_hogar'     => 4,
                'trabaja'            => true,
            ]),
            'estado'         => 'Borrador',
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-5 days')),
        ]);

        // Estudiante 2: 1 ficha enviada (pendiente de revisión)
        $this->db->table('fichas_socioeconomicas')->insert([
            'estudiante_id'  => 2,
            'periodo_id'     => 1,
            'json_data'      => json_encode([
                'ingresos_mensuales' => 600,
                'miembros_hogar'     => 3,
                'trabaja'            => false,
            ]),
            'estado'         => 'Enviada',
            'fecha_envio'    => date('Y-m-d H:i:s', strtotime('-2 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-7 days')),
        ]);

        // Estudiante 4 (admin): 1 ficha rechazada
        $this->db->table('fichas_socioeconomicas')->insert([
            'estudiante_id'  => 4,
            'periodo_id'     => 1,
            'json_data'      => json_encode([
                'ingresos_mensuales' => 1200,
                'miembros_hogar'     => 2,
                'trabaja'            => true,
            ]),
            'estado'         => 'Rechazada',
            'observaciones_admin' => 'Ingresos superan el umbral establecido.',
            'revisada_por_admin'  => 1,
            'fecha_envio'    => date('Y-m-d H:i:s', strtotime('-40 days')),
            'fecha_revision' => date('Y-m-d H:i:s', strtotime('-38 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-45 days')),
            'puntaje_calculado' => 30.00,
        ]);
    }
}
