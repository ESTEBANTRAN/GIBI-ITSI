<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Gestión de Respaldos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Respaldos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Respaldos -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Respaldos</h6>
                                <h3 class="mb-0" id="totalRespaldos">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-database fs-1"></i>
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
                                <h6 class="card-title">Último Respaldo</h6>
                                <h3 class="mb-0" id="ultimoRespaldo">Hoy</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock-history fs-1"></i>
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
                                <h6 class="card-title">Tamaño Total</h6>
                                <h3 class="mb-0" id="tamañoTotal">0 MB</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-hdd fs-1"></i>
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
                                <h6 class="card-title">Estado</h6>
                                <h3 class="mb-0" id="estadoRespaldo">Activo</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-2">
                                 <button type="button" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" onclick="crearRespaldo()">
                                     <i class="bi bi-database-add mb-2" style="font-size: 2rem;"></i>
                                     <span class="text-center">Crear Respaldo</span>
                                 </button>
                             </div>

                             <div class="col-md-2">
                                 <button type="button" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" onclick="configurarRespaldos()">
                                     <i class="bi bi-gear mb-2" style="font-size: 2rem;"></i>
                                     <span class="text-center">Configurar</span>
                                 </button>
                             </div>
                             <div class="col-md-2">
                                 <button type="button" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" onclick="verRespaldosServidor()">
                                     <i class="bi bi-server mb-2" style="font-size: 2rem;"></i>
                                     <span class="text-center">Ver en Servidor</span>
                                 </button>
                             </div>
                             <div class="col-md-2">
                                 <button type="button" class="btn text-white w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" style="background-color: #0F9D58;" onclick="configurarDriveMirror()">
                                     <i class="bi bi-google mb-2" style="font-size: 2rem;"></i>
                                     <span class="text-center">Drive Mirror</span>
                                 </button>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Respaldos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>Respaldos Disponibles
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="limpiarRespaldos()">
                                <i class="bi bi-trash me-1"></i>Limpiar Antiguos
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="actualizarLista()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaRespaldos">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Fecha</th>
                                        <th>Tamaño</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyRespaldos">
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Info -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="text-muted" id="infoPaginacionRespaldos">
                                    Mostrando 0 a 0 de 0 registros
                                </div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Navegación de respaldos">
                                    <ul class="pagination justify-content-end mb-0" id="paginacionRespaldos">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Configuración -->
<div class="modal fade" id="configuracionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configuración de Respaldos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="configuracionRespaldosForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="frecuenciaRespaldo" class="form-label">Frecuencia de Respaldo</label>
                                <select class="form-select" id="frecuenciaRespaldo" name="frecuencia">
                                    <option value="diario">Diario</option>
                                    <option value="semanal" selected>Semanal</option>
                                    <option value="mensual">Mensual</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="retenerDias" class="form-label">Retener Respaldos (días)</label>
                                <input type="number" class="form-control" id="retenerDias" name="retener_dias" value="30" min="7" max="365">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="respaldoAutomatico" name="automatico" checked>
                                    <label class="form-check-label" for="respaldoAutomatico">
                                        Respaldo Automático
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="comprimirRespaldo" name="comprimir" checked>
                                    <label class="form-check-label" for="comprimirRespaldo">
                                        Comprimir Respaldos
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Retrasar 200ms para asegurar que el DOM y dependencias estén 100% listas
    setTimeout(function() {
        cargarRespaldos();
        cargarEstadisticas();
    }, 200);
});

var paginaActualRespaldos = 1;

function cargarRespaldos(pagina) {
    if (pagina !== undefined) {
        paginaActualRespaldos = pagina;
    }
    $.ajax({
        url: '<?= base_url('global-admin/obtener-respaldos') ?>',
        type: 'GET',
        data: { pagina: paginaActualRespaldos },
        cache: false,
        success: function(response) {
            if (response.success) {
                mostrarRespaldos(response.respaldos, response.paginacion);
            } else {
            }
        },
        error: function() {
        }
    });
}

function mostrarRespaldos(respaldos, paginacion) {
    const tbody = $('#tbodyRespaldos');
    tbody.empty();
    
    if (!respaldos || respaldos.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="bi bi-database me-2"></i>No hay respaldos disponibles
                </td>
            </tr>
        `);
        $('#infoPaginacionRespaldos').text('Mostrando 0 a 0 de 0 registros');
        $('#paginacionRespaldos').empty();
        return;
    }
    
    respaldos.forEach(respaldo => {
        const row = `
            <tr>
                <td>${respaldo.nombre_archivo}</td>
                <td>${respaldo.fecha_formateada}</td>
                <td>${respaldo.tamaño_formateado}</td>
                <td><span class="badge bg-primary">${respaldo.tipo}</span></td>
                <td><span class="badge bg-success">${respaldo.estado}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="descargarRespaldo(${respaldo.id})" title="Descargar">
                            <i class="bi bi-download"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="mostrarModalEnvioEmail(${respaldo.id}, '${respaldo.nombre_archivo}')" title="Enviar por Email">
                            <i class="bi bi-envelope"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="restaurarRespaldo(${respaldo.id})" title="Restaurar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="eliminarRespaldo(${respaldo.id})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Udpate pagination info
    const desde = ((paginacion.pagina_actual - 1) * paginacion.por_pagina) + 1;
    const hasta = Math.min(paginacion.pagina_actual * paginacion.por_pagina, paginacion.total);
    $('#infoPaginacionRespaldos').text('Mostrando ' + desde + ' a ' + hasta + ' de ' + paginacion.total + ' registros');
    
    // Render pagination controls
    mostrarPaginacionRespaldos(paginacion);
}

function mostrarPaginacionRespaldos(paginacion) {
    const nav = $('#paginacionRespaldos');
    nav.empty();
    
    if (paginacion.total_paginas <= 1) return;
    
    // Previous button
    nav.append(`
        <li class="page-item ${paginacion.pagina_actual <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPaginaRespaldos(${paginacion.pagina_actual - 1}); return false;">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
    `);
    
    for (var i = 1; i <= paginacion.total_paginas; i++) {
        nav.append(`
            <li class="page-item ${i === paginacion.pagina_actual ? 'active' : ''}">
                <a class="page-link" href="#" onclick="cambiarPaginaRespaldos(${i}); return false;">${i}</a>
            </li>
        `);
    }
    
    // Next button
    nav.append(`
        <li class="page-item ${paginacion.pagina_actual >= paginacion.total_paginas ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPaginaRespaldos(${paginacion.pagina_actual + 1}); return false;">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function cambiarPaginaRespaldos(pagina) {
    if (pagina < 1) return;
    paginaActualRespaldos = pagina;
    cargarRespaldos(pagina);
}

function cargarEstadisticas() {
    $.ajax({
        url: '<?= base_url('global-admin/estadisticas-respaldos') ?>',
        type: 'GET',
        cache: false,
        success: function(response) {
            if (response.success) {
                $('#totalRespaldos').text(response.estadisticas.total);
                $('#ultimoRespaldo').text(response.estadisticas.ultimo);
                $('#tamañoTotal').text(formatearBytes(response.estadisticas.tamaño_total));
                $('#estadoRespaldo').text(response.estadisticas.estado);
            }
        },
        error: function() {
        }
    });
}

function crearRespaldo() {
    Swal.fire({
        title: 'Crear Respaldo',
        text: '¿Estás seguro de que quieres crear un nuevo respaldo de la base de datos?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear respaldo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Creando Respaldo',
                text: 'Por favor espera mientras se crea el respaldo de la base de datos...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('global-admin/crear-respaldo') ?>',
                type: 'POST',
                success: function(response) {
                                         if (response.success) {
                         // Mostrar opciones después de crear el respaldo
                         Swal.fire({
                             title: '¡Respaldo Creado!',
                             text: response.message,
                             icon: 'success',
                             showCancelButton: true,
                             showDenyButton: true,
                             confirmButtonText: 'Descargar Copia',
                             denyButtonText: 'Enviar por Email',
                             cancelButtonText: 'Solo Guardar'
                         }).then((result) => {
                             if (result.isConfirmed) {
                                 // Descargar copia adicional
                                 descargarArchivoConDialogo(response.download_url, response.filename);
                             } else if (result.isDenied) {
                                 // Enviar por email
                                 mostrarModalEnvioEmail(response.respaldo_id, response.filename);
                             }
                         });
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al crear el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function restaurarRespaldo(id = null) {
    if (!id) {
        Swal.fire('Error', 'Selecciona un respaldo para restaurar.', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Restaurar Respaldo',
        text: '¿Estás seguro de que quieres restaurar este respaldo? Esta acción sobrescribirá los datos actuales.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('global-admin/restaurar-respaldo') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Restaurado!',
                            'El respaldo se ha restaurado exitosamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al restaurar el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function descargarRespaldo(id = null) {
    if (id) {
        Swal.fire({
            title: 'Descargar Respaldo',
            text: '¿Cómo deseas obtener el respaldo?',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Descargar',
            denyButtonText: 'Enviar por Email',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                descargarArchivoConDialogo('<?= base_url('global-admin/descargar-respaldo') ?>/' + id);
            } else if (result.isDenied) {
                mostrarModalEnvioEmail(id);
            }
        });
    } else {
        Swal.fire('Error', 'Selecciona un respaldo para descargar.', 'error');
    }
}

function eliminarRespaldo(id) {
    Swal.fire({
        title: 'Eliminar Respaldo',
        text: '¿Estás seguro de que quieres eliminar este respaldo? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('global-admin/eliminar-respaldo') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El respaldo se ha eliminado exitosamente.',
                            'success'
                        );
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al eliminar el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function configurarRespaldos() {
    $('#configuracionModal').modal('show');
}

function guardarConfiguracion() {
    const formData = new FormData($('#configuracionRespaldosForm')[0]);
    
    $.ajax({
        url: '<?= base_url('global-admin/guardar-configuracion-respaldos') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire(
                    '¡Configuración Guardada!',
                    'La configuración de respaldos se ha actualizado.',
                    'success'
                );
                $('#configuracionModal').modal('hide');
            } else {
                Swal.fire(
                    'Error',
                    response.error || 'Error al guardar la configuración.',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Error de Conexión',
                'No se pudo conectar con el servidor.',
                'error'
            );
        }
    });
}

function limpiarRespaldos() {
    Swal.fire({
        title: 'Limpiar Respaldos Antiguos',
        text: '¿Estás seguro de que quieres eliminar los respaldos antiguos? Esta acción eliminará únicamente las copias con más de 30 días de antigüedad.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('global-admin/limpiar-respaldos') ?>',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Limpieza Completada!',
                            'Los respaldos antiguos han sido eliminados.',
                            'success'
                        );
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al limpiar respaldos.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function actualizarLista() {
    cargarRespaldos();
    cargarEstadisticas();
}

function formatearBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function descargarArchivoConDialogo(url, filename = '') {
    // Mostrar mensaje informativo
    Swal.fire({
        title: 'Descargando Respaldo',
        text: 'Se abrirá el diálogo para guardar el archivo. Elige la ubicación donde quieres guardar la copia.',
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'Entendido'
    }).then(() => {
        // Trigger download directly using window.location.href
        window.location.href = url;
        
        // Mostrar confirmación de descarga iniciada
        Swal.fire({
            title: '¡Descarga Iniciada!',
            text: 'El archivo se está descargando. Revisa tu carpeta de descargas.',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

function verRespaldosServidor() {
    Swal.fire({
        title: 'Respaldos en el Servidor',
        html: `
            <div class="text-start">
                <p><strong>Ubicación:</strong> <code>writable/backups/</code></p>
                <p><strong>Estado:</strong> Todos los respaldos se guardan automáticamente en el servidor.</p>
                <p><strong>Ventajas:</strong></p>
                <ul class="text-start">
                    <li>✓ Seguridad: Respaldos protegidos en el servidor</li>
                    <li>✓ Accesibilidad: Disponibles desde cualquier lugar</li>
                    <li>✓ Historial: Mantiene todos los respaldos creados</li>
                    <li>✓ Restauración: Puedes restaurar desde el servidor</li>
                </ul>
                <p class="mt-3"><strong>Nota:</strong> Los respaldos se pueden descargar individualmente usando los botones de la tabla.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Entendido',
        width: '600px'
    });
}

function configurarDriveMirror() {
    Swal.fire({
        title: 'Google Drive Mirroring',
        html: `
            <div class="text-start">
                <p>Los respaldos del sistema están configurados para hacer un "Mirror" (espejo) obligatorio hacia la nube.</p>
                <p>Cada vez que se genere un respaldo local, se subirá automáticamente a la subcarpeta <strong>backups</strong> dentro del Google Drive institucional configurado.</p>
                <p>Si la integración está apagada, solo se guardarán localmente.</p>
            </div>
        `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Ir a Configuración',
        cancelButtonText: 'Entendido'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('global-admin/configuracion') ?>';
        }
    });
}

function mostrarModalEnvioEmail(respaldoId, filename = '') {
    Swal.fire({
        title: 'Enviar Respaldo por Email',
        html: `
            <div class="form-group">
                <label for="emailDestino" class="form-label">Correo Electrónico:</label>
                <input type="email" id="emailDestino" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>
            ${filename ? '<p class="mt-2"><strong>Archivo:</strong> ' + filename + '</p>' : ''}
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const email = document.getElementById('emailDestino').value;
            if (!email) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico válido');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico válido');
                return false;
            }
            return email;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const email = result.value;
            
            // Mostrar loading
            Swal.fire({
                title: 'Enviando Email',
                text: 'Por favor espera mientras se envía el respaldo...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '<?= base_url('global-admin/enviar-respaldo-email') ?>',
                type: 'POST',
                data: {
                    respaldo_id: respaldoId,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Email Enviado!',
                            response.message,
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al enviar el email.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?> 
