<?= $this->extend('layouts/mainAdmin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">⚙️ Gestión de Plantillas</h4>
                <p class="text-muted mb-0">Administra las plantillas de documentos del sistema</p>
            </div>
            <div class="page-title-right">
                <a href="<?= base_url('plantillas') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Formulario de Subida -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-upload me-2"></i>Subir Nueva Plantilla
                </h5>
            </div>
            <div class="card-body">
                <form id="formSubirPlantilla" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="plantilla" class="form-label">Archivo Plantilla (.docx)</label>
                        <input type="file" class="form-control" id="plantilla" name="plantilla" 
                               accept=".docx" required>
                        <small class="text-muted">Solo archivos .docx (máx. 10MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cloud-upload me-1"></i>Subir Plantilla
                    </button>
                </form>
                <div id="uploadProgress" class="mt-3 d-none">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 100%">Subiendo...</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Información
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Formatos aceptados: .docx
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Tamaño máximo: 10MB
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Las plantillas se almacenan en: sistema/assets/plantilla/
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Listado de Plantillas -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-files me-2"></i>Plantillas Existentes
                </h5>
                <span class="badge bg-primary"><?= count($plantillas ?? []) ?> archivos</span>
            </div>
            <div class="card-body">
                <?php if (empty($plantillas)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No hay plantillas subidas</h5>
                    <p class="text-muted">Usa el formulario de la izquierda para subir tu primera plantilla</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Modificado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plantillas as $plantilla): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-word fs-4 text-primary me-2"></i>
                                        <span><?= esc($plantilla['archivo'] ?? $plantilla['nombre'] ?? '—') ?></span>
                                    </div>
                                </td>
                                <td><?= esc($plantilla['tamano'] ?? '—') ?></td>
                                <td><?= esc($plantilla['fecha_modificacion'] ?? '—') ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('plantillas/vista-previa/' . urlencode($plantilla['archivo'] ?? $plantilla['nombre'] ?? '')) ?>" 
                                           class="btn btn-outline-primary" title="Vista Previa">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="eliminarPlantilla('<?= esc($plantilla['archivo'] ?? $plantilla['nombre'] ?? '') ?>')" 
                                                title="Eliminar">
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
    </div>
</div>

<script>
document.getElementById('formSubirPlantilla').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressBar = document.getElementById('uploadProgress');
    progressBar.classList.remove('d-none');
    
    fetch('<?= base_url('plantillas/subir') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressBar.classList.add('d-none');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Plantilla subida',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire('Error', data.error || 'Error al subir plantilla', 'error');
        }
    })
    .catch(error => {
        progressBar.classList.add('d-none');
        Swal.fire('Error', 'Error de conexión', 'error');
    });
});

function eliminarPlantilla(archivo) {
    Swal.fire({
        title: '¿Eliminar plantilla?',
        text: `Se eliminará permanentemente: ${archivo}`,
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
