<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Sistema de Reportes Avanzados</h4>
                <p class="text-muted mb-0">Genera reportes detallados y análisis del sistema de bienestar estudiantil</p>
            </div>
            <div class="page-title-right">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" onclick="exportarReporteCompleto()">
                        <i class="bi bi-file-excel"></i> Reporte Completo
                    </button>
                    <button type="button" class="btn btn-primary" onclick="generarReportePDF()">
                        <i class="bi bi-file-pdf"></i> PDF Ejecutivo
                    </button>
                    <button type="button" class="btn btn-info" onclick="actualizarDatos()">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-1">Total Fichas</p>
                        <h5 class="mb-0"><?= $estadisticas['fichas']['total'] ?? 0 ?></h5>
                        <p class="text-muted mb-0">
                            <span class="text-success">
                                <i class="bi bi-arrow-up"></i> 
                                <?= $estadisticas['fichas']['estados'][0]['total'] ?? 0 ?> este período
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bi bi-file-earmark-text"></i>
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
                        <p class="text-muted fw-medium mb-1">Becas Activas</p>
                        <h5 class="mb-0"><?= $estadisticas['becas']['total_becas'] ?? 0 ?></h5>
                        <p class="text-muted mb-0">
                            <span class="text-info">
                                $<?= number_format($estadisticas['becas']['tipos'][0]['cupos_totales'] ?? 0) ?> total
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i class="bi bi-award"></i>
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
                        <p class="text-muted fw-medium mb-1">Solicitudes</p>
                        <h5 class="mb-0"><?= $estadisticas['solicitudes']['total'] ?? 0 ?></h5>
                        <p class="text-muted mb-0">
                            <span class="text-warning">
                                <?= count($estadisticas['solicitudes']['por_estado'] ?? []) ?> estados
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i class="bi bi-envelope"></i>
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
                        <p class="text-muted fw-medium mb-1">Usuarios Activos</p>
                        <h5 class="mb-0"><?= $estadisticas['usuarios']['total'] ?? 0 ?></h5>
                        <p class="text-muted mb-0">
                            <span class="text-primary">
                                <?= count($estadisticas['usuarios']['por_rol'] ?? []) ?> roles
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="bi bi-people"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros para Reportes -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>Configurar Reporte
        </h5>
    </div>
    <div class="card-body">
        <form id="formReporte">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="tipo_reporte" name="tipo_reporte">
                        <option value="general">Reporte General</option>
                        <option value="fichas">Fichas Socioeconómicas</option>
                        <option value="becas">Becas y Solicitudes</option>
                        <option value="usuarios">Usuarios del Sistema</option>
                        <option value="estadisticas">Estadísticas Avanzadas</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="periodo_reporte" class="form-label">Período</label>
                    <select class="form-select" id="periodo_reporte" name="periodo_id">
                        <option value="">Todos los períodos</option>
                        <?php if (isset($estadisticas['periodos'])): ?>
                            <?php foreach ($estadisticas['periodos'] as $periodo): ?>
                                <option value="<?= $periodo['id'] ?>"><?= $periodo['nombre'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                </div>
                
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                </div>
                
                <div class="col-md-2">
                    <label for="formato_reporte" class="form-label">Formato</label>
                    <select class="form-select" id="formato_reporte" name="formato">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" onclick="generarReporte()">
                            <i class="bi bi-file-earmark-plus"></i> Generar Reporte
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="previsualizarReporte()">
                            <i class="bi bi-eye"></i> Previsualizar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="programarReporte()">
                            <i class="bi bi-clock"></i> Programar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Gráficos de Análisis -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tendencias Mensuales</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartTendencias"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Distribución por Categorías</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="chartDistribucion"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Reportes Recientes -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-clock-history me-2"></i>Reportes Generados Recientemente
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-centered table-nowrap mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Período</th>
                        <th>Formato</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= date('d/m/Y H:i') ?></td>
                        <td>Reporte General</td>
                        <td>2024-2025</td>
                        <td><span class="badge bg-danger">PDF</span></td>
                        <td><span class="badge bg-success">Completado</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="descargarReporte(1)">
                                <i class="bi bi-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime('-1 hour')) ?></td>
                        <td>Fichas Socioeconómicas</td>
                        <td>2024-2025</td>
                        <td><span class="badge bg-success">Excel</span></td>
                        <td><span class="badge bg-success">Completado</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="descargarReporte(2)">
                                <i class="bi bi-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime('-2 hours')) ?></td>
                        <td>Estadísticas Becas</td>
                        <td>Todos</td>
                        <td><span class="badge bg-info">CSV</span></td>
                        <td><span class="badge bg-warning">Procesando</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-hourglass-split"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Variables globales
let chartTendencias, chartDistribucion;

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficos();
    configurarFechasDefecto();
});

function inicializarGraficos() {
    // Gráfico de tendencias
    const ctxTendencias = document.getElementById('chartTendencias').getContext('2d');
    chartTendencias = new Chart(ctxTendencias, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Fichas Creadas',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#556ee6',
                backgroundColor: 'rgba(85, 110, 230, 0.1)',
                tension: 0.4
            }, {
                label: 'Becas Solicitadas',
                data: [8, 15, 12, 18, 20, 25],
                borderColor: '#34c38f',
                backgroundColor: 'rgba(52, 195, 143, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de distribución
    const ctxDistribucion = document.getElementById('chartDistribucion').getContext('2d');
    chartDistribucion = new Chart(ctxDistribucion, {
        type: 'doughnut',
        data: {
            labels: ['Aprobadas', 'Pendientes', 'Rechazadas', 'En Revisión'],
            datasets: [{
                data: [<?= $estadisticas['fichas']['estados'][0]['total'] ?? 30 ?>, 
                       <?= $estadisticas['fichas']['estados'][1]['total'] ?? 20 ?>, 
                       <?= $estadisticas['fichas']['estados'][2]['total'] ?? 10 ?>, 
                       <?= $estadisticas['fichas']['estados'][3]['total'] ?? 15 ?>],
                backgroundColor: ['#34c38f', '#f1b44c', '#f46a6a', '#556ee6'],
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
                }
            }
        }
    });
}

function configurarFechasDefecto() {
    const hoy = new Date();
    const hace30Dias = new Date();
    hace30Dias.setDate(hoy.getDate() - 30);
    
    document.getElementById('fecha_desde').value = hace30Dias.toISOString().split('T')[0];
    document.getElementById('fecha_hasta').value = hoy.toISOString().split('T')[0];
}

function generarReporte() {
    const formData = new FormData(document.getElementById('formReporte'));
    
    // Mostrar loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando...';
    btn.disabled = true;
    
    fetch('<?= base_url('admin-bienestar/generarReporte') ?>', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        if (!response.ok) {
            const text = await response.text();
            throw new Error(text || 'Error interno del servidor');
        }
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        let ext = formData.get('formato');
        if (ext === 'excel') ext = 'csv'; // Usamos CSV para excel
        a.download = `reporte_${formData.get('tipo_reporte')}_${Date.now()}.${ext}`;
        a.click();
        window.URL.revokeObjectURL(url);
        
        mostrarNotificacion('Reporte generado correctamente', 'success');
    })
    .catch(error => {
        mostrarNotificacion('Error generando reporte: ' + error.message, 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function previsualizarReporte() {
    const formData = new FormData(document.getElementById('formReporte'));
    formData.append('preview', 'true');
    
    fetch('<?= base_url('admin-bienestar/generarReporte') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar modal de previsualización
            mostrarPreview(data.data);
        } else {
            mostrarNotificacion('Error en previsualización: ' + data.error, 'error');
        }
    })
    .catch(error => {
        mostrarNotificacion('Error de conexión', 'error');
    });
}

function mostrarPreview(data) {
    let html = `<div class="text-start">
        <h5 class="mb-3">${data.titulo}</h5>
        <p class="text-muted">Total de registros que coinciden con los filtros: <strong>${data.total_registros}</strong></p>
        <p class="text-muted mb-2"><small>Mostrando los primeros ${data.muestra.length} registros como muestra:</small></p>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                <thead class="table-light"><tr>`;
    
    if (data.muestra && data.muestra.length > 0) {
        // Headers
        Object.keys(data.muestra[0]).forEach(key => {
            html += `<th>${key.toUpperCase().replace(/_/g, ' ')}</th>`;
        });
        html += `</tr></thead><tbody>`;
        // Data
        data.muestra.forEach(row => {
            html += `<tr>`;
            Object.values(row).forEach(val => {
                html += `<td>${val !== null ? val : 'N/A'}</td>`;
            });
            html += `</tr>`;
        });
    } else {
        html += `<th>Sin Datos</th></tr></thead><tbody><tr><td>No hay datos para estos filtros</td></tr>`;
    }
    
    html += `</tbody></table></div></div>`;

    Swal.fire({
        title: '<i class="bi bi-eye"></i> Vista Previa del Reporte',
        html: html,
        width: '80%',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#6c757d'
    });
}

function programarReporte() {
    Swal.fire({
        title: '<i class="bi bi-clock"></i> Programar Reporte',
        html: `
            <div class="text-start mb-3">
                <p class="text-muted">El reporte se generará automáticamente con los filtros actuales y se enviará por correo.</p>
                <div class="mb-3">
                    <label class="form-label">Frecuencia</label>
                    <select id="swal-freq" class="form-select">
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email de destino</label>
                    <input type="email" id="swal-email" class="form-control" placeholder="admin@itsi.edu.ec">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar Programación',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#556ee6',
        preConfirm: () => {
            const freq = document.getElementById('swal-freq').value;
            const email = document.getElementById('swal-email').value;
            if (!email) {
                Swal.showValidationMessage('Debes ingresar un email de destino');
            }
            return { freq: freq, email: email };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Programado Exitosamente',
                text: `El reporte se enviará de forma ${result.value.freq} a ${result.value.email}`
            });
        }
    });
}

function exportarReporteCompleto() {
    const formData = new FormData();
    formData.append('tipo_reporte', 'completo');
    formData.append('formato', 'excel');
    
    fetch('<?= base_url('admin-bienestar/exportarDatos') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_completo_${Date.now()}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        mostrarNotificacion('Error exportando reporte completo', 'error');
    });
}

function generarReportePDF() {
    const formData = new FormData();
    formData.append('tipo_reporte', 'ejecutivo');
    formData.append('formato', 'pdf');
    
    fetch('<?= base_url('admin-bienestar/generarReporte') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_ejecutivo_${Date.now()}.pdf`;
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        mostrarNotificacion('Error generando PDF ejecutivo', 'error');
    });
}

function descargarReporte(id) {
    // Implementar descarga de reporte específico
    mostrarNotificacion(`Descargando reporte ${id}...`, 'info');
}

function actualizarDatos() {
    location.reload();
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
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

.chart-container {
    position: relative;
    height: 300px;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .chart-container {
        height: 250px;
    }
}
</style>

<?= $this->endSection() ?>
