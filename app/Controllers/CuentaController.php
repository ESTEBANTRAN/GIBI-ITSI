<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Security\InputSanitizerTrait;

class CuentaController extends BaseController
{
    use InputSanitizerTrait;

    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function configuracion()
    {
        if (!session('id')) {
            return redirect()->to('/login');
        }

        $rol_id = session('rol_id');
        
        if ($rol_id == ROLE_ESTUDIANTE) {
            // Estudiante
            return view('cuenta/estudiante');
        } elseif ($rol_id == ROLE_ADMIN_BIENESTAR || $rol_id == ROLE_SUPER_ADMIN) {
            // Administrativo Bienestar o Super Administrador
            return view('cuenta/administrador');
        } else {
            return redirect()->to('/login');
        }
    }

    public function cambiarPassword()
    {
        if (!session('id')) {
            return redirect()->to('/login');
        }

        $rules = [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min_length[6]',
            'password_confirmar' => 'required|matches[password_nuevo]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor corrija los errores en el formulario.');
        }

        $userId = session('id');
        $passwordActual = $this->getPostString('password_actual');
        $passwordNuevo = $this->getPostString('password_nuevo');

        // Obtener usuario actual
        $usuario = $this->usuarioModel->find($userId);

        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }

        // Verificar contraseña actual
        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'La contraseña actual es incorrecta.');
        }

        // Actualizar contraseña
        $data = [
            'password_hash' => password_hash($passwordNuevo, PASSWORD_DEFAULT)
        ];

        if ($this->usuarioModel->update($userId, $data)) {
            return redirect()->back()->with('success', 'Contraseña actualizada exitosamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la contraseña.');
        }
    }

    public function configuracionNotificaciones()
    {
        if (!session('id')) {
            return redirect()->to('/login');
        }

        $userId = session('id');
        
        $data = [
            'notificaciones_email' => $this->getPostString('notificaciones_email') ? 1 : 0,
            'notificaciones_sms' => $this->getPostString('notificaciones_sms') ? 1 : 0,
            'notificaciones_push' => $this->getPostString('notificaciones_push') ? 1 : 0
        ];

        if ($this->usuarioModel->update($userId, $data)) {
            return redirect()->back()->with('success', 'Configuración de notificaciones actualizada.');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar la configuración.');
        }
    }

    public function eliminarCuenta()
    {
        // DESHABILITADO: Los usuarios no pueden eliminar su propia cuenta.
        // Solo el Super Administrador (Global Admin) puede gestionar la eliminación de cuentas.
        return redirect()->back()->with('error', 'No tiene permisos para realizar esta acción. Contacte al administrador del sistema.');
    }

    public function exportarDatos()
    {
        if (!session('id')) {
            return redirect()->to('/login');
        }

        $userId = session('id');
        $usuario = $this->usuarioModel->find($userId);

        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }

        // Crear archivo JSON con datos del usuario
        $datosUsuario = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'cedula' => $usuario['cedula'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'direccion' => $usuario['direccion'],
            'carrera' => $usuario['carrera'],
            'semestre' => $usuario['semestre'],
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ];

        $filename = 'datos_usuario_' . $userId . '_' . date('Y-m-d') . '.json';
        
        return $this->response
            ->setContentType('application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(json_encode($datosUsuario, JSON_PRETTY_PRINT));
    }
} 