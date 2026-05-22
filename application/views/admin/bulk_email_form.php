<!-- application/views/admin/bulk_email_form.php -->

<div class="container">
    <h2>Send Bulk Email</h2>

    <div id="bulk-email-status" style="display:none; margin-bottom: 15px;"></div>

    <div id="bulk-email-progress" style="display:none; margin-bottom: 15px;">
        <div style="background:#e0e0e0; border-radius:4px; height:20px; overflow:hidden;">
            <div id="bulk-email-bar" style="background:#0ea5e9; height:20px; width:0%; border-radius:4px; transition:width 0.3s;"></div>
        </div>
        <p id="bulk-email-count" style="margin-top:5px; font-size:0.9em;"></p>
    </div>

    <form id="bulk-email-form">
        <div style="margin-bottom: 10px;">
            <label for="subject">Subject:</label><br>
            <input type="text" id="subject" name="subject" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" rows="10" required style="width: 100%; padding: 8px;"></textarea>
        </div>

        <button type="submit" id="send-btn" style="padding: 10px 20px;">Send Email</button>
    </form>

    <script>
    (function () {
        var CHUNK    = 20;
        var countUrl = '<?= base_url('admin/bulk_email_controller/get_users_count') ?>';
        var chunkUrl = '<?= base_url('admin/bulk_email_controller/send_bulk_email_chunk') ?>';

        $('#bulk-email-form').on('submit', function (e) {
            e.preventDefault();

            var subject = $('#subject').val().trim();
            var message = $('#message').val().trim();
            if (!subject || !message) { return; }

            var $btn    = $('#send-btn');
            var $status = $('#bulk-email-status');
            var $prog   = $('#bulk-email-progress');
            var $bar    = $('#bulk-email-bar');
            var $count  = $('#bulk-email-count');

            $btn.prop('disabled', true).text('Sending…');
            $status.hide().html('');
            $prog.show();
            $bar.css('width', '0%');
            $count.text('Preparing…');

            $.ajax({
                type: 'POST',
                url: countUrl,
                dataType: 'json',
                success: function (res) {
                    var total       = res.total || 0;
                    var offset      = 0;
                    var totalSent   = 0;
                    var totalFailed = 0;

                    if (total === 0) {
                        $status.show().html('<span style="color:orange;">No users found.</span>');
                        $btn.prop('disabled', false).text('Send Email');
                        $prog.hide();
                        return;
                    }

                    function sendChunk() {
                        $.ajax({
                            type: 'POST',
                            url: chunkUrl,
                            dataType: 'json',
                            data: { subject: subject, message: message, offset: offset, limit: CHUNK },
                            success: function (result) {
                                if (result.error) {
                                    $status.show().html('<span style="color:red;">' + result.error + '</span>');
                                    $btn.prop('disabled', false).text('Send Email');
                                    return;
                                }
                                totalSent   += result.sent   || 0;
                                totalFailed += result.failed || 0;
                                offset += CHUNK;

                                var pct = Math.min(100, Math.round(offset / total * 100));
                                $bar.css('width', pct + '%');
                                $count.text(Math.min(offset, total) + ' / ' + total + ' processed');

                                if (result.done || offset >= total) {
                                    finish();
                                } else {
                                    sendChunk();
                                }
                            },
                            error: function () {
                                $status.show().html('<span style="color:red;">An error occurred. ' + totalSent + ' email(s) sent so far.</span>');
                                $btn.prop('disabled', false).text('Send Email');
                            }
                        });
                    }

                    function finish() {
                        $bar.css('width', '100%');
                        if (totalFailed > 0) {
                            $status.show().html('<span style="color:orange;">Bulk email completed with ' + totalFailed + ' failure(s). ' + totalSent + ' sent successfully.</span>');
                        } else {
                            $status.show().html('<span style="color:green;">Bulk email sent successfully to ' + totalSent + ' users.</span>');
                        }
                        $btn.prop('disabled', false).text('Send Email');
                    }

                    sendChunk();
                },
                error: function () {
                    $status.show().html('<span style="color:red;">Failed to retrieve user count. Please try again.</span>');
                    $btn.prop('disabled', false).text('Send Email');
                    $prog.hide();
                }
            });
        });
    }());
    </script>
</div>
