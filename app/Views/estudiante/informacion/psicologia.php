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
                            <li class="breadcrumb-item active">Apoyo Psicológico</li>
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
                                <h5 class="card-title text-primary mb-4">Dependencia de Psicología</h5>
                                <p class="text-muted mb-4"><?= $descripcion ?></p>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Equipo de Profesionales</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Psicólogos clínicos y educativos</li>
                                        <li class="mb-2"><i class="bi bi-person-check me-2"></i>Especialistas en orientación y terapia familiar</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">Áreas de Intervención</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary"><i class="bi bi-person me-2"></i>Atención Individual</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Consultas y terapias individuales</li>
                                                        <li>• Crisis emocionales</li>
                                                        <li>• Intervención en emergencias</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success"><i class="bi bi-people me-2"></i>Atención Grupal</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Terapias grupales</li>
                                                        <li>• Talleres de desarrollo emocional</li>
                                                        <li>• Dinámicas grupales</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info"><i class="bi bi-shield-check me-2"></i>Prevención</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Programas preventivos</li>
                                                        <li>• Charlas y seminarios</li>
                                                        <li>• Campañas de sensibilización</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning"><i class="bi bi-mortarboard me-2"></i>Apoyo Académico</h6>
                                                    <ul class="list-unstyled small">
                                                        <li>• Manejo del estrés académico</li>
                                                        <li>• Técnicas de estudio</li>
                                                        <li>• Dificultades de aprendizaje</li>
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
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Diagnósticos psicológicos individuales y colectivos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Programas de apoyo emocional y adaptación escolar</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Atención a problemas cognitivos, emocionales y sociales</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Evaluación y seguimiento de casos atendidos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Intervención en crisis y violencia</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Psicoeducación y desarrollo de habilidades</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompañamiento a estudiantes con necesidades específicas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Investigación sobre salud mental universitaria</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Orientación para la transición académica</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Referencias a servicios externos especializados</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Trabajo interdisciplinario con otras áreas</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Capacitación para docentes y personal</li>
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
                                                <h6 class="mt-2">Pruebas Psicológicas</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-chat-dots text-success" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Entrevistas Estructuradas</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <i class="bi bi-lightbulb text-warning" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Terapia Cognitivo-Conductual</h6>
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
                                            <h6 class="fw-bold">Dependencia de Psicología</h6>
                                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Oficina de Bienestar Institucional</p>
                                            <p class="mb-1"><i class="bi bi-telephone me-2"></i>062-952-535</p>
                                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>psicologia@itsi.edu.ec</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Horarios de Atención</h6>
                                            <p class="mb-1"><i class="bi bi-clock me-2"></i>Lunes a Viernes</p>
                                            <p class="mb-1"><i class="bi bi-time me-2"></i>08:00 - 17:00</p>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="bi bi-shield-check me-2"></i>
                                            <strong>Confidencialidad:</strong> Todos los servicios son completamente confidenciales.
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>Emergencias:</strong> Para crisis emocionales, contacta inmediatamente al departamento.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Imagen del Apoyo Psicológico -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <img src="<?= base_url('sistema/assets/images/IMAGENES INFORMACION/Apoyo Psicológico.png') ?>" 
                                             alt="Apoyo Psicológico" 
                                             class="img-fluid rounded" 
                                             style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <p class="text-muted mt-2 small">Apoyo Psicológico ITSI</p>
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