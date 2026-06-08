<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">📄 Plantillas de Documentos</h4>
                <p class="text-muted mb-0">Gestiona las plantillas utilizadas para la generación de documentos PDF</p>
            </div>
            <div class="page-title-right">
                <a href="<?= base_url('plantillas/gestionar') ?>" class="btn btn-primary">
                    <i class="bi bi-gear me-1"></i>Gestionar Plantillas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="bi bi-file-earmark-pdf fs-1 mb-2 d-block"></i>
                <h3 class="mb-0"><?= count($plantillas ?? []) ?></h3>
                <p class="mb-0">Plantillas Disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="bi bi-filetype-docx fs-1 mb-2 d-block"></i>
                <h3 class="mb-0">Word</h3>
                <p class="mb-0">Formato Principal</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="bi bi-printer fs-1 mb-2 d-block"></i>
                <h3 class="mb-0">PDF</h3>
                <p class="mb-0">Generación de Documentos</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">📋 Listado de Plantillas</h5>
        <span class="badge bg-primary fs-6"><?= count($plantillas ?? []) ?> plantillas</span>
    </div>
    <div class="card-body">
        <?php if (empty($plantillas)): ?>
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-plus fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No hay plantillas disponibles</h5>
            <p class="text-muted mb-3">Aún no se han subido plantillas al sistema.</p>
            <a href="<?= base_url('plantillas/gestionar') ?>" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i>Subir Plantilla
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Archivo</th>
                        <th>Formato</th>
                        <th>Tamaño</th>
                        <th>Última Modificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plantillas as $plantilla): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-text fs-4 text-primary me-3"></i>
                                <div>
                                    <div class="fw-bold"><?= esc($plantilla['nombre'] ?? 'Sin nombre') ?></div>
                                    <small class="text-muted"><?= esc($plantilla['archivo'] ?? '') ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $ext = pathinfo($plantilla['archivo'] ?? '', PATHINFO_EXTENSION);
                            $badgeClass = match(strtolower($ext)) {
                                'docx' => 'bg-primary',
                                'pdf' => 'bg-danger',
                                'doc' => 'bg-info',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= strtoupper($ext) ?></span>
                        </td>
                        <td><?= esc($plantilla['tamano'] ?? '—') ?></td>
                        <td><?= esc($plantilla['fecha_modificacion'] ?? '—') ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?= base_url('plantillas/vista-previa/' . urlencode($plantilla['archivo'] ?? '')) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Vista Previa">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarPlantilla('<?= esc($plantilla['archivo'] ?? '') ?>')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-filetype-docx display-4 text-primary mb-3"></i>
                <h5>Probar Plantilla Word</h5>
                <p class="text-muted">Genera un PDF de prueba usando una plantilla de Word existente</p>
                <a href="<?= base_url('plantillas/probar-word') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-play-circle me-1"></i>Probar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-code-square display-4 text-success mb-3"></i>
                <h5>Probar Plantilla Personalizada</h5>
                <p class="text-muted">Genera un PDF de prueba con la plantilla personalizada del sistema</p>
                <a href="<?= base_url('plantillas/probar-personalizada') ?>" class="btn btn-outline-success">
                    <i class="bi bi-play-circle me-1"></i>Probar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarPlantilla(archivo) {
    Swal.fire({
        title: '¿Eliminar plantilla?',
        text: `Se eliminará: ${archivo}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('plantillas/eliminar') ?>/${encodeURIComponent(archivo)}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminada', data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Error', data.error || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error de conexión', 'error');
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
