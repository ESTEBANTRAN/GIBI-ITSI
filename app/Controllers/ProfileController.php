<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/** @deprecated Usar PerfilController::cambiarFoto() en su lugar. */
class ProfileController extends BaseController
{
    protected $request;
    protected $helpers = ['form'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function cambiarFotoPerfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo se permiten peticiones AJAX']);
        }

        $file = $this->request->getFile('foto_perfil');
        
        if (!$file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            return $this->response->setJSON(['error' => 'Error al subir el archivo']);
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['error' => 'Solo se permiten archivos JPG, PNG o GIF']);
        }

        // Validar tamaño (1MB = 1048576 bytes)
        if ($file->getSize() > 1048576) {
            return $this->response->setJSON(['error' => 'El archivo no puede pesar más de 1MB']);
        }

        // Generar nombre único para el archivo
        $extension = $file->getExtension();
        $newName = 'user_' . session('id') . '_' . time() . '.' . $extension;
        
        // Ruta donde se guardará
        $uploadPath = FCPATH . 'sistema/assets/images/profile/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Eliminar foto anterior si existe
        $oldFoto = session('foto_perfil');
        if ($oldFoto && file_exists($uploadPath . $oldFoto)) {
            unlink($uploadPath . $oldFoto);
        }

        // Mover el archivo
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON(['error' => 'Error al guardar el archivo']);
        }

        // Actualizar en la base de datos
        $db = \Config\Database::connect();
        $updated = $db->table('usuarios')
                     ->where('id', session('id'))
                     ->update(['foto_perfil' => $newName]);

        if (!$updated) {
            // Si falla la actualización, eliminar el archivo
            unlink($uploadPath . $newName);
            return $this->response->setJSON(['error' => 'Error al actualizar la base de datos']);
        }

        // Actualizar la sesión
        session()->set('foto_perfil', $newName);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Foto de perfil actualizada correctamente',
            'foto_url' => base_url('sistema/assets/images/profile/' . $newName)
        ]);
    }
} 