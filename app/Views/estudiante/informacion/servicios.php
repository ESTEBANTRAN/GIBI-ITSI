<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('content') ?>
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><?= $titulo ?></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('estudiante') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Información</li>
                            <li class="breadcrumb-item active">Servicios de Bienestar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title text-primary mb-4">Unidad de Bienestar Institucional</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Caracterización</h6>
                                    <p>La Unidad de Bienestar Institucional es un órgano fundamental en el Instituto Tecnologico Superior Ibarra, encargado de promover el bienestar integral de la comunidad educativa mediante la implementación de programas y estrategias que favorezcan la equidad, la inclusión y el desarrollo personal y académico de los estudiantes.</p>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Objetivos Principales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Garantizar el acceso a servicios de bienestar y apoyo integral para los estudiantes.</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Fomentar la inclusión y equidad en la educación mediante programas de becas y asistencia social.</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Promover la salud física, emocional y social de la comunidad educativa.</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Implementar estrategias de orientación y acompañamiento académico.</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Desarrollar mecanismos de prevención y atención en problemáticas sociales, psicológicas y de salud.</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Fortalecer la convivencia pacífica y la cultura de paz institucional.</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Dependencias de Servicio</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-mortarboard me-2"></i>Orientación Académica</h6>
                                                    <p class="card-text small">Asesoramiento pedagógico, tutorías y apoyo académico.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-award me-2"></i>Becas</h6>
                                                    <p class="card-text small">Gestión de becas y ayudas económicas.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-heart me-2"></i>Psicología</h6>
                                                    <p class="card-text small">Atención psicológica y apoyo emocional.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-people me-2"></i>Trabajo Social</h6>
                                                    <p class="card-text small">Apoyo socioeconómico y orientación social.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger"><i class="bi bi-heart-pulse me-2"></i>Salud</h6>
                                                    <p class="card-text small">Atención médica y promoción de la salud.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">Información de Contacto</h6>
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Unidad de Bienestar Institucional</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Instituto Tecnologico Superior Ibarra</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>bienestar@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Nota:</strong> Todos los servicios son gratuitos para estudiantes regulares del ITSI.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen de la Unidad de Bienestar Institucional -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Unidad de Bienestar Institucional.png') ?>" 
                                             alt="Unidad de Bienestar Institucional" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Unidad de Bienestar Institucional ITSI</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 