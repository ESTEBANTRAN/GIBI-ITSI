<?php

declare(strict_types=1);

namespace App\Security;

/**
 * CsrfGuard - Complemento CSRF para peticiones AJAX.
 * 
 * Genera meta tags con tokens CSRF para uso en JavaScript
 * y valida tokens dobles (cookie + header).
 */
class CsrfGuard
{
    /**
     * Genera las meta tags HTML para CSRF que JavaScript puede leer.
     * Incluir en el <head> del layout.
     */
    public static function metaTags(): string
    {
        $csrf = csrf_hash();
        $tokenName = csrf_token();
        
        return '<meta name="csrf-token" content="' . $csrf . '">' . PHP_EOL
             . '<meta name="csrf-token-name" content="' . $tokenName . '">';
    }

    /**
     * Genera un snippet JavaScript para configurar AJAX con CSRF.
     * Compatible con fetch, jQuery y XMLHttpRequest.
     */
    public static function ajaxSetupScript(): string
    {
        return <<<'SCRIPT'
        <script>
        /**
         * ITSI Security - CSRF para AJAX.
         * Configuración automática de tokens CSRF en peticiones AJAX.
         */
        (function() {
            'use strict';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const csrfName = document.querySelector('meta[name="csrf-token-name"]');
            
            if (!csrfToken || !csrfName) {
                console.warn('[ITSI Security] Meta tags CSRF no encontrados.');
                return;
            }
            
            // Configurar para jQuery si existe
            if (typeof jQuery !== 'undefined') {
                jQuery.ajaxSetup({
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
                    }
                });
                
                // Actualizar token después de cada respuesta AJAX
                jQuery(document).ajaxComplete(function(event, xhr) {
                    var newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        csrfToken.content = newToken;
                    }
                });
            }
            
            // Helper global para fetch con CSRF
            window.secureFetch = function(url, options) {
                options = options || {};
                options.headers = options.headers || {};
                
                // Agregar el token CSRF
                if (options.method && options.method.toUpperCase() !== 'GET') {
                    options.headers['X-CSRF-TOKEN'] = csrfToken.content;
                    
                    // Si es FormData, agregar como campo
                    if (options.body instanceof FormData) {
                        options.body.append(csrfName.content, csrfToken.content);
                    }
                }
                
                return fetch(url, options);
            };
            
            // Interceptar envíos de formularios para agregar el token actualizado
            document.addEventListener('submit', function(e) {
                var form = e.target;
                var csrfInput = form.querySelector('input[name="' + csrfName.content + '"]');
                if (csrfInput) {
                    csrfInput.value = csrfToken.content;
                }
            });
        })();
        </script>
        SCRIPT;
    }

    /**
     * Genera un campo hidden CSRF para formularios dinámicos.
     */
    public static function hiddenField(): string
    {
        return csrf_field();
    }

    /**
     * Obtiene el token CSRF actual como array (nombre => valor).
     */
    public static function getTokenArray(): array
    {
        return [
            'name'  => csrf_token(),
            'value' => csrf_hash(),
        ];
    }

    /**
     * Obtiene el token CSRF como JSON para respuestas AJAX.
     */
    public static function getTokenJson(): string
    {
        return json_encode(self::getTokenArray());
    }
}
