<?php

namespace App\Models;

use CodeIgniter\Model;

class PdfCodigoVerificacionModel extends Model
{
    protected $table            = 'pdf_codigos_verificacion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields = [
        'codigo', 'tipo_documento', 'id_documento', 'id_usuario', 
        'ip_generacion', 'user_agent', 'estado', 'fecha_verificacion', 
        'ip_verificacion'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_generacion';
    protected $updatedField  = 'fecha_generacion';
    
    /**
     * Generar código único de verificación
     */
    public function generarCodigoUnico($tipoDocumento, $idDocumento, $idUsuario)
    {
        do {
            // Formato: ITSI-YYYYMMDD-HHMMSS-XXXXX
            $fecha = date('Ymd-His');
            $random = strtoupper(substr(md5(uniqid()), 0, 5));
            $codigo = "ITSI-{$fecha}-{$random}";
            
            // Verificar que no exista
            $existe = $this->where('codigo', $codigo)->first();
        } while ($existe);
        
        // Obtener IP y User Agent
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
        
        // Crear registro
        $data = [
            'codigo' => $codigo,
            'tipo_documento' => $tipoDocumento,
            'id_documento' => $idDocumento,
            'id_usuario' => $idUsuario,
            'ip_generacion' => $ip,
            'user_agent' => $userAgent,
            'estado' => 'activo'
        ];
        
        $this->insert($data);
        return $codigo;
    }
    
    /**
     * Verificar código de verificación
     */
    public function verificarCodigo($codigo, $ipVerificacion = null)
    {
        try {
            $registro = $this->where('codigo', $codigo)->first();
            
            if (!$registro) {
                return [
                    'valido' => false,
                    'mensaje' => 'Código no encontrado',
                    'datos' => null
                ];
            }
            
            if ($registro['estado'] === 'expirado') {
                return [
                    'valido' => false,
                    'mensaje' => 'Código expirado',
                    'datos' => null
                ];
            }
            
            if ($registro['estado'] === 'verificado') {
                return [
                    'valido' => false,
                    'mensaje' => 'Código ya fue verificado anteriormente',
                    'datos' => $registro
                ];
            }
            
            // Marcar como verificado
            $this->update($registro['id'], [
                'estado' => 'verificado',
                'fecha_verificacion' => date('Y-m-d H:i:s'),
                'ip_verificacion' => $ipVerificacion ?? $this->getClientIP()
            ]);
            
            // Obtener el registro actualizado
            $registroActualizado = $this->find($registro['id']);
            
            return [
                'valido' => true,
                'mensaje' => 'Código verificado exitosamente',
                'datos' => $registroActualizado
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error en verificarCodigo: ' . $e->getMessage());
            
            return [
                'valido' => false,
                'mensaje' => 'Error interno: ' . $e->getMessage(),
                'datos' => null
            ];
        }
    }
    
    /**
     * Obtener IP del cliente
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

