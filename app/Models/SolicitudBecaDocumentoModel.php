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

}
