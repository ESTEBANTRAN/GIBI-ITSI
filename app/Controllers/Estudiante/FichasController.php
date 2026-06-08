<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use App\Security\InputSanitizerTrait;

class FichasController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
    protected $usuarioModel;
    protected $fichaModel;
    protected $periodoModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->db = \Config\Database::connect();
    }

    public function fichaSocioeconomica()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $allFichas = $this->fichaModel->getFichasConPeriodo(session('id'));
        $totalFichas = count($allFichas);
        $fichas = array_slice($allFichas, $offset, $perPage);
        $totalPages = max(1, ceil($totalFichas / $perPage));

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'fichas' => $fichas,
            'periodos' => $this->periodoModel->getPeriodosVigentesEstudiantes(),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total' => $totalFichas
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
        $limiteCheck = $this->periodoModel->verificarLimiteFichas($periodo_id);
        if (is_array($limiteCheck) && !$limiteCheck['success']) {
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
        
        // Salida del PDF como descarga nativa en CI4
        $pdfContent = $pdf->Output($filename, 'S');
        return $this->response->download($filename, $pdfContent);
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

    // ========== MÉTODOS PRIVADOS ==========

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
}
