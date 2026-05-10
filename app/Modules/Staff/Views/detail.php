<?php
$roleBadge = [
    'admin'   => '<span class="badge badge-danger">Admin</span>',
    'petugas' => '<span class="badge badge-primary">Petugas</span>',
];
$statusBadge = [
    1 => '<span class="badge badge-success">Aktif</span>',
    0 => '<span class="badge badge-secondary">Nonaktif</span>',
];
$priorityBadge = [
    'high'   => '<span class="badge priority-high">High</span>',
    'medium' => '<span class="badge priority-medium">Medium</span>',
    'low'    => '<span class="badge priority-low">Low</span>',
];
$ticketStatusBadge = [
    'open'     => '<span class="badge badge-danger">Terbuka</span>',
    'pending'  => '<span class="badge badge-warning text-dark">Pending</span>',
    'resolved' => '<span class="badge badge-success">Selesai</span>',
    'closed'   => '<span class="badge badge-secondary">Ditutup</span>',
];
?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<div class="row">

    <!-- ================================================================
         KIRI: col-md-4 — Profil + Aksi
    ================================================================ -->
    <div class="col-12 col-md-4 mb-3 mb-md-0">

        <!-- Profil Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center pb-2">
                <!-- Avatar -->
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3"
                     style="width:72px;height:72px;font-size:1.8rem;
                            background:<?= $staff['is_active'] ? '#1e6f9f' : '#6c757d' ?>;">
                    <?= strtoupper(mb_substr($staff['name'], 0, 1)) ?>
                </div>
                <h5 class="mb-1 font-weight-bold"><?= esc($staff['name']) ?></h5>
                <div class="mb-2">
                    <?= $roleBadge[$staff['role']] ?? '' ?>
                    <?= $statusBadge[(int) $staff['is_active']] ?>
                </div>
                <small class="text-muted">
                    <i class="fas fa-building mr-1"></i><?= esc($staff['division_name'] ?? '—') ?>
                </small>
            </div>

            <!-- Data Akun -->
            <div class="card-body py-2 border-top">
                <p class="text-muted mb-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">
                    <i class="fas fa-id-card mr-1"></i>Data Akun
                </p>
                <table class="table table-sm table-borderless mb-0" style="font-size:0.83rem;">
                    <tr>
                        <td class="text-muted py-1 pl-0" style="width:42%;white-space:nowrap;">Email</td>
                        <td class="py-1"><small><?= esc($staff['email']) ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1 pl-0">Divisi</td>
                        <td class="py-1"><small><?= esc($staff['division_name'] ?? '—') ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1 pl-0">Role</td>
                        <td class="py-1"><?= $roleBadge[$staff['role']] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1 pl-0">Status</td>
                        <td class="py-1"><?= $statusBadge[(int) $staff['is_active']] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1 pl-0">Terakhir Login</td>
                        <td class="py-1">
                            <small><?= ! empty($staff['last_login']) ? date('d M Y, H:i', strtotime($staff['last_login'])) : '—' ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1 pl-0">Terdaftar</td>
                        <td class="py-1">
                            <small><?= ! empty($staff['created_at']) ? date('d M Y', strtotime($staff['created_at'])) : '—' ?></small>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Aksi -->
            <div class="card-body pt-1 pb-3 border-top">
                <p class="text-muted mb-2" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">
                    <i class="fas fa-tools mr-1"></i>Aksi
                </p>
                <a href="<?= base_url('staff/edit/' . $staff['id']) ?>"
                   class="btn btn-warning btn-block btn-sm mb-2">
                    <i class="fas fa-edit mr-1"></i>Edit Data Staff
                </a>

                <form method="POST" action="<?= base_url('staff/toggle/' . $staff['id']) ?>"
                      onsubmit="return confirm('Yakin mengubah status staff ini?')">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="btn btn-block btn-sm mb-2 <?= $staff['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                        <i class="fas <?= $staff['is_active'] ? 'fa-ban' : 'fa-check' ?> mr-1"></i>
                        <?= $staff['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> Akun
                    </button>
                </form>

                <button type="button" class="btn btn-outline-secondary btn-block btn-sm"
                        data-toggle="modal" data-target="#modalResetPassword">
                    <i class="fas fa-key mr-1"></i>Reset Password
                </button>
            </div>
        </div>

        <!-- Statistik Card -->
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h6 class="mb-0"><i class="fas fa-chart-bar mr-2 text-info"></i>Statistik Tiket</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0" style="font-size:0.87rem;">
                    <tr class="border-bottom">
                        <td class="pl-3 py-2">Total Tiket Ditangani</td>
                        <td class="py-2 pr-3 text-right font-weight-bold"><?= $ticketStats['total'] ?></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="pl-3 py-2">
                            <span class="badge badge-danger mr-1"></span>Terbuka
                        </td>
                        <td class="py-2 pr-3 text-right">
                            <a href="?status=open" class="font-weight-bold text-danger">
                                <?= $ticketStats['open'] ?>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="pl-3 py-2">
                            <span class="badge badge-warning mr-1"></span>Pending
                        </td>
                        <td class="py-2 pr-3 text-right">
                            <a href="?status=pending" class="font-weight-bold text-warning">
                                <?= $ticketStats['pending'] ?>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="pl-3 py-2">
                            <span class="badge badge-success mr-1"></span>Selesai
                        </td>
                        <td class="py-2 pr-3 text-right">
                            <a href="?status=resolved" class="font-weight-bold text-success">
                                <?= $ticketStats['resolved'] ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-3 py-2">
                            <span class="badge badge-secondary mr-1"></span>Ditutup
                        </td>
                        <td class="py-2 pr-3 text-right">
                            <a href="?status=closed" class="font-weight-bold text-secondary">
                                <?= $ticketStats['closed'] ?>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div><!-- /col-md-4 -->


    <!-- ================================================================
         KANAN: col-md-8 — Riwayat Tiket
    ================================================================ -->
    <div class="col-12 col-md-8">

        <div class="card shadow-sm">
            <div class="card-header p-0">
                <ul class="nav nav-tabs border-0" id="ticketTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-3 <?= $statusTab === '' ? 'active' : '' ?>"
                           href="<?= base_url('staff/detail/' . $staff['id']) ?>">
                            <i class="fas fa-list mr-1"></i>Semua
                            <span class="badge badge-secondary ml-1"><?= $ticketStats['total'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-3 <?= $statusTab === 'open' ? 'active' : '' ?>"
                           href="<?= base_url('staff/detail/' . $staff['id']) ?>?status=open">
                            <i class="fas fa-folder-open mr-1 text-danger"></i>Terbuka
                            <span class="badge badge-danger ml-1"><?= $ticketStats['open'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-3 <?= $statusTab === 'pending' ? 'active' : '' ?>"
                           href="<?= base_url('staff/detail/' . $staff['id']) ?>?status=pending">
                            <i class="fas fa-clock mr-1 text-warning"></i>Pending
                            <span class="badge badge-warning text-dark ml-1"><?= $ticketStats['pending'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-3 <?= $statusTab === 'resolved' ? 'active' : '' ?>"
                           href="<?= base_url('staff/detail/' . $staff['id']) ?>?status=resolved">
                            <i class="fas fa-check-circle mr-1 text-success"></i>Selesai
                            <span class="badge badge-success ml-1"><?= $ticketStats['resolved'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-3 <?= $statusTab === 'closed' ? 'active' : '' ?>"
                           href="<?= base_url('staff/detail/' . $staff['id']) ?>?status=closed">
                            <i class="fas fa-lock mr-1 text-secondary"></i>Ditutup
                            <span class="badge badge-secondary ml-1"><?= $ticketStats['closed'] ?></span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <?php if (empty($tickets)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-ticket-alt fa-2x d-block mb-2"></i>
                    Belum ada tiket<?= $statusTab ? ' dengan status ini' : '' ?>.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="pl-3" style="width:140px;">No. Tiket</th>
                                <th>Customer / Kategori</th>
                                <th class="text-center">Prioritas</th>
                                <th class="text-center">Status</th>
                                <th>Dibuat</th>
                                <th class="text-center" style="width:60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td class="pl-3 align-middle">
                                    <a href="<?= base_url('tickets/detail/' . $t['id']) ?>"
                                       class="font-weight-bold text-primary"
                                       style="font-size:0.82rem;">
                                        <?= esc($t['ticket_number']) ?>
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <div style="font-size:0.85rem;"><?= esc($t['customer_name'] ?? '—') ?></div>
                                    <small class="text-muted"><?= esc($t['category_name'] ?? '—') ?></small>
                                </td>
                                <td class="text-center align-middle">
                                    <?= $priorityBadge[$t['priority']] ?? '' ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?= $ticketStatusBadge[$t['status']] ?? '' ?>
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted">
                                        <?= date('d M Y', strtotime($t['created_at'])) ?>
                                        <?php if ($t['resolved_at']): ?>
                                        <div class="text-success">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <?= date('d M Y', strtotime($t['resolved_at'])) ?>
                                        </div>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('tickets/detail/' . $t['id']) ?>"
                                       class="btn btn-xs btn-outline-info" title="Lihat Tiket">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /col-md-8 -->

</div><!-- /row -->


<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('staff/reset-password/' . $staff['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key mr-2 text-warning"></i>Reset Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Password untuk <strong><?= esc($staff['name']) ?></strong> akan diganti.
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="new_password" class="form-control"
                               placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key mr-1"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
