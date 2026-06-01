<?php

namespace App\Controllers;

use App\Models\SolicitudAyudaModel;
use App\Models\RespuestaSolicitudModel;
use App\Security\InputSanitizerTrait;

class SolicitudController extends BaseController
{
    use InputSanitizerTrait;

    protected $solicitudModel;
    protected $respuestaModel;

    public function __construct()
    {
        $this->solicitudModel = new SolicitudAyudaModel();
        $this->respuestaModel = new RespuestaSolicitudModel();
    }

    public function index()
    {
        if (session('rol_id') == ROLE_ESTUDIANTE) {
            return redirect()->to('estudiante/solicitudes-ayuda');
        } elseif (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/solicitudes');
        }
        return redirect()->to('/login');
    }

    public function adminIndex()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/solicitudes');
        }
        return redirect()->to('/login');
    }

    public function comunicacion()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/solicitudes_comunicacion');
        }
        return redirect()->to('/login');
    }

    public function integracion()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/solicitudes_integracion');
        }
        return redirect()->to('/login');
    }

    /**
     * Obtiene todas las solicitudes para el dashboard
     */
    public function getSolicitudes()
    {
        $solicitudes = $this->solicitudModel->getSolicitudesConInformacion();
        return $this->response->setJSON($solicitudes);
    }

    /**
     * Verifica que el usuario tenga permiso sobre una solicitud (IDOR protection)
     */
    private function verificarPermisoSolicitud($id)
    {
        $solicitud = $this->solicitudModel->find($id);
        
        if (!$solicitud) {
            return null; // No encontrada
        }
        
        $userId = session('id');
        $rolId = session('rol_id');
        
        // Admin Bienestar (rol 2) puede ver cualquier solicitud
        if ($rolId == ROLE_ADMIN_BIENESTAR) {
            return $solicitud;
        }
        
        // Estudiante (rol 1) solo puede ver sus propias solicitudes
        if ($rolId == ROLE_ESTUDIANTE && $solicitud['id_estudiante'] == $userId) {
            return $solicitud;
        }
        
        return false; // Sin permiso
    }

    /**
     * Obtiene una solicitud específica
     */
    public function getSolicitud($id)
    {
        $solicitud = $this->verificarPermisoSolicitud($id);
        
        if ($solicitud === null) {
            return $this->response->setJSON(['error' => 'Solicitud no encontrada'])->setStatusCode(404);
        }
        
        if ($solicitud === false) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }
        
        return $this->response->setJSON($solicitud);
    }

    /**
     * Crea una nueva solicitud
     */
    public function crear()
    {
        try {
            $data = [
                'id_estudiante' => session('id'),
                'asunto' => $this->getPostString('asunto'),
                'descripcion' => $this->getPostString('descripcion'),
                'prioridad' => $this->getPostString('prioridad', 'Media'),
                'estado' => 'Pendiente',
                'fecha_solicitud' => date('Y-m-d H:i:s'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            if ($this->solicitudModel->insert($data)) {
                return $this->response->setJSON(['success' => 'Solicitud creada exitosamente']);
            } else {
                return $this->response->setJSON(['error' => 'Error al crear la solicitud'])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creando solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error del sistema'])->setStatusCode(500);
        }
    }

    /**
     * Actualiza una solicitud
     */
    public function actualizar($id)
    {
        try {
            $solicitud = $this->verificarPermisoSolicitud($id);
            
            if ($solicitud === null) {
                return $this->response->setJSON(['error' => 'Solicitud no encontrada'])->setStatusCode(404);
            }
            
            if ($solicitud === false) {
                return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
            }
            
            $data = [
                'asunto' => $this->getPostString('asunto'),
                'descripcion' => $this->getPostString('descripcion'),
                'estado' => $this->getPostString('estado'),
                'prioridad' => $this->getPostString('prioridad'),
                'id_responsable' => $this->getPostInt('id_responsable'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ];

            if ($this->solicitudModel->update($id, $data)) {
                return $this->response->setJSON(['success' => 'Solicitud actualizada exitosamente']);
            } else {
                return $this->response->setJSON(['error' => 'Error al actualizar la solicitud'])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando solicitud: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error del sistema'])->setStatusCode(500);
        }
    }

    /**
     * Elimina una solicitud
     */
    public function eliminar($id)
    {
        $solicitud = $this->verificarPermisoSolicitud($id);
        
        if ($solicitud === null) {
            return $this->response->setJSON(['error' => 'Solicitud no encontrada'])->setStatusCode(404);
        }
        
        if ($solicitud === false) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }
        
        if ($this->solicitudModel->delete($id)) {
            return $this->response->setJSON(['success' => 'Solicitud eliminada exitosamente']);
        } else {
            return $this->response->setJSON(['error' => 'Error al eliminar la solicitud'])->setStatusCode(500);
        }
    }

    /**
     * Asigna una solicitud a un administrativo
     */
    public function asignar($id)
    {
        $adminId = $this->getPostInt('admin_id');
        
        if ($this->solicitudModel->asignarSolicitud($id, $adminId)) {
            return $this->response->setJSON(['success' => 'Solicitud asignada exitosamente']);
        } else {
            return $this->response->setJSON(['error' => 'Error al asignar la solicitud'])->setStatusCode(500);
        }
    }

    /**
     * Cambia el estado de una solicitud
     */
    public function cambiarEstado($id)
    {
        $estado = $this->getPostString('estado');
        
        if ($this->solicitudModel->cambiarEstado($id, $estado)) {
            return $this->response->setJSON(['success' => 'Estado cambiado exitosamente']);
        } else {
            return $this->response->setJSON(['error' => 'Error al cambiar el estado'])->setStatusCode(500);
        }
    }

    /**
     * Obtiene las respuestas de una solicitud
     */
    public function getRespuestas($solicitudId)
    {
        $respuestas = $this->respuestaModel->getRespuestasConInformacion($solicitudId);
        return $this->response->setJSON($respuestas);
    }

    /**
     * Agrega una respuesta a una solicitud
     */
    public function agregarRespuesta($solicitudId)
    {
        $data = [
            'solicitud_id' => $solicitudId,
            'usuario_id' => session('id'),
            'mensaje' => $this->getPostString('mensaje'),
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];

        if ($this->respuestaModel->insert($data)) {
            return $this->response->setJSON(['success' => 'Respuesta agregada exitosamente']);
        } else {
            return $this->response->setJSON(['error' => 'Error al agregar la respuesta'])->setStatusCode(500);
        }
    }

    /**
     * Obtiene estadísticas de solicitudes
     */
    public function getEstadisticas()
    {
        $estadisticas = $this->solicitudModel->getEstadisticasSolicitudes();
        return $this->response->setJSON($estadisticas);
    }
} 