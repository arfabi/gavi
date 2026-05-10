<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login - GAVI Dashboard') ?></title>

    <!-- AdminLTE + Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --gavi-primary: #1e6f9f;
            --gavi-dark:    #0f2235;
        }

        body.login-page {
            background: linear-gradient(135deg, var(--gavi-dark) 0%, var(--gavi-primary) 100%);
            min-height: 100vh;
        }

        .login-box {
            width: 400px;
        }

        .login-logo a {
            color: #fff;
            font-weight: 700;
            font-size: 1.8rem;
            text-decoration: none;
        }

        .login-logo a span {
            color: #EBF4FA;
            font-weight: 300;
        }

        .login-logo small {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            font-weight: 400;
            margin-top: -5px;
        }

        .card-outline.card-primary {
            border-top: 3px solid var(--gavi-primary);
        }

        .btn-primary {
            background-color: var(--gavi-primary);
            border-color: var(--gavi-primary);
        }

        .btn-primary:hover {
            background-color: var(--gavi-dark);
            border-color: var(--gavi-dark);
        }

        .captcha-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .captcha-wrapper img {
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        #btn-refresh-captcha {
            white-space: nowrap;
            font-size: 0.8rem;
        }

        .login-footer {
            text-align: center;
            color: rgba(255,255,255,0.5);
            font-size: 0.78rem;
            margin-top: 15px;
        }
    </style>
</head>
<body class="login-page">

<div class="login-box">
    <div class="login-logo">
        <a href="#">GAVI <span>Dashboard</span></a>
        <small>Kanwil Kemenkumham DIY</small>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <p class="mb-0 text-muted" style="font-size:0.88rem;">
                <i class="fas fa-robot text-primary me-1"></i>
                Government AI Virtual Intelligence
            </p>
        </div>
        <div class="card-body">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login') ?>" method="POST" id="login-form">
                <?= csrf_field() ?>

                <div class="input-group mb-3">
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="Email"
                           value="<?= esc(old('email')) ?>"
                           required
                           autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           placeholder="Password"
                           required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="toggle-password" tabindex="-1">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div class="form-group">
                    <label class="text-muted" style="font-size:0.85rem;">
                        <i class="fas fa-shield-alt me-1"></i> Verifikasi CAPTCHA
                    </label>
                    <div class="captcha-wrapper">
                        <div id="captcha-container"><?= $captchaHtml ?></div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh-captcha">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <input type="text"
                           name="captcha"
                           class="form-control"
                           placeholder="Masukkan kode di atas"
                           maxlength="6"
                           autocomplete="off"
                           required
                           style="text-transform:uppercase; letter-spacing:4px;">
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="login-footer">
        GAVI Dashboard &copy; <?= date('Y') ?> &mdash; Kanwil Kemenkumham DIY
    </div>
</div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

<script>
$(function () {
    // Toggle password visibility
    $('#toggle-password').on('click', function () {
        const pwd = $('#password');
        const icon = $('#eye-icon');
        if (pwd.attr('type') === 'password') {
            pwd.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            pwd.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Refresh CAPTCHA
    function refreshCaptcha() {
        $.get('<?= base_url('auth/captcha') ?>', function (html) {
            $('#captcha-container').html(html);
            $('input[name="captcha"]').val('').focus();
        });
    }

    $('#btn-refresh-captcha').on('click', refreshCaptcha);
    $(document).on('click', '#captcha-img', refreshCaptcha);

    // Auto uppercase captcha input
    $('input[name="captcha"]').on('input', function () {
        this.value = this.value.toUpperCase();
    });
});
</script>

</body>
</html>
