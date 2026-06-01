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

    /**
     * Obtener períodos vigentes para estudiantes.
     */
    public function getPeriodosVigentesEstudiantes()
    {
        return $this->where('vigente_estudiantes', 1)
                    ->where('activo', 1)
                    ->where('estado', 'Activo')
                    ->findAll();
    }
} 