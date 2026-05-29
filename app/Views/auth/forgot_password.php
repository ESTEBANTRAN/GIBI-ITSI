<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | Bienestar Institucional</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('sistema/assets/images/logos/faviconV2.png') ?>" />
    <link href="<?= base_url('login/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
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

        .recovery-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
            animation: fadeInDown 0.7s;
            max-width: 440px;
            margin: 2rem auto;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-floating>.form-control:focus~label {
            color: #0d6efd;
        }

        .card-body {
            padding: 2rem;
        }

        .recovery-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .recovery-icon i {
            font-size: 1.8rem;
            color: #fff;
        }

        .recaptcha-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .captcha-error {
            color: #dc3545;
            font-size: 0.85rem;
            text-align: center;
            margin-top: 0.25rem;
            display: none;
        }

        .info-text {
            font-size: 0.9rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        @media (max-width: 576px) {
            .recovery-card { max-width: 95vw; }
            .card-body { padding: 1.25rem; }
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
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="recovery-card card w-100">
            <div class="card-body">
                <div class="recovery-icon">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h4 class="text-center mb-2 fw-bold">¿Olvidó su contraseña?</h4>
                <p class="info-text">
                    Ingrese su <strong>número de cédula</strong> y <strong>correo electrónico</strong> registrado. 
                    Si los datos coinciden, podrá establecer una nueva contraseña.
                </p>

                <form action="<?= base_url('index.php/auth/verifyIdentity') ?>" method="post" id="forgotForm" autocomplete="off" novalidate>
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" required 
                               pattern="[0-9]{10}" maxlength="10" value="<?= old('cedula') ?>">
                        <label for="cedula"><i class="bi bi-person-vcard me-2"></i>Número de Cédula</label>
                        <div class="invalid-feedback">
                            Ingrese un número de cédula válido (10 dígitos).
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo" required value="<?= old('email') ?>">
                        <label for="email"><i class="bi bi-envelope-fill me-2"></i>Correo Electrónico</label>
                        <div class="invalid-feedback">
                            Ingrese un correo electrónico válido.
                        </div>
                    </div>

                    <!-- Google reCAPTCHA v2 -->
                    <div class="recaptcha-container mb-3">
                        <div class="g-recaptcha" data-sitekey="<?= \App\Helpers\RecaptchaHelper::getSiteKey() ?>" data-callback="onCaptchaSuccess" data-expired-callback="onCaptchaExpired"></div>
                    </div>
                    <div class="captcha-error" id="captchaError">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Complete la verificación de seguridad.
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary text-uppercase fw-bold" id="btnVerify">
                            <span id="btnText"><i class="bi bi-shield-check me-2"></i>VERIFICAR IDENTIDAD</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="<?= site_url('login') ?>" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                </div>
            </div>
            <footer class="text-center mt-2 mb-3 text-muted small">
                &copy; <?= date('Y') ?> Bienestar Institucional.
            </footer>
        </div>
    </div>

    <script src="<?= base_url('login/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        let captchaCompleted = false;

        function onCaptchaSuccess(token) {
            captchaCompleted = true;
            document.getElementById('captchaError').style.display = 'none';
        }

        function onCaptchaExpired() {
            captchaCompleted = false;
        }

        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            let isValid = true;

            if (!this.checkValidity()) {
                isValid = false;
            }

            if (!captchaCompleted) {
                document.getElementById('captchaError').style.display = 'block';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                document.getElementById('btnVerify').disabled = true;
                document.getElementById('btnText').classList.add('d-none');
                document.getElementById('btnSpinner').classList.remove('d-none');
            }
            this.classList.add('was-validated');
        });
    </script>
</body>

</html>
