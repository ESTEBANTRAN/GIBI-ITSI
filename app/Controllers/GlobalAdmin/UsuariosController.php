<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Models\GlobalAdmin\UsuarioGlobalModel;
use App\Models\GlobalAdmin\RolModel;
use App\Security\InputSanitizerTrait;

class UsuariosController extends BaseController
{
    use InputSanitizerTrait;

    protected $usuarioModel;
    protected $rolModel;
    protected $db;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioGlobalModel();
        $this->rolModel = new RolModel();
        $this->db = \Config\Database::connect();
    }

    public function gestionUsuarios()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 15;
        $search = $this->request->getGet('search') ?? '';
        
        // Si hay búsqueda, siempre ir a página 1 para mostrar todos los resultados
        if (!empty($search) && $page != 1) {
            return redirect()->to(base_url('index.php/global-admin/usuarios?search=' . urlencode($search) . '&page=1'));
        }
        
        $data = $this->usuarioModel->getUsuariosConRolesPaginados($page, $perPage, $search);
        
        // Agregar información adicional para la vista
        $data['search'] = $search;
        $data['has_search'] = !empty($search);
        
        return view('GlobalAdmin/gestion_usuarios', $data);
    }

    public function exportarUsuariosPDF()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        // Obtener todos los usuarios para exportar
        $usuarios = $this->usuarioModel->getTodosLosUsuariosConRoles();
        
        // Configurar zona horaria de Ecuador
        date_default_timezone_set('America/Guayaquil');
        
        try {
            // Verificar si TCPDF está disponible
            if (!class_exists('TCPDF')) {
                // Fallback a HTML
                header('Content-Type: text/html; charset=utf-8');
                echo '<h1>Reporte de Usuarios - ITSI</h1>';
                echo '<p>Fecha: ' . date('d/m/Y H:i:s') . ' (Ecuador)</p>';
                echo '<table border="1"><tr><th>#</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>';
                foreach ($usuarios as $i => $usuario) {
                    echo '<tr><td>' . ($i + 1) . '</td><td>' . $usuario['nombre'] . ' ' . $usuario['apellido'] . '</td><td>' . $usuario['email'] . '</td><td>' . $usuario['nombre_rol'] . '</td></tr>';
                }
                echo '</table>';
                return;
            }
            
            // Crear nueva instancia de TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configurar información del documento
            $pdf->SetCreator('ITSI');
            $pdf->SetAuthor('ITSI');
            $pdf->SetTitle('Reporte de Usuarios');
            
            // Configurar márgenes
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            
            // Configurar saltos de página automáticos
            $pdf->SetAutoPageBreak(TRUE, 25);
            
            // Configurar fuente
            $pdf->SetFont('helvetica', '', 10);
            
            // Agregar página
            $pdf->AddPage();
            
            // Título
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Instituto Tecnologico Superior Ibarra', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 5, 'Reporte de Usuarios del Sistema', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Fecha y Hora: ' . date('d/m/Y H:i:s') . ' (Ecuador)', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Información de contacto
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 5, 'Direccion: Ibarra, Av. Atahualpa 14-148 y Jose M. Leoro', 0, 1, 'L');
            $pdf->Cell(0, 5, 'Telefonos: 0978609734 / 062952535', 0, 1, 'L');
            $pdf->Cell(0, 5, 'Email: itsiibarra@itsi.edu.ec', 0, 1, 'L');
            $pdf->Ln(10);
            
            // Crear tabla manualmente
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(10, 7, '#', 1, 0, 'C');
            $pdf->Cell(60, 7, 'Nombre', 1, 0, 'C');
            $pdf->Cell(60, 7, 'Email', 1, 0, 'C');
            $pdf->Cell(40, 7, 'Rol', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 9);
            $contador = 1;
            foreach ($usuarios as $usuario) {
                $nombre = $usuario['nombre'] . ' ' . $usuario['apellido'];
                $email = $usuario['email'];
                $rol = $usuario['nombre_rol'];
                
                // Verificar si necesitamos nueva página
                if ($pdf->GetY() > 250) {
                    $pdf->AddPage();
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->Cell(10, 7, '#', 1, 0, 'C');
                    $pdf->Cell(60, 7, 'Nombre', 1, 0, 'C');
                    $pdf->Cell(60, 7, 'Email', 1, 0, 'C');
                    $pdf->Cell(40, 7, 'Rol', 1, 1, 'C');
                    $pdf->SetFont('helvetica', '', 9);
                }
                
                $pdf->Cell(10, 6, $contador, 1, 0, 'C');
                $pdf->Cell(60, 6, substr($nombre, 0, 25), 1, 0, 'L');
                $pdf->Cell(60, 6, substr($email, 0, 25), 1, 0, 'L');
                $pdf->Cell(40, 6, substr($rol, 0, 15), 1, 1, 'L');
                $contador++;
            }
            
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 5, 'Total de Usuarios: ' . count($usuarios), 0, 1, 'C');
            $pdf->Cell(0, 5, 'Documento generado automaticamente por el Sistema de Bienestar Estudiantil', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Instituto Tecnologico Superior Ibarra - Todos los derechos reservados', 0, 1, 'C');
            
            // Generar PDF
            $pdf->Output('usuarios_itsi_' . date('Y-m-d_H-i-s') . '.pdf', 'D');
            
        } catch (\Exception $e) {
            // Si hay error, mostrar información de debug
            log_message('error', 'Error generando PDF: ' . $e->getMessage());
            
            // Mostrar error en HTML genérico
            log_message('error', 'Error generando PDF usuarios: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            header('Content-Type: text/html; charset=utf-8');
            echo '<h1>Error generando PDF</h1>';
            echo '<p>Ocurrió un error al generar el reporte. Por favor intente nuevamente.</p>';
        }
    }

    // Métodos AJAX para gestión de usuarios
    public function crearUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $datos = $this->request->getPost();
            
            // Validar datos requeridos
            if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || 
                empty($datos['cedula']) || empty($datos['password']) || empty($datos['rol_id'])) {
                return $this->response->setJSON(['error' => 'Todos los campos marcados con * son obligatorios'])->setStatusCode(400);
            }

            // Verificar si el email ya existe
            if ($this->usuarioModel->where('email', $datos['email'])->first()) {
                return $this->response->setJSON(['error' => 'El email ya está registrado'])->setStatusCode(400);
            }

            // Verificar si la cédula ya existe
            if ($this->usuarioModel->where('cedula', $datos['cedula'])->first()) {
                return $this->response->setJSON(['error' => 'La cédula ya está registrada'])->setStatusCode(400);
            }

            // Hash de la contraseña
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

            // Preparar datos para insertar
            $usuarioData = [
                'rol_id' => $datos['rol_id'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'cedula' => $datos['cedula'],
                'email' => $datos['email'],
                'password_hash' => $password_hash,
                'telefono' => $datos['telefono'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'carrera' => $datos['carrera'] ?? null,
                'semestre' => $datos['semestre'] ?? null
            ];

            // Insertar usuario
            $usuario_id = $this->usuarioModel->insert($usuarioData);

            if ($usuario_id) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario creado exitosamente',
                    'usuario_id' => $usuario_id
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al crear el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function actualizarUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $datos = $this->request->getPost();
            
            // Validar datos requeridos
            if (empty($datos['id']) || empty($datos['nombre']) || empty($datos['apellido']) || 
                empty($datos['email']) || empty($datos['cedula']) || empty($datos['rol_id'])) {
                return $this->response->setJSON(['error' => 'Todos los campos marcados con * son obligatorios'])->setStatusCode(400);
            }

            $usuario_id = $datos['id'];

            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->find($usuario_id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            // Verificar si el email ya existe en otro usuario
            $emailExistente = $this->usuarioModel->where('email', $datos['email'])->where('id !=', $usuario_id)->first();
            if ($emailExistente) {
                return $this->response->setJSON(['error' => 'El email ya está registrado por otro usuario'])->setStatusCode(400);
            }

            // Verificar si la cédula ya existe en otro usuario
            $cedulaExistente = $this->usuarioModel->where('cedula', $datos['cedula'])->where('id !=', $usuario_id)->first();
            if ($cedulaExistente) {
                return $this->response->setJSON(['error' => 'La cédula ya está registrada por otro usuario'])->setStatusCode(400);
            }

            // Preparar datos para actualizar
            $usuarioData = [
                'rol_id' => $datos['rol_id'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'cedula' => $datos['cedula'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'carrera' => $datos['carrera'] ?? null,
                'semestre' => $datos['semestre'] ?? null
            ];

            // Si se proporcionó una nueva contraseña, actualizarla
            if (!empty($datos['password'])) {
                $usuarioData['password_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
            }

            // Actualizar usuario
            $resultado = $this->usuarioModel->update($usuario_id, $usuarioData);

            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario actualizado exitosamente'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al actualizar el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function eliminarUsuario()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $usuario_id = $this->getPostInt('id');
            
            if (empty($usuario_id)) {
                return $this->response->setJSON(['error' => 'ID de usuario requerido'])->setStatusCode(400);
            }

            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->find($usuario_id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            // No permitir eliminar el propio usuario
            if ($usuario_id == session('id')) {
                return $this->response->setJSON(['error' => 'No puedes eliminar tu propia cuenta'])->setStatusCode(400);
            }

            // Eliminar usuario
            $resultado = $this->usuarioModel->delete($usuario_id);

            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true, 
                    'mensaje' => 'Usuario eliminado exitosamente'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Error al eliminar el usuario'])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    public function obtenerUsuario($id)
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $usuario = $this->usuarioModel->getUsuariosConRoles();
            $usuario = array_filter($usuario, function($u) use ($id) {
                return $u['id'] == $id;
            });
            
            if (empty($usuario)) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado'])->setStatusCode(404);
            }

            $usuario = array_values($usuario)[0];
            
            return $this->response->setJSON([
                'success' => true,
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor'])->setStatusCode(500);
        }
    }

    /**
     * @deprecated Endpoint de depuración. No usar en producción.
     * Se mantiene solo para referencia durante desarrollo.
     */
    public function testBusqueda()
    {
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        // Registrar acceso a endpoint de depuración
        log_message('warning', 'Acceso a endpoint de depuración testBusqueda por usuario ID: ' . session('id'));

        $search = $this->request->getGet('search') ?? '';
        
        // Limitar resultados para no exponer datos masivos
        $limit = min((int)$this->request->getGet('limit') ?: 20, 50);
        
        $usuarios = $this->usuarioModel->getTodosLosUsuariosConRoles();
        
        $resultados = [];
        foreach ($usuarios as $usuario) {
            $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
            if (empty($search) || 
                stripos($nombreCompleto, $search) !== false ||
                stripos($usuario['email'], $search) !== false ||
                stripos($usuario['cedula'], $search) !== false ||
                stripos($usuario['nombre_rol'], $search) !== false) {
                $resultados[] = $usuario;
            }
            if (count($resultados) >= $limit) {
                break;
            }
        }
        
        echo "<h1>🔧 Endpoint de Depuración - Test Búsqueda</h1>";
        echo "<p><strong>⚠️ ADVERTENCIA:</strong> Este es un endpoint de depuración. No debe usarse en producción.</p>";
        echo "<p>Término de búsqueda: '" . esc($search) . "'</p>";
        echo "<p>Total usuarios encontrados (limitado a {$limit}): " . count($resultados) . "</p>";
        echo "<h2>Resultados:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($resultados as $usuario) {
            echo "<tr>";
            echo "<td>" . esc($usuario['id']) . "</td>";
            echo "<td>" . esc($usuario['nombre'] . ' ' . $usuario['apellido']) . "</td>";
            echo "<td>" . esc($usuario['email']) . "</td>";
            echo "<td>" . esc($usuario['nombre_rol']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    /**
     * @deprecated Endpoint de depuración. No usar en producción.
     * Se mantiene solo para referencia durante desarrollo.
     */
    public function testBusquedaDetallada()
    {
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        // Registrar acceso a endpoint de depuración
        log_message('warning', 'Acceso a endpoint de depuración testBusquedaDetallada por usuario ID: ' . session('id'));

        $search = $this->request->getGet('search') ?? '';
        $page = max(1, min((int)$this->request->getGet('page') ?: 1, 10)); // Máx 10 páginas
        $perPage = min((int)$this->request->getGet('per_page') ?: 30, 50); // Máx 50 por página
        
        echo "<h1>🔧 Endpoint de Depuración - Test Búsqueda Detallada</h1>";
        echo "<p><strong>⚠️ ADVERTENCIA:</strong> Este es un endpoint de depuración. No debe usarse en producción.</p>";
        echo "<p>Término de búsqueda: '" . esc($search) . "'</p>";
        echo "<p>Página: " . esc((string)$page) . "</p>";
        echo "<p>Usuarios por página: " . esc((string)$perPage) . "</p>";
        
        // Obtener datos con paginación
        $data = $this->usuarioModel->getUsuariosConRolesPaginados($page, $perPage, $search);
        
        echo "<h2>Resultados de la Consulta Paginada:</h2>";
        echo "<p>Total usuarios encontrados: " . esc((string)$data['total']) . "</p>";
        echo "<p>Página actual: " . esc((string)$data['current_page']) . "</p>";
        echo "<p>Total páginas: " . esc((string)$data['total_pages']) . "</p>";
        echo "<p>Usuarios en esta página: " . esc((string)count($data['usuarios'])) . "</p>";
        
        echo "<h3>Usuarios en esta página:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($data['usuarios'] as $usuario) {
            echo "<tr>";
            echo "<td>" . esc($usuario['id']) . "</td>";
            echo "<td>" . esc($usuario['nombre'] . ' ' . $usuario['apellido']) . "</td>";
            echo "<td>" . esc($usuario['email']) . "</td>";
            echo "<td>" . esc($usuario['nombre_rol']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
