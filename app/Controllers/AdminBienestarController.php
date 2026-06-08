<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AdminBienestarService;
use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use App\Models\BecaModel;
use App\Models\UsuarioModel;
use App\Security\InputSanitizerTrait;
use App\Security\SecurityLogger;

class AdminBienestarController extends BaseController
{
    use InputSanitizerTrait;

    protected $adminService;
    protected $fichaModel;
    protected $periodoModel;
    protected $becaModel;
    protected $usuarioModel;
    protected $db;
    protected $securityLogger;

    public function __construct()
    {
        $this->adminService = new AdminBienestarService();
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->becaModel = new BecaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->db = \Config\Database::connect();
        $this->securityLogger = new SecurityLogger();
    }

    /**
     * Obtener información del usuario actual
     */
    private function getUsuarioActual()
    {
        return [
            'id' => session('id'),
            'nombre' => session('nombre'),
            'email' => session('email'),
            'rol_id' => session('rol_id')
        ];
    }

    /**
     * Verificar permisos de administrador
     */
    private function verificarPermisos()
    {
        if (!session('id') || session('rol_id') != ROLE_ADMIN_BIENESTAR) {
            return false;
        }
        return true;
    }

    // ========================================
    // DASHBOARD Y ESTADÍSTICAS
    // ========================================

    /**
     * @deprecated Usar Admin\DashboardController::dashboard()
     * @see \App\Controllers\Admin\DashboardController
     */
    public function dashboard()
    {
        log_message('debug', 'AdminBienestarController::dashboard() llamado - deprecated');
        $controller = new \App\Controllers\Admin\DashboardController();
        return $controller->dashboard();
    }

    /**
     * @deprecated Usar Admin\DashboardController::getEstadisticas()
     * @see \App\Controllers\Admin\DashboardController
     */
    public function getEstadisticas()
    {
        log_message('debug', 'AdminBienestarController::getEstadisticas() llamado - deprecated');
        $controller = new \App\Controllers\Admin\DashboardController();
        return $controller->getEstadisticas();
    }

    // ========================================
    // GESTIÓN DE FICHAS SOCIOECONÓMICAS
    // ========================================

    /**
     * @deprecated Usar Admin\FichasController::fichasSocioeconomicas()
     * @see \App\Controllers\Admin\FichasController
     */
    public function fichasSocioeconomicas()
    {
        log_message('debug', 'AdminBienestarController::fichasSocioeconomicas() llamado - deprecated');
        $controller = new \App\Controllers\Admin\FichasController();
        return $controller->fichasSocioeconomicas();
    }

    /**
     * Redirect de ruta legacy camelCase a kebab-case
     * @deprecated Usar ver-ficha/{id}
     */
    public function redirectToVerFicha($id): \CodeIgniter\HTTP\RedirectResponse
    {
        log_message('debug', 'Ruta legacy verFicha/{id} usada - redirigiendo a ver-ficha/{id}');
        return redirect()->to('admin-bienestar/ver-ficha/' . $id);
    }

    /**
     * @deprecated Usar Admin\FichasController::verFicha()
     * @see \App\Controllers\Admin\FichasController
     */
    public function verFicha($id)
    {
        log_message('debug', 'AdminBienestarController::verFicha() llamado - deprecated');
        $controller = new \App\Controllers\Admin\FichasController();
        return $controller->verFicha($id);
    }

    /**
     * @deprecated Usar Admin\FichasController::actualizarEstadoFicha()
     * @see \App\Controllers\Admin\FichasController
     */
    public function actualizarEstadoFicha()
    {
        log_message('debug', 'AdminBienestarController::actualizarEstadoFicha() llamado - deprecated');
        $controller = new \App\Controllers\Admin\FichasController();
        return $controller->actualizarEstadoFicha();
    }

    /**
     * Aprobar ficha socioeconómica
     */
    public function aprobarFicha($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Verificar que la ficha existe y está en estado válido para aprobar
            $ficha = $this->fichaModel->find($id);
            
            if (!$ficha) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
            }

            // Verificar que la ficha esté en estado válido para aprobar
            if (!in_array($ficha['estado'], ['Enviada', 'Revisada'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'La ficha no puede ser aprobada en su estado actual']);
            }

            // Actualizar estado a Aprobada
            $resultado = $this->fichaModel->update($id, [
                'estado' => 'Aprobada',
                'fecha_revision' => date('Y-m-d H:i:s'),
                'revisado_por' => session('id'),
                'revisada_por_admin' => 1,
                'fecha_revision_admin' => date('Y-m-d H:i:s'),
                'observaciones_admin' => 'Ficha aprobada por administrador'
            ]);

            if ($resultado) {
                // Log de la acción
                log_message('info', 'Ficha ' . $id . ' aprobada por admin ' . session('id'));
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Ficha aprobada exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Error al aprobar la ficha'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error aprobando ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Rechazar ficha socioeconómica
     */
    public function rechazarFicha($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $motivo = $this->request->getPost('motivo');
            
            if (empty($motivo)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Debe especificar un motivo para el rechazo']);
            }

            // Verificar que la ficha existe y está en estado válido para rechazar
            $ficha = $this->fichaModel->find($id);
            
            if (!$ficha) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
            }

            // Verificar que la ficha esté en estado válido para rechazar
            if (!in_array($ficha['estado'], ['Enviada', 'Revisada'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'La ficha no puede ser rechazada en su estado actual']);
            }

            // Actualizar estado a Rechazada
            $resultado = $this->fichaModel->update($id, [
                'estado' => 'Rechazada',
                'fecha_revision' => date('Y-m-d H:i:s'),
                'revisado_por' => session('id'),
                'revisada_por_admin' => 1,
                'fecha_revision_admin' => date('Y-m-d H:i:s'),
                'observaciones_admin' => 'Ficha rechazada: ' . $motivo
            ]);

            if ($resultado) {
                // Log de la acción
                log_message('info', 'Ficha ' . $id . ' rechazada por admin ' . session('id') . ' - Motivo: ' . $motivo);
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Ficha rechazada exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Error al rechazar la ficha'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error rechazando ficha: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Guardar evaluación socioeconómica (auto-aprueba la ficha + habilita becas)
     */
    public function guardarEvaluacionSocioeconomica()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $ficha_id = $this->getPostInt('ficha_id');
            $categoria = $this->getPostString('categoria');
            $observaciones = $this->getPostString('observaciones');

            if (!$ficha_id || !$categoria) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos incompletos para la evaluación']);
            }

            $ficha = $this->fichaModel->find($ficha_id);
            if (!$ficha) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ficha no encontrada']);
            }

            if (!in_array($ficha['estado'], ['Enviada', 'Revisada'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'La ficha no puede ser evaluada en su estado actual']);
            }

            if ($ficha['revisada_por_admin']) {
                return $this->response->setJSON(['success' => false, 'error' => 'Esta ficha ya fue evaluada anteriormente']);
            }

            $puntaje = match ($categoria) {
                'A' => 3.00,
                'B' => 2.00,
                'C' => 1.00,
                default => 0.00
            };

            $adminId = session('id');
            $now = date('Y-m-d H:i:s');

            $this->fichaModel->update($ficha_id, [
                'revisada_por_admin' => 1,
                'fecha_revision_admin' => $now,
                'puntaje_calculado' => $puntaje,
                'observaciones_admin' => $observaciones,
                'estado' => 'Aprobada',
                'fecha_revision' => $now,
                'revisado_por' => $adminId
            ]);

            $this->securityLogger->logAction($adminId, 'Evaluación Socioeconómica', 'fichas_socioeconomicas', $ficha_id, "Categoría: $categoria, Puntaje: $puntaje");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Evaluación guardada y ficha aprobada exitosamente. El estudiante ahora puede solicitar becas.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en evaluación socioeconómica: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Exportar ficha a PDF usando plantilla profesional
     */
    public function exportarFichaPDF($id)
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $ficha = $this->fichaModel->getFichaCompletaAdmin($id);
            
            if (!$ficha) {
                return redirect()->back()->with('error', 'Ficha no encontrada');
            }

            // Obtener datos completos de la ficha
            $estudiante = $this->usuarioModel->find($ficha['estudiante_id']);
            $periodo = $this->periodoModel->find($ficha['periodo_id']);
            
            // Decodificar datos JSON de la ficha
            $datosFicha = json_decode($ficha['json_data'], true) ?: [];
            
            // Usar el servicio de plantillas para generar PDF profesional
            $plantillaService = new \App\Services\PlantillaPDFService();
            $pdf = $plantillaService->generarFichaSocioeconomicaPDF($ficha, $estudiante, $periodo, $datosFicha);
            
            // Configurar nombre del archivo
            $filename = 'Ficha_Socioeconomica_' . ($estudiante['nombre'] ?? '') . '_' . ($estudiante['apellido'] ?? '') . '_' . ($periodo['nombre'] ?? 'N/A') . '.pdf';
            $filename = str_replace(' ', '_', $filename); // Reemplazar espacios con guiones bajos
            
            // Salida del PDF como descarga nativa en CI4
            $pdfContent = $pdf->Output($filename, 'S');
            return $this->response->download($filename, $pdfContent);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando ficha con plantilla: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema');
        }
    }

    // ========================================
    // GESTIÓN DE BECAS
    // ========================================

    /**
     * Vista principal de becas
     */
    public function becas()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;

            $total = $this->becaModel->countAllResults();
            $becas = $this->becaModel->orderBy('id', 'DESC')->findAll($perPage, $offset);
            $tiposBeca = $this->db->table('becas')->select('tipo_beca')->distinct()->get()->getResultArray();
            $periodos = $this->periodoModel->findAll();

            return view('AdminBienestar/becas', [
                'becas' => $becas,
                'tipos_beca' => $tiposBeca,
                'periodos' => $periodos,
                'usuario' => $this->getUsuarioActual(),
                'current_page' => $page,
                'total_pages' => (int)ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en becas: ' . $e->getMessage());
            return view('AdminBienestar/becas', [
                'becas' => [],
                'tipos_beca' => [],
                'periodos' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando becas'
            ]);
        }
    }

    /**
     * Crear nueva beca
     */
    public function crearBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos de entrada inválidos']);
            }

            $becaModel = new \App\Models\BecaModel();
            
            // Validar datos
            if (!$becaModel->validate($input)) {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Datos inválidos',
                    'validation_errors' => $becaModel->errors()
                ]);
            }

            // Procesar carreras habilitadas
            if (!empty($input['carreras_habilitadas'])) {
                $input['carreras_habilitadas'] = json_encode($input['carreras_habilitadas']);
            }

            // Procesar documentos requeridos
            if (!empty($input['documentos_requisitos'])) {
                $input['documentos_requisitos'] = json_encode($input['documentos_requisitos']);
            }

            $becaId = $becaModel->insert($input);
            
            if (!$becaId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Error creando la beca']);
            }

            $this->logAction('crear_beca', 'becas', $becaId, $input);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Beca creada exitosamente',
                'beca_id' => $becaId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error creando beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Obtener beca por ID
     */
    public function obtenerBeca($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $becaModel = new \App\Models\BecaModel();
            $beca = $becaModel->find($id);
            
            if (!$beca) {
                return $this->response->setJSON(['success' => false, 'error' => 'Beca no encontrada']);
            }

            // Procesar campos JSON
            if (!empty($beca['carreras_habilitadas'])) {
                $beca['carreras_habilitadas'] = json_decode($beca['carreras_habilitadas'], true);
            }
            if (!empty($beca['documentos_requisitos'])) {
                $beca['documentos_requisitos'] = json_decode($beca['documentos_requisitos'], true);
            }

            return $this->response->setJSON(['success' => true, 'beca' => $beca]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Obtener todas las becas (AJAX con filtros y paginaci?n)
     */
    public function obtenerBecas()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;

            $builder = $this->db->table('becas b');
            $builder->select('b.*, p.nombre as periodo_nombre');
            $builder->join('periodos_academicos p', 'p.id = b.periodo_vigente_id', 'left');

            $estado = $this->request->getGet('estado');
            $tipo = $this->request->getGet('tipo');
            $periodo = $this->request->getGet('periodo');
            $buscar = $this->request->getGet('buscar');

            if (!empty($estado)) $builder->where('b.estado', $estado);
            if (!empty($tipo)) $builder->where('b.tipo_beca', $tipo);
            if (!empty($periodo)) $builder->where('b.periodo_vigente_id', $periodo);
            if (!empty($buscar)) $builder->like('b.nombre', $buscar);

            $total = (clone $builder)->countAllResults(false);
            $becas = $builder->orderBy('b.id', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $becas,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => (int)ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo becas: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error al cargar las becas']);
        }
    }

    /**
     * Actualizar beca
     */
    public function actualizarBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input) || empty($input['id'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'ID de beca requerido']);
            }

            $becaModel = new \App\Models\BecaModel();
            $becaId = $input['id'];
            
            // Verificar que la beca existe
            $becaExistente = $becaModel->find($becaId);
            if (!$becaExistente) {
                return $this->response->setJSON(['success' => false, 'error' => 'Beca no encontrada']);
            }

            // Validar datos
            unset($input['id']); // Remover ID para validación
            if (!$becaModel->validate($input)) {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Datos inválidos',
                    'validation_errors' => $becaModel->errors()
                ]);
            }

            // Procesar carreras habilitadas
            if (!empty($input['carreras_habilitadas'])) {
                $input['carreras_habilitadas'] = json_encode($input['carreras_habilitadas']);
            }

            // Procesar documentos requeridos
            if (!empty($input['documentos_requisitos'])) {
                $input['documentos_requisitos'] = json_encode($input['documentos_requisitos']);
            }

            $resultado = $becaModel->update($becaId, $input);
            
            if (!$resultado) {
                return $this->response->setJSON(['success' => false, 'error' => 'Error actualizando la beca']);
            }

            $this->logAction('actualizar_beca', 'becas', $becaId, $input);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Beca actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Eliminar beca
     */
    public function eliminarBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input['id'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'ID de beca requerido']);
            }

            $becaModel = new \App\Models\BecaModel();
            $becaId = $input['id'];
            
            // Verificar que la beca existe
            $beca = $becaModel->find($becaId);
            if (!$beca) {
                return $this->response->setJSON(['success' => false, 'error' => 'Beca no encontrada']);
            }

            // Verificar si puede ser eliminada
            if (!$becaModel->puedeEliminar($becaId)) {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'No se puede eliminar la beca porque tiene solicitudes asociadas'
                ]);
            }

            $resultado = $becaModel->delete($becaId);
            
            if (!$resultado) {
                return $this->response->setJSON(['success' => false, 'error' => 'Error eliminando la beca']);
            }

            $this->logAction('eliminar_beca', 'becas', $becaId, $beca);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Beca eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Cambiar estado de beca (activar/desactivar)
     */
    public function toggleEstadoBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input['id']) || !isset($input['activa'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'ID y estado requeridos']);
            }

            $becaModel = new \App\Models\BecaModel();
            $becaId = $input['id'];
            $nuevoEstado = $input['activa'] ? 1 : 0;
            
            // Verificar que la beca existe
            $beca = $becaModel->find($becaId);
            if (!$beca) {
                return $this->response->setJSON(['success' => false, 'error' => 'Beca no encontrada']);
            }

            $resultado = $becaModel->update($becaId, ['activa' => $nuevoEstado]);
            
            if (!$resultado) {
                return $this->response->setJSON(['success' => false, 'error' => 'Error cambiando estado de la beca']);
            }

            $accion = $nuevoEstado ? 'activar' : 'desactivar';
            $this->logAction($accion . '_beca', 'becas', $becaId, ['activa' => $nuevoEstado]);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Beca ' . ($nuevoEstado ? 'activada' : 'desactivada') . ' exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error cambiando estado de beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }



    /**
     * Exportar becas a Excel
     */
    public function exportarBecas()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $becaModel = new \App\Models\BecaModel();
            $becas = $becaModel->getBecasCompletas();
            
            $filename = 'programas_becas_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
            
            // Encabezados
            fputcsv($output, [
                'ID', 'Nombre', 'Descripción', 'Tipo', 'Estado', 'Monto', 'Cupos Disponibles',
                'Requisitos', 'Documentos Requeridos', 'Período', 'Carreras Habilitadas',
                'Puntaje Mínimo', 'Observaciones', 'Fecha Creación', 'Fecha Actualización'
            ], ';');
            
            // Datos
            foreach ($becas as $beca) {
                $carreras = !empty($beca['carreras_habilitadas']) ? 
                    json_decode($beca['carreras_habilitadas'], true) : [];
                $carrerasText = is_array($carreras) ? implode(', ', $carreras) : '';
                
                $documentos = !empty($beca['documentos_requisitos']) ? 
                    json_decode($beca['documentos_requisitos'], true) : [];
                $documentosText = is_array($documentos) ? implode(', ', $documentos) : '';
                
                fputcsv($output, [
                    $beca['id'],
                    $beca['nombre'],
                    $beca['descripcion'] ?? '',
                    $beca['tipo_beca'],
                    $beca['activa'] ? 'Activa' : 'Inactiva',
                    $beca['monto_beca'] ?? '',
                    $beca['cupos_disponibles'] ?? '',
                    $beca['requisitos'] ?? '',
                    $documentosText,
                    $beca['periodo_nombre'] ?? '',
                    $carrerasText,
                    $beca['puntaje_minimo'] ?? '',
                    $beca['observaciones'] ?? '',
                    $beca['created_at'] ?? '',
                    $beca['updated_at'] ?? ''
                ], ';');
            }
            
            fclose($output);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exportando becas: ' . $e->getMessage());
            return redirect()->to('/admin-bienestar/configuracion-becas')
                ->with('error', 'Error exportando becas');
        }
    }

    /**
     * Obtener estadísticas de becas para gráficos
     */
    public function getEstadisticasBecas()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $becaModel = new \App\Models\BecaModel();
            $estadisticas = $becaModel->getEstadisticasBecas();
            
            return $this->response->setJSON([
                'success' => true, 
                'estadisticas' => $estadisticas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas de becas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema'
            ]);
        }
    }

    // ========================================
    // GESTIÓN DE SOLICITUDES DE BECAS
    // ========================================

    /**
     * Vista de solicitudes de becas
     */
    public function solicitudesBecas()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            // Obtener parámetros de filtro
            $filtros = [
                'estado' => $this->request->getGet('estado'),
                'periodo_id' => $this->request->getGet('periodo_id'),
                'carrera_id' => $this->request->getGet('carrera_id'),
                'tipo_beca' => $this->request->getGet('tipo_beca'),
                'beca_id' => $this->request->getGet('beca_id'),
                'busqueda' => $this->request->getGet('busqueda'),
                'per_page' => 15,
                'page' => $this->request->getGet('page') ?? 1
            ];

            $solicitudes = $this->adminService->getSolicitudesBecasConFiltros($filtros);
            $estadisticas = $this->adminService->getEstadisticasBecas();

            // Obtener datos para filtros
            $periodos = $this->periodoModel->findAll();
            $carreras = $this->db->table('carreras')->where('activa', 1)->get()->getResultArray();
            $becas = $this->becaModel->where('activa', 1)->findAll();
            $tiposBecas = array_column($this->db->table('becas')->distinct()->select('tipo_beca')->get()->getResultArray(), 'tipo_beca');

            $totalSolicitudes = $this->adminService->contarSolicitudesBecas($filtros);
            $pager = [
                'total' => $totalSolicitudes,
                'perPage' => $filtros['per_page'],
                'currentPage' => (int)$filtros['page'],
                'totalPages' => (int)ceil($totalSolicitudes / $filtros['per_page'])
            ];

            return view('AdminBienestar/solicitudes_becas', [
                'solicitudes' => $solicitudes,
                'periodos' => $periodos,
                'carreras' => $carreras,
                'becas' => $becas,
                'tiposBecas' => $tiposBecas,
                'estadisticas' => $estadisticas,
                'filtros' => $filtros,
                'usuario' => $this->getUsuarioActual(),
                'pager' => $pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en solicitudes de becas: ' . $e->getMessage());
            return view('AdminBienestar/solicitudes_becas', [
                'solicitudes' => [],
                'periodos' => [],
                'carreras' => [],
                'becas' => [],
                'tiposBecas' => [],
                'estadisticas' => ['total' => 0, 'aprobadas' => 0, 'pendientes' => 0, 'rechazadas' => 0],
                'filtros' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando solicitudes'
            ]);
        }
    }
    

    


    // ========================================
    // GESTIÓN DE PERÍODOS ACADÉMICOS
    // ========================================

    /**
     * Vista de gestión de períodos académicos
     */
    public function gestionPeriodosAcademicos()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            // Configuración de paginación
            $porPagina = 15;
            $pagina = $this->request->getGet('page') ?? 1;
            $offset = ($pagina - 1) * $porPagina;
            
            // Usar Query Builder para evitar SQL injection
            $totalRegistros = $this->db->table('periodos_academicos')->countAll();
            $totalPaginas = ceil($totalRegistros / $porPagina);
            
            // Obtener períodos con paginación usando Query Builder
            $periodos = $this->db->table('periodos_academicos')
                ->orderBy('fecha_inicio', 'DESC')
                ->limit((int)$porPagina, (int)$offset)
                ->get()
                ->getResultArray();
            
            return view('AdminBienestar/gestion_periodos_academicos', [
                'periodos' => $periodos,
                'usuario' => $this->getUsuarioActual(),
                'paginacion' => [
                    'pagina_actual' => (int)$pagina,
                    'total_paginas' => (int)$totalPaginas,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalRegistros,
                    'offset' => $offset
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en períodos académicos: ' . $e->getMessage());
            return view('AdminBienestar/gestion_periodos_academicos', [
                'periodos' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando períodos'
            ]);
        }
    }



    // ========================================
    // GESTIÓN DE USUARIOS
    // ========================================

    /**
     * Vista de gestión de estudiantes con historial completo
     */
    public function usuarios()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            // Configuración de paginación
            $porPagina = 15;
            $pagina = $this->request->getGet('page') ?? 1;
            $offset = ($pagina - 1) * $porPagina;
            
            // Usar Query Builder para evitar SQL injection
            $totalRegistros = $this->db->table('usuarios u')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('u.rol_id', ROLE_ESTUDIANTE)
                ->countAllResults();
            $totalPaginas = ceil($totalRegistros / $porPagina);
            
            // Obtener estudiantes con información completa + LIMIT para paginación
            $estudiantes = $this->db->table('usuarios u')
                ->select('u.*, c.nombre as carrera_nombre')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('u.rol_id', ROLE_ESTUDIANTE)
                ->orderBy('u.created_at', 'DESC')
                ->limit((int)$porPagina, (int)$offset)
                ->get()
                ->getResultArray();

            // Obtener estadísticas agregadas en 2 consultas (reemplaza N+1*4)
            if (!empty($estudiantes)) {
                $ids = array_column($estudiantes, 'id');
                $idMap = array_flip($ids);

                // 1. Contar fichas, becas y ayudas por estudiante en una sola consulta
                $counts = $this->db->query("
                    SELECT u.id,
                        COUNT(DISTINCT fs.id) as total_fichas,
                        COUNT(DISTINCT sb.id) as total_becas,
                        COUNT(DISTINCT sa.id) as total_ayudas
                    FROM usuarios u
                    LEFT JOIN fichas_socioeconomicas fs ON fs.estudiante_id = u.id
                    LEFT JOIN solicitudes_becas sb ON sb.estudiante_id = u.id
                    LEFT JOIN solicitudes_ayuda sa ON sa.id_estudiante = u.id
                    WHERE u.id IN (" . implode(',', $ids) . ")
                    GROUP BY u.id
                ")->getResultArray();

                foreach ($counts as $row) {
                    $idx = $idMap[$row['id']];
                    $estudiantes[$idx]['total_fichas'] = (int)$row['total_fichas'];
                    $estudiantes[$idx]['total_becas'] = (int)$row['total_becas'];
                    $estudiantes[$idx]['total_ayudas'] = (int)$row['total_ayudas'];
                }

                // 2. Obtener última actividad de todos en una sola consulta
                $actividades = $this->db->query("
                    SELECT estudiante_id, fecha_actividad, tipo FROM (
                        SELECT estudiante_id, fecha_creacion as fecha_actividad, 'ficha' as tipo FROM fichas_socioeconomicas WHERE estudiante_id IN (" . implode(',', $ids) . ")
                        UNION ALL
                        SELECT estudiante_id, fecha_solicitud as fecha_actividad, 'beca' as tipo FROM solicitudes_becas WHERE estudiante_id IN (" . implode(',', $ids) . ")
                        UNION ALL
                        SELECT id_estudiante, fecha_solicitud as fecha_actividad, 'ayuda' as tipo FROM solicitudes_ayuda WHERE id_estudiante IN (" . implode(',', $ids) . ")
                    ) AS actividades
                    ORDER BY estudiante_id, fecha_actividad DESC
                ")->getResultArray();

                $lastActivity = [];
                foreach ($actividades as $a) {
                    $eid = $a['estudiante_id'];
                    if (!isset($lastActivity[$eid])) {
                        $lastActivity[$eid] = $a;
                    }
                }

                foreach ($estudiantes as &$est) {
                    $est['total_fichas'] = $est['total_fichas'] ?? 0;
                    $est['total_becas'] = $est['total_becas'] ?? 0;
                    $est['total_ayudas'] = $est['total_ayudas'] ?? 0;
                    $la = $lastActivity[$est['id']] ?? null;
                    $est['ultima_actividad'] = $la ? $la['fecha_actividad'] : null;
                    $est['tipo_ultima_actividad'] = $la ? $la['tipo'] : null;
                }
                unset($est);
            }

            $carreras = $this->db->table('carreras')->where('activa', 1)->get()->getResultArray();

            return view('AdminBienestar/usuarios', [
                'estudiantes' => $estudiantes,
                'carreras' => $carreras,
                'usuario' => $this->getUsuarioActual(),
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalRegistros,
                    'offset' => $offset
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en estudiantes: ' . $e->getMessage());
            
            $carreras = [
                ['id' => 1, 'nombre' => 'Ingeniería de Sistemas'],
                ['id' => 2, 'nombre' => 'Administración de Empresas']
            ];
            
            return view('AdminBienestar/usuarios', [
                'estudiantes' => [],
                'carreras' => $carreras,
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando estudiantes'
            ]);
        }
    }

    /**
     * Obtener historial completo de un estudiante
     */
    public function historialEstudiante($estudianteId)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Información básica del estudiante
            $estudiante = $this->db->table('usuarios u')
                ->select('u.*, c.nombre as carrera_nombre')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('u.id', $estudianteId)
                ->where('u.rol_id', 1)
                ->get()
                ->getRowArray();

            if (!$estudiante) {
                return $this->response->setJSON(['success' => false, 'error' => 'Estudiante no encontrado']);
            }

            // Fichas socioeconómicas
            $fichas = $this->db->table('fichas_socioeconomicas fs')
                ->select('fs.*, p.nombre as periodo_nombre')
                ->join('periodos_academicos p', 'p.id = fs.periodo_id')
                ->where('fs.estudiante_id', $estudianteId)
                ->orderBy('fs.fecha_creacion', 'DESC')
                ->get()
                ->getResultArray();

            // Solicitudes de becas
            $solicitudesBecas = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, b.nombre as nombre_beca, p.nombre as periodo_nombre')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos p', 'p.id = sb.periodo_id')
                ->where('sb.estudiante_id', $estudianteId)
                ->orderBy('sb.fecha_solicitud', 'DESC')
                ->get()
                ->getResultArray();

            // Solicitudes de ayuda
            $solicitudesAyuda = $this->db->table('solicitudes_ayuda sa')
                ->select('sa.*, sa.asunto as tipo_ayuda_nombre')
                ->where('sa.id_estudiante', $estudianteId)
                ->orderBy('sa.fecha_solicitud', 'DESC')
                ->get()
                ->getResultArray();

            // Documentos de becas
            $documentos = $this->db->table('documentos_solicitud_becas sbd')
                ->select('sbd.*, bdr.nombre_documento, b.nombre as nombre_beca')
                ->join('becas_documentos_requisitos bdr', 'bdr.id = sbd.documento_requerido_id')
                ->join('solicitudes_becas sb', 'sb.id = sbd.solicitud_beca_id')
                ->join('becas b', 'b.id = sb.beca_id')
                ->where('sb.estudiante_id', $estudianteId)
                ->orderBy('sbd.id', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'estudiante' => $estudiante,
                'fichas' => $fichas,
                'solicitudes_becas' => $solicitudesBecas,
                'solicitudes_ayuda' => $solicitudesAyuda,
                'documentos' => $documentos
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo historial del estudiante: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error obteniendo historial'
            ]);
        }
    }

    // ========================================
    // REPORTES Y EXPORTACIÓN
    // ========================================



    /**
     * Generar reporte (PDF Ejecutivo y Personalizados)
     */
    public function generarReporte()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setStatusCode(403)->setBody('No autorizado');
        }

        try {
            $tipo = $this->request->getPost('tipo_reporte') ?? 'ejecutivo';
            $formato = $this->request->getPost('formato') ?? 'pdf';
            $preview = $this->request->getPost('preview') === 'true';
            $periodoId = $this->request->getPost('periodo_id');
            $fechaDesde = $this->request->getPost('fecha_desde');
            $fechaHasta = $this->request->getPost('fecha_hasta');
            
            $db = \Config\Database::connect();
            $datos = [];
            $titulo = 'Reporte Ejecutivo - Bienestar Estudiantil';
            
            // Recolectar datos según el tipo
            if ($tipo === 'fichas') {
                $titulo = 'Reporte de Fichas Socioeconómicas';
                $builder = $db->table('fichas_socioeconomicas f')
                    ->select('f.id, u.nombre, u.apellido, u.cedula, f.estado, f.fecha_creacion, p.nombre as periodo')
                    ->join('usuarios u', 'u.id = f.estudiante_id', 'left')
                    ->join('periodos_academicos p', 'p.id = f.periodo_id', 'left');
                if (!empty($periodoId)) $builder->where('f.periodo_id', $periodoId);
                if (!empty($fechaDesde)) $builder->where('DATE(f.fecha_creacion) >=', $fechaDesde);
                if (!empty($fechaHasta)) $builder->where('DATE(f.fecha_creacion) <=', $fechaHasta);
                $datos = $builder->orderBy('f.fecha_creacion', 'DESC')->get()->getResultArray();
            } 
            elseif ($tipo === 'becas') {
                $titulo = 'Reporte de Solicitudes de Becas';
                $builder = $db->table('solicitudes_becas sb')
                    ->select('sb.id, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, b.nombre as beca, sb.estado, sb.fecha_solicitud')
                    ->join('usuarios u', 'u.id = sb.estudiante_id', 'left')
                    ->join('becas b', 'b.id = sb.beca_id', 'left');
                if (!empty($periodoId)) $builder->where('sb.periodo_id', $periodoId);
                if (!empty($fechaDesde)) $builder->where('DATE(sb.fecha_solicitud) >=', $fechaDesde);
                if (!empty($fechaHasta)) $builder->where('DATE(sb.fecha_solicitud) <=', $fechaHasta);
                $datos = $builder->orderBy('sb.fecha_solicitud', 'DESC')->get()->getResultArray();
            }
            elseif ($tipo === 'usuarios') {
                $titulo = 'Reporte de Usuarios Registrados';
                $builder = $db->table('usuarios u')
                    ->select('u.id, u.nombre, u.apellido, u.cedula, u.email, r.nombre as rol, u.estado')
                    ->join('roles r', 'r.id = u.rol_id', 'left');
                if (!empty($fechaDesde)) $builder->where('DATE(u.created_at) >=', $fechaDesde);
                if (!empty($fechaHasta)) $builder->where('DATE(u.created_at) <=', $fechaHasta);
                $datos = $builder->orderBy('u.created_at', 'DESC')->get()->getResultArray();
            }
            elseif ($tipo === 'general') {
                $titulo = 'Reporte General - Panorama Completo';
                $builder = $db->table('usuarios u')
                    ->select('u.cedula, CONCAT(u.nombre, " ", u.apellido) as estudiante, IFNULL(f.estado, "Sin Ficha") as estado_ficha, IFNULL(b.nombre, "Sin Solicitud") as beca_solicitada, IFNULL(sb.estado, "N/A") as estado_beca')
                    ->join('fichas_socioeconomicas f', 'f.estudiante_id = u.id', 'left')
                    ->join('solicitudes_becas sb', 'sb.estudiante_id = u.id', 'left')
                    ->join('becas b', 'b.id = sb.beca_id', 'left')
                    ->where('u.rol_id', 1);
                if (!empty($periodoId)) {
                    $builder->groupStart()
                        ->where('f.periodo_id', $periodoId)
                        ->orWhere('sb.periodo_id', $periodoId)
                    ->groupEnd();
                }
                $builder->groupBy('u.id'); // Evitar duplicados múltiples si tienen varias
                $datos = $builder->orderBy('u.nombre', 'ASC')->get()->getResultArray();
            }
            else {
                // Estadisticas o ejecutivo
                $titulo = 'Reporte de Estadísticas Avanzadas';
                $totalEstudiantes = $db->table('usuarios')->where('rol_id', 1)->countAllResults();
                $totalBecas = $db->table('becas')->where('activa', 1)->countAllResults();
                $totalSolicitudes = $db->table('solicitudes_becas')->countAllResults();
                $fichasAprobadas = $db->table('fichas_socioeconomicas')->where('estado', 'Aprobada')->countAllResults();
                $fichasRechazadas = $db->table('fichas_socioeconomicas')->where('estado', 'Rechazada')->countAllResults();
                $becasAprobadas = $db->table('solicitudes_becas')->where('estado', 'Aprobada')->countAllResults();
                
                $datos = [
                    ['Metrica' => 'Total Estudiantes Registrados', 'Valor' => $totalEstudiantes],
                    ['Metrica' => 'Total Becas Activas en Sistema', 'Valor' => $totalBecas],
                    ['Metrica' => 'Fichas Socioeconómicas Aprobadas', 'Valor' => $fichasAprobadas],
                    ['Metrica' => 'Fichas Socioeconómicas Rechazadas', 'Valor' => $fichasRechazadas],
                    ['Metrica' => 'Total Solicitudes de Beca', 'Valor' => $totalSolicitudes],
                    ['Metrica' => 'Becas Otorgadas (Aprobadas)', 'Valor' => $becasAprobadas],
                ];
            }

            // Si es preview, retornar JSON
            if ($preview) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'titulo' => $titulo,
                        'total_registros' => count($datos),
                        'muestra' => array_slice($datos, 0, 5) // Muestra de 5
                    ]
                ]);
            }

            // Generación de Archivo Físico
            if ($formato === 'csv' || $formato === 'excel') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=reporte_' . strtolower($tipo) . '_' . date('YmdHis') . '.csv');
                $output = fopen('php://output', 'w');
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                
                if (count($datos) > 0) {
                    fputcsv($output, array_keys($datos[0])); // Encabezados
                    foreach ($datos as $row) {
                        fputcsv($output, array_values($row));
                    }
                } else {
                    fputcsv($output, ['No hay datos para los filtros seleccionados']);
                }
                fclose($output);
                exit;
            } 
            else {
                // Generar PDF
                $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Sistema GIBI-ITSI');
                $pdf->SetTitle($titulo);
                $pdf->SetMargins(15, 15, 15);
                $pdf->AddPage();
                
                $pdf->SetFont('helvetica', 'B', 16);
                $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 8, 'Fecha de generación: ' . date('d/m/Y H:i'), 0, 1, 'C');
                $pdf->Ln(5);
                
                $html = '<table border="1" cellpadding="4"><thead><tr style="background-color:#f0f0f0;">';
                if (count($datos) > 0) {
                    // Encabezados
                    foreach (array_keys($datos[0]) as $k) {
                        $html .= '<th><b>' . strtoupper($k) . '</b></th>';
                    }
                    $html .= '</tr></thead><tbody>';
                    foreach ($datos as $row) {
                        $html .= '<tr>';
                        foreach ($row as $val) {
                            $html .= '<td>' . $val . '</td>';
                        }
                        $html .= '</tr>';
                    }
                } else {
                    $html .= '<th>Sin Datos</th></tr></thead><tbody><tr><td>No hay datos para este reporte</td></tr>';
                }
                $html .= '</tbody></table>';
                
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->Output('reporte_' . strtolower($tipo) . '_' . date('Ymd') . '.pdf', 'I');
                exit;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error generando reporte PDF/CSV: ' . $e->getMessage());
            if ($this->request->getPost('preview') === 'true') {
                return $this->response->setJSON(['success' => false, 'error' => 'Error generando reporte']);
            }
            return $this->response->setStatusCode(500)->setBody('Error del sistema');
        }
    }
    
    /**
     * Exportar datos (Reporte Completo Excel/CSV)
     */
    public function exportarDatos()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setStatusCode(403)->setBody('No autorizado');
        }

        try {
            // Generar CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=reporte_completo_' . date('YmdHis') . '.csv');
            
            $output = fopen('php://output', 'w');
            // BOM for Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($output, ['ID', 'Nombre', 'Apellido', 'Cedula', 'Email', 'Estado Ficha', 'Periodo']);
            
            // Datos
            $db = \Config\Database::connect();
            $datos = $db->table('usuarios u')
                ->select('u.id, u.nombre, u.apellido, u.cedula, u.email, f.estado, p.nombre as periodo')
                ->join('fichas_socioeconomicas f', 'f.estudiante_id = u.id', 'left')
                ->join('periodos_academicos p', 'p.id = f.periodo_id', 'left')
                ->where('u.rol_id', 1)
                ->orderBy('u.id', 'DESC')
                ->get()->getResultArray();
                
            foreach ($datos as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['nombre'],
                    $row['apellido'],
                    $row['cedula'],
                    $row['email'],
                    $row['estado'] ?? 'Sin Ficha',
                    $row['periodo'] ?? 'N/A'
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando datos: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error del sistema');
        }
    }

    // ========================================
    // CONFIGURACIÓN DEL SISTEMA
    // ========================================

    /**
     * Vista de configuración del sistema
     */
    public function configuracionSistema()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $configRows = $this->db->table('configuracion_sistema')->get()->getResultArray();
            $configuracion = [];
            foreach ($configRows as $row) {
                $configuracion[$row['clave']] = $row['valor'];
            }
            
            return view('AdminBienestar/configuracion_sistema', [
                'configuracion' => $configuracion,
                'usuario' => $this->getUsuarioActual()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en configuración: ' . $e->getMessage());
            return view('AdminBienestar/configuracion_sistema', [
                'configuracion' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando configuración'
            ]);
        }
    }

    /**
     * Guardar configuración del sistema
     */
    public function guardarConfiguracion()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $postData = $this->request->getPost();
            $adminId = session('id');

            if (empty($postData)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos de configuración requeridos']);
            }

            // Obtener todas las configuraciones actuales
            $existingConfigs = $this->db->table('configuracion_sistema')->get()->getResultArray();
            
            // Identificar qué categorías de configuración se incluyeron en este submit
            $submittedCategories = [];
            foreach ($existingConfigs as $c) {
                if (array_key_exists($c['clave'], $postData)) {
                    $submittedCategories[$c['categoria']] = true;
                }
            }

            // Si es un submit específico de alguna categoría, actualizar los campos
            foreach ($existingConfigs as $c) {
                $clave = $c['clave'];
                $categoria = $c['categoria'];
                
                if (isset($submittedCategories[$categoria])) {
                    if (array_key_exists($clave, $postData)) {
                        $valor = $postData[$clave];
                        if ($c['tipo'] === 'boolean') {
                            $valor = ($valor === 'on' || $valor === '1' || $valor === 1) ? '1' : '0';
                        }
                        $this->db->table('configuracion_sistema')
                            ->where('clave', $clave)
                            ->update(['valor' => $valor]);
                    } else {
                        // Si es boolean y pertenece a una categoría enviada pero no está en POST, se desmarcó
                        if ($c['tipo'] === 'boolean') {
                            $this->db->table('configuracion_sistema')
                                ->where('clave', $clave)
                                ->update(['valor' => '0']);
                        }
                    }
                }
            }

            // Registrar en logs si existe la tabla
            try {
                $this->db->table('logs')->insert([
                    'usuario_id' => $adminId,
                    'accion' => 'actualizar_configuracion_sistema',
                    'tabla' => 'configuracion_sistema',
                    'valores_nuevos' => json_encode($postData),
                    'fecha' => date('Y-m-d H:i:s'),
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (\Exception $e) {
                log_message('warning', 'No se pudo guardar log de configuración: ' . $e->getMessage());
            }
                
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configuración guardada correctamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error guardando configuración: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    // ========================================
    // PERFIL Y CUENTA
    // ========================================

    /**
     * Vista del perfil del administrador
     */
    public function perfil()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $usuario = $this->usuarioModel->find(session('id'));
            
            return view('AdminBienestar/perfil', [
                'usuario' => $usuario
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en perfil: ' . $e->getMessage());
            return view('AdminBienestar/perfil', [
                'usuario' => null,
                'error' => 'Error cargando perfil'
            ]);
        }
    }

    /**
     * Actualizar perfil del administrador
     */
    public function actualizarPerfil()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $datos = [
                'nombre' => $this->request->getPost('nombre'),
                'apellido' => $this->request->getPost('apellido'),
                'email' => $this->request->getPost('email'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion')
            ];

            $resultado = $this->usuarioModel->update(session('id'), $datos);

            if ($resultado) {
                // Actualizar sesión
                session()->set([
                    'nombre' => $datos['nombre'],
                    'apellido' => $datos['apellido'],
                    'email' => $datos['email']
                ]);

            return $this->response->setJSON([
                'success' => true, 
                    'message' => 'Perfil actualizado correctamente'
                ]);
            } else {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'Error actualizando perfil'
            ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando perfil: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }
    
    /**
     * Vista de la cuenta del administrador
     */
    public function cuenta()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $usuario = $this->usuarioModel->find(session('id'));
            
            return view('AdminBienestar/cuenta', [
                'usuario' => $usuario
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en cuenta: ' . $e->getMessage());
            return view('AdminBienestar/cuenta', [
                'usuario' => null,
                'error' => 'Error cargando cuenta'
            ]);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $passwordActual = $this->request->getPost('password_actual');
            $passwordNuevo = $this->request->getPost('password_nuevo');
            $passwordConfirmar = $this->request->getPost('password_confirmar');

            if (!$passwordActual || !$passwordNuevo || !$passwordConfirmar) {
                return $this->response->setJSON(['success' => false, 'error' => 'Todos los campos son requeridos']);
            }

            if ($passwordNuevo !== $passwordConfirmar) {
                return $this->response->setJSON(['success' => false, 'error' => 'Las contraseñas no coinciden']);
            }

            // Verificar contraseña actual
            $usuario = $this->usuarioModel->find(session('id'));
            if (!password_verify($passwordActual, $usuario['password_hash'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Contraseña actual incorrecta']);
            }

            // Actualizar contraseña
            $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
            $resultado = $this->usuarioModel->update(session('id'), ['password_hash' => $passwordHash]);

            if ($resultado) {
            return $this->response->setJSON([
                'success' => true,
                    'message' => 'Contraseña cambiada correctamente'
                ]);
            } else {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'Error cambiando contraseña'
            ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error cambiando contraseña: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    // ========================================
    // MÉTODOS DE COMPATIBILIDAD (MANTENER)
    // ========================================

    /**
     * Métodos legacy para mantener compatibilidad
     */
    public function fichas() {
        return $this->fichasSocioeconomicas();
    }
    
    public function estudiantes() {
        return $this->usuarios();
    }
    
    public function solicitudes() {
        return $this->solicitudesBecas();
    }
    
    public function reportes() {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $estadisticas = $this->adminService->getEstadisticasCompletas();
            
            return view('AdminBienestar/reportes_mejorado', [
                'estadisticas' => $estadisticas,
                'usuario' => $this->getUsuarioActual()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en reportes: ' . $e->getMessage());
            return view('AdminBienestar/reportes_mejorado', [
                'estadisticas' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando reportes'
            ]);
        }
    }

    // ========================================
    // GESTIÓN DE PERÍODOS ACADÉMICOS
    // ========================================

    /**
     * Vista de gestión de períodos académicos
     */
    public function gestionPeriodos()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $periodos = $this->periodoModel->orderBy('fecha_inicio', 'DESC')->findAll();
            
            return view('AdminBienestar/gestion_periodos_mejorada', [
                'periodos' => $periodos,
                'usuario' => $this->getUsuarioActual()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en gestión períodos: ' . $e->getMessage());
            return view('AdminBienestar/gestion_periodos_mejorada', [
                'periodos' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando períodos'
            ]);
        }
    }

    /**
     * Crear nuevo período académico
     */
    public function crearPeriodo()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            // Validaciones
            if (empty($input['nombre']) || empty($input['fecha_inicio']) || empty($input['fecha_fin'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Nombre, fecha de inicio y fecha de fin son obligatorios'
                ]);
            }

            if (strtotime($input['fecha_fin']) <= strtotime($input['fecha_inicio'])) {
                return $this->response->setJSON([
                'success' => false,
                    'error' => 'La fecha de fin debe ser posterior a la fecha de inicio'
                ]);
            }

            // Verificar si ya existe un período con el mismo nombre
            $existente = $this->periodoModel->where('nombre', $input['nombre'])->first();
            if ($existente) {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'Ya existe un período con ese nombre'
                ]);
            }

            $datos = [
                'nombre' => $input['nombre'],
                'fecha_inicio' => $input['fecha_inicio'],
                'fecha_fin' => $input['fecha_fin'],
                'activo' => $input['activo'] ?? 1,
                'activo_fichas' => $input['activo_fichas'] ?? 1,
                'activo_becas' => $input['activo_becas'] ?? 1,
                'vigente_estudiantes' => $input['vigente_estudiantes'] ?? 0,
                'limite_fichas' => !empty($input['limite_fichas']) ? (int)$input['limite_fichas'] : null,
                'limite_becas' => !empty($input['limite_becas']) ? (int)$input['limite_becas'] : null,
                'descripcion' => $input['descripcion'] ?? null,
                'fichas_creadas' => 0,
                'becas_asignadas' => 0,
                'created_by' => $this->getUsuarioActual()['id'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $periodoId = $this->periodoModel->insert($datos);

            if (!$periodoId) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error creando el período académico'
                ]);
            }

            // Log de la acción
            $this->logAction('crear_periodo', 'periodos_academicos', $periodoId, $datos);

                return $this->response->setJSON([
                    'success' => true,
                'message' => 'Período académico creado exitosamente',
                'periodo_id' => $periodoId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error creando período: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Actualizar período académico
     */
    public function actualizarPeriodo()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);

            if (empty($input['periodo_id'])) {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'ID del período es requerido'
                ]);
            }

            $periodo = $this->periodoModel->find($input['periodo_id']);
            if (!$periodo) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            // Validaciones
            if (strtotime($input['fecha_fin']) <= strtotime($input['fecha_inicio'])) {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'La fecha de fin debe ser posterior a la fecha de inicio'
                ]);
            }

            $datos = [
                'nombre' => $input['nombre'],
                'fecha_inicio' => $input['fecha_inicio'],
                'fecha_fin' => $input['fecha_fin'],
                'activo' => $input['activo'] ?? 1,
                'activo_fichas' => $input['activo_fichas'] ?? 1,
                'activo_becas' => $input['activo_becas'] ?? 1,
                'vigente_estudiantes' => $input['vigente_estudiantes'] ?? 0,
                'limite_fichas' => !empty($input['limite_fichas']) ? (int)$input['limite_fichas'] : null,
                'limite_becas' => !empty($input['limite_becas']) ? (int)$input['limite_becas'] : null,
                'descripcion' => $input['descripcion'] ?? null,
                'updated_by' => $this->getUsuarioActual()['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->periodoModel->update($input['periodo_id'], $datos);

            // Log de la acción
            $this->logAction('actualizar_periodo', 'periodos_academicos', $input['periodo_id'], $datos);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Período académico actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando período: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }



    /**
     * Actualizar límites de un período
     */
    public function actualizarLimitesPeriodo()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            $periodo = $this->periodoModel->find($input['periodo_id']);
            if (!$periodo) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            $datos = [
                'limite_fichas' => !empty($input['limite_fichas']) ? (int)$input['limite_fichas'] : null,
                'limite_becas' => !empty($input['limite_becas']) ? (int)$input['limite_becas'] : null,
                'updated_by' => $this->getUsuarioActual()['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->periodoModel->update($input['periodo_id'], $datos);

            // Log de la acción
            $this->logAction('actualizar_limites_periodo', 'periodos_academicos', $input['periodo_id'], $datos);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Límites actualizados exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando límites: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Toggle configuración de período (fichas/becas activas)
     */
    public function toggleConfiguracionPeriodo()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);

            $periodo = $this->periodoModel->find($input['periodo_id']);
            if (!$periodo) {
            return $this->response->setJSON([
                'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            $camposPermitidos = ['activo_fichas', 'activo_becas'];
            if (!in_array($input['campo'], $camposPermitidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Campo no válido'
                ]);
            }

            $datos = [
                $input['campo'] => $input['valor'],
                'updated_by' => $this->getUsuarioActual()['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->periodoModel->update($input['periodo_id'], $datos);

            // Log de la acción
            $this->logAction('toggle_configuracion_periodo', 'periodos_academicos', $input['periodo_id'], $datos);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configuración actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando configuración: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }





    // ========================================
    // GESTIÓN DE SOLICITUDES DE BECAS Y DOCUMENTOS
    // ========================================

    /**
     * Vista de solicitudes de becas mejorada
     */
    public function solicitudesBecasMejorada()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            // Configuración de paginación
            $porPagina = 15;
            $pagina = $this->request->getGet('page') ?? 1;
            $offset = ($pagina - 1) * $porPagina;
            
            // Obtener período de filtro del request
            $periodoFiltroId = $this->request->getGet('periodo_id');
            
            // Usar Query Builder para evitar SQL injection
            $builder = $this->db->table('solicitudes_becas sb')
                ->select('sb.id, sb.estudiante_id, sb.beca_id, sb.periodo_id, sb.estado, sb.observaciones, sb.fecha_solicitud, sb.fecha_revision, sb.revisado_por, sb.motivo_rechazo, sb.documentos_revisados, sb.total_documentos, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, u.cedula as estudiante_cedula, u.carrera_id, c.nombre as carrera_nombre, b.nombre as beca_nombre, b.tipo_beca, b.monto_beca, pa.nombre as periodo_nombre')
                ->join('usuarios u', 'u.id = sb.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos pa', 'pa.id = sb.periodo_id');
            
            if (!empty($periodoFiltroId)) {
                $builder->where('sb.periodo_id', $periodoFiltroId);
            }
            
            // Contar total de registros
            $countBuilder = clone $builder;
            $totalRegistros = $countBuilder->countAllResults(false);
            $totalPaginas = ceil($totalRegistros / $porPagina);
            
            // Obtener solicitudes con paginación
            $solicitudes = $builder
                ->orderBy('sb.fecha_solicitud', 'DESC')
                ->limit((int)$porPagina, (int)$offset)
                ->get()
                ->getResultArray();
            
            // Para cada solicitud, obtener conteo real de documentos subidos
            foreach ($solicitudes as &$sol) {
                $docStats = $this->db->table('documentos_solicitud_becas')
                    ->select("
                        COUNT(*) as total,
                        SUM(CASE WHEN estado != 'Pendiente' THEN 1 ELSE 0 END) as subidos,
                        SUM(CASE WHEN estado = 'Aprobado' THEN 1 ELSE 0 END) as aprobados,
                        SUM(CASE WHEN estado = 'En Revision' THEN 1 ELSE 0 END) as en_revision,
                        SUM(CASE WHEN estado = 'Rechazado' THEN 1 ELSE 0 END) as rechazados
                    ")
                    ->where('solicitud_beca_id', $sol['id'])
                    ->get()
                    ->getRowArray();
                
                $sol['docs_total'] = $docStats['total'] ?? 0;
                $sol['docs_subidos'] = $docStats['subidos'] ?? 0;
                $sol['docs_aprobados'] = $docStats['aprobados'] ?? 0;
                $sol['docs_en_revision'] = $docStats['en_revision'] ?? 0;
                $sol['docs_rechazados'] = $docStats['rechazados'] ?? 0;
            }
            unset($sol);

            // Filtros usando Query Builder
            $tiposBecas = $this->db->table('becas')
                ->select('DISTINCT tipo_beca')
                ->get()
                ->getResultArray();
            
            $carreras = $this->db->table('usuarios')
                ->select('DISTINCT carrera')
                ->where('carrera IS NOT NULL')
                ->get()
                ->getResultArray();
            
            // Obtener todos los períodos para el dropdown de filtro
            $periodos = $this->periodoModel->orderBy('fecha_inicio', 'DESC')->findAll();
            
            // Estadísticas usando Query Builder
            $estadisticasEstado = $this->db->table('solicitudes_becas')
                ->select('estado, COUNT(*) as cantidad')
                ->groupBy('estado')
                ->get()
                ->getResultArray();
            
            // Estadísticas de progreso
            $todasSolicitudes = $this->db->table('solicitudes_becas')
                ->select('documentos_revisados')
                ->get()
                ->getResultArray();
            $estadisticasProgreso = [];
            foreach ($todasSolicitudes as $s) {
                $rev = (int)($s['documentos_revisados'] ?? 0);
                $prog = $rev >= 5 ? '100%' : ($rev * 20) . '%';
                if (!isset($estadisticasProgreso[$prog])) {
                    $estadisticasProgreso[$prog] = ['progreso' => $prog, 'cantidad' => 0];
                }
                $estadisticasProgreso[$prog]['cantidad']++;
            }
            $estadisticasProgreso = array_values($estadisticasProgreso);
            usort($estadisticasProgreso, function($a, $b) {
                return (int)$a['progreso'] <=> (int)$b['progreso'];
            });

            return view('AdminBienestar/solicitudes_becas_mejorada', [
                'solicitudes' => $solicitudes,
                'tipos_becas' => array_column($tiposBecas, 'tipo_beca'),
                'carreras' => array_column($carreras, 'carrera'),
                'periodos' => $periodos,
                'periodo_filtro_id' => $periodoFiltroId,
                'estadisticas_estado' => $estadisticasEstado,
                'estadisticas_progreso' => $estadisticasProgreso,
                'usuario' => $this->getUsuarioActual(),
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalRegistros,
                    'offset' => $offset
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en solicitudes becas mejorada: ' . $e->getMessage());
            return view('AdminBienestar/solicitudes_becas_mejorada', [
                'solicitudes' => [],
                'tipos_becas' => [],
                'carreras' => [],
                'periodos' => [],
                'periodo_filtro_id' => null,
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando solicitudes'
            ]);
        }
    }

    /**
     * Vista de revisión de documentos
     */
    public function revisionDocumentos($solicitudId)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setStatusCode(403)->setBody('No autorizado');
        }

        try {
            // Obtener datos de la solicitud con información del estudiante y beca
            $solicitud = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, u.email as estudiante_email, u.cedula as estudiante_cedula, c.nombre as carrera_nombre, b.nombre as beca_nombre, b.tipo_beca, b.monto_beca, p.nombre as periodo_nombre')
                ->join('usuarios u', 'u.id = sb.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos p', 'p.id = sb.periodo_id')
                ->where('sb.id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setStatusCode(404)->setBody('Solicitud no encontrada');
            }

            // Obtener documentos de la solicitud
            $documentos = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, bdr.nombre_documento as documento_requerido_nombre, bdr.descripcion as documento_requerido_descripcion')
                ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
                ->where('dsb.solicitud_beca_id', $solicitudId)
                ->orderBy('dsb.orden_revision', 'ASC')
                ->get()
                ->getResultArray();

            // Obtener ficha socioeconómica del estudiante para este período
            $ficha = $this->db->table('fichas_socioeconomicas')
                ->where('estudiante_id', $solicitud['estudiante_id'])
                ->where('periodo_id', $solicitud['periodo_id'])
                ->get()
                ->getRowArray();

            return view('AdminBienestar/revision_documentos', [
                'solicitud' => $solicitud,
                'documentos' => $documentos,
                'ficha' => $ficha
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en revisionDocumentos: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error del sistema');
        }
    }

    /**
     * Aprobar documento
     */
    public function aprobarDocumento()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        try {
            $documentoId = $this->request->getPost('documento_id');
            $observaciones = $this->request->getPost('observaciones');

            // Verificar que el documento existe
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id, sb.id as solicitud_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Actualizar estado del documento
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'estado' => 'Aprobado',
                    'observaciones' => $observaciones,
                    'revisado_por' => session('id'),
                    'fecha_revision' => date('Y-m-d H:i:s')
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($documento['solicitud_id']);

            // Notificar al estudiante
            $this->notificarEstudianteDocumento($documento['estudiante_id'], 'Documento Aprobado', $observaciones);

            return $this->response->setJSON(['success' => true, 'message' => 'Documento aprobado exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error aprobando documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Rechazar documento
     */
    public function rechazarDocumento()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        try {
            $documentoId = $this->request->getPost('documento_id');
            $motivoRechazo = $this->request->getPost('motivo_rechazo');

            if (empty($motivoRechazo)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Debe especificar el motivo del rechazo']);
            }

            // Verificar que el documento existe
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id, sb.id as solicitud_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Actualizar estado del documento
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'estado' => 'Rechazado',
                    'observaciones' => $motivoRechazo,
                    'revisado_por' => session('id'),
                    'fecha_revision' => date('Y-m-d H:i:s')
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($documento['solicitud_id']);

            // Notificar al estudiante
            $this->notificarEstudianteDocumento($documento['estudiante_id'], 'Documento Rechazado', $motivoRechazo);

            return $this->response->setJSON(['success' => true, 'message' => 'Documento rechazado exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error rechazando documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Aprobar solicitud de beca
     */
    public function aprobarSolicitudBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        try {
            $solicitudId = $this->request->getPost('solicitud_id');
            $observaciones = $this->request->getPost('observaciones');

            // Verificar que la solicitud existe
            $solicitud = $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no encontrada']);
            }

            // Verificar que todos los documentos estén aprobados
            $documentosPendientes = $this->db->table('documentos_solicitud_becas')
                ->where('solicitud_beca_id', $solicitudId)
                ->whereIn('estado', ['Pendiente', 'En Revision', 'Rechazado'])
                ->countAllResults();

            if ($documentosPendientes > 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se puede aprobar la beca. Hay documentos pendientes de revisión o rechazados.']);
            }

            // Verificar que la ficha socioeconómica esté aprobada
            $ficha = $this->db->table('fichas_socioeconomicas')
                ->where('estudiante_id', $solicitud['estudiante_id'])
                ->where('periodo_id', $solicitud['periodo_id'])
                ->get()
                ->getRowArray();

            if (!$ficha || $ficha['estado'] !== 'Aprobada') {
                return $this->response->setJSON(['success' => false, 'message' => 'No se puede aprobar la beca. La ficha socioeconómica debe estar aprobada.']);
            }

            // Aprobar la solicitud
            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'estado' => 'Aprobada',
                    'fecha_aprobacion' => date('Y-m-d H:i:s'),
                    'aprobado_por' => session('id'),
                    'observaciones_admin' => $observaciones
                ]);

            // Notificar al estudiante
            $this->notificarEstudianteBeca($solicitud['estudiante_id'], 'Beca Aprobada', $observaciones);

            return $this->response->setJSON(['success' => true, 'message' => 'Beca aprobada exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error aprobando beca: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Rechazar solicitud de beca
     */
    public function rechazarSolicitudBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        try {
            $solicitudId = $this->request->getPost('solicitud_id');
            $motivoRechazo = $this->request->getPost('motivo_rechazo');

            if (empty($motivoRechazo)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Debe especificar el motivo del rechazo']);
            }

            // Verificar que la solicitud existe
            $solicitud = $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no encontrada']);
            }

            // Rechazar la solicitud
            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'estado' => 'Rechazada',
                    'fecha_rechazo' => date('Y-m-d H:i:s'),
                    'rechazado_por' => session('id'),
                    'motivo_rechazo' => $motivoRechazo
                ]);

            // Notificar al estudiante
            $this->notificarEstudianteBeca($solicitud['estudiante_id'], 'Beca Rechazada', $motivoRechazo);

            return $this->response->setJSON(['success' => true, 'message' => 'Beca rechazada exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error rechazando beca: ' . $e->getMessage());
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

            // Porcentaje basado en documentos subidos
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

    /**
     * Notificar al estudiante sobre cambio de estado de documento
     */
    private function notificarEstudianteDocumento($estudianteId, $tipo, $mensaje)
    {
        try {
            $estudiante = $this->usuarioModel->find($estudianteId);
            $subject = "Notificación: $tipo";
            $body = "<p>Estimado/a {$estudiante['nombre']} {$estudiante['apellido']},</p><p>$mensaje</p>";
            
            $enviado = \App\Helpers\EmailHelper::enviarCorreo($estudiante['email'], $subject, $body);
            
            if ($enviado) {
                log_message('info', "Notificación email enviada a estudiante $estudianteId: $tipo");
            } else {
                log_message('info', "Notificación (solo log) para estudiante $estudianteId: $tipo - $mensaje");
            }
        } catch (\Exception $e) {
            log_message('error', 'Error notificando estudiante: ' . $e->getMessage());
        }
    }

    /**
     * Notificar al estudiante sobre cambio de estado de beca
     */
    private function notificarEstudianteBeca($estudianteId, $tipo, $mensaje)
    {
        try {
            $estudiante = $this->usuarioModel->find($estudianteId);
            $subject = "Notificación de Beca: $tipo";
            $body = "<p>Estimado/a {$estudiante['nombre']} {$estudiante['apellido']},</p><p>$mensaje</p>";
            
            $enviado = \App\Helpers\EmailHelper::enviarCorreo($estudiante['email'], $subject, $body);
            
            if ($enviado) {
                log_message('info', "Notificación email enviada a estudiante $estudianteId: $tipo");
            } else {
                log_message('info', "Notificación (solo log) para estudiante $estudianteId: $tipo - $mensaje");
            }
        } catch (\Exception $e) {
            log_message('error', 'Error notificando estudiante: ' . $e->getMessage());
        }
    }

    // ========================================
    // OBSERVACIONES Y REVISIÓN DE SOLICITUDES DE BECA
    // ========================================

    /**
     * Agregar observación a una solicitud de beca
     */
    public function agregarObservacionSolicitud()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $solicitudId = $input['solicitud_id'] ?? null;
            $observacion = $input['observacion'] ?? '';
            $notificarEstudiante = !empty($input['notificar_estudiante']);

            if (!$solicitudId || empty(trim($observacion))) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos incompletos: solicitud_id y observación son requeridos']);
            }

            // Verificar que la solicitud existe
            $solicitud = $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            // Guardar la observación en el campo observaciones_admin (concatenar con las existentes)
            $observacionesPrevias = $solicitud['observaciones_admin'] ?? '';
            $nuevaObservacion = date('Y-m-d H:i:s') . ' - Admin #' . session('id') . ': ' . $observacion;
            $observacionesActualizadas = $observacionesPrevias 
                ? $observacionesPrevias . "\n" . $nuevaObservacion
                : $nuevaObservacion;

            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'observaciones_admin' => $observacionesActualizadas,
                    'observaciones_admin' => $observacionesActualizadas
                ]);

            // Notificar al estudiante si se solicitó
            if ($notificarEstudiante) {
                $this->notificarEstudianteBeca(
                    $solicitud['estudiante_id'],
                    'Nueva observación en solicitud de beca',
                    $observacion
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Observación agregada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error agregando observación: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Marcar un documento rechazado para revisión nuevamente
     */
    public function revisarNuevamenteDocumento()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $documentoId = $input['documento_id'] ?? null;

            if (!$documentoId) {
                return $this->response->setJSON(['success' => false, 'message' => 'ID de documento requerido']);
            }

            // Verificar que el documento existe
            $documento = $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            if ($documento['estado'] !== 'Rechazado') {
                return $this->response->setJSON(['success' => false, 'message' => 'Solo se pueden marcar para revisión documentos rechazados']);
            }

            // Cambiar estado a Pendiente para que el estudiante pueda re-subir
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'estado' => 'Pendiente',
                    'observaciones' => 'Solicitada revisión nuevamente por admin #' . session('id'),
                    'revisado_por' => null,
                    'fecha_revision' => null
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($documento['solicitud_beca_id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documento marcado para revisión. El estudiante podrá subir una nueva versión.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error marcando documento para revisión: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    // ========================================
    // GESTIÓN DE SOLICITUDES DE AYUDA
    // ========================================

    /**
     * Vista de solicitudes de ayuda mejorada
     */
    public function solicitudesAyudaMejorada()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            // Configuración de paginación
            $porPagina = 15;
            $pagina = $this->request->getGet('page') ?? 1;
            $offset = ($pagina - 1) * $porPagina;
            
            // Contar total de registros
            $sqlCount = "
                SELECT COUNT(*) as total
                FROM solicitudes_ayuda sa
                JOIN usuarios u ON u.id = sa.id_estudiante
                LEFT JOIN carreras c ON c.id = u.carrera_id
                LEFT JOIN usuarios admin ON admin.id = sa.id_responsable
            ";
            $totalRegistros = $this->db->query($sqlCount)->getRow()->total;
            $totalPaginas = ceil($totalRegistros / $porPagina);
            
            // Obtener solicitudes de ayuda con información del estudiante + LIMIT para paginación
            $sqlSolicitudes = "
                SELECT sa.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, 
                       u.cedula as estudiante_cedula, u.carrera_id, c.nombre as carrera_nombre, 
                       admin.nombre as responsable_nombre
                FROM solicitudes_ayuda sa
                JOIN usuarios u ON u.id = sa.id_estudiante
                LEFT JOIN carreras c ON c.id = u.carrera_id
                LEFT JOIN usuarios admin ON admin.id = sa.id_responsable
                ORDER BY sa.fecha_solicitud DESC
                LIMIT " . (int)$porPagina . " OFFSET " . (int)$offset . "
            ";
            $solicitudes = $this->db->query($sqlSolicitudes)->getResultArray();
            
            // Obtener estadísticas para los gráficos
            $estadisticasEstado = $this->db->table('solicitudes_ayuda')
                ->select('estado, COUNT(*) as cantidad')
                ->groupBy('estado')
                ->get()
                ->getResultArray();
                
            $estadisticasPrioridad = $this->db->table('solicitudes_ayuda')
                ->select('prioridad, COUNT(*) as cantidad')
                ->groupBy('prioridad')
                ->get()
                ->getResultArray();
            
            return view('AdminBienestar/solicitudes_ayuda_mejorada', [
                'solicitudes' => $solicitudes,
                'estadisticas_estado' => $estadisticasEstado,
                'estadisticas_prioridad' => $estadisticasPrioridad,
                'usuario' => $this->getUsuarioActual(),
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'por_pagina' => $porPagina,
                    'total_registros' => $totalRegistros,
                    'offset' => $offset
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en solicitudes ayuda mejorada: ' . $e->getMessage());
            return view('AdminBienestar/solicitudes_ayuda_mejorada', [
                'solicitudes' => [],
                'usuario' => $this->getUsuarioActual(),
                'error' => 'Error cargando solicitudes'
            ]);
        }
    }

    /**
     * Responder solicitud de ayuda
     */
    public function responderSolicitudAyuda()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            // Log de entrada para debugging
            log_message('info', 'Responder solicitud - Input recibido: ' . json_encode($input));
            
            if (empty($input)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos de entrada inválidos']);
            }
            
            $solicitudId = $input['solicitud_id'] ?? null;
            $respuesta = $input['respuesta'] ?? '';
            $nuevoEstado = $input['nuevo_estado'] ?? null;
            $nuevaPrioridad = $input['nueva_prioridad'] ?? null;
            $notificarEstudiante = $input['notificar_estudiante'] ?? false;

            // Validar campos obligatorios
            if (empty($solicitudId) || empty($respuesta)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud ID y respuesta son obligatorios']);
            }

            // Verificar que la solicitud existe
            $solicitudExistente = $this->db->table('solicitudes_ayuda')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();
            
            if (!$solicitudExistente) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            log_message('info', 'Solicitud encontrada: ' . json_encode($solicitudExistente));

            // Actualizar la solicitud
            $datosActualizacion = [
                'id_responsable' => $this->getUsuarioActual()['id'],
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            if ($nuevoEstado) {
                $datosActualizacion['estado'] = $nuevoEstado;
                if ($nuevoEstado === 'Resuelta') {
                    $datosActualizacion['fecha_actualizacion'] = date('Y-m-d H:i:s');
                    
                    // Calcular tiempo de respuesta
                    $fechaCreacion = new \DateTime($solicitudExistente['fecha_solicitud']);
                    $fechaResolucion = new \DateTime();
                    $diferencia = $fechaCreacion->diff($fechaResolucion);
                    $tiempoRespuestaHrs = ($diferencia->days * 24) + $diferencia->h;
                    
                    $datosActualizacion['comentarios_resolucion'] = $respuesta . "\n\nTiempo de respuesta: " . $tiempoRespuestaHrs . " horas";
                } else {
                    $datosActualizacion['comentarios_resolucion'] = $respuesta;
                }
            } else {
                $datosActualizacion['comentarios_resolucion'] = $respuesta;
            }

            if ($nuevaPrioridad) {
                $datosActualizacion['prioridad'] = $nuevaPrioridad;
            }

            log_message('info', 'Datos a actualizar: ' . json_encode($datosActualizacion));

            // Actualizar la solicitud
            $resultadoUpdate = $this->db->table('solicitudes_ayuda')
                ->update($datosActualizacion, ['id' => $solicitudId]);

            if (!$resultadoUpdate) {
                log_message('error', 'No se pudo actualizar la solicitud ID: ' . $solicitudId);
                return $this->response->setJSON(['success' => false, 'error' => 'No se pudo actualizar la solicitud']);
            }

            log_message('info', 'Solicitud actualizada exitosamente');

            // Crear respuesta en el sistema de respuestas
            try {
                $datosRespuesta = [
                    'solicitud_ayuda_id' => $solicitudId,
                    'id_responsable' => $this->getUsuarioActual()['id'],
                    'respuesta' => $respuesta,
                    'fecha_respuesta' => date('Y-m-d H:i:s')
                ];
                
                $resultadoRespuesta = $this->db->table('respuestas_solicitudes_ayuda')->insert($datosRespuesta);
                
                if ($resultadoRespuesta) {
                    log_message('info', 'Respuesta registrada en sistema de respuestas');
                } else {
                    log_message('warning', 'No se pudo registrar la respuesta en el sistema');
                }
            } catch (\Exception $e) {
                log_message('warning', 'Error registrando respuesta en sistema: ' . $e->getMessage());
                // Continuar sin error, no es crítico
            }

            // Log de la acción (opcional)
            try {
                $this->logAction('responder_solicitud_ayuda', 'solicitudes_ayuda', $solicitudId, $datosActualizacion);
            } catch (\Exception $e) {
                log_message('warning', 'Error en logging de acción: ' . $e->getMessage());
                // Continuar sin error, no es crítico
            }

            log_message('info', 'Respuesta enviada exitosamente para solicitud ID: ' . $solicitudId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Respuesta enviada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error respondiendo solicitud ayuda: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al procesar la solicitud'
            ]);
        }
    }

    /**
     * Guardar respuesta predefinida
     */
    public function guardarRespuestaPredefinida()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos de entrada inválidos']);
            }
            
            $datos = [
                'nombre' => $input['nombre'] ?? '',
                'categoria' => $input['categoria'] ?? '',
                'contenido' => $input['contenido'] ?? '',
                'tags' => $input['tags'] ?? '',
                'publica' => $input['publica'] ?? true,
                'activa' => $input['activa'] ?? true,
                'id_usuario' => $this->getUsuarioActual()['id']
            ];
            
            // Validar campos obligatorios
            if (empty($datos['nombre']) || empty($datos['categoria']) || empty($datos['contenido'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Nombre, categoría y contenido son obligatorios']);
            }
            
            // Insertar en la base de datos
            $resultado = $this->db->table('respuestas_predefinidas')->insert($datos);
            
            if ($resultado) {
                $idRespuesta = $this->db->insertID();
                $datos['id'] = $idRespuesta;
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respuesta predefinida guardada exitosamente',
                    'respuesta' => $datos
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'No se pudo guardar la respuesta']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error guardando respuesta predefinida: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema al guardar respuesta']);
        }
    }

    /**
     * Obtener respuestas predefinidas del usuario
     */
    public function obtenerRespuestasPredefinidas()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $userId = $this->getUsuarioActual()['id'];
            
            $respuestas = $this->db->table('respuestas_predefinidas')
                ->where('id_usuario', $userId)
                ->where('activa', true)
                ->orderBy('categoria', 'ASC')
                ->orderBy('nombre', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'respuestas' => $respuestas
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo respuestas predefinidas: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Eliminar respuesta predefinida
     */
    public function eliminarRespuestaPredefinida()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $respuestaId = $input['id'] ?? null;
            
            if (empty($respuestaId)) {
                return $this->response->setJSON(['success' => false, 'error' => 'ID de respuesta requerido']);
            }
            
            // Verificar que la respuesta pertenece al usuario
            $userId = $this->getUsuarioActual()['id'];
            $respuesta = $this->db->table('respuestas_predefinidas')
                ->where('id', $respuestaId)
                ->where('id_usuario', $userId)
                ->get()
                ->getRowArray();
            
            if (!$respuesta) {
                return $this->response->setJSON(['success' => false, 'error' => 'Respuesta no encontrada o no autorizada']);
            }
            
            // Eliminar la respuesta
            $resultado = $this->db->table('respuestas_predefinidas')
                ->where('id', $respuestaId)
                ->delete();
            
            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respuesta predefinida eliminada exitosamente'
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'No se pudo eliminar la respuesta']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error eliminando respuesta predefinida: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Obtener detalle de una solicitud de ayuda
     */
    public function detalleSolicitudAyuda($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $sql = "
                SELECT sa.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, 
                       u.cedula as estudiante_cedula, u.email as estudiante_email, u.carrera_id, 
                       c.nombre as carrera_nombre, admin.nombre as responsable_nombre,
                       cat.nombre as categoria_nombre, cat.color as categoria_color, cat.icono as categoria_icono
                FROM solicitudes_ayuda sa
                JOIN usuarios u ON u.id = sa.id_estudiante
                LEFT JOIN carreras c ON c.id = u.carrera_id
                LEFT JOIN usuarios admin ON admin.id = sa.id_responsable
                LEFT JOIN categorias_solicitud_ayuda cat ON cat.id = sa.categoria_id
                WHERE sa.id = ?
            ";
            
            $solicitud = $this->db->query($sql, [$id])->getRowArray();
            
            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            // Obtener historial de respuestas si existe
            $respuestas = [];
            try {
                $respuestas = $this->db->table('respuestas_solicitudes_ayuda')
                    ->where('solicitud_ayuda_id', $id)
                    ->orderBy('fecha_respuesta', 'ASC')
                    ->get()
                    ->getResultArray();
            } catch (\Exception $e) {
                log_message('error', 'Error obteniendo respuestas de solicitud #' . $id . ': ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'solicitud' => $solicitud,
                'respuestas' => $respuestas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo detalle solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Marcar solicitud como resuelta
     */
    public function marcarSolicitudResuelta()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $solicitudId = $input['solicitud_id'];

            $datos = [
                'estado' => 'Resuelta',
                'fecha_resolucion' => date('Y-m-d H:i:s'),
                'asignado_a' => $this->getUsuarioActual()['id']
            ];

            $this->db->table('solicitudes_ayuda')->update($datos, ['id' => $solicitudId]);

            $this->logAction('marcar_solicitud_resuelta', 'solicitudes_ayuda', $solicitudId, $datos);
            
                            return $this->response->setJSON([
                'success' => true,
                'message' => 'Solicitud marcada como resuelta'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error marcando solicitud como resuelta: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al marcar solicitud'
            ]);
        }
    }

    /**
     * Asignar solicitud a un administrador
     */
    public function asignarSolicitud()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $solicitudId = $input['solicitud_id'];
            $adminId = $input['admin_id'] ?? $this->getUsuarioActual()['id'];

            $datos = [
                'id_responsable' => $adminId,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            $this->db->table('solicitudes_ayuda')->update($datos, ['id' => $solicitudId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Solicitud asignada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error asignando solicitud: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al asignar solicitud'
            ]);
        }
    }

    /**
     * Cambiar prioridad de una solicitud
     */
    public function cambiarPrioridad()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $solicitudId = $input['solicitud_id'];
            $nuevaPrioridad = $input['nueva_prioridad'];

            if (!in_array($nuevaPrioridad, ['Baja', 'Media', 'Alta', 'Urgente'])) {
                return $this->response->setJSON([
                    'success' => false,
                'error' => 'Prioridad inválida'
                ]);
            }

            $datos = [
                'prioridad' => $nuevaPrioridad,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            $this->db->table('solicitudes_ayuda')->update($datos, ['id' => $solicitudId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Prioridad actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error cambiando prioridad: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al cambiar prioridad'
            ]);
        }
    }

    /**
     * Cerrar una solicitud
     */
    public function cerrarSolicitud()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $solicitudId = $input['solicitud_id'];

            $datos = [
                'estado' => 'Cerrada',
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            $this->db->table('solicitudes_ayuda')->update($datos, ['id' => $solicitudId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Solicitud cerrada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error cerrando solicitud: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al cerrar solicitud'
            ]);
        }
    }

    /**
     * Obtener historial completo de solicitudes de un estudiante
     */
    public function historialSolicitudesEstudiante($estudianteId)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Obtener todas las solicitudes del estudiante con estadísticas
            $sql = "
                SELECT sa.*, 
                       cat.nombre as categoria_nombre, cat.color as categoria_color, cat.icono as categoria_icono,
                       COUNT(rsa.id) as total_respuestas
                FROM solicitudes_ayuda sa
                LEFT JOIN categorias_solicitud_ayuda cat ON cat.id = sa.categoria_id
                LEFT JOIN respuestas_solicitudes_ayuda rsa ON rsa.solicitud_ayuda_id = sa.id
                WHERE sa.id_estudiante = ?
                GROUP BY sa.id
                ORDER BY sa.fecha_solicitud DESC
            ";
            
            $solicitudes = $this->db->query($sql, [$estudianteId])->getResultArray();

            // Calcular estadísticas
            $estadisticas = [
                'total_solicitudes' => count($solicitudes),
                'resueltas' => 0,
                'pendientes' => 0,
                'en_proceso' => 0,
                'cerradas' => 0,
                'promedio_respuesta' => null
            ];

            $tiemposRespuesta = [];

            foreach ($solicitudes as $solicitud) {
                switch ($solicitud['estado']) {
                    case 'Resuelta':
                        $estadisticas['resueltas']++;
                        break;
                    case 'Pendiente':
                        $estadisticas['pendientes']++;
                        break;
                    case 'En Proceso':
                        $estadisticas['en_proceso']++;
                        break;
                    case 'Cerrada':
                        $estadisticas['cerradas']++;
                        break;
                }

                // Calcular tiempo de respuesta si está resuelta
                if ($solicitud['estado'] === 'Resuelta' && $solicitud['fecha_actualizacion']) {
                    $fechaCreacion = new \DateTime($solicitud['fecha_solicitud']);
                    $fechaResolucion = new \DateTime($solicitud['fecha_actualizacion']);
                    $diferencia = $fechaCreacion->diff($fechaResolucion);
                    $tiempoHoras = ($diferencia->days * 24) + $diferencia->h;
                    $tiemposRespuesta[] = $tiempoHoras;
                }
            }

            // Calcular promedio de respuesta
            if (!empty($tiemposRespuesta)) {
                $estadisticas['promedio_respuesta'] = round(array_sum($tiemposRespuesta) / count($tiemposRespuesta), 1);
            }

            return $this->response->setJSON([
                'success' => true,
                'solicitudes' => $solicitudes,
                'estadisticas' => $estadisticas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo historial de solicitudes: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al obtener historial'
            ]);
        }
    }

    /**
     * Obtener período académico por ID
     */
    public function obtenerPeriodo($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $periodo = $this->periodoModel->find($id);
            
            if (!$periodo) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'periodo' => $periodo
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo período: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al obtener período'
            ]);
        }
    }

    /**
     * Eliminar período académico
     */
    public function eliminarPeriodo()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (empty($input['id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'ID del período es requerido'
                ]);
            }

            $periodo = $this->periodoModel->find($input['id']);
            if (!$periodo) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            // Verificar si tiene fichas o becas asociadas
            $fichasAsociadas = $this->db->table('fichas_socioeconomicas')
                ->where('periodo_id', $input['id'])
                ->countAllResults();

            $becasAsociadas = $this->db->table('solicitudes_becas')
                ->where('periodo_id', $input['id'])
                ->countAllResults();

            if ($fichasAsociadas > 0 || $becasAsociadas > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No se puede eliminar el período porque tiene fichas o becas asociadas'
                ]);
            }

            // Eliminar el período
            $this->periodoModel->delete($input['id']);

            // Log de la acción
            $this->logAction('eliminar_periodo', 'periodos_academicos', $input['id'], $periodo);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Período académico eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando período: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al eliminar período'
            ]);
        }
    }

    /**
     * Ver detalles completos de un período académico
     */
    public function verPeriodo($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $periodo = $this->periodoModel->find($id);
            
            if (!$periodo) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Período no encontrado'
                ]);
            }

            // Obtener estadísticas del período
            $estadisticas = [
                'total_fichas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $id)
                    ->countAllResults(),
                'total_becas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $id)
                    ->countAllResults(),
                'fichas_aprobadas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Aprobada')
                    ->countAllResults(),
                'fichas_rechazadas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Rechazada')
                    ->countAllResults(),
                'fichas_pendientes' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Pendiente')
                    ->countAllResults(),
                'becas_aprobadas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Aprobada')
                    ->countAllResults(),
                'becas_rechazadas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Rechazada')
                    ->countAllResults(),
                'becas_pendientes' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $id)
                    ->where('estado', 'Pendiente')
                    ->countAllResults()
            ];

            // Obtener todos los estudiantes con fichas del período
            $estudiantesConFichas = $this->db->table('fichas_socioeconomicas fs')
                ->select('fs.*, u.nombre, u.apellido, u.cedula, u.email, c.nombre as carrera_nombre')
                ->join('usuarios u', 'u.id = fs.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('fs.periodo_id', $id)
                ->orderBy('u.apellido', 'ASC')
                ->orderBy('u.nombre', 'ASC')
                ->get()
                ->getResultArray();

            // Obtener todos los estudiantes con becas del período
            $estudiantesConBecas = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, u.nombre, u.apellido, u.cedula, u.email, c.nombre as carrera_nombre, b.nombre as nombre_beca')
                ->join('usuarios u', 'u.id = sb.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->join('becas b', 'b.id = sb.beca_id', 'left')
                ->where('sb.periodo_id', $id)
                ->orderBy('u.apellido', 'ASC')
                ->orderBy('u.nombre', 'ASC')
                ->get()
                ->getResultArray();

            // Obtener resumen por carreras
            $resumenCarreras = $this->db->table('fichas_socioeconomicas fs')
                ->select('c.nombre as carrera, COUNT(*) as total_fichas, COUNT(CASE WHEN fs.estado = "Aprobada" THEN 1 END) as aprobadas')
                ->join('usuarios u', 'u.id = fs.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('fs.periodo_id', $id)
                ->groupBy('c.id, c.nombre')
                ->orderBy('total_fichas', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'periodo' => $periodo,
                'estadisticas' => $estadisticas,
                'estudiantes_con_fichas' => $estudiantesConFichas,
                'estudiantes_con_becas' => $estudiantesConBecas,
                'resumen_carreras' => $resumenCarreras
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo detalles del período: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al obtener detalles del período'
            ]);
        }
    }

    /**
     * Exportar períodos académicos a Excel o PDF
     */
    public function exportarPeriodos()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $tipo = $this->request->getGet('tipo') ?? 'excel';
            
            // Obtener todos los períodos con estadísticas
            $sql = "
                SELECT p.*, 
                       COUNT(DISTINCT fs.id) as total_fichas,
                       COUNT(DISTINCT sb.id) as total_becas,
                       SUM(CASE WHEN fs.estado = 'Aprobada' THEN 1 ELSE 0 END) as fichas_aprobadas,
                       SUM(CASE WHEN sb.estado = 'Aprobada' THEN 1 ELSE 0 END) as becas_aprobadas
                FROM periodos_academicos p
                LEFT JOIN fichas_socioeconomicas fs ON fs.periodo_id = p.id
                LEFT JOIN solicitudes_becas sb ON sb.periodo_id = p.id
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC
            ";
            
            $periodos = $this->db->query($sql)->getResultArray();

            if ($tipo === 'excel') {
                return $this->exportarPeriodosExcel($periodos);
            } else {
                return $this->exportarPeriodosPDF($periodos);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error exportando períodos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al exportar períodos'
            ]);
        }
    }

    /**
     * Exportar períodos a Excel
     */
    private function exportarPeriodosExcel($periodos)
    {
        $filename = 'periodos_academicos_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'ID', 'Nombre', 'Descripción', 'Fecha Inicio', 'Fecha Fin', 
            'Estado', 'Fichas Activas', 'Becas Activas', 'Total Fichas', 
            'Total Becas', 'Fichas Aprobadas', 'Becas Aprobadas'
        ], ';');
        
        foreach ($periodos as $periodo) {
            $estado = $this->calcularEstadoPeriodo($periodo['fecha_inicio'], $periodo['fecha_fin']);
            
            fputcsv($output, [
                $periodo['id'],
                $periodo['nombre'],
                $periodo['descripcion'] ?? '',
                $periodo['fecha_inicio'],
                $periodo['fecha_fin'],
                $estado,
                $periodo['activo_fichas'] ? 'Sí' : 'No',
                $periodo['activo_becas'] ? 'Sí' : 'No',
                $periodo['total_fichas'] ?? 0,
                $periodo['total_becas'] ?? 0,
                $periodo['fichas_aprobadas'] ?? 0,
                $periodo['becas_aprobadas'] ?? 0
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Exportar períodos a PDF
     */
    private function exportarPeriodosPDF($periodos)
    {
        $filename = 'periodos_academicos_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Crear PDF con TCPDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('ITSI - Sistema de Bienestar Estudiantil');
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle('Períodos Académicos');
        $pdf->SetSubject('Reporte de Períodos Académicos');
        
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'REPORTE DE PERÍODOS ACADÉMICOS', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Fecha del reporte
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Fecha del reporte: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        $pdf->Ln(5);
        
        // Tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        
        // Encabezados de tabla
        $headers = ['ID', 'Nombre', 'Fechas', 'Estado', 'Fichas', 'Becas'];
        $widths = [15, 50, 40, 25, 20, 20];
        
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Datos
        $pdf->SetFont('helvetica', '', 8);
        foreach ($periodos as $periodo) {
            $estado = $this->calcularEstadoPeriodo($periodo['fecha_inicio'], $periodo['fecha_fin']);
            $fechas = date('d/m/Y', strtotime($periodo['fecha_inicio'])) . ' - ' . 
                     date('d/m/Y', strtotime($periodo['fecha_fin']));
            
            $pdf->Cell($widths[0], 6, $periodo['id'], 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $periodo['nombre'], 1, 0, 'L');
            $pdf->Cell($widths[2], 6, $fechas, 1, 0, 'C');
            $pdf->Cell($widths[3], 6, $estado, 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $periodo['total_fichas'] ?? 0, 1, 0, 'C');
            $pdf->Cell($widths[5], 6, $periodo['total_becas'] ?? 0, 1, 0, 'C');
            $pdf->Ln();
        }
        
        // Pie de página
        $pdf->SetY(-15);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Página ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'C');
        
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Calcular estado del período basado en fechas
     */
    private function calcularEstadoPeriodo($fechaInicio, $fechaFin)
    {
        $hoy = date('Y-m-d');
        
        if ($hoy < $fechaInicio) {
            return 'Próximo';
        } elseif ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
            return 'Activo';
        } else {
            return 'Finalizado';
        }
    }

    /**
     * Actualizar contadores de fichas y becas para todos los períodos
     */
    public function actualizarContadoresPeriodos()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Obtener todos los períodos
            $periodos = $this->periodoModel->findAll();
            
            foreach ($periodos as $periodo) {
                // Contar fichas del período
                $totalFichas = $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $periodo['id'])
                    ->countAllResults();
                
                // Contar becas del período
                $totalBecas = $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodo['id'])
                    ->countAllResults();
                
                // Actualizar contadores en la base de datos
                $this->periodoModel->update($periodo['id'], [
                    'fichas_creadas' => $totalFichas,
                    'becas_asignadas' => $totalBecas,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contadores de períodos actualizados exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando contadores: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al actualizar contadores'
            ]);
        }
    }

    /**
     * Ver detalles completos de un período académico (vista completa)
     */
    public function detallePeriodo($periodoId)
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $periodo = $this->periodoModel->find($periodoId);
            if (!$periodo) {
                throw new \Exception('Período no encontrado');
            }

            // Obtener estadísticas del período
            $estadisticas = [
                'fichas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $periodoId)
                    ->countAllResults(),
                'fichas_por_estado' => $this->db->table('fichas_socioeconomicas')
                    ->select('estado, COUNT(*) as total')
                    ->where('periodo_id', $periodoId)
                    ->groupBy('estado')
                    ->get()
                    ->getResultArray(),
                'becas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodoId)
                    ->countAllResults(),
                'solicitudes_becas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodoId)
                    ->countAllResults(),
                'fichas_aprobadas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Aprobada')
                    ->countAllResults(),
                'fichas_rechazadas' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Rechazada')
                    ->countAllResults(),
                'fichas_pendientes' => $this->db->table('fichas_socioeconomicas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Pendiente')
                    ->countAllResults(),
                'becas_aprobadas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Aprobada')
                    ->countAllResults(),
                'becas_rechazadas' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Rechazada')
                    ->countAllResults(),
                'becas_pendientes' => $this->db->table('solicitudes_becas')
                    ->where('periodo_id', $periodoId)
                    ->where('estado', 'Pendiente')
                    ->countAllResults()
            ];

            // Obtener todas las fichas del período con información del estudiante
            $fichas = $this->db->table('fichas_socioeconomicas fs')
                ->select('fs.*, u.nombre, u.apellido, u.cedula, u.email, c.nombre as carrera_nombre')
                ->join('usuarios u', 'u.id = fs.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('fs.periodo_id', $periodoId)
                ->orderBy('u.apellido', 'ASC')
                ->orderBy('u.nombre', 'ASC')
                ->get()
                ->getResultArray();

            // Obtener todas las becas del período con información del estudiante
            $becas = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, u.nombre, u.apellido, u.cedula, u.email, c.nombre as carrera_nombre, b.nombre as nombre_beca')
                ->join('usuarios u', 'u.id = sb.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->join('becas b', 'b.id = sb.beca_id', 'left')
                ->where('sb.periodo_id', $periodoId)
                ->orderBy('u.apellido', 'ASC')
                ->orderBy('u.nombre', 'ASC')
                ->get()
                ->getResultArray();

            // Obtener resumen por carreras
            $resumenCarreras = $this->db->table('fichas_socioeconomicas fs')
                ->select('c.nombre as carrera, COUNT(*) as total_fichas, COUNT(CASE WHEN fs.estado = "Aprobada" THEN 1 END) as aprobadas, COUNT(CASE WHEN fs.estado = "Rechazada" THEN 1 END) as rechazadas, COUNT(CASE WHEN fs.estado = "Pendiente" THEN 1 END) as pendientes')
                ->join('usuarios u', 'u.id = fs.estudiante_id')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('fs.periodo_id', $periodoId)
                ->groupBy('c.id, c.nombre')
                ->orderBy('total_fichas', 'DESC')
                ->get()
                ->getResultArray();

            // Obtener resumen por tipo de beca
            $resumenBecas = $this->db->table('solicitudes_becas sb')
                ->select('b.nombre as tipo_beca, COUNT(*) as total_solicitudes, COUNT(CASE WHEN sb.estado = "Aprobada" THEN 1 END) as aprobadas, COUNT(CASE WHEN sb.estado = "Rechazada" THEN 1 END) as rechazadas, COUNT(CASE WHEN sb.estado = "Pendiente" THEN 1 END) as pendientes')
                ->join('becas b', 'b.id = sb.beca_id', 'left')
                ->where('sb.periodo_id', $periodoId)
                ->groupBy('b.id, b.nombre')
                ->orderBy('total_solicitudes', 'DESC')
                ->get()
                ->getResultArray();

            return view('AdminBienestar/detalle_periodo', [
                'periodo' => $periodo,
                'estadisticas' => $estadisticas,
                'fichas' => $fichas,
                'becas' => $becas,
                'resumen_carreras' => $resumenCarreras,
                'resumen_becas' => $resumenBecas,
                'usuario' => $this->getUsuarioActual()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en detalle período: ' . $e->getMessage());
            return redirect()->to('/admin-bienestar/gestion-periodos')
                ->with('error', 'Error cargando detalles del período');
        }
    }

    /**
     * Exportar solicitudes a Excel
     */
    public function exportarSolicitudes()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Log para debugging
            log_message('info', 'Iniciando exportación de solicitudes');
            
            $sql = "
                SELECT sa.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, 
                       u.cedula as estudiante_cedula, u.email as estudiante_email, u.carrera_id, 
                       c.nombre as carrera_nombre, admin.nombre as responsable_nombre,
                       cat.nombre as categoria_nombre
                FROM solicitudes_ayuda sa
                JOIN usuarios u ON u.id = sa.id_estudiante
                LEFT JOIN carreras c ON c.id = u.carrera_id
                LEFT JOIN usuarios admin ON admin.id = sa.id_responsable
                LEFT JOIN categorias_solicitud_ayuda cat ON cat.id = sa.categoria_id
                ORDER BY sa.fecha_solicitud DESC
            ";
            
            $solicitudes = $this->db->query($sql)->getResultArray();
            
            log_message('info', 'Solicitudes encontradas: ' . count($solicitudes));
            
            if (empty($solicitudes)) {
                log_message('warning', 'No hay solicitudes para exportar');
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No hay solicitudes para exportar'
                ]);
            }
            
            // Crear directorio temp si no existe
            $tempDir = WRITEPATH . 'temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Crear archivo CSV con codificación UTF-8 BOM para Excel
            $filename = 'solicitudes_ayuda_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = $tempDir . $filename;
            
            $fp = fopen($filepath, 'w');
            
            // Agregar BOM UTF-8 para Excel
            fwrite($fp, "\xEF\xBB\xBF");
            
            // Headers con mejor formato
            $headers = [
                'ID', 'Estudiante', 'Cédula', 'Carrera', 'Categoría', 'Asunto', 
                'Prioridad', 'Estado', 'Fecha Solicitud', 'Responsable', 'Descripción',
                'Comentarios Resolución', 'Fecha Actualización'
            ];
            
            fputcsv($fp, $headers, ';'); // Usar punto y coma como separador
            
            // Datos con mejor formato
            foreach ($solicitudes as $solicitud) {
                $row = [
                    $solicitud['id'],
                    trim($solicitud['estudiante_nombre'] . ' ' . $solicitud['estudiante_apellido']),
                    $solicitud['estudiante_cedula'],
                    $solicitud['carrera_nombre'] ?? 'Sin carrera',
                    $solicitud['categoria_nombre'] ?? 'Sin categoría',
                    $solicitud['asunto'],
                    $solicitud['prioridad'],
                    $solicitud['estado'],
                    date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])),
                    $solicitud['responsable_nombre'] ?? 'Sin asignar',
                    str_replace(["\r", "\n"], ' ', $solicitud['descripcion']), // Limpiar saltos de línea
                    str_replace(["\r", "\n"], ' ', $solicitud['comentarios_resolucion'] ?? ''), // Limpiar saltos de línea
                    $solicitud['fecha_actualizacion'] ? date('d/m/Y H:i', strtotime($solicitud['fecha_actualizacion'])) : 'N/A'
                ];
                
                fputcsv($fp, $row, ';');
            }
            
            fclose($fp);
            
            log_message('info', 'Archivo CSV creado exitosamente: ' . $filepath);
            log_message('info', 'Tamaño del archivo: ' . filesize($filepath) . ' bytes');
            
            // Verificar que el archivo se creó correctamente
            if (!file_exists($filepath) || filesize($filepath) < 100) {
                log_message('error', 'Archivo CSV no se creó correctamente o está vacío');
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al crear el archivo CSV'
                ]);
            }
            
            // Configurar headers para descarga
            $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $this->response->setHeader('Content-Length', filesize($filepath));
            $this->response->setHeader('Cache-Control', 'no-cache, must-revalidate');
            $this->response->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
            
            // Leer y enviar archivo
            $content = file_get_contents($filepath);
            
            // Limpiar archivo temporal
            unlink($filepath);
            
            log_message('info', 'Archivo enviado exitosamente');
            
            return $this->response->setBody($content);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando solicitudes: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al exportar solicitudes'
            ]);
        }
    }

    /**
     * Crear respuesta rápida
     */
    public function crearRespuestaRapida()
    {
        if (!$this->verificarPermisos()) {
            log_message('error', 'Respuesta rápida: Usuario no autorizado');
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            
            // Log de entrada
            log_message('info', 'Respuesta rápida - Input recibido: ' . json_encode($input));
            
            if (empty($input)) {
                log_message('error', 'Respuesta rápida: Input vacío o inválido');
                return $this->response->setJSON(['success' => false, 'error' => 'Datos de entrada inválidos']);
            }
            
            $solicitudId = $input['solicitud_id'] ?? null;
            $tipoRespuesta = $input['tipo_respuesta'] ?? '';
            $comentarioAdicional = $input['comentario_adicional'] ?? '';
            $respuestaPersonalizada = $input['respuesta_personalizada'] ?? '';

            // Validar campos obligatorios
            if (empty($solicitudId) || empty($tipoRespuesta)) {
                log_message('error', 'Respuesta rápida: Campos obligatorios faltantes - solicitud_id: ' . $solicitudId . ', tipo_respuesta: ' . $tipoRespuesta);
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud ID y tipo de respuesta son obligatorios']);
            }

            // Verificar que la solicitud existe
            $solicitudExistente = $this->db->table('solicitudes_ayuda')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();
            
            if (!$solicitudExistente) {
                log_message('error', 'Respuesta rápida: Solicitud no encontrada con ID: ' . $solicitudId);
                return $this->response->setJSON(['success' => false, 'error' => 'Solicitud no encontrada']);
            }

            log_message('info', 'Respuesta rápida - Solicitud encontrada: ' . json_encode($solicitudExistente));

            // Plantillas de respuesta rápida
            $plantillas = [
                'saludo' => 'Hola, gracias por contactarnos. Hemos recibido tu solicitud y la estamos procesando.',
                'solicitar_info' => 'Para poder ayudarte mejor, necesito que proporciones información adicional: ' . $comentarioAdicional,
                'resolucion' => 'Tu solicitud ha sido resuelta. Si tienes más preguntas, no dudes en contactarnos.',
                'proceso' => 'Tu solicitud está siendo procesada. Te mantendremos informado sobre el avance.',
                'cerrada' => 'Esta solicitud ha sido cerrada. Si necesitas ayuda adicional, crea una nueva solicitud.',
                'personalizada' => $respuestaPersonalizada
            ];

            $respuesta = $plantillas[$tipoRespuesta] ?? $plantillas['saludo'];

            // Si es respuesta personalizada, usar el texto personalizado
            if ($tipoRespuesta === 'personalizada' && !empty($respuestaPersonalizada)) {
                $respuesta = $respuestaPersonalizada;
            }

            $datos = [
                'comentarios_resolucion' => $respuesta,
                'id_responsable' => $this->getUsuarioActual()['id'],
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            // Actualizar estado según el tipo de respuesta
            if ($tipoRespuesta === 'resolucion') {
                $datos['estado'] = 'Resuelta';
            } elseif ($tipoRespuesta === 'proceso') {
                $datos['estado'] = 'En Proceso';
            } elseif ($tipoRespuesta === 'cerrada') {
                $datos['estado'] = 'Cerrada';
            }

            log_message('info', 'Respuesta rápida - Datos a actualizar: ' . json_encode($datos));

            $resultado = $this->db->table('solicitudes_ayuda')->update($datos, ['id' => $solicitudId]);
            
            log_message('info', 'Respuesta rápida - Resultado de actualización: ' . ($resultado ? 'true' : 'false'));

            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Respuesta rápida enviada exitosamente',
                    'solicitud_id' => $solicitudId,
                    'tipo_respuesta' => $tipoRespuesta
                ]);
            } else {
                log_message('error', 'Respuesta rápida: No se pudo actualizar la solicitud ID: ' . $solicitudId);
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No se pudo actualizar la solicitud'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error creando respuesta rápida: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al crear respuesta rápida'
            ]);
        }
    }

    

    // ========================================
    // CONFIGURACIÓN DE BECAS
    // ========================================

    /**
     * Vista de configuración de becas
     */
    public function configuracionBecas()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $becaModel = new \App\Models\BecaModel();
            $periodoModel = new \App\Models\PeriodoAcademicoModel();
            
            $data = [
                'becas' => $becaModel->getBecasCompletas(),
                'periodos' => $periodoModel->findAll(),
                'tipos_beca' => $becaModel->getTiposBeca(),
                'carreras' => $this->db->table('carreras')->get()->getResultArray(),
                'usuario' => $this->getUsuarioActual()
            ];

            return view('AdminBienestar/configuracion_becas', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error en configuración de becas: ' . $e->getMessage());
            return redirect()->to('/admin-bienestar')
                ->with('error', 'Error cargando configuración de becas');
        }
    }

    /**
     * Configurar documentos requeridos para una beca
     */
    public function configurarDocumentosBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $becaId = $input['beca_id'];
            $documentos = $input['documentos'];

            $this->db->transStart();

            // Desactivar documentos existentes
            $this->db->table('becas_documentos_requisitos')
                ->where('beca_id', $becaId)
                ->update(['estado' => 'Inactivo']);

            // Insertar nuevos documentos
            foreach ($documentos as $index => $documento) {
                $this->db->table('becas_documentos_requisitos')->insert([
                    'beca_id' => $becaId,
                    'nombre' => $documento['nombre'],
                    'descripcion' => $documento['descripcion'] ?? null,
                    'obligatorio' => $documento['obligatorio'] ?? 1,
                    'orden_verificacion' => $index + 1,
                    'estado' => 'Activo',
                    'created_by' => $this->getUsuarioActual()['id'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documentos configurados exitosamente'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error configurando documentos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema al configurar documentos'
            ]);
        }
    }



    // ========================================
    // GESTIÓN DE USUARIOS
    // ========================================

    /**
     * Ver detalles de un usuario específico
     */
    public function verUsuario($id)
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->response->setJSON(['success' => false, 'error' => 'Usuario no encontrado']);
            }

            // Obtener información adicional del usuario
            $carrera = null;
            if ($usuario['carrera_id']) {
                $carrera = $this->db->table('carreras')->where('id', $usuario['carrera_id'])->get()->getRowArray();
            }

            $rol = $this->db->table('roles')->where('id', $usuario['rol_id'])->get()->getRowArray();

            // Si es estudiante, obtener estadísticas
            $estadisticas = null;
            if ($usuario['rol_id'] == 1) {
                $estadisticas = [
                    'fichas' => $this->fichaModel->where('estudiante_id', $id)->countAllResults(),
                    'solicitudes_becas' => $this->db->table('solicitudes_becas')->where('estudiante_id', $id)->countAllResults(),
                    'solicitudes_ayuda' => $this->db->table('solicitudes_ayuda')->where('id_estudiante', $id)->countAllResults()
                ];
            }

            $html = view('AdminBienestar/partials/detalle_usuario', [
                'usuario' => $usuario,
                'carrera' => $carrera,
                'rol' => $rol,
                'estadisticas' => $estadisticas
            ]);

            return $this->response->setJSON(['success' => true, 'html' => $html]);

        } catch (\Exception $e) {
            log_message('error', 'Error viendo usuario: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Crear nuevo usuario
     */
    public function crearUsuario()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);

            // Validaciones
            if (empty($input['nombre']) || empty($input['apellido']) || empty($input['cedula']) || empty($input['email'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Campos obligatorios faltantes']);
            }

            // Verificar si ya existe usuario con la misma cédula o email
            $existente = $this->usuarioModel->where('cedula', $input['cedula'])->orWhere('email', $input['email'])->first();
            if ($existente) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ya existe un usuario con esa cédula o email']);
            }

            $datos = [
                'nombre' => $input['nombre'],
                'apellido' => $input['apellido'],
                'cedula' => $input['cedula'],
                'email' => $input['email'],
                'telefono' => $input['telefono'] ?? null,
                'direccion' => $input['direccion'] ?? null,
                'rol_id' => $input['rol_id'],
                'carrera_id' => ($input['rol_id'] == 1 && !empty($input['carrera_id'])) ? $input['carrera_id'] : null,
                'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
                'activo' => $input['activo'] ?? 1,
                'created_by' => $this->getUsuarioActual()['id'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $usuarioId = $this->usuarioModel->insert($datos);

            if ($usuarioId) {
                $this->logAction('crear_usuario', 'usuarios', $usuarioId, $datos);
            return $this->response->setJSON([
                'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'usuario_id' => $usuarioId
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Error creando usuario']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error creando usuario: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema al crear usuario']);
        }
    }

    /**
     * Cambiar estado de usuario
     */
    public function cambiarEstadoUsuario()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $usuarioId = $input['usuario_id'];
            $activo = (int) ($input['activo'] ?? 0);

            // Solo permitir operar sobre estudiantes
            $usuario = $this->usuarioModel->find($usuarioId);
            if (!$usuario || (int)$usuario['rol_id'] !== ROLE_ESTUDIANTE) {
                return $this->response->setJSON(['success' => false, 'error' => 'No puedes modificar este usuario']);
            }

            $this->usuarioModel->update($usuarioId, ['estado' => $activo ? 'Activo' : 'Inactivo']);

            $this->logAction('cambiar_estado_usuario', 'usuarios', $usuarioId, ['estado' => $activo ? 'Activo' : 'Inactivo']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $activo ? 'Usuario activado' : 'Usuario desactivado'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error cambiando estado usuario: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Resetear contraseña de usuario
     */
    public function resetearPasswordUsuario()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $input = $this->request->getJSON(true);
            $usuarioId = $input['usuario_id'];

            // Solo permitir operar sobre estudiantes
            $usuario = $this->usuarioModel->find($usuarioId);
            if (!$usuario || (int)$usuario['rol_id'] !== ROLE_ESTUDIANTE) {
                return $this->response->setJSON(['success' => false, 'error' => 'No puedes resetear la contraseña de este usuario']);
            }

            // Generar contraseña temporal segura (12 caracteres)
            $nuevaPassword = bin2hex(random_bytes(6));
            $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

            $this->usuarioModel->update($usuarioId, [
                'password_hash' => $passwordHash,
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null
            ]);

            $this->logAction('resetear_password', 'usuarios', $usuarioId, ['password_reseteado' => true]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contraseña reseteada exitosamente',
                'nueva_password' => $nuevaPassword
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error reseteando password: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error del sistema']);
        }
    }

    /**
     * Exportar usuarios
     */
    public function exportarUsuarios()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $formato = $this->request->getGet('formato') ?? 'excel';
            
            // Obtener estudiantes del rol estudiante (rol_id = 1)
            $usuarios = $this->db->table('usuarios u')
                ->select('u.*, c.nombre as carrera_nombre')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->where('u.rol_id', 1)
                ->orderBy('u.created_at', 'DESC')
                ->get()
                ->getResultArray();

            if (!empty($usuarios)) {
                $ids = array_column($usuarios, 'id');
                $counts = $this->db->query("
                    SELECT u.id,
                        COUNT(DISTINCT fs.id) as total_fichas,
                        COUNT(DISTINCT sb.id) as total_becas,
                        COUNT(DISTINCT sa.id) as total_ayudas
                    FROM usuarios u
                    LEFT JOIN fichas_socioeconomicas fs ON fs.estudiante_id = u.id
                    LEFT JOIN solicitudes_becas sb ON sb.estudiante_id = u.id
                    LEFT JOIN solicitudes_ayuda sa ON sa.id_estudiante = u.id
                    WHERE u.id IN (" . implode(',', $ids) . ")
                    GROUP BY u.id
                ")->getResultArray();

                $countsMap = [];
                foreach ($counts as $row) {
                    $countsMap[$row['id']] = $row;
                }

                foreach ($usuarios as &$est) {
                    $c = $countsMap[$est['id']] ?? null;
                    $est['total_fichas'] = $c ? (int)$c['total_fichas'] : 0;
                    $est['total_becas'] = $c ? (int)$c['total_becas'] : 0;
                    $est['total_ayudas'] = $c ? (int)$c['total_ayudas'] : 0;
                }
                unset($est);
            }

            if ($formato === 'excel') {
                return $this->exportarUsuariosExcel($usuarios);
            } else {
                return $this->exportarUsuariosPDF($usuarios);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando usuarios: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema al exportar usuarios');
        }
    }

    private function exportarUsuariosExcel($usuarios)
    {
        $filename = 'estudiantes_' . date('Y-m-d_H-i-s') . '.csv';
        
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
        
        // Encabezados
        fputcsv($output, [
            'ID', 'Cédula', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 
            'Carrera', 'Fecha Registro', 'Fichas', 'Becas', 'Ayudas'
        ], ';');
        
        foreach ($usuarios as $u) {
            fputcsv($output, [
                $u['id'],
                $u['cedula'] ?? '',
                $u['nombre'] ?? '',
                $u['apellido'] ?? '',
                $u['email'] ?? '',
                $u['telefono'] ?? '',
                $u['carrera_nombre'] ?? 'Sin especificar',
                date('d/m/Y', strtotime($u['created_at'])),
                $u['total_fichas'] ?? 0,
                $u['total_becas'] ?? 0,
                $u['total_ayudas'] ?? 0
            ], ';');
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $this->response->download($filename, $csvContent)
            ->setContentType('text/csv');
    }

    private function exportarUsuariosPDF($usuarios)
    {
        $filename = 'estudiantes_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('ITSI - Sistema de Bienestar Estudiantil');
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle('Lista de Estudiantes');
        
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'REPORTE DE ESTUDIANTES', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Fecha del reporte
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        $pdf->Ln(5);
        
        // Tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        
        $headers = ['Cédula', 'Estudiante', 'Correo', 'Carrera', 'Fichas', 'Becas', 'Ayudas'];
        $widths = [30, 60, 60, 67, 20, 20, 20];
        
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        $pdf->SetFont('helvetica', '', 8);
        foreach ($usuarios as $u) {
            $nombreCompleto = ($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '');
            
            $pdf->Cell($widths[0], 6, $u['cedula'] ?? '', 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $nombreCompleto, 1, 0, 'L');
            $pdf->Cell($widths[2], 6, $u['email'] ?? '', 1, 0, 'L');
            $pdf->Cell($widths[3], 6, $u['carrera_nombre'] ?? 'Sin especificar', 1, 0, 'L');
            $pdf->Cell($widths[4], 6, $u['total_fichas'] ?? 0, 1, 0, 'C');
            $pdf->Cell($widths[5], 6, $u['total_becas'] ?? 0, 1, 0, 'C');
            $pdf->Cell($widths[6], 6, $u['total_ayudas'] ?? 0, 1, 0, 'C');
            $pdf->Ln();
        }
        
        $pdfContent = $pdf->Output($filename, 'S');
        return $this->response->download($filename, $pdfContent);
    }

    public function generarConstancia()
    {
        if (!$this->verificarPermisos()) {
            return redirect()->to('/login');
        }

        try {
            $id = $this->request->getGet('id');
            if (empty($id)) {
                return redirect()->back()->with('error', 'ID de solicitud no proporcionado');
            }

            // Obtener detalles de la solicitud de beca
            $solicitud = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, u.nombre, u.apellido, u.cedula, u.email, b.nombre as beca_nombre, b.tipo_beca, p.nombre as periodo_nombre')
                ->join('usuarios u', 'u.id = sb.estudiante_id')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos p', 'p.id = sb.periodo_id')
                ->where('sb.id', $id)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            if ($solicitud['estado'] !== 'Aprobada') {
                return redirect()->back()->with('error', 'La constancia solo se puede generar para solicitudes aprobadas');
            }

            $filename = 'Constancia_Beca_' . $solicitud['cedula'] . '_' . date('Ymd') . '.pdf';

            // Crear PDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
            $pdf->SetCreator('ITSI - Sistema de Bienestar Estudiantil');
            $pdf->SetAuthor('Bienestar Estudiantil ITSI');
            $pdf->SetTitle('Constancia de Beca');
            
            // Quitar headers/footers por defecto para un estilo personalizado
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            $pdf->AddPage();
            
            // Margen
            $pdf->SetMargins(20, 20, 20);
            
            // Decoración - Línea superior elegante
            $pdf->SetFillColor(102, 126, 234); // Color primario #667eea
            $pdf->Rect(0, 0, 210, 10, 'F');
            
            $pdf->Ln(15);
            
            // Membrete / Encabezado
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor(74, 85, 104);
            $pdf->Cell(0, 6, 'INSTITUTO SUPERIOR TECNOLÓGICO DE IMBABURA', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 4, 'DEPARTAMENTO DE BIENESTAR ESTUDIANTIL', 0, 1, 'C');
            $pdf->Cell(0, 4, 'SISTEMA DE GESTIÓN GIBI-ITSI', 0, 1, 'C');
            
            // Línea divisoria
            $pdf->Ln(5);
            $pdf->SetDrawColor(226, 232, 240);
            $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
            $pdf->Ln(15);
            
            // Título de la Constancia
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->SetTextColor(26, 32, 44);
            $pdf->Cell(0, 10, 'CERTIFICADO DE BECA ESTUDIANTIL', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Texto del cuerpo
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetTextColor(45, 55, 72);
            
            $nombreEstudiante = mb_strtoupper(($solicitud['nombre'] ?? '') . ' ' . ($solicitud['apellido'] ?? ''), 'UTF-8');
            $cedulaEstudiante = $solicitud['cedula'] ?? '';
            $becaNombre = $solicitud['beca_nombre'] ?? '';
            $tipoBeca = $solicitud['tipo_beca'] ?? '';
            $periodoNombre = $solicitud['periodo_nombre'] ?? '';
            $fechaAprobacion = date('d/m/Y', strtotime($solicitud['updated_at'] ?? $solicitud['fecha_solicitud']));
            
            $htmlCuerpo = "El Departamento de Bienestar Estudiantil del <b>Instituto Superior Tecnológico de Imbabura (ITSI)</b>, por medio del presente documento:<br><br>" .
                          "HACE CONSTAR que el/la estudiante <b>{$nombreEstudiante}</b>, portador/a del documento de identidad/cédula Nro. <b>{$cedulaEstudiante}</b>, " .
                          "ha sido formalmente adjudicado/a con la <b>{$becaNombre}</b> (Tipo: {$tipoBeca}) para el período académico <b>{$periodoNombre}</b>.<br><br>" .
                          "La solicitud correspondiente fue debidamente revisada y aprobada el día <b>{$fechaAprobacion}</b> al cumplir satisfactoriamente " .
                          "con las condiciones socioeconómicas, académicas y reglamentarias exigidas por la institución para la asignación de este beneficio.<br><br>" .
                          "Para constancia de lo actuado y a petición de la parte interesada, se extiende el presente certificado en la provincia de Imbabura, " .
                          "el día <b>" . date('d') . "</b> de <b>" . $this->getNombreMes(date('n')) . "</b> del año <b>" . date('Y') . "</b>.";
            
            $pdf->writeHTML($htmlCuerpo, true, false, true, false, 'J');
            $pdf->Ln(25);
            
            // Sección de firma
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 5, 'Atentamente,', 0, 1, 'C');
            $pdf->Ln(15);
            
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 5, 'DEPARTAMENTO DE BIENESTAR ESTUDIANTIL', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 5, 'Instituto Superior Tecnológico de Imbabura', 0, 1, 'C');
            
            // Código de verificación de seguridad en el pie de página
            $codigoVerificacion = strtoupper(substr(md5($solicitud['id'] . '-' . $solicitud['cedula'] . '-itsi'), 0, 16));
            
            $pdf->SetY(-30);
            $pdf->SetDrawColor(226, 232, 240);
            $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
            $pdf->Ln(2);
            
            $pdf->SetFont('helvetica', 'I', 7);
            $pdf->SetTextColor(113, 128, 150);
            $pdf->Cell(0, 4, 'Código de Verificación Único de Seguridad: ' . $codigoVerificacion, 0, 1, 'C');
            $pdf->Cell(0, 4, 'Documento oficial generado de forma automática por la plataforma de Bienestar GIBI-ITSI.', 0, 1, 'C');
            
            $pdfContent = $pdf->Output($filename, 'S');
            return $this->response->download($filename, $pdfContent);
            
        } catch (\Exception $e) {
            log_message('error', 'Error generando constancia de beca: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema al generar constancia');
        }
    }

    private function getNombreMes($mesNum)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[$mesNum] ?? '';
    }

    /**
     * Log de acciones para auditoría
     */
    private function logAction($accion, $tabla, $registroId, $datos)
    {
        try {
            $usuario = $this->getUsuarioActual();
            $this->db->table('logs')->insert([
                'id_usuario' => $usuario['id'],
                'accion' => $accion,
                'tabla' => $tabla,
                'registro_id' => $registroId,
                'datos' => json_encode($datos),
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]);

            // También registrar en SecurityLogger para monitoreo de seguridad
            $nivel = in_array($accion, ['eliminar_beca', 'eliminar_periodo', 'resetear_password', 'cambiar_estado_usuario']) 
                ? \App\Security\SecurityLogger::LEVEL_DANGER 
                : \App\Security\SecurityLogger::LEVEL_INFO;
            $this->securityLogger->log(
                $nivel,
                strtoupper($accion),
                "Acción '{$accion}' en {$tabla} #{$registroId} por usuario {$usuario['id']}",
                [
                    'tabla' => $tabla,
                    'registro_id' => $registroId,
                    'usuario_id' => $usuario['id'],
                    'accion' => $accion
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error logging action: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estudiantes sin beca para evaluación automática
     */
    public function estudiantesSinBeca()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            // Obtener fichas socioeconómicas de estudiantes que NO tienen solicitudes de beca activas
            $sql = "
                SELECT 
                    fs.id,
                    fs.estudiante_id,
                    fs.periodo_id,
                    fs.estado,
                    fs.json_data,
                    fs.fecha_creacion,
                    u.nombre,
                    u.apellido,
                    u.cedula,
                    u.email,
                    c.nombre as carrera_nombre,
                    p.nombre as periodo_nombre
                FROM fichas_socioeconomicas fs
                INNER JOIN usuarios u ON u.id = fs.estudiante_id
                LEFT JOIN carreras c ON c.id = u.carrera_id
                INNER JOIN periodos_academicos p ON p.id = fs.periodo_id
                WHERE u.rol_id = 1 
                AND fs.estado IN ('Enviada', 'Revisada')
                AND NOT EXISTS (
                    SELECT 1 FROM solicitudes_becas sb 
                    WHERE sb.estudiante_id = fs.estudiante_id 
                    AND sb.periodo_id = fs.periodo_id
                    AND sb.estado IN ('Postulada', 'Aprobada', 'Lista de Espera')
                )
                ORDER BY fs.fecha_creacion DESC
            ";
            
            $estudiantes = $this->db->query($sql)->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'estudiantes' => $estudiantes,
                'total' => count($estudiantes)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estudiantes sin beca: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Error del sistema al obtener estudiantes'
            ]);
        }
    }

    /**
     * Verificar código de PDF
     */
    public function verificarCodigoPDF()
    {
        // Verificar si es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }
        
        // Obtener datos del request
        $json = $this->request->getJSON();
        $codigo = $json->codigo ?? '';
        
        if (empty($codigo)) {
            return $this->response->setJSON([
                'valido' => false,
                'mensaje' => 'Código no proporcionado'
            ]);
        }
        
        try {
            // Log para debug
            log_message('info', 'Verificando código: ' . $codigo);
            
            // Verificar código
            $codigoModel = new \App\Models\PdfCodigoVerificacionModel();
            $resultado = $codigoModel->verificarCodigo($codigo);
            
            // Log del resultado
            log_message('info', 'Resultado de verificación: ' . json_encode($resultado));
            
            // Agregar información adicional del documento si es válido
            if ($resultado['valido'] && $resultado['datos']) {
                $resultado['datos']['informacion_documento'] = $this->obtenerInformacionDocumento(
                    $resultado['datos']['tipo_documento'], 
                    $resultado['datos']['id_documento']
                );
                
                // Log de la información del documento
                log_message('info', 'Información del documento: ' . json_encode($resultado['datos']['informacion_documento']));
            }
            
            return $this->response->setJSON($resultado);
            
        } catch (\Exception $e) {
            log_message('error', 'Error verificando código PDF: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'valido' => false,
                'mensaje' => 'Error interno del servidor al verificar código'
            ]);
        }
    }
    
    /**
     * Obtener información del documento para mostrar en verificación
     */
    private function obtenerInformacionDocumento($tipoDocumento, $idDocumento)
    {
        try {
            switch ($tipoDocumento) {
                case 'ficha_socioeconomica':
                    $fichaModel = new \App\Models\FichaSocioeconomicaModel();
                    $ficha = $fichaModel->getFichaCompletaAdmin($idDocumento);
                    
                    if ($ficha) {
                        return [
                            'tipo' => 'Ficha Socioeconómica',
                            'estudiante' => $ficha['estudiante_nombre'] ?? 'N/A',
                            'cedula' => $ficha['cedula'] ?? 'N/A',
                            'carrera' => $ficha['carrera_nombre'] ?? 'N/A',
                            'periodo' => $ficha['periodo_nombre'] ?? 'N/A',
                            'estado' => $ficha['estado'] ?? 'N/A',
                            'fecha_creacion' => $ficha['fecha_creacion'] ?? 'N/A'
                        ];
                    }
                    break;
                    
                case 'solicitud_beca':
                    $becaModel = new \App\Models\BecaModel();
                    $beca = $becaModel->find($idDocumento);
                    
                    if ($beca) {
                        return [
                            'tipo' => 'Solicitud de Beca',
                            'estudiante' => $beca['estudiante_nombre'] ?? 'N/A',
                            'tipo_beca' => $beca['tipo_beca'] ?? 'N/A',
                            'estado' => $beca['estado'] ?? 'N/A',
                            'fecha_solicitud' => $beca['fecha_solicitud'] ?? 'N/A'
                        ];
                    }
                    break;
                    
                case 'solicitud_ayuda':
                    // Implementar para solicitudes de ayuda
                    return [
                        'tipo' => 'Solicitud de Ayuda',
                        'id' => $idDocumento,
                        'estado' => 'Información no disponible'
                    ];
                    
                default:
                    return [
                        'tipo' => 'Documento',
                        'id' => $idDocumento,
                        'tipo_documento' => $tipoDocumento
                    ];
            }
            
            return [
                'tipo' => 'Documento no encontrado',
                'id' => $idDocumento,
                'estado' => 'Información no disponible'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo información del documento: ' . $e->getMessage());
            return [
                'tipo' => 'Error al obtener información',
                'id' => $idDocumento,
                'estado' => 'Error al procesar documento'
            ];
        }
    }

    /**
     * Ver documento de forma segura (solo para administradores)
     */
    public function verDocumento($documentoId)
    {
        // Verificar que sea admin
        if (session('rol_id') != 2) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }

        try {
            // Obtener información del documento
            $documento = $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ]);
            }

            // Verificar que el archivo existe
            $rutaCompleta = FCPATH . $documento['ruta_archivo'];
            if (!file_exists($rutaCompleta)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ]);
            }

            // Obtener información del archivo
            $tipoMime = mime_content_type($rutaCompleta);
            $tamaño = filesize($rutaCompleta);

            // Verificar que sea un PDF
            if ($tipoMime !== 'application/pdf') {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Solo se permiten archivos PDF'
                ]);
            }

            // Servir el archivo
            return $this->response
                ->setHeader('Content-Type', $tipoMime)
                ->setHeader('Content-Disposition', 'inline; filename="' . basename($documento['nombre_archivo']) . '"')
                ->setHeader('Content-Length', $tamaño)
                ->setHeader('Cache-Control', 'no-cache, must-revalidate')
                ->setHeader('Pragma', 'no-cache')
                ->setBody(file_get_contents($rutaCompleta));

        } catch (Exception $e) {
            log_message('error', 'Error al ver documento: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error del sistema al cargar el documento'
            ]);
        }
    }

    /**
     * Procesar evaluación automática masiva y guardar en base de datos
     */
    public function guardarEvaluacionMasiva()
    {
        if (!$this->verificarPermisos()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        try {
            $json = $this->request->getJSON();
            if (!$json) {
                return $this->response->setJSON(['success' => false, 'error' => 'Datos inválidos']);
            }

            $rangoAInicio = floatval($json->rangoAInicio ?? 100);
            $rangoAFin = floatval($json->rangoAFin ?? 500);
            $rangoBInicio = floatval($json->rangoBInicio ?? 501);
            $rangoBFin = floatval($json->rangoBFin ?? 1000);
            $rangoCInicio = floatval($json->rangoCInicio ?? 1001);
            $rangoCFin = floatval($json->rangoCFin ?? 1500);

            // Obtener estudiantes sin beca que no están evaluados
            $sql = "
                SELECT 
                    fs.id,
                    fs.estudiante_id,
                    fs.periodo_id,
                    fs.estado,
                    fs.json_data,
                    fs.revisada_por_admin
                FROM fichas_socioeconomicas fs
                INNER JOIN usuarios u ON u.id = fs.estudiante_id
                WHERE u.rol_id = 1 
                AND fs.estado IN ('Enviada', 'Revisada')
                AND fs.revisada_por_admin = 0
                AND NOT EXISTS (
                    SELECT 1 FROM solicitudes_becas sb 
                    WHERE sb.estudiante_id = fs.estudiante_id 
                    AND sb.periodo_id = fs.periodo_id
                    AND sb.estado IN ('Postulada', 'Aprobada', 'Lista de Espera')
                )
            ";
            
            $fichas = $this->db->query($sql)->getResultArray();
            $adminId = session('id');
            $now = date('Y-m-d H:i:s');
            $countEvaluated = 0;

            $this->db->transStart();

            foreach ($fichas as $ficha) {
                $datosFicha = json_decode($ficha['json_data'], true) ?: [];
                $ingresosPadre = floatval($datosFicha['ingresos_padre'] ?? 0);
                $ingresosMadre = floatval($datosFicha['ingresos_madre'] ?? 0);
                $otrosIngresos = floatval($datosFicha['otros_ingresos'] ?? 0);
                $totalIngresos = $ingresosPadre + $ingresosMadre + $otrosIngresos;

                if ($totalIngresos === 0.0) {
                    continue; // Saltar si no tiene ingresos especificados
                }

                $categoria = null;
                $puntaje = 0.00;

                if ($totalIngresos >= $rangoAInicio && $totalIngresos <= $rangoAFin) {
                    $categoria = 'A';
                    $puntaje = 3.00;
                } elseif ($totalIngresos >= $rangoBInicio && $totalIngresos <= $rangoBFin) {
                    $categoria = 'B';
                    $puntaje = 2.00;
                } elseif ($totalIngresos >= $rangoCInicio && $totalIngresos <= $rangoCFin) {
                    $categoria = 'C';
                    $puntaje = 1.00;
                }

                if ($categoria !== null) {
                    $this->fichaModel->update($ficha['id'], [
                        'revisada_por_admin' => 1,
                        'fecha_revision_admin' => $now,
                        'puntaje_calculado' => $puntaje,
                        'observaciones_admin' => 'Evaluado automáticamente mediante el proceso masivo',
                        'estado' => 'Aprobada',
                        'fecha_revision' => $now,
                        'revisado_por' => $adminId
                    ]);

                    $this->securityLogger->logAction(
                        $adminId, 
                        'Evaluación Socioeconómica Masiva', 
                        'fichas_socioeconomicas', 
                        $ficha['id'], 
                        "Categoría: $categoria, Puntaje: $puntaje"
                    );

                    $countEvaluated++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al guardar la transacción de evaluación masiva'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Proceso completado. Se evaluaron y aprobaron exitosamente {$countEvaluated} estudiantes.",
                'evaluados' => $countEvaluated
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en evaluación masiva: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }
} 