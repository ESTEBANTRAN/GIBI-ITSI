<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Bienestar Institucional</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('sistema/assets/images/logos/faviconV2.png') ?>" />
    <link href="<?= base_url('login/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js?hl=es" async defer></script>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.8), rgba(187, 222, 251, 0.85));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('<?= base_url('login/assets/img/fondo_login.jpg') ?>');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            transform: scale(1.1);
        }

        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
            animation: fadeInDown 0.7s;
            max-width: 420px;
            margin: 2rem auto;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-floating>.form-control:focus~label {
            color: #0d6efd;
        }

        .card-body {
            padding: 2rem 2rem 1.5rem 2rem;
        }

        /* reCAPTCHA centrado y responsivo */
        .recaptcha-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .recaptcha-container > div {
            transform-origin: center;
        }

        .captcha-error {
            color: #dc3545;
            font-size: 0.85rem;
            text-align: center;
            margin-top: 0.25rem;
            display: none;
        }

        @media (max-width: 576px) {
            .login-card {
                max-width: 95vw;
                padding: 0.5rem;
            }
            .card-body {
                padding: 1rem;
            }
            /* Escalar reCAPTCHA en móviles */
            .recaptcha-container > div {
                transform: scale(0.85);
                transform-origin: center;
            }
        }
    </style>
</head>

<body>
    <!-- Toasts -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card card w-100">
            <div class="card-body">
                <div class="text-center">
                    <img src="<?= base_url('login/assets/img/logo_instituto.png') ?>" alt="Logo ITSI" class="mb-4" style="max-width: 200px;">
                </div>
                <form action="<?= base_url('index.php/auth/attemptLogin') ?>" method="post" id="loginForm" autocomplete="off" novalidate>
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="identificador" name="identificador" placeholder="Cédula o Correo" required autofocus value="<?= old('identificador') ?>">
                        <label for="identificador"><i class="bi bi-person-circle me-2"></i>Cédula o Correo Electrónico</label>
                        <div class="invalid-feedback">
                            Este campo es obligatorio.
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password"><i class="bi bi-lock-fill me-2"></i>Contraseña</label>
                        <div class="invalid-feedback">
                            La contraseña es obligatoria.
                        </div>
                    </div>

                    <!-- Google reCAPTCHA v2 -->
                    <div class="recaptcha-container mb-3">
                        <div class="g-recaptcha" data-sitekey="<?= \App\Helpers\RecaptchaHelper::getSiteKey() ?>" data-callback="onCaptchaSuccess" data-expired-callback="onCaptchaExpired"></div>
                    </div>
                    <div class="captcha-error" id="captchaError">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Por favor, complete la verificación de seguridad.
                    </div>

                    <div class="d-flex justify-content-end align-items-center mb-4">
                        <a href="<?= site_url('forgot-password') ?>" class="text-decoration-none">¿Olvidó su contraseña?</a>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login text-uppercase fw-bold" id="btnLogin">
                            <span id="btnText">INGRESAR</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
            <footer class="text-center mt-3 text-muted small">
                &copy; <?= date('Y') ?> Bienestar Institucional.
            </footer>
        </div>
    </div>
    <script src="<?= base_url('login/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        let captchaCompleted = <?= ENVIRONMENT === 'development' ? 'true' : 'false' ?>;

        function onCaptchaSuccess(token) {
            captchaCompleted = true;
            document.getElementById('captchaError').style.display = 'none';
        }

        function onCaptchaExpired() {
            <?php if (ENVIRONMENT !== 'development'): ?>
            captchaCompleted = false;
            <?php endif; ?>
        }

        // Validar formulario al enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Validar campos
            if (!this.checkValidity()) {
                isValid = false;
            }

            // Validar reCAPTCHA                    <?php if (ENVIRONMENT !== 'development'): ?>
                    if (!captchaCompleted) {
                        document.getElementById('captchaError').style.display = 'block';
                        isValid = false;
                    }
                    <?php endif; ?>

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                document.getElementById('btnLogin').disabled = true;
                document.getElementById('btnText').classList.add('d-none');
                document.getElementById('btnSpinner').classList.remove('d-none');
            }
            this.classList.add('was-validated');
        });
    </script>
</body>

</html>