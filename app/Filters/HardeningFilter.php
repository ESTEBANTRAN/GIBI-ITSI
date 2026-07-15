<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Security\SecurityHelper;
use App\Security\SecurityLogger;

class HardeningFilter implements FilterInterface
{
    private const BLOCKED_USER_AGENTS = [
        'bot', 'crawl', 'spider', 'scanner', 'nikto', 'wpscan',
        'sqlmap', 'nmap', 'acunetix', 'nessus', 'openvas',
        'burp', 'zap', 'python-requests', 'go-http',
        'masscan', 'hydra', 'jndi', 'lookup',
    ];

    private const SUSPICIOUS_PATHS = [
        '/wp-admin', '/wp-content', '/wp-', '/wordpress',
        '/administrator', '/joomla', '/cgi-bin',
        '/.env', '/.git', '/composer.json', '/vendor',
        '/phpmyadmin', '/pma', '/admin',
        '/server-status', '/server-info',
        '/actuator', '/console',
    ];

    private const SUSPICIOUS_PARAMS = [
        'cmd', 'exec', 'command', 'shell', 'powershell',
        'eval', 'code', 'file', 'include', 'require',
        'dbg', 'debug', 'xdebug',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $uri    = $request->getUri();
        $path   = $uri->getPath();
        $method = $request->getMethod(true);
        $ip     = SecurityHelper::getClientIp();
        $ua     = $request->getUserAgent()->getAgentString() ?? '';

        // ─── 1. Bloquear métodos HTTP peligrosos ──────────────────────
        if (in_array($method, ['TRACE', 'TRACK', 'OPTIONS', 'PUT', 'DELETE'], true)) {
            return $this->block('METHOD_NOT_ALLOWED', "Método HTTP prohibido: {$method}", 405);
        }

        // ─── 2. Bloquear paths de escaneo conocidos ───────────────────
        foreach (self::SUSPICIOUS_PATHS as $suspicious) {
            if (str_starts_with($path, $suspicious)) {
                return $this->block('SCANNER_PATH', "Path sospechoso: {$path}", 403);
            }
        }

        // ─── 3. Bloquear User-Agents maliciosos ───────────────────────
        $uaLower = strtolower($ua);
        foreach (self::BLOCKED_USER_AGENTS as $agent) {
            if (str_contains($uaLower, $agent)) {
                return $this->block('MALICIOUS_UA', "User-Agent bloqueado: {$ua}", 403);
            }
        }

        // ─── 4. Bloquear parámetros sospechosos en query string ───────
        $query = $uri->getQuery();
        if ($query) {
            parse_str($query, $params);
            foreach ($params as $key => $value) {
                $keyLower = strtolower((string)$key);
                if (in_array($keyLower, self::SUSPICIOUS_PARAMS, true)) {
                    return $this->block('SUSPICIOUS_PARAM', "Parámetro sospechoso: {$key}", 403);
                }
                $valueStr = is_string($value) ? strtolower($value) : '';
                if (preg_match('/(union\s+select|select\s+.*from|drop\s+table|insert\s+into|delete\s+from|exec\s*\()/i', $valueStr)) {
                    return $this->block('SQL_INJECTION', "Posible SQLi en parámetro: {$key}", 403);
                }
            }
        }

        // ─── 5. Validar Referer para POST requests ────────────────────
        if ($method === 'POST') {
            $referer = $request->getHeaderLine('Referer');
            $origin  = $request->getHeaderLine('Origin');
            $baseURL = rtrim(config('App')->baseURL, '/');

            if ($referer && !str_starts_with($referer, $baseURL) && !str_contains($referer, 'localhost')) {
                return $this->block('INVALID_REFERER', "Referer inválido: {$referer}", 403);
            }
        }

        // ─── 6. Rate limit por IP (protección contra fuerza bruta) ────
        $cache = \Config\Services::cache();
        $rateKey = 'hr_' . str_replace(['.', ':'], '_', $ip);
        $attempts = (int)$cache->get($rateKey);

        if ($attempts > 120) {
            return $this->block('RATE_LIMIT', "IP bloqueada por exceso de peticiones: {$ip}", 429);
        }

        $cache->save($rateKey, $attempts + 1, 60);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Security headers adicionales (capa aplicación por si .htaccess no alcanza)
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->removeHeader('X-Powered-By');

        return $response;
    }

    private function block(string $reason, string $detail, int $code = 403)
    {
        $logger = new SecurityLogger();
        $logger->log(
            SecurityLogger::LEVEL_WARNING,
            $reason,
            $detail,
            ['ip' => SecurityHelper::getClientIp(), 'uri' => service('request')->getUri()->getPath()]
        );

        $response = service('response');
        $response->setStatusCode($code);
        $response->setJSON([
            'error'   => true,
            'message' => 'Acceso denegado',
        ]);
        $response->send();
        exit;
    }
}
