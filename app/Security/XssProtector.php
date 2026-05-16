<?php

declare(strict_types=1);

namespace App\Security;

/**
 * XssProtector - Protección contra Cross-Site Scripting.
 * 
 * Filtra y sanitiza entradas para prevenir ataques XSS.
 * Limpia atributos HTML peligrosos y realiza encoding de salida.
 */
class XssProtector
{
    /** Eventos JavaScript peligrosos */
    private static array $dangerousEvents = [
        'onabort', 'onblur', 'onchange', 'onclick', 'ondblclick',
        'onerror', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup',
        'onload', 'onmousedown', 'onmousemove', 'onmouseout',
        'onmouseover', 'onmouseup', 'onreset', 'onresize',
        'onselect', 'onsubmit', 'onunload', 'onbeforeunload',
        'onanimationend', 'onanimationstart', 'ontransitionend',
        'onscroll', 'onwheel', 'oncontextmenu', 'ondrag', 'ondrop',
        'onpaste', 'oncopy', 'oncut', 'oninput', 'oninvalid',
    ];

    /**
     * Limpia una cadena de posible contenido XSS.
     */
    public static function clean(string $input): string
    {
        // Eliminar bytes nulos
        $input = str_replace("\0", '', $input);
        
        // Decodificar entidades HTML para detectar payloads ofuscados
        $decoded = html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Eliminar etiquetas script y similares
        $decoded = self::removeScriptTags($decoded);
        
        // Eliminar event handlers
        $decoded = self::removeEventHandlers($decoded);
        
        // Eliminar protocolos peligrosos
        $decoded = self::removeDangerousProtocols($decoded);
        
        // Re-encode para salida segura
        return htmlspecialchars($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Limpia un array completo de posible contenido XSS.
     */
    public static function cleanArray(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);
            } elseif (is_string($value)) {
                $cleaned[$key] = self::clean($value);
            } else {
                $cleaned[$key] = $value;
            }
        }
        return $cleaned;
    }

    /**
     * Detecta si un input contiene posible payload XSS.
     */
    public static function detectXss(string $input): bool
    {
        $decoded = html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = urldecode($decoded);
        $lower = strtolower($decoded);

        $patterns = [
            '/<\s*script/i',
            '/<\s*iframe/i',
            '/<\s*object/i',
            '/<\s*embed/i',
            '/<\s*applet/i',
            '/<\s*form/i',
            '/<\s*svg[^>]*on/i',
            '/<\s*img[^>]*on/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:\s*text\/html/i',
            '/expression\s*\(/i',
            '/url\s*\(\s*["\']?\s*javascript/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decoded)) {
                return true;
            }
        }

        // Verificar event handlers
        foreach (self::$dangerousEvents as $event) {
            if (stripos($lower, $event) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Elimina etiquetas <script> y su contenido.
     */
    private static function removeScriptTags(string $input): string
    {
        // Eliminar <script>...</script> y variantes
        $input = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', '', $input);
        // Eliminar tags script sin cierre
        $input = preg_replace('/<\s*script[^>]*>/i', '', $input);
        // Eliminar otras etiquetas peligrosas
        $input = preg_replace('/<\s*(iframe|object|embed|applet|meta|link|base)[^>]*>/i', '', $input);
        return $input;
    }

    /**
     * Elimina event handlers de atributos HTML.
     */
    private static function removeEventHandlers(string $input): string
    {
        foreach (self::$dangerousEvents as $event) {
            $input = preg_replace('/\s*' . $event . '\s*=\s*["\'][^"\']*["\']/i', '', $input);
            $input = preg_replace('/\s*' . $event . '\s*=\s*\S+/i', '', $input);
        }
        return $input;
    }

    /**
     * Elimina protocolos peligrosos (javascript:, vbscript:, data:).
     */
    private static function removeDangerousProtocols(string $input): string
    {
        $protocols = ['javascript', 'vbscript', 'data', 'mhtml'];
        foreach ($protocols as $protocol) {
            $input = preg_replace('/' . $protocol . '\s*:/i', '', $input);
        }
        return $input;
    }

    /**
     * Encode seguro para salida en atributos HTML.
     */
    public static function encodeForAttribute(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Encode seguro para salida en JavaScript.
     */
    public static function encodeForJs(string $input): string
    {
        return json_encode($input, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Encode seguro para URLs.
     */
    public static function encodeForUrl(string $input): string
    {
        return rawurlencode($input);
    }
}
