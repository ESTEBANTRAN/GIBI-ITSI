<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration that creates additional tables needed for Service-level testing.
 * 
 * Adds: logs, carreras, observaciones_fichas, fichas_becas_relacion,
 * estudiantes_habilitacion_becas, documentos_solicitud_becas.
 * 
 * Must run AFTER CreateRolesTables and CreateBecasTables.
 */
class CreateServiceTestTables extends Migration
{
    protected $DBGroup = 'tests';

    public function up(): void
    {
        $this->createCarreras();
        $this->createLogs();
        $this->createObservacionesFichas();
        $this->createFichasBecasRelacion();
        $this->createEstudiantesHabilitacionBecas();
        $this->createDocumentosSolicitudBecas();
    }

    private function createCarreras(): void
    {
        $table = $this->db->prefixTable('carreras');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"nombre\" VARCHAR(150) NOT NULL,
                \"codigo\" VARCHAR(20) NULL,
                \"descripcion\" TEXT NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activo',
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
    }

    private function createLogs(): void
    {
        $table = $this->db->prefixTable('logs');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"id_usuario\" INTEGER NULL,
                \"accion\" VARCHAR(100) NOT NULL,
                \"tabla\" VARCHAR(100) NULL,
                \"registro_id\" INTEGER NULL,
                \"datos\" TEXT NULL,
                \"ip_address\" VARCHAR(45) NULL,
                \"user_agent\" TEXT NULL,
                \"fecha_creacion\" DATETIME NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function createObservacionesFichas(): void
    {
        $table = $this->db->prefixTable('observaciones_fichas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"ficha_id\" INTEGER NOT NULL,
                \"admin_id\" INTEGER NULL,
                \"observacion\" TEXT NOT NULL,
                \"fecha_creacion\" DATETIME NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function createFichasBecasRelacion(): void
    {
        $table = $this->db->prefixTable('fichas_becas_relacion');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"ficha_id\" INTEGER NOT NULL,
                \"beca_id\" INTEGER NOT NULL,
                \"tipo_relacion\" VARCHAR(50) NOT NULL DEFAULT 'Asociada',
                \"fecha_creacion\" DATETIME NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function createEstudiantesHabilitacionBecas(): void
    {
        $table = $this->db->prefixTable('estudiantes_habilitacion_becas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"estudiante_id\" INTEGER NOT NULL,
                \"periodo_id\" INTEGER NOT NULL,
                \"puede_solicitar_becas\" INTEGER NOT NULL DEFAULT 0,
                \"ficha_completada\" INTEGER NOT NULL DEFAULT 0,
                \"fecha_habilitacion\" DATETIME NULL,
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
    }

    private function createDocumentosSolicitudBecas(): void
    {
        $table = $this->db->prefixTable('documentos_solicitud_becas');
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$table}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"solicitud_beca_id\" INTEGER NOT NULL,
                \"documento_requerido_id\" INTEGER NOT NULL,
                \"orden_revision\" INTEGER NOT NULL DEFAULT 1,
                \"nombre_archivo\" VARCHAR(255) NULL,
                \"ruta_archivo\" VARCHAR(500) NULL,
                \"estado\" VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
                \"fecha_subida\" DATETIME NULL,
                \"revisado_por\" INTEGER NULL,
                \"fecha_revision\" DATETIME NULL,
                \"observaciones\" TEXT NULL
            )
        ");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('documentos_solicitud_becas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('estudiantes_habilitacion_becas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('fichas_becas_relacion') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('observaciones_fichas') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('logs') . '"');
        $this->db->query('DROP TABLE IF EXISTS "' . $this->db->prefixTable('carreras') . '"');
    }
}
