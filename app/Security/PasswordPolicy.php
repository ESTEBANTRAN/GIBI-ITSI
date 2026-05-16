<?php

declare(strict_types=1);

namespace App\Security;

/**
 * PasswordPolicy - Políticas de contraseñas.
 * 
 * Valida la fortaleza de contraseñas contra políticas configurables:
 * longitud mínima, complejidad, contraseñas comunes, etc.
 */
class PasswordPolicy
{
    /** Longitud mínima requerida */
    private int $minLength = 8;

    /** Requiere al menos una mayúscula */
    private bool $requireUppercase = true;

    /** Requiere al menos una minúscula */
    private bool $requireLowercase = true;

    /** Requiere al menos un número */
    private bool $requireNumber = true;

    /** Requiere al menos un carácter especial */
    private bool $requireSpecialChar = false;

    /** Lista de contraseñas comunes prohibidas */
    private array $commonPasswords = [
        'password', '123456', '12345678', 'qwerty', 'abc123',
        'monkey', '1234567', 'letmein', 'trustno1', 'dragon',
        'baseball', 'iloveyou', 'master', 'sunshine', 'ashley',
        'michael', 'shadow', '123123', '654321', 'superman',
        'qazwsx', 'admin', 'welcome', 'login', 'passw0rd',
        'contraseña', 'password123', 'admin123', 'root', '12345',
    ];

    /**
     * Valida una contraseña contra todas las políticas.
     * 
     * @return array Lista de errores. Vacío si la contraseña cumple.
     */
    public function validate(string $password): array
    {
        $errors = [];

        if (strlen($password) < $this->minLength) {
            $errors[] = "La contraseña debe tener al menos {$this->minLength} caracteres.";
        }

        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra mayúscula.';
        }

        if ($this->requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra minúscula.';
        }

        if ($this->requireNumber && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número.';
        }

        if ($this->requireSpecialChar && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un carácter especial (!@#$%^&*).';
        }

        if ($this->isCommonPassword($password)) {
            $errors[] = 'Esta contraseña es demasiado común. Por favor elija otra.';
        }

        return $errors;
    }

    /**
     * Verifica si la contraseña está en la lista de contraseñas comunes.
     */
    public function isCommonPassword(string $password): bool
    {
        return in_array(strtolower($password), $this->commonPasswords, true);
    }

    /**
     * Evalúa la fortaleza de la contraseña.
     * @return string 'débil', 'media', 'fuerte', 'muy_fuerte'
     */
    public function getStrengthLabel(string $password): string
    {
        $score = SecurityHelper::getPasswordStrength($password);
        
        if ($score >= 80) return 'muy_fuerte';
        if ($score >= 60) return 'fuerte';
        if ($score >= 40) return 'media';
        return 'débil';
    }

    /**
     * Genera una contraseña segura aleatoria.
     */
    public function generateSecurePassword(int $length = 16): string
    {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=';
        
        $all = $lower . $upper . $numbers . $special;
        
        // Asegurar que tenga al menos uno de cada tipo
        $password = $lower[random_int(0, strlen($lower) - 1)];
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Completar el resto
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Mezclar
        return str_shuffle($password);
    }

    /**
     * Verifica que la nueva contraseña sea diferente a la anterior.
     */
    public function isDifferentFromPrevious(string $newPassword, string $previousHash): bool
    {
        return !password_verify($newPassword, $previousHash);
    }
}
