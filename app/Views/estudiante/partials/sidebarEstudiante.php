<!-- app/Views/estudiante/partials/sidebarEstudiante.php -->
<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('estudiante') ?>" class="text-nowrap logo-img">
                <img src="<?= base_url('sistema/assets/images/logos/logo2.jpg?v=2') ?>" alt="Logo" />
                <span class="ms-2 fw-bold" style="font-size: 1.3rem; color: #000;">Estudiante Bienestar <br> Institucional <br></span>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i> 
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">INICIO</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante') ?>" aria-expanded="false">
                        <span><i class="bi bi-house-door"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">FORMULARIOS</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/ficha-socioeconomica') ?>" aria-expanded="false">
                        <span><i class="bi bi-file-earmark-text"></i></span>
                        <span class="hide-menu">Ficha Socioeconómica</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/documentos') ?>" aria-expanded="false">
                        <span><i class="bi bi-folder"></i></span>
                        <span class="hide-menu">Mis Documentos</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">BECAS</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/becas') ?>" aria-expanded="false">
                        <span><i class="bi bi-award"></i></span>
                        <span class="hide-menu">Solicitudes de Becas</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">SOLICITUDES</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/solicitudes-ayuda') ?>" aria-expanded="false">
                        <span><i class="bi bi-question-circle"></i></span>
                        <span class="hide-menu">Solicitudes de Ayuda</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">INFORMACIÓN</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/servicios') ?>" aria-expanded="false">
                        <span><i class="bi bi-info-circle"></i></span>
                        <span class="hide-menu">Servicios de Bienestar</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/becas') ?>" aria-expanded="false">
                        <span><i class="bi bi-award"></i></span>
                        <span class="hide-menu">Información de Becas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/psicologia') ?>" aria-expanded="false">
                        <span><i class="bi bi-heart"></i></span>
                        <span class="hide-menu">Apoyo Psicológico</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/salud') ?>" aria-expanded="false">
                        <span><i class="bi bi-heart-pulse"></i></span>
                        <span class="hide-menu">Servicios de Salud</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/trabajo-social') ?>" aria-expanded="false">
                        <span><i class="bi bi-people"></i></span>
                        <span class="hide-menu">Trabajo Social</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('estudiante/informacion/orientacion-academica') ?>" aria-expanded="false">
                        <span><i class="bi bi-mortarboard"></i></span>
                        <span class="hide-menu">Orientación Académica</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside> 