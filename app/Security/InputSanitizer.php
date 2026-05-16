<?php

declare(strict_types=1);

namespace App\Security;

/**
 * InputSanitizer - Sanitización avanzada de entradas.
 * 
 * Capa adicional de protección contra inyecciones SQL, XSS,
 * y otros ataques basados en entradas maliciosas.
 */
class InputSanitizer
{
    /**
     * Sanitiza todos los datos de un array (típicamente $_POST o $_GET).
     */
    public static function sanitizeArray(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            $cleanKey = self::sanitizeKey($key);
            if (is_array($value)) {
                $cleaned[$cleanKey] = self::sanitizeArray($value);
            } else {
                $cleaned[$cleanKey] = self::sanitizeValue((string)$value);
            }
        }
        return $cleaned;
    }

    /**
     * Sanitiza una clave de formulario.
     */
    public static function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-\[\]]/', '', $key);
    }

    /**
     * Sanitiza un valor individual.
     */
    public static function sanitizeValue(string $value): string
    {
        // Eliminar bytes nulos
        $value = str_replace("\0", '', $value);
        
        // Eliminar caracteres de control invisibles (excepto newline, tab)
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Normalizar line endings
        $value = str_replace(["\r\n", "\r"], "\n", $value);

        return trim($value);
    }

    /**
     * Elimina etiquetas HTML peligrosas pero permite formato básico.
     */
    public static function sanitizeHtml(string $html): string
    {
        // Tags permitidos para campos de texto enriquecido
        $allowedTags = '<p><br><strong><em><ul><ol><li><b><i><u>';
        $html = strip_tags($html, $allowedTags);

        // Eliminar atributos de evento (onclick, onload, etc.)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Eliminar javascript: y data: en href/src
        $html = preg_replace('/\s*(href|src)\s*=\s*["\']?\s*(javascript|data|vbscript)\s*:/i', '', $html);
        
        // Eliminar style con expression
        $html = preg_replace('/style\s*=\s*["\'][^"\']*expression\s*\([^"\']*["\']/i', '', $html);

        return $html;
    }

    /**
     * Valida y sanitiza un número de cédula ecuatoriana.
     */
    public static function sanitizeCedula(string $cedula): string
    {
        // Solo permitir dígitos
        return preg_replace('/[^0-9]/', '', $cedula);
    }

    /**
     * Detecta patrones de inyección SQL comunes.
     * NOTA: Esto es una capa ADICIONAL. CI4 ya sanitiza con query bindings.
     */
    public static function detectSqlInjection(string $input): bool
    {
        $patterns = [
            '/(\b(union|select|insert|update|delete|drop|alter|create|truncate|exec|execute)\b\s+(all\s+)?)/i',
            '/(\'|\"|;|--|\#|\/\*|\*\/)/i',
            '/(\b(or|and)\b\s+[\'\"\d]+\s*=\s*[\'\"\d]+)/i',
            '/(sleep|benchmark|waitfor|delay)\s*\(/i',
            '/\b(char|nchar|varchar|nvarchar)\s*\(/i',
            '/0x[0-9a-fA-F]+/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitiza un nombre de archivo para upload seguro.
     */
    public static function sanitizeUploadFilename(string $filename): string
    {
        // Obtener extensión
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Limpiar nombre
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        
        // Si el nombre queda vacío, generar uno
        if (empty($name)) {
            $name = 'archivo_' . time();
        }
        
        // Limpiar extensión
        $ext = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $ext));
        
        return $name . '.' . $ext;
    }

    /**
     * Valida el tipo MIME de un archivo subido.
     */
    public static function validateMimeType(string $filePath, array $allowedMimes): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return in_array($mimeType, $allowedMimes, true);
    }

    /**
     * Sanitiza un número de teléfono.
     */
    public static function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);
    }

    /**
     * Limita la longitud de un string para prevenir ataques de buffer.
     */
    public static function truncate(string $value, int $maxLength = 500): string
    {
        if (mb_strlen($value) > $maxLength) {
            return mb_substr($value, 0, $maxLength);
        }
        return $value;
    }
}
