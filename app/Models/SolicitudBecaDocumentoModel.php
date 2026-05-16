<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudBecaDocumentoModel extends Model
{
    protected $table            = 'documentos_solicitud_becas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'solicitud_beca_id',
        'documento_requerido_id',
        'nombre_archivo',
        'ruta_archivo',
        'orden_revision',
        'estado',
        'observaciones',
        'revisado_por',
        'fecha_subida',
        'fecha_revision',
        'tama±o_archivo',
        'tipo_mime'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'solicitud_beca_id'      => 'required|integer',
        'documento_requerido_id' => 'required|integer',
        'estado'                 => 'required|in_list[Pendiente,En Revision,Aprobado,Rechazado]'
    ];

    protected $validationMessages = [
        'solicitud_beca_id' => [
            'required' => 'El ID de la solicitud de beca es obligatorio',
            'integer' => 'El ID de la solicitud de beca debe ser un número entero'
        ],
        'documento_requisito_id' => [
            'required' => 'El ID del documento requisito es obligatorio',
            'integer' => 'El ID del documento requisito debe ser un número entero'
        ],
        'nombre_archivo' => [
            'required' => 'El nombre del archivo es obligatorio',
            'max_length' => 'El nombre del archivo no puede exceder 255 caracteres'
        ],
        'ruta_archivo' => [
            'required' => 'La ruta del archivo es obligatoria',
            'max_length' => 'La ruta del archivo no puede exceder 500 caracteres'
        ],
        'tipo_archivo' => [
            'required' => 'El tipo de archivo es obligatorio',
            'max_length' => 'El tipo de archivo no puede exceder 100 caracteres'
        ],
        'tamano_archivo' => [
            'required' => 'El tamaño del archivo es obligatorio',
            'integer' => 'El tamaño del archivo debe ser un número entero'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser Pendiente, Aprobado o Rechazado'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener documentos de una solicitud específica
     */
    public function getDocumentosSolicitud($solicitudId)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento, becas_documentos_requisitos.orden_verificacion')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->orderBy('becas_documentos_requisitos.orden_verificacion', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener documentos pendientes de una solicitud
     */
    public function getDocumentosPendientes($solicitudId)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento, becas_documentos_requisitos.orden_verificacion')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->where('documentos_solicitud_becas.estado', 'Pendiente')
                   ->orderBy('becas_documentos_requisitos.orden_verificacion', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener documentos aprobados de una solicitud
     */
    public function getDocumentosAprobados($solicitudId)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento, becas_documentos_requisitos.orden_verificacion')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->where('documentos_solicitud_becas.estado', 'Aprobado')
                   ->orderBy('becas_documentos_requisitos.orden_verificacion', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener el siguiente documento pendiente de una solicitud
     */
    public function getSiguienteDocumentoPendiente($solicitudId)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento, becas_documentos_requisitos.orden_verificacion')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->where('documentos_solicitud_becas.estado', 'Pendiente')
                   ->orderBy('becas_documentos_requisitos.orden_verificacion', 'ASC')
                   ->first();
    }

    /**
     * Verificar si todos los documentos de una solicitud están aprobados
     */
    public function todosDocumentosAprobados($solicitudId)
    {
        $totalDocumentos = $this->where('solicitud_beca_id', $solicitudId)->countAllResults();
        $documentosAprobados = $this->where('solicitud_beca_id', $solicitudId)
                                   ->where('estado', 'Aprobado')
                                   ->countAllResults();
        
        return $totalDocumentos > 0 && $totalDocumentos === $documentosAprobados;
    }

    /**
     * Obtener documentos rechazados de una solicitud
     */
    public function getDocumentosRechazados($solicitudId)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento, becas_documentos_requisitos.orden_verificacion')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->where('documentos_solicitud_becas.estado', 'Rechazado')
                   ->orderBy('becas_documentos_requisitos.orden_verificacion', 'ASC')
                   ->findAll();
    }

    /**
     * Contar documentos por estado en una solicitud
     */
    public function contarDocumentosPorEstado($solicitudId)
    {
        return $this->select('estado, COUNT(*) as total')
                   ->where('solicitud_beca_id', $solicitudId)
                   ->groupBy('estado')
                   ->findAll();
    }

    /**
     * Obtener documentos por tipo de archivo
     */
    public function getDocumentosPorTipo($solicitudId, $tipoArchivo)
    {
        return $this->where('solicitud_beca_id', $solicitudId)
                   ->where('tipo_archivo', $tipoArchivo)
                   ->orderBy('fecha_subida', 'ASC')
                   ->findAll();
    }

    /**
     * Buscar documentos por nombre
     */
    public function buscarDocumentosPorNombre($solicitudId, $nombre)
    {
        return $this->select('documentos_solicitud_becas.*, becas_documentos_requisitos.nombre_documento')
                   ->join('becas_documentos_requisitos', 'becas_documentos_requisitos.id = documentos_solicitud_becas.documento_requerido_id')
                   ->where('documentos_solicitud_becas.solicitud_beca_id', $solicitudId)
                   ->like('documentos_solicitud_becas.nombre_archivo', $nombre)
                   ->orLike('becas_documentos_requisitos.nombre_documento', $nombre)
                   ->findAll();
    }
}
