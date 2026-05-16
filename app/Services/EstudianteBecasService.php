<?php

namespace App\Services;

use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use App\Models\BecaModel;
use App\Models\SolicitudBecaModel;
use App\Models\UsuarioModel;
use CodeIgniter\Database\BaseConnection;

class EstudianteBecasService
{
    protected $fichaModel;
    protected $periodoModel;
    protected $becaModel;
    protected $solicitudBecaModel;
    protected $usuarioModel;
    protected $db;

    public function __construct()
    {
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->becaModel = new BecaModel();
        $this->solicitudBecaModel = new SolicitudBecaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Verificar si un estudiante puede solicitar becas
     */
    public function puedesolicitarBecas($estudianteId, $periodoId = null)
    {
        try {
            // Si no se especifica período, usar el activo
            if (!$periodoId) {
                $periodoActivo = $this->periodoModel->where('activo', 1)->first();
                if (!$periodoActivo) {
                    return [
                        'puede_solicitar' => false,
                        'motivo' => 'No hay período académico activo',
                        'detalles' => []
                    ];
                }
                $periodoId = $periodoActivo['id'];
            }

            // Verificar período activo para becas
            $periodo = $this->periodoModel->find($periodoId);
            if (!$periodo || !$periodo['activo_becas']) {
                return [
                    'puede_solicitar' => false,
                    'motivo' => 'El período académico no está activo para becas',
                    'detalles' => [
                        'periodo_activo' => false,
                        'periodo_nombre' => $periodo['nombre'] ?? 'Desconocido'
                    ]
                ];
            }

            // Verificar si existe ficha para el período
            $ficha = $this->fichaModel->where([
                'estudiante_id' => $estudianteId,
                'periodo_id' => $periodoId
            ])->first();

            if (!$ficha) {
                return [
                    'puede_solicitar' => false,
                    'motivo' => 'No se ha creado la ficha socioeconómica para este período',
                    'detalles' => [
                        'ficha_existe' => false,
                        'periodo_nombre' => $periodo['nombre'],
                        'accion_requerida' => 'Crear ficha socioeconómica'
                    ]
                ];
            }

            // Verificar estado de la ficha - Solo se requiere que NO esté en borrador
            if ($ficha['estado'] === 'Borrador') {
                return [
                    'puede_solicitar' => false,
                    'motivo' => 'La ficha socioeconómica está en borrador. Debe completarla y enviarla.',
                    'detalles' => [
                        'ficha_existe' => true,
                        'estado_ficha' => $ficha['estado'],
                        'accion_requerida' => 'Completar y enviar ficha socioeconómica'
                    ]
                ];
            }
            
            // Si la ficha está enviada, revisada o aprobada, puede solicitar becas
            // La evaluación final se hace durante la revisión de documentos

            // Verificar límite de solicitudes por período
            $configMaxSolicitudes = $this->getConfiguracion('max_solicitudes_beca_por_periodo', 3);
            $solicitudesActuales = $this->solicitudBecaModel->where([
                'estudiante_id' => $estudianteId,
                'periodo_id' => $periodoId
            ])->countAllResults();

            if ($solicitudesActuales >= $configMaxSolicitudes) {
                return [
                    'puede_solicitar' => false,
                    'motivo' => "Ha alcanzado el límite máximo de $configMaxSolicitudes solicitudes de beca para este período",
                    'detalles' => [
                        'solicitudes_actuales' => $solicitudesActuales,
                        'limite_maximo' => $configMaxSolicitudes,
                        'solicitudes_restantes' => 0
                    ]
                ];
            }

            // Verificar límite de becas del período
            if ($periodo['limite_becas'] && $periodo['becas_asignadas'] >= $periodo['limite_becas']) {
                return [
                    'puede_solicitar' => false,
                    'motivo' => 'Se ha alcanzado el límite de becas para este período académico',
                    'detalles' => [
                        'becas_asignadas' => $periodo['becas_asignadas'],
                        'limite_becas' => $periodo['limite_becas']
                    ]
                ];
            }

            // Actualizar registro de habilitación
            $this->actualizarHabilitacionBecas($estudianteId, $periodoId, true);

            return [
                'puede_solicitar' => true,
                'motivo' => 'Habilitado para solicitar becas',
                'detalles' => [
                    'ficha_estado' => $ficha['estado'],
                    'ficha_aprobada' => ($ficha['estado'] === 'Aprobada'),
                    'solicitudes_actuales' => $solicitudesActuales,
                    'solicitudes_restantes' => $configMaxSolicitudes - $solicitudesActuales,
                    'periodo_nombre' => $periodo['nombre'],
                    'limite_becas' => $periodo['limite_becas'],
                    'becas_disponibles' => $periodo['limite_becas'] ? ($periodo['limite_becas'] - $periodo['becas_asignadas']) : 'Ilimitadas'
                ]
            ];

        } catch (\Exception $e) {
            log_message('error', 'Error verificando habilitación de becas: ' . $e->getMessage());
            return [
                'puede_solicitar' => false,
                'motivo' => 'Error del sistema verificando habilitación',
                'detalles' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Obtener becas disponibles para un estudiante
     */
    public function getBecasDisponibles($estudianteId, $periodoId = null)
    {
        // Obtener período activo si no se especifica
        if (!$periodoId) {
            $periodoActivo = $this->periodoModel->where('activo', 1)->first();
            if (!$periodoActivo) {
                return [
                    'becas' => [],
                    'puede_solicitar' => false,
                    'motivo' => 'No hay período académico activo',
                    'detalles' => []
                ];
            }
            $periodoId = $periodoActivo['id'];
        }

        // Obtener becas activas para el período
        $becas = $this->becaModel->where([
            'estado' => 'Activa',
            'periodo_vigente_id' => $periodoId
        ])->findAll();

        // Filtrar becas con cupos disponibles
        $becasConCupos = [];
        foreach ($becas as $beca) {
            $infoCupos = $this->getInfoCuposBeca($beca['id'], $periodoId);
            if ($infoCupos && ($infoCupos['sin_limite'] || $infoCupos['cupos_disponibles'] > 0)) {
                $beca['cupos_info'] = $infoCupos;
                $becasConCupos[] = $beca;
            }
        }

        // Obtener solicitudes ya realizadas por el estudiante
        $solicitudesRealizadas = $this->solicitudBecaModel->where([
            'estudiante_id' => $estudianteId,
            'periodo_id' => $periodoId
        ])->findAll();

        $becasSolicitadas = array_column($solicitudesRealizadas, 'beca_id');

        // Filtrar becas ya solicitadas
        $becasDisponibles = array_filter($becas, function($beca) use ($becasSolicitadas) {
            return !in_array($beca['id'], $becasSolicitadas);
        });

        // Verificar habilitación para solicitar
        $verificacion = $this->puedesolicitarBecas($estudianteId, $periodoId);
        
        // Obtener información del período
        $periodo = $this->periodoModel->find($periodoId);
        
        // Agregar información adicional a cada beca
        foreach ($becasDisponibles as &$beca) {
            $beca['puede_solicitar_esta_beca'] = $verificacion['puede_solicitar'];
            $beca['motivo_no_habilitado'] = $verificacion['puede_solicitar'] ? null : $verificacion['motivo'];
            $beca['requisitos_pendientes'] = $verificacion['detalles'] ?? [];
            $beca['periodo_nombre'] = $periodo['nombre'] ?? 'Desconocido';
        }

        return [
            'becas' => array_values($becasDisponibles),
            'puede_solicitar' => $verificacion['puede_solicitar'],
            'motivo' => $verificacion['motivo'],
            'solicitudes_realizadas' => $solicitudesRealizadas,
            'detalles' => $verificacion['detalles'],
            'periodo_actual' => $periodo
        ];
    }

    /**
     * Obtener todas las becas disponibles de todos los períodos
     */
    public function getTodasLasBecasDisponibles($estudianteId)
    {
        try {
            // Obtener solo el período actual (no todos los activos)
            $periodoActual = $this->periodoModel->getPeriodoActualReal();
            
            if (!$periodoActual) {
                return [
                    'becas' => [],
                    'total_periodos' => 0,
                    'total_becas' => 0,
                    'mensaje' => 'No hay período académico vigente'
                ];
            }

            $periodosActivos = [$periodoActual];
            
            $todasLasBecas = [];
            
            foreach ($periodosActivos as $periodo) {
                // Obtener becas del período
                $becasPeriodo = $this->becaModel->where([
                    'estado' => 'Activa',
                    'periodo_vigente_id' => $periodo['id']
                ])->findAll();
                
                // Obtener solicitudes ya realizadas por el estudiante en este período
                $solicitudesRealizadas = $this->solicitudBecaModel->where([
                    'estudiante_id' => $estudianteId,
                    'periodo_id' => $periodo['id']
                ])->findAll();
                
                $becasSolicitadas = array_column($solicitudesRealizadas, 'beca_id');
                
                // Filtrar becas ya solicitadas
                $becasDisponibles = array_filter($becasPeriodo, function($beca) use ($becasSolicitadas) {
                    return !in_array($beca['id'], $becasSolicitadas);
                });
                
                // Verificar habilitación para este período
                $verificacion = $this->puedesolicitarBecas($estudianteId, $periodo['id']);
                
                // Agregar información adicional a cada beca
                foreach ($becasDisponibles as &$beca) {
                    $beca['puede_solicitar_esta_beca'] = $verificacion['puede_solicitar'];
                    $beca['motivo_no_habilitado'] = $verificacion['puede_solicitar'] ? null : $verificacion['motivo'];
                    $beca['requisitos_pendientes'] = $verificacion['detalles'] ?? [];
                    $beca['periodo_nombre'] = $periodo['nombre'];
                    $beca['periodo_id'] = $periodo['id'];
                }
                
                $todasLasBecas = array_merge($todasLasBecas, array_values($becasDisponibles));
            }
            
            return [
                'becas' => $todasLasBecas,
                'total_periodos' => count($periodosActivos),
                'total_becas' => count($todasLasBecas)
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo todas las becas: ' . $e->getMessage());
            return [
                'becas' => [],
                'total_periodos' => 0,
                'total_becas' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar cupos disponibles para una beca
     */
    public function verificarCuposDisponibles($becaId, $periodoId)
    {
        // Obtener la beca
        $beca = $this->becaModel->find($becaId);
        if (!$beca) {
            return ['disponible' => false, 'mensaje' => 'Beca no encontrada'];
        }

        // Si no hay límite de cupos, siempre está disponible
        if (empty($beca['cupos_disponibles']) || $beca['cupos_disponibles'] <= 0) {
            return ['disponible' => true, 'cupos_disponibles' => null, 'mensaje' => 'Sin límite de cupos'];
        }

        // Contar solicitudes activas (Postulada, En Revisión, Aprobada)
        $solicitudesActivas = $this->db->table('solicitudes_becas')
            ->where('beca_id', $becaId)
            ->where('periodo_id', $periodoId)
            ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
            ->countAllResults();

        $cuposDisponibles = $beca['cupos_disponibles'] - $solicitudesActivas;

        return [
            'disponible' => $cuposDisponibles > 0,
            'cupos_disponibles' => $cuposDisponibles,
            'cupos_totales' => $beca['cupos_disponibles'],
            'solicitudes_activas' => $solicitudesActivas,
            'mensaje' => $cuposDisponibles > 0 ? 
                "Cupos disponibles: {$cuposDisponibles}" : 
                "No hay cupos disponibles. Solicitudes activas: {$solicitudesActivas}"
        ];
    }

    /**
     * Crear solicitud de beca
     */
    public function crearSolicitudBeca($datos)
    {
        $this->db->transStart();

        try {
            // Verificar nuevamente que puede solicitar
            $verificacion = $this->puedesolicitarBecas($datos['estudiante_id'], $datos['periodo_id']);
            if (!$verificacion['puede_solicitar']) {
                throw new \Exception($verificacion['motivo']);
            }

            // Verificar que la beca existe y está activa
            $beca = $this->becaModel->where([
                'id' => $datos['beca_id'],
                'estado' => 'Activa',
                'periodo_vigente_id' => $datos['periodo_id']
            ])->first();

            if (!$beca) {
                throw new \Exception('La beca seleccionada no está disponible');
            }

            // Verificar cupos disponibles
            $verificacionCupos = $this->verificarCuposDisponibles($datos['beca_id'], $datos['periodo_id']);
            if (!$verificacionCupos['disponible']) {
                throw new \Exception($verificacionCupos['mensaje']);
            }

            // Crear la solicitud con los campos correctos de la tabla
            $solicitudData = [
                'estudiante_id' => $datos['estudiante_id'],
                'beca_id' => $datos['beca_id'],
                'periodo_id' => $datos['periodo_id'],
                'estado' => 'Postulada', // Usar el enum correcto de la tabla
                'observaciones' => $datos['observaciones'] ?? null,
                'fecha_solicitud' => date('Y-m-d H:i:s'),
                'documentos_revisados' => 0,
                'total_documentos' => 0,
                'documento_actual_revision' => 1,
                'puede_solicitar_beca' => 1,
                'porcentaje_avance' => 0.00
            ];

            $solicitudId = $this->solicitudBecaModel->insert($solicitudData);

            if (!$solicitudId) {
                throw new \Exception('Error creando la solicitud de beca');
            }

            // Marcar la ficha socioeconómica como relacionada con beca
            $this->marcarFichaRelacionadaBeca($datos['estudiante_id'], $datos['periodo_id']);

            // Inicializar documentos requeridos
            $this->inicializarDocumentosRequeridos($solicitudId, $beca['id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos');
            }

            return [
                'success' => true,
                'solicitud_id' => $solicitudId,
                'message' => 'Solicitud de beca creada exitosamente',
                'siguiente_paso' => 'Subir documentos requeridos',
                'cupos_restantes' => $verificacionCupos['cupos_disponibles'] - 1
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creando solicitud de beca: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Marcar ficha socioeconómica como relacionada con beca
     */
    public function marcarFichaRelacionadaBeca($estudianteId, $periodoId)
    {
        try {
            // Buscar la ficha socioeconómica del estudiante en el período
            $ficha = $this->db->table('fichas_socioeconomicas')
                ->where('estudiante_id', $estudianteId)
                ->where('periodo_id', $periodoId)
                ->get()
                ->getRowArray();

            if ($ficha) {
                // Actualizar la ficha para marcarla como relacionada con beca
                $this->db->table('fichas_socioeconomicas')
                    ->where('id', $ficha['id'])
                    ->update([
                        'relacionada_beca' => 1,
                        'fecha_relacion_beca' => date('Y-m-d H:i:s')
                    ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error marcando ficha como relacionada con beca: ' . $e->getMessage());
        }
    }

    /**
     * Liberar cupo cuando se rechaza una solicitud
     */
    public function liberarCupoRechazo($solicitudId)
    {
        try {
            // Obtener la solicitud
            $solicitud = $this->solicitudBecaModel->find($solicitudId);
            if (!$solicitud) {
                return false;
            }

            // Obtener la beca
            $beca = $this->becaModel->find($solicitud['beca_id']);
            if (!$beca || empty($beca['cupos_disponibles'])) {
                return false;
            }

            // Solo liberar cupo si la solicitud estaba en estado activo
            if (in_array($solicitud['estado'], ['Postulada', 'En Revisión'])) {
                // Actualizar cupos disponibles de la beca
                $nuevosCupos = $beca['cupos_disponibles'] + 1;
                $this->becaModel->update($solicitud['beca_id'], [
                    'cupos_disponibles' => $nuevosCupos
                ]);

                log_message('info', "Cupo liberado para beca ID {$solicitud['beca_id']}. Nuevos cupos: {$nuevosCupos}");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error liberando cupo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información de cupos de una beca
     */
    public function getInfoCuposBeca($becaId, $periodoId)
    {
        try {
            $beca = $this->becaModel->find($becaId);
            if (!$beca) {
                return null;
            }

            // Contar solicitudes por estado
            $solicitudesPorEstado = $this->db->table('solicitudes_becas')
                ->select('estado, COUNT(*) as total')
                ->where('beca_id', $becaId)
                ->where('periodo_id', $periodoId)
                ->groupBy('estado')
                ->get()
                ->getResultArray();

            $estadisticas = [];
            foreach ($solicitudesPorEstado as $estado) {
                $estadisticas[$estado['estado']] = $estado['total'];
            }

            $solicitudesActivas = ($estadisticas['Postulada'] ?? 0) + 
                                 ($estadisticas['En Revisión'] ?? 0) + 
                                 ($estadisticas['Aprobada'] ?? 0);

            $cuposDisponibles = $beca['cupos_disponibles'] ? 
                max(0, $beca['cupos_disponibles'] - $solicitudesActivas) : null;

            return [
                'cupos_totales' => $beca['cupos_disponibles'],
                'cupos_disponibles' => $cuposDisponibles,
                'solicitudes_activas' => $solicitudesActivas,
                'solicitudes_por_estado' => $estadisticas,
                'sin_limite' => empty($beca['cupos_disponibles'])
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo información de cupos: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener solicitudes de beca de un estudiante
     */
    public function getSolicitudesEstudiante($estudianteId, $periodoId = null)
    {
        $builder = $this->db->table('solicitudes_becas sb')
            ->select('sb.*, b.nombre as beca_nombre, b.tipo_beca, b.monto_beca, p.nombre as periodo_nombre')
            ->join('becas b', 'b.id = sb.beca_id')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->where('sb.estudiante_id', $estudianteId);

        if ($periodoId) {
            $builder->where('sb.periodo_id', $periodoId);
        }

        $solicitudes = $builder->orderBy('sb.fecha_solicitud', 'DESC')->get()->getResultArray();

        // Obtener detalles de documentos para cada solicitud
        foreach ($solicitudes as &$solicitud) {
            $documentos = $this->getDocumentosSolicitud($solicitud['id']);
            $solicitud['documentos'] = $documentos;
            $solicitud['progreso_documentos'] = $this->calcularProgresoDocumentos($documentos);
        }

        return $solicitudes;
    }

    /**
     * Obtener documentos de una solicitud
     */
    public function getDocumentosSolicitud($solicitudId)
    {
        return $this->db->table('documentos_solicitud_becas dsb')
            ->select('dsb.*, bdr.nombre_documento as documento_nombre, bdr.descripcion, bdr.obligatorio')
            ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
            ->where('dsb.solicitud_beca_id', $solicitudId)
            ->orderBy('dsb.orden_revision', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Calcular progreso de documentos
     */
    private function calcularProgresoDocumentos($documentos)
    {
        if (empty($documentos)) {
            return ['porcentaje' => 0, 'subidos' => 0, 'total' => 0, 'aprobados' => 0, 'pendientes' => 0, 'en_revision' => 0, 'rechazados' => 0, 'porcentaje_aprobados' => 0];
        }

        $total = count($documentos);
        $subidos = 0;
        $aprobados = 0;
        $rechazados = 0;

        foreach ($documentos as $doc) {
            if ($doc['estado'] !== 'Pendiente') {
                $subidos++;
            }
            if ($doc['estado'] === 'Aprobado') {
                $aprobados++;
            }
            if ($doc['estado'] === 'Rechazado') {
                $rechazados++;
            }
        }

        return [
            'porcentaje' => round(($subidos / $total) * 100, 1),
            'subidos' => $subidos,
            'total' => $total,
            'aprobados' => $aprobados,
            'pendientes' => $total - $subidos,
            'en_revision' => $subidos - $aprobados - $rechazados,
            'rechazados' => $rechazados,
            'porcentaje_aprobados' => round(($aprobados / $total) * 100, 1)
        ];
    }

    /**
     * Inicializar documentos requeridos para una solicitud
     */
    private function inicializarDocumentosRequeridos($solicitudId, $becaId)
    {
        $documentosRequeridos = $this->db->table('becas_documentos_requisitos')
            ->where('beca_id', $becaId)
            ->orderBy('obligatorio', 'DESC')
            ->orderBy('nombre_documento', 'ASC')
            ->get()
            ->getResultArray();

        $orden = 1;
        foreach ($documentosRequeridos as $docReq) {
            $this->db->table('documentos_solicitud_becas')->insert([
                'solicitud_beca_id' => $solicitudId,
                'documento_requerido_id' => $docReq['id'],
                'orden_revision' => $orden,
                'nombre_archivo' => 'pendiente_subida.tmp',
                'ruta_archivo' => '/temp/pendiente_subida.tmp',
                'estado' => 'Pendiente'
            ]);
            $orden++;
        }

        // Actualizar total de documentos en la solicitud
        $this->solicitudBecaModel->update($solicitudId, [
            'total_documentos' => count($documentosRequeridos)
        ]);
    }

    /**
     * Actualizar habilitación de becas para un estudiante
     */
    private function actualizarHabilitacionBecas($estudianteId, $periodoId, $puede_solicitar)
    {
        $datos = [
            'estudiante_id' => $estudianteId,
            'periodo_id' => $periodoId,
            'puede_solicitar_becas' => $puede_solicitar ? 1 : 0,
            'ficha_completada' => 1,
            'fecha_habilitacion' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $existing = $this->db->table('estudiantes_habilitacion_becas')
            ->where(['estudiante_id' => $estudianteId, 'periodo_id' => $periodoId])
            ->get()
            ->getRowArray();

        if ($existing) {
            $this->db->table('estudiantes_habilitacion_becas')
                ->where(['estudiante_id' => $estudianteId, 'periodo_id' => $periodoId])
                ->update($datos);
        } else {
            $datos['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('estudiantes_habilitacion_becas')->insert($datos);
        }
    }

    /**
     * Obtener configuración del sistema
     */
    private function getConfiguracion($clave, $default = null)
    {
        // Por ahora, usar valores por defecto ya que la tabla de configuraciones no existe
        // En el futuro, se puede implementar una tabla de configuraciones
        $configuraciones = [
            'max_solicitudes_beca_por_periodo' => 3,
            'limite_becas_por_periodo' => 100,
            'tiempo_limite_solicitud' => 30 // días
        ];
        
        return $configuraciones[$clave] ?? $default;
    }

    /**
     * Obtener estadísticas para el estudiante
     */
    public function getEstadisticasEstudiante($estudianteId)
    {
        $estadisticas = [];

        // Fichas por período
        $fichas = $this->fichaModel->where('estudiante_id', $estudianteId)->findAll();
        $estadisticas['fichas'] = [
            'total' => count($fichas),
            'aprobadas' => count(array_filter($fichas, fn($f) => $f['estado'] === 'Aprobada')),
            'pendientes' => count(array_filter($fichas, fn($f) => in_array($f['estado'], ['Borrador', 'Enviada', 'Revisada'])))
        ];

        // Solicitudes de becas
        $solicitudes = $this->solicitudBecaModel->where('estudiante_id', $estudianteId)->findAll();
        $estadisticas['solicitudes_becas'] = [
            'total' => count($solicitudes),
            'aprobadas' => count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Aprobada')),
            'pendientes' => count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Pendiente')),
            'rechazadas' => count(array_filter($solicitudes, fn($s) => $s['estado'] === 'Rechazada'))
        ];

        // Verificar habilitación actual
        $periodoActivo = $this->periodoModel->where('activo', 1)->first();
        if ($periodoActivo) {
            $habilitacion = $this->puedesolicitarBecas($estudianteId, $periodoActivo['id']);
            $estadisticas['habilitacion_actual'] = $habilitacion;
        }

        return $estadisticas;
    }
}
