<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\FichaSocioeconomicaModel;
use App\Models\BecaModel;
use App\Models\SolicitudBecaModel;
use App\Models\SolicitudAyudaModel;
use App\Models\PeriodoAcademicoModel;
use App\Services\EstudianteBecasService;
use App\Security\InputSanitizerTrait;

class EstudianteController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        // Obtener datos básicos del formulario
        $periodo_id = $this->getPostInt('periodo_id');
        
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
        $campos = $this->getPostSanitized();
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
            log_message('error', 'Error al crear ficha: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error al crear ficha'
            ]);
        }
    }



    public function testCrearFicha()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
            log_message('error', 'Error en testCrearFicha: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $id = $this->getPostInt('id');

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
            log_message('error', 'Error al enviar ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al enviar ficha']);
        }
    }

    public function exportarFichaPDF($id)
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        // Obtener todos los documentos del estudiante de sus solicitudes de becas
        $documentos = $this->db->table('documentos_solicitud_becas dsb')
            ->select('dsb.*, dsb.`tama±o_archivo` as tamano_archivo, bdr.nombre_documento, bdr.descripcion, sb.beca_id, b.nombre as nombre_beca, sb.periodo_id, p.nombre as periodo_nombre')
            ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
            ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
            ->join('becas b', 'b.id = sb.beca_id')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->where('sb.estudiante_id', session('id'))
            ->where('dsb.ruta_archivo IS NOT NULL')
            ->orderBy('sb.periodo_id', 'DESC')
            ->orderBy('dsb.orden_revision', 'ASC')
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
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

            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud cancelada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error al cancelar solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cancelar solicitud']);
        }
    }

    public function crearSolicitudAyuda()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->getPostSanitized();
            
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
            $input = $this->getJsonSanitized();
            $solicitudId = $input['id'] ?? 0;

            // Validar que la solicitud pertenece al estudiante
            $solicitud = $this->solicitudModel->where('id', $solicitudId)->where('id_estudiante', session('id'))->first();
            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            if (empty($input['categoria_id'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Debe seleccionar una categoría']);
            }
            
            $categoriaModel = new \App\Models\CategoriaSolicitudAyudaModel();
            if ($categoriaModel->esOtroAsunto($input['categoria_id']) && empty($input['asunto_personalizado'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Para "Otro Asunto" debe proporcionar una descripción personalizada']);
            }
            
            $data = [
                'asunto' => $input['asunto'],
                'categoria_id' => $input['categoria_id'],
                'asunto_personalizado' => $input['asunto_personalizado'] ?? null,
                'descripcion' => $input['descripcion'],
                'prioridad' => $input['prioridad']
            ];
            
            $this->solicitudModel->update($solicitudId, $data);
            
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

        $json = $this->getJsonSanitized();
        $id = $json['id'] ?? 0;

        try {
            $solicitud = $this->solicitudModel->where('id', $id)->where('id_estudiante', session('id'))->first();
            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            $this->solicitudModel->update($id, ['estado' => 'Cerrada']);
            return $this->response->setJSON(['success' => true, 'message' => 'Solicitud cancelada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error al cancelar solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cancelar solicitud']);
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

        $ficha_id = $this->getPostInt('ficha_id');
        
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
        $campos = $this->getPostSanitized();
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
            log_message('error', 'Error al actualizar ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar ficha']);
        }
    }

    public function actualizarPerfil()
    {
        if (!session('id') || session('rol_id') != 1) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        $data = [
            'nombre' => $this->getPostString('nombre'),
            'apellido' => $this->getPostString('apellido'),
            'cedula' => $this->getPostString('cedula'),
            'email' => $this->getPostString('email'),
            'telefono' => $this->getPostString('telefono'),
            'direccion' => $this->getPostString('direccion'),
            'carrera' => $this->getPostString('carrera'),
            'semestre' => $this->getPostString('semestre')
        ];

        try {
            $this->usuarioModel->update(session('id'), $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Perfil actualizado exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar perfil: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al actualizar perfil']);
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

        // Validar tipo MIME (solo imágenes)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Solo se permiten imágenes (JPG, PNG, GIF, WebP)']);
        }

        // Validar extensión
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, $allowedExts)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Extensión de archivo no permitida']);
        }

        // Validar tamaño (máximo 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'error' => 'La imagen no puede superar los 2MB']);
        }

        try {
            $fileName = $file->getRandomName();
            $uploadDir = ROOTPATH . 'public/uploads/perfiles';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $fileName);

            $this->usuarioModel->update(session('id'), [
                'foto_perfil' => 'uploads/perfiles/' . $fileName
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Foto actualizada exitosamente']);
        } catch (\Exception $e) {
            log_message('error', 'Error al cambiar foto: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cambiar foto']);
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
            log_message('error', 'Error al cambiar contraseña: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error al cambiar contraseña']);
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
} 