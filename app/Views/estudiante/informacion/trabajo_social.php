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
                            <li class="breadcrumb-item active">Trabajo Social</li>
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
                                <h5 class="card-title text-primary mb-4">Dependencia de Trabajo Social</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Equipo de Profesionales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Trabajadores sociales</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Mediadores familiares</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Áreas de Intervención</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-cash-coin me-2"></i>Asesoría Socioeconómica</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Becas y ayudas económicas</li>
                                                        <li>• Orientación para estudiantes vulnerables</li>
                                                        <li>• Apoyo para cargas familiares</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-shield-check me-2"></i>Vulnerabilidad y Riesgo</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Evaluación de casos de vulnerabilidad</li>
                                                        <li>• Apoyo a estudiantes con discapacidad</li>
                                                        <li>• Intervención en crisis sociales</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-chat-dots me-2"></i>Orientación Psicosocial</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Consejería y orientación</li>
                                                        <li>• Acompañamiento en adaptación</li>
                                                        <li>• Procesos de integración</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-shield-exclamation me-2"></i>Prevención Social</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Prevención de violencia y acoso</li>
                                                        <li>• Prevención de adicciones</li>
                                                        <li>• Promoción de derechos y equidad</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger"><i class="bi bi-mortarboard me-2"></i>Procesos Académicos</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Orientación en trámites institucionales</li>
                                                        <li>• Apoyo en reintegración académica</li>
                                                        <li>• Gestión administrativa</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-secondary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-secondary"><i class="bi bi-people me-2"></i>Integración Social</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Fomento de integración social</li>
                                                        <li>• Red de apoyo entre estudiantes</li>
                                                        <li>• Actividades colaborativas</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Servicios Especializados</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Mediación familiar</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Orientación para problemas familiares</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gestión de recursos comunitarios</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Colaboración con ONG y fundaciones</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Promoción de autonomía y empoderamiento</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Capacitación en habilidades de vida</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Investigación de necesidades sociales</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Evaluación de programas y servicios</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompañamiento en inserción laboral</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Desarrollo de habilidades laborales</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Vinculación con servicios externos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Estudios socioeconómicos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Herramientas e Instrumentos</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-clipboard-data text-primary" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Estudios Socioeconómicos</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-chat-dots text-success" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Entrevistas en Profundidad</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-lightbulb text-warning" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Planes de Intervención Social</h6>
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
                                            <h6 class="fw-bold">Dependencia de Trabajo Social</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Oficina de Bienestar Institucional</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>trabajosocial@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="bi bi-people me-2"></i>
                                            <strong>Inclusión:</strong> Servicios especializados para grupos vulnerables.
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <i class="bi bi-handshake me-2"></i>
                                            <strong>Mediación:</strong> Servicios de mediación familiar disponibles.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen de la Dependencia de Trabajo Social -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Dependencia de Trabajo Social.png') ?>" 
                                             alt="Dependencia de Trabajo Social" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Dependencia de Trabajo Social ITSI</p>
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