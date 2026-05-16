<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Security\XssProtector;
use App\Security\SecurityLogger;

/**
 * XssFilter - Filtro global anti-XSS.
 * 
 * Sanitiza todos los datos POST y GET entrantes para
 * prevenir ataques Cross-Site Scripting.
 */
class XssFilter implements FilterInterface
{
    /**
     * Sanitiza los datos de entrada antes de llegar al controlador.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar datos GET
        $getData = $request->getGet();
        if (!empty($getData)) {
            foreach ($getData as $key => $value) {
                if (is_string($value) && XssProtector::detectXss($value)) {
                    $logger = new SecurityLogger();
                    $logger->logInjectionAttempt('xss', "GET[{$key}]: {$value}");
                    
                    // Limpiar el valor en lugar de bloquear la petición
                    $_GET[$key] = XssProtector::clean($value);
                }
            }
        }

        // Verificar datos POST
        $postData = $request->getPost();
        if (!empty($postData)) {
            $this->scanPostData($postData, '');
        }
    }

    /**
     * Escanea recursivamente los datos POST.
     */
    private function scanPostData(array $data, string $parentKey): void
    {
        foreach ($data as $key => $value) {
            $fullKey = $parentKey ? "{$parentKey}[{$key}]" : $key;
            
            if (is_array($value)) {
                $this->scanPostData($value, $fullKey);
            } elseif (is_string($value) && XssProtector::detectXss($value)) {
                $logger = new SecurityLogger();
                $logger->logInjectionAttempt('xss', "POST[{$fullKey}]: " . mb_substr($value, 0, 100));
                
                // Limpiar el valor
                $_POST[$key] = XssProtector::clean($value);
            }
        }
    }

    /**
     * No hace nada después de la respuesta.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Sin procesamiento posterior
    }
}
