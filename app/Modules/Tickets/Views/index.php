<?php
$priorityBadge = [
    'high'   => '<span class="badge priority-high">High</span>',
    'medium' => '<span class="badge priority-medium">Medium</span>',
    'low'    => '<span class="badge priority-low">Low</span>',
];
$statusBadge = [
    'open'     => '<span class="badge badge-danger">Open</span>',
    'pending'  => '<span class="badge badge-warning text-dark">Pending</span>',
    'resolved' => '<span class="badge badge-success">Selesai</span>',
    'closed'   => '<span class="badge badge-secondary">Ditutup</span>',
];
$tabs = [
    ''         => ['label' => 'Semua',   'icon' => 'list'],
    'open'     => ['label' => 'Terbuka', 'icon' => 'envelope-open'],
    'pending'  => ['label' => 'Pending', 'icon' => 'clock'],
    'resolved' => ['label' => 'Selesai', 'icon' => 'check-circle'],
    'closed'   => ['label' => 'Ditutup', 'icon' => 'lock'],
];
?>

<!-- Stat Badges -->
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-danger"><i class="fas fa-envelope-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Terbuka</span>
                <span class="info-box-number" id="count-open"><?= $counts['open'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending</span>
                <span class="info-box-number" id="count-pending"><?= $counts['pending'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Selesai</span>
                <span class="info-box-number" id="count-resolved"><?= $counts['resolved'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-secondary"><i class="fas fa-lock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Ditutup</span>
                <span class="info-box-number" id="count-closed"><?= $counts['closed'] ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card shadow-sm">
    <!-- Tab Navigation -->
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="ticket-tabs">
            <?php foreach ($tabs as $tabStatus => $tabInfo): ?>
            <li class="nav-item">
                <a class="nav-link <?= $filters['status'] === $tabStatus ? 'active' : '' ?>"
                   href="<?= base_url('tickets') ?>?status=<?= $tabStatus ?>&search=<?= urlencode($filters['search']) ?>&priority=<?= $filters['priority'] ?>">
                    <i class="fas fa-<?= $tabInfo['icon'] ?> me-1"></i>
                    <?= $tabInfo['label'] ?>
                    <?php if ($tabStatus !== '' && isset($counts[$tabStatus]) && $counts[$tabStatus] > 0): ?>
                        <span class="badge badge-<?= $tabStatus === 'open' ? 'danger' : ($tabStatus === 'pending' ? 'warning text-dark' : 'secondary') ?> ms-1">
                            <?= $counts[$tabStatus] ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Filter Bar -->
    <div class="card-body border-bottom py-2">
        <form method="GET" action="<?= base_url('tickets') ?>" class="row g-2 align-items-center">
            <input type="hidden" name="status" value="<?= esc($filters['status']) ?>">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nomor tiket, nama, ringkasan..."
                           value="<?= esc($filters['search']) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="priority" class="form-control form-control-sm auto-submit">
                    <option value="">Semua Prioritas</option>
                    <option value="high"   <?= $filters['priority'] === 'high'   ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= $filters['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low"    <?= $filters['priority'] === 'low'    ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <?php if ($filters['search'] || $filters['priority']): ?>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('tickets?status=' . $filters['status']) ?>"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
            <?php endif; ?>
            <div class="col-auto ml-auto">
                <small class="text-muted" id="last-update">Auto-refresh aktif</small>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:150px">No. Tiket</th>
                        <th>Ringkasan AI</th>
                        <th style="width:160px">Kategori</th>
                        <th style="width:110px">Customer</th>
                        <th style="width:110px">Petugas</th>
                        <th style="width:80px">Prioritas</th>
                        <th style="width:80px">Status</th>
                        <th style="width:100px">Waktu</th>
                        <th style="width:70px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-ticket-alt fa-2x mb-2 d-block text-muted"></i>
                                Tidak ada tiket ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('tickets/detail/' . $row['id']) ?>"
                                   class="font-weight-bold text-primary">
                                    <?= esc($row['ticket_number']) ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?= esc($row['summary']) ?>">
                                    <?= esc(mb_substr($row['summary'], 0, 80)) ?>
                                    <?= strlen($row['summary']) > 80 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?= esc($row['category_name'] ?? '-') ?></small>
                            </td>
                            <td>
                                <span title="<?= esc($row['whatsapp_number'] ?? '') ?>">
                                    <?= esc($row['customer_name'] ?? '-') ?>
                                </span>
                                <?php if ($row['whatsapp_number']): ?>
                                <div class="text-muted" style="font-size:0.72rem;">
                                    <?= esc(preg_replace('/(\+62\d{3})\d{4}(\d{4})/', '$1****$2', $row['whatsapp_number'])) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['assigned_staff_id']): ?>
                                    <small><?= esc($row['assigned_name'] ?? '-') ?></small>
                                <?php else: ?>
                                    <small class="text-muted font-italic">Unassigned</small>
                                <?php endif; ?>
                            </td>
                            <td><?= $priorityBadge[$row['priority']] ?? '-' ?></td>
                            <td><?= $statusBadge[$row['status']] ?? '-' ?></td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m H:i', strtotime($row['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= base_url('tickets/detail/' . $row['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total > 0): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            <?= number_format(($page - 1) * $perPage + 1) ?>–<?= number_format(min($page * $perPage, $total)) ?>
            dari <?= number_format($total) ?> tiket
        </small>
        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">
                        &laquo;
                    </a>
                </li>
                <?php endif; ?>
                <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>">
                        <?= $p ?>
                    </a>
                </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>">
                        &raquo;
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// AJAX polling setiap 30 detik
(function () {
    var interval = 30000;
    function poll() {
        $.get('<?= base_url('tickets/poll') ?>', function (res) {
            if (res.success) {
                ['open', 'pending', 'resolved', 'closed'].forEach(function (s) {
                    $('#count-' + s).text(res.counts[s] || 0);
                });
                var now = new Date();
                $('#last-update').text('Update: ' + now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0') + ':' + now.getSeconds().toString().padStart(2,'0'));
            }
        });
    }
    setInterval(poll, interval);
})();
</script>
