<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Bienestar Institucional</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('sistema/assets/images/logos/faviconV2.png') ?>" />
    <link href="<?= base_url('login/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

        .reset-card {
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

        .form-floating>.form-control:focus~label { color: #0d6efd; }

        .card-body { padding: 2rem; }

        .reset-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #198754, #0f5132);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .reset-icon i { font-size: 1.8rem; color: #fff; }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
            margin-top: 0.25rem;
        }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 0.15rem;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 0.15rem;
        }

        .requirement-item.met { color: #198754; }
        .requirement-item.unmet { color: #dc3545; }

        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            color: #6c757d;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover { color: #0d6efd; }

        @media (max-width: 576px) {
            .reset-card { max-width: 95vw; }
            .card-body { padding: 1.25rem; }
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
    </div>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="reset-card card w-100">
            <div class="card-body">
                <div class="reset-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4 class="text-center mb-2 fw-bold">Establecer Nueva Contraseña</h4>
                <p class="text-center text-muted small mb-3">
                    Hola, <strong><?= esc($nombre_usuario ?? 'Usuario') ?></strong>. Ingrese su nueva contraseña.
                </p>

                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Tiempo restante:</strong> Este enlace expira en <span id="countdown" class="fw-bold">10:00</span> minutos.
                </div>

                <form action="<?= base_url('auth/resetPassword') ?>" method="post" id="resetForm" autocomplete="off" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="reset_token" value="<?= esc($reset_token ?? '') ?>">

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control pe-5" id="new_password" name="new_password" 
                               placeholder="Nueva Contraseña" required minlength="8">
                        <label for="new_password"><i class="bi bi-lock-fill me-2"></i>Nueva Contraseña</label>
                        <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <div class="invalid-feedback">
                            La contraseña debe tener al menos 8 caracteres.
                        </div>
                        <!-- Indicador de fortaleza -->
                        <div class="password-strength bg-secondary" id="strengthBar"></div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>

                    <!-- Requisitos de contraseña -->
                    <div class="password-requirements mb-3" id="requirements">
                        <div class="requirement-item unmet" id="req-length">
                            <i class="bi bi-x-circle-fill"></i> Mínimo 8 caracteres
                        </div>
                        <div class="requirement-item unmet" id="req-upper">
                            <i class="bi bi-x-circle-fill"></i> Al menos una mayúscula
                        </div>
                        <div class="requirement-item unmet" id="req-lower">
                            <i class="bi bi-x-circle-fill"></i> Al menos una minúscula
                        </div>
                        <div class="requirement-item unmet" id="req-number">
                            <i class="bi bi-x-circle-fill"></i> Al menos un número
                        </div>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control pe-5" id="confirm_password" name="confirm_password" 
                               placeholder="Confirmar Contraseña" required>
                        <label for="confirm_password"><i class="bi bi-lock me-2"></i>Confirmar Contraseña</label>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <div class="invalid-feedback" id="confirmFeedback">
                            Las contraseñas no coinciden.
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-success text-uppercase fw-bold" id="btnReset">
                            <span id="btnText"><i class="bi bi-check-circle me-2"></i>CAMBIAR CONTRASEÑA</span>
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
        // Toggle password visibility
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                field.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        }

        // Password strength checker
        const passwordField = document.getElementById('new_password');
        const confirmField = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordField.addEventListener('input', function() {
            const pwd = this.value;
            let score = 0;

            // Check requirements
            const hasLength = pwd.length >= 8;
            const hasUpper = /[A-Z]/.test(pwd);
            const hasLower = /[a-z]/.test(pwd);
            const hasNumber = /[0-9]/.test(pwd);

            updateRequirement('req-length', hasLength);
            updateRequirement('req-upper', hasUpper);
            updateRequirement('req-lower', hasLower);
            updateRequirement('req-number', hasNumber);

            if (hasLength) score++;
            if (hasUpper) score++;
            if (hasLower) score++;
            if (hasNumber) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;

            // Update strength bar
            const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
            const colors = ['', '#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];
            const labels = ['', 'Muy débil', 'Débil', 'Aceptable', 'Fuerte', 'Muy fuerte'];

            strengthBar.style.width = widths[score];
            strengthBar.style.backgroundColor = colors[score];
            strengthText.textContent = labels[score];
            strengthText.style.color = colors[score];
        });

        function updateRequirement(id, met) {
            const el = document.getElementById(id);
            const icon = el.querySelector('i');
            if (met) {
                el.classList.replace('unmet', 'met');
                icon.classList.replace('bi-x-circle-fill', 'bi-check-circle-fill');
            } else {
                el.classList.replace('met', 'unmet');
                icon.classList.replace('bi-check-circle-fill', 'bi-x-circle-fill');
            }
        }

        // Form validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const pwd = passwordField.value;
            const confirm = confirmField.value;
            let isValid = true;

            if (pwd.length < 8 || !/[A-Z]/.test(pwd) || !/[a-z]/.test(pwd) || !/[0-9]/.test(pwd)) {
                passwordField.setCustomValidity('La contraseña no cumple los requisitos.');
                isValid = false;
            } else {
                passwordField.setCustomValidity('');
            }

            if (pwd !== confirm) {
                confirmField.setCustomValidity('Las contraseñas no coinciden.');
                document.getElementById('confirmFeedback').textContent = 'Las contraseñas no coinciden.';
                isValid = false;
            } else {
                confirmField.setCustomValidity('');
            }

            if (!isValid || !this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                document.getElementById('btnReset').disabled = true;
                document.getElementById('btnText').classList.add('d-none');
                document.getElementById('btnSpinner').classList.remove('d-none');
            }
            this.classList.add('was-validated');
        });

        // Countdown timer (10 minutes)
        let timeLeft = <?= $tiempo_restante ?? 600 ?>;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(function() {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = '0:00';
                document.getElementById('btnReset').disabled = true;
                document.getElementById('btnText').innerHTML = '<i class="bi bi-clock-history me-2"></i>ENLACE EXPIRADO';

                // Show alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger py-2 small mt-2';
                alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> El tiempo ha expirado. <a href="' + '<?= site_url("forgot-password") ?>' + '">Solicite un nuevo enlace</a>.';
                document.getElementById('resetForm').prepend(alertDiv);
                return;
            }
            const min = Math.floor(timeLeft / 60);
            const sec = timeLeft % 60;
            countdownEl.textContent = min + ':' + (sec < 10 ? '0' : '') + sec;

            if (timeLeft <= 60) {
                countdownEl.style.color = '#dc3545';
            }
        }, 1000);
    </script>
</body>

</html>
