<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\FichaSocioeconomicaModel;
use App\Models\BecaModel;
use App\Models\SolicitudBecaModel;
use App\Models\SolicitudAyudaModel;
use App\Models\PeriodoAcademicoModel;
use App\Services\EstudianteBecasService;

class EstudianteController extends BaseController
{
    protected $usuarioModel;
    protected $fichaModel;
    protected $becaModel;
    protected $solicitudBecaModel;
    protected $solicitudModel;
    protected $periodoModel;
    protected $estudianteBecasService;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->becaModel = new BecaModel();
        $this->solicitudBecaModel = new SolicitudBecaModel();
        $this->solicitudModel = new SolicitudAyudaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->estudianteBecasService = new EstudianteBecasService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        try {
            // Obtener datos del estudiante
            $estudiante = $this->usuarioModel->find(session('id'));
            
            // Obtener estadísticas del estudiante
            $fichas = $this->fichaModel->where('estudiante_id', session('id'))->findAll();
            $solicitudesBecas = $this->solicitudBecaModel->getSolicitudesEstudiante(session('id'));
            $solicitudesAyuda = $this->solicitudModel->where('id_estudiante', session('id'))->findAll();
            
            $data = [
                'estudiante' => $estudiante,
                'fichas' => $fichas,
                'solicitudes_becas' => $solicitudesBecas,
                'solicitudes_ayuda' => $solicitudesAyuda,
                'estadisticas' => [
                    'total_fichas' => count($fichas),
                    'fichas_aprobadas' => count(array_filter($fichas, function($f) { return $f['estado'] == 'Aprobada'; })),
                    'solicitudes_becas' => count($solicitudesBecas),
                    'becas_aprobadas' => count(array_filter($solicitudesBecas, function($s) { return $s['estado'] == 'Aprobada'; })),
                    'solicitudes_ayuda' => count($solicitudesAyuda),
                    'ayudas_pendientes' => count(array_filter($solicitudesAyuda, function($s) { return $s['estado'] == 'Pendiente'; }))
                ]
            ];

            return view('estudiante/estudiante', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'EstudianteController::index - Error: ' . $e->getMessage());
            // En caso de error, mostrar vista con datos básicos
            $data = [
                'estudiante' => $this->usuarioModel->find(session('id')),
                'fichas' => [],
                'solicitudes_becas' => [],
                'solicitudes_ayuda' => [],
                'estadisticas' => [
                    'total_fichas' => 0,
                    'fichas_aprobadas' => 0,
                    'solicitudes_becas' => 0,
                    'becas_aprobadas' => 0,
                    'solicitudes_ayuda' => 0,
                    'ayudas_pendientes' => 0
                ]
            ];
            return view('estudiante/estudiante', $data);
        }
    }

    public function fichaSocioeconomica()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'fichas' => $this->fichaModel->getFichasConPeriodo(session('id')),
            'periodos' => $this->periodoModel->getPeriodosVigentesEstudiantes()
        ];

        return view('estudiante/ficha_socioeconomica', $data);
    }

    public function crearFicha()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        // Obtener datos básicos del formulario
        $periodo_id = $this->request->getPost('periodo_id');
        
        if (!$periodo_id) {
            return $this->response->setJSON(['success' => false, 'error' => 'Período académico es requerido']);
        }

        // Verificar que el período esté vigente para estudiantes
        $periodo = $this->periodoModel->find($periodo_id);
        if (!$periodo || !$periodo['vigente_estudiantes'] || !$periodo['activo_fichas']) {
            return $this->response->setJSON(['success' => false, 'error' => 'Período académico no disponible para fichas']);
        }

        // Verificar límite de fichas para el período
        if (!$this->periodoModel->verificarLimiteFichas($periodo_id)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Se ha alcanzado el límite de fichas para este período']);
        }

        // Verificar que no exista una ficha para el mismo período
        $fichaExistente = $this->fichaModel->where('estudiante_id', session('id'))
                                          ->where('periodo_id', $periodo_id)
                                          ->first();
        
        if ($fichaExistente) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ya existe una ficha para este período académico']);
        }

        // Recopilar todos los datos del formulario de forma simplificada
        $datosFicha = [];
        
        // Obtener todos los campos del formulario
        $campos = $this->request->getPost();
        foreach ($campos as $campo => $valor) {
            if ($campo !== 'periodo_id') {
                $datosFicha[$campo] = $valor;
            }
        }

        // Procesar arrays especiales
        $datosFamilia = $this->request->getPost('datos_familia');
        if ($datosFamilia) {
            $datosFicha['datos_familia'] = json_decode($datosFamilia, true);
        }

        $serviciosBasicos = $this->request->getPost('servicios_basicos');
        if ($serviciosBasicos) {
            $datosFicha['servicios_basicos'] = json_decode($serviciosBasicos, true);
        }

        $tipoCuentas = $this->request->getPost('tipo_cuentas');
        if ($tipoCuentas) {
            $datosFicha['tipo_cuentas'] = json_decode($tipoCuentas, true);
        }

        $quienEmigrante = $this->request->getPost('quien_emigrante');
        if ($quienEmigrante) {
            $datosFicha['quien_emigrante'] = json_decode($quienEmigrante, true);
        }

        $data = [
            'estudiante_id' => session('id'),
            'periodo_id' => $periodo_id,
            'json_data' => json_encode($datosFicha),
            'estado' => 'Borrador'
        ];

        try {
            $resultado = $this->fichaModel->insert($data);
            
            if ($resultado) {
                // Actualizar contador de fichas del período
                $this->periodoModel->actualizarContadorFichas($periodo_id, 1);
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Ficha creada exitosamente',
                    'ficha_id' => $resultado
                ]);
            } else {
                $errores = $this->fichaModel->errors();
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Error al crear ficha: ' . implode(', ', $errores)
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error al crear ficha: ' . $e->getMessage()
            ]);
        }
    }



    public function testCrearFicha()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        // Crear una ficha de prueba simple
        $data = [
            'estudiante_id' => session('id'),
            'periodo_id' => 1, // Usar el primer período
            'json_data' => json_encode([
                'test' => 'Ficha de prueba',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]),
            'estado' => 'Borrador'
        ];

        try {
            $resultado = $this->fichaModel->insert($data);
            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Ficha de prueba creada exitosamente con ID: ' . $resultado,
                    'data' => $data
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Error en inserción: ' . implode(', ', $this->fichaModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Excepción: ' . $e->getMessage()
            ]);
        }
    }

    public function verFicha($id)
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $ficha = $this->fichaModel->getFichaCompleta($id, session('id'));
        
        if (!$ficha) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
        }

        try {
            $html = $this->generarHTMLFicha($ficha);
            return $this->response->setJSON(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            log_message('error', 'Error al generar HTML de ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al generar la vista de la ficha']);
        }
    }

    public function enviarFicha()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id');

        // Verificar que la ficha pertenezca al estudiante
        $ficha = $this->fichaModel->where('id', $id)
                                 ->where('estudiante_id', session('id'))
                                 ->first();

        if (!$ficha) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
        }

        if ($ficha['estado'] !== 'Borrador') {
            return $this->response->setJSON(['success' => false, 'error' => 'Solo se pueden enviar fichas en estado Borrador']);
        }

        try {
            $this->fichaModel->update($id, [
                'estado' => 'Enviada',
                'fecha_envio' => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'Ficha enviada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al enviar ficha: ' . $e->getMessage()]);
        }
    }

    public function exportarFichaPDF($id)
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $ficha = $this->fichaModel->getFichaCompleta($id, session('id'));
        
        if (!$ficha) {
            return redirect()->to('/estudiante/ficha-socioeconomica');
        }

        // Generar PDF usando TCPDF
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configurar información del documento
        $pdf->SetCreator('Sistema de Bienestar Estudiantil');
        $pdf->SetAuthor('ITSI');
        $pdf->SetTitle('Ficha Socioeconómica - ' . $ficha['nombre_periodo']);
        $pdf->SetSubject('Ficha Socioeconómica');
        
        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        // Configurar auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Agregar página
        $pdf->AddPage();
        
        // Generar contenido HTML
        $html = $this->generarHTMLFicha($ficha);
        
        // Escribir HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Configurar headers para descarga
        $filename = 'Ficha_Socioeconomica_' . $ficha['nombre_periodo'] . '.pdf';
        $filename = str_replace(' ', '_', $filename); // Reemplazar espacios con guiones bajos
        
        // Salida del PDF como descarga
        $pdf->Output($filename, 'D');
    }

    private function generarHTMLFicha($ficha)
    {
        $datos = json_decode($ficha['json_data'], true);
        
        // Función helper para manejar valores de forma segura
        $safeValue = function($value) {
            if (is_array($value)) {
                return implode(', ', array_map(function($item) {
                    return htmlspecialchars(is_string($item) ? $item : (string)$item);
                }, $value));
            }
            return htmlspecialchars($value ?? '');
        };
        
        $html = '
        <div class="ficha-container">
            <div class="ficha-header text-center mb-4">
                <h4 class="text-primary">UNIDAD DE BIENESTAR INSTITUCIONAL</h4>
                <h5 class="text-secondary">FICHA SOCIOECONÓMICA</h5>
                <p class="text-muted">Período: ' . $safeValue($ficha['nombre_periodo']) . '</p>
                <p class="text-muted">Estado: <span class="badge bg-' . $this->getEstadoColor($ficha['estado']) . '">' . $ficha['estado'] . '</span></p>
            </div>';

        // 1. INFORMACIÓN PERSONAL
        $html .= '
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">1. INFORMACIÓN PERSONAL DEL/LA ESTUDIANTE</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Apellidos y Nombres:</strong> ' . $safeValue($datos['apellidos_nombres'] ?? '') . '</p>
                        <p><strong>Nacionalidad:</strong> ' . $safeValue($datos['nacionalidad'] ?? '') . '</p>
                        <p><strong>Cédula:</strong> ' . $safeValue($datos['cedula'] ?? '') . '</p>
                        <p><strong>Lugar y Fecha de Nacimiento:</strong> ' . $safeValue($datos['lugar_nacimiento'] ?? '') . '</p>
                        <p><strong>Edad:</strong> ' . $safeValue($datos['edad'] ?? '') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado Civil:</strong> ' . $safeValue($datos['estado_civil'] ?? '') . '</p>
                        <p><strong>Ciudad:</strong> ' . $safeValue($datos['ciudad'] ?? '') . '</p>
                        <p><strong>Barrio:</strong> ' . $safeValue($datos['barrio'] ?? '') . '</p>
                        <p><strong>Calle Principal:</strong> ' . $safeValue($datos['calle_principal'] ?? '') . '</p>
                        <p><strong>Calle Secundaria:</strong> ' . $safeValue($datos['calle_secundaria'] ?? '') . '</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Etnia:</strong> ' . $safeValue($datos['etnia'] ?? '') . '</p>
                        <p><strong>¿Trabaja?</strong> ' . $safeValue($datos['trabaja'] ?? '') . '</p>
                        <p><strong>Teléfono Domicilio:</strong> ' . $safeValue($datos['telefono_domicilio'] ?? '') . '</p>
                        <p><strong>Celular:</strong> ' . $safeValue($datos['celular'] ?? '') . '</p>
                        <p><strong>Email:</strong> ' . $safeValue($datos['email'] ?? '') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vive con:</strong> ' . $safeValue($datos['vive_con'] ?? '') . '</p>
                        <p><strong>¿Sus padres están separados?</strong> ' . $safeValue($datos['padres_separados'] ?? '') . '</p>
                    </div>
                </div>
            </div>
        </div>';

        // 4. DATOS DEL GRUPO FAMILIAR
        if (isset($datos['datos_familia']) && is_array($datos['datos_familia'])) {
            $html .= '
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">4. DATOS DEL GRUPO FAMILIAR</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre y Apellidos</th>
                                    <th>Parentesco</th>
                                    <th>Edad</th>
                                    <th>Estado Civil</th>
                                    <th>Ocupación</th>
                                    <th>Institución</th>
                                    <th>Ingresos</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>';
            
            foreach ($datos['datos_familia'] as $index => $familiar) {
                $html .= '
                                <tr>
                                    <td>' . ($index + 1) . '</td>
                                    <td>' . $safeValue($familiar['nombre_apellido'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['parentesco'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['edad'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['estado_civil'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['ocupacion'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['institucion'] ?? '') . '</td>
                                    <td>$' . $safeValue($familiar['ingresos'] ?? '') . '</td>
                                    <td>' . $safeValue($familiar['observaciones'] ?? '') . '</td>
                                </tr>';
            }
            
            $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';
        }

        // 5. SITUACIÓN ECONÓMICA
        $html .= '
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">5. SITUACIÓN ECONÓMICA</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Total Ingresos Familiares:</strong> $' . $safeValue($datos['total_ingresos_familiares'] ?? '') . '</p>
                        <p><strong>Total Gastos Familiares:</strong> $' . $safeValue($datos['total_gastos_familiares'] ?? '') . '</p>
                        <p><strong>Diferencia Ingresos-Egresos:</strong> $' . $safeValue($datos['diferencia_ingresos_egresos'] ?? '') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Servicios Básicos:</strong> ' . $safeValue($datos['servicios_basicos'] ?? '') . '</p>
                        <p><strong>Tipo de Cuentas:</strong> ' . $safeValue($datos['tipo_cuentas'] ?? '') . '</p>
                    </div>
                </div>
            </div>
        </div>';

        // 6. SITUACIÓN DE VIVIENDA
        $html .= '
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">6. SITUACIÓN DE VIVIENDA</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo de Vivienda:</strong> ' . $safeValue($datos['tipo_vivienda'] ?? '') . '</p>
                        <p><strong>Condición de la Vivienda:</strong> ' . $safeValue($datos['condicion_vivienda'] ?? '') . '</p>
                        <p><strong>Número de Habitaciones:</strong> ' . $safeValue($datos['numero_habitaciones'] ?? '') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>¿Tiene Préstamos?</strong> ' . $safeValue($datos['registra_prestamos'] ?? '') . '</p>
                        <p><strong>Monto de Préstamos:</strong> $' . $safeValue($datos['monto_prestamos'] ?? '') . '</p>
                        <p><strong>Institución Prestamista:</strong> ' . $safeValue($datos['institucion_prestamista'] ?? '') . '</p>
                    </div>
                </div>
            </div>
        </div>';

        // 7. SITUACIÓN DE SALUD
        $html .= '
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">7. SITUACIÓN DE SALUD</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>¿Hay Enfermedad Grave?</strong> ' . $safeValue($datos['enfermedad_grave'] ?? '') . '</p>
                        <p><strong>Enfermedad:</strong> ' . $safeValue($datos['tipo_enfermedad'] ?? '') . '</p>
                        <p><strong>Familiar Afectado:</strong> ' . $safeValue($datos['familiar_afectado'] ?? '') . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>¿Hay Familiar Emigrante?</strong> ' . $safeValue($datos['familiar_emigrante'] ?? '') . '</p>
                        <p><strong>¿Quién Emigró?</strong> ' . $safeValue($datos['quien_emigrante'] ?? '') . '</p>
                        <p><strong>País de Destino:</strong> ' . $safeValue($datos['pais_destino'] ?? '') . '</p>
                    </div>
                </div>
            </div>
        </div>';

        // 8. COMENTARIOS ADICIONALES
        if (isset($datos['comentarios_estudiante']) && !empty($datos['comentarios_estudiante'])) {
            $html .= '
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">8. COMENTARIOS ADICIONALES</h6>
                </div>
                <div class="card-body">
                    <p>' . $safeValue($datos['comentarios_estudiante']) . '</p>
                </div>
            </div>';
        }

        $html .= '
        </div>';
        
        return $html;
    }

    private function getEstadoColor($estado)
    {
        switch ($estado) {
            case 'Borrador': return 'secondary';
            case 'Enviada': return 'info';
            case 'Revisada': return 'warning';
            case 'Aprobada': return 'success';
            case 'Rechazada': return 'danger';
            default: return 'secondary';
        }
    }

    private function mostrarArray($array)
    {
        if (is_array($array) && !empty($array)) {
            return implode(', ', array_map(function($item) {
                return htmlspecialchars(is_string($item) ? $item : (string)$item);
            }, $array));
        }
        return 'No especificado';
    }

    public function becas()
    {
        if (!session('id') || session('rol_id') != 1) {
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

            $data = [
                'estudiante' => $this->usuarioModel->find($estudianteId),
                'habilitacion' => $habilitacion,
                'becas_disponibles' => $becasInfo['becas'] ?? [],
                'solicitudes' => $solicitudes,
                'estadisticas' => $estadisticas,
                'puede_solicitar' => $habilitacion['puede_solicitar'],
                'motivo_deshabilitacion' => $habilitacion['puede_solicitar'] ? null : $habilitacion['motivo'],
                'detalles_habilitacion' => $habilitacion['detalles'] ?? []
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

    public function solicitudesAyuda()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $categoriaModel = new \App\Models\CategoriaSolicitudAyudaModel();
        
        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'solicitudes' => $this->solicitudModel->where('id_estudiante', session('id'))->orderBy('fecha_solicitud', 'DESC')->findAll(),
            'categorias' => $categoriaModel->getCategoriasActivas()
        ];

        return view('estudiante/solicitudes_ayuda', $data);
    }

    public function documentos()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        // Obtener todos los documentos del estudiante de sus solicitudes de becas
        $documentos = $this->db->table('solicitudes_becas_documentos sbd')
            ->select('sbd.*, bdr.nombre_documento, bdr.descripcion, sb.beca_id, b.nombre as nombre_beca, sb.periodo_id, p.nombre as periodo_nombre')
            ->join('becas_documentos_requisitos bdr', 'bdr.id = sbd.documento_requisito_id')
            ->join('solicitudes_becas sb', 'sb.id = sbd.solicitud_beca_id')
            ->join('becas b', 'b.id = sb.beca_id')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->where('sb.estudiante_id', session('id'))
            ->orderBy('sb.periodo_id', 'DESC')
            ->orderBy('bdr.orden_verificacion', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'documentos' => $documentos
        ];

        return view('estudiante/documentos', $data);
    }

    public function perfil()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        // Obtener fichas rechazadas con comentarios
        $fichasRechazadas = $this->db->table('fichas_socioeconomicas fs')
            ->select('fs.*, p.nombre as periodo_nombre')
            ->join('periodos_academicos p', 'p.id = fs.periodo_id')
            ->where('fs.estudiante_id', session('id'))
            ->where('fs.estado', 'Rechazada')
            ->where('fs.observaciones_admin IS NOT NULL')
            ->where('fs.observaciones_admin !=', '')
            ->orderBy('fs.fecha_revision_admin', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'fichasRechazadas' => $fichasRechazadas
        ];

        return view('estudiante/perfil', $data);
    }

    public function cuenta()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id'))
        ];

        return view('estudiante/cuenta', $data);
    }

    public function solicitarBeca()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
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
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelarSolicitudBeca()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getJSON()->id;

        try {
            $this->becaModel->deleteSolicitud($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud cancelada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cancelar solicitud: ' . $e->getMessage()]);
        }
    }

    public function crearSolicitudAyuda()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getPost();
            
            // Validar que se seleccionó una categoría
            if (empty($input['categoria_id'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Debe seleccionar una categoría']);
            }
            
            // Verificar si es "Otro Asunto" y requiere descripción personalizada
            $categoriaModel = new \App\Models\CategoriaSolicitudAyudaModel();
            if ($categoriaModel->esOtroAsunto($input['categoria_id']) && empty($input['asunto_personalizado'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Para "Otro Asunto" debe proporcionar una descripción personalizada']);
            }
            
            $data = [
                'id_estudiante' => session('id'),
                'asunto' => $input['asunto'],
                'categoria_id' => $input['categoria_id'],
                'asunto_personalizado' => $input['asunto_personalizado'] ?? null,
                'descripcion' => $input['descripcion'],
                'prioridad' => $input['prioridad'],
                'estado' => 'Pendiente',
                'fecha_solicitud' => date('Y-m-d H:i:s')
            ];
            
            $this->solicitudModel->insert($data);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud creada exitosamente']);
            
        } catch (\Exception $e) {
            log_message('error', 'Error creando solicitud de ayuda: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    public function editarSolicitudAyuda()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON();
            
            // Validar que se seleccionó una categoría
            if (empty($input->categoria_id)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Debe seleccionar una categoría']);
            }
            
            // Verificar si es "Otro Asunto" y requiere descripción personalizada
            $categoriaModel = new \App\Models\CategoriaSolicitudAyudaModel();
            if ($categoriaModel->esOtroAsunto($input->categoria_id) && empty($input->asunto_personalizado)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Para "Otro Asunto" debe proporcionar una descripción personalizada']);
            }
            
            $data = [
                'asunto' => $input->asunto,
                'categoria_id' => $input->categoria_id,
                'asunto_personalizado' => $input->asunto_personalizado ?? null,
                'descripcion' => $input->descripcion,
                'prioridad' => $input->prioridad
            ];
            
            $this->solicitudModel->update($input->id, $data);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud actualizada exitosamente']);
            
        } catch (\Exception $e) {
            log_message('error', 'Error editando solicitud de ayuda: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    public function cancelarSolicitudAyuda()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->request->getJSON()->id;

        try {
            $this->solicitudModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud cancelada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cancelar solicitud: ' . $e->getMessage()]);
        }
    }



    public function editarFicha($id)
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $ficha = $this->fichaModel->getFichaCompleta($id, session('id'));
        
        if (!$ficha) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
        }

        if ($ficha['estado'] !== 'Borrador') {
            return $this->response->setJSON(['success' => false, 'error' => 'Solo se pueden editar fichas en estado Borrador']);
        }

        // Devolver los datos de la ficha para cargar en el formulario
        $datos = json_decode($ficha['json_data'], true);
        $datos['ficha_id'] = $ficha['id'];
        $datos['periodo_id'] = $ficha['periodo_id'];
        
        return $this->response->setJSON(['success' => true, 'datos' => $datos]);
    }

    public function actualizarFicha()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $ficha_id = $this->request->getPost('ficha_id');
        
        // Verificar que la ficha pertenezca al estudiante y esté en borrador
        $ficha = $this->fichaModel->where('id', $ficha_id)
                                 ->where('estudiante_id', session('id'))
                                 ->where('estado', 'Borrador')
                                 ->first();

        if (!$ficha) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada o no se puede editar']);
        }

        // Recopilar todos los datos del formulario (igual que crearFicha)
        $datosFicha = [];
        
        // Obtener todos los campos del formulario
        $campos = $this->request->getPost();
        foreach ($campos as $campo => $valor) {
            if ($campo !== 'ficha_id' && $campo !== 'periodo_id') {
                $datosFicha[$campo] = $valor;
            }
        }

        // Procesar arrays especiales
        $datosFamilia = $this->request->getPost('datos_familia');
        if ($datosFamilia) {
            $datosFicha['datos_familia'] = json_decode($datosFamilia, true);
        }

        $serviciosBasicos = $this->request->getPost('servicios_basicos');
        if ($serviciosBasicos) {
            $datosFicha['servicios_basicos'] = json_decode($serviciosBasicos, true);
        }

        $tipoCuentas = $this->request->getPost('tipo_cuentas');
        if ($tipoCuentas) {
            $datosFicha['tipo_cuentas'] = json_decode($tipoCuentas, true);
        }

        $quienEmigrante = $this->request->getPost('quien_emigrante');
        if ($quienEmigrante) {
            $datosFicha['quien_emigrante'] = json_decode($quienEmigrante, true);
        }

        $data = [
            'json_data' => json_encode($datosFicha)
        ];

        try {
            $this->fichaModel->update($ficha_id, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Ficha actualizada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar ficha: ' . $e->getMessage()]);
        }
    }

    public function actualizarPerfil()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'cedula' => $this->request->getPost('cedula'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
            'carrera' => $this->request->getPost('carrera'),
            'semestre' => $this->request->getPost('semestre')
        ];

        try {
            $this->usuarioModel->update(session('id'), $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Perfil actualizado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar perfil: ' . $e->getMessage()]);
        }
    }

    public function cambiarFoto()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $file = $this->request->getFile('foto');
        
        if (!$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Archivo no válido']);
        }

        try {
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/perfiles', $fileName);

            $this->usuarioModel->update(session('id'), [
                'foto_perfil' => 'uploads/perfiles/' . $fileName
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Foto actualizada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cambiar foto: ' . $e->getMessage()]);
        }
    }

    public function cambiarPassword()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $passwordActual = $this->request->getPost('password_actual');
        $passwordNuevo = $this->request->getPost('password_nuevo');

        // Verificar contraseña actual
        $usuario = $this->usuarioModel->find(session('id'));
        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            return $this->response->setJSON(['success' => false, 'error' => 'Contraseña actual incorrecta']);
        }

        try {
            $this->usuarioModel->update(session('id'), [
                'password_hash' => password_hash($passwordNuevo, PASSWORD_DEFAULT)
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'Contraseña cambiada exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cambiar contraseña: ' . $e->getMessage()]);
        }
    }

    public function configurarNotificaciones()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        // Lógica para configurar notificaciones
        return $this->response->setJSON(['success' => true, 'message' => 'Configuración guardada exitosamente']);
    }

    public function exportarDatos()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $usuario = $this->usuarioModel->find(session('id'));
        $fichas = $this->fichaModel->where('estudiante_id', session('id'))->findAll();
        $solicitudesBecas = $this->solicitudBecaModel->getSolicitudesEstudiante(session('id'));
        $solicitudesAyuda = $this->solicitudModel->where('id_estudiante', session('id'))->findAll();

        $datos = [
            'usuario' => $usuario,
            'fichas' => $fichas,
            'solicitudes_becas' => $solicitudesBecas,
            'solicitudes_ayuda' => $solicitudesAyuda,
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ];

        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="datos_estudiante_' . session('id') . '.json"');
        return $this->response->setBody(json_encode($datos, JSON_PRETTY_PRINT));
    }

    public function eliminarCuenta()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $this->usuarioModel->delete(session('id'));
            session()->destroy();
            return $this->response->setJSON(['success' => true, 'message' => 'Cuenta eliminada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'EstudianteController::eliminarCuenta - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar la cuenta']);
        }
    }

    // ========== SECCIÓN DE INFORMACIÓN ==========

    public function informacionServicios()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Servicios de Bienestar Institucional',
            'descripcion' => 'Conoce todos los servicios que ofrece la Unidad de Bienestar Institucional del ITSI'
        ];

        return view('estudiante/informacion/servicios', $data);
    }

    public function informacionBecas()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Información de Becas',
            'descripcion' => 'Información completa sobre becas, ayudas económicas y programas de apoyo'
        ];

        return view('estudiante/informacion/becas', $data);
    }

    public function informacionPsicologia()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Apoyo Psicológico',
            'descripcion' => 'Servicios de atención psicológica y apoyo emocional'
        ];

        return view('estudiante/informacion/psicologia', $data);
    }

    public function informacionSalud()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Servicios de Salud',
            'descripcion' => 'Atención médica, prevención y promoción de la salud'
        ];

        return view('estudiante/informacion/salud', $data);
    }

    public function informacionTrabajoSocial()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Trabajo Social',
            'descripcion' => 'Apoyo socioeconómico y orientación social'
        ];

        return view('estudiante/informacion/trabajo_social', $data);
    }

    public function informacionOrientacionAcademica()
    {
        if (!session('id') || session('rol_id') != 1) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Orientación Académica',
            'descripcion' => 'Asesoramiento académico y apoyo para el desarrollo estudiantil'
        ];

        return view('estudiante/informacion/orientacion_academica', $data);
    }



    // ========================================
    // SISTEMA DE BECAS - MÉTODOS PRINCIPALES
    // ========================================

    /**
     * Obtener becas disponibles para el estudiante
     */
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

            // Obtener becas activas
            $becaModel = new \App\Models\BecaModel();
            $becas = $becaModel->where('estado', 'Activa')
                              ->where('activa', 1)
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

            return $this->response->setJSON([
                'success' => true,
                'data' => $becasDisponibles
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
     * Verificar elegibilidad para una beca específica
     */
    public function verificarElegibilidadBeca()
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            $becaId = $this->request->getPost('beca_id');
            
            if (empty($becaId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de beca es obligatorio'
                ]);
            }

            // Verificar ficha socioeconómica aprobada
            $fichaModel = new \App\Models\FichaSocioeconomicaModel();
            $fichaAprobada = $fichaModel->where('estudiante_id', $estudianteId)
                                       ->where('estado', 'Aprobada')
                                       ->first();

            if (!$fichaAprobada) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe tener una ficha socioeconómica aprobada',
                    'elegible' => false
                ]);
            }

            // Verificar si ya solicitó esta beca
            $solicitudModel = new \App\Models\SolicitudBecaModel();
            $solicitudExistente = $solicitudModel->where('estudiante_id', $estudianteId)
                                                ->where('beca_id', $becaId)
                                                ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
                                                ->first();

            if ($solicitudExistente) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ya tiene una solicitud activa para esta beca',
                    'elegible' => false
                ]);
            }

            // Obtener información de la beca
            $becaModel = new \App\Models\BecaModel();
            $beca = $becaModel->find($becaId);

            if (!$beca || $beca['estado'] !== 'Activa') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La beca no está disponible',
                    'elegible' => false
                ]);
            }

            // Verificar cupos disponibles
            if ($beca['cupos_disponibles'] && $beca['cupos_disponibles'] > 0) {
                $solicitudesAprobadas = $solicitudModel->where('beca_id', $becaId)
                                                      ->where('estado', 'Aprobada')
                                                      ->countAllResults();

                if ($solicitudesAprobadas >= $beca['cupos_disponibles']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'La beca no tiene cupos disponibles',
                        'elegible' => false
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estudiante elegible para la beca',
                'elegible' => true,
                'beca' => $beca
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al verificar elegibilidad: ' . $e->getMessage());
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

    /**
     * Actualizar documentos de una solicitud de beca
     */
    public function actualizarDocumentosBeca()
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            $solicitudId = $this->request->getPost('solicitud_id');
            $documentoRequisitoId = $this->request->getPost('documento_requisito_id');
            
            if (empty($solicitudId) || empty($documentoRequisitoId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parámetros incompletos'
                ]);
            }

            // Verificar que la solicitud pertenece al estudiante
            $solicitudModel = new \App\Models\SolicitudBecaModel();
            $solicitud = $solicitudModel->where('id', $solicitudId)
                                       ->where('estudiante_id', $estudianteId)
                                       ->first();

            if (!$solicitud) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ]);
            }

            // Verificar que la solicitud esté en estado válido para actualizar
            if (!in_array($solicitud['estado'], ['Postulada', 'En Revisión'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede actualizar documentos en el estado actual'
                ]);
            }

            // Procesar archivo subido
            $archivo = $this->request->getFile('documento');
            
            if (!$archivo->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Archivo no válido'
                ]);
            }

            // Validar tipo de archivo
            $tiposPermitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $extension = $archivo->getExtension();
            
            if (!in_array(strtolower($extension), $tiposPermitidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de archivo no permitido'
                ]);
            }

            // Validar tamaño (máximo 10MB)
            if ($archivo->getSize() > 10 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El archivo excede el tamaño máximo permitido (10MB)'
                ]);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = 'doc_' . $solicitudId . '_' . $documentoRequisitoId . '_' . time() . '.' . $extension;
            $rutaDestino = 'uploads/becas/documentos/';
            
            // Crear directorio si no existe
            if (!is_dir($rutaDestino)) {
                mkdir($rutaDestino, 0755, true);
            }

            // Mover archivo
            $archivo->move($rutaDestino, $nombreArchivo);
            $rutaCompleta = $rutaDestino . $nombreArchivo;

            // Guardar información en la base de datos
            $documentoModel = new \App\Models\SolicitudBecaDocumentoModel();
            
            // Verificar si ya existe un documento para este requisito
            $documentoExistente = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                ->where('documento_requisito_id', $documentoRequisitoId)
                                                ->first();

            $datosDocumento = [
                'solicitud_beca_id' => $solicitudId,
                'documento_requisito_id' => $documentoRequisitoId,
                'nombre_archivo' => $archivo->getClientName(),
                'ruta_archivo' => $rutaCompleta,
                'tipo_archivo' => $archivo->getClientMimeType(),
                'tamano_archivo' => $archivo->getSize(),
                'estado' => 'Pendiente',
                'fecha_subida' => date('Y-m-d H:i:s')
            ];

            if ($documentoExistente) {
                // Actualizar documento existente
                $documentoModel->update($documentoExistente['id'], $datosDocumento);
                $mensaje = 'Documento actualizado exitosamente';
            } else {
                // Crear nuevo documento
                $documentoModel->insert($datosDocumento);
                $mensaje = 'Documento subido exitosamente';
            }

            // Actualizar porcentaje de avance de la solicitud
            $this->actualizarPorcentajeAvanceSolicitud($solicitudId);

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensaje
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar documentos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Descargar documento de beca
     */
    public function descargarDocumentoBeca($id)
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            
            $documentoModel = new \App\Models\SolicitudBecaDocumentoModel();
            $documento = $documentoModel->select('solicitudes_becas_documentos.*, solicitudes_becas.estudiante_id')
                                       ->join('solicitudes_becas', 'solicitudes_becas.id = solicitudes_becas_documentos.solicitud_beca_id')
                                       ->where('solicitudes_becas_documentos.id', $id)
                                       ->where('solicitudes_becas.estudiante_id', $estudianteId)
                                       ->first();

            if (!$documento) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ]);
            }

            $rutaArchivo = $documento['ruta_archivo'];
            
            if (!file_exists($rutaArchivo)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Archivo no encontrado en el servidor'
                ]);
            }

            // Descargar archivo
            return $this->response->download($rutaArchivo, $documento['nombre_archivo']);

        } catch (\Exception $e) {
            log_message('error', 'Error al descargar documento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PRIVADOS DE APOYO
    // ========================================

    /**
     * Actualizar porcentaje de avance de una solicitud
     */
    private function actualizarPorcentajeAvanceSolicitud($solicitudId)
    {
        try {
            $documentoModel = new \App\Models\SolicitudBecaDocumentoModel();
            $solicitudModel = new \App\Models\SolicitudBecaModel();
            
            // Obtener total de documentos requisitos
            $totalDocumentos = $documentoModel->where('solicitud_beca_id', $solicitudId)->countAllResults();
            
            if ($totalDocumentos > 0) {
                // Obtener documentos subidos (estado != Pendiente)
                $documentosSubidos = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                    ->where('estado !=', 'Pendiente')
                                                    ->countAllResults();
                
                // Obtener documentos aprobados
                $documentosAprobados = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                    ->where('estado', 'Aprobado')
                                                    ->countAllResults();
                
                // Calcular porcentaje basado en documentos subidos
                $porcentaje = round(($documentosSubidos / $totalDocumentos) * 100, 1);
                
                // Actualizar solicitud
                $solicitudModel->update($solicitudId, [
                    'porcentaje_avance' => $porcentaje,
                    'total_documentos' => $totalDocumentos,
                    'documentos_revisados' => $documentosSubidos
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar porcentaje de avance: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar y gestionar documentos de una solicitud de beca
     */
    public function documentosBeca($solicitudId)
    {
        try {
            // Verificar que la solicitud pertenece al estudiante logueado
            $estudianteId = session('id');
            $solicitud = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, b.nombre as beca_nombre, p.nombre as periodo_nombre')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos p', 'p.id = sb.periodo_id')
                ->where('sb.id', $solicitudId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return redirect()->to('estudiante/becas')->with('error', 'Solicitud no encontrada');
            }

            // Obtener la beca
            $beca = $this->becaModel->find($solicitud['beca_id']);
            if (!$beca) {
                return redirect()->to('estudiante/becas')->with('error', 'Beca no encontrada');
            }

            // Obtener documentos de la solicitud
            $documentos = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, bdr.nombre_documento, bdr.descripcion, bdr.obligatorio')
                ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
                ->where('dsb.solicitud_beca_id', $solicitudId)
                ->orderBy('dsb.orden_revision', 'ASC')
                ->get()
                ->getResultArray();

            // Si no hay documentos, crear la estructura básica
            if (empty($documentos)) {
                // Obtener documentos requeridos de la beca
                $documentosRequeridos = $this->db->table('becas_documentos_requisitos')
                    ->where('beca_id', $solicitud['beca_id'])
                    ->where('estado', 'Activo')
                    ->orderBy('orden_verificacion', 'ASC')
                    ->get()
                    ->getResultArray();

                // Crear registros de documentos para la solicitud
                foreach ($documentosRequeridos as $doc) {
                    $this->db->table('documentos_solicitud_becas')->insert([
                        'solicitud_beca_id' => $solicitudId,
                        'documento_requerido_id' => $doc['id'],
                        'nombre_archivo' => '',
                        'ruta_archivo' => '',
                        'orden_revision' => $doc['orden_verificacion'],
                        'estado' => 'Pendiente',
                        'fecha_subida' => date('Y-m-d H:i:s')
                    ]);
                }

                // Obtener los documentos recién creados
                $documentos = $this->db->table('documentos_solicitud_becas dsb')
                    ->select('dsb.*, bdr.nombre_documento, bdr.descripcion, bdr.obligatorio')
                    ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
                    ->where('dsb.solicitud_beca_id', $solicitudId)
                    ->orderBy('dsb.orden_revision', 'ASC')
                    ->get()
                    ->getResultArray();
            }

            // Calcular progreso
            $totalDocumentos = count($documentos);
            $documentosSubidos = count(array_filter($documentos, fn($d) => $d['estado'] !== 'Pendiente'));
            $documentosAprobados = count(array_filter($documentos, fn($d) => $d['estado'] === 'Aprobado'));
            $porcentajeAvance = $totalDocumentos > 0 ? round(($documentosAprobados / $totalDocumentos) * 100, 1) : 0;

            $data = [
                'solicitud' => $solicitud,
                'beca' => $beca,
                'documentos' => $documentos,
                'estadisticas' => [
                    'total' => $totalDocumentos,
                    'subidos' => $documentosSubidos,
                    'aprobados' => $documentosAprobados,
                    'porcentaje' => $porcentajeAvance
                ]
            ];

            return view('estudiante/documentos_beca', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error mostrando documentos de beca: ' . $e->getMessage());
            return redirect()->to('estudiante/becas')->with('error', 'Error del sistema');
        }
    }

    /**
     * Subir documento para una solicitud de beca
     */
    public function subirDocumento()
    {
        try {
            $estudianteId = session('id');
            $solicitudId = $this->request->getPost('solicitud_id');
            $documentoId = $this->request->getPost('documento_id');

            // Verificar que la solicitud pertenece al estudiante
            $solicitud = $this->db->table('solicitudes_becas')
                ->select('solicitudes_becas.*, b.id as beca_id, p.id as periodo_id')
                ->join('becas b', 'b.id = solicitudes_becas.beca_id')
                ->join('periodos_academicos p', 'p.id = solicitudes_becas.periodo_id')
                ->where('solicitudes_becas.id', $solicitudId)
                ->where('solicitudes_becas.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no encontrada']);
            }

            // Verificar que el documento existe
            $documento = $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->where('solicitud_beca_id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Obtener el archivo
            $archivo = $this->request->getFile('archivo');
            if (!$archivo || !$archivo->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Archivo no válido']);
            }

            // Validar tipo de archivo (solo PDF)
            if ($archivo->getClientMimeType() !== 'application/pdf') {
                return $this->response->setJSON(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
            }

            // Validar tamaño (máximo 2MB)
            if ($archivo->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['success' => false, 'message' => 'El archivo no puede superar 2MB']);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = 'doc_' . $solicitudId . '_' . $documentoId . '_' . time() . '.pdf';
            $rutaDestino = 'uploads/documentos_becas/' . $nombreArchivo;

            // Crear directorio si no existe
            $directorio = FCPATH . 'uploads/documentos_becas/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Mover archivo
            if (!$archivo->move($directorio, $nombreArchivo)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar el archivo']);
            }

            // Actualizar documento en la base de datos
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'nombre_archivo' => $archivo->getClientName(),
                    'ruta_archivo' => $rutaDestino,
                    'estado' => 'En Revision',
                    'fecha_subida' => date('Y-m-d H:i:s'),
                    'tama±o_archivo' => $archivo->getSize(),
                    'tipo_mime' => $archivo->getClientMimeType()
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($solicitudId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documento subido exitosamente',
                'archivo' => $archivo->getClientName()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error subiendo documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Descargar documento
     */
    public function descargarDocumento($documentoId)
    {
        try {
            $estudianteId = session('id');
            
            // Verificar que el documento pertenece a una solicitud del estudiante
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return redirect()->back()->with('error', 'Documento no encontrado');
            }

            $rutaArchivo = FCPATH . $documento['ruta_archivo'];
            if (!file_exists($rutaArchivo)) {
                return redirect()->back()->with('error', 'Archivo no encontrado');
            }

            return $this->response->download($rutaArchivo, $documento['nombre_archivo']);

        } catch (\Exception $e) {
            log_message('error', 'Error descargando documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema');
        }
    }

    /**
     * Eliminar documento
     */
    public function eliminarDocumento()
    {
        try {
            $estudianteId = session('id');
            $documentoId = $this->request->getPost('documento_id');

            // Verificar que el documento pertenece a una solicitud del estudiante
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Solo permitir eliminar si está en estado "En Revision" o "Pendiente"
            if (!in_array($documento['estado'], ['En Revision', 'Pendiente'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se puede eliminar un documento ya revisado']);
            }

            // Eliminar archivo físico
            $rutaArchivo = FCPATH . $documento['ruta_archivo'];
            if (file_exists($rutaArchivo) && $documento['ruta_archivo'] !== '/temp/pendiente_subida.tmp') {
                unlink($rutaArchivo);
            }

            // Actualizar documento en la base de datos
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'nombre_archivo' => 'pendiente_subida.tmp',
                    'ruta_archivo' => '/temp/pendiente_subida.tmp',
                    'estado' => 'Pendiente',
                    'fecha_subida' => null,
                    'tama±o_archivo' => null,
                    'tipo_mime' => null
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($documento['solicitud_beca_id']);

            return $this->response->setJSON(['success' => true, 'message' => 'Documento eliminado exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Actualizar progreso de una solicitud de beca
     */
    private function actualizarProgresoSolicitud($solicitudId)
    {
        try {
            // Contar documentos por estado
            $estados = $this->db->table('documentos_solicitud_becas')
                ->select('estado, COUNT(*) as total')
                ->where('solicitud_beca_id', $solicitudId)
                ->groupBy('estado')
                ->get()
                ->getResultArray();

            $totalDocumentos = 0;
            $documentosSubidos = 0;
            $documentosAprobados = 0;

            foreach ($estados as $estado) {
                $totalDocumentos += $estado['total'];
                if ($estado['estado'] !== 'Pendiente') {
                    $documentosSubidos += $estado['total'];
                }
                if ($estado['estado'] === 'Aprobado') {
                    $documentosAprobados += $estado['total'];
                }
            }

            // Porcentaje basado en documentos subidos (no solo aprobados)
            $porcentajeAvance = $totalDocumentos > 0 ? round(($documentosSubidos / $totalDocumentos) * 100, 1) : 0;

            // Actualizar solicitud
            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'documentos_revisados' => $documentosSubidos,
                    'total_documentos' => $totalDocumentos,
                    'porcentaje_avance' => $porcentajeAvance
                ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando progreso de solicitud: ' . $e->getMessage());
        }
    }
} 