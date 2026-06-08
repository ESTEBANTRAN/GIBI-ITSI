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
                            <li class="breadcrumb-item active">Orientación Académica</li>
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
                                <h5 class="card-title text-primary mb-4">Dependencia de Servicio y Orientación Académica</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Equipo de Profesionales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Docentes</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Psicopedagogos</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Asesores tecnológicos</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Funciones Principales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Brindar asesoramiento pedagógico y metodológico a estudiantes en riesgo académico</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Implementar programas de acompañamiento académico y tutorías</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Facilitar el acceso a recursos tecnológicos y didácticos</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Coordinar con docentes estrategias de refuerzo académico</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Áreas de Intervención</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-mortarboard me-2"></i>Orientación Académica</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Asesoramiento sobre elección de carrera</li>
                                                        <li>• Guía para objetivos académicos</li>
                                                        <li>• Planificación de carga de materias</li>
                                                        <li>• Manejo de carga académica</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-heart me-2"></i>Desarrollo Personal</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Apoyo psicológico y emocional</li>
                                                        <li>• Desarrollo de habilidades socioemocionales</li>
                                                        <li>• Talleres de bienestar</li>
                                                        <li>• Gestión del tiempo</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-shield-check me-2"></i>Estudiantes Vulnerables</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Acompañamiento a estudiantes con discapacidades</li>
                                                        <li>• Programas de integración</li>
                                                        <li>• Políticas de inclusión y equidad</li>
                                                        <li>• Adaptación cultural</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-globe me-2"></i>Movilidad Académica</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Programas de intercambio estudiantil</li>
                                                        <li>• Pasantías y movilidad internacional</li>
                                                        <li>• Postulación a becas</li>
                                                        <li>• Ayudas económicas</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger"><i class="bi bi-person-workspace me-2"></i>Tutorías Académicas</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Programas de tutoría</li>
                                                        <li>• Estrategias de seguimiento</li>
                                                        <li>• Apoyo en materias específicas</li>
                                                        <li>• Evolución académica</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-secondary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-secondary"><i class="bi bi-briefcase me-2"></i>Competencias Profesionales</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Talleres de habilidades profesionales</li>
                                                        <li>• Comunicación y liderazgo</li>
                                                        <li>• Emprendimiento</li>
                                                        <li>• Vinculación laboral</li>
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
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompañamiento en procesos de graduación</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Información sobre requisitos de titulación</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Orientación para trabajos de investigación</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Asesoramiento en tesis y proyectos de grado</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Investigación sobre necesidades académicas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Evaluación continua de programas</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Información y difusión de recursos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Asesoramiento sobre tecnologías educativas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Oportunidades educativas internas y externas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Evaluaciones diagnósticas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Tutorías personalizadas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Plataformas de aprendizaje virtual</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Herramientas Teórico-Metodológicas</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-clipboard-check text-primary" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Evaluaciones Diagnósticas</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Tutorías Personalizadas</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-laptop text-warning" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Plataformas Virtuales</h6>
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
                                            <h6 class="fw-bold">Dependencia de Orientación Académica</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Oficina de Bienestar Institucional</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>orientacion@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="bi bi-mortarboard me-2"></i>
                                            <strong>Asesoramiento:</strong> Servicios de orientación académica personalizada.
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <i class="bi bi-people me-2"></i>
                                            <strong>Tutorías:</strong> Programas de acompañamiento académico disponibles.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen de la Dependencia de Servicio y Orientación Académica -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Dependencia de Servicio y Orientación Académica.png') ?>" 
                                             alt="Dependencia de Servicio y Orientación Académica" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Dependencia de Servicio y Orientación Académica ITSI</p>
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