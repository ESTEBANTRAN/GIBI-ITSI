<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Formularios Socioeconómicos</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download"></i> Exportar
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel"></i> Filtros
        </button>
    </div>
</div>

<!-- Filtros Rápidos -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="searchEstudiante" class="form-label">Buscar Estudiante</label>
                        <input type="text" class="form-control" id="searchEstudiante" placeholder="Cédula o nombre...">
                    </div>
                    <div class="col-md-2">
                        <label for="filterEstado" class="form-label">Estado</label>
                        <select class="form-select" id="filterEstado">
                            <option value="">Todos</option>
                            <option value="Borrador">Borrador</option>
                            <option value="Enviada">Enviada</option>
                            <option value="Revisada">Revisada</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="filterPeriodo" class="form-label">Periodo</label>
                        <select class="form-select" id="filterPeriodo">
                            <option value="">Todos</option>
                            <option value="2024-1">2024-1</option>
                            <option value="2024-2">2024-2</option>
                            <option value="2025-1">2025-1</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="filterCarrera" class="form-label">Carrera</label>
                        <select class="form-select" id="filterCarrera">
                            <option value="">Todas</option>
                            <option value="Ingeniería Informática">Ingeniería Informática</option>
                            <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                            <option value="Administración">Administración</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Fichas -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaFichas">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Carrera</th>
                        <th>Periodo</th>
                        <th>Estado</th>
                        <th>Fecha Envío</th>
                        <th>Última Revisión</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos de ejemplo -->
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('sistema/assets/images/profile/user-1.jpg') ?>" 
                                     class="rounded-circle me-2" width="32" height="32" alt="Foto">
                                <div>
                                    <div class="fw-bold">María González</div>
                                    <small class="text-muted">maria.gonzalez@estudiante.itsi.edu.ec</small>
                                </div>
                            </div>
                        </td>
                        <td>1723456789</td>
                        <td>Ingeniería Informática</td>
                        <td>2024-2</td>
                        <td>
                            <span class="badge bg-warning">Enviada</span>
                        </td>
                        <td>15/01/2025</td>
                        <td>-</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verFicha(1)" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        onclick="aprobarFicha(1)" title="Aprobar">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="rechazarFicha(1)" title="Rechazar">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('sistema/assets/images/profile/user-2.jpg') ?>" 
                                     class="rounded-circle me-2" width="32" height="32" alt="Foto">
                                <div>
                                    <div class="fw-bold">Carlos Rodríguez</div>
                                    <small class="text-muted">carlos.rodriguez@estudiante.itsi.edu.ec</small>
                                </div>
                            </div>
                        </td>
                        <td>1754321098</td>
                        <td>Ingeniería Industrial</td>
                        <td>2024-2</td>
                        <td>
                            <span class="badge bg-success">Aprobada</span>
                        </td>
                        <td>10/01/2025</td>
                        <td>12/01/2025</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verFicha(2)" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="editarFicha(2)" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('sistema/assets/images/profile/user-3.jpg') ?>" 
                                     class="rounded-circle me-2" width="32" height="32" alt="Foto">
                                <div>
                                    <div class="fw-bold">Ana Martínez</div>
                                    <small class="text-muted">ana.martinez@estudiante.itsi.edu.ec</small>
                                </div>
                            </div>
                        </td>
                        <td>1798765432</td>
                        <td>Administración</td>
                        <td>2024-2</td>
                        <td>
                            <span class="badge bg-danger">Rechazada</span>
                        </td>
                        <td>08/01/2025</td>
                        <td>09/01/2025</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verFicha(3)" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="revisarFicha(3)" title="Revisar">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <nav aria-label="Navegación de páginas" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Estadísticas Rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Fichas</h6>
                        <h3 class="mb-0">156</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-file-earmark-text fs-1"></i>
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
                        <h6 class="card-title">Pendientes</h6>
                        <h3 class="mb-0">23</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock fs-1"></i>
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
                        <h6 class="card-title">Aprobadas</h6>
                        <h3 class="mb-0">98</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Rechazadas</h6>
                        <h3 class="mb-0">12</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-x-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modal') ?>
<!-- Modal Ver Ficha -->
<div class="modal fade" id="modalVerFicha" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Ficha Socioeconómica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Estudiante</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nombre:</strong></td><td>María González</td></tr>
                            <tr><td><strong>Cédula:</strong></td><td>1723456789</td></tr>
                            <tr><td><strong>Carrera:</strong></td><td>Ingeniería Informática</td></tr>
                            <tr><td><strong>Semestre:</strong></td><td>6to</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Estado de la Ficha</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-warning">Enviada</span></td></tr>
                            <tr><td><strong>Fecha Envío:</strong></td><td>15/01/2025</td></tr>
                            <tr><td><strong>Última Revisión:</strong></td><td>-</td></tr>
                            <tr><td><strong>Revisado por:</strong></td><td>-</td></tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <h6>Datos Socioeconómicos</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Información Familiar</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Ingresos Familiares:</strong></td><td>$1,200.00</td></tr>
                            <tr><td><strong>Miembros del Hogar:</strong></td><td>4</td></tr>
                            <tr><td><strong>Personas que Trabajan:</strong></td><td>2</td></tr>
                            <tr><td><strong>Vivienda:</strong></td><td>Alquilada</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Gastos Mensuales</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Alimentación:</strong></td><td>$400.00</td></tr>
                            <tr><td><strong>Transporte:</strong></td><td>$120.00</td></tr>
                            <tr><td><strong>Servicios Básicos:</strong></td><td>$180.00</td></tr>
                            <tr><td><strong>Otros Gastos:</strong></td><td>$200.00</td></tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <h6>Documentos Adjuntos</h6>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    Cédula de Identidad
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Descargar</button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    Certificado de Ingresos
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Descargar</button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    Certificado de Notas
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Descargar</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="aprobarFichaModal()">
                    <i class="bi bi-check-circle"></i> Aprobar
                </button>
                <button type="button" class="btn btn-danger" onclick="rechazarFichaModal()">
                    <i class="bi bi-x-circle"></i> Rechazar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filtros Avanzados -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros Avanzados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Rango de Fechas</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="fechaDesde">
                            <span class="input-group-text">hasta</span>
                            <input type="date" class="form-control" id="fechaHasta">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semestre</label>
                        <select class="form-select" id="filterSemestre">
                            <option value="">Todos</option>
                            <option value="1">1er Semestre</option>
                            <option value="2">2do Semestre</option>
                            <option value="3">3er Semestre</option>
                            <option value="4">4to Semestre</option>
                            <option value="5">5to Semestre</option>
                            <option value="6">6to Semestre</option>
                            <option value="7">7mo Semestre</option>
                            <option value="8">8vo Semestre</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rango de Ingresos</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="ingresoMin" placeholder="Mínimo">
                            <span class="input-group-text">-</span>
                            <input type="number" class="form-control" id="ingresoMax" placeholder="Máximo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Vivienda</label>
                        <select class="form-select" id="filterVivienda">
                            <option value="">Todos</option>
                            <option value="Propia">Propia</option>
                            <option value="Alquilada">Alquilada</option>
                            <option value="Familiar">Familiar</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltrosAvanzados()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Exportar -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Formato de Exportación</label>
                    <select class="form-select" id="formatoExport">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rango de Datos</label>
                    <select class="form-select" id="rangoExport">
                        <option value="todos">Todos los registros</option>
                        <option value="filtrados">Solo registros filtrados</option>
                        <option value="seleccionados">Solo registros seleccionados</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="exportarDatos()">
                    <i class="bi bi-download"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<script>
// Funciones JavaScript para la funcionalidad
function verFicha(id) {
    $('#modalVerFicha').modal('show');
}

function aprobarFicha(id) {
    if (confirm('¿Está seguro de que desea aprobar esta ficha?')) {
        // Lógica para aprobar ficha
        alert('Ficha aprobada exitosamente');
    }
}

function rechazarFicha(id) {
    if (confirm('¿Está seguro de que desea rechazar esta ficha?')) {
        // Lógica para rechazar ficha
        alert('Ficha rechazada');
    }
}

function editarFicha(id) {
    window.location.href = '<?= base_url('fichas/editar/') ?>' + id;
}

function revisarFicha(id) {
    window.location.href = '<?= base_url('fichas/revisar/') ?>' + id;
}

function limpiarFiltros() {
    $('#searchEstudiante').val('');
    $('#filterEstado').val('');
    $('#filterPeriodo').val('');
    $('#filterCarrera').val('');
}

function aplicarFiltros() {
    // Lógica para aplicar filtros
}

function aplicarFiltrosAvanzados() {
    // Lógica para aplicar filtros avanzados
    $('#filterModal').modal('hide');
}

function exportarDatos() {
    const formato = $('#formatoExport').val();
    const rango = $('#rangoExport').val();
    
    // Lógica para exportar datos
    alert(`Exportando datos en formato ${formato} para ${rango}`);
    $('#exportModal').modal('hide');
}

function aprobarFichaModal() {
    if (confirm('¿Está seguro de que desea aprobar esta ficha?')) {
        $('#modalVerFicha').modal('hide');
        alert('Ficha aprobada exitosamente');
    }
}

function rechazarFichaModal() {
    if (confirm('¿Está seguro de que desea rechazar esta ficha?')) {
        $('#modalVerFicha').modal('hide');
        alert('Ficha rechazada');
    }
}

// Select all functionality
$('#selectAll').change(function() {
    $('.select-item').prop('checked', $(this).is(':checked'));
});

// Initialize tooltips
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script> 