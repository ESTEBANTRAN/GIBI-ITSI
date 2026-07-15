<?php

namespace App\Services;

use App\Models\FichaSocioeconomicaModel;
use App\Models\PeriodoAcademicoModel;
use App\Models\BecaModel;
use App\Models\UsuarioModel;
use App\Models\SolicitudBecaModel;
use CodeIgniter\Database\BaseConnection;

class AdminBienestarService
{
    protected $fichaModel;
    protected $periodoModel;
    protected $becaModel;
    protected $usuarioModel;
    protected $solicitudBecaModel;
    protected $db;

    public function __construct()
    {
        $this->fichaModel = new FichaSocioeconomicaModel();
        $this->periodoModel = new PeriodoAcademicoModel();
        $this->becaModel = new BecaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->solicitudBecaModel = new SolicitudBecaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Obtener estadísticas completas del sistema
     */
    public function getEstadisticasCompletas()
    {
        $estadisticas = [
            'fichas' => $this->getEstadisticasFichas(),
            'becas' => $this->getEstadisticasBecas(),
            'periodos' => $this->getEstadisticasPeriodos(),
            'usuarios' => $this->getEstadisticasUsuarios(),
            'solicitudes' => $this->getEstadisticasSolicitudes(),
            'actividad_reciente' => $this->getActividadReciente(),
            'alertas' => $this->getAlertas()
        ];

        return $estadisticas;
    }

    /**
     * Obtener actividad reciente real de los logs del sistema
     */
    public function getActividadReciente()
    {
        $logs = $this->db->table('logs l')
            ->select('l.accion, l.fecha_creacion as fecha, u.nombre, u.apellido, l.tabla')
            ->join('usuarios u', 'u.id = l.id_usuario', 'left')
            ->orderBy('l.fecha_creacion', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $actividades = [];
        foreach ($logs as $log) {
            $accionFormat = ucfirst(str_replace('_', ' ', $log['accion']));
            $usuarioFormat = ($log['nombre'] && $log['apellido']) ? $log['nombre'] . ' ' . $log['apellido'] : 'Sistema';
            
            // Determinar el color según la tabla o acción
            $estado = 'info';
            if (strpos($log['accion'], 'rechaz') !== false || strpos($log['accion'], 'eliminar') !== false) {
                $estado = 'danger';
            } elseif (strpos($log['accion'], 'aprobar') !== false || strpos($log['accion'], 'crear') !== false) {
                $estado = 'success';
            } elseif (strpos($log['accion'], 'actualizar') !== false) {
                $estado = 'warning';
            }

            $actividades[] = [
                'accion' => $accionFormat,
                'usuario' => $usuarioFormat,
                'fecha' => date('d/m/Y H:i', strtotime($log['fecha'])),
                'estado' => $estado
            ];
        }

        return $actividades;
    }

    /**
     * Obtener alertas reales del sistema
     */
    public function getAlertas()
    {
        $alertas = [];

        // Todas las alerts en una sola consulta (3 COUNT → 1 UNION ALL)
        $conteos = $this->db->query("
            SELECT 'fichas' as tipo, COUNT(*) as total FROM fichas_socioeconomicas WHERE estado = 'Enviada'
            UNION ALL
            SELECT 'becas', COUNT(*) FROM solicitudes_becas WHERE estado = 'Pendiente'
            UNION ALL
            SELECT 'ayudas', COUNT(*) FROM solicitudes_ayuda WHERE estado = 'Pendiente'
        ")->getResultArray();

        $fichasPendientes = 0;
        $solicitudesPendientes = 0;
        $ayudasPendientes = 0;

        foreach ($conteos as $row) {
            switch ($row['tipo']) {
                case 'fichas': $fichasPendientes = (int)$row['total']; break;
                case 'becas': $solicitudesPendientes = (int)$row['total']; break;
                case 'ayudas': $ayudasPendientes = (int)$row['total']; break;
            }
        }

        if ($fichasPendientes > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => $fichasPendientes . ' ficha(s) pendientes de revisión',
                'icono' => 'bi-file-earmark-text'
            ];
        }

        if ($solicitudesPendientes > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => $solicitudesPendientes . ' solicitud(es) de beca pendientes de revisión',
                'icono' => 'bi-award'
            ];
        }

        if ($ayudasPendientes > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => $ayudasPendientes . ' solicitud(es) de ayuda requieren atención',
                'icono' => 'bi-exclamation-triangle'
            ];
        }

        if (empty($alertas)) {
            $alertas[] = [
                'tipo' => 'success',
                'mensaje' => 'Todo al día. No hay tareas pendientes urgentes.',
                'icono' => 'bi-check-circle'
            ];
        }

        return $alertas;
    }

    /**
     * Estadísticas de fichas socioeconómicas
     */
    public function getEstadisticasFichas()
    {
        // Estadísticas por estado
        $estados = $this->db->table('fichas_socioeconomicas')
            ->select('estado, COUNT(*) as total')
            ->where('estado !=', 'Borrador')
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        // Estadísticas por período
        $periodos = $this->db->table('fichas_socioeconomicas fs')
            ->select('p.nombre as periodo, COUNT(*) as total')
            ->join('periodos_academicos p', 'p.id = fs.periodo_id')
            ->where('fs.estado !=', 'Borrador')
            ->groupBy('fs.periodo_id, p.nombre')
            ->orderBy('p.fecha_inicio', 'DESC')
            ->get()
            ->getResultArray();

        // Estadísticas por carrera
        $carreras = $this->db->table('fichas_socioeconomicas fs')
            ->select('c.nombre as carrera, COUNT(*) as total')
            ->join('usuarios u', 'u.id = fs.estudiante_id')
            ->join('carreras c', 'c.id = u.carrera_id', 'left')
            ->where('fs.estado !=', 'Borrador')
            ->groupBy('u.carrera_id, c.nombre')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        return [
            'estados' => $estados,
            'periodos' => $periodos,
            'carreras' => $carreras,
            'total' => array_sum(array_column($estados, 'total'))
        ];
    }

    /**
     * Estadísticas de becas
     */
    public function getEstadisticasBecas()
    {
        // Becas por tipo
        $tipos = $this->db->table('becas')
            ->select('tipo_beca, COUNT(*) as total, SUM(cupos_disponibles) as cupos_totales')
            ->where('activa', 1)
            ->groupBy('tipo_beca')
            ->get()
            ->getResultArray();

        // Solicitudes por estado
        $solicitudes = $this->db->table('solicitudes_becas')
            ->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        // Becas más solicitadas
        $masSolicitadas = $this->db->table('solicitudes_becas sb')
            ->select('b.nombre as beca, COUNT(*) as total_solicitudes')
            ->join('becas b', 'b.id = sb.beca_id')
            ->groupBy('sb.beca_id, b.nombre')
            ->orderBy('total_solicitudes', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $total = 0;
        $aprobadas = 0;
        $pendientes = 0;
        $rechazadas = 0;
        foreach ($solicitudes as $s) {
            $total += $s['total'];
            if ($s['estado'] === 'Aprobada') $aprobadas = (int)$s['total'];
            if ($s['estado'] === 'Pendiente' || $s['estado'] === 'En Revisión') $pendientes += (int)$s['total'];
            if ($s['estado'] === 'Rechazada') $rechazadas = (int)$s['total'];
        }

        return [
            'tipos' => $tipos,
            'solicitudes' => $solicitudes,
            'mas_solicitadas' => $masSolicitadas,
            'total_becas' => $this->db->table('becas')->where('activa', 1)->countAllResults(),
            'total_solicitudes' => $this->db->table('solicitudes_becas')->countAllResults(),
            'total' => $total,
            'aprobadas' => $aprobadas,
            'pendientes' => $pendientes,
            'rechazadas' => $rechazadas
        ];
    }

    /**
     * Estadísticas de períodos académicos
     */
    public function getEstadisticasPeriodos()
    {
        $periodos = $this->db->query("
            SELECT p.*,
                COUNT(DISTINCT fs.id) as total_fichas,
                COUNT(DISTINCT sb.id) as total_solicitudes
            FROM periodos_academicos p
            LEFT JOIN fichas_socioeconomicas fs ON fs.periodo_id = p.id
            LEFT JOIN solicitudes_becas sb ON sb.periodo_id = p.id
            GROUP BY p.id
            ORDER BY p.fecha_inicio DESC
        ")->getResultArray();

        return $periodos;
    }

    public function getPeriodosActivos()
    {
        return $this->periodoModel->where('activo', 1)->findAll();
    }

    public function getCarrerasActivas()
    {
        return $this->db->table('carreras')->where('activa', 1)->get()->getResultArray();
    }

    /**
     * Estadísticas de usuarios
     */
    public function getEstadisticasUsuarios()
    {
        $usuarios = $this->db->table('usuarios u')
            ->select('r.nombre as nombre_rol, COUNT(*) as total')
            ->join('roles r', 'r.id = u.rol_id')
            ->groupBy('u.rol_id, r.nombre')
            ->get()
            ->getResultArray();

        $estudiantes = $this->db->table('usuarios u')
            ->select('c.nombre as carrera, COUNT(*) as total')
            ->join('carreras c', 'c.id = u.carrera_id', 'left')
            ->where('u.rol_id', 1)
            ->groupBy('u.carrera_id, c.nombre')
            ->get()
            ->getResultArray();

        return [
            'por_rol' => $usuarios,
            'estudiantes_por_carrera' => $estudiantes,
            'total' => $this->db->table('usuarios')->countAllResults()
        ];
    }

    /**
     * Estadísticas de solicitudes
     */
    public function getEstadisticasSolicitudes()
    {
        $solicitudes = $this->db->table('solicitudes_becas')
            ->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        $solicitudesPorMes = $this->db->table('solicitudes_becas')
            ->select('DATE_FORMAT(fecha_solicitud, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'DESC')
            ->limit(12)
            ->get()
            ->getResultArray();

        return [
            'por_estado' => $solicitudes,
            'por_mes' => $solicitudesPorMes,
            'total' => array_sum(array_column($solicitudes, 'total'))
        ];
    }

    /**
     * Obtener fichas con filtros avanzados
     */
    public function getFichasConFiltros($filtros = [])
    {
        // Usar vista SQL directa para mejor rendimiento
        $builder = $this->db->table('v_fichas_admin');

        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $builder->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['periodo_id'])) {
            $builder->where('periodo_id', $filtros['periodo_id']);
        }

        if (!empty($filtros['carrera_id'])) {
            $builder->where('estudiante_id IN (SELECT id FROM usuarios WHERE carrera_id = ' . (int)$filtros['carrera_id'] . ')');
        }

        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('estudiante_nombre', $filtros['busqueda'])
                ->orLike('cedula', $filtros['busqueda'])
                ->orLike('email', $filtros['busqueda'])
                ->groupEnd();
        }

        if (isset($filtros['evaluacion']) && $filtros['evaluacion'] !== '') {
            if ($filtros['evaluacion'] === 'con') {
                $builder->where('revisada_por_admin', 1);
            } elseif ($filtros['evaluacion'] === 'sin') {
                $builder->where('revisada_por_admin', 0);
            }
        }

        if (!empty($filtros['fecha_desde'])) {
            $builder->where('fecha_creacion >=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('fecha_creacion <=', $filtros['fecha_hasta']);
        }

        if (isset($filtros['relacionada_beca']) && $filtros['relacionada_beca'] !== '') {
            $builder->where('relacionada_beca', (int)$filtros['relacionada_beca']);
        }

        // Ordenamiento
        $orden = $filtros['orden'] ?? 'fecha_creacion DESC';
        $builder->orderBy($orden);

        // Paginación
        if (!empty($filtros['per_page'])) {
            $page = $filtros['page'] ?? 1;
            $offset = ($page - 1) * $filtros['per_page'];
            $builder->limit($filtros['per_page'], $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Obtener solicitudes de becas con filtros
     */
    public function getSolicitudesBecasConFiltros($filtros = [])
    {
        $builder = $this->db->table('solicitudes_becas sb')
            ->select('sb.*, u.nombre, u.apellido, u.cedula, u.email, c.nombre as carrera_nombre,
                     p.nombre as periodo_nombre, b.nombre as beca_nombre, b.tipo_beca')
            ->join('usuarios u', 'u.id = sb.estudiante_id')
            ->join('carreras c', 'c.id = u.carrera_id', 'left')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->join('becas b', 'b.id = sb.beca_id');

        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $builder->where('sb.estado', $filtros['estado']);
        }

        if (!empty($filtros['periodo_id'])) {
            $builder->where('sb.periodo_id', $filtros['periodo_id']);
        }

        if (!empty($filtros['carrera_id'])) {
            $builder->where('u.carrera_id', $filtros['carrera_id']);
        }

        if (!empty($filtros['tipo_beca'])) {
            $builder->where('b.tipo_beca', $filtros['tipo_beca']);
        }

        if (!empty($filtros['beca_id'])) {
            $builder->where('sb.beca_id', $filtros['beca_id']);
        }

        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('u.nombre', $filtros['busqueda'])
                ->orLike('u.apellido', $filtros['busqueda'])
                ->orLike('u.cedula', $filtros['busqueda'])
                ->orLike('u.email', $filtros['busqueda'])
                ->groupEnd();
        }

        // Ordenamiento
        $orden = $filtros['orden'] ?? 'sb.fecha_solicitud DESC';
        $builder->orderBy($orden);

        // Paginación
        if (!empty($filtros['per_page'])) {
            $page = $filtros['page'] ?? 1;
            $offset = ($page - 1) * $filtros['per_page'];
            $builder->limit($filtros['per_page'], $offset);
        }

        return $builder->get()->getResultArray();
    }

    public function contarSolicitudesBecas($filtros = [])
    {
        $builder = $this->db->table('solicitudes_becas sb')
            ->join('usuarios u', 'u.id = sb.estudiante_id')
            ->join('carreras c', 'c.id = u.carrera_id', 'left')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->join('becas b', 'b.id = sb.beca_id');

        if (!empty($filtros['estado'])) {
            $builder->where('sb.estado', $filtros['estado']);
        }
        if (!empty($filtros['periodo_id'])) {
            $builder->where('sb.periodo_id', $filtros['periodo_id']);
        }
        if (!empty($filtros['carrera_id'])) {
            $builder->where('u.carrera_id', $filtros['carrera_id']);
        }
        if (!empty($filtros['tipo_beca'])) {
            $builder->where('b.tipo_beca', $filtros['tipo_beca']);
        }
        if (!empty($filtros['beca_id'])) {
            $builder->where('sb.beca_id', $filtros['beca_id']);
        }
        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('u.nombre', $filtros['busqueda'])
                ->orLike('u.apellido', $filtros['busqueda'])
                ->orLike('u.cedula', $filtros['busqueda'])
                ->orLike('u.email', $filtros['busqueda'])
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Actualizar estado de ficha
     */
    public function actualizarEstadoFicha($fichaId, $nuevoEstado, $observaciones = null, $adminId = null)
    {
        $this->db->transStart();

        try {
            // Actualizar estado de la ficha
            $this->db->table('fichas_socioeconomicas')
                ->where('id', $fichaId)
                ->update([
                    'estado' => $nuevoEstado,
                    'fecha_actualizacion' => date('Y-m-d H:i:s'),
                    'actualizado_por' => $adminId
                ]);

            // Registrar observación si existe
            if ($observaciones) {
                $this->db->table('observaciones_fichas')->insert([
                    'ficha_id' => $fichaId,
                    'admin_id' => $adminId,
                    'observacion' => $observaciones,
                    'fecha_creacion' => date('Y-m-d H:i:s')
                ]);
            }

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'actualizar_estado_ficha',
                'tabla' => 'fichas_socioeconomicas',
                'registro_id' => $fichaId,
                'datos' => json_encode(['estado_anterior' => 'anterior', 'estado_nuevo' => $nuevoEstado])
            ]);

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error actualizando estado de ficha: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aprobar solicitud de beca
     */
    public function aprobarSolicitudBeca($solicitudId, $adminId, $observaciones = null)
    {
        $this->db->transStart();

        try {
            // Actualizar estado de la solicitud
            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'estado' => 'Aprobada',
                    'fecha_aprobacion' => date('Y-m-d H:i:s'),
                    'aprobado_por' => $adminId,
                    'observaciones_admin' => $observaciones
                ]);

            // Obtener información de la solicitud
            $solicitud = $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->get()
                ->getRowArray();

            // Crear relación ficha-beca si no existe
            $this->db->table('fichas_becas_relacion')->insert([
                'ficha_id' => $solicitud['ficha_id'],
                'beca_id' => $solicitud['beca_id'],
                'tipo_relacion' => 'Aprobada',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]);

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'aprobar_solicitud_beca',
                'tabla' => 'solicitudes_becas',
                'registro_id' => $solicitudId,
                'datos' => json_encode(['estado_anterior' => 'Pendiente', 'estado_nuevo' => 'Aprobada'])
            ]);

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error aprobando solicitud de beca: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechazar solicitud de beca
     */
    public function rechazarSolicitudBeca($solicitudId, $adminId, $motivo, $observaciones = null)
    {
        $this->db->transStart();

        try {
            // Actualizar estado de la solicitud
            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'estado' => 'Rechazada',
                    'fecha_rechazo' => date('Y-m-d H:i:s'),
                    'rechazado_por' => $adminId,
                    'motivo_rechazo' => $motivo,
                    'observaciones_admin' => $observaciones
                ]);

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'rechazar_solicitud_beca',
                'tabla' => 'solicitudes_becas',
                'registro_id' => $solicitudId,
                'datos' => json_encode(['estado_anterior' => 'Pendiente', 'estado_nuevo' => 'Rechazada', 'motivo' => $motivo])
            ]);

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error rechazando solicitud de beca: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nueva beca
     */
    public function crearBeca($datos, $adminId)
    {
        $this->db->transStart();

        try {
            // Insertar beca
            $becaId = $this->db->table('becas')->insert([
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'],
                'requisitos' => $datos['requisitos'],
                'puntaje_minimo_requerido' => $datos['puntaje_minimo'] ?? null,
                'monto_beca' => $datos['monto'] ?? null,
                'tipo_beca' => $datos['tipo_beca'],
                'cupos_disponibles' => $datos['cupos'] ?? null,
                'periodo_vigente_id' => $datos['periodo_id'] ?? null,
                'fecha_inicio_vigencia' => $datos['fecha_inicio'] ?? null,
                'fecha_fin_vigencia' => $datos['fecha_fin'] ?? null,
                'activa' => 1,
                'estado' => 'Activa',
                'creado_por' => $adminId,
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]);

            // Insertar documentos requisitos si existen
            if (!empty($datos['documentos_requisitos'])) {
                foreach ($datos['documentos_requisitos'] as $documento) {
                    $this->db->table('becas_documentos_requisitos')->insert([
                        'beca_id' => $becaId,
                        'nombre_documento' => $documento['nombre'],
                        'descripcion' => $documento['descripcion'],
                        'tipo_documento' => $documento['tipo'],
                        'obligatorio' => $documento['obligatorio'] ?? 1,
                        'orden_verificacion' => $documento['orden'] ?? 1,
                        'estado' => 'Activo'
                    ]);
                }
            }

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'crear_beca',
                'tabla' => 'becas',
                'registro_id' => $becaId,
                'datos' => json_encode($datos)
            ]);

            $this->db->transComplete();

            return $this->db->transStatus() ? $becaId : false;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creando beca: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar beca existente
     */
    public function actualizarBeca($becaId, $datos, $adminId)
    {
        $this->db->transStart();

        try {
            // Obtener valores anteriores para el log
            $valoresAnteriores = $this->db->table('becas')
                ->where('id', $becaId)
                ->get()
                ->getRowArray();

            // Actualizar beca
            $this->db->table('becas')
                ->where('id', $becaId)
                ->update([
                    'nombre' => $datos['nombre'],
                    'descripcion' => $datos['descripcion'],
                    'requisitos' => $datos['requisitos'],
                    'puntaje_minimo_requerido' => $datos['puntaje_minimo'] ?? null,
                    'monto_beca' => $datos['monto'] ?? null,
                    'tipo_beca' => $datos['tipo_beca'],
                    'cupos_disponibles' => $datos['cupos'] ?? null,
                    'periodo_vigente_id' => $datos['periodo_id'] ?? null,
                    'fecha_inicio_vigencia' => $datos['fecha_inicio'] ?? null,
                    'fecha_fin_vigencia' => $datos['fecha_fin'] ?? null
                ]);

            // Actualizar documentos requisitos si se proporcionan
            if (isset($datos['documentos_requisitos'])) {
                // Eliminar requisitos existentes
                $this->db->table('becas_documentos_requisitos')
                    ->where('beca_id', $becaId)
                    ->delete();

                // Insertar nuevos requisitos
                foreach ($datos['documentos_requisitos'] as $documento) {
                    $this->db->table('becas_documentos_requisitos')->insert([
                        'beca_id' => $becaId,
                        'nombre_documento' => $documento['nombre'],
                        'descripcion' => $documento['descripcion'],
                        'tipo_documento' => $documento['tipo'],
                        'obligatorio' => $documento['obligatorio'] ?? 1,
                        'orden_verificacion' => $documento['orden'] ?? 1,
                        'estado' => 'Activo'
                    ]);
                }
            }

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'actualizar_beca',
                'tabla' => 'becas',
                'registro_id' => $becaId,
                'datos' => json_encode(['anteriores' => $valoresAnteriores, 'nuevos' => $datos])
            ]);

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error actualizando beca: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nuevo período académico
     */
    public function crearPeriodoAcademico($datos, $adminId)
    {
        $this->db->transStart();

        try {
            $periodoId = $this->db->table('periodos_academicos')->insert([
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'],
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'activo' => $datos['activo'] ?? 0,
                'permite_fichas' => $datos['permite_fichas'] ?? 0,
                'permite_becas' => $datos['permite_becas'] ?? 0,
                'creado_por' => $adminId,
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]);

            // Registrar en logs
            $this->db->table('logs')->insert([
                'id_usuario' => $adminId,
                'accion' => 'crear_periodo_academico',
                'tabla' => 'periodos_academicos',
                'registro_id' => $periodoId,
                'datos' => json_encode($datos)
            ]);

            $this->db->transComplete();

            return $this->db->transStatus() ? $periodoId : false;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creando período académico: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar reporte en PDF
     */
    public function generarReportePDF($tipo, $filtros = [], $formato = 'pdf')
    {
        try {
            $data = [];
            
            switch ($tipo) {
                case 'fichas':
                    $data = $this->getFichasConFiltros($filtros);
                    break;
                case 'becas':
                    $data = $this->getSolicitudesBecasConFiltros($filtros);
                    break;
                case 'estadisticas':
                    $data = $this->getEstadisticasCompletas();
                    break;
                default:
                    throw new \Exception('Tipo de reporte no válido');
            }

            // Aquí se implementaría la generación del PDF usando TCPDF
            // Por ahora retornamos los datos
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error generando reporte: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exportar datos a Excel/CSV
     */
    public function exportarDatos($tipo, $filtros = [], $formato = 'csv')
    {
        try {
            $data = [];
            
            switch ($tipo) {
                case 'fichas':
                    $data = $this->getFichasConFiltros($filtros);
                    break;
                case 'becas':
                    $data = $this->getSolicitudesBecasConFiltros($filtros);
                    break;
                case 'usuarios':
                    $data = $this->usuarioModel->getUsuariosConCarrera(null, null, $filtros);
                    break;
                default:
                    throw new \Exception('Tipo de exportación no válido');
            }

            if ($formato === 'csv') {
                return $this->arrayToCsv($data);
            }

            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error exportando datos: ' . $e->getMessage());
            return false;
        }
    }

    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        $output = fopen('php://temp', 'r+');
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }

    /**
     * Obtener datos completos de una ficha por ID (para AJAX).
     * Incluye datos del estudiante, período y JSON data de la ficha.
     *
     * @param int|string $fichaId
     * @return array|null
     */
    public function getFichaCompleta($fichaId): ?array
    {
        try {
            $ficha = $this->db->table('fichas_socioeconomicas fs')
                ->select('fs.*, u.nombre, u.apellido, u.cedula, u.email, u.telefono,
                         c.nombre as carrera_nombre,
                         p.nombre as nombre_periodo,
                         p.nombre as periodo_nombre')
                ->join('usuarios u', 'u.id = fs.estudiante_id', 'left')
                ->join('carreras c', 'c.id = u.carrera_id', 'left')
                ->join('periodos_academicos p', 'p.id = fs.periodo_id', 'left')
                ->where('fs.id', (int)$fichaId)
                ->get()
                ->getRowArray();

            if (!$ficha) {
                return null;
            }

            // Decodificar json_data si existe
            if (!empty($ficha['json_data'])) {
                $ficha['datos_ficha'] = json_decode($ficha['json_data'], true) ?? [];
            } else {
                $ficha['datos_ficha'] = [];
            }

            return $ficha;
        } catch (\Exception $e) {
            log_message('error', 'AdminBienestarService::getFichaCompleta() error: ' . $e->getMessage());
            return null;
        }
    }
}

