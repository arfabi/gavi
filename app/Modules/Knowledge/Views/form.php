<?php $isEdit = $doc !== null; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?> me-2"></i>
                    <?= $isEdit ? 'Edit Dokumen' : 'Tambah Dokumen Baru' ?>
                </h3>
            </div>
            <form method="POST"
                  action="<?= base_url($isEdit ? 'knowledge/update/' . $doc['id'] : 'knowledge/store') ?>">
                <?= csrf_field() ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-warning mx-3 mt-3">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="form-group">
                        <label>Kategori Layanan <span class="text-danger">*</span></label>
                        <select name="service_category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= old('service_category_id', $doc['service_category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul / Keyword <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control"
                               placeholder="Judul atau kata kunci dokumen"
                               value="<?= esc(old('judul', $doc['judul'] ?? '')) ?>"
                               required maxlength="200">
                        <small class="text-muted">Max 200 karakter. Gunakan kata kunci yang mudah dicari.</small>
                    </div>

                    <div class="form-group">
                        <label>Konten / Isi Dokumen <span class="text-danger">*</span></label>
                        <textarea name="konten" class="form-control" rows="12"
                                  placeholder="Isi dokumen untuk RAG knowledge base..."
                                  required><?= esc(old('konten', $doc['konten'] ?? '')) ?></textarea>
                        <small class="text-muted">
                            Tulis konten yang jelas dan informatif. Teks ini akan digunakan AI untuk menjawab pertanyaan masyarakat.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="aktif" name="aktif"
                                   value="1"
                                   <?= old('aktif', $doc['aktif'] ?? 1) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="aktif">
                                Dokumen Aktif
                                <small class="text-muted">(dokumen nonaktif tidak akan digunakan AI)</small>
                            </label>
                        </div>
                    </div>

                    <?php if ($isEdit): ?>
                    <div class="alert alert-light border">
                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Status Sync:</strong>
                                <?php if ($doc['synced_to_supabase']): ?>
                                    <span class="badge badge-synced ms-1">Tersinkron</span>
                                <?php else: ?>
                                    <span class="badge badge-unsynced ms-1">Belum Sync</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-6">
                                <strong>Dibuat oleh:</strong> <?= esc($doc['creator_name'] ?? '-') ?>
                            </div>
                        </div>
                        <?php if ($doc['synced_to_supabase']): ?>
                        <small class="text-warning d-block mt-1">
                            <i class="fas fa-exclamation-triangle"></i>
                            Menyimpan perubahan akan mereset status sync. Sync ulang setelah disimpan.
                        </small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('knowledge') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Dokumen' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
