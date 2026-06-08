<!-- app/Views/layouts/mainEstudiante.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienestar Estudiantil - Estudiante</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('sistema/assets/images/logos/faviconV2.png') ?>" />

    <!-- ESTILOS -->
    <link rel="stylesheet" href="<?= base_url('sistema/assets/css/styles.min.css') ?>" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos personalizados - Añadir esta línea -->
    <link rel="stylesheet" href="<?= base_url('sistema/assets/css/custom.css') ?>?v=<?= time() ?>" />
    <!-- jQuery (must be first) -->
    <script src="<?= base_url('sistema/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
    <?= csrf_meta() ?>
    <!-- FIX: Estilos inline para eliminar el gap del header -->
    <style>
        .app-header { background: transparent !important; padding: 0 !important; margin: 0 !important; top: 0 !important; }
        #main-wrapper[data-layout=vertical][data-header-position=fixed] .app-header { position: fixed !important; top: 0 !important; z-index: 50 !important; }
        .app-header .navbar { margin: 0 !important; border-radius: 0 !important; }
        @media (min-width: 1200px) { #main-wrapper[data-layout=vertical][data-header-position=fixed] .app-header { width: calc(100% - 300px) !important; } }
        #main-wrapper[data-layout=vertical][data-header-position=fixed] .body-wrapper > .container-fluid { padding-top: 70px !important; }
        body > *:not(.page-wrapper):not(script):not(link):not(style) { display: none !important; }
    </style>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar -->
        <?php if(isset($sidebar)): ?>
            <?= $this->include($sidebar); ?>
        <?php else: ?>
            <?= $this->include('estudiante/partials/sidebarEstudiante'); ?>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div class="body-wrapper">
            <!-- Navbar -->
            <?= $this->include('partials/navbar'); ?>
            
            <!-- Content -->
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
            
            <!-- Modal Section -->
            <div class="container-fluid">
                <?= $this->renderSection('modal') ?>
            </div>
            
            <!-- Footer -->
            <?= $this->include('partials/footer') ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('sistema/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- ApexCharts solo se carga si la página lo requiere -->
    <?php if(isset($cargar_apexcharts) && $cargar_apexcharts): ?>
        <script src="<?= base_url('sistema/assets/libs/apexcharts/dist/apexcharts.min.js') ?>"></script>
    <?php endif; ?>
    <script src="<?= base_url('sistema/assets/libs/simplebar/dist/simplebar.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/js/app.min.js') ?>"></script>
    <!-- dashboard.js solo se carga si la página lo requiere -->
    <?php if(isset($cargar_dashboard) && $cargar_dashboard): ?>
        <script src="<?= base_url('sistema/assets/js/dashboard.js') ?>"></script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    const csrfMeta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
    if (csrfMeta) {
        const csrfToken = csrfMeta.getAttribute('content');
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }
            });
        }
        const origFetch = window.fetch;
        window.fetch = function(url, opts) {
            opts = opts || {};
            opts.headers = opts.headers || {};
            if (opts.method && opts.method.toUpperCase() !== 'GET') {
                if (opts.headers instanceof Headers) {
                    opts.headers.set('X-CSRF-TOKEN', csrfToken);
                } else {
                    opts.headers['X-CSRF-TOKEN'] = csrfToken;
                }
            }
            return origFetch.call(this, url, opts);
        };
    }
    </script>
    
    <!-- Scripts específicos de la página -->
    <?= $this->renderSection('scripts') ?>
</body>

</html> 