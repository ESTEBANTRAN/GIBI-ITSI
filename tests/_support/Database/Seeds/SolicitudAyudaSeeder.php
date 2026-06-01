<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SolicitudAyudaSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // === CATEGORÍAS ===
        $this->db->table('categorias_solicitud_ayuda')->insert([
            'nombre'      => 'Problemas Académicos',
            'descripcion' => 'Dificultades con materias, horarios o profesorado',
            'color'       => '#dc3545',
            'icono'       => 'bi-book',
            'activo'      => 1,
            'orden'       => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('categorias_solicitud_ayuda')->insert([
            'nombre'      => 'Problemas Económicos',
            'descripcion' => 'Becas, ayudas financieras, pagos',
            'color'       => '#28a745',
            'icono'       => 'bi-currency-dollar',
            'activo'      => 1,
            'orden'       => 2,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('categorias_solicitud_ayuda')->insert([
            'nombre'      => 'Problemas Psicológicos',
            'descripcion' => 'Apoyo psicológico y bienestar emocional',
            'color'       => '#ffc107',
            'icono'       => 'bi-heart',
            'activo'      => 0,
            'orden'       => 3,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // === SOLICITUDES DE AYUDA ===
        // Estudiante 1 tiene 3 solicitudes
        $this->db->table('solicitudes_ayuda')->insert([
            'id_estudiante'  => 1,
            'asunto'         => 'Problema con cálculo diferencial',
            'categoria_id'   => 1,
            'descripcion'    => 'No entiendo los límites y necesito tutoría',
            'prioridad'      => 'Alta',
            'estado'         => 'Pendiente',
            'fecha_solicitud' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-5 days')),
        ]);

        $this->db->table('solicitudes_ayuda')->insert([
            'id_estudiante'  => 1,
            'asunto'         => 'Solicitud de beca alimenticia',
            'categoria_id'   => 2,
            'descripcion'    => 'Necesito apoyo para comedor universitario',
            'prioridad'      => 'Urgente',
            'estado'         => 'Resuelta',
            'fecha_solicitud' => date('Y-m-d H:i:s', strtotime('-15 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-15 days')),
            'fecha_respuesta' => $now,
        ]);

        $this->db->table('solicitudes_ayuda')->insert([
            'id_estudiante'  => 1,
            'asunto'         => 'Apoyo emocional',
            'categoria_id'   => 3,
            'descripcion'    => 'Estoy pasando por una situación difícil',
            'prioridad'      => 'Media',
            'estado'         => 'Pendiente',
            'fecha_solicitud' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        // Estudiante 2 tiene 1 solicitud resuelta
        $this->db->table('solicitudes_ayuda')->insert([
            'id_estudiante'  => 2,
            'asunto'         => 'Problema con horario',
            'categoria_id'   => 1,
            'descripcion'    => 'Tengo un conflicto de horarios entre dos materias',
            'prioridad'      => 'Baja',
            'estado'         => 'Resuelta',
            'fecha_solicitud' => date('Y-m-d H:i:s', strtotime('-30 days')),
            'fecha_creacion' => date('Y-m-d H:i:s', strtotime('-30 days')),
            'fecha_respuesta' => date('Y-m-d H:i:s', strtotime('-25 days')),
        ]);

        // Estudiante 3 (Pedro - inactivo) tiene 1 solicitud
        $this->db->table('solicitudes_ayuda')->insert([
            'id_estudiante'  => 3,
            'asunto'         => 'Problema de matrícula',
            'categoria_id'   => 1,
            'descripcion'    => 'No puedo matricularme en el semestre',
            'prioridad'      => 'Alta',
            'estado'         => 'Pendiente',
            'fecha_solicitud' => $now,
            'fecha_creacion' => $now,
        ]);

        // === RESPUESTAS ===
        $this->db->table('respuestas_solicitudes_ayuda')->insert([
            'solicitud_ayuda_id' => 2,
            'respuesta'          => 'Se ha asignado un cupo en el comedor universitario.',
            'fecha_respuesta'    => $now,
            'id_responsable'     => 4,
        ]);

        $this->db->table('respuestas_solicitudes_ayuda')->insert([
            'solicitud_ayuda_id' => 4,
            'respuesta'          => 'Se ha ajustado su horario. Contacte a secretaría.',
            'fecha_respuesta'    => date('Y-m-d H:i:s', strtotime('-25 days')),
            'id_responsable'     => 4,
        ]);
    }
}
