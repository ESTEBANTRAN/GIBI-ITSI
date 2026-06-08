<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Estudiantes</h4>
                <p class="text-muted mb-0">Visualiza el historial completo de los estudiantes</p>
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
                    <i class="bi bi-mortarboard fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= is_array($estudiantes) ? count($estudiantes) : 0 ?></h3>
                        <p class="mb-0">Total Estudiantes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-file-earmark-text fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= is_array($estudiantes) ? array_sum(array_column($estudiantes, 'total_fichas')) : 0 ?></h3>
                        <p class="mb-0">Total Fichas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-award fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= is_array($estudiantes) ? array_sum(array_column($estudiantes, 'total_becas')) : 0 ?></h3>
                        <p class="mb-0">Total Becas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-question-circle fs-1 me-3"></i>
                    <div>
                        <h3 class="mb-0"><?= is_array($estudiantes) ? array_sum(array_column($estudiantes, 'total_ayudas')) : 0 ?></h3>
                        <p class="mb-0">Total Ayudas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Info -->
<?php if (isset($error)): ?>
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Error:</strong> <?= esc($error) ?>
</div>
<?php endif; ?>

<!-- Tabla de Estudiantes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lista de Estudiantes</h5>
        <div>
            <button type="button" class="btn btn-outline-success btn-sm" onclick="window.location.href='<?= base_url('admin-bienestar/usuarios/exportar') ?>?formato=excel'">
                <i class="bi bi-file-excel me-1"></i>Exportar Excel
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="window.location.href='<?= base_url('admin-bienestar/usuarios/exportar') ?>?formato=pdf'">
                <i class="bi bi-file-pdf me-1"></i>Exportar PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($estudiantes)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No hay usuarios registrados</h5>
            <p class="text-muted">Crea el primer usuario usando el botón "Nuevo Usuario"</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                <i class="bi bi-plus-circle me-2"></i>Crear Primer Usuario
            </button>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Carrera</th>
                        <th>Semestre</th>
                        <th>Fichas</th>
                        <th>Becas</th>
                        <th>Ayudas</th>
                        <th>Última Actividad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <?= strtoupper(substr($estudiante['nombre'] ?? 'E', 0, 1)) ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= esc(($estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellido'] ?? '')) ?></h6>
                                    <small class="text-muted"><?= esc($estudiante['email'] ?? '') ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace"><?= esc($estudiante['cedula'] ?? '') ?></span>
                        </td>
                        <td>
                            <?php if (!empty($estudiante['carrera_nombre'])): ?>
                                <span class="badge bg-info"><?= esc($estudiante['carrera_nombre']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">No especificada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= esc($estudiante['semestre'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?= $estudiante['total_fichas'] ?? 0 ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success"><?= $estudiante['total_becas'] ?? 0 ?></span>
                        </td>
                        <td>
                            <span class="badge bg-warning"><?= $estudiante['total_ayudas'] ?? 0 ?></span>
                        </td>
                        <td>
                            <?php if (!empty($estudiante['ultima_actividad'])): ?>
                                <small><?= date('d/m/Y', strtotime($estudiante['ultima_actividad'])) ?></small>
                                <br><small class="text-muted"><?= ucfirst($estudiante['tipo_ultima_actividad'] ?? '') ?></small>
                            <?php else: ?>
                                <span class="text-muted">Sin actividad</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verHistorialEstudiante(<?= $estudiante['id'] ?? 0 ?>)" title="Ver Historial">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="verDetallesEstudiante(<?= $estudiante['id'] ?? 0 ?>)" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
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

<!-- Paginación -->
<?php if (isset($paginacion) && $paginacion['total_paginas'] > 1): ?>
<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Mostrando <?= ($paginacion['offset'] + 1) ?> a <?= min($paginacion['offset'] + $paginacion['por_pagina'], $paginacion['total_registros']) ?> 
                de <?= $paginacion['total_registros'] ?> estudiantes
            </div>
            <nav aria-label="Paginación de estudiantes">
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($paginacion['pagina_actual'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $paginacion['pagina_actual'] - 1 ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    <?php endif; ?>

                    <?php
                    $inicio = max(1, $paginacion['pagina_actual'] - 2);
                    $fin = min($paginacion['total_paginas'], $paginacion['pagina_actual'] + 2);

                    if ($inicio > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1">1</a>
                        </li>
                        <?php if ($inicio > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <li class="page-item <?= $i == $paginacion['pagina_actual'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($fin < $paginacion['total_paginas']): ?>
                        <?php if ($fin < $paginacion['total_paginas'] - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $paginacion['total_paginas'] ?>"><?= $paginacion['total_paginas'] ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $paginacion['pagina_actual'] + 1 ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Gráficos Informativos -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Distribución por Carreras
                </h5>
            </div>
            <div class="card-body">
                <canvas id="graficoCarreras" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Actividad por Semestres
                </h5>
            </div>
            <div class="card-body">
                <canvas id="graficoSemestres" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Tendencias de Actividad -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Tendencias de Actividad Estudiantil
                </h5>
            </div>
            <div class="card-body">
                <canvas id="graficoTendencias" width="800" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial del Estudiante -->
<div class="modal fade" id="modalHistorialEstudiante" tabindex="-1" aria-labelledby="modalHistorialEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialEstudianteLabel">Historial Completo del Estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingHistorial" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando historial...</p>
                </div>
                
                <div id="contenidoHistorial" style="display: none;">
                    <!-- Información del Estudiante -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información del Estudiante</h6>
                                </div>
                                <div class="card-body" id="infoEstudiante">
                                    <!-- Se llena dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestañas de Historial -->
                    <ul class="nav nav-tabs" id="historialTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="fichas-tab" data-bs-toggle="tab" data-bs-target="#fichas" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i>Fichas Socioeconómicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="becas-tab" data-bs-toggle="tab" data-bs-target="#becas" type="button" role="tab">
                                <i class="bi bi-award me-2"></i>Solicitudes de Becas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ayudas-tab" data-bs-toggle="tab" data-bs-target="#ayudas" type="button" role="tab">
                                <i class="bi bi-question-circle me-2"></i>Solicitudes de Ayuda
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                                <i class="bi bi-folder me-2"></i>Documentos
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="historialTabsContent">
                        <!-- Pestaña Fichas -->
                        <div class="tab-pane fade show active" id="fichas" role="tabpanel">
                            <div id="contenidoFichas">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Pestaña Becas -->
                        <div class="tab-pane fade" id="becas" role="tabpanel">
                            <div id="contenidoBecas">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Pestaña Ayudas -->
                        <div class="tab-pane fade" id="ayudas" role="tabpanel">
                            <div id="contenidoAyudas">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Pestaña Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                            <div id="contenidoDocumentos">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    height: 2.5rem;
    width: 2.5rem;
    font-size: 1rem;
    font-weight: 600;
}

.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
}

.table > :not(caption) > * > * {
    padding: 0.75rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: 1px;
}
</style>

<script>
function mostrarCampoCarrera() {
    const rolId = document.getElementById('rol_id').value;
    const divCarrera = document.getElementById('divCarrera');
    const carreraField = document.getElementById('carrera_id');
    
    if (rolId == '1') { // Estudiante
        divCarrera.style.display = 'block';
        carreraField.required = true;
    } else {
        divCarrera.style.display = 'none';
        carreraField.required = false;
        carreraField.value = '';
    }
}

// Función para ver el historial completo de un estudiante
function verHistorialEstudiante(estudianteId) {
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalHistorialEstudiante'));
    modal.show();
    
    // Mostrar loading
    document.getElementById('loadingHistorial').style.display = 'block';
    document.getElementById('contenidoHistorial').style.display = 'none';
    
    // Cargar historial
    fetch(`<?= base_url('admin-bienestar/historial-estudiante') ?>/${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarHistorialEstudiante(data);
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar el historial del estudiante', 'error');
        })
        .finally(() => {
            document.getElementById('loadingHistorial').style.display = 'none';
            document.getElementById('contenidoHistorial').style.display = 'block';
        });
}

// Función para mostrar el historial del estudiante
function mostrarHistorialEstudiante(data) {
    const { estudiante, fichas, solicitudes_becas, solicitudes_ayuda, documentos } = data;
    
    // Información del estudiante
    document.getElementById('infoEstudiante').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nombre:</strong> ${estudiante.nombre} ${estudiante.apellido}</p>
                <p><strong>Cédula:</strong> ${estudiante.cedula}</p>
                <p><strong>Email:</strong> ${estudiante.email}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Carrera:</strong> ${estudiante.carrera_nombre || 'No especificada'}</p>
                <p><strong>Semestre:</strong> ${estudiante.semestre || 'N/A'}</p>
                <p><strong>Teléfono:</strong> ${estudiante.telefono || 'No especificado'}</p>
            </div>
        </div>
    `;
    
    // Pestaña Fichas
    if (fichas && fichas.length > 0) {
        let fichasHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        fichas.forEach(ficha => {
            const estadoClass = ficha.estado === 'Aprobada' ? 'bg-success' : 
                               ficha.estado === 'Rechazada' ? 'bg-danger' : 'bg-warning';
            
            fichasHTML += `
                <tr>
                    <td><span class="badge bg-info">${ficha.periodo_nombre}</span></td>
                    <td><span class="badge ${estadoClass}">${ficha.estado}</span></td>
                    <td>${new Date(ficha.fecha_creacion).toLocaleDateString('es-ES')}</td>
                    <td>${ficha.comentarios || 'Sin comentarios'}</td>
                </tr>
            `;
        });
        
        fichasHTML += '</tbody></table></div>';
        document.getElementById('contenidoFichas').innerHTML = fichasHTML;
    } else {
        document.getElementById('contenidoFichas').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">No hay fichas socioeconómicas registradas</p></div>';
    }
    
    // Pestaña Becas
    if (solicitudes_becas && solicitudes_becas.length > 0) {
        let becasHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Beca</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Fecha Solicitud</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        solicitudes_becas.forEach(beca => {
            const estadoClass = beca.estado === 'Aprobada' ? 'bg-success' : 
                               beca.estado === 'Rechazada' ? 'bg-danger' : 'bg-warning';
            
            becasHTML += `
                <tr>
                    <td><span class="badge bg-primary">${beca.nombre_beca}</span></td>
                    <td><span class="badge bg-info">${beca.periodo_nombre}</span></td>
                    <td><span class="badge ${estadoClass}">${beca.estado}</span></td>
                    <td>${new Date(beca.fecha_solicitud).toLocaleDateString('es-ES')}</td>
                </tr>
            `;
        });
        
        becasHTML += '</tbody></table></div>';
        document.getElementById('contenidoBecas').innerHTML = becasHTML;
    } else {
        document.getElementById('contenidoBecas').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">No hay solicitudes de becas registradas</p></div>';
    }
    
    // Pestaña Ayudas
    if (solicitudes_ayuda && solicitudes_ayuda.length > 0) {
        let ayudasHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Ayuda</th>
                            <th>Estado</th>
                            <th>Fecha Solicitud</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        solicitudes_ayuda.forEach(ayuda => {
            const estadoClass = ayuda.estado === 'Resuelta' ? 'bg-success' : 
                               ayuda.estado === 'Cerrada' ? 'bg-secondary' : 
                               ayuda.estado === 'En Proceso' ? 'bg-info' : 'bg-warning';
            
            ayudasHTML += `
                <tr>
                    <td><span class="badge bg-warning">${ayuda.tipo_ayuda_nombre}</span></td>
                    <td><span class="badge ${estadoClass}">${ayuda.estado}</span></td>
                    <td>${new Date(ayuda.fecha_solicitud).toLocaleDateString('es-ES')}</td>
                    <td>${ayuda.descripcion || 'Sin descripción'}</td>
                </tr>
            `;
        });
        
        ayudasHTML += '</tbody></table></div>';
        document.getElementById('contenidoAyudas').innerHTML = ayudasHTML;
    } else {
        document.getElementById('contenidoAyudas').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">No hay solicitudes de ayuda registradas</p></div>';
    }
    
    // Pestaña Documentos
    if (documentos && documentos.length > 0) {
        let documentosHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Documento</th>
                            <th>Beca</th>
                            <th>Estado</th>
                            <th>Fecha Revisión</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        documentos.forEach(doc => {
            const estadoClass = doc.estado === 'Aprobado' ? 'bg-success' : 
                               doc.estado === 'Rechazado' ? 'bg-danger' : 'bg-warning';
            
            documentosHTML += `
                <tr>
                    <td><span class="badge bg-primary">${doc.nombre_documento}</span></td>
                    <td><span class="badge bg-info">${doc.nombre_beca}</span></td>
                    <td><span class="badge ${estadoClass}">${doc.estado}</span></td>
                    <td>${doc.fecha_revision ? new Date(doc.fecha_revision).toLocaleDateString('es-ES') : 'Sin revisar'}</td>
                </tr>
            `;
        });
        
        documentosHTML += '</tbody></table></div>';
        document.getElementById('contenidoDocumentos').innerHTML = documentosHTML;
    } else {
        document.getElementById('contenidoDocumentos').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">No hay documentos registrados</p></div>';
    }
}

// Función para ver detalles básicos del estudiante
function verDetallesEstudiante(estudianteId) {
    Swal.fire('Info', 'Ver detalles del estudiante ID: ' + estudianteId, 'info');
}

// Debug info
// Función para crear gráficos informativos
function crearGraficos() {
    const estudiantes = <?= json_encode($estudiantes ?? []) ?>;
    
    if (estudiantes.length === 0) return;
    
    // Preparar datos para gráfico de carreras
    const carrerasData = {};
    estudiantes.forEach(estudiante => {
        const carrera = estudiante.carrera_nombre || 'Sin Carrera';
        carrerasData[carrera] = (carrerasData[carrera] || 0) + 1;
    });
    
    // Preparar datos para gráfico de semestres
    const semestresData = {};
    estudiantes.forEach(estudiante => {
        const semestre = estudiante.semestre || 'N/A';
        semestresData[semestre] = (semestresData[semestre] || 0) + 1;
    });
    
    // Preparar datos para gráfico de tendencias
    const tendenciasData = {
        fichas: estudiantes.reduce((sum, est) => sum + (est.total_fichas || 0), 0),
        becas: estudiantes.reduce((sum, est) => sum + (est.total_becas || 0), 0),
        ayudas: estudiantes.reduce((sum, est) => sum + (est.total_ayudas || 0), 0)
    };
    
    // Colores para los gráficos
    const colores = [
        '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d',
        '#17a2b8', '#fd7e14', '#6f42c1', '#e83e8c', '#20c997'
    ];
    
    // Gráfico de distribución por carreras (Pie Chart)
    const ctxCarreras = document.getElementById('graficoCarreras').getContext('2d');
    new Chart(ctxCarreras, {
        type: 'pie',
        data: {
            labels: Object.keys(carrerasData),
            datasets: [{
                data: Object.values(carrerasData),
                backgroundColor: colores.slice(0, Object.keys(carrerasData).length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de actividad por semestres (Bar Chart)
    const ctxSemestres = document.getElementById('graficoSemestres').getContext('2d');
    new Chart(ctxSemestres, {
        type: 'bar',
        data: {
            labels: Object.keys(semestresData),
            datasets: [{
                label: 'Estudiantes',
                data: Object.values(semestresData),
                backgroundColor: '#17a2b8',
                borderColor: '#138496',
                borderWidth: 1,
                borderRadius: 5
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
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Estudiantes: ${context.parsed.y}`;
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de tendencias de actividad (Doughnut Chart)
    const ctxTendencias = document.getElementById('graficoTendencias').getContext('2d');
    new Chart(ctxTendencias, {
        type: 'doughnut',
        data: {
            labels: ['Fichas Socioeconómicas', 'Solicitudes de Becas', 'Solicitudes de Ayuda'],
            datasets: [{
                data: [tendenciasData.fichas, tendenciasData.becas, tendenciasData.ayudas],
                backgroundColor: ['#28a745', '#007bff', '#ffc107'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Crear gráficos cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Cargar Chart.js desde CDN si no está disponible
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
        script.onload = crearGraficos;
        document.head.appendChild(script);
    } else {
        crearGraficos();
    }
});
</script>

<?= $this->endSection() ?>