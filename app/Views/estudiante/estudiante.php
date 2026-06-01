<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('styles') ?>
<style>
/* ===== Dashboard Estudiante - Diseño Moderno ===== */

/* Welcome Hero */
.welcome-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
}
.welcome-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
}
.welcome-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.welcome-hero h4 {
    color: #fff;
    font-weight: 700;
    position: relative;
    z-index: 1;
}
.welcome-hero p {
    color: rgba(255,255,255,0.85);
    position: relative;
    z-index: 1;
}
.welcome-hero .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}
.welcome-hero .breadcrumb-item,
.welcome-hero .breadcrumb-item a {
    color: rgba(255,255,255,0.75);
}
.welcome-hero .breadcrumb-item.active {
    color: #fff;
}
.welcome-hero .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}

/* Stats Cards - Glassmorphism */
.stat-card {
    border: none;
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: default;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}
.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}
.stat-card:hover::before {
    opacity: 1;
}
.stat-card .card-body {
    position: relative;
    z-index: 1;
}
.stat-card .stat-icon {
    width: 65px;
    height: 65px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    transition: all 0.4s ease;
}
.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(-5deg);
}
.stat-card .stat-number {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1.2;
    transition: all 0.3s ease;
}
.stat-card:hover .stat-number {
    transform: scale(1.05);
}
.stat-card .stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.85;
}

/* Quick Action Cards */
.quick-action-card {
    border: 2px solid transparent;
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}
.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(var(--bs-primary-rgb),0.05) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}
.quick-action-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}
.quick-action-card:hover::before {
    opacity: 1;
}
.quick-action-card .action-icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    transition: all 0.4s ease;
    margin-bottom: 0.75rem;
}
.quick-action-card:hover .action-icon-wrapper {
    transform: scale(1.1) rotate(-3deg);
}

/* Activity Section */
.activity-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}
.activity-item {
    padding: 1rem 1.25rem;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}
.activity-item:hover {
    background: rgba(102,126,234,0.05);
    border-left-color: #667eea;
    transform: translateX(4px);
}
.activity-item .activity-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Section Headers */
.section-header {
    position: relative;
    padding-bottom: 0.75rem;
    margin-bottom: 1.5rem;
}
.section-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    border-radius: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(102,126,234,0.4); }
    50% { box-shadow: 0 0 0 15px rgba(102,126,234,0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease forwards;
}
.animate-fade-in-left {
    animation: fadeInLeft 0.6s ease forwards;
}
.animate-fade-in-right {
    animation: fadeInRight 0.6s ease forwards;
}
.g-4 > .col-xl-3:nth-child(1) { animation-delay: 0.1s; }
.g-4 > .col-xl-3:nth-child(2) { animation-delay: 0.2s; }
.g-4 > .col-xl-3:nth-child(3) { animation-delay: 0.3s; }
.g-4 > .col-xl-3:nth-child(4) { animation-delay: 0.4s; }

/* Responsive */
@media (max-width: 768px) {
    .welcome-hero {
        padding: 1.5rem;
    }
    .stat-card .stat-number {
        font-size: 1.8rem;
    }
    .quick-action-card .action-icon-wrapper {
        width: 55px;
        height: 55px;
        font-size: 1.5rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Welcome Hero -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-hero shadow-lg">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="<?= base_url('index.php/estudiante') ?>"><i class="bi bi-house-door me-1"></i>Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                    <h4 class="mb-1"><i class="bi bi-hand-wave me-2"></i>Bienvenido/a, <?= session('nombre') ?> <?= session('apellido') ?></h4>
                    <p class="mb-0">Panel de control del estudiante — Gestiona tus actividades académicas y de bienestar</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 animate-fade-in-up">
        <div class="stat-card card shadow-sm" style="background: linear-gradient(135deg, #e8f0fe 0%, #d4e4fc 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label text-primary mb-2">Fichas Socioeconómicas</p>
                        <h3 class="stat-number text-primary mb-0"><?= $estadisticas['total_fichas'] ?? 0 ?></h3>
                        <small class="text-primary opacity-75">Total creadas</small>
                    </div>
                    <div class="stat-icon bg-primary text-white shadow-sm">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 animate-fade-in-up">
        <div class="stat-card card shadow-sm" style="background: linear-gradient(135deg, #e6f7ee 0%, #c8f0d9 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label text-success mb-2">Fichas Aprobadas</p>
                        <h3 class="stat-number text-success mb-0"><?= $estadisticas['fichas_aprobadas'] ?? 0 ?></h3>
                        <small class="text-success opacity-75">✔ Aprobadas</small>
                    </div>
                    <div class="stat-icon bg-success text-white shadow-sm">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 animate-fade-in-up">
        <div class="stat-card card shadow-sm" style="background: linear-gradient(135deg, #fff8e6 0%, #ffefc8 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label text-warning mb-2">Solicitudes Becas</p>
                        <h3 class="stat-number text-warning mb-0"><?= $estadisticas['solicitudes_becas'] ?? 0 ?></h3>
                        <small class="text-warning opacity-75">En proceso</small>
                    </div>
                    <div class="stat-icon bg-warning text-white shadow-sm">
                        <i class="bi bi-award"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 animate-fade-in-up">
        <div class="stat-card card shadow-sm" style="background: linear-gradient(135deg, #e6f7ff 0%, #c8effc 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label text-info mb-2">Solicitudes Ayuda</p>
                        <h3 class="stat-number text-info mb-0"><?= $estadisticas['solicitudes_ayuda'] ?? 0 ?></h3>
                        <small class="text-info opacity-75">Registradas</small>
                    </div>
                    <div class="stat-icon bg-info text-white shadow-sm">
                        <i class="bi bi-question-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="section-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-lightning-fill me-2 text-warning"></i>Acciones Rápidas
                    </h5>
                    <small class="text-muted">Acceso directo a tus herramientas</small>
                </div>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="<?= base_url('index.php/estudiante/ficha-socioeconomica') ?>" class="quick-action-card card border-0 shadow-sm text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="action-icon-wrapper bg-primary-subtle">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                            </div>
                            <span class="fw-semibold text-dark small">Nueva Ficha Socioeconómica</span>
                            <small class="text-muted mt-1">Completa tus datos</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= base_url('index.php/estudiante/becas') ?>" class="quick-action-card card border-0 shadow-sm text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="action-icon-wrapper bg-success-subtle">
                                <i class="bi bi-award text-success"></i>
                            </div>
                            <span class="fw-semibold text-dark small">Solicitar Beca</span>
                            <small class="text-muted mt-1">Postula a becas</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= base_url('index.php/estudiante/solicitudes-ayuda') ?>" class="quick-action-card card border-0 shadow-sm text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="action-icon-wrapper bg-warning-subtle">
                                <i class="bi bi-question-circle text-warning"></i>
                            </div>
                            <span class="fw-semibold text-dark small">Solicitar Ayuda</span>
                            <small class="text-muted mt-1">Recibe apoyo</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= base_url('index.php/estudiante/documentos') ?>" class="quick-action-card card border-0 shadow-sm text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="action-icon-wrapper bg-info-subtle">
                                <i class="bi bi-folder text-info"></i>
                            </div>
                            <span class="fw-semibold text-dark small">Mis Documentos</span>
                            <small class="text-muted mt-1">Gestiona archivos</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= view('partials/footer') ?>
