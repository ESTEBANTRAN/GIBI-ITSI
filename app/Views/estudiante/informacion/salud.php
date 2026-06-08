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
                            <li class="breadcrumb-item active">Servicios de Salud</li>
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
                                <h5 class="card-title text-primary mb-4">Dependencia de Salud</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Equipo de Profesionales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Médicos generales</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Enfermeros</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Psicólogos de la salud</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Nutricionistas</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Servicios Ofrecidos</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-heart-pulse me-2"></i>Atención Primaria</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Consultas médicas generales</li>
                                                        <li>• Detección de enfermedades</li>
                                                        <li>• Emergencias médicas</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-shield-check me-2"></i>Prevención</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Campañas de salud pública</li>
                                                        <li>• Vacunación</li>
                                                        <li>• Promoción de hábitos saludables</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-heart me-2"></i>Salud Mental</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Orientación psicológica</li>
                                                        <li>• Prevención de suicidio</li>
                                                        <li>• Terapias y derivación</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-activity me-2"></i>Salud Sexual</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Consejería sobre salud sexual</li>
                                                        <li>• Distribución de métodos anticonceptivos</li>
                                                        <li>• Exámenes de salud reproductiva</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Control de Adicciones</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Prevención del consumo de sustancias</li>
                                                        <li>• Tratamiento y apoyo</li>
                                                        <li>• Talleres de concientización</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-secondary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-secondary"><i class="bi bi-apple me-2"></i>Atención Nutricional</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Asesoramiento nutricional</li>
                                                        <li>• Programas de alimentación saludable</li>
                                                        <li>• Evaluación del estado nutricional</li>
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
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Atención a enfermedades crónicas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Atención a estudiantes con discapacidad</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Servicios de urgencias y primeros auxilios</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gestión de enfermedades infecciosas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Intervención en salud ocupacional</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Educación y sensibilización en salud</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Servicios de seguimiento y rehabilitación</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompañamiento en procesos postquirúrgicos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Reducción de estigmas en salud mental</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Capacitación en primeros auxilios</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Monitoreo y control de brotes</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Ergonomía y prevención de lesiones</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Herramientas e Instrumentos</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-file-medical text-primary" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Expedientes Médicos</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Protocolos de Emergencia</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-megaphone text-success" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Campañas de Prevención</h6>
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
                                            <h6 class="fw-bold">Dependencia de Salud</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Oficina de Bienestar Institucional</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>salud@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <i class="bi bi-heart-pulse me-2"></i>
                                            <strong>Servicios Gratuitos:</strong> Todos los servicios médicos son gratuitos para estudiantes.
                                        </div>
                                        
                                        <div class="alert alert-danger">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>Emergencias:</strong> Para emergencias médicas, contacta inmediatamente al departamento.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen de los Servicios de Salud -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Servicios de Salud.png') ?>" 
                                             alt="Servicios de Salud" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Servicios de Salud ITSI</p>
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