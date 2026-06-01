<?php

namespace App\Helpers;

/**
 * Helper de Correo Electrónico del Sistema GIBI-ITSI
 */
class EmailHelper
{
    /**
     * Envía un correo electrónico utilizando la configuración dinámica de la base de datos.
     * 
     * @param string $to Correo del destinatario
     * @param string $subject Asunto del correo
     * @param string $message Mensaje en formato HTML o Texto
     * @param array $attachments Lista de rutas de archivos adjuntos (opcional)
     * @return bool True si se envió con éxito, False en caso contrario
     */
    public static function enviarCorreo(string $to, string $subject, string $message, array $attachments = []): bool
    {
        try {
            $db = \Config\Database::connect();
            
            // Cargar configuración de la base de datos (con valores del archivo .env como fallback)
            $smtpUser = self::getValorDb($db, 'gmail_correo', env('EMAIL_SMTP_USER', 'bienestar.itsi.info@gmail.com'));
            $smtpPass = self::getValorDb($db, 'gmail_clave', env('EMAIL_SMTP_PASS', ''));
            $smtpHost = self::getValorDb($db, 'gmail_smtp_host', 'smtp.gmail.com');
            $smtpPort = (int)self::getValorDb($db, 'gmail_smtp_port', '587');
            $smtpCrypto = self::getValorDb($db, 'gmail_smtp_crypto', 'tls');
            $fromName = self::getValorDb($db, 'nombre_institucion', 'Unidad de Bienestar - ITSI');
            
            // Validar que tengamos los datos mínimos
            if (empty($smtpUser) || empty($smtpPass)) {
                log_message('error', 'EmailHelper::enviarCorreo - No se pueden enviar correos sin usuario/clave SMTP.');
                return false;
            }

            // Inicializar servicio de email de CodeIgniter
            $email = \Config\Services::email();
            
            // Crear objeto de configuración personalizado
            $config = new \Config\Email();
            $config->protocol = 'smtp';
            $config->SMTPHost = $smtpHost;
            $config->SMTPUser = $smtpUser;
            $config->SMTPPass = $smtpPass;
            $config->SMTPPort = $smtpPort;
            $config->SMTPCrypto = $smtpCrypto;
            $config->fromEmail = $smtpUser;
            $config->fromName = $fromName;
            $config->mailType = 'html'; // Cambiamos a HTML por defecto para mejor estética
            $config->charset = 'UTF-8';
            $config->wordWrap = true;
            $config->newline = "\r\n";
            $config->CRLF = "\r\n";

            // Re-inicializar el servicio con la configuración dinámica
            $email->initialize((array)$config);

            $email->setFrom($smtpUser, $fromName);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($message);

            // Adjuntar archivos si existen
            foreach ($attachments as $filepath) {
                if (file_exists($filepath)) {
                    $email->attach($filepath);
                }
            }

            if ($email->send()) {
                log_message('info', "EmailHelper::enviarCorreo - Correo enviado exitosamente a: {$to} | Asunto: {$subject}");
                return true;
            } else {
                $errorMsg = $email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', "EmailHelper::enviarCorreo - Error al enviar correo a {$to}: " . $errorMsg);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "EmailHelper::enviarCorreo - Excepción al enviar correo a {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Función interna auxiliar para leer de la base de datos de manera segura
     */
    private static function getValorDb($db, string $clave, string $default = ''): string
    {
        try {
            if ($db->tableExists('configuracion_sistema')) {
                $row = $db->table('configuracion_sistema')
                    ->where('clave', $clave)
                    ->get()
                    ->getRowArray();
                if ($row && !empty($row['valor'])) {
                    return $row['valor'];
                }
            }
        } catch (\Exception $e) {
            log_message('warning', "EmailHelper::getValorDb - No se pudo leer clave {$clave}: " . $e->getMessage());
        }
        return $default;
    }
}
