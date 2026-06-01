<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudBecaModel extends Model
{
    protected $table            = 'solicitudes_becas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'estudiante_id',
        'beca_id',
        'periodo_id',
        'estado',
        'observaciones',
        'fecha_solicitud',
        'fecha_revision',
        'revisado_por',
        'motivo_rechazo',
        'documentos_revisados',
        'total_documentos',
        'documento_actual_revision',
        'puede_solicitar_beca',
        'fecha_aprobacion',
        'fecha_rechazo',
        'porcentaje_avance',
        'documento_actual_verificando',
        'fecha_actualizacion',
        'actualizado_por',
        'observaciones_admin',
        'aprobado_por',
        'rechazado_por'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'estudiante_id' => 'required|integer',
        'beca_id'       => 'required|integer',
        'periodo_id'    => 'required|integer',
        'estado'        => 'required|in_list[Postulada,En Revisión,Aprobada,Rechazada,Lista de Espera]'
    ];

    protected $validationMessages = [
        'estudiante_id' => [
            'required' => 'El ID del estudiante es obligatorio',
            'integer' => 'El ID del estudiante debe ser un número entero'
        ],
        'beca_id' => [
            'required' => 'El ID de la beca es obligatorio',
            'integer' => 'El ID de la beca debe ser un número entero'
        ],
        'periodo_id' => [
            'required' => 'El ID del período es obligatorio',
            'integer' => 'El ID del período debe ser un número entero'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser Postulada, En Revisión, Aprobada, Rechazada o Lista de Espera'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener solicitudes de un estudiante
     */
    public function getSolicitudesEstudiante($estudianteId)
    {
        return $this->select('solicitudes_becas.*, becas.nombre as beca_nombre, becas.tipo_beca, periodos_academicos.nombre as periodo_nombre')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                   ->where('solicitudes_becas.estudiante_id', $estudianteId)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'DESC')
                   ->findAll();
    }

}
