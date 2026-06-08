<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Vista de Estudiante - Acceso Rápido</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('global-admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Vista Estudiante</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Estudiante -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-circle me-2"></i>Información del Estudiante
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> <?= $estudiante['nombre'] ?? 'Juan Pérez' ?></p>
                                <p><strong>Email:</strong> <?= $estudiante['email'] ?? 'juan.perez@estudiante.itsi.edu.ec' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Carrera:</strong> <?= $estudiante['carrera'] ?? 'Ingeniería Informática' ?></p>
                                <p><strong>Semestre:</strong> <?= $estudiante['semestre'] ?? '5to Semestre' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard del Estudiante -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Fichas Socioeconómicas</h6>
                                <h3 class="mb-0">2</h3>
                                <small>Completadas</small>
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
                                <h6 class="card-title">Becas Solicitadas</h6>
                                <h3 class="mb-0">2</h3>
                                <small>En proceso</small>
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
                                <h3 class="mb-0">1</h3>
                                <small>Pendiente</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-question-circle fs-1"></i>
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
                                <h6 class="card-title">Documentos</h6>
                                <h3 class="mb-0">3</h3>
                                <small>Subidos</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-folder fs-1"></i>
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
                                    <span class="text-center">Ficha Socioeconómica</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-award mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Solicitar Beca</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-question-circle mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Solicitar Ayuda</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-folder mb-2" style="font-size: 2.5rem;"></i>
                                    <span class="text-center">Mis Documentos</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fichas Socioeconómicas -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>Fichas Socioeconómicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fichas ?? [] as $ficha): ?>
                                    <tr>
                                        <td><?= $ficha['periodo'] ?></td>
                                        <td><span class="badge bg-success"><?= $ficha['estado'] ?></span></td>
                                        <td><?= $ficha['fecha'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Ver</button>
                                            <button class="btn btn-sm btn-outline-info">PDF</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Becas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-award me-2"></i>Becas Solicitadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($becas ?? [] as $beca): ?>
                                    <tr>
                                        <td><?= $beca['tipo'] ?></td>
                                        <td><span class="badge bg-warning"><?= $beca['estado'] ?></span></td>
                                        <td><?= $beca['monto'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Ver</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Información de Bienestar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Servicios de Bienestar</h6>
                                <p class="text-muted">Información sobre los servicios disponibles para estudiantes.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h6>Información de Becas</h6>
                                <p class="text-muted">Detalles sobre los tipos de becas y requisitos.</p>
                                <a href="#" class="btn btn-sm btn-outline-success">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h6>Apoyo Psicológico</h6>
                                <p class="text-muted">Servicios de orientación y apoyo psicológico.</p>
                                <a href="#" class="btn btn-sm btn-outline-info">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simular funcionalidad de botones
$(document).ready(function() {
    $('.btn').on('click', function() {
        const action = $(this).text().trim();
        Swal.fire({
            title: 'Vista de Estudiante',
            text: `Acción: ${action} - Esta es una vista de demostración desde SuperAdmin`,
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    });
});
</script>
<?= $this->endSection() ?> 