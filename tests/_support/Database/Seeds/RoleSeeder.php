<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Insert roles individually (avoid insertBatch issues with SQLite)
        $now = date('Y-m-d H:i:s');

        $this->db->table('roles')->insert([
            'id'          => ROLE_ESTUDIANTE,
            'nombre'      => 'Estudiante',
            'descripcion' => 'Rol de estudiante - acceso a funcionalidades básicas',
            'permisos'    => null,
            'estado'      => 'Activo',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('roles')->insert([
            'id'          => ROLE_ADMIN_BIENESTAR,
            'nombre'      => 'Admin Bienestar',
            'descripcion' => 'Rol administrativo de bienestar estudiantil',
            'permisos'    => null,
            'estado'      => 'Activo',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('roles')->insert([
            'id'          => ROLE_SUPER_ADMIN,
            'nombre'      => 'Super Administrador',
            'descripcion' => 'Rol de super administrador - acceso total al sistema',
            'permisos'    => null,
            'estado'      => 'Activo',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->db->table('roles')->insert([
            'id'          => 3,
            'nombre'      => 'Docente',
            'descripcion' => 'Rol de docente',
            'permisos'    => null,
            'estado'      => 'Inactivo',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // Insert usuarios individually
        $passwordHash = password_hash('test1234', PASSWORD_BCRYPT);

        // 2 estudiantes activos
        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_ESTUDIANTE,
            'nombre'            => 'Juan',
            'apellido'          => 'Pérez',
            'cedula'            => '1234567890',
            'email'             => 'juan.perez@test.com',
            'password_hash'     => $passwordHash,
            'telefono'          => '0987654321',
            'direccion'         => 'Calle 1',
            'carrera'           => 'Ingeniería en Sistemas',
            'semestre'          => '5',
            'estado'            => 'Activo',
            'intentos_fallidos' => 0,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-30 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-30 days')),
            'updated_at'        => $now,
        ]);

        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_ESTUDIANTE,
            'nombre'            => 'María',
            'apellido'          => 'Gómez',
            'cedula'            => '0987654321',
            'email'             => 'maria.gomez@test.com',
            'password_hash'     => $passwordHash,
            'telefono'          => '0999999999',
            'direccion'         => 'Calle 2',
            'carrera'           => 'Administración',
            'semestre'          => '3',
            'estado'            => 'Activo',
            'intentos_fallidos' => 0,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-10 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-10 days')),
            'updated_at'        => $now,
        ]);

        // 1 estudiante inactivo
        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_ESTUDIANTE,
            'nombre'            => 'Pedro',
            'apellido'          => 'Ramírez',
            'cedula'            => '1112233445',
            'email'             => 'pedro.ramirez@test.com',
            'password_hash'     => $passwordHash,
            'telefono'          => '0977777777',
            'estado'            => 'Inactivo',
            'intentos_fallidos' => 5,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-60 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-60 days')),
            'updated_at'        => $now,
        ]);

        // 1 admin bienestar activo
        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_ADMIN_BIENESTAR,
            'nombre'            => 'Carlos',
            'apellido'          => 'Mendoza',
            'cedula'            => '1723456789',
            'email'             => 'carlos.mendoza@admin.com',
            'password_hash'     => $passwordHash,
            'telefono'          => '0966666666',
            'direccion'         => 'Oficina 101',
            'estado'            => 'Activo',
            'intentos_fallidos' => 0,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-90 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-90 days')),
            'updated_at'        => $now,
        ]);

        // 1 admin bienestar inactivo
        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_ADMIN_BIENESTAR,
            'nombre'            => 'Ana',
            'apellido'          => 'López',
            'cedula'            => '1712345678',
            'email'             => 'ana.lopez@admin.com',
            'password_hash'     => $passwordHash,
            'estado'            => 'Inactivo',
            'intentos_fallidos' => 0,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-45 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-45 days')),
            'updated_at'        => $now,
        ]);

        // 1 super admin activo
        $this->db->table('usuarios')->insert([
            'rol_id'            => ROLE_SUPER_ADMIN,
            'nombre'            => 'Admin',
            'apellido'          => 'Super',
            'cedula'            => '1798765432',
            'email'             => 'super.admin@sistema.com',
            'password_hash'     => $passwordHash,
            'telefono'          => '0955555555',
            'estado'            => 'Activo',
            'intentos_fallidos' => 0,
            'fecha_registro'    => date('Y-m-d H:i:s', strtotime('-365 days')),
            'created_at'        => date('Y-m-d H:i:s', strtotime('-365 days')),
            'updated_at'        => $now,
        ]);
    }
}
