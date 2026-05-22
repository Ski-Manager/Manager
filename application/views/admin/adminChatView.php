<div class="w-full">
    <legend><?php echo $this->lang->line('admin_page')['chat_title']; ?></legend>

    <div class="w-full container-border padding_top_bot_15">
        <div class="md:col-span-12">

            <?php echo $this->session->flashdata('msg'); ?>

            <!-- Compose form -->
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
                        <div class="col-md-7">
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

            <!-- Messages list -->
            <div class="overflow-x-auto">
                <table id="chatTable" class="table table-zebra rounded-table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('admin_page')['chat_sender']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['chat_recipient']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['chat_message']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['chat_date']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['chat_read']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['chat_reply_to']; ?></th>
                            <th><?php echo $this->lang->line('admin_page')['Actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                        <tr data-id_message="<?php echo (int)$msg->id_message; ?>">
                            <td><?php echo (int)$msg->id_message; ?></td>
                            <td><?php echo htmlspecialchars($msg->sender_username); ?></td>
                            <td><?php echo htmlspecialchars($msg->recipient_username); ?></td>
                            <td><?php echo htmlspecialchars($msg->message); ?></td>
                            <td><?php echo htmlspecialchars($msg->created_at); ?></td>
                            <td><?php echo $msg->is_read ? '<span class="text-success">&#10003;</span>' : '<span class="text-base-content/60">&mdash;</span>'; ?></td>
                            <td><?php echo !empty($msg->reply_to_id) ? '#' . (int)$msg->reply_to_id : '<span class="text-base-content/60">&mdash;</span>'; ?></td>
                            <td>
                                <a class="chat-delete-btn btn btn-error btn-sm"><?php echo $this->lang->line('admin_page')['delete']; ?></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center"><?php echo $pagination; ?></div>
        </div>
    </div>

    <div id="chat-dialog-confirm" style="display:none;">
        <?php echo $this->lang->line('admin_page')['confirm_delete']; ?>
    </div>
</div>

<style>
    .rounded-table { border-radius: 10px; overflow: hidden; }
    .table thead th { background-color: #f8f9fa; border-b: 2px solid #dee2e6; color: #343a40; }
    .table-zebra tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.02); }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.05); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var base_url = '<?php echo base_url(); ?>';

    // Send message via AJAX
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
        xhr.open('POST', base_url + 'admin/admin_chat_controller/send_message', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            var resp = JSON.parse(xhr.responseText);
            if (resp.returned) {
                resultDiv.innerHTML = '<div class="alert alert-success"><?php echo $this->lang->line('admin_page')['chat_sent_success']; ?></div>';
                document.getElementById('chat_recipient').value = '';
                document.getElementById('chat_message').value   = '';
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                resultDiv.innerHTML = '<div class="alert alert-error">' + (resp.error || '<?php echo $this->lang->line('admin_page')['chat_error_send']; ?>') + '</div>';
            }
        };
        xhr.send(params);
    });

    // Delete message via AJAX
    document.querySelectorAll('.chat-delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('<?php echo $this->lang->line('admin_page')['confirm_delete']; ?>')) return;
            var row       = btn.closest('tr');
            var id_message = row.getAttribute('data-id_message');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', base_url + 'admin/admin_chat_controller/delete_message', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                var resp = JSON.parse(xhr.responseText);
                if (resp.returned) {
                    row.remove();
                }
            };
            xhr.send('id_message=' + encodeURIComponent(id_message));
        });
    });
});
</script>
