<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Vista de AdminBienestar - Acceso Rápido</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Vista AdminBienestar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Admin -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge me-2"></i>Información del Administrador
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> <?= $admin['nombre'] ?? 'María González' ?></p>
                                <p><strong>Email:</strong> <?= $admin['email'] ?? 'maria.gonzalez@itsi.edu.ec' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cargo:</strong> <?= $admin['cargo'] ?? 'Coordinadora de Bienestar Estudiantil' ?></p>
                                <p><strong>Estado:</strong> <span class="badge bg-success">Activo</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard del AdminBienestar -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Estudiantes</h6>
                                <h3 class="mb-0"><?= $estadisticas['total_estudiantes'] ?? 1247 ?></h3>
                                <small>Registrados</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-people fs-1"></i>
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
                                <h6 class="card-title">Fichas Pendientes</h6>
                                <h3 class="mb-0"><?= $estadisticas['fichas_pendientes'] ?? 45 ?></h3>
                                <small>Por revisar</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-file-earmark-text fs-1"></i>
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
                                <h6 class="card-title">Becas Aprobadas</h6>
                                <h3 class="mb-0"><?= $estadisticas['becas_aprobadas'] ?? 156 ?></h3>
                                <small>Este período</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-award fs-1"></i>
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
                                <h6 class="card-title">Solicitudes de Ayuda</h6>
                                <h3 class="mb-0"><?= $estadisticas['solicitudes_ayuda'] ?? 23 ?></h3>
                                <small>Pendientes</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-question-circle fs-1"></i>
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
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-file-earmark-text mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Fichas Socioeconómicas</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-award mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Gestión de Becas</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-graph-up mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Reportes</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-calendar mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Períodos Académicos</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fichas Recientes -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>Fichas Socioeconómicas Recientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Período</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fichas_recientes ?? [] as $ficha): ?>
                                    <tr>
                                        <td><?= $ficha['estudiante'] ?></td>
                                        <td><?= $ficha['periodo'] ?></td>
                                        <td><span class="badge bg-warning"><?= $ficha['estado'] ?></span></td>
                                        <td><?= date('d/m/Y') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Revisar</button>
                                            <button class="btn btn-sm btn-outline-success">Aprobar</button>
                                            <button class="btn btn-sm btn-outline-danger">Rechazar</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de Estado de Fichas -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pie-chart me-2"></i>Estado de Fichas
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartFichas" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Períodos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-check me-2"></i>Gestión de Períodos Académicos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Período Actual</h6>
                                <div class="alert alert-success">
                                    <strong>2024-2</strong> - Período Vigente
                                    <br>
                                    <small>Fichas: 892/1000 | Becas: 156/200</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Próximo Período</h6>
                                <div class="alert alert-info">
                                    <strong>2025-1</strong> - Configuración Pendiente
                                    <br>
                                    <small>Inicio: 15/01/2025 | Fin: 15/06/2025</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gráfico de Estado de Fichas
    const ctxFichas = document.getElementById('chartFichas').getContext('2d');
    new Chart(ctxFichas, {
        type: 'doughnut',
        data: {
            labels: ['Aprobadas', 'Pendientes', 'Rechazadas', 'En Revisión'],
            datasets: [{
                data: [892, 45, 23, 12],
                backgroundColor: [
                    '#28a745', '#ffc107', '#dc3545', '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Simular funcionalidad de botones
    $('.btn').on('click', function() {
        const action = $(this).text().trim();
        Swal.fire({
            title: 'Vista de AdminBienestar',
            text: `Acción: ${action} - Esta es una vista de demostración desde SuperAdmin`,
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    });
});
</script>
<?= $this->endSection() ?> 