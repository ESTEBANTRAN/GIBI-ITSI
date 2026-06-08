<!-- app/Views/GlobalAdmin/partials/sidebarGlobalAdmin.php -->
<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('global-admin/dashboard') ?>" class="text-nowrap logo-img">
                <img src="<?= base_url('sistema/assets/images/logos/logo.jpg') ?>" alt="Logo" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" />
                <span class="ms-2 fw-bold" style="font-size: 1.3rem; color: #000;">Super Administrador</span>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">PANEL PRINCIPAL</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/dashboard') ?>" aria-expanded="false">
                        <span><i class="bi bi-house-door"></i></span>
                        <span class="hide-menu">Dashboard Global</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">GESTIÓN DE USUARIOS</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/usuarios') ?>" aria-expanded="false">
                        <span><i class="bi bi-people"></i></span>
                        <span class="hide-menu">Gestión de Usuarios</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/roles') ?>" aria-expanded="false">
                        <span><i class="bi bi-shield-lock"></i></span>
                        <span class="hide-menu">Gestión de Roles</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">ADMINISTRACIÓN DEL SISTEMA</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/configuracion') ?>" aria-expanded="false">
                        <span><i class="bi bi-gear"></i></span>
                        <span class="hide-menu">Configuración del Sistema</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/respaldos') ?>" aria-expanded="false">
                        <span><i class="bi bi-database-check"></i></span>
                        <span class="hide-menu">Gestión de Respaldos</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/logs') ?>" aria-expanded="false">
                        <span><i class="bi bi-file-text"></i></span>
                        <span class="hide-menu">Logs del Sistema</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">ESTADÍSTICAS Y MONITOREO</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/estadisticas') ?>" aria-expanded="false">
                        <span><i class="bi bi-bar-chart"></i></span>
                        <span class="hide-menu">Estadísticas Globales</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">ACCESO RÁPIDO</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/vista-admin-bienestar') ?>" aria-expanded="false">
                        <span><i class="bi bi-arrow-right-circle"></i></span>
                        <span class="hide-menu">Ver vista Admin Bienestar</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('global-admin/vista-estudiante') ?>" aria-expanded="false">
                        <span><i class="bi bi-arrow-right-circle"></i></span>
                        <span class="hide-menu">Ver vista Estudiante</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside> 