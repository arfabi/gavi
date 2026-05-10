<?php
$statusMap = ['1' => 'Aktif', '0' => 'Nonaktif', '' => 'Semua Status'];
$syncMap   = ['1' => 'Tersinkron', '0' => 'Belum Sync', '' => 'Semua Sync'];
?>

<!-- Stat Cards -->
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary"><i class="fas fa-book"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Dokumen</span>
                <span class="info-box-number"><?= number_format($stats['total']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Aktif</span>
                <span class="info-box-number"><?= number_format($stats['aktif']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info"><i class="fas fa-cloud-upload-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Tersinkron</span>
                <span class="info-box-number"><?= number_format($stats['synced']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Belum Sync</span>
                <span class="info-box-number"><?= number_format($stats['unsynced']) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="fas fa-book me-2"></i> Daftar Dokumen</h3>
        <a href="<?= base_url('knowledge/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Dokumen
        </a>
    </div>

    <!-- Filter -->
    <div class="card-body border-bottom pb-3">
        <form method="GET" action="<?= base_url('knowledge') ?>" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari judul / konten..."
                           value="<?= esc($filters['search']) ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="category_id" class="form-control form-control-sm auto-submit">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= $filters['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-3 col-md-2">
                <select name="aktif" class="form-control form-control-sm auto-submit">
                    <?php foreach ($statusMap as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $filters['aktif'] === (string)$val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-3 col-md-2">
                <select name="synced" class="form-control form-control-sm auto-submit">
                    <?php foreach ($syncMap as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $filters['synced'] === (string)$val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($filters['search'] || $filters['category_id'] || $filters['aktif'] !== '' || $filters['synced'] !== ''): ?>
            <div class="col-12 col-md-1">
                <a href="<?= base_url('knowledge') ?>" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th style="width:120px">Status</th>
                        <th style="width:130px">Sync Supabase</th>
                        <th style="width:120px">Diperbarui</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada dokumen ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="text-muted small"><?= $row['id'] ?></td>
                            <td>
                                <strong><?= esc($row['judul']) ?></strong>
                                <div class="text-muted small mt-1">
                                    <?= esc(mb_substr(strip_tags($row['konten']), 0, 80)) ?>...
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light border text-dark" style="font-size:0.75rem;">
                                    <?= esc($row['category_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['aktif']): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['synced_to_supabase']): ?>
                                    <span class="badge badge-synced">
                                        <i class="fas fa-cloud-upload-alt me-1"></i> Tersinkron
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-unsynced">
                                        <i class="fas fa-exclamation-circle me-1"></i> Belum Sync
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('knowledge/edit/' . $row['id']) ?>"
                                       class="btn btn-outline-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (! $row['synced_to_supabase']): ?>
                                    <form method="POST" action="<?= base_url('knowledge/sync/' . $row['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-info btn-sm" title="Sync ke Supabase">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= base_url('knowledge/delete/' . $row['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                title="Hapus"
                                                data-confirm="Yakin hapus dokumen ini?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination & Info -->
    <?php if ($total > 0): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan <?= number_format(($page - 1) * $perPage + 1) ?>–<?= number_format(min($page * $perPage, $total)) ?>
            dari <?= number_format($total) ?> dokumen
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
                <?php
                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);
                for ($p = $start; $p <= $end; $p++):
                ?>
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
