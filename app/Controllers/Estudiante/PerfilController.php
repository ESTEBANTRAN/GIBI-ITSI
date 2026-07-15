<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Security\InputSanitizerTrait;

class PerfilController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->db = \Config\Database::connect();
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

        $fichaModel = new \App\Models\FichaSocioeconomicaModel();
        $solicitudBecaModel = new \App\Models\SolicitudBecaModel();
        $solicitudModel = new \App\Models\SolicitudAyudaModel();

        $usuario = $this->usuarioModel->find(session('id'));
        $fichas = $fichaModel->where('estudiante_id', session('id'))->findAll();
        $solicitudesBecas = $solicitudBecaModel->getSolicitudesEstudiante(session('id'));
        $solicitudesAyuda = $solicitudModel->where('id_estudiante', session('id'))->findAll();

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
        // DESHABILITADO: Los estudiantes no pueden eliminar su propia cuenta.
        // Solo el Super Administrador (Global Admin) puede gestionar la eliminación de cuentas.
        return $this->response->setJSON([
            'success' => false, 
            'error' => 'No tiene permisos para realizar esta acción. Contacte al administrador del sistema.'
        ]);
    }
}
