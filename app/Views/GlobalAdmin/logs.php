<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Logs del Sistema</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Logs</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="filtroNivel" class="form-label">Nivel</label>
                                <select class="form-select" id="filtroNivel">
                                    <option value="">Todos los niveles</option>
                                    <option value="ERROR">Error</option>
                                    <option value="WARNING">Warning</option>
                                    <option value="INFO">Info</option>
                                    <option value="DEBUG">Debug</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroFecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="filtroFecha">
                            </div>
                            <div class="col-md-3">
                                <label for="filtroUsuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="filtroUsuario" placeholder="Buscar por usuario">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary me-2" onclick="aplicarFiltros()">
                                    <i class="bi bi-search me-1"></i>Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                    <i class="bi bi-x-circle me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Logs -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Errores</h6>
                                <h3 class="mb-0" id="totalErrores">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Warnings</h6>
                                <h3 class="mb-0" id="totalWarnings">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Info</h6>
                                <h3 class="mb-0" id="totalInfo">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-info-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total</h6>
                                <h3 class="mb-0" id="totalLogs">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-list-ul fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Actividad -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Actividad de Logs (Últimas 24 horas)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartLogs" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Logs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>Registros de Logs
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="limpiarLogs()">
                                <i class="bi bi-trash me-1"></i>Limpiar Logs
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="exportarLogs()">
                                <i class="bi bi-download me-1"></i>Exportar
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="actualizarLogs()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaLogs">
                                <thead>
                                    <tr>
                                        <th>Fecha/Hora</th>
                                        <th>Nivel</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Mensaje</th>
                                        <th>IP</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyLogs">
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación -->
                        <nav aria-label="Paginación de logs">
                            <ul class="pagination justify-content-center" id="paginacionLogs">
                                <!-- Paginación se generará dinámicamente -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-folder2-open me-2"></i>Archivos de Log (writable/logs)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha Modificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyLogArchivos">
                            <!-- Se llenará por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    cargarLogs();
    cargarEstadisticas();
    inicializarGrafico();
    cargarArchivosLog();
});

function cargarLogs(pagina = 1) {
    const filtros = {
        nivel: $('#filtroNivel').val(),
        fecha: $('#filtroFecha').val(),
        usuario: $('#filtroUsuario').val(),
        pagina: pagina
    };
    
    $.ajax({
        url: '<?= base_url('global-admin/obtener-logs') ?>',
        type: 'GET',
        data: filtros,
        success: function(response) {
            if (response.success) {
                mostrarLogs(response.logs);
                mostrarPaginacion(response.paginacion);
            } else {
            }
        },
        error: function() {
        }
    });
}

function mostrarLogs(logs) {
    const tbody = $('#tbodyLogs');
    tbody.empty();
    
    if (logs.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="bi bi-file-text me-2"></i>No hay logs disponibles
                </td>
            </tr>
        `);
        return;
    }
    
    logs.forEach(log => {
        const nivelClass = getNivelClass(log.nivel);
        const row = `
            <tr>
                <td>${formatearFecha(log.fecha)}</td>
                <td><span class="badge ${nivelClass}">${log.nivel}</span></td>
                <td>${log.usuario || 'Sistema'}</td>
                <td>${log.accion}</td>
                <td>
                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${log.mensaje}">
                        ${log.mensaje}
                    </span>
                </td>
                <td>${log.ip || 'N/A'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info" onclick="verDetalleLog(${log.id})" title="Ver Detalle">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="eliminarLog(${log.id})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function mostrarPaginacion(paginacion) {
    const ul = $('#paginacionLogs');
    ul.empty();
    
    if (paginacion.total_paginas <= 1) return;
    
    // Botón anterior
    if (paginacion.pagina_actual > 1) {
        ul.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="cargarLogs(${paginacion.pagina_actual - 1})">Anterior</a>
            </li>
        `);
    }
    
    // Páginas
    for (let i = 1; i <= paginacion.total_paginas; i++) {
        const active = i === paginacion.pagina_actual ? 'active' : '';
        ul.append(`
            <li class="page-item ${active}">
                <a class="page-link" href="#" onclick="cargarLogs(${i})">${i}</a>
            </li>
        `);
    }
    
    // Botón siguiente
    if (paginacion.pagina_actual < paginacion.total_paginas) {
        ul.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="cargarLogs(${paginacion.pagina_actual + 1})">Siguiente</a>
            </li>
        `);
    }
}

function cargarEstadisticas() {
    $.ajax({
        url: '<?= base_url('global-admin/estadisticas-logs') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalErrores').text(response.estadisticas.errores);
                $('#totalWarnings').text(response.estadisticas.warnings);
                $('#totalInfo').text(response.estadisticas.info);
                $('#totalLogs').text(response.estadisticas.total);
            }
        },
        error: function() {
        }
    });
}

function inicializarGrafico() {
    const ctx = document.getElementById('chartLogs').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            datasets: [{
                label: 'Errores',
                data: [5, 3, 8, 12, 6, 4, 2],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'Warnings',
                data: [12, 8, 15, 20, 18, 14, 10],
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            }, {
                label: 'Info',
                data: [25, 30, 35, 40, 38, 32, 28],
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function aplicarFiltros() {
    cargarLogs(1);
}

function limpiarFiltros() {
    $('#filtroNivel').val('');
    $('#filtroFecha').val('');
    $('#filtroUsuario').val('');
    cargarLogs(1);
}

function verDetalleLog(id) {
    $.ajax({
        url: '<?= base_url('global-admin/obtener-log') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarDetalleLog(response.log);
            } else {
                Swal.fire('Error', response.error || 'Error al obtener detalles del log.', 'error');
            }
        },
        error: function() {
            Swal.fire('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
        }
    });
}

function mostrarDetalleLog(log) {
    Swal.fire({
        title: 'Detalle del Log',
        html: `
            <div class="text-start">
                <p><strong>Fecha:</strong> ${formatearFecha(log.fecha)}</p>
                <p><strong>Nivel:</strong> <span class="badge ${getNivelClass(log.nivel)}">${log.nivel}</span></p>
                <p><strong>Usuario:</strong> ${log.usuario || 'Sistema'}</p>
                <p><strong>Acción:</strong> ${log.accion}</p>
                <p><strong>IP:</strong> ${log.ip || 'N/A'}</p>
                <p><strong>Mensaje:</strong></p>
                <div class="alert alert-info">${log.mensaje}</div>
                ${log.detalles ? `<p><strong>Detalles:</strong></p><div class="alert alert-secondary">${log.detalles}</div>` : ''}
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Cerrar'
    });
}

function eliminarLog(id) {
    Swal.fire({
        title: 'Eliminar Log',
        text: '¿Estás seguro de que quieres eliminar este registro de log?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('global-admin/eliminar-log') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Eliminado!', 'El log se ha eliminado exitosamente.', 'success');
                        cargarLogs();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.error || 'Error al eliminar el log.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}

function limpiarLogs() {
    Swal.fire({
        title: 'Limpiar Logs',
        text: '¿Estás seguro de que quieres eliminar todos los logs antiguos?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('global-admin/limpiar-logs') ?>',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Limpieza Completada!', 'Los logs antiguos han sido eliminados.', 'success');
                        cargarLogs();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.error || 'Error al limpiar logs.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}

function exportarLogs() {
    const filtros = {
        nivel: $('#filtroNivel').val(),
        fecha: $('#filtroFecha').val(),
        usuario: $('#filtroUsuario').val()
    };
    
    // Crear URL con filtros
    const params = new URLSearchParams(filtros);
    window.open('<?= base_url('global-admin/exportar-logs') ?>?' + params.toString(), '_blank');
}

function actualizarLogs() {
    cargarLogs();
    cargarEstadisticas();
}

function getNivelClass(nivel) {
    switch (nivel) {
        case 'ERROR': return 'bg-danger';
        case 'WARNING': return 'bg-warning';
        case 'INFO': return 'bg-info';
        case 'DEBUG': return 'bg-secondary';
        default: return 'bg-primary';
    }
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleString('es-EC');
}

function cargarArchivosLog() {
    $.ajax({
        url: '<?= base_url('global-admin/obtener-logs') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success && response.logs_archivos) {
                mostrarArchivosLog(response.logs_archivos);
            }
        }
    });
}

function mostrarArchivosLog(archivos) {
    const tbody = $('#tbodyLogArchivos');
    tbody.empty();
    if (!archivos.length) {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No hay archivos de log</td></tr>');
        return;
    }
    archivos.forEach(file => {
        tbody.append(`
            <tr>
                <td>${file.nombre}</td>
                <td>${file.tamaño}</td>
                <td>${file.fecha_modificacion}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="descargarLog('${encodeURIComponent(file.nombre)}')">
                        <i class="bi bi-download"></i> Descargar
                    </button>
                </td>
            </tr>
        `);
    });
}

function descargarLog(nombre) {
    window.open('<?= base_url('global-admin/exportar-logs') ?>?archivo=' + nombre, '_blank');
}
</script>
<?= $this->endSection() ?> 
