<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\BecaModel;
use App\Models\SolicitudBecaModel;
use App\Models\SolicitudBecaDocumentoModel;
use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use App\Services\EstudianteBecasService;
use App\Security\InputSanitizerTrait;

class BecasController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
    protected $usuarioModel;
    protected $becaModel;
    protected $solicitudBecaModel;
    protected $periodoModel;
    protected $estudianteBecasService;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->becaModel = new BecaModel();
        $this->solicitudBecaModel = new SolicitudBecaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->estudianteBecasService = new EstudianteBecasService();
        $this->db = \Config\Database::connect();
    }

    public function becas()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        try {
            $estudianteId = session('id');
            
            // Verificar habilitación para solicitar becas
            $habilitacion = $this->estudianteBecasService->puedesolicitarBecas($estudianteId);
            
            // Obtener todas las becas disponibles de todos los períodos
            $becasInfo = $this->estudianteBecasService->getTodasLasBecasDisponibles($estudianteId);
            
            // Obtener solicitudes del estudiante
            $solicitudes = $this->estudianteBecasService->getSolicitudesEstudiante($estudianteId);
            
            // Obtener estadísticas
            $estadisticas = $this->estudianteBecasService->getEstadisticasEstudiante($estudianteId);

            // Paginación para solicitudes
            $page = (int)($this->request->getGet('page_solicitudes') ?? 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;
            $totalSolicitudes = count($solicitudes);
            $solicitudes = array_slice($solicitudes, $offset, $perPage);
            $totalPagesSolicitudes = max(1, ceil($totalSolicitudes / $perPage));

            $data = [
                'estudiante' => $this->usuarioModel->find($estudianteId),
                'habilitacion' => $habilitacion,
                'becas_disponibles' => $becasInfo['becas'] ?? [],
                'solicitudes' => $solicitudes,
                'estadisticas' => $estadisticas,
                'puede_solicitar' => $habilitacion['puede_solicitar'],
                'motivo_deshabilitacion' => $habilitacion['puede_solicitar'] ? null : $habilitacion['motivo'],
                'detalles_habilitacion' => $habilitacion['detalles'] ?? [],
                'current_page_solicitudes' => $page,
                'total_pages_solicitudes' => $totalPagesSolicitudes,
                'per_page_solicitudes' => $perPage,
                'total_solicitudes' => $totalSolicitudes
            ];

            return view('estudiante/becas_mejorado', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en becas estudiante: ' . $e->getMessage());
            
            $data = [
                'estudiante' => $this->usuarioModel->find(session('id')),
                'habilitacion' => ['puede_solicitar' => false, 'motivo' => 'Error del sistema'],
                'becas_disponibles' => [],
                'solicitudes' => [],
                'estadisticas' => [],
                'puede_solicitar' => false,
                'motivo_deshabilitacion' => 'Error del sistema. Contacte al administrador.',
                'error' => true
            ];

            return view('estudiante/becas_mejorado', $data);
        }
    }

    public function solicitarBeca()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->getJsonSanitized();
            
            // Usar el período enviado desde el frontend, o el activo como fallback
            $periodoId = $input['periodo_id'] ?? null;
            
            if (!$periodoId) {
                // Obtener período activo como fallback
                $periodoActivo = $this->periodoModel->where('activo', 1)->first();
                if (!$periodoActivo) {
                    return $this->response->setJSON([
                        'success' => false, 
                        'error' => 'No hay período académico activo'
                    ]);
                }
                $periodoId = $periodoActivo['id'];
            }

            $datos = [
                'estudiante_id' => session('id'),
                'beca_id' => $input['beca_id'],
                'periodo_id' => $periodoId,
                'observaciones' => $input['observaciones'] ?? null
            ];

            $resultado = $this->estudianteBecasService->crearSolicitudBeca($datos);
            
            return $this->response->setJSON($resultado);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al solicitar beca: ' . $e->getMessage());
            log_message('error', 'Error al procesar solicitud de beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error al procesar la solicitud'
            ]);
        }
    }

    public function cancelarSolicitudBeca()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $json = $this->getJsonSanitized();
        $id = $json['id'] ?? 0;

        try {
            $solicitud = $this->db->table('solicitudes_becas')
                ->where('id', $id)
                ->where('estudiante_id', session('id'))
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            $this->db->table('solicitudes_becas')
                ->where('id', $id)
                ->where('estudiante_id', session('id'))
                ->update(['estado' => 'Rechazada', 'observaciones' => 'Cancelada por el estudiante']);

            // Eliminar archivos físicos y limpiar registros de documentos asociados
            $documentos = $this->db->table('documentos_solicitud_becas')
                ->where('solicitud_beca_id', $id)
                ->get()
                ->getResultArray();

            foreach ($documentos as $doc) {
                if (!empty($doc['ruta_archivo']) && $doc['ruta_archivo'] !== '/temp/pendiente_subida.tmp') {
                    $rutaArchivo = FCPATH . $doc['ruta_archivo'];
                    if (file_exists($rutaArchivo)) {
                        @unlink($rutaArchivo);
                    }
                }
            }

            $this->db->table('documentos_solicitud_becas')
                ->where('solicitud_beca_id', $id)
                ->update([
                    'nombre_archivo' => 'pendiente_subida.tmp',
                    'ruta_archivo' => '/temp/pendiente_subida.tmp',
                    'estado' => 'Pendiente',
                    'fecha_subida' => null,
                    'tamano_archivo' => null,
                    'tipo_mime' => null
                ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud cancelada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error al cancelar solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cancelar solicitud']);
        }
    }

    /**
     * Devuelve HTML con los detalles de una beca para el modal
     */
    public function detalleBeca($becaId)
    {
        if (!session('id') || session('rol_id') != 1) {
            return 'No autorizado';
        }

        try {
            $beca = $this->db->table('becas b')
                ->select('b.*, p.nombre as periodo_nombre')
                ->join('periodos_academicos p', 'p.id = b.periodo_id', 'left')
                ->where('b.id', $becaId)
                ->get()
                ->getRowArray();

            if (!$beca) {
                return '<div class="alert alert-danger">Beca no encontrada</div>';
            }

            // Parsear documentos requisitos
            $documentos = [];
            if (!empty($beca['documentos_requisitos'])) {
                $documentos = json_decode($beca['documentos_requisitos'], true) ?? [];
            }

            $html = '
            <div class="beca-detalle">
                <div class="text-center mb-4">
                    <h4 class="text-primary">' . esc($beca['nombre']) . '</h4>
                    <span class="badge bg-info fs-6">' . esc($beca['tipo_beca']) . '</span>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Monto:</strong> <span class="text-success">$' . number_format($beca['monto_beca'] ?? 0, 0, ',', '.') . '</span></p>
                        <p><strong>Período:</strong> ' . esc($beca['periodo_nombre'] ?? 'Sin período') . '</p>
                        <p><strong>Cupos disponibles:</strong> ' . ($beca['cupos_disponibles'] ?? 'N/A') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> ' . ($beca['activa'] ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>') . '</p>
                        <p><strong>Puntaje mínimo:</strong> ' . ($beca['puntaje_minimo'] ?? 'N/A') . '</p>
                    </div>
                </div>';

            if (!empty($beca['descripcion'])) {
                $html .= '<div class="mb-3">
                    <h6>Descripción</h6>
                    <p class="text-muted">' . nl2br(esc($beca['descripcion'])) . '</p>
                </div>';
            }

            if (!empty($beca['requisitos'])) {
                $html .= '<div class="mb-3">
                    <h6>Requisitos</h6>
                    <p class="text-muted">' . nl2br(esc($beca['requisitos'])) . '</p>
                </div>';
            }

            if (!empty($documentos)) {
                $html .= '<div class="mb-3">
                    <h6>Documentos Requeridos</h6>
                    <div class="d-flex flex-wrap gap-2">';
                foreach ($documentos as $doc) {
                    $html .= '<span class="badge bg-warning text-dark">' . esc($doc) . '</span>';
                }
                $html .= '</div></div>';
            }

            $html .= '</div>';

            return $html;

        } catch (\Exception $e) {
            log_message('error', 'Error en detalleBeca: ' . $e->getMessage());
            return '<div class="alert alert-danger">Error al cargar detalles de la beca</div>';
        }
    }

    public function obtenerBecasDisponibles()
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            
            // Paginación
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;

            // Verificar si el estudiante tiene una ficha socioeconómica aprobada
            $fichaModel = new \App\Models\FichaSocioeconomicaModel();
            $fichaAprobada = $fichaModel->where('estudiante_id', $estudianteId)
                                       ->where('estado', 'Aprobada')
                                       ->first();

            if (!$fichaAprobada) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe tener una ficha socioeconómica aprobada para solicitar becas'
                ]);
            }

            // Obtener becas activas con paginación
            $becaModel = new \App\Models\BecaModel();
            $totalBecas = $becaModel->where('estado', 'Activa')
                                   ->where('activa', 1)
                                   ->countAllResults();
            $becas = $becaModel->where('estado', 'Activa')
                              ->where('activa', 1)
                              ->limit($perPage, $offset)
                              ->findAll();

            // Verificar si ya tiene solicitudes activas
            $solicitudModel = new \App\Models\SolicitudBecaModel();
            $solicitudesActivas = $solicitudModel->where('estudiante_id', $estudianteId)
                                                ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
                                                ->findAll();

            $becasDisponibles = [];
            foreach ($becas as $beca) {
                // Verificar si ya solicitó esta beca
                $yaSolicitada = false;
                foreach ($solicitudesActivas as $solicitud) {
                    if ($solicitud['beca_id'] == $beca['id']) {
                        $yaSolicitada = true;
                        break;
                    }
                }

                if (!$yaSolicitada) {
                    $becasDisponibles[] = $beca;
                }
            }

            $totalPages = max(1, ceil($totalBecas / $perPage));

            return $this->response->setJSON([
                'success' => true,
                'data' => $becasDisponibles,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'per_page' => $perPage,
                    'total' => $totalBecas
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener becas disponibles: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Obtener estado de una solicitud de beca
     */
    public function estadoSolicitudBeca($id)
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            
            $solicitudModel = new \App\Models\SolicitudBecaModel();
            $solicitud = $solicitudModel->select('solicitudes_becas.*, becas.nombre as beca_nombre, becas.tipo_beca, periodos_academicos.nombre as periodo_nombre')
                                       ->join('becas', 'becas.id = solicitudes_becas.beca_id')
                                       ->join('periodos_academicos', 'periodos_academicos.id = solicitudes_becas.periodo_id')
                                       ->where('solicitudes_becas.id', $id)
                                       ->where('solicitudes_becas.estudiante_id', $estudianteId)
                                       ->first();

            if (!$solicitud) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ]);
            }

            // Obtener documentos de la solicitud
            $documentoModel = new \App\Models\SolicitudBecaDocumentoModel();
            $documentos = $documentoModel->getDocumentosSolicitud($id);

            $solicitud['documentos'] = $documentos;

            return $this->response->setJSON([
                'success' => true,
                'data' => $solicitud
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener estado de solicitud: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    public function informacionBecas()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Información de Becas',
            'descripcion' => 'Información completa sobre becas, ayudas económicas y programas de apoyo'
        ];

        return view('estudiante/informacion/becas', $data);
    }
}
