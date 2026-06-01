<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration que crea todas las tablas necesarias para tests de integración
 * de BecaModel, SolicitudAyudaModel y FichaSocioeconomicaModel.
 * 
 * Incluye: periodos_academicos, categorias_solicitud_ayuda, becas,
 * becas_documentos_requisitos, solicitudes_ayuda, respuestas_solicitudes_ayuda,
 * fichas_socioeconomicas, solicitudes_becas.
 */
class CreateBecasTables extends Migration
{
    protected $DBGroup = 'tests';

    public function up(): void
    {
        $this->createPeriodosAcademicos();
        $this->createCategoriasSolicitudAyuda();
        $this->createBecas();
        $this->createBecasDocumentosRequisitos();
        $this->createSolicitudesAyuda();
        $this->createRespuestasSolicitudesAyuda();
        $this->createFichasSocioeconomicas();
        $this->createSolicitudesBecas();
    }

    private function createPeriodosAcademicos(): void
    {
        $table = $this->db->prefixTable('periodos_academicos');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"nombre\" VARCHAR(50) NOT NULL,
                \"descripcion\" TEXT NULL,
                \"fecha_inicio\" DATE NULL,
                \"fecha_fin\" DATE NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activo',
                \"activo\" INTEGER NOT NULL DEFAULT 1,
                \"activo_fichas\" INTEGER NOT NULL DEFAULT 1,
                \"activo_becas\" INTEGER NOT NULL DEFAULT 1,
                \"vigente_estudiantes\" INTEGER NOT NULL DEFAULT 1,
                \"limite_fichas\" INTEGER NULL DEFAULT NULL,
                \"limite_becas\" INTEGER NULL DEFAULT NULL,
                \"fichas_creadas\" INTEGER NOT NULL DEFAULT 0,
                \"becas_asignadas\" INTEGER NOT NULL DEFAULT 0,
                \"created_by\" INTEGER NULL,
                \"updated_by\" INTEGER NULL,
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
    }

    private function createCategoriasSolicitudAyuda(): void
    {
        $table = $this->db->prefixTable('categorias_solicitud_ayuda');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"nombre\" VARCHAR(100) NOT NULL,
                \"descripcion\" TEXT NULL,
                \"color\" VARCHAR(7) NOT NULL DEFAULT '#007bff',
                \"icono\" VARCHAR(50) NOT NULL DEFAULT 'bi-question-circle',
                \"activo\" INTEGER NOT NULL DEFAULT 1,
                \"orden\" INTEGER NOT NULL DEFAULT 0,
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
    }

    private function createBecas(): void
    {
        $table = $this->db->prefixTable('becas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"nombre\" VARCHAR(255) NOT NULL,
                \"descripcion\" TEXT NULL,
                \"tipo_beca\" VARCHAR(50) NOT NULL DEFAULT 'Académica',
                \"monto_beca\" DECIMAL(10,2) NULL DEFAULT NULL,
                \"cupos_disponibles\" INTEGER NULL DEFAULT NULL,
                \"requisitos\" TEXT NULL,
                \"documentos_requisitos\" TEXT NULL,
                \"activa\" INTEGER NOT NULL DEFAULT 1,
                \"fecha_inicio_vigencia\" DATE NULL,
                \"fecha_fin_vigencia\" DATE NULL,
                \"periodo_vigente_id\" INTEGER NULL,
                \"puntaje_minimo_requerido\" DECIMAL(5,2) NULL DEFAULT NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activa',
                \"fecha_creacion\" DATETIME NULL,
                \"creado_por\" INTEGER NULL,
                \"fecha_actualizacion\" DATETIME NULL,
                \"actualizado_por\" INTEGER NULL,
                \"prioridad\" INTEGER NOT NULL DEFAULT 1
            )
        ");
    }

    private function createBecasDocumentosRequisitos(): void
    {
        $table = $this->db->prefixTable('becas_documentos_requisitos');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"beca_id\" INTEGER NOT NULL,
                \"nombre_documento\" VARCHAR(255) NOT NULL,
                \"descripcion\" TEXT NULL,
                \"tipo_documento\" VARCHAR(100) NOT NULL,
                \"obligatorio\" INTEGER NOT NULL DEFAULT 1,
                \"orden_verificacion\" INTEGER NOT NULL DEFAULT 1,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activo'
            )
        ");
    }

    private function createSolicitudesAyuda(): void
    {
        $table = $this->db->prefixTable('solicitudes_ayuda');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"id_estudiante\" INTEGER NOT NULL,
                \"asunto\" VARCHAR(255) NOT NULL,
                \"categoria_id\" INTEGER NULL,
                \"asunto_personalizado\" TEXT NULL,
                \"descripcion\" TEXT NOT NULL,
                \"comentarios_resolucion\" TEXT NULL,
                \"fecha_solicitud\" DATETIME NULL,
                \"estado\" VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
                \"prioridad\" VARCHAR(20) NOT NULL DEFAULT 'Media',
                \"fecha_actualizacion\" DATETIME NULL,
                \"id_responsable\" INTEGER NULL,
                \"fecha_creacion\" DATETIME NULL,
                \"fecha_respuesta\" DATETIME NULL
            )
        ");
    }

    private function createRespuestasSolicitudesAyuda(): void
    {
        $table = $this->db->prefixTable('respuestas_solicitudes_ayuda');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"solicitud_ayuda_id\" INTEGER NOT NULL,
                \"respuesta\" TEXT NOT NULL,
                \"fecha_respuesta\" DATETIME NULL,
                \"id_responsable\" INTEGER NULL
            )
        ");
    }

    private function createFichasSocioeconomicas(): void
    {
        $table = $this->db->prefixTable('fichas_socioeconomicas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"estudiante_id\" INTEGER NOT NULL,
                \"periodo_id\" INTEGER NOT NULL,
                \"json_data\" TEXT NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Borrador',
                \"revisada_por_admin\" INTEGER NOT NULL DEFAULT 0,
                \"fecha_revision_admin\" DATETIME NULL,
                \"observaciones_admin\" TEXT NULL,
                \"revisado_por\" INTEGER NULL,
                \"actualizado_por\" INTEGER NULL,
                \"puntaje_calculado\" DECIMAL(5,2) NULL DEFAULT NULL,
                \"relacionada_beca\" INTEGER NOT NULL DEFAULT 0,
                \"fecha_relacion_beca\" DATETIME NULL,
                \"fecha_envio\" DATETIME NULL,
                \"fecha_revision\" DATETIME NULL,
                \"fecha_creacion\" DATETIME NULL,
                \"fecha_actualizacion\" DATETIME NULL
            )
        ");
    }

    private function createSolicitudesBecas(): void
    {
        $table = $this->db->prefixTable('solicitudes_becas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"estudiante_id\" INTEGER NOT NULL,
                \"beca_id\" INTEGER NOT NULL,
                \"periodo_id\" INTEGER NOT NULL,
                \"estado\" VARCHAR(30) NOT NULL DEFAULT 'Postulada',
                \"observaciones\" TEXT NULL,
                \"fecha_solicitud\" DATETIME NULL,
                \"fecha_revision\" DATETIME NULL,
                \"revisado_por\" INTEGER NULL,
                \"motivo_rechazo\" TEXT NULL,
                \"documentos_revisados\" INTEGER NOT NULL DEFAULT 0,
                \"total_documentos\" INTEGER NOT NULL DEFAULT 0,
                \"documento_actual_revision\" INTEGER NULL DEFAULT 1,
                \"puede_solicitar_beca\" INTEGER NOT NULL DEFAULT 0,
                \"fecha_aprobacion\" DATETIME NULL,
                \"fecha_rechazo\" DATETIME NULL,
                \"porcentaje_avance\" DECIMAL(5,2) NULL DEFAULT 0.00,
                \"documento_actual_verificando\" INTEGER NULL,
                \"fecha_actualizacion\" DATETIME NULL,
                \"actualizado_por\" INTEGER NULL,
                \"observaciones_admin\" TEXT NULL,
                \"aprobado_por\" INTEGER NULL,
                \"rechazado_por\" INTEGER NULL
            )
        ");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('solicitudes_becas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('fichas_socioeconomicas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('respuestas_solicitudes_ayuda') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('solicitudes_ayuda') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('becas_documentos_requisitos') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('becas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('categorias_solicitud_ayuda') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('periodos_academicos') . '"');
    }
}
