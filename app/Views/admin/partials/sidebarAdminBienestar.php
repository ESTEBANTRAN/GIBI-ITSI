<!-- app/Views/admin/partials/sidebarAdminBienestar.php -->
<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('admin-bienestar') ?>" class="text-nowrap logo-img">
                <img src="<?= base_url('sistema/assets/images/logos/logo2.jpg?v=2') ?>" alt="Logo" />
                <span class="ms-2 fw-bold" style="font-size: 1.3rem; color: #000;">Administrador de <br>Bienestar Institucional <br></span>
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
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar') ?>" aria-expanded="false">
                        <span><i class="bi bi-house-door"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">GESTIÓN ESTUDIANTIL</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/fichas-socioeconomicas') ?>" aria-expanded="false">
                        <span><i class="bi bi-file-earmark-text"></i></span>
                        <span class="hide-menu">Fichas Socioeconómicas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/solicitudes-ayuda') ?>" aria-expanded="false">
                        <span><i class="bi bi-chat-square-dots"></i></span>
                        <span class="hide-menu">Solicitudes de Ayuda</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/estudiantes') ?>" aria-expanded="false">
                        <span><i class="bi bi-people"></i></span>
                        <span class="hide-menu">Información Estudiantil</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">SOLICITUDES</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/solicitudes-becas') ?>" aria-expanded="false">
                        <span><i class="bi bi-ticket-detailed"></i></span>
                        <span class="hide-menu">Solicitudes de Becas</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">REPORTES Y ANALÍTICA</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/reportes') ?>" aria-expanded="false">
                        <span><i class="bi bi-bar-chart"></i></span>
                        <span class="hide-menu">Reportes y Analítica</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">CONFIGURACIÓN</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/gestion-periodos') ?>" aria-expanded="false">
                        <span><i class="bi bi-calendar-range"></i></span>
                        <span class="hide-menu">Gestión de Períodos Académicos</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('admin-bienestar/configuracion-becas') ?>" aria-expanded="false">
                        <span><i class="bi bi-award-fill"></i></span>
                        <span class="hide-menu">Configurar Programas de Becas</span>
                    </a>
                </li>



            </ul>
        </nav>
    </div>
</aside> 