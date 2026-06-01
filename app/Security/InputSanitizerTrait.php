<?php

declare(strict_types=1);

namespace App\Security;

/**
 * InputSanitizerTrait - Trait reutilizable para sanitización consistente.
 * 
 * Úsalo en cualquier controlador con `use App\Security\InputSanitizerTrait;`
 * Proporciona métodos para sanitizar entradas GET, POST, JSON y arrays.
 */
trait InputSanitizerTrait
{
    /**
     * Obtiene y sanitiza un valor POST como string.
     */
    protected function getPostString(string $key, ?string $default = null): ?string
    {
        $value = $this->request->getPost($key);
        if ($value === null) {
            return $default;
        }
        return InputSanitizer::sanitizeValue((string) $value);
    }

    /**
     * Obtiene un valor POST como entero sanitizado.
     */
    protected function getPostInt(string $key, ?int $default = null): ?int
    {
        $value = $this->request->getPost($key);
        if ($value === null || $value === '') {
            return $default;
        }
        return (int) preg_replace('/[^0-9\-]/', '', (string) $value);
    }

    /**
     * Obtiene y sanitiza un array POST completo.
     */
    protected function getPostSanitized(): array
    {
        return InputSanitizer::sanitizeArray($this->request->getPost() ?? []);
    }

    /**
     * Obtiene datos JSON del body y sanitiza.
     */
    protected function getJsonSanitized(bool $assoc = true): ?array
    {
        $data = $this->request->getJSON($assoc);
        if (!is_array($data)) {
            return null;
        }
        return InputSanitizer::sanitizeArray($data);
    }


}
