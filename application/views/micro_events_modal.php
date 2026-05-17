<?php
/**
 * micro_events_modal.php
 *
 * Bootstrap modal that presents a pending micro-event (quick decision) to the player.
 * Expected variables:
 *   $micro_event  (object)  – Row from game_resort_micro_events
 *
 * The player's choice is submitted via AJAX to micro_events_controller/respond.
 * After a successful response the modal auto-closes and shows a brief result toast.
 */
$CI      =& get_instance();
$lang_me = $CI->lang->line('micro_events');
$title   = htmlspecialchars($lang_me[$micro_event->event_type . '_title']       ?? $micro_event->event_type, ENT_QUOTES, 'UTF-8');
$desc    = htmlspecialchars($lang_me[$micro_event->event_type . '_description'] ?? '', ENT_QUOTES, 'UTF-8');
$a_label = htmlspecialchars($lang_me[$micro_event->event_type . '_a_label']     ?? 'Choice A', ENT_QUOTES, 'UTF-8');
$a_hint  = htmlspecialchars($lang_me[$micro_event->event_type . '_a_hint']      ?? '', ENT_QUOTES, 'UTF-8');
$b_label = htmlspecialchars($lang_me[$micro_event->event_type . '_b_label']     ?? 'Choice B', ENT_QUOTES, 'UTF-8');
$b_hint  = htmlspecialchars($lang_me[$micro_event->event_type . '_b_hint']      ?? '', ENT_QUOTES, 'UTF-8');
?>
<!-- Micro-event quick-decision modal -->
<dialog id="microEventModal" class="modal modal-middle" aria-labelledby="microEventModalLabel">
    <div class="modal-box">
        <div class="flex items-center gap-2 bg-warning/25 -mx-6 -mt-6 px-6 pt-5 pb-4 mb-4 border-b border-base-300">
            <h2 class="h2 font-bold text-lg flex-1" id="microEventModalLabel">
                <?php echo htmlspecialchars($lang_me['modal_title'] ?? '⚡ Quick Decision', ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <span class="badge badge-neutral font-normal text-sm">
                <i class="fa-regular fa-clock mr-1"></i><?php echo htmlspecialchars($lang_me['expires_in'] ?? 'Expires in ' . MICRO_EVENT_EXPIRY_HOURS . ' h', ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>
        <div id="microEventBody">
            <h6 class="h6 font-semibold mb-2"><?php echo $title; ?></h6>
            <p class="mb-3"><?php echo $desc; ?></p>
            <div class="flex flex-col gap-2" id="microEventChoices">
                <button type="button" class="btn btn-outline btn-primary micro-event-choice"
                        data-choice="a"
                        data-event-id="<?php echo (int)$micro_event->id_micro_event; ?>">
                    <strong><?php echo $a_label; ?></strong>
                    <?php if ($a_hint): ?>
                        <span class="badge badge-primary ml-2 font-normal"><?php echo $a_hint; ?></span>
                    <?php endif; ?>
                </button>
                <button type="button" class="btn btn-outline btn-ghost micro-event-choice"
                        data-choice="b"
                        data-event-id="<?php echo (int)$micro_event->id_micro_event; ?>">
                    <strong><?php echo $b_label; ?></strong>
                    <?php if ($b_hint): ?>
                        <span class="badge badge-ghost ml-2 font-normal"><?php echo $b_hint; ?></span>
                    <?php endif; ?>
                </button>
            </div>
            <div id="microEventResult" class="mt-3" style="display:none;"></div>
            <div id="microEventLoading" class="mt-2 text-base-content/50 text-sm" style="display:none;">
                <span class="loading loading-spinner loading-sm mr-1" role="status" aria-hidden="true"></span>
                <?php echo htmlspecialchars($lang_me['loading'] ?? 'Processing…', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.getElementById('microEventModal');
    // Static backdrop: prevent close via ESC key or backdrop click
    dialog.addEventListener('cancel', function(e) { e.preventDefault(); });
    dialog.addEventListener('click', function(e) { if (e.target === dialog) e.preventDefault(); });
    dialog.showModal();

    document.querySelectorAll('.micro-event-choice').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var choice  = btn.getAttribute('data-choice');
            var eventId = btn.getAttribute('data-event-id');

            // Disable both buttons
            document.querySelectorAll('.micro-event-choice').forEach(function (b) { b.disabled = true; });
            document.getElementById('microEventLoading').style.display = '';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo base_url('micro_events_controller/respond'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function () {
                document.getElementById('microEventLoading').style.display = 'none';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.ok) {
                        document.getElementById('microEventChoices').style.display = 'none';
                        var resultDiv = document.getElementById('microEventResult');
                        resultDiv.innerHTML =
                            '<div class="alert alert-success mb-0">' +
                            (res.msg || '') +
                            '</div>';
                        resultDiv.style.display = '';
                        setTimeout(function () { dialog.close(); }, 3000);
                    } else {
                        document.querySelectorAll('.micro-event-choice').forEach(function (b) { b.disabled = false; });
                    }
                } catch (e) {
                    document.querySelectorAll('.micro-event-choice').forEach(function (b) { b.disabled = false; });
                }
            };
            xhr.onerror = function () {
                document.getElementById('microEventLoading').style.display = 'none';
                document.querySelectorAll('.micro-event-choice').forEach(function (b) { b.disabled = false; });
            };
            xhr.send('id_micro_event=' + encodeURIComponent(eventId) + '&choice=' + encodeURIComponent(choice));
        });
    });
});
</script>
