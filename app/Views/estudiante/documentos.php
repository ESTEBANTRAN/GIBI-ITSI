<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('styles') ?>
<style>
/* ===== Documentos del Estudiante - Diseño Moderno ===== */

/* Info Alert - Glassmorphism */
.glass-alert {
    background: rgba(102, 126, 234, 0.08);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.1);
}

/* Stat Cards */
.doc-stat-card {
    border: none;
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: default;
}
.doc-stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    transform: translate(30%, -30%);
}
.doc-stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12) !important;
}
.doc-stat-card .stat-icon {
    font-size: 2.2rem;
    opacity: 0.9;
    transition: all 0.4s ease;
}
.doc-stat-card:hover .stat-icon {
    transform: scale(1.15) rotate(-5deg);
}
.doc-stat-card h4 {
    font-weight: 800;
    font-size: 2rem;
}

/* Table Modern */
.doc-table {
    border-radius: 12px;
    overflow: hidden;
}
.doc-table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem;
    border: none;
}
.doc-table thead th:first-child {
    border-radius: 12px 0 0 0;
}
.doc-table thead th:last-child {
    border-radius: 0 12px 0 0;
}
.doc-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.doc-table tbody tr:hover {
    background: linear-gradient(90deg, rgba(102,126,234,0.05) 0%, transparent 100%);
    transform: scale(1.005);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.doc-table tbody tr:last-child {
    border-bottom: none;
}
.doc-table tbody td {
    padding: 0.9rem 1rem;
    vertical-align: middle;
}

/* Badges modernos */
.doc-badge {
    border-radius: 50px;
    padding: 0.4rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

/* Period title */
.period-title {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1.25rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(102,126,234,0.3);
}

/* Card sections */
.doc-section-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.06);
    transition: box-shadow 0.3s ease;
}
.doc-section-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

/* Animations */
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-slide-up {
    animation: slideUp 0.5s ease forwards;
}
.doc-stat-card:nth-child(1) { animation-delay: 0.05s; }
.doc-stat-card:nth-child(2) { animation-delay: 0.1s; }
.doc-stat-card:nth-child(3) { animation-delay: 0.15s; }
.doc-stat-card:nth-child(4) { animation-delay: 0.2s; }

/* Empty state */
.empty-state-icon {
    font-size: 4rem;
    opacity: 0.5;
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Responsive */
@media (max-width: 768px) {
    .doc-stat-card h4 {
        font-size: 1.5rem;
    }
    .doc-stat-card .stat-icon {
        font-size: 1.6rem;
    }
    .period-title {
        font-size: 0.75rem;
        padding: 0.4rem 1rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>Mis Documentos<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold"><i class="bi bi-folder me-2 text-primary"></i>Mis Documentos</h4>
                <p class="text-muted mb-0">Documentos subidos en solicitudes de becas</p>
            </div>
        </div>
    </div>
</div>

<!-- Información -->
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-alert p-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill text-primary me-3 fs-4 flex-shrink-0 mt-1"></i>
                <div>
                    <strong class="text-primary">Información:</strong>
                    <p class="mb-0 text-muted">En esta sección puedes visualizar todos los documentos que has subido al momento de solicitar becas. Los documentos se organizan por beca y período académico. Para subir nuevos documentos, debes solicitar una beca desde la sección de Becas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen Estadístico -->
<?php if (!empty($documentos)): ?>
<?php
$totalDocumentos = count($documentos);
$documentosPendientes = count(array_filter($documentos, fn($d) => $d['estado'] === 'Pendiente'));
$documentosAprobados = count(array_filter($documentos, fn($d) => $d['estado'] === 'Aprobado'));
$documentosRechazados = count(array_filter($documentos, fn($d) => $d['estado'] === 'Rechazado'));
?>
<div class="row g-3 mb-4">
    <div class="col-md-3 animate-slide-up">
        <div class="doc-stat-card card shadow-sm text-white" style="background: linear-gradient(135deg, #667eea 0%, #5a67d8 100%);">
            <div class="card-body text-center py-4">
                <i class="bi bi-folder stat-icon mb-2 d-block"></i>
                <h4 class="mb-0 fw-bold"><?= $totalDocumentos ?></h4>
                <p class="mb-0 small opacity-75">Total Documentos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 animate-slide-up">
        <div class="doc-stat-card card shadow-sm text-white" style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);">
            <div class="card-body text-center py-4">
                <i class="bi bi-clock stat-icon mb-2 d-block"></i>
                <h4 class="mb-0 fw-bold"><?= $documentosPendientes ?></h4>
                <p class="mb-0 small opacity-75">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 animate-slide-up">
        <div class="doc-stat-card card shadow-sm text-white" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
            <div class="card-body text-center py-4">
                <i class="bi bi-check-circle stat-icon mb-2 d-block"></i>
                <h4 class="mb-0 fw-bold"><?= $documentosAprobados ?></h4>
                <p class="mb-0 small opacity-75">Aprobados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 animate-slide-up">
        <div class="doc-stat-card card shadow-sm text-white" style="background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);">
            <div class="card-body text-center py-4">
                <i class="bi bi-x-circle stat-icon mb-2 d-block"></i>
                <h4 class="mb-0 fw-bold"><?= $documentosRechazados ?></h4>
                <p class="mb-0 small opacity-75">Rechazados</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Documentos -->
<div class="row">
    <div class="col-12">
        <div class="doc-section-card card">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-folder me-2 text-primary"></i>Documentos de Solicitudes de Becas
                    </h5>
                    <?php if (!empty($documentos)): ?>
                    <input type="text" class="form-control form-control-sm" id="docSearchInput" placeholder="🔍 Buscar documentos..." style="max-width: 250px;">
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($documentos)): ?>
                    <?php
                    // Agrupar documentos por período académico
                    $documentosPorPeriodo = [];
                    foreach ($documentos as $documento) {
                        $periodoId = $documento['periodo_id'];
                        if (!isset($documentosPorPeriodo[$periodoId])) {
                            $documentosPorPeriodo[$periodoId] = [
                                'periodo_nombre' => $documento['periodo_nombre'],
                                'documentos' => []
                            ];
                        }
                        $documentosPorPeriodo[$periodoId]['documentos'][] = $documento;
                    }
                    ?>
                    
                    <?php foreach ($documentosPorPeriodo as $periodoId => $periodoData): ?>
                    <div class="period-section mb-4">
                        <div class="period-title mb-3">
                            <i class="bi bi-calendar me-2"></i>
                            Período: <?= htmlspecialchars($periodoData['periodo_nombre']) ?>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table doc-table mb-0" data-search="<?= htmlspecialchars($periodoData['periodo_nombre']) ?>">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Tipo</th>
                                        <th>Beca</th>
                                        <th>Estado</th>
                                        <th>Tamaño</th>
                                        <th>Subida</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($periodoData['documentos'] as $documento): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
                                                <div>
                                                    <strong class="small"><?= htmlspecialchars($documento['nombre_archivo']) ?></strong>
                                                    <?php if ($documento['descripcion']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($documento['descripcion']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary doc-badge">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($documento['nombre_documento']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-info small fw-semibold">
                                                <i class="bi bi-award me-1"></i><?= htmlspecialchars($documento['nombre_beca']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $estadoClass = '';
                                            $estadoIcon = '';
                                            switch ($documento['estado']) {
                                                case 'Pendiente':
                                                    $estadoClass = 'bg-warning';
                                                    $estadoIcon = 'bi-clock';
                                                    break;
                                                case 'Aprobado':
                                                    $estadoClass = 'bg-success';
                                                    $estadoIcon = 'bi-check-circle';
                                                    break;
                                                case 'Rechazado':
                                                    $estadoClass = 'bg-danger';
                                                    $estadoIcon = 'bi-x-circle';
                                                    break;
                                                default:
                                                    $estadoClass = 'bg-secondary';
                                                    $estadoIcon = 'bi-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?= $estadoClass ?> doc-badge">
                                                <i class="bi <?= $estadoIcon ?> me-1"></i><?= $documento['estado'] ?>
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?= number_format(($documento['tamano_archivo'] ?? 0) / 1024, 2) ?> MB</small></td>
                                        <td><small class="text-muted"><?= date('d/m/Y', strtotime($documento['fecha_subida'])) ?></small></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="descargarDocumento(<?= $documento['id'] ?>)">
                                                <i class="bi bi-download me-1"></i>Descargar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open empty-state-icon d-block text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">No tienes documentos subidos</h5>
                        <p class="text-muted small mb-4">Los documentos aparecerán aquí cuando solicites una beca y subas los archivos requeridos.</p>
                        <a href="<?= base_url('estudiante/becas') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-award me-2"></i>Ver Becas Disponibles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Descargar documento
function descargarDocumento(id) {
    window.open('<?= base_url('estudiante/descargar-documento-beca') ?>/' + id, '_blank');
}

// Búsqueda en vivo de documentos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('docSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.doc-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });                    // Mostrar/ocultar secciones de período si no tienen filas visibles
                    document.querySelectorAll('.period-section').forEach(section => {
                const visibleRows = section.querySelectorAll('tbody tr[style*="display: none"]');
                const totalRows = section.querySelectorAll('tbody tr').length;
                if (visibleRows.length === totalRows && totalRows > 0) {
                    section.style.display = 'none';
                } else {
                    section.style.display = '';
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?> 