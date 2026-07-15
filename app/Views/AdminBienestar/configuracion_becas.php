<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Configurar Programas de Becas</h4>
                <p class="text-muted mb-0">Gestiona y configura los programas de becas disponibles</p>
            </div>
            <div class="page-title-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaBeca">
                    <i class="bi bi-plus-circle"></i> Nueva Beca
                </button>
                <button type="button" class="btn btn-info" onclick="exportarBecas()">
                    <i class="bi bi-file-excel"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-award fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= count($becas ?? []) ?></h3>
                        <p class="mb-0">Total Becas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-check-circle fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($becas ?? [], fn($b) => ($b['activa'] ?? 0) == 1)) ?></h3>
                        <p class="mb-0">Activas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-pause-circle fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($becas ?? [], fn($b) => ($b['activa'] ?? 0) == 0)) ?></h3>
                        <p class="mb-0">Inactivas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-people fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= array_sum(array_map(fn($b) => $b['cupos_disponibles'] ?? 0, $becas ?? [])) ?></h3>
                        <p class="mb-0">Cupos Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Info -->
<?php if (ENVIRONMENT === 'development'): ?>
<div class="alert alert-info">
    <strong>Debug:</strong> 
    Becas cargadas: <?= count($becas ?? []) ?> | 
    Períodos: <?= count($periodos ?? []) ?> | 
    Tipos: <?= count($tipos_beca ?? []) ?>
</div>
<?php endif; ?>

<!-- Tabla de Becas -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Programas de Becas Disponibles</h5>
        <span class="badge bg-primary fs-6"><?= count($becas ?? []) ?> becas</span>
    </div>
    <div class="card-body">
        <?php if (empty($becas)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No se encontraron becas</h5>
            <p class="text-muted">No hay programas de becas configurados</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaBeca">
                <i class="bi bi-plus-circle"></i> Crear Primera Beca
            </button>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Cupos</th>
                        <th>Monto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($becas as $beca): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary">#<?= $beca['id'] ?? 0 ?></span>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0"><?= esc($beca['nombre'] ?? '') ?></h6>
                                <small class="text-muted"><?= esc(substr($beca['descripcion'] ?? '', 0, 50)) ?>...</small>
                            </div>
                        </td>
                        <td>
                            <?php
                            $tipoClass = 'bg-secondary';
                            $tipoText = $beca['tipo_beca'] ?? 'Sin tipo';
                            switch($beca['tipo_beca'] ?? '') {
                                case 'Académica': $tipoClass = 'bg-primary'; break;
                                case 'Socioeconómica': 
                                case 'Económica': $tipoClass = 'bg-success'; break;
                                case 'Deportiva': $tipoClass = 'bg-warning'; break;
                                case 'Cultural': $tipoClass = 'bg-info'; break;
                                case 'Investigación': $tipoClass = 'bg-purple'; break;
                                case 'Otros': $tipoClass = 'bg-dark'; break;
                            }
                            ?>
                            <span class="badge <?= $tipoClass ?>"><?= esc($tipoText) ?></span>
                        </td>
                        <td>
                            <?php if (($beca['activa'] ?? 0) == 1): ?>
                                <span class="badge bg-success">Activa</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($beca['cupos_disponibles'])): ?>
                                <span class="badge bg-info"><?= esc($beca['cupos_disponibles']) ?> cupos</span>
                            <?php else: ?>
                                <span class="text-muted">Sin límite</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($beca['monto_beca'])): ?>
                                <strong>$<?= number_format($beca['monto_beca'], 2) ?></strong>
                            <?php else: ?>
                                <span class="text-muted">No especificado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verBeca(<?= $beca['id'] ?? 0 ?>)" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="editarBeca(<?= $beca['id'] ?? 0 ?>)" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="toggleEstadoBeca(<?= $beca['id'] ?? 0 ?>, <?= ($beca['activa'] ?? 0) ?>)" 
                                        title="<?= ($beca['activa'] ?? 0) ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-<?= ($beca['activa'] ?? 0) ? 'pause' : 'play' ?>"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarBeca(<?= $beca['id'] ?? 0 ?>)" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Gráficos de Información -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Distribución por Estado
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('estadoChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="estadoChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Becas por Tipo
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('tipoChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="tipoChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Montos de Becas
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('montoChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="montoChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Disponibilidad de Cupos
                </h6>
                <button class="btn btn-outline-primary btn-sm" onclick="exportarGrafico('cuposChart')">
                    <i class="bi bi-download me-1"></i>Exportar PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="cuposChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let becas = <?= json_encode($becas ?? []) ?>;
let periodos = <?= json_encode($periodos ?? []) ?>;
let carreras = <?= json_encode($carreras ?? []) ?>;

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    Swal.fire({
        title: tipo === 'success' ? 'Éxito' : 'Error',
        text: mensaje,
        icon: tipo,
        timer: tipo === 'success' ? 2000 : 4000,
        showConfirmButton: false
    });
}

// Función para ver detalles de una beca
function verBeca(id) {
    const beca = becas.find(b => b.id == id);
    if (!beca) {
        mostrarNotificacion('Beca no encontrada', 'error');
        return;
    }

    // Cargar detalles de la beca
    fetch(`<?= base_url('admin-bienestar/obtener-beca') ?>/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetallesBeca(data.beca);
            } else {
                mostrarNotificacion(data.error || 'Error cargando detalles', 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion('Error de conexión', 'error');
        });
}

// Función para mostrar detalles de beca en modal
function mostrarDetallesBeca(beca) {
            const carrerasText = 'Todas las carreras'; // Campo no disponible en la tabla actual
    
    const documentosText = beca.documentos_requisitos && beca.documentos_requisitos.length > 0 ? 
        beca.documentos_requisitos.join(', ') : 'No especificados';
    
    const periodoText = beca.periodo_vigente_id ? 
        periodos.find(p => p.id == beca.periodo_vigente_id)?.nombre || 'Período no encontrado' : 'No asignado';
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Información General</h6>
                <p><strong>ID:</strong> #${beca.id}</p>
                <p><strong>Nombre:</strong> ${beca.nombre}</p>
                <p><strong>Tipo:</strong> <span class="badge bg-primary">${beca.tipo_beca}</span></p>
                <p><strong>Estado:</strong> <span class="badge bg-${beca.activa ? 'success' : 'danger'}">${beca.activa ? 'Activa' : 'Inactiva'}</span></p>
                <p><strong>Descripción:</strong> ${beca.descripcion || 'Sin descripción'}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">Detalles Financieros</h6>
                <p><strong>Monto:</strong> ${beca.monto_beca ? '$' + parseFloat(beca.monto_beca).toFixed(2) : 'No especificado'}</p>
                <p><strong>Cupos:</strong> ${beca.cupos_disponibles || 'Sin límite'}</p>
                <p><strong>Puntaje Mínimo:</strong> ${beca.puntaje_minimo_requerido || 'No requerido'}</p>
                <p><strong>Período:</strong> ${periodoText}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-info">Configuración</h6>
                <p><strong>Carreras Habilitadas:</strong> ${carrerasText}</p>
                <p><strong>Requisitos:</strong> ${beca.requisitos || 'No especificados'}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-warning">Documentación</h6>
                <p><strong>Documentos Requeridos:</strong> ${documentosText}</p>
                <p><strong>Observaciones:</strong> ${beca.observaciones || 'Sin observaciones'}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-secondary">Información del Sistema</h6>
                        <p><strong>Creada:</strong> ${new Date(beca.fecha_creacion).toLocaleString('es-ES')}</p>
        <p><strong>Última Actualización:</strong> ${beca.fecha_actualizacion ? new Date(beca.fecha_actualizacion).toLocaleString('es-ES') : 'No actualizada'}</p>
            </div>
        </div>
    `;
    
    document.getElementById('detallesBecaContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalVerBeca')).show();
}

// Función para editar una beca
function editarBeca(id) {
    const beca = becas.find(b => b.id == id);
    if (!beca) {
        mostrarNotificacion('Beca no encontrada', 'error');
        return;
    }

    // Cargar datos de la beca en el formulario
    fetch(`<?= base_url('admin-bienestar/obtener-beca') ?>/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                llenarFormularioEdicion(data.beca);
                new bootstrap.Modal(document.getElementById('modalEditarBeca')).show();
            } else {
                mostrarNotificacion(data.error || 'Error cargando beca', 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion('Error de conexión', 'error');
        });
}

// Función para llenar formulario de edición
function llenarFormularioEdicion(beca) {
    document.getElementById('edit_id').value = beca.id;
    document.getElementById('edit_nombre').value = beca.nombre || '';
    document.getElementById('edit_tipo_beca').value = beca.tipo_beca || '';
    document.getElementById('edit_descripcion').value = beca.descripcion || '';
    document.getElementById('edit_monto_beca').value = beca.monto_beca || '';
    document.getElementById('edit_cupos_disponibles').value = beca.cupos_disponibles || '';
    document.getElementById('edit_periodo_vigente_id').value = beca.periodo_vigente_id || '';
    document.getElementById('edit_puntaje_minimo_requerido').value = beca.puntaje_minimo_requerido || '';
    document.getElementById('edit_requisitos').value = beca.requisitos || '';
    document.getElementById('edit_observaciones').value = beca.observaciones || '';
    document.getElementById('edit_activa').checked = beca.activa == 1;
    
    // Llenar carreras habilitadas
    // Campo carreras_habilitadas no disponible en la tabla actual
    // const carrerasSelect = document.getElementById('edit_carreras_habilitadas');
    // Array.from(carrerasSelect.options).forEach(option => {
    //     option.selected = beca.carreras_habilitadas && beca.carreras_habilitadas.includes(parseInt(option.value));
    // });
    
    // Llenar documentos requeridos
    if (beca.documentos_requisitos && Array.isArray(beca.documentos_requisitos)) {
        document.getElementById('edit_documentos_requeridos').value = beca.documentos_requisitos.join(', ');
    } else {
        document.getElementById('edit_documentos_requeridos').value = '';
    }
}

// Función para cambiar estado de una beca
function toggleEstadoBeca(id, estadoActual) {
    const accion = estadoActual ? 'desactivar' : 'activar';
    const titulo = estadoActual ? '¿Desactivar beca?' : '¿Activar beca?';
    
    Swal.fire({
        title: titulo,
        text: `Esta acción ${accion}á la beca`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: estadoActual ? '#dc3545' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: `${accion}ando beca`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar petición
            fetch('<?= base_url('admin-bienestar/toggle-estado-beca') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    activa: !estadoActual
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarNotificacion(data.error || 'Error cambiando estado', 'error');
                }
            })
            .catch(error => {
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
    });
}

// Función para exportar becas
function exportarBecas() {
    Swal.fire({
        title: 'Exportando...',
        text: 'Preparando archivo de exportación',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear enlace temporal para descarga
    const url = '<?= base_url('admin-bienestar/exportar-becas') ?>';
    const link = document.createElement('a');
    link.href = url;
    link.download = 'programas_becas_<?= date('Y-m-d_H-i-s') ?>.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        Swal.fire('Éxito', 'Archivo exportado exitosamente', 'success');
    }, 1000);
}

// Debug
// Event Listeners para formularios
document.addEventListener('DOMContentLoaded', function() {
    // Formulario nueva beca
    document.getElementById('formNuevaBeca').addEventListener('submit', function(e) {
        e.preventDefault();
        crearBeca();
    });
    
    // Formulario editar beca
    document.getElementById('formEditarBeca').addEventListener('submit', function(e) {
        e.preventDefault();
        actualizarBeca();
    });
});

// Función para crear nueva beca
function crearBeca() {
    const formData = new FormData(document.getElementById('formNuevaBeca'));
    const data = {};
    
    // Convertir FormData a objeto
    for (let [key, value] of formData.entries()) {
        if (key === 'carreras_habilitadas[]') {
            if (!data.carreras_habilitadas) data.carreras_habilitadas = [];
            data.carreras_habilitadas.push(value);
        } else if (key === 'documentos_requeridos') {
            data.documentos_requisitos = value.split(',').map(doc => doc.trim()).filter(doc => doc);
        } else {
            data[key] = value;
        }
    }
    
    // Procesar checkbox
    data.activa = document.getElementById('activa').checked ? 1 : 0;
    
    // Validar campos requeridos
    if (!data.nombre || !data.tipo_beca) {
        mostrarNotificacion('Los campos Nombre y Tipo de Beca son obligatorios', 'error');
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Creando...',
        text: 'Creando nueva beca',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición
    fetch('<?= base_url('admin-bienestar/crear-beca') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            let errorMsg = data.error;
            if (data.validation_errors) {
                errorMsg = Object.values(data.validation_errors).join('\n');
            }
            mostrarNotificacion(errorMsg, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error de conexión', 'error');
    });
}

// Función para actualizar beca
function actualizarBeca() {
    const formData = new FormData(document.getElementById('formEditarBeca'));
    const data = {};
    
    // Convertir FormData a objeto
    for (let [key, value] of formData.entries()) {
        if (key === 'carreras_habilitadas[]') {
            if (!data.carreras_habilitadas) data.carreras_habilitadas = [];
            data.carreras_habilitadas.push(value);
        } else if (key === 'documentos_requeridos') {
            data.documentos_requisitos = value.split(',').map(doc => doc.trim()).filter(doc => doc);
        } else {
            data[key] = value;
        }
    }
    
    // Procesar checkbox
    data.activa = document.getElementById('edit_activa').checked ? 1 : 0;
    
    // Validar campos requeridos
    if (!data.nombre || !data.tipo_beca) {
        mostrarNotificacion('Los campos Nombre y Tipo de Beca son obligatorios', 'error');
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Actualizando...',
        text: 'Actualizando beca',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición
    fetch('<?= base_url('admin-bienestar/actualizar-beca') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            let errorMsg = data.error;
            if (data.validation_errors) {
                errorMsg = Object.values(data.validation_errors).join('\n');
            }
            mostrarNotificacion(errorMsg, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error de conexión', 'error');
    });
}

// Función para eliminar beca
function eliminarBeca(id) {
    Swal.fire({
        title: '¿Eliminar beca?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Eliminando beca',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar petición
            fetch('<?= base_url('admin-bienestar/eliminar-beca') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarNotificacion(data.error || 'Error eliminando beca', 'error');
                }
            })
            .catch(error => {
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
    });
}



// Datos para gráficos
const datosBecas = <?= json_encode($becas ?? []) ?>;

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
        inicializarGraficos();
    } else {
    }
});

function inicializarGraficos() {
    // 1. Gráfico de Distribución por Estado
    const estadoData = procesarDatosEstado(datosBecas);
    new Chart(document.getElementById('estadoChart'), {
        type: 'doughnut',
        data: {
            labels: estadoData.labels,
            datasets: [{
                data: estadoData.values,
                backgroundColor: [
                    '#28a745', // Activas - Verde
                    '#dc3545', // Inactivas - Rojo
                    '#ffc107'  // Pendientes - Amarillo
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
                    text: 'Estado de las Becas'
                }
            }
        }
    });

    // 2. Gráfico de Becas por Tipo
    const tipoData = procesarDatosTipo(datosBecas);
    new Chart(document.getElementById('tipoChart'), {
        type: 'bar',
        data: {
            labels: tipoData.labels,
            datasets: [{
                label: 'Cantidad de Becas',
                data: tipoData.values,
                backgroundColor: [
                    '#007bff', // Azul
                    '#28a745', // Verde
                    '#ffc107', // Amarillo
                    '#dc3545', // Rojo
                    '#6f42c1', // Púrpura
                    '#fd7e14'  // Naranja
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
                    text: 'Distribución por Tipo de Beca'
                }
            }
        }
    });

    // 3. Gráfico de Montos
    const montoData = procesarDatosMontos(datosBecas);
    new Chart(document.getElementById('montoChart'), {
        type: 'line',
        data: {
            labels: montoData.labels,
            datasets: [{
                label: 'Monto ($)',
                data: montoData.values,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Montos de Becas por Programa'
                }
            }
        }
    });

    // 4. Gráfico de Cupos Disponibles
    const cuposData = procesarDatosCupos(datosBecas);
    new Chart(document.getElementById('cuposChart'), {
        type: 'polarArea',
        data: {
            labels: cuposData.labels,
            datasets: [{
                data: cuposData.values,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 205, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderWidth: 2
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
                    text: 'Cupos Disponibles por Beca'
                }
            }
        }
    });
}

// Funciones para procesar datos
function procesarDatosEstado(becas) {
    const activas = becas.filter(b => b.activa == 1).length;
    const inactivas = becas.filter(b => b.activa == 0).length;
    
    return {
        labels: ['Activas', 'Inactivas'],
        values: [activas, inactivas]
    };
}

function procesarDatosTipo(becas) {
    const tipos = {};
    becas.forEach(beca => {
        const tipo = beca.tipo_beca || 'Sin clasificar';
        tipos[tipo] = (tipos[tipo] || 0) + 1;
    });
    
    return {
        labels: Object.keys(tipos),
        values: Object.values(tipos)
    };
}

function procesarDatosMontos(becas) {
    const becasConMonto = becas.filter(b => b.monto_beca && b.monto_beca > 0);
    becasConMonto.sort((a, b) => parseFloat(a.monto_beca) - parseFloat(b.monto_beca));
    
    return {
        labels: becasConMonto.map(b => b.nombre.substring(0, 15) + '...'),
        values: becasConMonto.map(b => parseFloat(b.monto_beca))
    };
}

function procesarDatosCupos(becas) {
    const becasConCupos = becas.filter(b => b.cupos_disponibles && b.cupos_disponibles > 0);
    
    return {
        labels: becasConCupos.map(b => b.nombre.substring(0, 20)),
        values: becasConCupos.map(b => parseInt(b.cupos_disponibles))
    };
}
</script>

<!-- Modal Nueva Beca -->
<div class="modal fade" id="modalNuevaBeca" tabindex="-1" aria-labelledby="modalNuevaBecaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaBecaLabel">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Beca
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaBeca">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la Beca *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_beca" class="form-label">Tipo de Beca *</label>
                                <select class="form-select" id="tipo_beca" name="tipo_beca" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php foreach ($tipos_beca as $tipo => $descripcion): ?>
                                        <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="monto_beca" class="form-label">Monto de la Beca ($)</label>
                                <input type="number" class="form-control" id="monto_beca" name="monto_beca" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cupos_disponibles" class="form-label">Cupos Disponibles</label>
                                <input type="number" class="form-control" id="cupos_disponibles" name="cupos_disponibles" min="0">
                                <small class="form-text text-muted">Dejar vacío para sin límite</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="periodo_vigente_id" class="form-label">Período Académico</label>
                                <select class="form-select" id="periodo_vigente_id" name="periodo_vigente_id">
                                    <option value="">Seleccionar período</option>
                                    <?php foreach ($periodos as $periodo): ?>
                                        <option value="<?= $periodo['id'] ?>"><?= esc($periodo['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="puntaje_minimo_requerido" class="form-label">Puntaje Mínimo</label>
                                <input type="number" class="form-control" id="puntaje_minimo_requerido" name="puntaje_minimo_requerido" step="0.1" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo no disponible en la tabla actual
                    <div class="mb-3">
                        <label for="carreras_habilitadas" class="form-label">Carreras Habilitadas</label>
                        <select class="form-select" id="carreras_habilitadas" name="carreras_habilitadas[]" multiple>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id'] ?>"><?= esc($carrera['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Seleccionar múltiples con Ctrl+Click</small>
                    </div>
                    -->
                    
                    <div class="mb-3">
                        <label for="requisitos" class="form-label">Requisitos</label>
                        <textarea class="form-control" id="requisitos" name="requisitos" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="documentos_requeridos" class="form-label">Documentos Requeridos</label>
                        <textarea class="form-control" id="documentos_requeridos" name="documentos_requeridos" rows="3" placeholder="Separar documentos con comas"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activa" name="activa" value="1" checked>
                        <label class="form-check-label" for="activa">
                            Beca Activa
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Crear Beca
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Beca -->
<div class="modal fade" id="modalEditarBeca" tabindex="-1" aria-labelledby="modalEditarBecaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarBecaLabel">
                    <i class="bi bi-pencil me-2"></i>Editar Beca
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarBeca">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nombre" class="form-label">Nombre de la Beca *</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tipo_beca" class="form-label">Tipo de Beca *</label>
                                <select class="form-select" id="edit_tipo_beca" name="tipo_beca" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php foreach ($tipos_beca as $tipo => $descripcion): ?>
                                        <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_monto_beca" class="form-label">Monto de la Beca ($)</label>
                                <input type="number" class="form-control" id="edit_monto_beca" name="monto_beca" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_cupos_disponibles" class="form-label">Cupos Disponibles</label>
                                <input type="number" class="form-control" id="edit_cupos_disponibles" name="cupos_disponibles" min="0">
                                <small class="form-text text-muted">Dejar vacío para sin límite</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_periodo_vigente_id" class="form-label">Período Académico</label>
                                <select class="form-select" id="edit_periodo_vigente_id" name="periodo_vigente_id">
                                    <option value="">Seleccionar período</option>
                                    <?php foreach ($periodos as $periodo): ?>
                                        <option value="<?= $periodo['id'] ?>"><?= esc($periodo['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_puntaje_minimo_requerido" class="form-label">Puntaje Mínimo</label>
                                <input type="number" class="form-control" id="edit_puntaje_minimo_requerido" name="puntaje_minimo_requerido" step="0.1" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo no disponible en la tabla actual
                    <div class="mb-3">
                        <label for="edit_carreras_habilitadas" class="form-label">Carreras Habilitadas</label>
                        <select class="form-select" id="edit_carreras_habilitadas" name="carreras_habilitadas[]" multiple>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= esc($carrera['id']) ?>"><?= esc($carrera['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Seleccionar múltiples con Ctrl+Click</small>
                    </div>
                    -->
                    
                    <div class="mb-3">
                        <label for="edit_requisitos" class="form-label">Requisitos</label>
                        <textarea class="form-control" id="edit_requisitos" name="requisitos" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_documentos_requeridos" class="form-label">Documentos Requeridos</label>
                        <textarea class="form-control" id="edit_documentos_requeridos" name="documentos_requeridos" rows="3" placeholder="Separar documentos con comas"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="edit_observaciones" name="observaciones" rows="2"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_activa" name="activa" value="1">
                        <label class="form-check-label" for="edit_activa">
                            Beca Activa
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Actualizar Beca
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles de Beca -->
<div class="modal fade" id="modalVerBeca" tabindex="-1" aria-labelledby="modalVerBecaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerBecaLabel">
                    <i class="bi bi-eye me-2"></i>Detalles de la Beca
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detallesBecaContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
