<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">👁️ Vista Previa de Plantilla</h4>
                <p class="text-muted mb-0">Información detallada de la plantilla seleccionada</p>
            </div>
            <div class="page-title-right">
                <a href="<?= base_url('plantillas/gestionar') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Detalles de la Plantilla
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($plantilla)): ?>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nombre del archivo</h6>
                        <p class="fw-bold"><?= esc($plantilla['archivo'] ?? $plantilla['nombre'] ?? 'No disponible') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Formato</h6>
                        <?php
                        $ext = pathinfo($plantilla['archivo'] ?? '', PATHINFO_EXTENSION);
                        $badgeClass = match(strtolower($ext)) {
                            'docx' => 'bg-primary',
                            'pdf' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        ?>
                        <p><span class="badge <?= $badgeClass ?> fs-6"><?= strtoupper($ext ?: 'DESCONOCIDO') ?></span></p>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Tamaño</h6>
                        <p><?= esc($plantilla['tamano'] ?? 'No disponible') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Última Modificación</h6>
                        <p><?= esc($plantilla['fecha_modificacion'] ?? 'No disponible') ?></p>
                    </div>
                </div>
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Ruta</h6>
                    <code class="bg-light p-2 d-block rounded">
                        <?= FCPATH ?>sistema/assets/plantilla/<?= esc($plantilla['archivo'] ?? '') ?>
                    </code>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h5 class="mt-3 text-muted">No se encontró información de la plantilla</h5>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>Acciones
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('plantillas/probar-word') ?>" class="btn btn-primary">
                        <i class="bi bi-play-circle me-1"></i>Probar Plantilla
                    </a>
                    <a href="<?= base_url('plantillas') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-list me-1"></i>Ver Todas
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Tipos de Plantillas
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <span class="badge bg-primary me-2">.docx</span>
                        Plantillas de Word
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-danger me-2">.pdf</span>
                        Documentos generados
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
