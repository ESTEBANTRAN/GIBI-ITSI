<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTables extends Migration
{
    protected $DBGroup = 'tests';

    public function up(): void
    {
        $rolesTable = $this->db->prefixTable('roles');
        $usuariosTable = $this->db->prefixTable('usuarios');

        // Create roles table using raw SQL for SQLite compatibility
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$rolesTable}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"nombre\" VARCHAR(50) NOT NULL,
                \"descripcion\" TEXT NULL,
                \"permisos\" TEXT NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activo',
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
        $this->db->query("CREATE INDEX IF NOT EXISTS \"idx_{$rolesTable}_nombre\" ON \"{$rolesTable}\" (\"nombre\")");

        // Create usuarios table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS \"{$usuariosTable}\" (
                \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                \"rol_id\" INTEGER NOT NULL,
                \"carrera_id\" INTEGER NULL,
                \"nombre\" VARCHAR(100) NOT NULL,
                \"apellido\" VARCHAR(100) NOT NULL,
                \"cedula\" VARCHAR(10) NOT NULL,
                \"email\" VARCHAR(255) NOT NULL,
                \"password_hash\" VARCHAR(255) NOT NULL,
                \"telefono\" VARCHAR(15) NULL,
                \"direccion\" VARCHAR(255) NULL,
                \"carrera\" VARCHAR(100) NULL,
                \"semestre\" VARCHAR(50) NULL,
                \"foto_perfil\" VARCHAR(255) NULL,
                \"estado\" VARCHAR(20) NOT NULL DEFAULT 'Activo',
                \"ultimo_acceso\" DATETIME NULL,
                \"intentos_fallidos\" INTEGER NOT NULL DEFAULT 0,
                \"bloqueado_hasta\" DATETIME NULL,
                \"configuraciones_usuario\" TEXT NULL,
                \"fecha_registro\" DATETIME NULL,
                \"created_at\" DATETIME NULL,
                \"updated_at\" DATETIME NULL
            )
        ");
        $this->db->query("CREATE INDEX IF NOT EXISTS \"idx_{$usuariosTable}_rol_id\" ON \"{$usuariosTable}\" (\"rol_id\")");
        $this->db->query("CREATE INDEX IF NOT EXISTS \"idx_{$usuariosTable}_cedula\" ON \"{$usuariosTable}\" (\"cedula\")");
        $this->db->query("CREATE INDEX IF NOT EXISTS \"idx_{$usuariosTable}_email\" ON \"{$usuariosTable}\" (\"email\")");
    }

    public function down(): void
    {
        $usuariosTable = $this->db->prefixTable('usuarios');
        $rolesTable = $this->db->prefixTable('roles');
        $this->db->query("DROP TABLE IF EXISTS \"{$usuariosTable}\"");
        $this->db->query("DROP TABLE IF EXISTS \"{$rolesTable}\"");
    }
}
