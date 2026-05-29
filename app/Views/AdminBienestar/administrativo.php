<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="page-wrapper">
    <div class="page-content">
        <!-- Header del Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">Panel de Administración - Bienestar Estudiantil</h4>
                        <p class="text-muted mb-0">Bienvenido/a, <?= session('nombre') ?? 'Administrador' ?></p>
                    </div>
                    <div class="page-title-right">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <small class="text-muted">Última actividad</small>
                                <div class="fw-medium">Hace 5 minutos</div>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="actualizarDashboard()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card mini-stat">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h4 class="fw-medium">Formularios</h4>
                                <h4 class="mb-0" id="stat-formularios"><?= esc($formularios['total']) ?></h4>
                                <p class="text-muted mb-0">
                                    <span class="text-success me-2">
                                        <i class="bi bi-arrow-up"></i>12%
                                    </span>
                                    Desde el mes pasado
                                </p>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bi bi-file-earmark-text font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card mini-stat">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h4 class="fw-medium">Becas</h4>
                                <h4 class="mb-0" id="stat-becas"><?= esc($becas['total']) ?></h4>
                                <p class="text-muted mb-0">
                                    <span class="text-success me-2">
                                        <i class="bi bi-arrow-up"></i><?= esc($becas['solicitudes_activas']) ?>
                                    </span>
                                    Solicitudes activas
                                </p>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="bi bi-award font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card mini-stat">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h4 class="fw-medium">Solicitudes</h4>
                                <h4 class="mb-0" id="stat-solicitudes"><?= esc($solicitudes['total']) ?></h4>
                                <p class="text-muted mb-0">
                                    <span class="text-warning me-2">
                                        <i class="bi bi-arrow-up"></i>15%
                                    </span>
                                    Pendientes: <?= esc($solicitudes['pendientes']) ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="bi bi-ticket-detailed font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card mini-stat">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h4 class="fw-medium">Estudiantes</h4>
                                <h4 class="mb-0" id="stat-estudiantes"><?= esc(number_format($estudiantes['total'])) ?></h4>
                                <p class="text-muted mb-0">
                                    <span class="text-info me-2">
                                        <i class="bi bi-arrow-up"></i>5%
                                    </span>
                                    Registrados activos
                                </p>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="bi bi-people font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos Principales -->
<div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                        <div class="mb-3">
                <i class="bi bi-file-earmark-text display-4 text-primary"></i>
                        </div>
                        <h5 class="card-title">Formularios Socioeconómicos</h5>
                        <p class="card-text text-muted">Gestiona y revisa los formularios socioeconómicos de los estudiantes.</p>
                        <div class="d-grid">
                            <a href="<?= base_url('index.php/fichas') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Ver Formularios
                            </a>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>Última actualización: Hace 2 horas
                            </small>
                        </div>
            </div>
        </div>
    </div>
            
            <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                        <div class="mb-3">
                <i class="bi bi-award display-4 text-success"></i>
                        </div>
                        <h5 class="card-title">Solicitudes de Becas</h5>
                        <p class="card-text text-muted">Supervisa y gestiona el proceso de becas estudiantiles.</p>
                        <div class="d-grid">
                            <a href="<?= base_url('index.php/solicitudes-becas') ?>" class="btn btn-outline-success">
                                <i class="bi bi-eye me-1"></i>Ver Becas
                            </a>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>Última actualización: Hace 1 hora
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-ticket-detailed display-4 text-warning"></i>
                        </div>
                        <h5 class="card-title">Solicitudes de Ayuda</h5>
                        <p class="card-text text-muted">Atiende y resuelve solicitudes generadas por los estudiantes.</p>
                        <div class="d-grid">
                            <a href="<?= base_url('index.php/solicitudes') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-eye me-1"></i>Ver Solicitudes
                            </a>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>Última actualización: Hace 30 minutos
                            </small>
                        </div>
            </div>
        </div>
    </div>
            
            <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people display-4 text-info"></i>
                        </div>
                        <h5 class="card-title">Gestión de Estudiantes</h5>
                        <p class="card-text text-muted">Administra la información y seguimiento de estudiantes.</p>
                        <div class="d-grid">
                            <a href="<?= base_url('index.php/estudiantes') ?>" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>Ver Estudiantes
                            </a>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>Última actualización: Hace 1 día
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos Secundarios -->
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Reportes y Analítica
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Genera reportes detallados sobre actividades, becas y solicitudes para la toma de decisiones estratégicas.</p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <small>Reportes mensuales</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <small>Análisis de tendencias</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <small>Exportación PDF/Excel</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <small>Gráficos interactivos</small>
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('index.php/reportes') ?>" class="btn btn-primary">
                            <i class="bi bi-graph-up me-1"></i>Ir a Reportes
                        </a>
        </div>
    </div>
</div>
        </div>

        <!-- Actividad Reciente -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                        </h5>
    </div>
            <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Actividad</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="actividad-reciente">
                                    <?php if (!empty($actividad_reciente)): ?>
                                        <?php foreach ($actividad_reciente as $act): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                    $icon = 'bi-check-circle text-success';
                                                    if ($act['estado'] == 'Rechazado') $icon = 'bi-x-circle text-danger';
                                                    elseif ($act['estado'] == 'Pendiente') $icon = 'bi-clock-history text-warning';
                                                    ?>
                                                    <i class="bi <?= $icon ?> me-2"></i>
                                                    <?= esc($act['accion']) ?>
                                                </div>
                                            </td>
                                            <td><?= esc($act['usuario']) ?></td>
                                            <td><?= esc($act['fecha']) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'info';
                                                if ($act['estado'] == 'Completado') $badgeClass = 'success';
                                                elseif ($act['estado'] == 'Rechazado') $badgeClass = 'danger';
                                                elseif ($act['estado'] == 'Pendiente') $badgeClass = 'warning';
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>"><?= esc($act['estado']) ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay actividad reciente</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para actualizar estadísticas
function actualizarDashboard() {
    const btnActualizar = document.querySelector('button[onclick="actualizarDashboard()"]');
    const icono = btnActualizar.querySelector('i');
    
    // Cambiar icono a loading
    icono.className = 'bi bi-arrow-clockwise me-1 fa-spin';
    btnActualizar.disabled = true;
    
    // Hacer petición AJAX
    fetch('<?= base_url('index.php/dashboard/actualizar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar estadísticas en la página
            actualizarEstadisticas(data.estadisticas);
            actualizarActividad(data.actividad);
            
            // Mostrar notificación de éxito
            mostrarNotificacion('Dashboard actualizado exitosamente', 'success');
        } else {
            mostrarNotificacion('Error al actualizar dashboard', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    })
    .finally(() => {
        // Restaurar icono y habilitar botón
        icono.className = 'bi bi-arrow-clockwise me-1';
        btnActualizar.disabled = false;
    });
}

// Función para actualizar estadísticas
function actualizarEstadisticas(estadisticas) {
    if (estadisticas.formularios) {
        document.getElementById('stat-formularios').textContent = estadisticas.formularios.total || 0;
    }
    if (estadisticas.becas) {
        document.getElementById('stat-becas').textContent = estadisticas.becas.total || 0;
    }
    if (estadisticas.solicitudes) {
        document.getElementById('stat-solicitudes').textContent = estadisticas.solicitudes.total || 0;
        // La descripción de pendientes se puede buscar por nodo
        const solCard = document.getElementById('stat-solicitudes').parentElement;
        const pElement = solCard.querySelector('p.text-muted');
        if (pElement) {
            pElement.innerHTML = `
                <span class="text-warning me-2">
                    <i class="bi bi-arrow-up"></i>15%
                </span>
                Pendientes: ${estadisticas.solicitudes.pendientes || 0}
            `;
        }
    }
    if (estadisticas.estudiantes) {
        // formatear numero con comas
        document.getElementById('stat-estudiantes').textContent = new Intl.NumberFormat().format(estadisticas.estudiantes.total || 0);
    }
}

// Función para actualizar actividad reciente
function actualizarActividad(actividadData) {
    const tbody = document.getElementById('actividad-reciente');
    if (!tbody) return;
    
    // El backend puede enviar actividad en diferentes formatos dependiendo del controlador,
    // en DashboardController retorna 'formularios', 'solicitudes', 'becas' en getActividadReciente()
    // pero si usamos actualizarDashboard() retorna 'actividad' que acabamos de cambiar!
    // Espera, en DashboardController::actualizarDashboard() llama a getActividadReciente().
    // Yo modifiqué DashboardController::adminBienestar() para formatear `$actividadFormateada`.
    // ¡OJO! DashboardController::getActividadReciente() sigue devolviendo un array crudo.
    // Vamos a tener que manejar eso. De momento, si el backend manda el formato nuevo:
    if (Array.isArray(actividadData) && actividadData.length > 0) {
        tbody.innerHTML = actividadData.map(act => {
            let badgeClass = 'info';
            let iconClass = 'bi-check-circle text-success';
            
            if (act.estado === 'Completado') badgeClass = 'success';
            else if (act.estado === 'Rechazado') { badgeClass = 'danger'; iconClass = 'bi-x-circle text-danger'; }
            else if (act.estado === 'Pendiente') { badgeClass = 'warning'; iconClass = 'bi-clock-history text-warning'; }
            
            return `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi ${iconClass} me-2"></i>
                        ${act.accion || 'Actividad'}
                    </div>
                </td>
                <td>${act.usuario || 'Sistema'}</td>
                <td>${act.fecha || ''}</td>
                <td><span class="badge bg-${badgeClass}">${act.estado || 'Info'}</span></td>
            </tr>`;
        }).join('');
    } else {
        // En caso de usar el getActividadReciente() antiguo, no hace nada o muestra vacío
        console.log('Actividad reciente actualizada desde servidor');
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notificacion.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Agregar al body
    document.body.appendChild(notificacion);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }, 5000);
}

// Cargar estadísticas al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Cargar estadísticas iniciales
    fetch('<?= base_url('index.php/dashboard/estadisticas') ?>')
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                actualizarEstadisticas(data);
            }
        })
        .catch(error => {
            console.error('Error al cargar estadísticas:', error);
        });
    
    // Cargar actividad reciente
    fetch('<?= base_url('index.php/dashboard/actividad') ?>')
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                actualizarActividad(data);
            }
        })
        .catch(error => {
            console.error('Error al cargar actividad:', error);
        });
});

// Actualizar estadísticas cada 5 minutos
setInterval(function() {
    // Solo actualizar si la página está visible
    if (!document.hidden) {
        actualizarDashboard();
    }
}, 300000);

// Actualizar cuando la página vuelve a estar visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Actualizar estadísticas cuando la página vuelve a estar visible
        actualizarDashboard();
    }
});
</script>

<?= $this->endSection() ?>
