<?php
$priorityBadge = [
    'high'   => '<span class="badge priority-high">High</span>',
    'medium' => '<span class="badge priority-medium">Medium</span>',
    'low'    => '<span class="badge priority-low">Low</span>',
];
$statusBadge = [
    'open'     => '<span class="badge badge-danger">Terbuka</span>',
    'pending'  => '<span class="badge badge-warning text-dark">Pending</span>',
    'resolved' => '<span class="badge badge-success">Selesai</span>',
    'closed'   => '<span class="badge badge-secondary">Ditutup</span>',
];
$isActive   = in_array($ticket['status'], ['open', 'pending']);
$canAssign  = $isActive && empty($ticket['assigned_staff_id']);
$isAssigned = (int) ($ticket['assigned_staff_id'] ?? 0) === $staffId;
$canResolve = $isActive && ($isAdmin || $isAssigned);
$canReply   = $isActive && ($isAdmin || $isAssigned);
?>

<!-- ============================================================
     HEADER — col-md-12
============================================================ -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:8px;">
            <div>
                <h4 class="mb-0 font-weight-bold text-dark">
                    <?= esc($ticket['ticket_number']) ?>
                </h4>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Dibuat <?= date('d M Y \p\u\k\u\l H:i', strtotime($ticket['created_at'])) ?>
                    &nbsp;&mdash;&nbsp;
                    <i class="fas fa-tag mr-1"></i>
                    <?= esc($ticket['category_name'] ?? '-') ?>
                </small>
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                <?= $priorityBadge[$ticket['priority']] ?? '' ?>
                <?= $statusBadge[$ticket['status']] ?? '' ?>
                <a href="<?= base_url('tickets') ?>" class="btn btn-outline-secondary btn-sm ml-1">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
            </div>
        </div>
        <hr class="mt-2 mb-0">
    </div>
</div>


<!-- ============================================================
     MAIN CONTENT ROW
============================================================ -->
<div class="row">

    <!-- ============================================================
         KIRI: col-md-4 — 3 Tabs
    ============================================================ -->
    <div class="col-12 col-md-4 mb-3 mb-md-0">

        <ul class="nav nav-pills nav-fill" id="leftTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active px-3 py-2" data-toggle="tab" href="#pane-detail" role="tab"
                    style="font-size:0.82rem;">
                    <i class="fas fa-info-circle mr-1"></i>Detail
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-3 py-2" data-toggle="tab" href="#pane-templates" role="tab"
                    style="font-size:0.82rem;">
                    <i class="fas fa-book mr-1"></i>Template
                    <?php if (! empty($templates)): ?>
                        <span class="badge badge-primary ml-1" style="font-size:0.68rem;"><?= count($templates) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-3 py-2" data-toggle="tab" href="#pane-customer" role="tab"
                    style="font-size:0.82rem;">
                    <i class="fas fa-user mr-1"></i>Customer
                </a>
            </li>
        </ul>

        <div class="tab-content card border-top-0 rounded-0 rounded-bottom shadow-sm"
            style="min-height:300px;">

            <!-- ===== Tab: Detail Tiket ===== -->
            <div class="tab-pane fade show active p-0" id="pane-detail" role="tabpanel">

                <!-- Aksi Tiket -->
                <div class="px-3 pt-3 pb-2 border-bottom">
                    <p class="text-muted mb-2" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:600;">
                        <i class="fas fa-tools mr-1"></i>Aksi Tiket
                    </p>

                    <?php if ($canAssign): ?>
                        <form method="POST" action="<?= base_url('tickets/assign/' . $ticket['id']) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-block btn-sm mb-2">
                                <i class="fas fa-hand-paper mr-1"></i> Ambil Tiket Ini
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($canResolve): ?>
                        <button type="button" class="btn btn-success btn-block btn-sm mb-2"
                            data-toggle="modal" data-target="#resolveModal">
                            <i class="fas fa-check-circle mr-1"></i> Tandai Selesai
                        </button>
                    <?php endif; ?>

                    <?php if ($isActive && $isAdmin && ! empty($ticket['assigned_staff_id'])): ?>
                        <form method="POST" action="<?= base_url('tickets/assign/' . $ticket['id']) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-warning btn-block btn-sm mb-2">
                                <i class="fas fa-exchange-alt mr-1"></i> Ambil Alih
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Informasi Tiket -->
                <div class="px-3 pt-2 pb-1 border-bottom">
                    <p class="text-muted mb-1" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:600;">
                        <i class="fas fa-info-circle mr-1"></i>Informasi Tiket
                    </p>
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.83rem;">
                        <tr>
                            <td class="text-muted py-1 pl-0" style="width:42%; white-space:nowrap;">No. Tiket</td>
                            <td class="py-1 font-weight-bold text-primary"><?= esc($ticket['ticket_number']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Status</td>
                            <td class="py-1"><?= $statusBadge[$ticket['status']] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Prioritas</td>
                            <td class="py-1"><?= $priorityBadge[$ticket['priority']] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Kategori</td>
                            <td class="py-1"><small><?= esc($ticket['category_name'] ?? '-') ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Petugas</td>
                            <td class="py-1">
                                <?php if (! empty($ticket['assigned_staff_id'])): ?>
                                    <i class="fas fa-user-tie text-success mr-1"></i>
                                    <small><?= esc($ticket['assigned_name']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted font-italic small">Belum di-assign</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Dibuat</td>
                            <td class="py-1"><small><?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Diupdate</td>
                            <td class="py-1"><small><?= date('d M Y, H:i', strtotime($ticket['updated_at'])) ?></small></td>
                        </tr>
                        <?php if ($ticket['resolved_at']): ?>
                            <tr>
                                <td class="text-muted py-1 pl-0">Diselesaikan</td>
                                <td class="py-1 text-success"><small><?= date('d M Y, H:i', strtotime($ticket['resolved_at'])) ?></small></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Catatan Petugas (jika resolved) -->
                <?php if (! empty($ticket['catatan_petugas'])): ?>
                    <div class="px-3 pt-2 pb-3">
                        <p class="text-muted mb-1" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:600;">
                            <i class="fas fa-clipboard-check mr-1 text-success"></i>Catatan Penyelesaian
                        </p>
                        <div class="bg-light rounded p-2 border-left border-success" style="font-size:0.83rem; border-left-width:3px !important;">
                            <?= nl2br(esc($ticket['catatan_petugas'])) ?>
                            <?php if ($ticket['resolved_at']): ?>
                                <div class="text-muted mt-1" style="font-size:0.75rem;">
                                    <i class="fas fa-clock mr-1"></i><?= date('d M Y, H:i', strtotime($ticket['resolved_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-3"></div>
                <?php endif; ?>

            </div><!-- /pane-detail -->


            <!-- ===== Tab: Template Balasan ===== -->
            <div class="tab-pane fade p-0" id="pane-templates" role="tabpanel">

                <!-- Search -->
                <div class="px-3 pt-3 pb-2 border-bottom bg-light">
                    <div class="input-group input-group-sm">
                        <input type="text" id="template-search" class="form-control"
                            placeholder="Cari template..."
                            autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-template-search">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-template-reset"
                                title="Reset ke kategori tiket ini">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-1">
                        <small class="text-muted" id="template-hint">
                            <i class="fas fa-tag mr-1 text-primary"></i>
                            Menampilkan template untuk: <strong><?= esc($ticket['category_name'] ?? 'semua kategori') ?></strong>
                        </small>
                    </div>
                </div>

                <!-- Template List -->
                <div id="template-list" style="max-height:420px; overflow-y:auto;">
                    <?php if (empty($templates)): ?>
                        <div class="text-center py-4 text-muted" id="template-empty">
                            <i class="fas fa-book-open fa-2x d-block mb-2"></i>
                            Tidak ada template untuk kategori ini.
                        </div>
                    <?php else: ?>
                        <?php foreach ($templates as $i => $tpl): ?>
                            <div class="template-item border-bottom px-3 py-2 d-flex align-items-start"
                                style="cursor:pointer; transition:background .12s;"
                                data-konten="<?= esc($tpl['konten'], 'attr') ?>"
                                onmouseover="this.style.background='#EBF4FA'"
                                onmouseout="this.style.background='#fff'">
                                <div class="mr-2 mt-1 flex-shrink-0">
                                    <span class="badge badge-light border text-muted"
                                        style="font-size:0.68rem; min-width:22px; text-align:center;">
                                        <?= $i + 1 ?>
                                    </span>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="font-weight-bold text-dark"
                                        style="font-size:0.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= esc($tpl['judul']) ?>
                                    </div>
                                    <div class="text-muted"
                                        style="font-size:0.75rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= esc(mb_substr(strip_tags($tpl['konten']), 0, 80)) ?>...
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0 text-primary" style="font-size:0.8rem;">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div><!-- /pane-templates -->


            <!-- ===== Tab: Profil Customer ===== -->
            <div class="tab-pane fade p-0" id="pane-customer" role="tabpanel">

                <!-- Avatar -->
                <div class="text-center pt-3 pb-2 border-bottom">
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white mb-2"
                        style="width:52px;height:52px;font-size:1.4rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="font-weight-bold"><?= esc($ticket['customer_name'] ?? 'Tidak diketahui') ?></div>
                    <small class="text-muted">
                        <i class="fab fa-whatsapp text-success mr-1"></i>
                        <?= esc($ticket['whatsapp_number'] ?? '-') ?>
                    </small>
                    <div class="mt-1">
                        <?php if (! empty($ticket['ai_mode'])): ?>
                            <span class="badge badge-success" style="font-size:0.72rem;"><i class="fas fa-robot mr-1"></i>AI Mode Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-secondary" style="font-size:0.72rem;"><i class="fas fa-robot mr-1"></i>AI Nonaktif</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Data Diri -->
                <div class="px-3 py-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.83rem;">
                        <tr>
                            <td class="text-muted py-1 pl-0" style="width:42%; white-space:nowrap;">NIK</td>
                            <td class="py-1"><?= esc($ticket['nik'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Instansi</td>
                            <td class="py-1"><small><?= esc($ticket['instansi'] ?? '—') ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Kota</td>
                            <td class="py-1"><small><?= esc($ticket['address_city'] ?? '—') ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Provinsi</td>
                            <td class="py-1"><small><?= esc($ticket['address_province'] ?? '—') ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Alamat</td>
                            <td class="py-1"><small><?= esc($ticket['address'] ?? '—') ?></small></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Terdaftar</td>
                            <td class="py-1">
                                <small><?= ! empty($ticket['customer_created_at']) ? date('d M Y', strtotime($ticket['customer_created_at'])) : '—' ?></small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1 pl-0">Terakhir Aktif</td>
                            <td class="py-1 text-success">
                                <small><?= ! empty($ticket['last_interaction']) ? date('d M Y, H:i', strtotime($ticket['last_interaction'])) : '—' ?></small>
                            </td>
                        </tr>
                    </table>
                    <div class="mt-2">
                        <a href="<?= base_url('customers/detail/' . $ticket['customer_id']) ?>"
                            class="btn btn-outline-primary btn-sm btn-block" target="_blank">
                            <i class="fas fa-external-link-alt mr-1"></i>Lihat Profil Lengkap
                        </a>
                    </div>
                </div>

            </div><!-- /pane-customer -->

        </div><!-- /tab-content -->
    </div><!-- /col-md-4 -->


    <!-- ============================================================
         KANAN: col-md-8 — Ringkasan + Percakapan + Reply
    ============================================================ -->
    <div class="col-12 col-md-8">

        <!-- Ringkasan AI -->
        <div class="card shadow-sm mb-3" style="border-left: 4px solid #1e6f9f;">
            <div class="card-body py-2 px-3">
                <p class="mb-1" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:#1e6f9f; font-weight:600;">
                    <i class="fas fa-robot mr-1"></i>Ringkasan AI
                </p>
                <p class="mb-0" style="font-size:0.92rem; line-height:1.6;">
                    <?= esc($ticket['summary']) ?>
                </p>
            </div>
        </div>

        <!-- Percakapan -->
        <div class="card shadow-sm">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">
                    <i class="fas fa-comments mr-2"></i>Riwayat Percakapan
                </h6>
                <span class="badge badge-secondary" id="chat-count"><?= count($chats) ?> pesan</span>
            </div>

            <!-- Chat bubbles -->
            <div class="card-body py-3 px-3" id="chat-container"
                style="height:420px; overflow-y:auto; background:#f8f9fa;">

                <?php if (empty($chats)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comment-slash fa-2x d-block mb-2"></i>
                        Belum ada riwayat percakapan.
                    </div>
                <?php else: ?>
                    <?php $lastDate = ''; ?>
                    <?php foreach ($chats as $chat): ?>
                        <?php $chatDate = date('d M Y', strtotime($chat['created_at'])); ?>
                        <?php if ($chatDate !== $lastDate): ?>
                            <div class="text-center my-3">
                                <span class="badge badge-light border text-muted px-3 py-1" style="font-size:0.75rem;">
                                    <?= $chatDate ?>
                                </span>
                            </div>
                            <?php $lastDate = $chatDate; ?>
                        <?php endif; ?>

                        <?php if ($chat['sender'] === 'customer'): ?>
                            <div class="d-flex mb-3">
                                <div class="mr-2 mt-1">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="fas fa-user" style="font-size:0.8rem;"></i>
                                    </div>
                                </div>
                                <div style="max-width:75%;">
                                    <div class="chat-bubble customer shadow-sm">
                                        <?= nl2br(esc($chat['message'])) ?>
                                    </div>
                                    <div class="chat-time mt-1">
                                        <i class="fas fa-user mr-1"></i><?= esc($ticket['customer_name'] ?? 'Customer') ?>
                                        &middot; <?= date('H:i', strtotime($chat['created_at'])) ?>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($chat['sender'] === 'ai'): ?>
                            <div class="d-flex justify-content-end mb-3">
                                <div style="max-width:75%;">
                                    <div class="chat-bubble ai shadow-sm">
                                        <?= nl2br(esc($chat['message'])) ?>
                                        <?php if (! empty($chat['confidence_score'])): ?>
                                            <div class="mt-2 pt-1" style="border-top:1px solid rgba(0,0,0,0.08); font-size:0.72rem; color:#555;">
                                                <i class="fas fa-brain mr-1 text-info"></i>
                                                Confidence: <strong><?= number_format($chat['confidence_score'], 1) ?>%</strong>
                                                <?php if (! empty($chat['intent'])): ?>
                                                    &bull; Intent: <code style="font-size:0.7rem;"><?= esc($chat['intent']) ?></code>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chat-time text-right mt-1">
                                        <i class="fas fa-robot mr-1 text-info"></i>GAVI AI
                                        <?php if (! empty($chat['agent_handler'])): ?>
                                            &middot; <span style="text-transform:capitalize;"><?= esc($chat['agent_handler']) ?></span>
                                        <?php endif; ?>
                                        &middot; <?= date('H:i', strtotime($chat['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="ml-2 mt-1">
                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="fas fa-robot" style="font-size:0.8rem;"></i>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="d-flex justify-content-end mb-3">
                                <div style="max-width:75%;">
                                    <div class="chat-bubble staff shadow-sm">
                                        <?= nl2br(esc($chat['message'])) ?>
                                    </div>
                                    <div class="chat-time text-right mt-1">
                                        <i class="fas fa-user-tie mr-1 text-success"></i>
                                        <?= esc($chat['staff_name'] ?? 'Petugas') ?>
                                        &middot; <?= date('H:i', strtotime($chat['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="ml-2 mt-1">
                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="fas fa-user-tie" style="font-size:0.8rem;"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div><!-- /#chat-container -->

            <!-- Reply Box -->
            <?php if ($canReply): ?>
                <div class="card-footer p-0">
                    <div class="px-3 py-2">
                        <div class="d-flex align-items-end" style="gap:8px;">
                            <div style="flex:1;">
                                <textarea id="reply-input" class="form-control form-control-sm"
                                    rows="3"
                                    placeholder="Ketik balasan... atau klik template di tab Template sebelah kiri."
                                    style="resize:vertical; min-height:72px; max-height:200px;"></textarea>
                                <div id="reply-error" class="text-danger small mt-1" style="display:none;"></div>
                            </div>
                            <div>
                                <button type="button" id="btn-send-reply"
                                    class="btn btn-primary btn-sm"
                                    style="height:72px; width:72px; white-space:normal; font-size:0.8rem;">
                                    <i class="fas fa-paper-plane d-block mb-1"></i>Kirim
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">
                                <i class="fas fa-user-tie mr-1 text-success"></i>
                                Balas sebagai: <strong><?= esc(session()->get('name')) ?></strong>
                            </small>
                            <small class="text-muted" id="reply-status"></small>
                        </div>
                    </div>
                </div>

            <?php elseif (! $isActive): ?>
                <div class="card-footer bg-light text-center py-2">
                    <small class="text-muted">
                        <i class="fas fa-lock mr-1"></i>
                        Tiket sudah <strong><?= $ticket['status'] === 'resolved' ? 'diselesaikan' : 'ditutup' ?></strong> — balasan tidak dapat dikirim.
                    </small>
                </div>
            <?php else: ?>
                <div class="card-footer bg-light text-center py-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ambil tiket ini terlebih dahulu untuk dapat membalas percakapan.
                    </small>
                </div>
            <?php endif; ?>

        </div><!-- /card percakapan -->
    </div><!-- /col-md-8 -->

</div><!-- /row -->


<!-- Modal Resolve -->
<?php if ($canResolve): ?>
    <div class="modal fade" id="resolveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('tickets/resolve/' . $ticket['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle text-success mr-2"></i>Selesaikan Tiket
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <strong><?= esc($ticket['ticket_number']) ?></strong> &mdash;
                            <small class="text-muted"><?= esc(mb_substr($ticket['summary'] ?? '', 0, 80)) ?>...</small>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">
                                Catatan Penyelesaian <span class="text-danger">*</span>
                            </label>
                            <textarea name="catatan_petugas" class="form-control" rows="5"
                                placeholder="Jelaskan tindakan yang sudah dilakukan dan hasil penyelesaiannya..."
                                required></textarea>
                            <small class="text-muted">Wajib diisi. Catatan ini akan ditampilkan di tiket.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i>Selesaikan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>


<script>
    $(function() {
        var TICKET_ID = <?= (int) $ticket['id'] ?>;
        var CATEGORY_ID = <?= (int) ($ticket['service_category_id'] ?? 0) ?>;
        var REPLY_URL = '<?= base_url('tickets/reply/' . $ticket['id']) ?>';
        var TPL_URL = '<?= base_url('tickets/templates') ?>';
        var CSRF_NAME = '<?= csrf_token() ?>';
        var CSRF_HASH = $('[name="<?= csrf_token() ?>"]').val();

        // ---- Chat: scroll ke bawah ----
        var $chat = $('#chat-container');
        $chat.scrollTop($chat[0].scrollHeight);

        // ---- Template: klik item → isi textarea ----
        $(document).on('click', '.template-item', function() {
            var konten = $(this).data('konten');
            $('#reply-input').val(konten).focus();
            // Pindah ke tab percakapan (kanan) sudah ada, tapi kita arahkan user ke textarea
            // Tidak perlu pindah tab karena textarea ada di kanan
        });

        // ---- Template: search AJAX ----
        function loadTemplates(keyword) {
            var params = {
                q: keyword,
                category_id: CATEGORY_ID
            };
            $('#template-list').html(
                '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fa-lg"></i></div>'
            );
            $.get(TPL_URL, params, function(res) {
                if (!res.success || res.templates.length === 0) {
                    $('#template-list').html(
                        '<div class="text-center py-4 text-muted" id="template-empty">' +
                        '<i class="fas fa-book-open fa-2x d-block mb-2"></i>Tidak ada template ditemukan.</div>'
                    );
                    return;
                }
                var html = '';
                $.each(res.templates, function(i, tpl) {
                    var preview = tpl.konten.replace(/<[^>]+>/g, '').substring(0, 80);
                    var konten = tpl.konten.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    html += '<div class="template-item border-bottom px-3 py-2 d-flex align-items-start" ' +
                        'style="cursor:pointer;transition:background .12s;" ' +
                        'data-konten="' + tpl.konten.replace(/"/g, '&quot;') + '" ' +
                        'onmouseover="this.style.background=\'#EBF4FA\'" ' +
                        'onmouseout="this.style.background=\'#fff\'">' +
                        '<div class="mr-2 mt-1 flex-shrink-0">' +
                        '<span class="badge badge-light border text-muted" style="font-size:0.68rem;min-width:22px;text-align:center;">' + (i + 1) + '</span>' +
                        '</div>' +
                        '<div style="flex:1;min-width:0;">' +
                        '<div class="font-weight-bold text-dark" style="font-size:0.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(tpl.judul) + '</div>' +
                        '<div class="text-muted" style="font-size:0.75rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(preview) + '...</div>' +
                        '</div>' +
                        '<div class="ml-2 flex-shrink-0 text-primary" style="font-size:0.8rem;"><i class="fas fa-arrow-right"></i></div>' +
                        '</div>';
                });
                $('#template-list').html(html);
                $('#template-hint').html(
                    '<i class="fas fa-search mr-1 text-primary"></i>' +
                    'Menampilkan <strong>' + res.templates.length + '</strong> template' +
                    (keyword ? ' untuk: "<strong>' + escHtml(keyword) + '</strong>"' : '')
                );
            }).fail(function() {
                $('#template-list').html(
                    '<div class="text-center py-4 text-danger">Gagal memuat template.</div>'
                );
            });
        }

        $('#btn-template-search').on('click', function() {
            loadTemplates($.trim($('#template-search').val()));
        });

        $('#template-search').on('keydown', function(e) {
            if (e.key === 'Enter') {
                loadTemplates($.trim($(this).val()));
            }
        });

        $('#btn-template-reset').on('click', function() {
            $('#template-search').val('');
            loadTemplates('');
            $('#template-hint').html(
                '<i class="fas fa-tag mr-1 text-primary"></i>' +
                'Menampilkan template untuk: <strong><?= esc($ticket['category_name'] ?? 'semua kategori') ?></strong>'
            );
        });

        // ---- Reply AJAX ----
        $('#btn-send-reply').on('click', sendReply);

        $('#reply-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendReply();
            }
        });

        function sendReply() {
            var msg = $.trim($('#reply-input').val());
            if (!msg) {
                $('#reply-error').text('Pesan tidak boleh kosong.').show();
                return;
            }
            $('#reply-error').hide();

            var $btn = $('#btn-send-reply').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin d-block mb-1"></i>Kirim');
            $('#reply-status').text('Mengirim...');

            var data = {
                message: msg
            };
            data[CSRF_NAME] = CSRF_HASH;

            $.post(REPLY_URL, data)
                .done(function(res) {
                    if (res.success) {
                        CSRF_HASH = res.csrf || CSRF_HASH;
                        $('[name="' + CSRF_NAME + '"]').val(CSRF_HASH);

                        $chat.append(buildBubble(res.chat, res.staff_name));
                        $chat.scrollTop($chat[0].scrollHeight);
                        $('#reply-input').val('');

                        var cnt = parseInt($('#chat-count').text()) + 1;
                        $('#chat-count').text(cnt + ' pesan');
                        $('#reply-status').text('Terkirim ✓').css('color', '#198754');
                        setTimeout(function() {
                            $('#reply-status').text('');
                        }, 3000);
                    } else {
                        $('#reply-error').text(res.message || 'Gagal mengirim.').show();
                    }
                })
                .fail(function(xhr) {
                    var errMsg = 'Gagal mengirim pesan.';
                    try {
                        errMsg = JSON.parse(xhr.responseText).message || errMsg;
                    } catch (e) {}
                    $('#reply-error').text(errMsg).show();
                })
                .always(function() {
                    $btn.prop('disabled', false)
                        .html('<i class="fas fa-paper-plane d-block mb-1"></i>Kirim');
                });
        }

        function buildBubble(chat, staffName) {
            var time = formatTime(chat.created_at);
            return '<div class="d-flex justify-content-end mb-3">' +
                '<div style="max-width:75%;">' +
                '<div class="chat-bubble staff shadow-sm">' +
                escHtml(chat.message).replace(/\n/g, '<br>') +
                '</div>' +
                '<div class="chat-time text-right mt-1">' +
                '<i class="fas fa-user-tie mr-1 text-success"></i>' +
                escHtml(staffName) + ' &middot; ' + time +
                '</div>' +
                '</div>' +
                '<div class="ml-2 mt-1">' +
                '<div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;flex-shrink:0;">' +
                '<i class="fas fa-user-tie" style="font-size:0.8rem;"></i>' +
                '</div>' +
                '</div>' +
                '</div>';
        }

        function escHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function formatTime(dateStr) {
            var d = new Date(dateStr.replace(' ', 'T'));
            return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
        }
    });
</script>