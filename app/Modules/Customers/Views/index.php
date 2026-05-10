<?php
$aiModeFilter = $filters['ai_mode'] ?? '';
$searchFilter = $filters['search'] ?? '';
?>

<!-- Stat Cards -->
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Customer</span>
                <span class="info-box-number"><?= number_format($stats['total']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success"><i class="fas fa-robot"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">AI Mode Aktif</span>
                <span class="info-box-number"><?= number_format($stats['ai_active']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dari Instansi</span>
                <span class="info-box-number"><?= number_format($stats['with_instansi']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info"><i class="fas fa-user-plus"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Baru Hari Ini</span>
                <span class="info-box-number"><?= number_format($stats['new_today']) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter + Table -->
<div class="card shadow-sm">
    <div class="card-header">
        <form method="GET" action="<?= base_url('customers') ?>" class="w-100">
            <div class="row align-items-end">
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama, nomor WA, instansi, NIK..."
                               value="<?= esc($searchFilter) ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <select name="ai_mode" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Semua Mode AI</option>
                        <option value="1" <?= $aiModeFilter === '1' ? 'selected' : '' ?>>AI Mode Aktif</option>
                        <option value="0" <?= $aiModeFilter === '0' ? 'selected' : '' ?>>AI Mode Nonaktif</option>
                    </select>
                </div>
                <?php if ($searchFilter || $aiModeFilter !== ''): ?>
                <div class="col-12 col-md-2">
                    <a href="<?= base_url('customers') ?>" class="btn btn-outline-secondary btn-sm btn-block">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                </div>
                <?php endif; ?>
                <div class="col-12 col-md-2 ml-auto text-right">
                    <small class="text-muted">
                        <?= number_format($total) ?> customer ditemukan
                    </small>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width:36px;">#</th>
                        <th>Nama</th>
                        <th>No. WA</th>
                        <th>Instansi / Kota</th>
                        <th class="text-center">Sesi</th>
                        <th class="text-center">Chat</th>
                        <th class="text-center">Tiket</th>
                        <th class="text-center">AI</th>
                        <th>Terakhir Aktif</th>
                        <th class="text-center" style="width:60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-users-slash fa-2x d-block mb-2"></i>
                            Tidak ada data customer.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = ($page - 1) * $perPage + 1; ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="pl-3 text-muted small"><?= $no++ ?></td>
                        <td>
                            <a href="<?= base_url('customers/detail/' . $row['id']) ?>"
                               class="font-weight-bold text-dark">
                                <?= esc($row['name'] ?? '<i class="text-muted">Tidak diketahui</i>') ?>
                            </a>
                            <?php if (! empty($row['nik'])): ?>
                            <div class="text-muted" style="font-size:0.75rem;">NIK: <?= esc($row['nik']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-monospace small">
                                <i class="fab fa-whatsapp text-success mr-1"></i>
                                <?= esc(preg_replace('/(\+62\d{3})\d{4}(\d{4})/', '$1****$2', $row['whatsapp_number'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (! empty($row['instansi'])): ?>
                                <span class="small"><?= esc($row['instansi']) ?></span><br>
                            <?php endif; ?>
                            <?php if (! empty($row['address_city'])): ?>
                                <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($row['address_city']) ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border"><?= (int) $row['total_sessions'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border"><?= (int) $row['total_chats'] ?></span>
                        </td>
                        <td class="text-center">
                            <?php $tc = (int) $row['total_tickets']; ?>
                            <span class="badge <?= $tc > 0 ? 'badge-warning' : 'badge-light border' ?>">
                                <?= $tc ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($row['ai_mode']): ?>
                                <span class="badge badge-success" title="AI Mode Aktif"><i class="fas fa-robot"></i></span>
                            <?php else: ?>
                                <span class="badge badge-secondary" title="AI Mode Nonaktif"><i class="fas fa-robot"></i></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d M Y, H:i', strtotime($row['last_interaction'])) ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('customers/detail/' . $row['id']) ?>"
                               class="btn btn-xs btn-outline-primary btn-sm" title="Lihat Detail">
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

    <?php if ($totalPages > 1): ?>
    <div class="card-footer py-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
            <small class="text-muted">
                Halaman <?= $page ?> dari <?= $totalPages ?> &mdash; <?= number_format($total) ?> total
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">
                            <i class="fas fa-chevron-left"></i>
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
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</div>
