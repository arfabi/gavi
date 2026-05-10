<?php
$roleBadge = [
    'admin'   => '<span class="badge badge-danger">Admin</span>',
    'petugas' => '<span class="badge badge-primary">Petugas</span>',
];
$statusBadge = [
    1 => '<span class="badge badge-success">Aktif</span>',
    0 => '<span class="badge badge-secondary">Nonaktif</span>',
];

$activeTab = service('request')->getGet('tab') ?? 'staff';
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

<!-- ===============================================================
     STATS CARDS
=============================================================== -->
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Staff</span>
                <span class="info-box-number"><?= $stats['total'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-danger"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Admin</span>
                <span class="info-box-number"><?= $stats['admin'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-primary"><i class="fas fa-user-tie"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Petugas</span>
                <span class="info-box-number"><?= $stats['petugas'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Aktif</span>
                <span class="info-box-number"><?= $stats['active'] ?></span>
            </div>
        </div>
    </div>
</div>

<!-- ===============================================================
     MAIN TABS
=============================================================== -->
<div class="card shadow-sm">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="mainTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link px-4 py-3 <?= $activeTab === 'staff' ? 'active' : '' ?>"
                   data-toggle="tab" href="#tab-staff" role="tab">
                    <i class="fas fa-users mr-2"></i>Daftar Staff
                    <span class="badge badge-secondary ml-1"><?= $total ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 <?= $activeTab === 'divisi' ? 'active' : '' ?>"
                   data-toggle="tab" href="#tab-divisi" role="tab">
                    <i class="fas fa-sitemap mr-2"></i>Divisi
                    <span class="badge badge-secondary ml-1"><?= count($divisions) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 <?= $activeTab === 'layanan' ? 'active' : '' ?>"
                   data-toggle="tab" href="#tab-layanan" role="tab">
                    <i class="fas fa-tags mr-2"></i>Layanan
                    <span class="badge badge-secondary ml-1"><?= count($services) ?></span>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">

        <!-- =========================================================
             TAB: DAFTAR STAFF
        ========================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'staff' ? 'show active' : '' ?>" id="tab-staff">

            <!-- Filter bar -->
            <div class="p-3 border-bottom bg-light">
                <form method="GET" action="<?= base_url('staff') ?>">
                    <input type="hidden" name="tab" value="staff">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-4">
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Cari nama atau email..."
                                   value="<?= esc($filters['search']) ?>">
                        </div>
                        <div class="col-6 col-sm-2">
                            <select name="division_id" class="form-control form-control-sm">
                                <option value="">Semua Divisi</option>
                                <?php foreach ($divisions as $div): ?>
                                <option value="<?= $div['id'] ?>"
                                    <?= (string) $filters['division_id'] === (string) $div['id'] ? 'selected' : '' ?>>
                                    <?= esc($div['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-sm-2">
                            <select name="role" class="form-control form-control-sm">
                                <option value="">Semua Role</option>
                                <option value="admin"   <?= $filters['role'] === 'admin'   ? 'selected' : '' ?>>Admin</option>
                                <option value="petugas" <?= $filters['role'] === 'petugas' ? 'selected' : '' ?>>Petugas</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-2">
                            <select name="is_active" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-2 d-flex" style="gap:6px;">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-search mr-1"></i>Filter
                            </button>
                            <a href="<?= base_url('staff') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <small class="text-muted">
                    Menampilkan <strong><?= count($rows) ?></strong> dari <strong><?= $total ?></strong> staff
                </small>
                <a href="<?= base_url('staff/create') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>Tambah Staff
                </a>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="pl-3" style="width:40px;">#</th>
                            <th>Nama / Email</th>
                            <th>Divisi</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tiket</th>
                            <th>Terakhir Login</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>Tidak ada staff ditemukan.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rows as $i => $s): ?>
                        <tr>
                            <td class="pl-3 text-muted align-middle" style="font-size:0.8rem;">
                                <?= ($page - 1) * $perPage + $i + 1 ?>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white mr-2 flex-shrink-0"
                                         style="width:32px;height:32px;font-size:0.8rem;
                                                background:<?= $s['is_active'] ? '#1e6f9f' : '#6c757d' ?>;">
                                        <?= strtoupper(mb_substr($s['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold" style="font-size:0.87rem;">
                                            <?= esc($s['name']) ?>
                                        </div>
                                        <small class="text-muted"><?= esc($s['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <small><?= esc($s['division_name'] ?? '—') ?></small>
                            </td>
                            <td class="text-center align-middle">
                                <?= $roleBadge[$s['role']] ?? esc($s['role']) ?>
                            </td>
                            <td class="text-center align-middle">
                                <?= $statusBadge[(int) $s['is_active']] ?>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border" title="Total tiket ditangani">
                                    <?= (int) $s['total_tickets'] ?>
                                </span>
                                <?php if ((int) $s['open_tickets'] > 0): ?>
                                <span class="badge badge-warning text-dark ml-1" title="Tiket aktif">
                                    <?= (int) $s['open_tickets'] ?> aktif
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <small class="text-muted">
                                    <?= ! empty($s['last_login']) ? date('d M Y, H:i', strtotime($s['last_login'])) : '—' ?>
                                </small>
                            </td>
                            <td class="text-center align-middle">
                                <a href="<?= base_url('staff/detail/' . $s['id']) ?>"
                                   class="btn btn-xs btn-outline-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= base_url('staff/edit/' . $s['id']) ?>"
                                   class="btn btn-xs btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?= base_url('staff/toggle/' . $s['id']) ?>"
                                      class="d-inline" onsubmit="return confirm('Yakin ingin mengubah status staff ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-xs <?= $s['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                            title="<?= $s['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="fas <?= $s['is_active'] ? 'fa-ban' : 'fa-check' ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-center py-3">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?tab=staff&page=<?= $page - 1 ?>&<?= http_build_query(array_merge($filters, ['page' => null, 'tab' => null])) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=staff&page=<?= $p ?>&<?= http_build_query(array_merge($filters, ['page' => null, 'tab' => null])) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?tab=staff&page=<?= $page + 1 ?>&<?= http_build_query(array_merge($filters, ['page' => null, 'tab' => null])) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>

        </div><!-- /tab-staff -->


        <!-- =========================================================
             TAB: DIVISI
        ========================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'divisi' ? 'show active' : '' ?>" id="tab-divisi">

            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <small class="text-muted"><?= count($divisions) ?> divisi terdaftar</small>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalDivisiTambah">
                    <i class="fas fa-plus mr-1"></i>Tambah Divisi
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="tbl-divisi">
                    <thead class="thead-light">
                        <tr>
                            <th class="pl-3" style="width:40px;">#</th>
                            <th>Nama Divisi</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Staff</th>
                            <th class="text-center">Layanan</th>
                            <th class="text-center" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($divisions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada divisi.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($divisions as $i => $div): ?>
                        <tr id="div-row-<?= $div['id'] ?>">
                            <td class="pl-3 text-muted align-middle" style="font-size:0.8rem;"><?= $i + 1 ?></td>
                            <td class="align-middle font-weight-bold"><?= esc($div['name']) ?></td>
                            <td class="align-middle">
                                <small class="text-muted"><?= esc($div['description'] ?? '—') ?></small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border"><?= (int) $div['staff_count'] ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border"><?= (int) $div['service_count'] ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-xs btn-outline-warning btn-divisi-edit"
                                        data-id="<?= $div['id'] ?>"
                                        data-name="<?= esc($div['name'], 'attr') ?>"
                                        data-desc="<?= esc($div['description'] ?? '', 'attr') ?>"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger btn-divisi-delete"
                                        data-id="<?= $div['id'] ?>"
                                        data-name="<?= esc($div['name'], 'attr') ?>"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /tab-divisi -->


        <!-- =========================================================
             TAB: LAYANAN
        ========================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'layanan' ? 'show active' : '' ?>" id="tab-layanan">

            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <small class="text-muted"><?= count($services) ?> layanan terdaftar</small>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalLayananTambah">
                    <i class="fas fa-plus mr-1"></i>Tambah Layanan
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="tbl-layanan">
                    <thead class="thead-light">
                        <tr>
                            <th class="pl-3" style="width:40px;">#</th>
                            <th>Nama Layanan</th>
                            <th>Divisi</th>
                            <th class="text-center" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada layanan.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($services as $i => $svc): ?>
                        <tr id="svc-row-<?= $svc['id'] ?>">
                            <td class="pl-3 text-muted align-middle" style="font-size:0.8rem;"><?= $i + 1 ?></td>
                            <td class="align-middle font-weight-bold" style="font-size:0.87rem;">
                                <?= esc($svc['name']) ?>
                            </td>
                            <td class="align-middle">
                                <small class="badge badge-light border"><?= esc($svc['division_name'] ?? '—') ?></small>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-xs btn-outline-warning btn-layanan-edit"
                                        data-id="<?= $svc['id'] ?>"
                                        data-name="<?= esc($svc['name'], 'attr') ?>"
                                        data-division="<?= (int) $svc['divisions_id'] ?>"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger btn-layanan-delete"
                                        data-id="<?= $svc['id'] ?>"
                                        data-name="<?= esc($svc['name'], 'attr') ?>"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /tab-layanan -->

    </div><!-- /tab-content -->
</div><!-- /card -->


<!-- ===============================================================
     MODALS — DIVISI
=============================================================== -->

<!-- Modal Tambah Divisi -->
<div class="modal fade" id="modalDivisiTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sitemap mr-2 text-success"></i>Tambah Divisi</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Nama Divisi <span class="text-danger">*</span></label>
                    <input type="text" id="divisi-tambah-nama" class="form-control" placeholder="Nama divisi...">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Deskripsi</label>
                    <input type="text" id="divisi-tambah-desc" class="form-control" placeholder="Deskripsi singkat...">
                </div>
                <div id="divisi-tambah-error" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-divisi-tambah-simpan">
                    <i class="fas fa-save mr-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Divisi -->
<div class="modal fade" id="modalDivisiEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2 text-warning"></i>Edit Divisi</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="divisi-edit-id">
                <div class="form-group">
                    <label class="font-weight-bold">Nama Divisi <span class="text-danger">*</span></label>
                    <input type="text" id="divisi-edit-nama" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Deskripsi</label>
                    <input type="text" id="divisi-edit-desc" class="form-control">
                </div>
                <div id="divisi-edit-error" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btn-divisi-edit-simpan">
                    <i class="fas fa-save mr-1"></i>Perbarui
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ===============================================================
     MODALS — LAYANAN
=============================================================== -->

<?php
$divisionOptions = '';
foreach ($divisions as $div) {
    $divisionOptions .= '<option value="' . $div['id'] . '">' . esc($div['name']) . '</option>';
}
?>

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="modalLayananTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tag mr-2 text-success"></i>Tambah Layanan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Divisi <span class="text-danger">*</span></label>
                    <select id="layanan-tambah-divisi" class="form-control">
                        <option value="">-- Pilih Divisi --</option>
                        <?= $divisionOptions ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Nama Layanan <span class="text-danger">*</span></label>
                    <input type="text" id="layanan-tambah-nama" class="form-control" placeholder="Nama layanan...">
                </div>
                <div id="layanan-tambah-error" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-layanan-tambah-simpan">
                    <i class="fas fa-save mr-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Layanan -->
<div class="modal fade" id="modalLayananEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2 text-warning"></i>Edit Layanan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="layanan-edit-id">
                <div class="form-group">
                    <label class="font-weight-bold">Divisi <span class="text-danger">*</span></label>
                    <select id="layanan-edit-divisi" class="form-control">
                        <option value="">-- Pilih Divisi --</option>
                        <?= $divisionOptions ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Nama Layanan <span class="text-danger">*</span></label>
                    <input type="text" id="layanan-edit-nama" class="form-control">
                </div>
                <div id="layanan-edit-error" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btn-layanan-edit-simpan">
                    <i class="fas fa-save mr-1"></i>Perbarui
                </button>
            </div>
        </div>
    </div>
</div>


<script>
$(function () {
    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = $('[name="<?= csrf_token() ?>"]').val();

    function csrfData(extra) {
        var d = extra || {};
        d[CSRF_NAME] = CSRF_HASH;
        return d;
    }

    function updateCsrf(res) {
        if (res.csrf) {
            CSRF_HASH = res.csrf;
            $('[name="' + CSRF_NAME + '"]').val(CSRF_HASH);
        }
    }

    function showToast(msg, type) {
        type = type || 'success';
        var cls = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        var $alert = $('<div class="alert ' + cls + ' alert-dismissible fade show shadow-sm" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:260px;">' +
            '<i class="fas ' + icon + ' mr-2"></i>' + escHtml(msg) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        $('body').append($alert);
        setTimeout(function () { $alert.alert('close'); }, 3500);
    }

    // ================================================================
    // DIVISI — Tambah
    // ================================================================
    $('#btn-divisi-tambah-simpan').on('click', function () {
        var $btn  = $(this).prop('disabled', true);
        var nama  = $.trim($('#divisi-tambah-nama').val());
        var desc  = $.trim($('#divisi-tambah-desc').val());
        $('#divisi-tambah-error').hide();

        $.post('<?= base_url('staff/division-store') ?>', csrfData({ name: nama, description: desc }))
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#modalDivisiTambah').modal('hide');
                $('#divisi-tambah-nama').val('');
                $('#divisi-tambah-desc').val('');
                showToast(res.message);
                var row = res.row;
                $('#tbl-divisi tbody tr:first').removeClass('text-center py-4');
                var cnt = $('#tbl-divisi tbody tr').length;
                $('#tbl-divisi tbody').append(
                    '<tr id="div-row-' + row.id + '">' +
                    '<td class="pl-3 text-muted align-middle" style="font-size:0.8rem;">' + (cnt + 1) + '</td>' +
                    '<td class="align-middle font-weight-bold">' + escHtml(row.name) + '</td>' +
                    '<td class="align-middle"><small class="text-muted">' + escHtml(row.description || '—') + '</small></td>' +
                    '<td class="text-center align-middle"><span class="badge badge-light border">0</span></td>' +
                    '<td class="text-center align-middle"><span class="badge badge-light border">0</span></td>' +
                    '<td class="text-center align-middle">' +
                        '<button class="btn btn-xs btn-outline-warning btn-divisi-edit" data-id="' + row.id + '" data-name="' + escAttr(row.name) + '" data-desc="' + escAttr(row.description || '') + '"><i class="fas fa-edit"></i></button> ' +
                        '<button class="btn btn-xs btn-outline-danger btn-divisi-delete" data-id="' + row.id + '" data-name="' + escAttr(row.name) + '"><i class="fas fa-trash"></i></button>' +
                    '</td></tr>'
                );
            } else {
                $('#divisi-tambah-error').text(res.message).show();
            }
        })
        .fail(function (xhr) {
            try { $('#divisi-tambah-error').text(JSON.parse(xhr.responseText).message).show(); } catch(e) {}
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    // DIVISI — Edit button click
    $(document).on('click', '.btn-divisi-edit', function () {
        $('#divisi-edit-id').val($(this).data('id'));
        $('#divisi-edit-nama').val($(this).data('name'));
        $('#divisi-edit-desc').val($(this).data('desc'));
        $('#divisi-edit-error').hide();
        $('#modalDivisiEdit').modal('show');
    });

    $('#btn-divisi-edit-simpan').on('click', function () {
        var $btn = $(this).prop('disabled', true);
        var id   = $('#divisi-edit-id').val();
        var nama = $.trim($('#divisi-edit-nama').val());
        var desc = $.trim($('#divisi-edit-desc').val());
        $('#divisi-edit-error').hide();

        $.post('<?= base_url('staff/division-update') ?>/' + id, csrfData({ name: nama, description: desc }))
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#modalDivisiEdit').modal('hide');
                showToast(res.message);
                var $row = $('#div-row-' + id);
                $row.find('td:eq(1)').text(nama);
                $row.find('td:eq(2) small').text(desc || '—');
                $row.find('.btn-divisi-edit').data('name', nama).data('desc', desc)
                    .attr('data-name', nama).attr('data-desc', desc);
            } else {
                $('#divisi-edit-error').text(res.message).show();
            }
        })
        .fail(function (xhr) {
            try { $('#divisi-edit-error').text(JSON.parse(xhr.responseText).message).show(); } catch(e) {}
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    // DIVISI — Delete
    $(document).on('click', '.btn-divisi-delete', function () {
        var id   = $(this).data('id');
        var nama = $(this).data('name');
        if (! confirm('Hapus divisi "' + nama + '"? Divisi tidak dapat dihapus jika masih memiliki staff atau layanan.')) return;

        $.post('<?= base_url('staff/division-delete') ?>/' + id, csrfData())
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#div-row-' + id).remove();
                showToast(res.message);
            } else {
                showToast(res.message, 'error');
            }
        })
        .fail(function (xhr) {
            try { showToast(JSON.parse(xhr.responseText).message, 'error'); } catch(e) {}
        });
    });

    // ================================================================
    // LAYANAN — Tambah
    // ================================================================
    $('#btn-layanan-tambah-simpan').on('click', function () {
        var $btn  = $(this).prop('disabled', true);
        var nama  = $.trim($('#layanan-tambah-nama').val());
        var divId = $('#layanan-tambah-divisi').val();
        $('#layanan-tambah-error').hide();

        $.post('<?= base_url('staff/service-store') ?>', csrfData({ name: nama, divisions_id: divId }))
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#modalLayananTambah').modal('hide');
                $('#layanan-tambah-nama').val('');
                $('#layanan-tambah-divisi').val('');
                showToast(res.message);
                var row = res.row;
                if (row) {
                    var cnt = $('#tbl-layanan tbody tr').length;
                    $('#tbl-layanan tbody').append(
                        '<tr id="svc-row-' + row.id + '">' +
                        '<td class="pl-3 text-muted align-middle" style="font-size:0.8rem;">' + (cnt + 1) + '</td>' +
                        '<td class="align-middle font-weight-bold" style="font-size:0.87rem;">' + escHtml(row.name) + '</td>' +
                        '<td class="align-middle"><small class="badge badge-light border">' + escHtml(row.division_name || '—') + '</small></td>' +
                        '<td class="text-center align-middle">' +
                            '<button class="btn btn-xs btn-outline-warning btn-layanan-edit" data-id="' + row.id + '" data-name="' + escAttr(row.name) + '" data-division="' + row.divisions_id + '"><i class="fas fa-edit"></i></button> ' +
                            '<button class="btn btn-xs btn-outline-danger btn-layanan-delete" data-id="' + row.id + '" data-name="' + escAttr(row.name) + '"><i class="fas fa-trash"></i></button>' +
                        '</td></tr>'
                    );
                }
            } else {
                $('#layanan-tambah-error').text(res.message).show();
            }
        })
        .fail(function (xhr) {
            try { $('#layanan-tambah-error').text(JSON.parse(xhr.responseText).message).show(); } catch(e) {}
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    // LAYANAN — Edit button click
    $(document).on('click', '.btn-layanan-edit', function () {
        $('#layanan-edit-id').val($(this).data('id'));
        $('#layanan-edit-nama').val($(this).data('name'));
        $('#layanan-edit-divisi').val($(this).data('division'));
        $('#layanan-edit-error').hide();
        $('#modalLayananEdit').modal('show');
    });

    $('#btn-layanan-edit-simpan').on('click', function () {
        var $btn  = $(this).prop('disabled', true);
        var id    = $('#layanan-edit-id').val();
        var nama  = $.trim($('#layanan-edit-nama').val());
        var divId = $('#layanan-edit-divisi').val();
        $('#layanan-edit-error').hide();

        $.post('<?= base_url('staff/service-update') ?>/' + id, csrfData({ name: nama, divisions_id: divId }))
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#modalLayananEdit').modal('hide');
                showToast(res.message);
                var divName = $('#layanan-edit-divisi option:selected').text();
                var $row = $('#svc-row-' + id);
                $row.find('td:eq(1)').text(nama);
                $row.find('td:eq(2) small').text(divName);
                $row.find('.btn-layanan-edit').data('name', nama).data('division', divId)
                    .attr('data-name', nama).attr('data-division', divId);
            } else {
                $('#layanan-edit-error').text(res.message).show();
            }
        })
        .fail(function (xhr) {
            try { $('#layanan-edit-error').text(JSON.parse(xhr.responseText).message).show(); } catch(e) {}
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    // LAYANAN — Delete
    $(document).on('click', '.btn-layanan-delete', function () {
        var id   = $(this).data('id');
        var nama = $(this).data('name');
        if (! confirm('Hapus layanan "' + nama + '"? Layanan yang sudah digunakan tiket tidak dapat dihapus.')) return;

        $.post('<?= base_url('staff/service-delete') ?>/' + id, csrfData())
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                $('#svc-row-' + id).remove();
                showToast(res.message);
            } else {
                showToast(res.message, 'error');
            }
        })
        .fail(function (xhr) {
            try { showToast(JSON.parse(xhr.responseText).message, 'error'); } catch(e) {}
        });
    });

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(str) {
        return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
});
</script>
