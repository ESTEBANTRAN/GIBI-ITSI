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
                            <li class="breadcrumb-item active">Becas</li>
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
                                <h5 class="card-title text-primary mb-4">Dependencia de Becas</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Propósito</h6>
                                    <p>Facilitar el acceso a la educación mediante apoyo financiero y reconocimiento del talento académico, cultural y deportivo.</p>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Tipos de Becas Disponibles</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-star me-2"></i>Mérito Académico</h6>
                                                    <p class="card-text small">Para estudiantes con excelente rendimiento académico.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-music-note me-2"></i>Mérito Cultural</h6>
                                                    <p class="card-text small">Para estudiantes destacados en actividades culturales.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-trophy me-2"></i>Mérito Deportivo</h6>
                                                    <p class="card-text small">Para estudiantes destacados en actividades deportivas.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-heart me-2"></i>Discapacidad</h6>
                                                    <p class="card-text small">Apoyo para estudiantes con necesidades especiales.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger"><i class="bi bi-people me-2"></i>Minorías Étnicas</h6>
                                                    <p class="card-text small">Apoyo para estudiantes de comunidades indígenas.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Procedimiento de Otorgamiento</h6>
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="fw-bold">1. Postulación</h6>
                                                <p>Presentación de solicitud y documentación requerida.</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="fw-bold">2. Selección</h6>
                                                <p>Evaluación según criterios establecidos.</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="fw-bold">3. Otorgamiento</h6>
                                                <p>Publicación de resultados y firma de compromiso.</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning"></div>
                                            <div class="timeline-content">
                                                <h6 class="fw-bold">4. Seguimiento</h6>
                                                <p>Control y renovación de becas según rendimiento.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Servicios Ofrecidos</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Asesoramiento sobre requisitos y tipos de becas</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompañamiento durante el proceso de solicitud</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gestión de becas externas y colaboraciones</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Control y seguimiento de los beneficiarios</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Informes y estadísticas de becas</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Apoyo a la inclusión y equidad</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">Información de Contacto</h6>
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Dependencia de Becas</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Oficina de Bienestar Institucional</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>becas@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Importante:</strong> Las becas cubren al menos el 10% de estudiantes regulares según normativa vigente.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen de la Dependencia de Becas -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                                                                    <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Dependencia de Becas.png') . '?v=' . filemtime(FCPATH . 'sistema/assets/images/IMAGENES INFORMACION/Dependencia de Becas.png') ?>" 
                                             alt="Dependencia de Becas" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Dependencia de Becas ITSI</p>
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

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 20px;
    border-left: 2px solid #e9ecef;
    padding-bottom: 10px;
}
</style>
<?= $this->endSection() ?> 