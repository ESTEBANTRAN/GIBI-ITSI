<?php

declare(strict_types=1);

namespace App\Security;

/**
 * RateLimiter - Rate Limiting basado en caché de CI4.
 * 
 * Protege contra ataques de fuerza bruta limitando
 * la cantidad de intentos por IP o identificador en un período de tiempo.
 */
class RateLimiter
{
    /** @var \CodeIgniter\Cache\CacheInterface */
    private $cache;

    /** Máximo de intentos permitidos */
    private int $maxAttempts;

    /** Ventana de tiempo en segundos */
    private int $decaySeconds;

    /** Prefijo para las claves de caché */
    private string $prefix;

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 900, string $prefix = 'rate_limit')
    {
        $this->cache = \Config\Services::cache();
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        $this->prefix = $prefix;
    }

    /**
     * Verifica si el identificador (IP, email, etc.) ha superado el límite.
     */
    public function tooManyAttempts(string $key): bool
    {
        $attempts = $this->getAttempts($key);
        return $attempts >= $this->maxAttempts;
    }

    /**
     * Registra un intento para el identificador.
     */
    public function hit(string $key): int
    {
        $cacheKey = $this->getCacheKey($key);
        $attempts = $this->getAttempts($key);
        $attempts++;

        $this->cache->save($cacheKey, $attempts, $this->decaySeconds);
        
        // Guardar también el timestamp del primer intento
        $timestampKey = $cacheKey . '_ts';
        if (!$this->cache->get($timestampKey)) {
            $this->cache->save($timestampKey, time(), $this->decaySeconds);
        }

        return $attempts;
    }

    /**
     * Obtiene la cantidad de intentos actuales.
     */
    public function getAttempts(string $key): int
    {
        $cacheKey = $this->getCacheKey($key);
        $attempts = $this->cache->get($cacheKey);
        return $attempts ? (int)$attempts : 0;
    }

    /**
     * Obtiene los segundos restantes hasta que se resetee el límite.
     */
    public function getSecondsUntilReset(string $key): int
    {
        $timestampKey = $this->getCacheKey($key) . '_ts';
        $firstAttempt = $this->cache->get($timestampKey);

        if (!$firstAttempt) {
            return 0;
        }

        $elapsed = time() - (int)$firstAttempt;
        $remaining = $this->decaySeconds - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Obtiene los intentos restantes antes del bloqueo.
     */
    public function getRemainingAttempts(string $key): int
    {
        return max(0, $this->maxAttempts - $this->getAttempts($key));
    }

    /**
     * Limpia los intentos para un identificador.
     */
    public function clear(string $key): void
    {
        $cacheKey = $this->getCacheKey($key);
        $this->cache->delete($cacheKey);
        $this->cache->delete($cacheKey . '_ts');
    }

    /**
     * Genera la clave de caché normalizada.
     */
    private function getCacheKey(string $key): string
    {
        return $this->prefix . '_' . md5($key);
    }

    /**
     * Formatea el tiempo restante en un string legible.
     */
    public function getFormattedWaitTime(string $key): string
    {
        $seconds = $this->getSecondsUntilReset($key);
        
        if ($seconds >= 60) {
            $minutes = ceil($seconds / 60);
            return "{$minutes} minuto(s)";
        }
        
        return "{$seconds} segundo(s)";
    }
}
