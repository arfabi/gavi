<?php
$role        = session()->get('role');
$currentUri  = service('uri')->getPath();
$isAdmin     = $role === 'admin';

function isActive(string $path, string $current): string {
    return str_starts_with('/' . ltrim($current, '/'), $path) ? 'active' : '';
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #0f2235;">

    <!-- Brand Logo -->
    <a href="<?= base_url($isAdmin ? 'dashboard' : 'tickets') ?>" class="brand-link" style="background-color: #0a1a2a;">
        <i class="fas fa-robot brand-image elevation-3 p-1" style="color:#1e6f9f; font-size:1.5rem; opacity:1;"></i>
        <span class="brand-text font-weight-bold">GAVI</span>
        <small class="brand-text font-weight-light" style="font-size:0.7rem;"> Dashboard</small>
    </a>

    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white" style="opacity:0.7;"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= esc(session()->get('name') ?? 'Staff') ?></a>
                <small style="color:rgba(255,255,255,0.5); text-transform:uppercase; font-size:0.7rem;">
                    <?= esc($role ?? '') ?>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= isActive('/dashboard', $currentUri) ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('dashboard/analytics') ?>" class="nav-link <?= isActive('/dashboard/analytics', $currentUri) ?>">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Analytics</p>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);">OPERASIONAL</li>

                <li class="nav-item">
                    <a href="<?= base_url('tickets') ?>" class="nav-link <?= isActive('/tickets', $currentUri) ?>">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Tiket Eskalasi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('conversations') ?>" class="nav-link <?= isActive('/conversations', $currentUri) ?>">
                        <i class="nav-icon fab fa-whatsapp" style="color:#25d366;"></i>
                        <p>Percakapan</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('customers') ?>" class="nav-link <?= isActive('/customers', $currentUri) ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Customer</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('knowledge') ?>" class="nav-link <?= isActive('/knowledge', $currentUri) ?>">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Knowledge Base</p>
                    </a>
                </li>

                <?php if ($isAdmin): ?>
                <li class="nav-header" style="color:rgba(255,255,255,0.4);">ADMIN</li>

                <li class="nav-item <?= isActive('/staff', $currentUri) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('/staff', $currentUri) ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Manajemen
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('staff') ?>" class="nav-link <?= str_starts_with('/' . ltrim($currentUri, '/'), '/staff') && ! str_starts_with('/' . ltrim($currentUri, '/'), '/staff?tab=divisi') && ! str_starts_with('/' . ltrim($currentUri, '/'), '/staff?tab=layanan') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('staff') ?>?tab=divisi" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Divisi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('staff') ?>?tab=layanan" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Layanan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('settings') ?>" class="nav-link <?= isActive('/settings', $currentUri) ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Pengaturan Sistem</p>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);">AKUN</li>

                <li class="nav-item">
                    <a href="<?= base_url('auth/logout') ?>" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
