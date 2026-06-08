<?php

namespace App\Helpers;

/**
 * RecaptchaHelper - Integración con Google reCAPTCHA v2.
 * 
 * Valida respuestas del widget "No soy un robot" contra la API de Google.
 * 
 * INSTRUCCIONES PARA PRODUCCIÓN:
 * 1. Visite https://www.google.com/recaptcha/admin/create
 * 2. Registre su dominio y obtenga Site Key y Secret Key
 * 3. Reemplace las claves de prueba en este archivo
 */
class RecaptchaHelper
{
    /**
     * Clave del sitio (pública, usada en el frontend).
     * 
     * ⚠️ IMPORTANTE: Las claves actuales son claves de PRUEBA de Google que siempre pasan.
     * Para producción, reemplácelas con claves reales de https://www.google.com/recaptcha/admin
     * 
     * @see https://www.google.com/recaptcha/admin/create
     */
    private const SITE_KEY   = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'; // @phpstan-ignore-line
    private const SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // @phpstan-ignore-line

    /** URL de verificación de Google reCAPTCHA */
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Obtiene la clave del sitio para usar en el frontend.
     */
    public static function getSiteKey(): string
    {
        return self::SITE_KEY;
    }

    /**
     * Valida la respuesta del reCAPTCHA contra la API de Google.
     *
     * @param string|null $recaptchaResponse El valor de 'g-recaptcha-response' del formulario
     * @return bool true si la validación es exitosa
     */
    public static function validar(?string $recaptchaResponse): bool
    {

        if (empty($recaptchaResponse)) {
            return false;
        }

        $data = [
            'secret'   => self::SECRET_KEY,
            'response' => $recaptchaResponse,
            'remoteip' => self::getClientIp(),
        ];

        // Intentar con cURL primero (más confiable)
        if (function_exists('curl_init')) {
            return self::verificarConCurl($data);
        }

        // Fallback: file_get_contents
        return self::verificarConFileGetContents($data);
    }

    /**
     * Verificación usando cURL.
     */
    private static function verificarConCurl(array $data): bool
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::VERIFY_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            log_message('error', "reCAPTCHA cURL error: {$error} (HTTP {$httpCode})");
            return false;
        }

        $result = json_decode($response, true);

        if (!is_array($result)) {
            log_message('error', 'reCAPTCHA: Respuesta JSON inválida');
            return false;
        }

        if (!empty($result['error-codes'])) {
            log_message('warning', 'reCAPTCHA errors: ' . implode(', ', $result['error-codes']));
        }

        return isset($result['success']) && $result['success'] === true;
    }

    /**
     * Verificación usando file_get_contents (fallback).
     */
    private static function verificarConFileGetContents(array $data): bool
    {
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 10,
            ],
        ];

        $context  = stream_context_create($options);
        $response = @file_get_contents(self::VERIFY_URL, false, $context);

        if ($response === false) {
            log_message('error', 'reCAPTCHA: No se pudo conectar al servidor de verificación');
            return false;
        }

        $result = json_decode($response, true);

        return isset($result['success']) && $result['success'] === true;
    }

    /**
     * Obtiene la IP del cliente.
     */
    private static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
