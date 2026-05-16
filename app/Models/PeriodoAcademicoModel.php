<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodoAcademicoModel extends Model
{
    protected $table            = 'periodos_academicos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nombre', 
        'descripcion',
        'fecha_inicio', 
        'fecha_fin', 
        'estado',
        'activo',
        'anio_academico',
        'activo_fichas',
        'activo_becas',
        'vigente_estudiantes',
        'limite_fichas',
        'limite_becas',
        'fichas_creadas',
        'becas_asignadas',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'nombre'         => 'required|max_length[100]',
        'fecha_inicio'   => 'required|valid_date',
        'fecha_fin'      => 'required|valid_date',
        'estado'         => 'required|in_list[Activo,Inactivo,Cerrado]',
        'activo'         => 'required|in_list[0,1]',
        'anio_academico' => 'required|max_length[9]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre del período es obligatorio',
            'max_length' => 'El nombre del período no puede exceder 100 caracteres'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es obligatoria',
            'valid_date' => 'La fecha de inicio debe ser una fecha válida'
        ],
        'fecha_fin' => [
            'required' => 'La fecha de fin es obligatoria',
            'valid_date' => 'La fecha de fin debe ser una fecha válida'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser Activo, Inactivo o Cerrado'
        ],
        'activo' => [
            'required' => 'El campo activo es requerido',
            'in_list' => 'El campo activo debe ser 0 o 1'
        ],
        'anio_academico' => [
            'required' => 'El año académico es obligatorio',
            'max_length' => 'El año académico no puede exceder 9 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener período académico activo
     */
    public function getPeriodoActivo()
    {
        return $this->where('estado', 'Activo')
                   ->where('activo', 1)
                   ->where('fecha_inicio <=', date('Y-m-d'))
                   ->where('fecha_fin >=', date('Y-m-d'))
                   ->first();
    }

    /**
     * Obtener períodos activos
     */
    public function getPeriodosActivos()
    {
        return $this->where('estado', 'Activo')
                   ->where('activo', 1)
                   ->orderBy('fecha_inicio', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener períodos por año académico
     */
    public function getPeriodosPorAnio($anio)
    {
        return $this->where('anio_academico', $anio)
                   ->orderBy('fecha_inicio', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener períodos por estado
     */
    public function getPeriodosPorEstado($estado)
    {
        return $this->where('estado', $estado)
                   ->orderBy('fecha_inicio', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener períodos futuros
     */
    public function getPeriodosFuturos()
    {
        return $this->where('fecha_inicio >', date('Y-m-d'))
                   ->where('estado', 'Activo')
                   ->orderBy('fecha_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener períodos pasados
     */
    public function getPeriodosPasados()
    {
        return $this->where('fecha_fin <', date('Y-m-d'))
                   ->orderBy('fecha_fin', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener períodos por rango de fechas
     */
    public function getPeriodosPorRango($fechaInicio, $fechaFin)
    {
        return $this->where('fecha_inicio >=', $fechaInicio)
                   ->where('fecha_fin <=', $fechaFin)
                   ->orderBy('fecha_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Verificar si una fecha está dentro de un período
     */
    public function fechaEnPeriodo($fecha, $periodoId)
    {
        $periodo = $this->find($periodoId);
        if (!$periodo) return false;

        return $fecha >= $periodo['fecha_inicio'] && $fecha <= $periodo['fecha_fin'];
    }

    /**
     * Obtener período actual o próximo
     */
    public function getPeriodoActualOProximo()
    {
        // Primero intentar obtener el período activo
        $periodoActivo = $this->getPeriodoActivo();
        if ($periodoActivo) {
            return $periodoActivo;
        }

        // Si no hay período activo, obtener el próximo
        return $this->where('fecha_inicio >', date('Y-m-d'))
                   ->where('estado', 'Activo')
                   ->orderBy('fecha_inicio', 'ASC')
                    ->first();
    }

    /**
     * Obtener períodos con estadísticas de becas
     */
    public function getPeriodosConEstadisticasBecas()
    {
        $periodos = $this->where('estado', 'Activo')
                        ->orderBy('fecha_inicio', 'DESC')
                        ->findAll();

        $becaModel = new \App\Models\BecaModel();
        
        foreach ($periodos as &$periodo) {
            $becas = $becaModel->where('periodo_id', $periodo['id'])
                               ->where('estado', 'Activa')
                               ->findAll();
            
            $periodo['total_becas'] = count($becas);
            $periodo['becas_activas'] = count(array_filter($becas, fn($b) => $b['activa'] == 1));
        }

        return $periodos;
    }

    /**
     * Verificar si se puede crear más fichas en un período
     */
    public function verificarLimiteFichas($periodoId)
    {
        $periodo = $this->find($periodoId);
        if (!$periodo) {
            return ['success' => false, 'message' => 'Período no encontrado.'];
        }

        if ($periodo['limite_fichas'] > 0 && $periodo['fichas_creadas'] >= $periodo['limite_fichas']) {
            return ['success' => false, 'message' => 'Se ha alcanzado el límite de fichas para este período.'];
        }

        return ['success' => true];
    }

    /**
     * Obtener períodos con estadísticas de solicitudes
     */
    public function getPeriodosConEstadisticasSolicitudes()
    {
        $periodos = $this->where('estado', 'Activo')
                        ->orderBy('fecha_inicio', 'DESC')
                        ->findAll();

        $solicitudModel = new \App\Models\SolicitudBecaModel();
        
        foreach ($periodos as &$periodo) {
            $solicitudes = $solicitudModel->where('periodo_id', $periodo['id'])->findAll();
            
            $periodo['total_solicitudes'] = count($solicitudes);
            $periodo['solicitudes_pendientes'] = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Postulada'));
            $periodo['solicitudes_en_revision'] = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'En Revisión'));
            $periodo['solicitudes_aprobadas'] = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Aprobada'));
            $periodo['solicitudes_rechazadas'] = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Rechazada'));
        }

        return $periodos;
    }

    /**
     * Crear nuevo período académico
     */
    public function crearPeriodo($datos)
    {
        // Validar que no haya solapamiento de fechas
        $solapamiento = $this->where('estado', 'Activo')
                             ->groupStart()
                             ->where('fecha_inicio <=', $datos['fecha_fin'])
                             ->where('fecha_fin >=', $datos['fecha_inicio'])
                             ->groupEnd()
                             ->countAllResults();

        if ($solapamiento > 0) {
            throw new \Exception('Existe un período académico que se solapa con las fechas especificadas');
        }

        return $this->insert($datos);
    }

    /**
     * Actualizar período académico
     */
    public function actualizarPeriodo($id, $datos)
    {
        // Si se están cambiando las fechas, validar solapamiento
        if (isset($datos['fecha_inicio']) || isset($datos['fecha_fin'])) {
            $periodoActual = $this->find($id);
            $fechaInicio = $datos['fecha_inicio'] ?? $periodoActual['fecha_inicio'];
            $fechaFin = $datos['fecha_fin'] ?? $periodoActual['fecha_fin'];

            $solapamiento = $this->where('estado', 'Activo')
                                 ->where('id !=', $id)
                                 ->groupStart()
                                 ->where('fecha_inicio <=', $fechaFin)
                                 ->where('fecha_fin >=', $fechaInicio)
                                 ->groupEnd()
                                 ->countAllResults();

            if ($solapamiento > 0) {
                throw new \Exception('Existe un período académico que se solapa con las fechas especificadas');
            }
        }

        return $this->update($id, $datos);
    }

    /**
     * Activar período académico
     */
    public function activarPeriodo($id)
    {
        // Desactivar todos los demás períodos
        $this->where('id !=', $id)->set(['activo' => 0])->update();
        
        // Activar el período seleccionado
        return $this->update($id, [
            'activo' => 1,
            'estado' => 'Activo'
        ]);
    }

    /**
     * Cerrar período académico
     */
    public function cerrarPeriodo($id)
    {
        return $this->update($id, [
            'estado' => 'Cerrado',
            'activo' => 0
        ]);
    }

    /**
     * Obtener años académicos disponibles
     */
    public function getAniosAcademicos()
    {
        return $this->select('anio_academico')
                   ->distinct()
                   ->orderBy('anio_academico', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener períodos por año académico con estadísticas
     */
    public function getPeriodosPorAnioConEstadisticas($anio)
    {
        $periodos = $this->getPeriodosPorAnio($anio);
        
        $becaModel = new \App\Models\BecaModel();
        $solicitudModel = new \App\Models\SolicitudBecaModel();
        
        foreach ($periodos as &$periodo) {
            // Estadísticas de becas
            $becas = $becaModel->where('periodo_id', $periodo['id'])->findAll();
            $periodo['total_becas'] = count($becas);
            $periodo['becas_activas'] = count(array_filter($becas, fn($b) => $b['estado'] === 'Activa' && $b['activa'] == 1));
            
            // Estadísticas de solicitudes
            $solicitudes = $solicitudModel->where('periodo_id', $periodo['id'])->findAll();
            $periodo['total_solicitudes'] = count($solicitudes);
            $periodo['solicitudes_aprobadas'] = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Aprobada'));
        }
        
        return $periodos;
    }

    /**
     * Verificar si un período está activo
     */
    public function esPeriodoActivo($id)
    {
        $periodo = $this->find($id);
        if (!$periodo) return false;

        return $periodo['estado'] === 'Activo' && 
               $periodo['activo'] == 1 && 
               date('Y-m-d') >= $periodo['fecha_inicio'] && 
               date('Y-m-d') <= $periodo['fecha_fin'];
    }

    /**
     * Obtener períodos próximos a vencer
     */
    public function getPeriodosProximosAVencer($dias = 30)
    {
        $fechaLimite = date('Y-m-d', strtotime("+{$dias} days"));
        
        return $this->where('fecha_fin <=', $fechaLimite)
                   ->where('fecha_fin >=', date('Y-m-d'))
                   ->where('estado', 'Activo')
                   ->orderBy('fecha_fin', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener períodos vigentes para estudiantes
     * Períodos activos que permiten registro de fichas socioeconómicas
     */
    public function getPeriodosVigentesEstudiantes()
    {
        return $this->where('estado', 'Activo')
                   ->where('activo', 1)
                   ->where('activo_fichas', 1)
                   ->where('fecha_inicio <=', date('Y-m-d'))
                   ->where('fecha_fin >=', date('Y-m-d'))
                   ->orderBy('fecha_inicio', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener todos los períodos para estudiantes (vigentes y próximos)
     */
    public function getPeriodosParaEstudiantes()
    {
        return $this->where('estado', 'Activo')
                   ->where('activo', 1)
                   ->where('fecha_fin >=', date('Y-m-d'))
                   ->orderBy('fecha_inicio', 'DESC')
                   ->findAll();
    }

    /**
     * Verificar si un estudiante puede registrar ficha en un período
     */
    public function puedeRegistrarFicha($periodoId)
    {
        $periodo = $this->find($periodoId);
        if (!$periodo) return false;

        return $periodo['estado'] === 'Activo' && 
               $periodo['activo'] == 1 && 
               date('Y-m-d') >= $periodo['fecha_inicio'] && 
               date('Y-m-d') <= $periodo['fecha_fin'];
    }

    /**
     * Obtener período activo para fichas socioeconómicas
     */
    public function getPeriodoActivoParaFichas()
    {
        return $this->where('estado', 'Activo')
                   ->where('activo', 1)
                   ->where('fecha_inicio <=', date('Y-m-d'))
                   ->where('fecha_fin >=', date('Y-m-d'))
                   ->first();
    }
    
    /**
     * Actualizar contador de fichas creadas
     */
    public function actualizarContadorFichas($periodoId, $incremento = 1)
    {
        $periodo = $this->find($periodoId);
        if (!$periodo) return false;
        
        $nuevoContador = ($periodo['fichas_creadas'] ?? 0) + $incremento;
        
        return $this->update($periodoId, ['fichas_creadas' => $nuevoContador]);
    }
    /**
     * Obtener el período académico actual REAL.
     * Usa múltiples criterios: vigente_estudiantes, estado, rango de fechas.
     * Útil para filtrar datos que solo deben mostrarse en el período vigente.
     */
    public function getPeriodoActualReal()
    {
        // Primero: período marcado como vigente para estudiantes y dentro del rango de fechas
        $periodo = $this->where('vigente_estudiantes', 1)
                       ->where('activo', 1)
                       ->where('estado', 'Activo')
                       ->first();
        
        if ($periodo) {
            return $periodo;
        }

        // Segundo: período activo dentro del rango de fechas actual
        $periodo = $this->where('activo', 1)
                       ->where('estado', 'Activo')
                       ->where('fecha_inicio <=', date('Y-m-d'))
                       ->where('fecha_fin >=', date('Y-m-d'))
                       ->first();
        
        if ($periodo) {
            return $periodo;
        }

        // Tercero: último período activo (fallback)
        return $this->where('activo', 1)
                    ->where('estado', 'Activo')
                    ->orderBy('fecha_inicio', 'DESC')
                    ->first();
    }
} 