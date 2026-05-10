<?php
// Pre-select first customer from URL param
$openId = (int) (service('request')->getGet('open') ?? 0);
?>

<style>
/* ── WhatsApp-like shell ── */
#conv-shell {
    display: flex;
    height: calc(100vh - 158px);
    min-height: 520px;
    border: 1px solid #dee2e6;
    border-radius: .375rem;
    overflow: hidden;
    background: #fff;
}

/* Left panel */
#conv-left {
    width: 320px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #dee2e6;
}
#conv-left-header {
    padding: 10px 12px;
    background: #0f2235;
    color: #fff;
    font-weight: 600;
    font-size: .9rem;
    flex-shrink: 0;
}
#conv-search-wrap {
    padding: 8px 10px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    flex-shrink: 0;
}
#conv-list {
    flex: 1;
    overflow-y: auto;
}

/* Customer item */
.conv-item {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background .12s;
    gap: 10px;
}
.conv-item:hover  { background: #f0f7ff; }
.conv-item.active { background: #e1f0ff; border-left: 3px solid #1e6f9f; }
.conv-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1rem; color: #fff;
    flex-shrink: 0;
}
.conv-meta { flex: 1; min-width: 0; }
.conv-name {
    font-size: .875rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.conv-preview {
    font-size: .75rem; color: #6c757d;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.conv-time { font-size: .68rem; color: #adb5bd; white-space: nowrap; }
.unread-dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #25d366; flex-shrink: 0;
}

/* Right panel */
#conv-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #efeae2;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c5b9a8' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
#conv-right-empty {
    flex: 1;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #a0a0a0;
}

/* Chat header */
#chat-header {
    background: #fff;
    border-bottom: 1px solid #dee2e6;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

/* Chat bubbles area */
#chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
}

/* Bottom area (tools + reply) */
#chat-bottom { flex-shrink: 0; }

/* Tools panel */
#chat-tools {
    background: #fff;
    border-top: 1px solid #dee2e6;
    display: none;
    max-height: 220px;
}
#chat-tools .nav-tabs { padding: 0 12px; background: #f8f9fa; }
#chat-tools .tab-content { overflow-y: auto; max-height: 170px; }

/* Reply box */
#chat-reply-box {
    background: #f0f0f0;
    border-top: 1px solid #dee2e6;
    padding: 8px 10px;
    display: none;
    align-items: flex-end;
    gap: 8px;
}
#chat-reply-input {
    flex: 1;
    border-radius: 20px;
    border: 1px solid #ccc;
    padding: 8px 14px;
    font-size: .87rem;
    resize: none;
    min-height: 40px;
    max-height: 120px;
}
#chat-reply-input:focus { outline: none; border-color: #1e6f9f; }
#btn-send-msg {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: none;
    background: #1e6f9f;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .15s;
}
#btn-send-msg:hover { background: #155a82; }

/* AI-mode takeover banner */
#ai-takeover-bar {
    background: #fff3cd;
    border-top: 1px solid #ffc107;
    padding: 8px 14px;
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    font-size: .83rem;
}

/* Bubble styles */
.bubble-wrap-customer { display: flex; margin-bottom: 10px; }
.bubble-wrap-right    { display: flex; justify-content: flex-end; margin-bottom: 10px; }
.bubble {
    max-width: 68%;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: .85rem;
    line-height: 1.5;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}
.bubble.customer { background: #fff; border-top-left-radius: 0; }
.bubble.ai       { background: #dcf8c6; border-top-right-radius: 0; }
.bubble.staff    { background: #cce5ff; border-top-right-radius: 0; }
.bubble-time     { font-size: .65rem; color: #aaa; margin-top: 3px; text-align: right; }
.bubble-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; color: #fff; flex-shrink: 0;
    margin-top: 2px;
}
.date-divider {
    text-align: center; margin: 12px 0;
}
.date-divider span {
    background: rgba(255,255,255,.75);
    border-radius: 8px;
    padding: 2px 12px;
    font-size: .72rem;
    color: #777;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}

/* Template items */
.tpl-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    font-size: .82rem;
    transition: background .1s;
}
.tpl-item:hover { background: #f0f7ff; }
.tpl-item .tpl-title { font-weight: 600; color: #222; }
.tpl-item .tpl-preview { color: #888; font-size: .75rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Mobile: hide left panel when chat open */
@media (max-width: 767px) {
    #conv-left  { width: 100%; }
    #conv-right { display: none; }
    #conv-shell.chat-open #conv-left  { display: none; }
    #conv-shell.chat-open #conv-right { display: flex; }
}
</style>


<div id="conv-shell">

    <!-- ============================================================
         LEFT PANEL — Customer list
    ============================================================ -->
    <div id="conv-left">
        <div id="conv-left-header">
            <i class="fab fa-whatsapp mr-2" style="color:#25d366;"></i>Percakapan
        </div>
        <div id="conv-search-wrap">
            <div class="input-group input-group-sm">
                <input type="text" id="conv-search" class="form-control rounded-pill"
                       placeholder="Cari nama atau nomor..."
                       value="<?= esc($search) ?>"
                       style="border-radius:20px!important;">
            </div>
        </div>
        <div id="conv-list">
            <?php if (empty($customers)): ?>
            <div class="text-center text-muted py-5" style="font-size:.85rem;">
                <i class="fas fa-comments fa-2x d-block mb-2"></i>
                Belum ada percakapan.
            </div>
            <?php else: ?>
            <?php foreach ($customers as $c): ?>
            <?php
            $initials  = strtoupper(mb_substr($c['name'], 0, 1));
            $bgColors  = ['#1e6f9f','#2ecc71','#e74c3c','#9b59b6','#f39c12','#16a085','#c0392b','#2980b9'];
            $bg        = $bgColors[crc32($c['name']) % count($bgColors)];
            $isAiOn    = (int) $c['ai_mode'] === 1;
            $unread    = (int) ($c['unread_count'] ?? 0);
            $lastMsg   = $c['last_message'] ?? '';
            $lastTime  = '';
            if (! empty($c['last_message_at'])) {
                $ts      = strtotime($c['last_message_at']);
                $today   = date('Y-m-d');
                $lastDay = date('Y-m-d', $ts);
                $lastTime = ($lastDay === $today) ? date('H:i', $ts) : date('d/m', $ts);
            }
            ?>
            <div class="conv-item <?= $c['id'] === $openId ? 'active' : '' ?>"
                 data-id="<?= $c['id'] ?>"
                 data-name="<?= esc($c['name'], 'attr') ?>">
                <div class="conv-avatar" style="background:<?= $bg ?>;"><?= $initials ?></div>
                <div class="conv-meta">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="conv-name"><?= esc($c['name']) ?></span>
                        <span class="conv-time"><?= $lastTime ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-1" style="gap:4px;">
                        <span class="conv-preview" style="flex:1;">
                            <?php if ($c['last_sender'] === 'staff'): ?>
                                <i class="fas fa-reply" style="font-size:.65rem;color:#1e6f9f;"></i>
                            <?php elseif ($c['last_sender'] === 'ai'): ?>
                                <i class="fas fa-robot" style="font-size:.65rem;color:#28a745;"></i>
                            <?php endif; ?>
                            <?= esc(mb_substr($lastMsg, 0, 38)) ?>
                        </span>
                        <div class="d-flex align-items-center" style="gap:3px;">
                            <?php if ($isAiOn): ?>
                            <span style="font-size:.6rem;background:#28a745;color:#fff;border-radius:3px;padding:1px 4px;">
                                <i class="fas fa-robot" style="font-size:.55rem;"></i> AI
                            </span>
                            <?php else: ?>
                            <span style="font-size:.6rem;background:#6c757d;color:#fff;border-radius:3px;padding:1px 4px;">
                                <i class="fas fa-user-tie" style="font-size:.55rem;"></i> Staff
                            </span>
                            <?php endif; ?>
                            <?php if ($unread > 0): ?>
                            <div class="unread-dot"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div><!-- /conv-left -->


    <!-- ============================================================
         RIGHT PANEL — Chat area (empty state default)
    ============================================================ -->
    <div id="conv-right">
        <div id="conv-right-empty">
            <div class="text-center">
                <i class="fab fa-whatsapp" style="font-size:4rem;color:#25d366;opacity:.4;"></i>
                <p class="mt-3" style="font-size:1rem;">Pilih percakapan untuk memulai</p>
                <small style="font-size:.8rem;">Klik nama customer di sebelah kiri</small>
            </div>
        </div>
    </div><!-- /conv-right -->

</div><!-- /conv-shell -->


<script>
$(function () {
    var CSRF_NAME   = '<?= csrf_token() ?>';
    var CSRF_HASH   = $('[name="<?= csrf_token() ?>"]').val();
    var BASE_URL    = '<?= base_url() ?>';
    var currentId   = 0;
    var lastMsgId   = 0;
    var pollTimer   = null;
    var currentAiMode = 1;

    function csrfData(extra) {
        var d = extra || {};
        d[CSRF_NAME] = CSRF_HASH;
        return d;
    }
    function updateCsrf(res) {
        if (res && res.csrf) {
            CSRF_HASH = res.csrf;
            $('[name="' + CSRF_NAME + '"]').val(CSRF_HASH);
        }
    }

    // ================================================================
    // Load chat panel on customer click
    // ================================================================
    $(document).on('click', '.conv-item', function () {
        var id = $(this).data('id');
        openChat(id, $(this));
    });

    function openChat(id, $item) {
        if (currentId === id) return;
        currentId = id;
        lastMsgId = 0;
        currentAiMode = 1;

        $('.conv-item').removeClass('active');
        if ($item) $item.addClass('active');
        else $('.conv-item[data-id="' + id + '"]').addClass('active');

        $('#conv-shell').addClass('chat-open');

        $('#conv-right').html(
            '<div style="flex:1;display:flex;align-items:center;justify-content:center;">' +
            '<i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>'
        );

        $.get(BASE_URL + 'conversations/chat/' + id, function (html) {
            $('#conv-right').html(html);
            afterChatLoaded();
        }).fail(function () {
            $('#conv-right').html('<div class="p-4 text-danger">Gagal memuat percakapan.</div>');
        });
    }

    function afterChatLoaded() {
        scrollChatBottom();

        // Read ai_mode from rendered panel
        currentAiMode = parseInt($('#conv-right').find('#ai-mode-val').val() || '1');
        applyAiMode(currentAiMode, false);

        // Collect last message id
        var $lastMsg = $('#chat-messages .msg-bubble').last();
        if ($lastMsg.length) {
            lastMsgId = parseInt($lastMsg.data('id') || '0');
        }

        startPoll();
    }

    // ================================================================
    // AI Mode UI
    // ================================================================
    function applyAiMode(mode, animate) {
        currentAiMode = mode;

        // Update badge in header
        var $badge = $('#ai-mode-badge');
        if (mode === 1) {
            $badge.html('<span class="badge badge-success"><i class="fas fa-robot mr-1"></i>AI Aktif</span>');
            $('#ai-takeover-bar').css('display', 'flex');
            $('#chat-reply-box').hide();
            $('#chat-tools').hide();
        } else {
            $badge.html('<span class="badge badge-secondary"><i class="fas fa-user-tie mr-1"></i>Mode Staff</span>');
            $('#ai-takeover-bar').hide();
            $('#chat-reply-box').css('display', 'flex');
            $('#chat-tools').show();
            if (animate) {
                $('#chat-reply-input').focus();
                scrollChatBottom();
            }
        }

        // Update conv-item badge in list
        var $item = $('.conv-item[data-id="' + currentId + '"]');
        if (mode === 1) {
            $item.find('[data-role-badge]').html('<span style="font-size:.6rem;background:#28a745;color:#fff;border-radius:3px;padding:1px 4px;"><i class="fas fa-robot" style="font-size:.55rem;"></i> AI</span>');
        } else {
            $item.find('[data-role-badge]').html('<span style="font-size:.6rem;background:#6c757d;color:#fff;border-radius:3px;padding:1px 4px;"><i class="fas fa-user-tie" style="font-size:.55rem;"></i> Staff</span>');
        }
    }

    // ================================================================
    // Takeover / Release
    // ================================================================
    $(document).on('click', '#btn-takeover', function () {
        var $btn = $(this).prop('disabled', true);
        $.post(BASE_URL + 'conversations/takeover/' + currentId, csrfData())
        .done(function (res) {
            updateCsrf(res);
            if (res.success) applyAiMode(0, true);
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    $(document).on('click', '#btn-release', function () {
        if (! confirm('Kembalikan percakapan ini ke AI?')) return;
        var $btn = $(this).prop('disabled', true);
        $.post(BASE_URL + 'conversations/release/' + currentId, csrfData())
        .done(function (res) {
            updateCsrf(res);
            if (res.success) applyAiMode(1, false);
        })
        .always(function () { $btn.prop('disabled', false); });
    });

    // ================================================================
    // Send reply
    // ================================================================
    $(document).on('click', '#btn-send-msg', sendMessage);
    $(document).on('keydown', '#chat-reply-input', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    function sendMessage() {
        var msg = $.trim($('#chat-reply-input').val());
        if (! msg || ! currentId) return;

        $('#chat-reply-input').val('').prop('disabled', true);
        $('#btn-send-msg').prop('disabled', true);

        $.post(BASE_URL + 'conversations/reply/' + currentId, csrfData({ message: msg }))
        .done(function (res) {
            updateCsrf(res);
            if (res.success) {
                appendBubble(res.chat, res.staff_name);
                lastMsgId = Math.max(lastMsgId, parseInt(res.chat.id || '0'));
                updateListPreview(currentId, msg, 'staff');
            }
        })
        .always(function () {
            $('#chat-reply-input').prop('disabled', false).focus();
            $('#btn-send-msg').prop('disabled', false);
        });
    }

    // ================================================================
    // Append a bubble to chat
    // ================================================================
    function appendBubble(chat, staffName) {
        var sender = chat.sender;
        var time   = formatTime(chat.created_at);
        var msg    = escHtml(chat.message).replace(/\n/g, '<br>');
        var html   = '';

        if (sender === 'customer') {
            html = '<div class="bubble-wrap-customer msg-bubble" data-id="' + chat.id + '">' +
                '<div class="bubble-avatar mr-2" style="background:#6c757d;"><i class="fas fa-user" style="font-size:.65rem;"></i></div>' +
                '<div><div class="bubble customer">' + msg + '</div>' +
                '<div class="bubble-time">' + time + '</div></div></div>';
        } else if (sender === 'ai') {
            html = '<div class="bubble-wrap-right msg-bubble" data-id="' + chat.id + '">' +
                '<div><div class="bubble ai">' + msg + '</div>' +
                '<div class="bubble-time"><i class="fas fa-robot mr-1" style="color:#28a745;font-size:.65rem;"></i>GAVI AI · ' + time + '</div></div>' +
                '<div class="bubble-avatar ml-2" style="background:#28a745;"><i class="fas fa-robot" style="font-size:.65rem;"></i></div></div>';
        } else {
            var name = escHtml(staffName || chat.staff_name || 'Staff');
            html = '<div class="bubble-wrap-right msg-bubble" data-id="' + chat.id + '">' +
                '<div><div class="bubble staff">' + msg + '</div>' +
                '<div class="bubble-time"><i class="fas fa-user-tie mr-1" style="color:#1e6f9f;font-size:.65rem;"></i>' + name + ' · ' + time + '</div></div>' +
                '<div class="bubble-avatar ml-2" style="background:#1e6f9f;"><i class="fas fa-user-tie" style="font-size:.65rem;"></i></div></div>';
        }

        $('#chat-messages').append(html);
        scrollChatBottom();
    }

    // ================================================================
    // Polling
    // ================================================================
    function startPoll() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(doPoll, 5000);
    }

    function doPoll() {
        if (! currentId) return;
        $.get(BASE_URL + 'conversations/poll/' + currentId + '?after_id=' + lastMsgId)
        .done(function (res) {
            updateCsrf(res);
            if (! res.success) return;

            // If ai_mode changed externally, update UI
            if (res.ai_mode !== currentAiMode) {
                applyAiMode(res.ai_mode, false);
            }

            if (res.messages && res.messages.length) {
                $.each(res.messages, function (_, chat) {
                    appendBubble(chat, chat.staff_name);
                    lastMsgId = Math.max(lastMsgId, parseInt(chat.id || '0'));
                });
                updateListPreview(currentId,
                    res.messages[res.messages.length - 1].message,
                    res.messages[res.messages.length - 1].sender);
            }
        });
    }

    // ================================================================
    // Templates
    // ================================================================
    $(document).on('click', '#btn-tpl-search', function () {
        loadTemplates($.trim($('#tpl-search-input').val()));
    });
    $(document).on('keydown', '#tpl-search-input', function (e) {
        if (e.key === 'Enter') loadTemplates($.trim($(this).val()));
    });

    function loadTemplates(kw) {
        $('#tpl-list').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div>');
        $.get(BASE_URL + 'conversations/templates?q=' + encodeURIComponent(kw), function (res) {
            if (! res.success || ! res.templates.length) {
                $('#tpl-list').html('<div class="text-center text-muted py-3" style="font-size:.8rem;">Tidak ada template ditemukan.</div>');
                return;
            }
            var html = '';
            $.each(res.templates, function (_, tpl) {
                html += '<div class="tpl-item" data-konten="' + escAttr(tpl.konten) + '">' +
                    '<div class="tpl-title">' + escHtml(tpl.judul) + '</div>' +
                    '<div class="tpl-preview">' + escHtml(tpl.konten.replace(/<[^>]+>/g,'').substring(0, 60)) + '...</div>' +
                    '</div>';
            });
            $('#tpl-list').html(html);
        });
    }

    $(document).on('click', '.tpl-item', function () {
        $('#chat-reply-input').val($(this).data('konten')).focus();
    });

    // ================================================================
    // Search customers
    // ================================================================
    var searchTimer;
    $('#conv-search').on('input', function () {
        clearTimeout(searchTimer);
        var q = $(this).val();
        searchTimer = setTimeout(function () {
            window.location.href = BASE_URL + 'conversations?search=' + encodeURIComponent(q);
        }, 500);
    });

    // ================================================================
    // Helpers
    // ================================================================
    function scrollChatBottom() {
        var el = document.getElementById('chat-messages');
        if (el) el.scrollTop = el.scrollHeight;
    }

    function updateListPreview(id, msg, sender) {
        var $item = $('.conv-item[data-id="' + id + '"]');
        var icon  = sender === 'staff'
            ? '<i class="fas fa-reply" style="font-size:.65rem;color:#1e6f9f;"></i> '
            : (sender === 'ai' ? '<i class="fas fa-robot" style="font-size:.65rem;color:#28a745;"></i> ' : '');
        $item.find('.conv-preview').html(icon + escHtml(msg).substring(0, 38));
        $item.find('.conv-time').text(formatNow());
        // Move to top
        $('#conv-list').prepend($item);
    }

    function formatTime(dateStr) {
        var d = new Date(dateStr.replace(' ', 'T'));
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }
    function formatNow() {
        var d = new Date();
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }
    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(str) {
        return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // ================================================================
    // Auto-open if ?open= param
    // ================================================================
    <?php if ($openId): ?>
    openChat(<?= $openId ?>, null);
    <?php endif; ?>
});
</script>
