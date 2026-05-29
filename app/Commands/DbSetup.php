<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbSetup extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'db:setup';
    protected $description = 'Crea y puebla la tabla configuracion_sistema y verifica la base de datos';
    protected $usage       = 'php spark db:setup';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Iniciando configuración de la base de datos...', 'blue');
        
        // 1. Crear tabla configuracion_sistema si no existe
        if (!$db->tableExists('configuracion_sistema')) {
            CLI::write('Creando tabla "configuracion_sistema"...', 'yellow');
            
            $query = "
                CREATE TABLE `configuracion_sistema` (
                    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `clave` VARCHAR(100) UNIQUE NOT NULL,
                    `valor` TEXT NULL,
                    `descripcion` TEXT NULL,
                    `tipo` VARCHAR(50) DEFAULT 'text',
                    `categoria` VARCHAR(50) DEFAULT 'general',
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $db->query($query);
            CLI::write('Tabla "configuracion_sistema" creada con éxito.', 'green');
        } else {
            CLI::write('La tabla "configuracion_sistema" ya existe.', 'green');
        }

        // 2. Insertar configuraciones iniciales
        CLI::write('Insertando/actualizando configuraciones del sistema...', 'yellow');
        
        $configuraciones = [
            // General
            [
                'clave' => 'nombre_institucion',
                'valor' => 'Instituto Tecnológico Superior de Informática',
                'descripcion' => 'Nombre oficial de la institución educativa',
                'tipo' => 'text',
                'categoria' => 'general'
            ],
            [
                'clave' => 'email_contacto',
                'valor' => 'bienestar@itsi.edu.ec',
                'descripcion' => 'Correo de contacto general del departamento de bienestar',
                'tipo' => 'email',
                'categoria' => 'general'
            ],
            [
                'clave' => 'telefono_contacto',
                'valor' => '+593 2 1234567',
                'descripcion' => 'Teléfono de contacto de bienestar estudiantil',
                'tipo' => 'text',
                'categoria' => 'general'
            ],
            [
                'clave' => 'direccion',
                'valor' => 'Av. Principal 123, Quito, Ecuador',
                'descripcion' => 'Dirección física de las oficinas de bienestar',
                'tipo' => 'textarea',
                'categoria' => 'general'
            ],

            // Notificaciones
            [
                'clave' => 'notificaciones_email',
                'valor' => '1',
                'descripcion' => 'Habilitar el envío de notificaciones automáticas por correo electrónico',
                'tipo' => 'boolean',
                'categoria' => 'notificaciones'
            ],
            [
                'clave' => 'notificaciones_sistema',
                'valor' => '1',
                'descripcion' => 'Habilitar notificaciones internas en el panel del sistema',
                'tipo' => 'boolean',
                'categoria' => 'notificaciones'
            ],
            [
                'clave' => 'recordatorios_automaticos',
                'valor' => '1',
                'descripcion' => 'Habilitar recordatorios automáticos de entrega de fichas/documentos',
                'tipo' => 'boolean',
                'categoria' => 'notificaciones'
            ],
            [
                'clave' => 'frecuencia_recordatorios',
                'valor' => 'semanal',
                'descripcion' => 'Frecuencia con la que se envían recordatorios automáticos',
                'tipo' => 'select',
                'categoria' => 'notificaciones'
            ],

            // Seguridad
            [
                'clave' => 'tiempo_sesion',
                'valor' => '30',
                'descripcion' => 'Tiempo de inactividad de sesión permitido en minutos',
                'tipo' => 'number',
                'categoria' => 'seguridad'
            ],
            [
                'clave' => 'intentos_login',
                'valor' => '3',
                'descripcion' => 'Límite de intentos de inicio de sesión fallidos antes del bloqueo temporal',
                'tipo' => 'number',
                'categoria' => 'seguridad'
            ],
            [
                'clave' => 'requerir_cambio_password',
                'valor' => '0',
                'descripcion' => 'Forzar a los usuarios a cambiar su contraseña periódicamente',
                'tipo' => 'boolean',
                'categoria' => 'seguridad'
            ],
            [
                'clave' => 'dias_cambio_password',
                'valor' => '90',
                'descripcion' => 'Días de validez de la contraseña antes de requerir cambio',
                'tipo' => 'number',
                'categoria' => 'seguridad'
            ],

            // Archivos
            [
                'clave' => 'tamano_maximo_archivo',
                'valor' => '10',
                'descripcion' => 'Tamaño máximo permitido para la subida de archivos (MB)',
                'tipo' => 'number',
                'categoria' => 'archivos'
            ],
            [
                'clave' => 'tipos_archivo_permitidos',
                'valor' => 'pdf,jpg,jpeg,png,doc,docx',
                'descripcion' => 'Extensiones de archivos permitidas separadas por comas',
                'tipo' => 'text',
                'categoria' => 'archivos'
            ],
            [
                'clave' => 'directorio_uploads',
                'valor' => 'uploads/',
                'descripcion' => 'Directorio de almacenamiento de archivos en el servidor',
                'tipo' => 'text',
                'categoria' => 'archivos'
            ],
            [
                'clave' => 'comprimir_imagenes',
                'valor' => '1',
                'descripcion' => 'Reducir el tamaño de las imágenes subidas automáticamente',
                'tipo' => 'boolean',
                'categoria' => 'archivos'
            ],

            // Respaldo
            [
                'clave' => 'respaldo_automatico',
                'valor' => '1',
                'descripcion' => 'Habilitar backups automáticos de la base de datos',
                'tipo' => 'boolean',
                'categoria' => 'respaldo'
            ],
            [
                'clave' => 'frecuencia_respaldo',
                'valor' => 'semanal',
                'descripcion' => 'Frecuencia de generación de backups de base de datos',
                'tipo' => 'select',
                'categoria' => 'respaldo'
            ],
            [
                'clave' => 'hora_respaldo',
                'valor' => '02:00',
                'descripcion' => 'Hora programada para generar el backup',
                'tipo' => 'text',
                'categoria' => 'respaldo'
            ],
            [
                'clave' => 'retener_respaldos',
                'valor' => '30',
                'descripcion' => 'Días de retención de backups antiguos antes de eliminarse',
                'tipo' => 'number',
                'categoria' => 'respaldo'
            ],

            // SMTP Email
            [
                'clave' => 'gmail_correo',
                'valor' => 'bienestar.itsi.info@gmail.com',
                'descripcion' => 'Correo institucional de Gmail para envío masivo de notificaciones',
                'tipo' => 'email',
                'categoria' => 'correo'
            ],
            [
                'clave' => 'gmail_clave',
                'valor' => 'itsi1234bienestar',
                'descripcion' => 'Clave de aplicación / Password SMTP del correo institucional',
                'tipo' => 'password',
                'categoria' => 'correo'
            ],
            [
                'clave' => 'gmail_smtp_host',
                'valor' => 'smtp.gmail.com',
                'descripcion' => 'Host del servidor SMTP de correo',
                'tipo' => 'text',
                'categoria' => 'correo'
            ],
            [
                'clave' => 'gmail_smtp_port',
                'valor' => '587',
                'descripcion' => 'Puerto del servidor SMTP',
                'tipo' => 'number',
                'categoria' => 'correo'
            ],
            [
                'clave' => 'gmail_smtp_crypto',
                'valor' => 'tls',
                'descripcion' => 'Cifrado de conexión SMTP (tls / ssl)',
                'tipo' => 'text',
                'categoria' => 'correo'
            ],

            // Google Drive API
            [
                'clave' => 'drive_activo',
                'valor' => '0',
                'descripcion' => 'Activar el guardado en la nube en Google Drive además del almacenamiento local',
                'tipo' => 'boolean',
                'categoria' => 'drive'
            ],
            [
                'clave' => 'drive_client_id',
                'valor' => '',
                'descripcion' => 'Google API OAuth Client ID',
                'tipo' => 'text',
                'categoria' => 'drive'
            ],
            [
                'clave' => 'drive_client_secret',
                'valor' => '',
                'descripcion' => 'Google API OAuth Client Secret',
                'tipo' => 'text',
                'categoria' => 'drive'
            ],
            [
                'clave' => 'drive_refresh_token',
                'valor' => '',
                'descripcion' => 'Google OAuth 2.0 Refresh Token obtenido para la cuenta bienestar.itsi.info@gmail.com',
                'tipo' => 'text',
                'categoria' => 'drive'
            ],
            [
                'clave' => 'drive_folder_id',
                'valor' => '',
                'descripcion' => 'ID de la carpeta en Google Drive donde se almacenarán las subidas',
                'tipo' => 'text',
                'categoria' => 'drive'
            ]
        ];

        foreach ($configuraciones as $config) {
            $existing = $db->table('configuracion_sistema')->where('clave', $config['clave'])->get()->getRowArray();
            
            if ($existing) {
                // Actualizar sólo si el valor por defecto cambió o para agregar campos nuevos
                $db->table('configuracion_sistema')->where('clave', $config['clave'])->update([
                    'descripcion' => $config['descripcion'],
                    'tipo' => $config['tipo'],
                    'categoria' => $config['categoria']
                ]);
            } else {
                $db->table('configuracion_sistema')->insert($config);
            }
        }
        
        CLI::write('Configuraciones insertadas/actualizadas correctamente.', 'green');

        // 3. Agregar columna google_drive_id si no existe
        if ($db->tableExists('documentos_solicitud_becas')) {
            $fields = $db->getFieldNames('documentos_solicitud_becas');
            if (!in_array('google_drive_id', $fields)) {
                CLI::write('Agregando columna "google_drive_id" a "documentos_solicitud_becas"...', 'yellow');
                $db->query("ALTER TABLE `documentos_solicitud_becas` ADD COLUMN `google_drive_id` VARCHAR(100) NULL AFTER `tipo_mime`;");
                CLI::write('Columna "google_drive_id" agregada con éxito.', 'green');
            } else {
                CLI::write('La columna "google_drive_id" ya existe.', 'green');
            }
        }

        CLI::write('Base de datos GIBI-ITSI configurada exitosamente.', 'green');
    }
}
