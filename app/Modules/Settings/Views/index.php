<?php
// Helper untuk ambil nilai setting dengan fallback
function sv(array $settings, string $group, string $key, string $default = ''): string {
    return esc($settings[$group][$key]['setting_value'] ?? $default);
}
function sd(array $settings, string $group, string $key): string {
    return esc($settings[$group][$key]['description'] ?? '');
}
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
<div class="col-12 col-md-3 mb-3">

    <!-- Nav Pills Vertikal -->
    <div class="card shadow-sm">
        <div class="card-header py-2">
            <h6 class="mb-0"><i class="fas fa-cog mr-2"></i>Grup Pengaturan</h6>
        </div>
        <div class="card-body p-0">
            <div class="nav flex-column nav-pills" id="settings-pills" role="tablist">
                <a class="nav-link rounded-0 border-bottom px-3 py-3 <?= $activeTab === 'general'  ? 'active' : '' ?>"
                   data-toggle="pill" href="#pane-general" role="tab">
                    <i class="fas fa-sliders-h mr-2"></i>General
                </a>
                <a class="nav-link rounded-0 border-bottom px-3 py-3 <?= $activeTab === 'rag'     ? 'active' : '' ?>"
                   data-toggle="pill" href="#pane-rag" role="tab">
                    <i class="fas fa-brain mr-2"></i>RAG / AI
                </a>
                <a class="nav-link rounded-0 border-bottom px-3 py-3 <?= $activeTab === 'n8n'     ? 'active' : '' ?>"
                   data-toggle="pill" href="#pane-n8n" role="tab">
                    <i class="fas fa-project-diagram mr-2"></i>N8N
                </a>
                <a class="nav-link rounded-0 border-bottom px-3 py-3 <?= $activeTab === 'waha'    ? 'active' : '' ?>"
                   data-toggle="pill" href="#pane-waha" role="tab">
                    <i class="fab fa-whatsapp mr-2 text-success"></i>WAHA
                </a>
                <a class="nav-link rounded-0 px-3 py-3 <?= $activeTab === 'supabase' ? 'active' : '' ?>"
                   data-toggle="pill" href="#pane-supabase" role="tab">
                    <i class="fas fa-database mr-2"></i>Supabase
                </a>
            </div>
        </div>
    </div>

</div>
<div class="col-12 col-md-9">

    <div class="tab-content">

        <!-- ============================================================
             GENERAL
        ============================================================ -->
        <div class="tab-pane fade <?= $activeTab === 'general' ? 'show active' : '' ?>" id="pane-general">
            <form method="POST" action="<?= base_url('settings/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="group" value="General">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sliders-h mr-2 text-primary"></i>Pengaturan General
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- App Name -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Nama Aplikasi
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="app_name" class="form-control"
                                       value="<?= sv($settings, 'General', 'app_name', 'GAVI') ?>">
                                <small class="text-muted"><?= sd($settings, 'General', 'app_name') ?></small>
                            </div>
                        </div>

                        <!-- Global AI Mode -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Master AI Switch
                            </label>
                            <div class="col-sm-8">
                                <?php $aiOn = ($settings['General']['global_ai_mode']['setting_value'] ?? '0') === '1'; ?>
                                <div class="custom-control custom-switch mt-1">
                                    <input type="hidden" name="global_ai_mode" value="0">
                                    <input type="checkbox" class="custom-control-input"
                                           id="global_ai_mode" name="global_ai_mode" value="1"
                                           <?= $aiOn ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="global_ai_mode">
                                        <?= $aiOn
                                            ? '<span class="text-success font-weight-bold">AI Aktif</span>'
                                            : '<span class="text-muted">AI Nonaktif</span>' ?>
                                    </label>
                                </div>
                                <small class="text-muted"><?= sd($settings, 'General', 'global_ai_mode') ?></small>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan General
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /pane-general -->


        <!-- ============================================================
             RAG / AI
        ============================================================ -->
        <div class="tab-pane fade <?= $activeTab === 'rag' ? 'show active' : '' ?>" id="pane-rag">
            <form method="POST" action="<?= base_url('settings/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="group" value="RAG">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-brain mr-2 text-info"></i>Pengaturan RAG / AI
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- Confidence Threshold -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Confidence Threshold
                                <span class="badge badge-info ml-1" id="conf-label">
                                    <?= sv($settings, 'RAG', 'rag_confidence_threshold', '80') ?>%
                                </span>
                            </label>
                            <div class="col-sm-8">
                                <input type="range" class="form-control-range mt-2"
                                       name="rag_confidence_threshold"
                                       id="rag_confidence_threshold"
                                       min="0" max="100" step="5"
                                       value="<?= sv($settings, 'RAG', 'rag_confidence_threshold', '80') ?>"
                                       oninput="document.getElementById('conf-label').textContent = this.value + '%'">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">0%</small>
                                    <small class="text-muted">50%</small>
                                    <small class="text-muted">100%</small>
                                </div>
                                <small class="text-muted"><?= sd($settings, 'RAG', 'rag_confidence_threshold') ?></small>
                            </div>
                        </div>

                        <!-- System Prompt -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                System Prompt
                            </label>
                            <div class="col-sm-8">
                                <textarea name="rag_system_prompt" class="form-control"
                                          rows="10" style="font-size:0.85rem; font-family:monospace; resize:vertical;"
                                          placeholder="Masukkan system prompt untuk AI..."><?= sv($settings, 'RAG', 'rag_system_prompt') ?></textarea>
                                <small class="text-muted"><?= sd($settings, 'RAG', 'rag_system_prompt') ?></small>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan RAG
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /pane-rag -->


        <!-- ============================================================
             N8N
        ============================================================ -->
        <div class="tab-pane fade <?= $activeTab === 'n8n' ? 'show active' : '' ?>" id="pane-n8n">
            <form method="POST" action="<?= base_url('settings/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="group" value="N8N">

                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-project-diagram mr-2 text-warning"></i>Pengaturan N8N
                        </h5>
                        <button type="button" class="btn btn-outline-info btn-sm" id="btn-test-n8n">
                            <i class="fas fa-plug mr-1"></i>Test Koneksi
                        </button>
                    </div>
                    <div class="card-body">

                        <div id="n8n-test-result" class="mb-3" style="display:none;"></div>

                        <!-- Webhook URL -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Webhook URL
                            </label>
                            <div class="col-sm-8">
                                <input type="url" name="n8n_webhook_url" class="form-control"
                                       value="<?= sv($settings, 'N8N', 'n8n_webhook_url') ?>"
                                       placeholder="https://n8n.domain.com/webhook/...">
                                <small class="text-muted"><?= sd($settings, 'N8N', 'n8n_webhook_url') ?></small>
                            </div>
                        </div>

                        <!-- API Token -->
                        <div class="form-group row mb-0">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                API Token
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="password" name="n8n_api_token" id="inp-n8n-token"
                                           class="form-control"
                                           value="<?= sv($settings, 'N8N', 'n8n_api_token') ?>"
                                           placeholder="Bearer token...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-toggle-pass"
                                                data-target="inp-n8n-token">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted"><?= sd($settings, 'N8N', 'n8n_api_token') ?></small>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan N8N
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /pane-n8n -->


        <!-- ============================================================
             WAHA
        ============================================================ -->
        <div class="tab-pane fade <?= $activeTab === 'waha' ? 'show active' : '' ?>" id="pane-waha">
            <form method="POST" action="<?= base_url('settings/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="group" value="WAHA">

                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="fab fa-whatsapp mr-2 text-success"></i>Pengaturan WAHA (WhatsApp)
                        </h5>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btn-test-waha">
                            <i class="fas fa-plug mr-1"></i>Test Koneksi
                        </button>
                    </div>
                    <div class="card-body">

                        <div id="waha-test-result" class="mb-3" style="display:none;"></div>

                        <!-- Endpoint URL -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Endpoint URL
                            </label>
                            <div class="col-sm-8">
                                <input type="url" name="waha_endpoint_url" class="form-control"
                                       value="<?= sv($settings, 'WAHA', 'waha_endpoint_url') ?>"
                                       placeholder="http://localhost:3000">
                                <small class="text-muted"><?= sd($settings, 'WAHA', 'waha_endpoint_url') ?></small>
                            </div>
                        </div>

                        <!-- Session Name -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Session Name
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="waha_session_name" class="form-control"
                                       value="<?= sv($settings, 'WAHA', 'waha_session_name', 'default') ?>"
                                       placeholder="default">
                                <small class="text-muted"><?= sd($settings, 'WAHA', 'waha_session_name') ?></small>
                            </div>
                        </div>

                        <!-- API Key -->
                        <div class="form-group row mb-0">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                API Key
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="password" name="waha_api_key" id="inp-waha-key"
                                           class="form-control"
                                           value="<?= sv($settings, 'WAHA', 'waha_api_key') ?>"
                                           placeholder="API Key WAHA...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-toggle-pass"
                                                data-target="inp-waha-key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted"><?= sd($settings, 'WAHA', 'waha_api_key') ?></small>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan WAHA
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /pane-waha -->


        <!-- ============================================================
             SUPABASE
        ============================================================ -->
        <div class="tab-pane fade <?= $activeTab === 'supabase' ? 'show active' : '' ?>" id="pane-supabase">
            <form method="POST" action="<?= base_url('settings/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="group" value="Supabase">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-database mr-2 text-secondary"></i>Pengaturan Supabase
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- Supabase URL -->
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Project URL
                            </label>
                            <div class="col-sm-8">
                                <input type="url" name="supabase_url" class="form-control"
                                       value="<?= sv($settings, 'Supabase', 'supabase_url') ?>"
                                       placeholder="https://xxxx.supabase.co">
                                <small class="text-muted"><?= sd($settings, 'Supabase', 'supabase_url') ?></small>
                            </div>
                        </div>

                        <!-- Service Role Key -->
                        <div class="form-group row mb-0">
                            <label class="col-sm-4 col-form-label font-weight-bold">
                                Service Role Key
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="password" name="supabase_service_role_key"
                                           id="inp-supabase-key" class="form-control"
                                           value="<?= sv($settings, 'Supabase', 'supabase_service_role_key') ?>"
                                           placeholder="eyJ...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-toggle-pass"
                                                data-target="inp-supabase-key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted"><?= sd($settings, 'Supabase', 'supabase_service_role_key') ?></small>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Simpan Pengaturan Supabase
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /pane-supabase -->

    </div><!-- /tab-content -->
</div><!-- /col-md-9 -->
</div><!-- /row -->


<script>
$(function () {
    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = $('[name="<?= csrf_token() ?>"]').first().val();

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

    // ---- Toggle password visibility ----
    $(document).on('click', '.btn-toggle-pass', function () {
        var $inp  = $('#' + $(this).data('target'));
        var $icon = $(this).find('i');
        if ($inp.attr('type') === 'password') {
            $inp.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $inp.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ---- AI toggle label update ----
    $('#global_ai_mode').on('change', function () {
        var $lbl = $(this).next('label');
        if ($(this).is(':checked')) {
            $lbl.html('<span class="text-success font-weight-bold">AI Aktif</span>');
        } else {
            $lbl.html('<span class="text-muted">AI Nonaktif</span>');
        }
    });

    // ---- Test WAHA ----
    $('#btn-test-waha').on('click', function () {
        var $btn    = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menguji...');
        var $result = $('#waha-test-result');
        $result.hide();

        $.post('<?= base_url('settings/test-connection') ?>', csrfData({ target: 'waha' }))
        .done(function (res) {
            updateCsrf(res);
            var cls = res.success ? 'alert-success' : 'alert-danger';
            var ico = res.success ? 'fa-check-circle' : 'fa-times-circle';
            $result.html('<div class="alert ' + cls + ' mb-0"><i class="fas ' + ico + ' mr-2"></i>' + res.message + '</div>').show();
        })
        .fail(function () {
            $result.html('<div class="alert alert-danger mb-0"><i class="fas fa-times-circle mr-2"></i>Gagal mengirim permintaan.</div>').show();
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-plug mr-1"></i>Test Koneksi');
        });
    });

    // ---- Test N8N ----
    $('#btn-test-n8n').on('click', function () {
        var $btn    = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menguji...');
        var $result = $('#n8n-test-result');
        $result.hide();

        $.post('<?= base_url('settings/test-connection') ?>', csrfData({ target: 'n8n' }))
        .done(function (res) {
            updateCsrf(res);
            var cls = res.success ? 'alert-success' : 'alert-danger';
            var ico = res.success ? 'fa-check-circle' : 'fa-times-circle';
            $result.html('<div class="alert ' + cls + ' mb-0"><i class="fas ' + ico + ' mr-2"></i>' + res.message + '</div>').show();
        })
        .fail(function () {
            $result.html('<div class="alert alert-danger mb-0"><i class="fas fa-times-circle mr-2"></i>Gagal mengirim permintaan.</div>').show();
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-plug mr-1"></i>Test Koneksi');
        });
    });

    // ---- Preserve active tab on page load via URL hash / param ----
    var tabMap = {
        general:  '#pane-general',
        rag:      '#pane-rag',
        n8n:      '#pane-n8n',
        waha:     '#pane-waha',
        supabase: '#pane-supabase',
    };
    var activeTab = '<?= esc($activeTab) ?>';
    if (tabMap[activeTab]) {
        $('[href="' + tabMap[activeTab] + '"]').tab('show');
    }
});
</script>
