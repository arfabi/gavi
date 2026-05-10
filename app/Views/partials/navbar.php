<?php
$aiMode = false;
try {
    $db      = \Config\Database::connect();
    $setting = $db->table('settings')->where('setting_key', 'global_ai_mode')->get()->getRow();
    $aiMode  = $setting && $setting->value === '1';
} catch (\Exception $e) {
    // DB mungkin belum tersedia
}
?>
<nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #0f2235;">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- AI Mode Badge -->
        <li class="nav-item d-flex align-items-center me-3">
            <span class="badge badge-<?= $aiMode ? 'success' : 'secondary' ?> px-3 py-2" style="font-size:0.8rem;">
                <i class="fas fa-robot me-1"></i>
                AI <?= $aiMode ? 'ON' : 'OFF' ?>
            </span>
        </li>

        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle me-1"></i>
                <?= esc(session()->get('name') ?? 'Staff') ?>
                <span class="badge badge-light ms-1" style="font-size:0.7rem; text-transform:uppercase;">
                    <?= esc(session()->get('role') ?? '') ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text text-muted" style="font-size:0.8rem;">
                    <?= esc(session()->get('email') ?? '') ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('auth/logout') ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
