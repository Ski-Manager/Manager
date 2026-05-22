<div class="w-full">
    <legend><?php echo $this->lang->line('admin_page')['chat_inbox_title']; ?></legend>

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="md:col-span-12">

            <?php if (!empty($is_admin)): ?>
            <!-- Send message form (visible to admin players only) -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong><?php echo $this->lang->line('admin_page')['chat_send_message']; ?></strong>
                </div>
                <div class="card-body">
                    <div class="grid gap-2 items-end">
                        <div class="md:col-span-3">
                            <label for="chat_recipient" class="label"><?php echo $this->lang->line('admin_page')['chat_recipient']; ?></label>
                            <input type="text" id="chat_recipient" class="input w-full" maxlength="25" placeholder="<?php echo $this->lang->line('admin_page')['chat_recipient_placeholder']; ?>" />
                        </div>
                        <div class="md:col-span-7">
                            <label for="chat_message" class="label"><?php echo $this->lang->line('admin_page')['chat_message']; ?></label>
                            <textarea id="chat_message" class="textarea w-full" rows="2" maxlength="1000" placeholder="<?php echo $this->lang->line('admin_page')['chat_message_placeholder']; ?>"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <button id="chat_send_btn" class="btn btn-primary w-full"><?php echo $this->lang->line('admin_page')['chat_send']; ?></button>
                        </div>
                    </div>
                    <div id="chat_result" class="mt-2"></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($messages)): ?>
                <div class="alert alert-info text-center">
                    <?php echo $this->lang->line('admin_page')['chat_no_messages']; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table table-zebra" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admin_page')['chat_sender']; ?></th>
                                <th><?php echo $this->lang->line('admin_page')['chat_message']; ?></th>
                                <th><?php echo $this->lang->line('admin_page')['chat_date']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                            <?php $is_incoming = ($msg->recipient_username === $current_username); ?>
                            <tr class="<?php echo $is_incoming ? '' : 'table-secondary'; ?>">
                                <td>
                                    <?php echo htmlspecialchars($msg->sender_username); ?>
                                    <?php if (!$is_incoming): ?>
                                        <span class="badge badge-neutral ml-1"><?php echo $this->lang->line('admin_page')['chat_reply_label']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo nl2br(htmlspecialchars($msg->message ?? '')); ?>
                                    <?php if ($is_incoming): ?>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline chat-reply-toggle" data-id="<?php echo (int)$msg->id_message; ?>">
                                            <?php echo $this->lang->line('admin_page')['chat_reply']; ?>
                                        </button>
                                        <div class="chat-reply-form mt-2" id="reply-form-<?php echo (int)$msg->id_message; ?>" style="display:none;">
                                            <textarea class="textarea w-full chat-reply-text" rows="2" maxlength="1000" placeholder="<?php echo $this->lang->line('admin_page')['chat_reply_placeholder']; ?>"></textarea>
                                            <button class="btn btn-sm btn-primary mt-1 chat-reply-submit" data-id="<?php echo (int)$msg->id_message; ?>">
                                                <?php echo $this->lang->line('admin_page')['chat_send']; ?>
                                            </button>
                                            <div class="chat-reply-result mt-1"></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($msg->created_at); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php if (!empty($is_admin)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var base_url = '<?php echo base_url(); ?>';

    document.getElementById('chat_send_btn').addEventListener('click', function () {
        var recipient = document.getElementById('chat_recipient').value.trim();
        var message   = document.getElementById('chat_message').value.trim();
        var resultDiv = document.getElementById('chat_result');

        if (!recipient || !message) {
            resultDiv.innerHTML = '<div class="alert alert-warning"><?php echo $this->lang->line('admin_page')['chat_error_fields']; ?></div>';
            return;
        }

        var params = 'recipient_username=' + encodeURIComponent(recipient) + '&message=' + encodeURIComponent(message);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', base_url + 'chat_controller/send_message', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.returned) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><?php echo $this->lang->line('admin_page')['chat_sent_success']; ?></div>';
                    document.getElementById('chat_recipient').value = '';
                    document.getElementById('chat_message').value   = '';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-error">' + (resp.error || '<?php echo $this->lang->line('admin_page')['chat_error_send']; ?>') + '</div>';
                }
            } catch (e) {
                resultDiv.innerHTML = '<div class="alert alert-error"><?php echo $this->lang->line('admin_page')['chat_error_send']; ?></div>';
            }
        };
        xhr.send(params);
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var base_url = '<?php echo base_url(); ?>';

    // Toggle reply form
    document.querySelectorAll('.chat-reply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id   = btn.getAttribute('data-id');
            var form = document.getElementById('reply-form-' + id);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Submit reply
    document.querySelectorAll('.chat-reply-submit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id        = btn.getAttribute('data-id');
            var form      = document.getElementById('reply-form-' + id);
            var textarea  = form.querySelector('.chat-reply-text');
            var resultDiv = form.querySelector('.chat-reply-result');
            var message   = textarea.value.trim();

            if (!message) {
                resultDiv.innerHTML = '<div class="alert alert-warning"><?php echo htmlspecialchars($this->lang->line('admin_page')['chat_error_fields'], ENT_QUOTES, 'UTF-8'); ?></div>';
                return;
            }

            btn.disabled = true;
            var params = 'id_message=' + encodeURIComponent(id) + '&message=' + encodeURIComponent(message);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', base_url + 'chat_controller/reply_to_admin', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                btn.disabled = false;
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.returned) {
                        resultDiv.innerHTML = '<div class="alert alert-success"><?php echo htmlspecialchars($this->lang->line('admin_page')['chat_reply_success'], ENT_QUOTES, 'UTF-8'); ?></div>';
                        textarea.value = '';
                        setTimeout(function () { location.reload(); }, 1200); // 1.2 s to let the user read the success message
                    } else {
                        resultDiv.innerHTML = '<div class="alert alert-error">' + (resp.error || '<?php echo htmlspecialchars($this->lang->line('admin_page')['chat_error_send'], ENT_QUOTES, 'UTF-8'); ?>') + '</div>';
                    }
                } catch (e) {
                    resultDiv.innerHTML = '<div class="alert alert-error"><?php echo htmlspecialchars($this->lang->line('admin_page')['chat_error_send'], ENT_QUOTES, 'UTF-8'); ?></div>';
                }
            };
            xhr.send(params);
        });
    });
});
</script>
