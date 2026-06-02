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

    // ========================================================================
    // MÉTODOS @deprecated - Fachada que redirige a los nuevos controladores
    // ========================================================================

    /**
     * @deprecated Usar Estudiante\FichasController::fichaSocioeconomica()
     */
    public function fichaSocioeconomica()
    {
        return redirect()->to(base_url('estudiante/ficha-socioeconomica'));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::crearFicha()
     */
    public function crearFicha()
    {
        return redirect()->to(base_url('estudiante/ficha-socioeconomica'));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::testCrearFicha()
     */
    public function testCrearFicha()
    {
        return redirect()->to(base_url('estudiante/ficha-socioeconomica'));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::verFicha()
     */
    public function verFicha($id)
    {
        return redirect()->to(base_url('estudiante/ver-ficha/' . $id));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::enviarFicha()
     */
    public function enviarFicha()
    {
        return redirect()->to(base_url('estudiante/ficha-socioeconomica'));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::exportarFichaPDF()
     */
    public function exportarFichaPDF($id)
    {
        return redirect()->to(base_url('estudiante/exportar-ficha-pdf/' . $id));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::editarFicha()
     */
    public function editarFicha($id)
    {
        return redirect()->to(base_url('estudiante/editar-ficha/' . $id));
    }

    /**
     * @deprecated Usar Estudiante\FichasController::actualizarFicha()
     */
    public function actualizarFicha()
    {
        return redirect()->to(base_url('estudiante/ficha-socioeconomica'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::becas()
     */
    public function becas()
    {
        return redirect()->to(base_url('estudiante/becas'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::solicitarBeca()
     */
    public function solicitarBeca()
    {
        return redirect()->to(base_url('estudiante/becas'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::cancelarSolicitudBeca()
     */
    public function cancelarSolicitudBeca()
    {
        return redirect()->to(base_url('estudiante/becas'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::detalleBeca()
     */
    public function detalleBeca($becaId)
    {
        return redirect()->to(base_url('estudiante/detalleBeca/' . $becaId));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::obtenerBecasDisponibles()
     */
    public function obtenerBecasDisponibles()
    {
        return redirect()->to(base_url('estudiante/becas'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::estadoSolicitudBeca()
     */
    public function estadoSolicitudBeca($id)
    {
        return redirect()->to(base_url('estudiante/estado-solicitud-beca/' . $id));
    }

    /**
     * @deprecated Usar Estudiante\SolicitudesController::solicitudesAyuda()
     */
    public function solicitudesAyuda()
    {
        return redirect()->to(base_url('estudiante/solicitudes-ayuda'));
    }

    /**
     * @deprecated Usar Estudiante\SolicitudesController::crearSolicitudAyuda()
     */
    public function crearSolicitudAyuda()
    {
        return redirect()->to(base_url('estudiante/solicitudes-ayuda'));
    }

    /**
     * @deprecated Usar Estudiante\SolicitudesController::editarSolicitudAyuda()
     */
    public function editarSolicitudAyuda()
    {
        return redirect()->to(base_url('estudiante/solicitudes-ayuda'));
    }

    /**
     * @deprecated Usar Estudiante\SolicitudesController::cancelarSolicitudAyuda()
     */
    public function cancelarSolicitudAyuda()
    {
        return redirect()->to(base_url('estudiante/solicitudes-ayuda'));
    }

    /**
     * @deprecated Usar Estudiante\DocumentosController::documentos()
     */
    public function documentos()
    {
        return redirect()->to(base_url('estudiante/documentos'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::perfil()
     */
    public function perfil()
    {
        return redirect()->to(base_url('estudiante/perfil'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::cuenta()
     */
    public function cuenta()
    {
        return redirect()->to(base_url('estudiante/cuenta'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::actualizarPerfil()
     */
    public function actualizarPerfil()
    {
        return redirect()->to(base_url('estudiante/perfil'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::cambiarFoto()
     */
    public function cambiarFoto()
    {
        return redirect()->to(base_url('estudiante/perfil'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::cambiarPassword()
     */
    public function cambiarPassword()
    {
        return redirect()->to(base_url('estudiante/cuenta'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::configurarNotificaciones()
     */
    public function configurarNotificaciones()
    {
        return redirect()->to(base_url('estudiante/cuenta'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::exportarDatos()
     */
    public function exportarDatos()
    {
        return redirect()->to(base_url('estudiante/exportar-datos'));
    }

    /**
     * @deprecated Usar Estudiante\PerfilController::eliminarCuenta()
     */
    public function eliminarCuenta()
    {
        return redirect()->to(base_url('estudiante/cuenta'));
    }

    /**
     * @deprecated Usar Estudiante\InformacionController::informacionServicios()
     */
    public function informacionServicios()
    {
        return redirect()->to(base_url('estudiante/informacion/servicios'));
    }

    /**
     * @deprecated Usar Estudiante\BecasController::informacionBecas()
     */
    public function informacionBecas()
    {
        return redirect()->to(base_url('estudiante/informacion/becas'));
    }

    /**
     * @deprecated Usar Estudiante\InformacionController::informacionPsicologia()
     */
    public function informacionPsicologia()
    {
        return redirect()->to(base_url('estudiante/informacion/psicologia'));
    }

    /**
     * @deprecated Usar Estudiante\InformacionController::informacionSalud()
     */
    public function informacionSalud()
    {
        return redirect()->to(base_url('estudiante/informacion/salud'));
    }

    /**
     * @deprecated Usar Estudiante\InformacionController::informacionTrabajoSocial()
     */
    public function informacionTrabajoSocial()
    {
        return redirect()->to(base_url('estudiante/informacion/trabajo-social'));
    }

    /**
     * @deprecated Usar Estudiante\InformacionController::informacionOrientacionAcademica()
     */
    public function informacionOrientacionAcademica()
    {
        return redirect()->to(base_url('estudiante/informacion/orientacion-academica'));
    }

    // ========================================================================
    // MÉTODOS STUB - Rutas legacy que no tenían implementación previa
    // ========================================================================

    /**
     * @deprecated Sin implementación activa. Mantenido solo por compatibilidad de rutas.
     */
    public function verificarHabilitacionBecas()
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    /**
     * @deprecated Sin implementación activa. Mantenido solo por compatibilidad de rutas.
     */
    public function detalleSolicitudBeca($id)
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    /**
     * @deprecated Sin implementación activa. Mantenido solo por compatibilidad de rutas.
     */
    public function solicitudesAyudaMejorada()
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    // ========================================================================
    // MÉTODOS PRIVADOS (solo usados internamente)
    // ========================================================================

    private function generarHTMLFicha($ficha)
    {
        // @deprecated - Movido a Estudiante\FichasController
        return '';
    }

    private function getEstadoColor($estado)
    {
        // @deprecated - Movido a Estudiante\FichasController
        return 'secondary';
    }

    private function mostrarArray($array)
    {
        // @deprecated - Movido a Estudiante\FichasController
        return 'No especificado';
    }
}
