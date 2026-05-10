<?php
$errors = session()->getFlashdata('errors') ?? [];
$error  = session()->getFlashdata('error') ?? '';

function old_val(string $key, $default = ''): string {
    $old = old($key);
    return $old !== null ? esc($old) : esc($default);
}
?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i><?= esc($error) ?>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<?php if (! empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <strong><i class="fas fa-exclamation-triangle mr-1"></i>Perbaiki kesalahan berikut:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ($errors as $err): ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-<?= $isEdit ? 'edit text-warning' : 'user-plus text-success' ?> mr-2"></i>
                    <?= $isEdit ? 'Edit Data Staff' : 'Tambah Staff Baru' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= $isEdit ? base_url('staff/update/' . $staff['id']) : base_url('staff/store') ?>">
                    <?= csrf_field() ?>

                    <!-- Nama -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               value="<?= old_val('name', $staff['name'] ?? '') ?>"
                               placeholder="Nama lengkap staff..."
                               required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" class="form-control"
                               value="<?= old_val('email', $staff['email'] ?? '') ?>"
                               placeholder="email@domain.com"
                               required>
                        <small class="text-muted">Digunakan untuk login ke dashboard.</small>
                    </div>

                    <?php if (! $isEdit): ?>
                    <!-- Password (hanya saat create) -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="inp-password"
                                   class="form-control" placeholder="Minimal 6 karakter"
                                   minlength="6" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass()" id="btn-toggle-pass">
                                    <i class="fas fa-eye" id="pass-icon"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Untuk ubah password gunakan menu Reset Password di halaman detail staff.</small>
                    </div>
                    <?php endif; ?>

                    <!-- Divisi -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Divisi <span class="text-danger">*</span>
                        </label>
                        <select name="division_id" class="form-control" required>
                            <option value="">-- Pilih Divisi --</option>
                            <?php foreach ($divisions as $div): ?>
                            <?php
                            $selected = '';
                            $oldDiv = old('division_id');
                            if ($oldDiv !== null) {
                                $selected = (string) $oldDiv === (string) $div['id'] ? 'selected' : '';
                            } elseif ($isEdit && (string) ($staff['division_id'] ?? '') === (string) $div['id']) {
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?= $div['id'] ?>" <?= $selected ?>>
                                <?= esc($div['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex" style="gap:16px;">
                            <?php
                            $currentRole = old('role') ?? ($staff['role'] ?? 'petugas');
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role"
                                       id="role-petugas" value="petugas"
                                       <?= $currentRole === 'petugas' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role-petugas">
                                    <span class="badge badge-primary mr-1">Petugas</span>
                                    <small class="text-muted">Akses operasional tiket</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role"
                                       id="role-admin" value="admin"
                                       <?= $currentRole === 'admin' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role-admin">
                                    <span class="badge badge-danger mr-1">Admin</span>
                                    <small class="text-muted">Akses penuh termasuk manajemen staff</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?= $isEdit ? base_url('staff/detail/' . $staff['id']) : base_url('staff') ?>"
                           class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-success' ?>">
                            <i class="fas fa-save mr-1"></i>
                            <?= $isEdit ? 'Perbarui Data' : 'Simpan Staff' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (! $isEdit): ?>
<script>
function togglePass() {
    var inp  = document.getElementById('inp-password');
    var icon = document.getElementById('pass-icon');
    if (inp.type === 'password') {
        inp.type  = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        inp.type  = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
<?php endif; ?>
