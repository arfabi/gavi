<?php
// Injected vars: $customer, $chats, $templates, $staffId, $isAdmin
$aiMode    = (int) ($customer['ai_mode'] ?? 1);
$isAiOn    = $aiMode === 1;
$initials  = strtoupper(mb_substr($customer['name'], 0, 1));
$bgColors  = ['#1e6f9f','#2ecc71','#e74c3c','#9b59b6','#f39c12','#16a085','#c0392b','#2980b9'];
$bg        = $bgColors[crc32($customer['name']) % count($bgColors)];
$lastMsgId = ! empty($chats) ? (int) end($chats)['id'] : 0;
?>

<!-- Hidden field for JS to read ai_mode -->
<input type="hidden" id="ai-mode-val" value="<?= $aiMode ?>">

<!-- ================================================================
     CHAT HEADER
================================================================ -->
<div id="chat-header">
    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
         style="width:40px;height:40px;background:<?= $bg ?>;font-weight:700;font-size:1rem;">
        <?= $initials ?>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="font-weight-bold" style="font-size:.92rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= esc($customer['name']) ?>
        </div>
        <div style="font-size:.75rem;color:#6c757d;">
            <i class="fab fa-whatsapp text-success mr-1"></i><?= esc($customer['whatsapp_number'] ?? '—') ?>
            <?php if (! empty($customer['instansi'])): ?>
            &bull; <?= esc($customer['instansi']) ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="ai-mode-badge" class="flex-shrink-0">
        <?php if ($isAiOn): ?>
        <span class="badge badge-success"><i class="fas fa-robot mr-1"></i>AI Aktif</span>
        <?php else: ?>
        <span class="badge badge-secondary"><i class="fas fa-user-tie mr-1"></i>Mode Staff</span>
        <?php endif; ?>
    </div>
    <a href="<?= base_url('customers/detail/' . $customer['id']) ?>"
       class="btn btn-xs btn-outline-secondary flex-shrink-0" target="_blank" title="Lihat Profil Customer">
        <i class="fas fa-user"></i>
    </a>
</div>


<!-- ================================================================
     CHAT MESSAGES
================================================================ -->
<div id="chat-messages">
    <?php if (empty($chats)): ?>
    <div class="text-center text-muted py-5" style="font-size:.85rem;">
        <i class="fas fa-comment-slash fa-2x d-block mb-2"></i>
        Belum ada percakapan.
    </div>
    <?php else: ?>
    <?php $lastDate = ''; ?>
    <?php foreach ($chats as $chat): ?>
        <?php
        $chatDate = date('d M Y', strtotime($chat['created_at']));
        $chatTime = date('H:i', strtotime($chat['created_at']));
        $today    = date('d M Y');
        $dispDate = $chatDate === $today ? 'Hari ini' : $chatDate;
        ?>

        <?php if ($chatDate !== $lastDate): ?>
        <div class="date-divider"><span><?= $dispDate ?></span></div>
        <?php $lastDate = $chatDate; ?>
        <?php endif; ?>

        <?php if ($chat['sender'] === 'customer'): ?>
        <div class="bubble-wrap-customer msg-bubble" data-id="<?= $chat['id'] ?>">
            <div class="bubble-avatar mr-2" style="background:#6c757d;">
                <i class="fas fa-user" style="font-size:.65rem;"></i>
            </div>
            <div>
                <div class="bubble customer"><?= nl2br(esc($chat['message'])) ?></div>
                <div class="bubble-time"><?= $chatTime ?></div>
            </div>
        </div>

        <?php elseif ($chat['sender'] === 'ai'): ?>
        <div class="bubble-wrap-right msg-bubble" data-id="<?= $chat['id'] ?>">
            <div>
                <div class="bubble ai">
                    <?= nl2br(esc($chat['message'])) ?>
                    <?php if (! empty($chat['confidence_score'])): ?>
                    <div style="border-top:1px solid rgba(0,0,0,.08);margin-top:5px;padding-top:4px;font-size:.68rem;color:#666;">
                        <i class="fas fa-brain mr-1 text-info"></i>
                        <?= number_format($chat['confidence_score'], 1) ?>%
                        <?php if (! empty($chat['intent'])): ?>
                        &bull; <code style="font-size:.66rem;"><?= esc($chat['intent']) ?></code>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="bubble-time">
                    <i class="fas fa-robot mr-1" style="color:#28a745;font-size:.65rem;"></i>GAVI AI · <?= $chatTime ?>
                </div>
            </div>
            <div class="bubble-avatar ml-2" style="background:#28a745;">
                <i class="fas fa-robot" style="font-size:.65rem;"></i>
            </div>
        </div>

        <?php else: /* staff */ ?>
        <div class="bubble-wrap-right msg-bubble" data-id="<?= $chat['id'] ?>">
            <div>
                <div class="bubble staff"><?= nl2br(esc($chat['message'])) ?></div>
                <div class="bubble-time">
                    <i class="fas fa-user-tie mr-1" style="color:#1e6f9f;font-size:.65rem;"></i>
                    <?= esc($chat['staff_name'] ?? 'Staff') ?> · <?= $chatTime ?>
                </div>
            </div>
            <div class="bubble-avatar ml-2" style="background:#1e6f9f;">
                <i class="fas fa-user-tie" style="font-size:.65rem;"></i>
            </div>
        </div>
        <?php endif; ?>

    <?php endforeach; ?>
    <?php endif; ?>
</div>


<!-- ================================================================
     BOTTOM AREA
================================================================ -->
<div id="chat-bottom">

    <!-- AI Takeover Banner (shown when AI is active) -->
    <div id="ai-takeover-bar" style="<?= $isAiOn ? 'display:flex;' : 'display:none;' ?>">
        <div style="flex:1;">
            <i class="fas fa-robot mr-2 text-warning"></i>
            <strong>AI sedang menangani percakapan ini.</strong>
            <span class="text-muted ml-1" style="font-size:.8rem;">Klik "Ambil Alih" untuk membalas langsung.</span>
        </div>
        <button type="button" id="btn-takeover"
                class="btn btn-warning btn-sm flex-shrink-0">
            <i class="fas fa-hand-paper mr-1"></i>Ambil Alih
        </button>
    </div>

    <!-- Tools Panel (Templates + Customer Info) — shown when NOT AI mode -->
    <div id="chat-tools" style="<?= ! $isAiOn ? 'display:block;' : 'display:none;' ?>">
        <ul class="nav nav-tabs nav-sm border-0 bg-light px-2 pt-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active py-1 px-2" style="font-size:.78rem;" data-toggle="tab" href="#tool-templates" role="tab">
                    <i class="fas fa-book mr-1"></i>Template
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2" style="font-size:.78rem;" data-toggle="tab" href="#tool-customer" role="tab">
                    <i class="fas fa-user mr-1"></i>Info Customer
                </a>
            </li>
            <li class="nav-item ml-auto">
                <button type="button" id="btn-release"
                        class="btn btn-xs btn-outline-success mt-1"
                        style="font-size:.72rem;">
                    <i class="fas fa-robot mr-1"></i>Kembalikan ke AI
                </button>
            </li>
        </ul>

        <div class="tab-content" style="max-height:170px;overflow-y:auto;">

            <!-- Templates -->
            <div class="tab-pane fade show active" id="tool-templates">
                <div class="d-flex border-bottom px-2 py-1 bg-white" style="gap:4px;">
                    <input type="text" id="tpl-search-input" class="form-control form-control-sm"
                           placeholder="Cari template..." style="font-size:.78rem;">
                    <button type="button" id="btn-tpl-search" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="tpl-list">
                    <?php if (empty($templates)): ?>
                    <div class="text-center text-muted py-3" style="font-size:.78rem;">Tidak ada template.</div>
                    <?php else: ?>
                    <?php foreach ($templates as $tpl): ?>
                    <div class="tpl-item" data-konten="<?= esc($tpl['konten'], 'attr') ?>">
                        <div class="tpl-title"><?= esc($tpl['judul']) ?></div>
                        <div class="tpl-preview"><?= esc(mb_substr(strip_tags($tpl['konten']), 0, 60)) ?>...</div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="tab-pane fade" id="tool-customer">
                <table class="table table-sm table-borderless mb-0 px-1" style="font-size:.78rem;">
                    <tr>
                        <td class="text-muted pl-2" style="width:36%;white-space:nowrap;">Nama</td>
                        <td class="font-weight-bold"><?= esc($customer['name']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-2">WhatsApp</td>
                        <td><?= esc($customer['whatsapp_number'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-2">NIK</td>
                        <td><?= esc($customer['nik'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-2">Instansi</td>
                        <td><?= esc($customer['instansi'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-2">Kota</td>
                        <td><?= esc($customer['address_city'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted pl-2">AI Mode</td>
                        <td>
                            <?= $isAiOn
                                ? '<span class="badge badge-success" style="font-size:.65rem;">Aktif</span>'
                                : '<span class="badge badge-secondary" style="font-size:.65rem;">Nonaktif</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pl-2 pt-0">
                            <a href="<?= base_url('customers/detail/' . $customer['id']) ?>"
                               class="btn btn-xs btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt mr-1"></i>Profil Lengkap
                            </a>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div><!-- /chat-tools -->

    <!-- Reply Box (shown when NOT AI mode) -->
    <div id="chat-reply-box" style="<?= ! $isAiOn ? 'display:flex;' : 'display:none;' ?>">
        <textarea id="chat-reply-input"
                  placeholder="Ketik pesan atau klik template di atas..."></textarea>
        <button id="btn-send-msg" title="Kirim (Enter)">
            <i class="fas fa-paper-plane" style="font-size:.9rem;"></i>
        </button>
    </div>

</div><!-- /chat-bottom -->

<script>
// Store last message id for polling (read by parent JS)
window._lastMsgId_<?= $customer['id'] ?> = <?= $lastMsgId ?>;
$(function () {
    // Update parent lastMsgId after chat partial loaded
    if (typeof window.lastMsgId !== 'undefined') {
        window.lastMsgId = <?= $lastMsgId ?>;
    }
});
</script>
