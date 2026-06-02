<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">💬 Solicitudes de Ayuda Estudiantil</h4>
                    <p class="text-muted mb-0">Gestiona y responde a las solicitudes de ayuda de los estudiantes</p>
                </div>
                <div class="page-title-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="crearRespuestaRapida()">
                            <i class="bi bi-plus-circle"></i> Respuesta Rápida
                        </button>
                        <button type="button" class="btn btn-info" onclick="exportarSolicitudes()">
                            <i class="bi bi-file-excel"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="actualizarVista()">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Solicitudes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">Total Solicitudes</p>
                            <h5 class="mb-0"><?= count($solicitudes ?? []) ?></h5>
                            <p class="text-muted mb-0">
                                <span class="text-primary">
                                    <i class="bi bi-arrow-up"></i> 
                                    <?= count(array_filter($solicitudes ?? [], fn($s) => $s['estado'] === 'Pendiente')) ?> abiertas
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bi bi-chat-dots"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">En Proceso</p>
                            <h5 class="mb-0"><?= count(array_filter($solicitudes ?? [], fn($s) => $s['estado'] === 'En Proceso')) ?></h5>
                            <p class="text-muted mb-0">
                                <span class="text-warning">
                                    Requieren atención
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bi bi-clock"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">Resueltas</p>
                            <h5 class="mb-0"><?= count(array_filter($solicitudes ?? [], fn($s) => $s['estado'] === 'Resuelta')) ?></h5>
                            <p class="text-muted mb-0">
                                <span class="text-success">
                                    Este mes
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bi bi-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-1">Tiempo Promedio</p>
                            <h5 class="mb-0" id="tiempoPromedio">1.2 días</h5>
                            <p class="text-muted mb-0">
                                <span class="text-info">
                                    De respuesta
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bi bi-speedometer2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Uso -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading mb-1">¿Cómo usar Respuesta Rápida?</h6>
                <p class="mb-0">
                    1. <strong>Selecciona</strong> las solicitudes que quieres responder usando los checkboxes
                    2. <strong>Haz clic</strong> en "Respuesta Rápida" 
                    3. <strong>Elige</strong> el tipo de respuesta o escribe una personalizada
                    4. <strong>Confirma</strong> y la respuesta se aplicará a todas las solicitudes seleccionadas
                </p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" onchange="aplicarFiltros()">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendientes</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Resuelta">Resueltas</option>
                        <option value="Cerrada">Cerradas</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="filtroPrioridad" class="form-label">Prioridad</label>
                    <select class="form-select" id="filtroPrioridad" onchange="aplicarFiltros()">
                        <option value="">Todas las prioridades</option>
                        <option value="Urgente">Urgente</option>
                        <option value="Alta">Alta</option>
                        <option value="Media">Media</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="busquedaEstudiante" class="form-label">Buscar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="busquedaEstudiante" 
                               placeholder="Estudiante, asunto..." onkeyup="aplicarFiltros()">
                        <button class="btn btn-outline-secondary" type="button" onclick="limpiarFiltros()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Solicitudes -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>Solicitudes de Ayuda
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-secondary" id="contadorResultados">
                        <?= count($solicitudes ?? []) ?> solicitudes
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarSolicitudesPendientes()">
                        <i class="bi bi-check-all me-1"></i>Seleccionar Pendientes
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                        <i class="bi bi-x-circle me-1"></i>Limpiar Selección
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="probarSeleccion()">
                        <i class="bi bi-bug me-1"></i>Probar Selección
                    </button>

                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaSolicitudes">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th width="25%">Estudiante</th>
                            <th width="20%">Asunto</th>
                            <th width="10%">Prioridad</th>
                            <th width="10%">Estado</th>
                            <th width="10%">Fecha</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($solicitudes)): ?>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr data-solicitud-id="<?= $solicitud['id'] ?>" class="solicitud-row 
                                    <?= $solicitud['prioridad'] === 'Urgente' ? 'table-danger' : '' ?>">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input solicitud-checkbox" type="checkbox" 
                                                   value="<?= $solicitud['id'] ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    <?= strtoupper(substr($solicitud['estudiante_nombre'] ?? 'E', 0, 1)) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?= esc($solicitud['estudiante_nombre'] ?? 'Estudiante') ?></h6>
                                                <p class="text-muted mb-0 small">
                                                    <i class="bi bi-credit-card me-1"></i><?= esc($solicitud['estudiante_cedula'] ?? '0003') ?>
                                                </p>
                                                <p class="text-muted mb-0 small">
                                                    <i class="bi bi-mortarboard me-1"></i><?= esc($solicitud['carrera_nombre'] ?? 'Sin carrera') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1"><?= esc($solicitud['asunto']) ?></h6>
                                            <p class="text-muted mb-0 small">
                                                <?= esc(substr($solicitud['descripcion'], 0, 80)) ?>...
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= match($solicitud['prioridad']) {
                                            'Urgente' => 'bg-danger',
                                            'Alta' => 'bg-warning',
                                            'Media' => 'bg-info',
                                            'Baja' => 'bg-success',
                                            default => 'bg-secondary'
                                        } ?>"><?= esc($solicitud['prioridad']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?= match($solicitud['estado']) {
                                            'Pendiente' => 'bg-primary',
                                            'En Proceso' => 'bg-warning',
                                            'Resuelta' => 'bg-success',
                                            'Cerrada' => 'bg-secondary',
                                            default => 'bg-light'
                                        } ?>"><?= esc($solicitud['estado']) ?></span>
                                        
                                        <?php if ($solicitud['responsable_nombre']): ?>
                                            <br><small class="text-muted">
                                                Asignado a: <?= esc($solicitud['responsable_nombre']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                            <br><small class="text-muted"><?= date('H:i', strtotime($solicitud['fecha_solicitud'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="responderSolicitud(<?= $solicitud['id'] ?>)" 
                                                    title="Responder">
                                                <i class="bi bi-reply"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="verHistorialSolicitudes(<?= $solicitud['id_estudiante'] ?>, '<?= $solicitud['estudiante_nombre'] ?> <?= $solicitud['estudiante_apellido'] ?>')" 
                                                    title="Historial del estudiante">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($solicitud['estado'] !== 'Resuelta'): ?>
                                                        <li><a class="dropdown-item text-success" href="#" 
                                                               onclick="marcarResuelta(<?= $solicitud['id'] ?>)">
                                                            <i class="bi bi-check-circle"></i> Marcar Resuelta
                                                        </a></li>
                                                    <?php endif; ?>
                                                    <li><a class="dropdown-item" href="#" 
                                                           onclick="asignarSolicitud(<?= $solicitud['id'] ?>)">
                                                        <i class="bi bi-person-plus"></i> Asignar
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" 
                                                           onclick="cambiarPrioridad(<?= $solicitud['id'] ?>)">
                                                        <i class="bi bi-flag"></i> Cambiar Prioridad
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" 
                                                           onclick="cerrarSolicitud(<?= $solicitud['id'] ?>)">
                                                        <i class="bi bi-x-circle"></i> Cerrar
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-chat-square-text display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No hay solicitudes de ayuda</h5>
                                    <p class="text-muted">Las solicitudes aparecerán aquí cuando los estudiantes las envíen</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Respuesta Predefinida -->
<div class="modal fade" id="modalCrearRespuesta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Crear Respuesta Predefinida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCrearRespuesta">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombreRespuesta" class="form-label">Nombre de la Respuesta *</label>
                                <input type="text" class="form-control" id="nombreRespuesta" name="nombre" 
                                       placeholder="Ej: Saludo estándar, Solicitud de información..." required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Un nombre descriptivo para identificar esta respuesta
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoriaRespuesta" class="form-label">Categoría *</label>
                                <select class="form-select" id="categoriaRespuesta" name="categoria" required>
                                    <option value="">Seleccionar categoría</option>
                                    <option value="saludo">Saludo</option>
                                    <option value="informacion">Solicitud de Información</option>
                                    <option value="resolucion">Resolución</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="cierre">Cierre</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenidoRespuesta" class="form-label">Contenido de la Respuesta *</label>
                        <textarea class="form-control" id="contenidoRespuesta" name="contenido" rows="8" 
                                  placeholder="Escribe aquí el contenido de tu respuesta predefinida..." required></textarea>
                        <div class="form-text">
                            <i class="bi bi-lightbulb me-1"></i>
                            Puedes usar variables como {nombre_estudiante}, {fecha}, {carrera} que se reemplazarán automáticamente
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tagsRespuesta" class="form-label">Etiquetas</label>
                                <input type="text" class="form-control" id="tagsRespuesta" name="tags" 
                                       placeholder="Ej: saludo, bienvenida, estándar">
                                <div class="form-text">
                                    <i class="bi bi-tags me-1"></i>
                                    Etiquetas separadas por comas para facilitar la búsqueda
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="respuestaPublica" name="publica" checked>
                                    <label class="form-check-label" for="respuestaPublica">
                                        Respuesta pública para todos los administradores
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="respuestaActiva" name="activa" checked>
                                    <label class="form-check-label" for="respuestaActiva">
                                        Respuesta activa y disponible
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cancelarCrearRespuesta()">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRespuestaPredefinida()">
                    <i class="bi bi-save me-2"></i>Guardar Respuesta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Responder Solicitud -->
<div class="modal fade" id="modalResponderSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responder Solicitud de Ayuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoSolicitud">
                    <!-- Contenido cargado dinámicamente -->
                </div>
                
                <form id="formRespuesta">
                    <input type="hidden" id="solicitud_id_respuesta" name="solicitud_id">
                    
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta</label>
                        <div class="btn-toolbar mb-2" role="toolbar">
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertarTexto('**Gracias por contactarnos.**')">
                                    <i class="bi bi-hand-thumbs-up"></i> Saludo
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertarTexto('Para resolver tu consulta, necesito que proporciones...')">
                                    <i class="bi bi-question-circle"></i> Solicitar Info
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertarTexto('Tu solicitud ha sido resuelta. Si tienes más preguntas...')">
                                    <i class="bi bi-check-circle"></i> Resolución
                                </button>
                            </div>
                        </div>
                        <textarea class="form-control" id="respuesta" name="respuesta" rows="6" 
                                  placeholder="Escribe tu respuesta aquí..." required></textarea>
                    </div>
                    
                    <!-- Nueva sección: Respuestas Personalizadas -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">
                                <i class="bi bi-star me-2"></i>Respuestas Personalizadas
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="mostrarModalCrearRespuesta()">
                                <i class="bi bi-plus-circle me-1"></i>Crear Nueva
                            </button>
                        </div>
                        <div id="respuestasPersonalizadas" class="border rounded p-3 bg-light">
                            <div class="text-center text-muted">
                                <i class="bi bi-info-circle me-2"></i>
                                No tienes respuestas personalizadas. Haz clic en "Crear Nueva" para crear tu primera respuesta.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="nuevo_estado" class="form-label">Cambiar Estado</label>
                            <select class="form-select" id="nuevo_estado" name="nuevo_estado">
                                <option value="">Mantener estado actual</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Resuelta">Resuelta</option>
                                <option value="Cerrada">Cerrada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nueva_prioridad" class="form-label">Cambiar Prioridad</label>
                            <select class="form-select" id="nueva_prioridad" name="nueva_prioridad">
                                <option value="">Mantener prioridad actual</option>
                                <option value="Baja">Baja</option>
                                <option value="Media">Media</option>
                                <option value="Alta">Alta</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notificar_estudiante" 
                                   name="notificar_estudiante" checked>
                            <label class="form-check-label" for="notificar_estudiante">
                                Notificar al estudiante por email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="solicitar_calificacion" 
                                   name="solicitar_calificacion">
                            <label class="form-check-label" for="solicitar_calificacion">
                                Solicitar calificación del servicio
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarRespuesta()">
                    <i class="bi bi-send"></i> Enviar Respuesta
                </button>
            </div>
        </div>
    </div>
</div>



<script>
// Variables globales
let solicitudesData = <?= json_encode($solicitudes ?? []) ?>;

function aplicarFiltros() {
    const estado = document.getElementById('filtroEstado').value;
    const prioridad = document.getElementById('filtroPrioridad').value;
    const busqueda = document.getElementById('busquedaEstudiante').value.toLowerCase();
    
    const filas = document.querySelectorAll('.solicitud-row');
    let visibles = 0;
    
    filas.forEach(fila => {
        const solicitudId = fila.getAttribute('data-solicitud-id');
        const solicitud = solicitudesData.find(s => s.id == solicitudId);
        
        if (!solicitud) return;
        
        let mostrar = true;
        
        if (estado && solicitud.estado !== estado) mostrar = false;
        if (prioridad && solicitud.prioridad !== prioridad) mostrar = false;
        
        if (busqueda) {
            const textoCompleto = `${solicitud.estudiante_nombre} ${solicitud.asunto} ${solicitud.descripcion}`.toLowerCase();
            if (!textoCompleto.includes(busqueda)) mostrar = false;
        }
        
        if (mostrar) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    document.getElementById('contadorResultados').textContent = `${visibles} solicitudes`;
}

function limpiarFiltros() {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroPrioridad').value = '';
    document.getElementById('busquedaEstudiante').value = '';
    aplicarFiltros();
}

function responderSolicitud(solicitudId) {
    fetch(`<?= base_url('admin-bienestar/detalle-solicitud-ayuda') ?>/${solicitudId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const solicitud = data.solicitud;
            const respuestas = data.respuestas;
            
            // Llenar el contenido del modal
            const contenidoSolicitud = document.getElementById('contenidoSolicitud');
            let historialHTML = '';
            
            if (respuestas && respuestas.length > 0) {
                historialHTML = `
                    <div class="mt-3">
                        <h6><i class="bi bi-clock-history me-2"></i>Historial de Respuestas</h6>
                        ${respuestas.map(resp => `
                            <div class="bg-light p-3 rounded mb-2 border-start border-primary">
                                <div class="d-flex justify-content-between align-items-start">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        ${new Date(resp.fecha_respuesta).toLocaleString('es-ES')}
                                    </small>
                                </div>
                                <p class="mb-1 mt-2">${resp.respuesta}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                historialHTML = `
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay respuestas registradas para esta solicitud
                        </div>
                    </div>
                `;
            }
            
            contenidoSolicitud.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                        <p><strong>Nombre:</strong> ${solicitud.estudiante_nombre} ${solicitud.estudiante_apellido}</p>
                        <p><strong>Cédula:</strong> ${solicitud.estudiante_cedula}</p>
                        <p><strong>Carrera:</strong> ${solicitud.carrera_nombre || 'No especificada'}</p>
                        <p><strong>Email:</strong> ${solicitud.estudiante_email}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-file-text me-2"></i>Detalles de la Solicitud</h6>
                        <p><strong>Asunto:</strong> ${solicitud.asunto_personalizado || solicitud.asunto}</p>
                        <p><strong>Prioridad:</strong> <span class="badge bg-${getPrioridadColor(solicitud.prioridad)}">${solicitud.prioridad}</span></p>
                        <p><strong>Estado:</strong> <span class="badge bg-${getEstadoColor(solicitud.estado)}">${solicitud.estado}</span></p>
                        <p><strong>Fecha:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleString('es-ES')}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6><i class="bi bi-chat-text me-2"></i>Descripción</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">${solicitud.descripcion}</p>
                    </div>
                </div>
                ${historialHTML}
            `;
            
            // Configurar el ID de la solicitud en el formulario
            document.getElementById('solicitud_id_respuesta').value = solicitudId;
            
            // Limpiar campos del formulario
            document.getElementById('respuesta').value = '';
            document.getElementById('nuevo_estado').value = '';
            document.getElementById('nueva_prioridad').value = '';
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalResponderSolicitud'));
            modal.show();
        } else {
            mostrarNotificacion(data.error || 'Error cargando detalles', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error cargando detalles de la solicitud', 'error');
    });
}

function enviarRespuesta() {
    const form = document.getElementById('formRespuesta');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const datos = {
        solicitud_id: formData.get('solicitud_id'),
        respuesta: formData.get('respuesta'),
        nuevo_estado: formData.get('nuevo_estado'),
        nueva_prioridad: formData.get('nueva_prioridad'),
        notificar_estudiante: document.getElementById('notificar_estudiante').checked,
        solicitar_calificacion: document.getElementById('solicitar_calificacion').checked
    };
    
    fetch(`<?= base_url('admin-bienestar/responder-solicitud-ayuda') ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('Respuesta enviada exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalResponderSolicitud')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion(data.error || 'Error enviando la respuesta', 'error');
        }
    });
}

function marcarResuelta(solicitudId) {
    if (confirm('¿Marcar esta solicitud como resuelta?')) {
        fetch(`<?= base_url('admin-bienestar/marcar-solicitud-resuelta') ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ solicitud_id: solicitudId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Solicitud marcada como resuelta', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacion(data.error || 'Error actualizando la solicitud', 'error');
            }
        });
    }
}

function insertarTexto(texto) {
    const textarea = document.getElementById('respuesta');
    const inicio = textarea.selectionStart;
    const fin = textarea.selectionEnd;
    const valorActual = textarea.value;
    
    textarea.value = valorActual.substring(0, inicio) + texto + valorActual.substring(fin);
    textarea.focus();
    textarea.setSelectionRange(inicio + texto.length, inicio + texto.length);
}

function mostrarModalCrearRespuesta() {
    // Solo abrir si se hace clic específicamente en "Crear Respuesta"
    // Cerrar el modal de responder para evitar conflictos
    const modalResponder = bootstrap.Modal.getInstance(document.getElementById('modalResponderSolicitud'));
    if (modalResponder) {
        modalResponder.hide();
    }
    
    // Esperar a que se cierre completamente antes de abrir el nuevo
    setTimeout(() => {
        // Limpiar el formulario
        document.getElementById('formCrearRespuesta').reset();
        
        // Mostrar el modal de crear respuesta
        const modal = new bootstrap.Modal(document.getElementById('modalCrearRespuesta'));
        modal.show();
        
        // Cargar respuestas predefinidas existentes
        cargarRespuestasPredefinidas();
    }, 300);
}

function guardarRespuestaPredefinida() {
    const form = document.getElementById('formCrearRespuesta');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const datos = {
        nombre: formData.get('nombre'),
        categoria: formData.get('categoria'),
        contenido: formData.get('contenido'),
        tags: formData.get('tags'),
        publica: document.getElementById('respuestaPublica').checked,
        activa: document.getElementById('respuestaActiva').checked
    };
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando Respuesta',
        text: 'Creando tu respuesta predefinida...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar al servidor
    fetch('<?= base_url('admin-bienestar/guardar-respuesta-predefinida') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Respuesta Guardada!',
                text: data.message,
                confirmButtonText: 'Perfecto'
            }).then(() => {
                // Cerrar modal de crear respuesta
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearRespuesta'));
                modal.hide();
                
                // Agregar la nueva respuesta a los botones existentes
                agregarRespuestaPredefinida(data.respuesta);
                
                // Reabrir el modal de responder después de un breve delay
                setTimeout(() => {
                    const modalResponder = new bootstrap.Modal(document.getElementById('modalResponderSolicitud'));
                    modalResponder.show();
                }, 300);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al Guardar',
                text: data.error || 'No se pudo guardar la respuesta',
                confirmButtonText: 'Entendido'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error del Sistema',
            text: 'No se pudo conectar con el servidor',
            confirmButtonText: 'Entendido'
        });
    });
}

function cancelarCrearRespuesta() {
    // Cerrar modal de crear respuesta
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearRespuesta'));
    modal.hide();
    
    // Reabrir el modal de responder después de un breve delay
    setTimeout(() => {
        const modalResponder = new bootstrap.Modal(document.getElementById('modalResponderSolicitud'));
        modalResponder.show();
    }, 300);
}

function agregarRespuestaPredefinida(respuesta) {
    // Crear contenedor para la respuesta personalizada
    const contenedor = document.createElement('div');
    contenedor.className = 'd-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-white';
    contenedor.style.minHeight = '40px';
    
    // Crear botón principal para la respuesta predefinida
    const nuevoBoton = document.createElement('button');
    nuevoBoton.type = 'button';
    nuevoBoton.className = 'btn btn-outline-success btn-sm flex-grow-1 me-2';
    nuevoBoton.onclick = () => insertarTexto(respuesta.contenido);
    nuevoBoton.innerHTML = `<i class="bi bi-star me-1"></i>${respuesta.nombre}`;
    nuevoBoton.style.textAlign = 'left';
    
    // Crear botón de eliminar (X)
    const botonEliminar = document.createElement('button');
    botonEliminar.type = 'button';
    botonEliminar.className = 'btn btn-outline-danger btn-sm';
    botonEliminar.style.fontSize = '0.8em';
    botonEliminar.style.padding = '4px 8px';
    botonEliminar.style.minWidth = '30px';
    botonEliminar.innerHTML = '×';
    botonEliminar.onclick = () => eliminarRespuestaPredefinida(respuesta.id, contenedor);
    
    // Agregar botones al contenedor
    contenedor.appendChild(nuevoBoton);
    contenedor.appendChild(botonEliminar);
    
    // Agregar a la sección de respuestas personalizadas
    const seccionRespuestas = document.getElementById('respuestasPersonalizadas');
    
    // Si es la primera respuesta, limpiar el mensaje de "no hay respuestas"
    if (seccionRespuestas.querySelector('.text-muted')) {
        seccionRespuestas.innerHTML = '';
    }
    
    seccionRespuestas.appendChild(contenedor);
    
    // Mostrar notificación
    mostrarNotificacion(`Respuesta "${respuesta.nombre}" agregada a tus respuestas personalizadas`, 'success');
}

function cargarRespuestasPredefinidas() {
    fetch('<?= base_url('admin-bienestar/obtener-respuestas-predefinidas') ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.respuestas) {
            const seccionRespuestas = document.getElementById('respuestasPersonalizadas');
            
            // Limpiar sección de respuestas personalizadas
            seccionRespuestas.innerHTML = '';
            
            // Agregar cada respuesta predefinida
            data.respuestas.forEach(respuesta => {
                agregarRespuestaPredefinida(respuesta);
            });
        }
    })
    .catch(error => {
        console.error('Error cargando respuestas predefinidas:', error);
    });
}

function eliminarRespuestaPredefinida(respuestaId, contenedor) {
    Swal.fire({
        title: '¿Eliminar Respuesta?',
        text: '¿Estás seguro de que quieres eliminar esta respuesta predefinida? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando Respuesta',
                text: 'Eliminando tu respuesta predefinida...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar solicitud de eliminación al servidor
            fetch('<?= base_url('admin-bienestar/eliminar-respuesta-predefinida') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: respuestaId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar el contenedor del DOM
                    contenedor.remove();
                    
                    // Verificar si no quedan más respuestas y mostrar mensaje
                    const seccionRespuestas = document.getElementById('respuestasPersonalizadas');
                    if (seccionRespuestas.children.length === 0) {
                        seccionRespuestas.innerHTML = `
                            <div class="text-center text-muted">
                                <i class="bi bi-info-circle me-2"></i>
                                No tienes respuestas personalizadas. Haz clic en "Crear Nueva" para crear tu primera respuesta.
                            </div>
                        `;
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Respuesta Eliminada!',
                        text: data.message,
                        confirmButtonText: 'Perfecto'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al Eliminar',
                        text: data.error || 'No se pudo eliminar la respuesta',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Sistema',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonText: 'Entendido'
                });
            });
        }
    });
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const alertClass = tipo === 'success' ? 'alert-success' : 
                      tipo === 'error' ? 'alert-danger' : 'alert-info';
    
    const alerta = document.createElement('div');
    alerta.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.remove();
        }
    }, 5000);
}

function actualizarVista() {
    location.reload();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.solicitud-checkbox');
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = selectAll.checked;
    });
    
    // Actualizar contador después de cambiar selección
    setTimeout(actualizarContadorSeleccionadas, 100);
}

function crearRespuestaRapida() {
    // Obtener solicitudes seleccionadas
    const solicitudesSeleccionadas = obtenerSolicitudesSeleccionadas();
    
    if (solicitudesSeleccionadas.length === 0) {
        Swal.fire({
            title: 'No hay solicitudes seleccionadas',
            text: 'Por favor, selecciona al menos una solicitud de la tabla antes de usar respuesta rápida',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    Swal.fire({
        title: 'Respuesta Rápida',
        html: `
            <div class="mb-3">
                <label class="form-label">Tipo de Respuesta</label>
                <select class="form-select" id="tipoRespuestaRapida">
                    <option value="saludo">Saludo y Confirmación</option>
                    <option value="solicitar_info">Solicitar Información</option>
                    <option value="resolucion">Resolución</option>
                    <option value="proceso">En Proceso</option>
                    <option value="cerrada">Cerrar Solicitud</option>
                    <option value="personalizada">Respuesta Personalizada</option>
                </select>
            </div>
            <div class="mb-3" id="comentarioAdicionalDiv" style="display: none;">
                <label class="form-label">Comentario Adicional</label>
                <textarea class="form-control" id="comentarioAdicional" rows="3" 
                          placeholder="Describe qué información adicional necesitas..."></textarea>
            </div>
            <div class="mb-3" id="respuestaPersonalizadaDiv" style="display: none;">
                <label class="form-label">Respuesta Personalizada</label>
                <textarea class="form-control" id="respuestaPersonalizada" rows="4" 
                          placeholder="Escribe tu respuesta personalizada aquí..."></textarea>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>${solicitudesSeleccionadas.length} solicitud(es) seleccionada(s)</strong>
                <br>La respuesta se aplicará a todas las solicitudes seleccionadas.
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar Respuesta',
        cancelButtonText: 'Cancelar',
        width: '600px',
        preConfirm: () => {
            const tipoRespuesta = document.getElementById('tipoRespuestaRapida').value;
            const comentarioAdicional = document.getElementById('comentarioAdicional').value;
            const respuestaPersonalizada = document.getElementById('respuestaPersonalizada').value;
            
            if (tipoRespuesta === 'solicitar_info' && !comentarioAdicional.trim()) {
                Swal.showValidationMessage('Debe proporcionar un comentario adicional');
                return false;
            }
            
            if (tipoRespuesta === 'personalizada' && !respuestaPersonalizada.trim()) {
                Swal.showValidationMessage('Debe escribir una respuesta personalizada');
                return false;
            }
            
            return { tipoRespuesta, comentarioAdicional, respuestaPersonalizada };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Aplicar respuesta a todas las solicitudes seleccionadas
            aplicarRespuestaRapida(solicitudesSeleccionadas, result.value);
        }
    });
    
    // Mostrar/ocultar campos según tipo
    setTimeout(() => {
        document.getElementById('tipoRespuestaRapida').addEventListener('change', function() {
            const comentarioDiv = document.getElementById('comentarioAdicionalDiv');
            const personalizadaDiv = document.getElementById('respuestaPersonalizadaDiv');
            
            comentarioDiv.style.display = 'none';
            personalizadaDiv.style.display = 'none';
            
            if (this.value === 'solicitar_info') {
                comentarioDiv.style.display = 'block';
            } else if (this.value === 'personalizada') {
                personalizadaDiv.style.display = 'block';
            }
        });
    }, 100);
}

// Función para obtener solicitudes seleccionadas
function obtenerSolicitudesSeleccionadas() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox:checked');
    const solicitudes = Array.from(checkboxes).map(cb => parseInt(cb.value));
    return solicitudes;
}

// Función para aplicar respuesta rápida a múltiples solicitudes
function aplicarRespuestaRapida(solicitudesIds, datosRespuesta) {
    Swal.fire({
        title: 'Aplicando Respuesta Rápida',
        text: `Procesando ${solicitudesIds.length} solicitud(es)...`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    let procesadas = 0;
    let exitosas = 0;
    let errores = 0;
    let erroresDetalle = [];
    
    // Procesar cada solicitud
    solicitudesIds.forEach((solicitudId, index) => {
        const datos = {
            solicitud_id: solicitudId,
            tipo_respuesta: datosRespuesta.tipoRespuesta,
            comentario_adicional: datosRespuesta.comentarioAdicional || '',
            respuesta_personalizada: datosRespuesta.respuestaPersonalizada || ''
        };
        const url = '<?= base_url('admin-bienestar/crear-respuesta-rapida') ?>';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            procesadas++;
            if (data.success) {
                exitosas++;
            } else {
                errores++;
                erroresDetalle.push(`Solicitud ${solicitudId}: ${data.error || 'Error desconocido'}`);
            }
            
            // Si es la última solicitud, mostrar resultado
            if (procesadas === solicitudesIds.length) {
                mostrarResultadoRespuestaRapida(exitosas, errores, solicitudesIds.length, erroresDetalle);
            }
        })
        .catch(error => {
            procesadas++;
            errores++;
            console.error(`Error procesando solicitud ${solicitudId}:`, error);
            erroresDetalle.push(`Solicitud ${solicitudId}: ${error.message}`);
            
            if (procesadas === solicitudesIds.length) {
                mostrarResultadoRespuestaRapida(exitosas, errores, solicitudesIds.length, erroresDetalle);
            }
        });
    });
}

// Función para mostrar resultado de respuesta rápida
function mostrarResultadoRespuestaRapida(exitosas, errores, total, erroresDetalle = []) {
    let icono = 'success';
    let titulo = '¡Respuesta Aplicada!';
    let mensaje = '';
    let html = '';
    
    if (errores === 0) {
        mensaje = `Se aplicó la respuesta rápida exitosamente a las ${exitosas} solicitudes.`;
        html = `<div class="text-success"><i class="bi bi-check-circle me-2"></i>${mensaje}</div>`;
    } else if (exitosas === 0) {
        icono = 'error';
        titulo = 'Error en la Aplicación';
        mensaje = `No se pudo aplicar la respuesta a ninguna de las ${total} solicitudes.`;
        html = `
            <div class="text-danger mb-3"><i class="bi bi-x-circle me-2"></i>${mensaje}</div>
            <div class="text-start">
                <strong>Detalles de errores:</strong>
                <ul class="mt-2 mb-0">
                    ${erroresDetalle.map(error => `<li class="text-danger">${error}</li>`).join('')}
                </ul>
            </div>
        `;
    } else {
        icono = 'warning';
        titulo = 'Aplicación Parcial';
        mensaje = `Se aplicó la respuesta a ${exitosas} solicitudes, pero hubo ${errores} errores.`;
        html = `
            <div class="text-warning mb-3"><i class="bi bi-exclamation-triangle me-2"></i>${mensaje}</div>
            ${errores > 0 ? `
                <div class="text-start">
                    <strong>Errores encontrados:</strong>
                    <ul class="mt-2 mb-0">
                        ${erroresDetalle.map(error => `<li class="text-danger">${error}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        `;
    }
    
    Swal.fire({
        icon: icono,
        title: titulo,
        html: html,
        confirmButtonText: 'Entendido',
        width: errores > 0 ? '600px' : '500px'
    }).then(() => {
        // Solo recargar si no hay errores o si hay éxitos
        if (errores === 0 || exitosas > 0) {
            location.reload();
        }
    });
}

function exportarSolicitudes() {
    Swal.fire({
        title: 'Exportando Solicitudes',
        text: 'Preparando archivo de exportación...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Usar fetch para obtener el archivo
    fetch('<?= base_url('admin-bienestar/exportar-solicitudes') ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Verificar el tipo de contenido
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            // Si es JSON, hay un error
            return response.json().then(data => {
                throw new Error(data.error || 'Error en la exportación');
            });
        }
        
        // Si es CSV, proceder con la descarga
        return response.blob();
    })
    .then(blob => {
        if (blob.size < 100) {
            throw new Error('El archivo generado está vacío o es muy pequeño');
        }
        
        // Crear URL del blob
        const url = window.URL.createObjectURL(blob);
        
        // Crear enlace de descarga
        const link = document.createElement('a');
        link.href = url;
        link.download = 'solicitudes_ayuda_' + new Date().toISOString().split('T')[0] + '.csv';
        
        // Agregar a DOM, hacer clic y limpiar
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Liberar memoria
        window.URL.revokeObjectURL(url);
        
        // Mostrar éxito
        Swal.fire({
            icon: 'success',
            title: '¡Exportado Exitosamente!',
            text: `Se han exportado ${Math.floor(blob.size / 100)} solicitudes aproximadamente`,
            confirmButtonText: 'Perfecto'
        });
    })
    .catch(error => {
        console.error('Error en exportación:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error en la Exportación',
            text: error.message || 'No se pudo exportar el archivo',
            confirmButtonText: 'Entendido'
        });
    });
}

function verDetalleSolicitud(solicitudId) {
    fetch(`<?= base_url('admin-bienestar/detalle-solicitud-ayuda') ?>/${solicitudId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const solicitud = data.solicitud;
            const respuestas = data.respuestas;
            
            // Llenar el contenido del modal
            const contenidoSolicitud = document.getElementById('contenidoSolicitud');
            let historialHTML = '';
            
            if (respuestas && respuestas.length > 0) {
                historialHTML = `
                    <div class="mt-3">
                        <h6><i class="bi bi-clock-history me-2"></i>Historial de Respuestas</h6>
                        ${respuestas.map(resp => `
                            <div class="bg-light p-3 rounded mb-2 border-start border-primary">
                                <div class="d-flex justify-content-between align-items-start">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        ${new Date(resp.fecha_respuesta).toLocaleString('es-ES')}
                                    </small>
                                </div>
                                <p class="mb-1 mt-2">${resp.respuesta}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                historialHTML = `
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay respuestas registradas para esta solicitud
                        </div>
                    </div>
                `;
            }
            
            contenidoSolicitud.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                        <p><strong>Nombre:</strong> ${solicitud.estudiante_nombre} ${solicitud.estudiante_apellido}</p>
                        <p><strong>Cédula:</strong> ${solicitud.estudiante_cedula}</p>
                        <p><strong>Carrera:</strong> ${solicitud.carrera_nombre || 'No especificada'}</p>
                        <p><strong>Email:</strong> ${solicitud.estudiante_email}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-file-text me-2"></i>Detalles de la Solicitud</h6>
                        <p><strong>Asunto:</strong> ${solicitud.asunto_personalizado || solicitud.asunto}</p>
                        <p><strong>Prioridad:</strong> <span class="badge bg-${getPrioridadColor(solicitud.prioridad)}">${solicitud.prioridad}</span></p>
                        <p><strong>Estado:</strong> <span class="badge bg-${getEstadoColor(solicitud.estado)}">${solicitud.estado}</span></p>
                        <p><strong>Fecha:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleString('es-ES')}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6><i class="bi bi-chat-text me-2"></i>Descripción</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">${solicitud.descripcion}</p>
                    </div>
                </div>
                ${historialHTML}
            `;
            
            // Configurar el ID de la solicitud en el formulario
            document.getElementById('solicitud_id_respuesta').value = solicitudId;
            
            // Limpiar campos del formulario
            document.getElementById('respuesta').value = '';
            document.getElementById('nuevo_estado').value = '';
            document.getElementById('nueva_prioridad').value = '';
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalResponderSolicitud'));
            modal.show();
            
            // Cargar respuestas predefinidas del usuario
            setTimeout(() => {
                cargarRespuestasPredefinidas();
            }, 100);
        } else {
            mostrarNotificacion(data.error || 'Error cargando detalles', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error cargando detalles de la solicitud', 'error');
    });
}

function asignarSolicitud(solicitudId) {
    Swal.fire({
        title: 'Asignar Solicitud',
        text: '¿Deseas asignar esta solicitud a tu usuario?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Asignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('admin-bienestar/asignar-solicitud') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ solicitud_id: solicitudId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.error || 'Error asignando solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
    });
}

function cambiarPrioridad(solicitudId) {
    Swal.fire({
        title: 'Cambiar Prioridad',
        html: `
            <div class="mb-3">
                <label class="form-label">Nueva Prioridad</label>
                <select class="form-select" id="nuevaPrioridad">
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                    <option value="Urgente">Urgente</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return document.getElementById('nuevaPrioridad').value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('admin-bienestar/cambiar-prioridad') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    solicitud_id: solicitudId,
                    nueva_prioridad: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.error || 'Error cambiando prioridad', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
    });
}

function verHistorial(solicitudId) {
    verDetalleSolicitud(solicitudId);
}

function cerrarSolicitud(solicitudId) {
    Swal.fire({
        title: '¿Cerrar Solicitud?',
        text: 'Esta acción marcará la solicitud como cerrada. ¿Estás seguro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, Cerrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('admin-bienestar/cerrar-solicitud') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ solicitud_id: solicitudId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.error || 'Error cerrando solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
    });
}

// Funciones auxiliares
function getPrioridadColor(prioridad) {
    const colores = {
        'Urgente': 'danger',
        'Alta': 'warning',
        'Media': 'info',
        'Baja': 'success'
    };
    return colores[prioridad] || 'secondary';
}

function getEstadoColor(estado) {
    const colores = {
        'Pendiente': 'primary',
        'En Proceso': 'warning',
        'Resuelta': 'success',
        'Cerrada': 'secondary'
    };
    return colores[estado] || 'light';
}

// Función para seleccionar todas las solicitudes pendientes
function seleccionarSolicitudesPendientes() {
    const filas = document.querySelectorAll('.solicitud-row');
    let seleccionadas = 0;
    
    filas.forEach(fila => {
        const solicitudId = fila.getAttribute('data-solicitud-id');
        const solicitud = solicitudesData.find(s => s.id == solicitudId);
        
        if (solicitud && solicitud.estado === 'Pendiente') {
            const checkbox = fila.querySelector('.solicitud-checkbox');
            checkbox.checked = true;
            seleccionadas++;
        }
    });
    
    // Actualizar checkbox principal
    document.getElementById('selectAll').checked = seleccionadas === filas.length;
    
    Swal.fire({
        icon: 'success',
        title: 'Solicitudes Seleccionadas',
        text: `Se seleccionaron ${seleccionadas} solicitudes pendientes`,
        confirmButtonText: 'Perfecto'
    });
}

// Función para limpiar toda la selección
function limpiarSeleccion() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.getElementById('selectAll').checked = false;
    
    Swal.fire({
        icon: 'info',
        title: 'Selección Limpiada',
        text: 'Se ha limpiado la selección de todas las solicitudes',
        confirmButtonText: 'Entendido'
    });
}

// Función para actualizar contador de seleccionadas
function actualizarContadorSeleccionadas() {
    const seleccionadas = document.querySelectorAll('.solicitud-checkbox:checked').length;
    const total = document.querySelectorAll('.solicitud-checkbox').length;
    
    // Resaltar visualmente las filas seleccionadas
    const filas = document.querySelectorAll('.solicitud-row');
    filas.forEach(fila => {
        const checkbox = fila.querySelector('.solicitud-checkbox');
        if (checkbox.checked) {
            fila.classList.add('selected');
        } else {
            fila.classList.remove('selected');
        }
    });
    
    if (seleccionadas > 0) {
        document.getElementById('contadorResultados').innerHTML = `
            <span class="badge bg-primary">${seleccionadas} seleccionada(s)</span>
            <span class="badge bg-secondary">${total} total</span>
        `;
    } else {
        document.getElementById('contadorResultados').textContent = `${total} solicitudes`;
    }
}

// Función de prueba para verificar selección
function probarSeleccion() {
    const totalCheckboxes = document.querySelectorAll('.solicitud-checkbox').length;
    const checkboxesSeleccionados = document.querySelectorAll('.solicitud-checkbox:checked').length;
    const selectAll = document.getElementById('selectAll');
    // Mostrar IDs de los seleccionados
    const seleccionados = Array.from(document.querySelectorAll('.solicitud-checkbox:checked')).map(cb => cb.value);
    // Mostrar en alerta
    Swal.fire({
        title: 'Información de Selección',
        html: `
            <div class="text-start">
                <p><strong>Total checkboxes:</strong> ${totalCheckboxes}</p>
                <p><strong>Seleccionados:</strong> ${checkboxesSeleccionados}</p>
                <p><strong>SelectAll:</strong> ${selectAll ? (selectAll.checked ? 'Marcado' : 'Desmarcado') : 'NO ENCONTRADO'}</p>
                <p><strong>IDs seleccionados:</strong> ${seleccionados.join(', ') || 'Ninguno'}</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}



// Agregar event listeners para los checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarContadorSeleccionadas);
    });
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            setTimeout(actualizarContadorSeleccionadas, 100);
        });
    }
});
</script>

<!-- Gráficos de Estadísticas -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Solicitudes por Estado
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('estadoSolicitudesChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="estadoSolicitudesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Solicitudes por Prioridad
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('prioridadSolicitudesChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="prioridadSolicitudesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Función para exportar gráficos
function exportarGrafico(chartId) {
    const canvas = document.getElementById(chartId);
    const url = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = url;
    a.download = `grafico_${chartId}_${new Date().toISOString().split('T')[0]}.png`;
    a.click();
}

// Inicializar gráficos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        inicializarGraficosSolicitudes();
    } else {
        console.error('Chart.js no está cargado');
    }
});

function inicializarGraficosSolicitudes() {
    // Datos reales desde el controlador
    const estadisticasEstado = <?= json_encode($estadisticas_estado ?? []) ?>;
    const estadisticasPrioridad = <?= json_encode($estadisticas_prioridad ?? []) ?>;
    
    // Procesar datos de estado
    const datosEstado = {
        labels: estadisticasEstado.map(item => item.estado),
        values: estadisticasEstado.map(item => parseInt(item.cantidad))
    };
    
    // Procesar datos de prioridad
    const datosPrioridad = {
        labels: estadisticasPrioridad.map(item => item.prioridad),
        values: estadisticasPrioridad.map(item => parseInt(item.cantidad))
    };
    
    // Debug
    // Fallback si no hay datos
    if (datosEstado.labels.length === 0) {
        datosEstado.labels = ['Sin datos'];
        datosEstado.values = [1];
    }
    
    if (datosPrioridad.labels.length === 0) {
        datosPrioridad.labels = ['Sin datos'];
        datosPrioridad.values = [1];
    }

    // 1. Gráfico de Estados
    new Chart(document.getElementById('estadoSolicitudesChart'), {
        type: 'doughnut',
        data: {
            labels: datosEstado.labels,
            datasets: [{
                data: datosEstado.values,
                backgroundColor: [
                    '#ffc107', // Pendiente - Amarillo
                    '#007bff', // En Proceso - Azul
                    '#28a745', // Resuelta - Verde
                    '#6c757d'  // Cerrada - Gris
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Distribución por Estado'
                }
            }
        }
    });

    // 2. Gráfico de Prioridades
    new Chart(document.getElementById('prioridadSolicitudesChart'), {
        type: 'bar',
        data: {
            labels: datosPrioridad.labels,
            datasets: [{
                label: 'Cantidad de Solicitudes',
                data: datosPrioridad.values,
                backgroundColor: [
                    '#dc3545', // Urgente - Rojo
                    '#fd7e14', // Alta - Naranja
                    '#ffc107', // Media - Amarillo
                    '#28a745'  // Baja - Verde
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Distribución por Prioridad'
                }
            }
        }
    });
}
</script>

<style>
.mini-stats-wid .card-body {
    padding: 1.25rem;
}

.avatar-title {
    align-items: center;
    background-color: #556ee6;
    border-radius: 50%;
    color: #fff;
    display: flex;
    font-size: 16px;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.solicitud-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.solicitud-row.selected {
    background-color: rgba(0, 123, 255, 0.1);
    border-left: 4px solid #007bff;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Estilos para checkboxes */
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Contador de seleccionadas */
#contadorResultados .badge {
    font-size: 0.8em;
    margin-right: 5px;
}

/* Botones de selección */
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-toolbar .btn-group .btn {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .mini-stats-wid {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
function verHistorialSolicitudes(estudianteId, nombreEstudiante) {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando Historial',
        text: 'Obteniendo historial de solicitudes del estudiante...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`<?= base_url('admin-bienestar/historial-solicitudes-estudiante') ?>/${estudianteId}`)
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            const solicitudes = data.solicitudes;
            const estadisticas = data.estadisticas;
            
            let historialHTML = '';
            
            if (solicitudes && solicitudes.length > 0) {
                historialHTML = `
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Respuestas</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${solicitudes.map(sol => `
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                ${new Date(sol.fecha_solicitud).toLocaleDateString('es-ES')}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">${sol.asunto_personalizado || sol.asunto}</div>
                                            <small class="text-muted">${sol.descripcion.substring(0, 50)}${sol.descripcion.length > 50 ? '...' : ''}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-${getEstadoColor(sol.estado)}">${sol.estado}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-${getPrioridadColor(sol.prioridad)}">${sol.prioridad}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">${sol.total_respuestas || 0}</span>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                historialHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Este estudiante no ha realizado solicitudes de ayuda
                    </div>
                `;
            }
            
            // Mostrar modal con historial
            Swal.fire({
                title: `<i class="bi bi-clock-history me-2"></i>Historial de Solicitudes`,
                html: `
                    <div class="text-start">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="bi bi-person me-2"></i>${nombreEstudiante}
                                </h6>
                            </div>
                        </div>
                        
                        ${estadisticas ? `
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-primary">${estadisticas.total_solicitudes}</div>
                                    <small class="text-muted">Total Solicitudes</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-success">${estadisticas.resueltas}</div>
                                    <small class="text-muted">Resueltas</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-warning">${estadisticas.pendientes}</div>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="h5 mb-0 text-info">${estadisticas.promedio_respuesta || 'N/A'}</div>
                                    <small class="text-muted">Promedio Respuesta (hrs)</small>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        <div class="mt-3">
                            <h6><i class="bi bi-list-ul me-2"></i>Detalle de Solicitudes</h6>
                            ${historialHTML}
                        </div>
                    </div>
                `,
                width: '800px',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#6c757d'
            });
        } else {
            mostrarNotificacion(data.error || 'Error cargando historial', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        mostrarNotificacion('Error cargando historial del estudiante', 'error');
    });
}
</script>

<?= $this->endSection() ?>
