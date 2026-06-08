<!-- app/Views/layouts/mainAdmin.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienestar Estudiantil</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('sistema/assets/images/logos/faviconV2.png') ?>" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- ESTILOS -->
    <link rel="stylesheet" href="<?= base_url('sistema/assets/css/styles.min.css') ?>" />
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= base_url('sistema/assets/css/custom.css') ?>?v=<?= time() ?>" />
    <!-- jQuery (must be first) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Debug: Verificar rutas CSS -->
    <script>
    </script>
    <?= csrf_meta() ?>
    <!-- FIX: Estilos inline para eliminar el gap del header (anti-caché) -->
    <style>
        /* Forzar header al tope absoluto */
        .app-header {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            top: 0 !important;
        }
        #main-wrapper[data-layout=vertical][data-header-position=fixed] .app-header {
            position: fixed !important;
            top: 0 !important;
            z-index: 50 !important;
        }
        .app-header .navbar {
            margin: 0 !important;
            border-radius: 0 !important;
        }
        /* Compensar sidebar de 300px */
        @media (min-width: 1200px) {
            #main-wrapper[data-layout=vertical][data-header-position=fixed] .app-header {
                width: calc(100% - 300px) !important;
            }
        }
        /* Padding del contenido: solo altura del header */
        #main-wrapper[data-layout=vertical][data-header-position=fixed] .body-wrapper > .container-fluid {
            padding-top: 70px !important;
        }
        /* Prevenir que Serveo u otros inyecten espacio */
        body > *:not(.page-wrapper):not(script):not(link):not(style) {
            display: none !important;
        }
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
            <?= $this->include('admin/partials/sidebarAdminBienestar'); ?>
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

    <!-- Other Scripts -->
    <script src="<?= base_url('sistema/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/libs/apexcharts/dist/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/libs/simplebar/dist/simplebar.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('sistema/assets/js/app.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Debug: Verify jQuery is loaded -->
    <script>
        if (typeof $ === 'undefined') {
        } else {
        }
    </script>
    
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
    
    <!-- Dashboard.js solo se carga si es necesario -->
    <?php if(isset($loadDashboard) && $loadDashboard): ?>
    <script src="<?= base_url('sistema/assets/js/dashboard.js') ?>"></script>
    <?php endif; ?>
    
    <!-- Scripts específicos de la página -->
    <?= $this->renderSection('scripts') ?>
    
    <!-- Scripts adicionales para páginas específicas -->
    <script>
        // Verificar que jQuery esté disponible
        if (typeof $ === 'undefined') {
        } else {
        }
        
        // Verificar que ApexCharts esté disponible
        if (typeof ApexCharts === 'undefined') {
        } else {
        }
    </script>
</body>

</html>
