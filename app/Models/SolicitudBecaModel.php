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

    /**
     * Obtener solicitudes por estado
     */
    public function getSolicitudesPorEstado($estado)
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca, periodos_academicos.nombre as periodo_nombre')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                   ->where('solicitudes_becas.estado', $estado)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener solicitudes con filtros
     */
    public function getSolicitudesConFiltros($filtros = [])
    {
        $query = $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca, periodos_academicos.nombre as periodo_nombre')
                     ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                     ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                     ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id');

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $query->where('solicitudes_becas.estado', $filtros['estado']);
        }

        if (isset($filtros['tipo']) && !empty($filtros['tipo'])) {
            $query->where('becas.tipo_beca', $filtros['tipo']);
        }

        if (isset($filtros['periodo']) && !empty($filtros['periodo'])) {
            $query->where('solicitudes_becas.periodo_id', $filtros['periodo']);
        }

        if (isset($filtros['buscar']) && !empty($filtros['buscar'])) {
            $query->groupStart()
                  ->like('usuarios.nombre', $filtros['buscar'])
                  ->orLike('usuarios.apellido', $filtros['buscar'])
                  ->orLike('becas.nombre', $filtros['buscar'])
                  ->groupEnd();
        }

        return $query->orderBy('solicitudes_becas.fecha_solicitud', 'DESC')->findAll();
    }

    /**
     * Obtener solicitud con detalles completos
     */
    public function getSolicitudCompleta($id)
    {
        $solicitud = $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, usuarios.email as estudiante_email, becas.nombre as beca_nombre, becas.tipo_beca, becas.descripcion as beca_descripcion, periodos_academicos.nombre as periodo_nombre')
                          ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                          ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                          ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                          ->where('solicitudes_becas.id', $id)
                          ->first();

        if ($solicitud) {
            // Obtener documentos de la solicitud
            $documentoModel = new \App\Models\SolicitudBecaDocumentoModel();
            $solicitud['documentos'] = $documentoModel->getDocumentosSolicitud($id);
        }

        return $solicitud;
    }

    /**
     * Verificar si un estudiante ya solicitó una beca
     */
    public function estudianteYaSolicito($estudianteId, $becaId, $periodoId)
    {
        return $this->where('estudiante_id', $estudianteId)
                   ->where('beca_id', $becaId)
                   ->where('periodo_id', $periodoId)
                   ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
                   ->countAllResults() > 0;
    }

    /**
     * Obtener estadísticas de solicitudes
     */
    public function getEstadisticasSolicitudes()
    {
        $totalSolicitudes = $this->countAllResults();
        
        $solicitudesPorEstado = $this->select('estado, COUNT(*) as total')
                                    ->groupBy('estado')
                                    ->findAll();

        return [
            'total_solicitudes' => $totalSolicitudes,
            'solicitudes_por_estado' => $solicitudesPorEstado
        ];
    }

    /**
     * Obtener solicitudes por período académico
     */
    public function getSolicitudesPorPeriodo($periodoId)
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->where('solicitudes_becas.periodo_id', $periodoId)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener solicitudes por beca
     */
    public function getSolicitudesPorBeca($becaId)
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, periodos_academicos.nombre as periodo_nombre')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                   ->where('solicitudes_becas.beca_id', $becaId)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'ASC')
                   ->findAll();
    }

    /**
     * Aprobar solicitud
     */
    public function aprobarSolicitud($id, $observaciones = '', $revisadoPor = null)
    {
        $datos = [
            'estado' => 'Aprobada',
            'fecha_revision' => date('Y-m-d H:i:s'),
            'revisado_por' => $revisadoPor,
            'observaciones' => $observaciones,
            'fecha_aprobacion' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $datos);
    }

    /**
     * Rechazar solicitud
     */
    public function rechazarSolicitud($id, $motivoRechazo, $revisadoPor = null)
    {
        $datos = [
            'estado' => 'Rechazada',
            'fecha_revision' => date('Y-m-d H:i:s'),
            'revisado_por' => $revisadoPor,
            'motivo_rechazo' => $motivoRechazo,
            'fecha_rechazo' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $datos);
    }

    /**
     * Colocar en lista de espera
     */
    public function colocarListaEspera($id, $observaciones = '', $revisadoPor = null)
    {
        $datos = [
            'estado' => 'Lista de Espera',
            'fecha_revision' => date('Y-m-d H:i:s'),
            'revisado_por' => $revisadoPor,
            'observaciones' => $observaciones
        ];

        return $this->update($id, $datos);
    }

    /**
     * Cambiar estado a "En Revisión"
     */
    public function cambiarAEnRevision($id, $revisadoPor = null)
    {
        $datos = [
            'estado' => 'En Revisión',
            'fecha_revision' => date('Y-m-d H:i:s'),
            'revisado_por' => $revisadoPor
        ];

        return $this->update($id, $datos);
    }

    /**
     * Obtener solicitudes pendientes de revisión
     */
    public function getSolicitudesPendientesRevision()
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca, periodos_academicos.nombre as periodo_nombre')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                   ->whereIn('solicitudes_becas.estado', ['Postulada', 'En Revisión'])
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener solicitudes por estudiante y período
     */
    public function getSolicitudesEstudiantePeriodo($estudianteId, $periodoId)
    {
        return $this->select('solicitudes_becas.*, becas.nombre as beca_nombre, becas.tipo_beca')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->where('solicitudes_becas.estudiante_id', $estudianteId)
                   ->where('solicitudes_becas.periodo_id', $periodoId)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'DESC')
                   ->findAll();
    }

    /**
     * Verificar si un estudiante tiene solicitudes activas
     */
    public function tieneSolicitudesActivas($estudianteId)
    {
        return $this->where('estudiante_id', $estudianteId)
                   ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
                   ->countAllResults() > 0;
    }

    /**
     * Obtener solicitudes aprobadas por período
     */
    public function getSolicitudesAprobadasPorPeriodo($periodoId)
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca, becas.monto_beca')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->where('solicitudes_becas.periodo_id', $periodoId)
                   ->where('solicitudes_becas.estado', 'Aprobada')
                   ->orderBy('solicitudes_becas.fecha_aprobacion', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener solicitudes rechazadas por período
     */
    public function getSolicitudesRechazadasPorPeriodo($periodoId)
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre, becas.tipo_beca')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->where('solicitudes_becas.periodo_id', $periodoId)
                   ->where('solicitudes_becas.estado', 'Rechazada')
                   ->orderBy('solicitudes_becas.fecha_rechazo', 'DESC')
                   ->findAll();
    }

    /**
     * Actualizar porcentaje de avance
     */
    public function actualizarPorcentajeAvance($id, $porcentaje)
    {
        return $this->update($id, ['porcentaje_avance' => $porcentaje]);
    }

    /**
     * Obtener solicitudes con documentos pendientes
     */
    public function getSolicitudesConDocumentosPendientes()
    {
        return $this->select('solicitudes_becas.*, usuarios.nombre as estudiante_nombre, usuarios.apellido as estudiante_apellido, becas.nombre as beca_nombre')
                   ->join('usuarios', 'usuarios.id = solicitudes_becas.estudiante_id')
                   ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                   ->whereIn('solicitudes_becas.estado', ['Postulada', 'En Revisión'])
                   ->where('solicitudes_becas.porcentaje_avance <', 100)
                   ->orderBy('solicitudes_becas.fecha_solicitud', 'ASC')
                   ->findAll();
    }
}
