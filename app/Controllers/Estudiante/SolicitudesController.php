<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\SolicitudAyudaModel;
use App\Models\CategoriaSolicitudAyudaModel;
use App\Security\InputSanitizerTrait;

class SolicitudesController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
    protected $usuarioModel;
    protected $solicitudModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->solicitudModel = new SolicitudAyudaModel();
        $this->db = \Config\Database::connect();
    }

    public function solicitudesAyuda()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $categoriaModel = new CategoriaSolicitudAyudaModel();
        
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $totalSolicitudes = $this->solicitudModel->where('id_estudiante', session('id'))->countAllResults();
        $solicitudes = $this->solicitudModel->where('id_estudiante', session('id'))
                                           ->orderBy('fecha_solicitud', 'DESC')
                                           ->limit($perPage, $offset)
                                           ->findAll();
        $totalPages = max(1, ceil($totalSolicitudes / $perPage));
        
        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'solicitudes' => $solicitudes,
            'categorias' => $categoriaModel->getCategoriasActivas(),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total' => $totalSolicitudes
        ];

        return view('estudiante/solicitudes_ayuda', $data);
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
            $categoriaModel = new CategoriaSolicitudAyudaModel();
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
            
            $categoriaModel = new CategoriaSolicitudAyudaModel();
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
}
