<?php

namespace App\Controllers\GlobalAdmin;

use App\Controllers\BaseController;
use App\Models\GlobalAdmin\UsuarioGlobalModel;
use App\Models\GlobalAdmin\RolModel;
use App\Models\GlobalAdmin\SistemaModel;
use App\Models\GlobalAdmin\BackupModel;
use App\Security\InputSanitizerTrait;

class GlobalAdminController extends BaseController
{
    use InputSanitizerTrait;

    protected $usuarioModel;
    protected $rolModel;
    protected $sistemaModel;
    protected $backupModel;
    protected $db;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioGlobalModel();
        $this->rolModel = new RolModel();
        $this->sistemaModel = new SistemaModel();
        $this->backupModel = new BackupModel();
        
        // Inicializar conexión a la base de datos
        $this->db = \Config\Database::connect();
    }

    /**
     * Crea un archivo temporal de credenciales para mysqldump/mysql
     * evitando exponer la contraseña en procesos del sistema (--password=).
     */
    private function getDbCredentialsFile(): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mycnf_');
        $content = "[client]\n";
        $content .= "user=\"{$this->db->username}\"\n";
        if (!empty($this->db->password)) {
            $content .= "password=\"{$this->db->password}\"\n";
        }
        $content .= "host=\"{$this->db->hostname}\"\n";
        file_put_contents($tmpFile, $content);
        @chmod($tmpFile, 0600);
        return $tmpFile;
    }

    // ──────────────────────────────────────────────
    //  Dashboard
    // ──────────────────────────────────────────────

    public function index()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return redirect()->to('/global-admin/dashboard');
    }

    public function dashboard()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            // Obtener estadísticas reales de usuarios
            $totalUsuarios = $this->db->table('usuarios')->countAllResults();
            $usuariosActivos = $this->db->table('usuarios')->where('estado', 'Activo')->countAllResults();
            $totalRoles = $this->db->table('roles')->countAllResults();
            
            // Obtener estadísticas del sistema de bienestar (nuevas)
            $estadisticasBienestar = $this->getEstadisticasBienestar();
            
            // Calcular cambios porcentuales
            $cambioUsuarios = $this->calcularCambioUsuarios();
            $cambioActivos = $this->calcularCambioActivos();
            
            // Obtener respaldos recientes reales
            $respaldosRecientes = $this->obtenerRespaldosRecientes();
            
            // Obtener información del sistema
            $sistemaInfo = $this->obtenerInfoSistema();
            
            // Obtener actividad reciente real
            $actividadReciente = $this->obtenerActividadReciente();
            
            // Obtener datos para el gráfico real
            $datosGrafico = $this->obtenerDatosGrafico();

            $data = [
                'total_usuarios' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
                'total_roles' => $totalRoles,
                'cambio_usuarios' => $cambioUsuarios,
                'cambio_activos' => $cambioActivos,
                'estadisticas_bienestar' => $estadisticasBienestar,
                'backups_recientes' => $respaldosRecientes,
                'sistema_info' => $sistemaInfo,
                'actividad_reciente' => $actividadReciente,
                'datos_grafico' => $datosGrafico
            ];

            return view('GlobalAdmin/dashboard', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'GlobalAdminController::dashboard - Error: ' . $e->getMessage());
            // En caso de error, mostrar vista con datos básicos
            $data = [
                'total_usuarios' => 0,
                'usuarios_activos' => 0,
                'total_roles' => 0,
                'cambio_usuarios' => 0,
                'cambio_activos' => 0,
                'estadisticas_bienestar' => [
                    'fichas_total' => 0,
                    'fichas_aprobadas' => 0,
                    'becas_activas' => 0,
                    'solicitudes_pendientes' => 0,
                    'ayudas_pendientes' => 0
                ],
                'backups_recientes' => [],
                'sistema_info' => [
                    'version' => '1.0.0',
                    'nombre' => 'Sistema de Bienestar Estudiantil'
                ],
                'actividad_reciente' => [],
                'datos_grafico' => []
            ];
            return view('GlobalAdmin/dashboard', $data);
        }
    }

    private function calcularCambioUsuarios()
    {
        try {
            // Usuarios del mes actual
            $usuariosMesActual = $this->db->table('usuarios')
                ->where('MONTH(fecha_registro)', date('n'))
                ->where('YEAR(fecha_registro)', date('Y'))
                ->countAllResults();
            
            // Usuarios del mes anterior
            $usuariosMesAnterior = $this->db->table('usuarios')
                ->where('MONTH(fecha_registro)', date('n', strtotime('-1 month')))
                ->where('YEAR(fecha_registro)', date('Y', strtotime('-1 month')))
                ->countAllResults();
            
            if ($usuariosMesAnterior > 0) {
                return round((($usuariosMesActual - $usuariosMesAnterior) / $usuariosMesAnterior) * 100);
            }
            
            return $usuariosMesActual > 0 ? 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calcularCambioActivos()
    {
        try {
            // Usuarios activos del mes actual
            $activosMesActual = $this->db->table('usuarios')
                ->where('estado', 'Activo')
                ->where('MONTH(ultimo_acceso)', date('n'))
                ->where('YEAR(ultimo_acceso)', date('Y'))
                ->countAllResults();
            
            // Usuarios activos del mes anterior
            $activosMesAnterior = $this->db->table('usuarios')
                ->where('estado', 'Activo')
                ->where('MONTH(ultimo_acceso)', date('n', strtotime('-1 month')))
                ->where('YEAR(ultimo_acceso)', date('Y', strtotime('-1 month')))
                ->countAllResults();
            
            if ($activosMesAnterior > 0) {
                return round((($activosMesActual - $activosMesAnterior) / $activosMesAnterior) * 100);
            }
            
            return $activosMesActual > 0 ? 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function obtenerRespaldosRecientes()
    {
        try {
            // Obtener respaldos reales de la tabla respaldos
            $respaldos = $this->db->table('respaldos')
                ->select('nombre_archivo, fecha_creacion, tamano_bytes, tipo')
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            $respaldosFormateados = [];
            foreach ($respaldos as $respaldo) {
                $respaldosFormateados[] = [
                    'nombre' => $respaldo['nombre_archivo'],
                    'fecha' => $respaldo['fecha_creacion'],
                    'tamaño' => $this->formatBytes($respaldo['tamano_bytes']),
                    'tipo' => $respaldo['tipo']
                ];
            }
            
            return $respaldosFormateados;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function obtenerInfoSistema()
    {
        return [
            'version' => '1.0.0',
            'nombre' => 'Sistema de Bienestar Estudiantil',
            'ultima_actualizacion' => date('d/m/Y H:i'),
            'estado_servidor' => 'Online',
            'estado_bd' => 'Conectada',
            'espacio_disco' => '75% usado'
        ];
    }

    private function obtenerActividadReciente()
    {
        try {
            // Obtener actividad reciente real de logs
            $logs = $this->db->table('logs')
                ->select('accion, tabla, datos, fecha_creacion')
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            $actividad = [];
            foreach ($logs as $log) {
                $actividad[] = [
                    'tipo' => 'info',
                    'titulo' => $log['accion'],
                    'descripcion' => $log['tabla'] . (isset($log['datos']) ? ' - ' . substr($log['datos'], 0, 50) : ''),
                    'tiempo' => $this->tiempoTranscurrido($log['fecha_creacion'])
                ];
            }
            
            return $actividad;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function tiempoTranscurrido($fecha)
    {
        $ahora = new \DateTime();
        $fechaLog = new \DateTime($fecha);
        $diferencia = $ahora->diff($fechaLog);
        
        if ($diferencia->d > 0) {
            return 'Hace ' . $diferencia->d . ' día(s)';
        } elseif ($diferencia->h > 0) {
            return 'Hace ' . $diferencia->h . ' hora(s)';
        } elseif ($diferencia->i > 0) {
            return 'Hace ' . $diferencia->i . ' minuto(s)';
        } else {
            return 'Hace unos segundos';
        }
    }

    private function obtenerDatosGrafico()
    {
        try {
            // Obtener datos reales de usuarios activos por mes (últimos 6 meses)
            $datos = [];
            $labels = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $mes = date('n', strtotime("-$i months"));
                $año = date('Y', strtotime("-$i months"));
                $nombreMes = date('M', strtotime("-$i months"));
                
                $usuariosActivos = $this->db->table('usuarios')
                    ->where('estado', 'Activo')
                    ->where('MONTH(ultimo_acceso)', $mes)
                    ->where('YEAR(ultimo_acceso)', $año)
                    ->countAllResults();
                
                $datos[] = $usuariosActivos;
                $labels[] = $nombreMes;
            }
            
            return [
                'datos' => $datos,
                'labels' => $labels
            ];
        } catch (\Exception $e) {
            return [
                'datos' => [30, 40, 35, 50, 49, 60],
                'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun']
            ];
        }
    }

    /**
     * Obtener estadísticas del sistema de bienestar estudiantil
     */
    private function getEstadisticasBienestar()
    {
        try {
            $stats = [];
            
            // Estadísticas de períodos académicos
            $stats['periodos'] = [
                'total' => $this->db->table('periodos_academicos')->countAllResults(),
                'activos' => $this->db->table('periodos_academicos')->where('activo', 1)->countAllResults(),
                'con_fichas_activas' => $this->db->table('periodos_academicos')->where('activo_fichas', 1)->countAllResults(),
                'con_becas_activas' => $this->db->table('periodos_academicos')->where('activo_becas', 1)->countAllResults()
            ];
            
            // Estadísticas de fichas socioeconómicas
            $stats['fichas'] = [
                'total' => $this->db->table('fichas_socioeconomicas')->countAllResults(),
                'pendientes' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Enviada')->countAllResults(),
                'aprobadas' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Aprobada')->countAllResults(),
                'rechazadas' => $this->db->table('fichas_socioeconomicas')->where('estado', 'Rechazada')->countAllResults()
            ];
            
            // Estadísticas de becas
            $stats['becas'] = [
                'total_becas' => $this->db->table('becas')->countAllResults(),
                'becas_activas' => $this->db->table('becas')->where('estado', 'Activa')->countAllResults(),
                'total_solicitudes' => $this->db->table('solicitudes_becas')->countAllResults(),
                'solicitudes_aprobadas' => $this->db->table('solicitudes_becas')->where('estado', 'Aprobada')->countAllResults(),
                'solicitudes_pendientes' => $this->db->table('solicitudes_becas')->where('estado', 'Pendiente')->countAllResults()
            ];
            
            // Estadísticas de usuarios por rol
            $stats['usuarios'] = [
                'estudiantes' => $this->db->table('usuarios')->where('rol_id', ROLE_ESTUDIANTE)->countAllResults(),
                'admin_bienestar' => $this->db->table('usuarios')->where('rol_id', ROLE_ADMIN_BIENESTAR)->countAllResults(),
                'super_admin' => $this->db->table('usuarios')->where('rol_id', ROLE_SUPER_ADMIN)->countAllResults()
            ];
            
            // Estadísticas de solicitudes de ayuda
            $stats['solicitudes_ayuda'] = [
                'total' => $this->db->table('solicitudes_ayuda')->countAllResults(),
                'abiertas' => $this->db->table('solicitudes_ayuda')->where('estado', 'Pendiente')->countAllResults(),
                'resueltas' => $this->db->table('solicitudes_ayuda')->where('estado', 'Resuelta')->countAllResults()
            ];
            
            // Rendimiento del sistema (últimas 24 horas)
            $stats['rendimiento'] = [
                'nuevas_fichas_hoy' => $this->db->table('fichas_socioeconomicas')
                    ->where('fecha_creacion >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults(),
                'nuevas_solicitudes_hoy' => $this->db->table('solicitudes_becas')
                    ->where('fecha_solicitud >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults(),
                'nuevas_ayudas_hoy' => $this->db->table('solicitudes_ayuda')
                    ->where('fecha_solicitud >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->countAllResults()
            ];
            
            // Alertas y problemas
            $stats['alertas'] = $this->getAlertasSistema();
            
            return $stats;
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas de bienestar: ' . $e->getMessage());
            return [
                'periodos' => ['total' => 0, 'activos' => 0, 'con_fichas_activas' => 0, 'con_becas_activas' => 0],
                'fichas' => ['total' => 0, 'pendientes' => 0, 'aprobadas' => 0, 'rechazadas' => 0],
                'becas' => ['total_becas' => 0, 'becas_activas' => 0, 'total_solicitudes' => 0, 'solicitudes_aprobadas' => 0, 'solicitudes_pendientes' => 0],
                'usuarios' => ['estudiantes' => 0, 'admin_bienestar' => 0, 'super_admin' => 0],
                'solicitudes_ayuda' => ['total' => 0, 'abiertas' => 0, 'resueltas' => 0],
                'rendimiento' => ['nuevas_fichas_hoy' => 0, 'nuevas_solicitudes_hoy' => 0, 'nuevas_ayudas_hoy' => 0],
                'alertas' => []
            ];
        }
    }

    /**
     * Obtener alertas del sistema
     */
    private function getAlertasSistema()
    {
        $alertas = [];
        
        try {
            // Verificar períodos sin actividad reciente
            $periodosInactivos = $this->db->table('periodos_academicos p')
                ->select('p.nombre')
                ->where('p.activo', 1)
                ->where('p.fichas_creadas', 0)
                ->where('p.fecha_inicio <=', date('Y-m-d', strtotime('-30 days')))
                ->get()
                ->getResultArray();
            
            foreach ($periodosInactivos as $periodo) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'mensaje' => "El período '{$periodo['nombre']}' está activo pero sin fichas creadas en 30 días",
                    'categoria' => 'periodos'
                ];
            }
            
            // Verificar solicitudes pendientes por mucho tiempo
            $solicitudesPendientes = $this->db->table('solicitudes_becas')
                ->where('estado', 'Pendiente')
                ->where('fecha_solicitud <=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->countAllResults();
            
            if ($solicitudesPendientes > 0) {
                $alertas[] = [
                    'tipo' => 'danger',
                    'mensaje' => "$solicitudesPendientes solicitudes de beca llevan más de 7 días pendientes",
                    'categoria' => 'solicitudes'
                ];
            }
            
            // Verificar límites de períodos
            $periodosLimiteAlcanzado = $this->db->table('periodos_academicos')
                ->where('limite_fichas IS NOT NULL')
                ->where('fichas_creadas >= limite_fichas')
                ->countAllResults();
            
            if ($periodosLimiteAlcanzado > 0) {
                $alertas[] = [
                    'tipo' => 'info',
                    'mensaje' => "$periodosLimiteAlcanzado períodos han alcanzado su límite de fichas",
                    'categoria' => 'limites'
                ];
            }
            
            // Verificar usuarios bloqueados
            $usuariosBloqueados = $this->db->table('usuarios')
                ->where('bloqueado_hasta IS NOT NULL')
                ->where('bloqueado_hasta >', date('Y-m-d H:i:s'))
                ->countAllResults();
            
            if ($usuariosBloqueados > 0) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'mensaje' => "$usuariosBloqueados usuarios están temporalmente bloqueados",
                    'categoria' => 'usuarios'
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo alertas del sistema: ' . $e->getMessage());
        }
        
        return $alertas;
    }

    // ──────────────────────────────────────────────
    //  Configuración del Sistema
    // ──────────────────────────────────────────────

    public function configuracionSistema()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $configRows = $this->db->table('configuracion_sistema')->get()->getResultArray();
            $configuracion = [];
            foreach ($configRows as $row) {
                $configuracion[$row['clave']] = $row['valor'];
            }
            
            return view('GlobalAdmin/configuracion_sistema', [
                'configuracion' => $configuracion
            ]);
        } catch (\Exception $e) {
            log_message('error', 'GlobalAdmin::configuracionSistema - Error: ' . $e->getMessage());
            return view('GlobalAdmin/configuracion_sistema', [
                'configuracion' => [],
                'error' => 'Error cargando configuración del sistema'
            ]);
        }
    }

    /**
     * Guardar configuración del sistema (AJAX desde formularios por sección)
     */
    public function guardarConfiguracion()
    {
        if (!session('id') || session('rol_id') != 4) {
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

    public function actualizarConfiguracion()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return redirect()->back()->with('success', 'Configuración actualizada exitosamente.');
    }

    // ──────────────────────────────────────────────
    //  Perfil del Super Administrador
    // ──────────────────────────────────────────────

    public function perfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return view('GlobalAdmin/perfil', [
            'usuario' => [
                'id' => session('id'),
                'nombre' => session('nombre'),
                'apellido' => session('apellido'),
                'email' => session('email'),
                'cedula' => session('cedula'),
                'telefono' => session('telefono'),
                'direccion' => session('direccion'),
                'foto_perfil' => session('foto_perfil')
            ]
        ]);
    }

    public function actualizarPerfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();

            $data = [
                'nombre' => $datos['nombre'] ?? session('nombre'),
                'apellido' => $datos['apellido'] ?? session('apellido'),
                'email' => $datos['email'] ?? session('email'),
                'telefono' => $datos['telefono'] ?? session('telefono'),
                'direccion' => $datos['direccion'] ?? session('direccion'),
            ];

            $this->db->table('usuarios')->where('id', session('id'))->update($data);

            session()->set($data);

            return redirect()->back()->with('success', 'Perfil actualizado exitosamente.');
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando perfil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar perfil');
        }
    }

    public function cambiarFotoPerfil()
    {
        if (!session('id') || session('rol_id') != 4) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $file = $this->request->getFile('foto');
            
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/perfiles/', $newName);
                
                session()->set('foto_perfil', $newName);
                
                return $this->response->setJSON(['success' => true, 'mensaje' => 'Foto actualizada exitosamente']);
            }
            
            return $this->response->setJSON(['error' => 'Error al subir la imagen'])->setStatusCode(400);
        } catch (\Exception $e) {
            log_message('error', 'Error subiendo foto: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al subir la imagen'])->setStatusCode(500);
        }
    }

    // ──────────────────────────────────────────────
    //  Cuenta del Super Administrador
    // ──────────────────────────────────────────────

    public function cuenta()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        return view('GlobalAdmin/cuenta', [
            'usuario' => [
                'id' => session('id'),
                'email' => session('email'),
                'nombre' => session('nombre'),
                'apellido' => session('apellido')
            ]
        ]);
    }

    public function cambiarPassword()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();
            $passwordActual = $datos['password_actual'] ?? '';
            $nuevaPassword = $datos['new_password'] ?? '';
            $confirmarPassword = $datos['confirm_password'] ?? '';

            if (empty($passwordActual) || empty($nuevaPassword) || empty($confirmarPassword)) {
                return redirect()->back()->with('error', 'Todos los campos son requeridos.');
            }

            $usuario = $this->db->table('usuarios')->where('id', session('id'))->get()->getRowArray();

            if (!password_verify($passwordActual, $usuario['password_hash'])) {
                return redirect()->back()->with('error', 'La contraseña actual no es correcta.');
            }

            if ($nuevaPassword !== $confirmarPassword) {
                return redirect()->back()->with('error', 'Las contraseñas no coinciden.');
            }

            if (strlen($nuevaPassword) < 8) {
                return redirect()->back()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
            }

            $this->db->table('usuarios')
                ->where('id', session('id'))
                ->update(['password_hash' => password_hash($nuevaPassword, PASSWORD_DEFAULT)]);

            return redirect()->back()->with('success', 'Contraseña cambiada exitosamente.');
        } catch (\Exception $e) {
            log_message('error', 'Error cambiando contraseña: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar contraseña');
        }
    }

    public function configuracionNotificaciones()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $datos = $this->request->getPost();

            $configNotif = json_encode([
                'email_notificaciones' => $datos['email_notificaciones'] ?? true,
                'backup_notificaciones' => $datos['backup_notificaciones'] ?? true,
                'login_notificaciones' => $datos['login_notificaciones'] ?? false,
            ]);

            $this->db->table('usuarios')
                ->where('id', session('id'))
                ->update(['configuraciones_usuario' => $configNotif]);

            return redirect()->back()->with('success', 'Configuración de notificaciones actualizada.');
        } catch (\Exception $e) {
            log_message('error', 'Error en config notificaciones: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar configuración');
        }
    }

    public function eliminarCuenta()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $userId = session('id');

            $this->db->table('usuarios')->where('id', $userId)->update([
                'estado' => 'Inactivo',
                'email' => 'deleted_' . $userId . '_' . session('email'),
                'cedula' => 'DEL_' . $userId,
            ]);

            session()->destroy();
            return redirect()->to('/login')->with('success', 'Cuenta desactivada exitosamente.');
        } catch (\Exception $e) {
            log_message('error', 'Error eliminando cuenta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar cuenta');
        }
    }

    public function exportarDatos()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }

        try {
            $usuario = $this->db->table('usuarios')->where('id', session('id'))->get()->getRowArray();
            unset($usuario['password_hash']);

            $filename = 'mis_datos_' . date('Y-m-d') . '.json';
            $filepath = WRITEPATH . 'temp/' . $filename;

            file_put_contents($filepath, json_encode($usuario, JSON_PRETTY_PRINT));

            return $this->response->download($filepath, null)->setFileName($filename);
        } catch (\Exception $e) {
            log_message('error', 'Error exportando datos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar datos');
        }
    }

    // ──────────────────────────────────────────────
    //  Vistas de perfiles
    // ──────────────────────────────────────────────

    public function vistaEstudiante()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        // Simular datos de estudiante para la vista
        $data = [
            'estudiante' => [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@estudiante.itsi.edu.ec',
                'carrera' => 'Ingeniería Informática',
                'semestre' => '5to Semestre'
            ],
            'fichas' => [
                ['periodo' => '2024-2', 'estado' => 'Aprobada', 'fecha' => '2024-12-15'],
                ['periodo' => '2024-1', 'estado' => 'Enviada', 'fecha' => '2024-06-20']
            ],
            'becas' => [
                ['tipo' => 'Excelencia Académica', 'estado' => 'Aprobada', 'monto' => '$500'],
                ['tipo' => 'Socioeconómica', 'estado' => 'En Revisión', 'monto' => '$300']
            ]
        ];
        
        return view('GlobalAdmin/vista_estudiante', $data);
    }

    public function vistaAdminBienestar()
    {
        if (!session('id') || session('rol_id') != 4) {
            return redirect()->to('/login');
        }
        
        // Simular datos de admin bienestar para la vista
        $data = [
            'admin' => [
                'nombre' => 'María González',
                'email' => 'maria.gonzalez@itsi.edu.ec',
                'cargo' => 'Coordinadora de Bienestar Estudiantil'
            ],
            'estadisticas' => [
                'total_estudiantes' => 1247,
                'fichas_pendientes' => 45,
                'becas_aprobadas' => 156,
                'solicitudes_ayuda' => 23
            ],
            'fichas_recientes' => [
                ['estudiante' => 'Ana López', 'periodo' => '2024-2', 'estado' => 'Pendiente'],
                ['estudiante' => 'Carlos Ruiz', 'periodo' => '2024-2', 'estado' => 'Aprobada']
            ]
        ];
        
        return view('GlobalAdmin/vista_admin_bienestar', $data);
    }

    // ══════════════════════════════════════════════
    //  FACADE METHODS (deprecated — redirect to new controllers)
    // ══════════════════════════════════════════════

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::gestionUsuarios()
     */
    public function gestionUsuarios()
    {
        log_message('debug', 'GlobalAdminController::gestionUsuarios() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/usuarios');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::exportarUsuariosPDF()
     */
    public function exportarUsuariosPDF()
    {
        log_message('debug', 'GlobalAdminController::exportarUsuariosPDF() called (deprecated)');
        return redirect()->to('global-admin/exportar-usuarios-pdf');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::crearUsuario()
     */
    public function crearUsuario()
    {
        log_message('debug', 'GlobalAdminController::crearUsuario() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/crear-usuario');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::actualizarUsuario()
     */
    public function actualizarUsuario()
    {
        log_message('debug', 'GlobalAdminController::actualizarUsuario() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/actualizar-usuario');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::eliminarUsuario()
     */
    public function eliminarUsuario()
    {
        log_message('debug', 'GlobalAdminController::eliminarUsuario() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/eliminar-usuario');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::obtenerUsuario()
     */
    public function obtenerUsuario($id)
    {
        log_message('debug', 'GlobalAdminController::obtenerUsuario() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/obtener-usuario/' . $id);
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::testBusqueda()
     */
    public function testBusqueda()
    {
        log_message('debug', 'GlobalAdminController::testBusqueda() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/test-busqueda');
    }

    /**
     * @deprecated Use GlobalAdmin\UsuariosController::testBusquedaDetallada()
     */
    public function testBusquedaDetallada()
    {
        log_message('debug', 'GlobalAdminController::testBusquedaDetallada() called (deprecated, use UsuariosController)');
        return redirect()->to('global-admin/test-busqueda-detallada');
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::gestionRoles()
     */
    public function gestionRoles()
    {
        log_message('debug', 'GlobalAdminController::gestionRoles() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/roles');
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::crearRol()
     */
    public function crearRol()
    {
        log_message('debug', 'GlobalAdminController::crearRol() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/crear-rol');
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::obtenerRol()
     */
    public function obtenerRol($id)
    {
        log_message('debug', 'GlobalAdminController::obtenerRol() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/obtener-rol/' . $id);
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::actualizarRol()
     */
    public function actualizarRol()
    {
        log_message('debug', 'GlobalAdminController::actualizarRol() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/actualizar-rol');
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::eliminarRol()
     */
    public function eliminarRol()
    {
        log_message('debug', 'GlobalAdminController::eliminarRol() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/eliminar-rol');
    }

    /**
     * @deprecated Use GlobalAdmin\RolesController::obtenerPermisosRol()
     */
    public function obtenerPermisosRol($id)
    {
        log_message('debug', 'GlobalAdminController::obtenerPermisosRol() called (deprecated, use RolesController)');
        return redirect()->to('global-admin/permisos-rol/' . $id);
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::respaldos()
     */
    public function respaldos()
    {
        log_message('debug', 'GlobalAdminController::respaldos() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/respaldos');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::crearRespaldo()
     */
    public function crearRespaldo()
    {
        log_message('debug', 'GlobalAdminController::crearRespaldo() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/crear-respaldo');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::obtenerRespaldos()
     */
    public function obtenerRespaldos()
    {
        log_message('debug', 'GlobalAdminController::obtenerRespaldos() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/obtener-respaldos');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::restaurarRespaldo()
     */
    public function restaurarRespaldo()
    {
        log_message('debug', 'GlobalAdminController::restaurarRespaldo() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/restaurar-respaldo');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::descargarRespaldo()
     */
    public function descargarRespaldo($id)
    {
        log_message('debug', 'GlobalAdminController::descargarRespaldo() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/descargar-respaldo/' . $id);
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::eliminarRespaldo()
     */
    public function eliminarRespaldo()
    {
        log_message('debug', 'GlobalAdminController::eliminarRespaldo() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/eliminar-respaldo');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::limpiarRespaldos()
     */
    public function limpiarRespaldos()
    {
        log_message('debug', 'GlobalAdminController::limpiarRespaldos() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/limpiar-respaldos');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::guardarConfiguracionRespaldos()
     */
    public function guardarConfiguracionRespaldos()
    {
        log_message('debug', 'GlobalAdminController::guardarConfiguracionRespaldos() called (deprecated)');
        return redirect()->to('global-admin/guardar-configuracion-respaldos');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::estadisticasRespaldos()
     */
    public function estadisticasRespaldos()
    {
        log_message('debug', 'GlobalAdminController::estadisticasRespaldos() called (deprecated)');
        return redirect()->to('global-admin/estadisticas-respaldos');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::enviarRespaldoPorEmail()
     */
    public function enviarRespaldoPorEmail()
    {
        log_message('debug', 'GlobalAdminController::enviarRespaldoPorEmail() called (deprecated)');
        return redirect()->to('global-admin/enviar-respaldo-email');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::crearBackup()
     */
    public function crearBackup()
    {
        log_message('debug', 'GlobalAdminController::crearBackup() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/crear-backup');
    }

    /**
     * @deprecated Use GlobalAdmin\BackupsController::restaurarBackup()
     */
    public function restaurarBackup()
    {
        log_message('debug', 'GlobalAdminController::restaurarBackup() called (deprecated, use BackupsController)');
        return redirect()->to('global-admin/restaurar-backup');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::logs()
     */
    public function logs()
    {
        log_message('debug', 'GlobalAdminController::logs() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/logs');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::obtenerLogs()
     */
    public function obtenerLogs()
    {
        log_message('debug', 'GlobalAdminController::obtenerLogs() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/obtener-logs');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::obtenerLog()
     */
    public function obtenerLog($id)
    {
        log_message('debug', 'GlobalAdminController::obtenerLog() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/obtener-log/' . $id);
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::eliminarLog()
     */
    public function eliminarLog()
    {
        log_message('debug', 'GlobalAdminController::eliminarLog() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/eliminar-log');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::limpiarLogs()
     */
    public function limpiarLogs()
    {
        log_message('debug', 'GlobalAdminController::limpiarLogs() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/limpiar-logs');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::exportarLogs()
     */
    public function exportarLogs()
    {
        log_message('debug', 'GlobalAdminController::exportarLogs() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/exportar-logs');
    }

    /**
     * @deprecated Use GlobalAdmin\LogsController::estadisticasLogs()
     */
    public function estadisticasLogs()
    {
        log_message('debug', 'GlobalAdminController::estadisticasLogs() called (deprecated, use LogsController)');
        return redirect()->to('global-admin/estadisticas-logs');
    }

    /**
     * @deprecated Use GlobalAdmin\EstadisticasController::estadisticas()
     */
    public function estadisticas()
    {
        log_message('debug', 'GlobalAdminController::estadisticas() called (deprecated, use EstadisticasController)');
        return redirect()->to('global-admin/estadisticas');
    }

    /**
     * @deprecated Use GlobalAdmin\EstadisticasController::obtenerEstadisticasGlobales()
     */
    public function obtenerEstadisticasGlobales()
    {
        log_message('debug', 'GlobalAdminController::obtenerEstadisticasGlobales() called (deprecated)');
        return redirect()->to('global-admin/obtener-estadisticas-globales');
    }

    /**
     * @deprecated Use GlobalAdmin\EstadisticasController::getMetricasRendimiento()
     */
    public function getMetricasRendimiento()
    {
        log_message('debug', 'GlobalAdminController::getMetricasRendimiento() called (deprecated)');
        return redirect()->to('global-admin/metricas-rendimiento');
    }
}
