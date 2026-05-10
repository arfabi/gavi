<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'GAVI Dashboard') ?> | GAVI</title>

    <!-- AdminLTE + Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom GAVI CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/gavi.css') ?>">

    <?= $extraCss ?? '' ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed <?= $bodyClass ?? '' ?>">
<div class="wrapper">

    <!-- Navbar -->
    <?= view('App\Views\partials\navbar') ?>

    <!-- Sidebar -->
    <?= view('App\Views\partials\sidebar') ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <!-- Breadcrumb Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= esc($pageTitle ?? $title ?? '') ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                            <?php if (isset($breadcrumb)): ?>
                                <?php foreach ($breadcrumb as $bc): ?>
                                    <?php if (isset($bc['url'])): ?>
                                        <li class="breadcrumb-item"><a href="<?= esc($bc['url']) ?>"><?= esc($bc['label']) ?></a></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item active"><?= esc($bc['label']) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-1"></i>
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?= view('App\Views\partials\footer') ?>

</div><!-- /.wrapper -->

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="<?= base_url('assets/js/gavi.js') ?>"></script>

<?= $extraJs ?? '' ?>

</body>
</html>
