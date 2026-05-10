<?php
$statusBadge = [
    'open'     => '<span class="badge badge-danger">Terbuka</span>',
    'pending'  => '<span class="badge badge-warning text-dark">Pending</span>',
    'resolved' => '<span class="badge badge-success">Selesai</span>',
    'closed'   => '<span class="badge badge-secondary">Ditutup</span>',
];
$priorityBadge = [
    'high'   => '<span class="badge priority-high">High</span>',
    'medium' => '<span class="badge priority-medium">Medium</span>',
    'low'    => '<span class="badge priority-low">Low</span>',
];
?>

<div class="row">

    <!-- ============================================================
         KIRI: Profil Customer  (md-3)
    ============================================================ -->
    <div class="col-12 col-md-3 mb-3 mb-md-0">

        <!-- Avatar + Nama -->
        <div class="card shadow-sm mb-3 text-center">
            <div class="card-body py-3">
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white mb-2"
                     style="width:64px;height:64px;font-size:1.6rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h6 class="mb-0 font-weight-bold">
                    <?= esc($customer['name'] ?? 'Tidak diketahui') ?>
                </h6>
                <small class="text-muted">
                    <i class="fab fa-whatsapp text-success mr-1"></i>
                    <?= esc($customer['whatsapp_number']) ?>
                </small>
                <div class="mt-2">
                    <?php if ($customer['ai_mode']): ?>
                        <span class="badge badge-success"><i class="fas fa-robot mr-1"></i>AI Mode Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-secondary"><i class="fas fa-robot mr-1"></i>AI Mode Nonaktif</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Data Diri -->
        <div class="card shadow-sm mb-3">
            <div class="card-header py-2">
                <h6 class="card-title mb-0"><i class="fas fa-id-card mr-2"></i>Data Diri</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0" style="font-size:0.83rem;">
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2" style="width:45%; white-space:nowrap;">NIK</td>
                        <td class="py-2"><?= esc($customer['nik'] ?? '—') ?></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2">Instansi</td>
                        <td class="py-2"><small><?= esc($customer['instansi'] ?? '—') ?></small></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2">Kota</td>
                        <td class="py-2"><small><?= esc($customer['address_city'] ?? '—') ?></small></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2">Provinsi</td>
                        <td class="py-2"><small><?= esc($customer['address_province'] ?? '—') ?></small></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2">Alamat</td>
                        <td class="py-2"><small><?= esc($customer['address'] ?? '—') ?></small></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="text-muted pl-3 py-2">Terdaftar</td>
                        <td class="py-2"><small><?= date('d M Y', strtotime($customer['created_at'])) ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-3 py-2">Terakhir Aktif</td>
                        <td class="py-2 text-success"><small><?= date('d M Y, H:i', strtotime($customer['last_interaction'])) ?></small></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <h6 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>Statistik</h6>
            </div>
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted"><i class="fas fa-history mr-1"></i>Total Sesi</small>
                    <span class="badge badge-primary"><?= count($sessions) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted"><i class="fas fa-comments mr-1"></i>Total Chat</small>
                    <span class="badge badge-info"><?= count($chats) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fas fa-ticket-alt mr-1"></i>Total Tiket</small>
                    <span class="badge badge-warning"><?= count($tickets) ?></span>
                </div>
            </div>
        </div>

    </div><!-- /col-md-3 -->


    <!-- ============================================================
         KANAN: Tabs — Sesi, Percakapan, Tiket  (md-9)
    ============================================================ -->
    <div class="col-12 col-md-9">

        <ul class="nav nav-tabs mb-0" id="customerTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-sesi" data-toggle="tab" href="#panel-sesi" role="tab">
                    <i class="fas fa-history mr-1"></i>Sesi
                    <span class="badge badge-secondary ml-1"><?= count($sessions) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-chat" data-toggle="tab" href="#panel-chat" role="tab">
                    <i class="fas fa-comments mr-1"></i>Percakapan
                    <span class="badge badge-secondary ml-1"><?= count($chats) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-tiket" data-toggle="tab" href="#panel-tiket" role="tab">
                    <i class="fas fa-ticket-alt mr-1"></i>Tiket
                    <span class="badge badge-secondary ml-1"><?= count($tickets) ?></span>
                </a>
            </li>
        </ul>

        <div class="tab-content card shadow-sm rounded-0 rounded-bottom border-top-0">

            <!-- ===== Tab Sesi ===== -->
            <div class="tab-pane fade show active p-0" id="panel-sesi" role="tabpanel">
                <?php if (empty($sessions)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-history fa-2x d-block mb-2"></i>Belum ada sesi.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="pl-3">#</th>
                                <th>ID Sesi</th>
                                <th>Kategori</th>
                                <th>Topik</th>
                                <th class="text-center">Chat</th>
                                <th>Status</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($sessions as $i => $ses): ?>
                        <tr>
                            <td class="pl-3 text-muted small"><?= $i + 1 ?></td>
                            <td><small class="text-monospace text-primary">#<?= $ses['id'] ?></small></td>
                            <td>
                                <span class="badge <?= $ses['category'] === '1' ? 'badge-info' : 'badge-warning' ?>">
                                    <?= $ses['category'] === '1' ? 'Umum' : 'Layanan' ?>
                                </span>
                            </td>
                            <td style="max-width:200px;">
                                <small class="text-muted" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                                    <?= esc($ses['topics'] ?? '—') ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border"><?= (int) $ses['chat_count'] ?></span>
                            </td>
                            <td>
                                <?php if ($ses['status'] === '1'): ?>
                                    <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i>Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="fas fa-circle mr-1" style="font-size:0.6em;"></i>Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= date('d M Y, H:i', strtotime($ses['create_at'])) ?></small></td>
                            <td>
                                <small class="text-muted">
                                    <?= $ses['close_at'] ? date('d M Y, H:i', strtotime($ses['close_at'])) : '—' ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('customers/detail/' . $customer['id'] . '?session_id=' . $ses['id'] . '#tab-chat') ?>"
                                   class="btn btn-xs btn-outline-info btn-sm" title="Lihat Chat Sesi Ini"
                                   onclick="document.getElementById('tab-chat').click()">
                                    <i class="fas fa-comments"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- ===== Tab Percakapan ===== -->
            <div class="tab-pane fade p-0" id="panel-chat" role="tabpanel">

                <!-- Filter sesi -->
                <?php if (! empty($sessions)): ?>
                <div class="px-3 py-2 border-bottom bg-light d-flex align-items-center" style="gap:8px; flex-wrap:wrap;">
                    <small class="text-muted font-weight-bold mr-1">Filter sesi:</small>
                    <a href="<?= base_url('customers/detail/' . $customer['id']) ?>#tab-chat"
                       class="btn btn-xs btn-sm <?= $sessionId === null ? 'btn-primary' : 'btn-outline-secondary' ?>"
                       style="font-size:0.75rem;"
                       onclick="setTimeout(()=>document.getElementById('tab-chat').click(),50)">
                        Semua
                    </a>
                    <?php foreach ($sessions as $ses): ?>
                    <a href="<?= base_url('customers/detail/' . $customer['id'] . '?session_id=' . $ses['id']) ?>#tab-chat"
                       class="btn btn-xs btn-sm <?= $sessionId === (int)$ses['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>"
                       style="font-size:0.75rem;"
                       onclick="setTimeout(()=>document.getElementById('tab-chat').click(),50)">
                        Sesi #<?= $ses['id'] ?>
                        <span class="badge badge-light border ml-1"><?= (int) $ses['chat_count'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Chat bubbles -->
                <div id="chat-container"
                     style="height:480px; overflow-y:auto; background:#f8f9fa; padding:16px;">
                    <?php if (empty($chats)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comment-slash fa-2x d-block mb-2"></i>
                        Belum ada percakapan.
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
                        <!-- Customer: kiri -->
                        <div class="d-flex mb-3">
                            <div class="mr-2 mt-1">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                     style="width:32px;height:32px;flex-shrink:0;">
                                    <i class="fas fa-user" style="font-size:0.8rem;"></i>
                                </div>
                            </div>
                            <div style="max-width:72%;">
                                <div class="chat-bubble customer shadow-sm">
                                    <?= nl2br(esc($chat['message'])) ?>
                                </div>
                                <div class="chat-time mt-1">
                                    <i class="fas fa-user mr-1"></i><?= esc($customer['name'] ?? 'Customer') ?>
                                    &middot; <?= date('H:i', strtotime($chat['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                        <?php elseif ($chat['sender'] === 'ai'): ?>
                        <!-- AI: kanan -->
                        <div class="d-flex justify-content-end mb-3">
                            <div style="max-width:72%;">
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
                        <!-- Staff: kanan -->
                        <div class="d-flex justify-content-end mb-3">
                            <div style="max-width:72%;">
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
                </div>
            </div>

            <!-- ===== Tab Tiket ===== -->
            <div class="tab-pane fade p-0" id="panel-tiket" role="tabpanel">
                <?php if (empty($tickets)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-ticket-alt fa-2x d-block mb-2"></i>Belum ada tiket.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="pl-3">No. Tiket</th>
                                <th>Ringkasan</th>
                                <th>Kategori</th>
                                <th>Petugas</th>
                                <th class="text-center">Prioritas</th>
                                <th class="text-center">Status</th>
                                <th>Dibuat</th>
                                <th class="text-center" style="width:60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="pl-3">
                                <a href="<?= base_url('tickets/detail/' . $t['id']) ?>"
                                   class="font-weight-bold text-primary text-monospace small">
                                    <?= esc($t['ticket_number']) ?>
                                </a>
                            </td>
                            <td style="max-width:220px;">
                                <small class="text-muted" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                                    <?= esc(mb_substr($t['summary'] ?? '', 0, 80)) ?>
                                </small>
                            </td>
                            <td><small><?= esc($t['category_name'] ?? '—') ?></small></td>
                            <td>
                                <small class="text-muted">
                                    <?= esc($t['assigned_name'] ?? '<i>Belum di-assign</i>') ?>
                                </small>
                            </td>
                            <td class="text-center"><?= $priorityBadge[$t['priority']] ?? '—' ?></td>
                            <td class="text-center"><?= $statusBadge[$t['status']] ?? '—' ?></td>
                            <td><small><?= date('d M Y', strtotime($t['created_at'])) ?></small></td>
                            <td class="text-center">
                                <a href="<?= base_url('tickets/detail/' . $t['id']) ?>"
                                   class="btn btn-xs btn-outline-primary btn-sm" title="Lihat Tiket">
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

        </div><!-- /tab-content -->
    </div><!-- /col-md-9 -->

</div><!-- /row -->

<script>
$(function () {
    // Scroll chat ke bawah saat load
    var $chat = $('#chat-container');
    $chat.scrollTop($chat[0].scrollHeight);

    // Jika URL mengandung #tab-chat, aktifkan tab tersebut
    if (window.location.hash === '#tab-chat') {
        $('#customerTabs a[href="#panel-chat"]').tab('show');
        setTimeout(function () {
            $chat.scrollTop($chat[0].scrollHeight);
        }, 150);
    }
    if (window.location.hash === '#tab-tiket') {
        $('#customerTabs a[href="#panel-tiket"]').tab('show');
    }

    // Scroll chat ke bawah setiap tab chat ditampilkan
    $('#tab-chat').on('shown.bs.tab', function () {
        $chat.scrollTop($chat[0].scrollHeight);
    });
});
</script>
