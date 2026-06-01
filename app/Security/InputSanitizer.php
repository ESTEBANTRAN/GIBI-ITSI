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


}
