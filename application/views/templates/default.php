<?php

$this->load->view('includes/header');

$controller_name = $this->uri->segment(1);
if (isset($this->lang->line('home')[$controller_name.'_title'])) {
    echo '<title>' . htmlspecialchars($this->lang->line('home')[$controller_name.'_title'], ENT_QUOTES, 'UTF-8') . ' | Ski-Manager</title>';
} else {
    echo '<title>Ski-Manager</title>';
}
echo '<link rel="icon" href="'.base_url().'favicon.ico" type="image/x-icon">';
echo '</head>';
echo '<body>';
echo '<a href="#main-content" class="sr-only-focusable">' . ($this->lang->line('home')['skip_to_main'] ?? 'Skip to main content') . '</a>';

echo '<header class="bgimage" role="banner">
    <h1 class="bgimage-title"><span aria-hidden="true">&#9975;</span> Ski-Manager</h1>
    <p class="bgimage-subtitle" aria-hidden="true">Build Your Alpine Empire</p>
</header>';

echo '<div class="sm-navbar"><nav aria-label="' . ($this->lang->line('home')['main_nav'] ?? 'Main navigation') . '"><div class="wrapper">';
    $this->load->view('includes/navbar');
echo '</div></nav></div>';


echo '<div class="clearfix"></div>';

echo '<div class="wrapper">';
    $this->load->view('includes/sidebar');
    echo '<main id="main-content" tabindex="-1">';
    echo '<div id="page-content-wrapper" class="prose max-w-none">';
        $this->load->view($main_content);
    echo '</div>';
    echo '</main>';

    echo '<div class="clearfix"></div>';
echo '</div>';

// ── Beta prompt modal ────────────────────────────────────────────────────────
// Show once to any logged-in user who has not yet accepted the beta T&C.
// Bots (search-engine crawlers, etc.) are silently bypassed.
$_beta_show = false;
if ($this->session->userdata('is_logged_in')) {
    $_beta_token = getenv('MAINTENANCE_BYPASS_TOKEN') ?: 'e4a1b6c3d9f7a2b8c5d1e3f4b6a7c8d9';
    $_beta_accepted = (!empty($_COOKIE['maintenance_bypass'])
        && $_COOKIE['maintenance_bypass'] === $_beta_token);
    if (!$_beta_accepted) {
        // Let known bots through without the modal
        $_beta_is_bot = !empty($_SERVER['HTTP_USER_AGENT']) && (bool) preg_match(
            '/googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|sogou|exabot'
            . '|facebookexternalhit|ia_archiver|google-inspectiontool|googleother|apis-google/i',
            $_SERVER['HTTP_USER_AGENT']
        );
        if (!$_beta_is_bot) {
            $_beta_show = true;
        }
    }
}
if ($_beta_show):
?>
<!-- Beta T&C modal – shown once per browser until accepted -->
<dialog id="betaPromptModal" class="modal modal-middle" aria-labelledby="betaPromptModalLabel">
    <div class="modal-box">
        <h2 class="h2 font-bold text-lg mb-4" id="betaPromptModalLabel">⚠️ Beta Access &ndash; Terms &amp; Conditions</h2>
        <div class="mb-4">
            <p>By accessing the beta version of Ski Manager you agree to the following:</p>
            <ul>
                <li>The beta is a <strong>work-in-progress</strong>. You may encounter bugs, crashes, or incomplete features.</li>
                <li>Game data and progress <strong>may be reset</strong> at any time without notice.</li>
                <li>Please <a href="https://forms.gle/XwEB3MxPSupAEzw97" target="_blank" rel="noopener noreferrer">report any bugs or issues</a> you encounter to help us improve the game.</li>
                <li>Ski Manager is <strong>not responsible</strong> for any lost progress or data during the beta period.</li>
            </ul>
            <p>If you understand and accept these conditions, click <strong>Accept &amp; Enter Beta</strong>.</p>
        </div>
        <div class="modal-action justify-center gap-3 flex-wrap">
            <button type="button" id="betaAcceptBtn" class="btn btn-primary btn-lg">Accept &amp; Enter Beta</button>
            <a href="<?php echo base_url('login_controller/logout'); ?>" class="btn btn-outline btn-secondary btn-lg">Decline</a>
        </div>
    </div>
</dialog>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.getElementById('betaPromptModal');
    // Static backdrop: prevent close via ESC key or backdrop click
    dialog.addEventListener('cancel', function(e) { e.preventDefault(); });
    dialog.addEventListener('click', function(e) { if (e.target === dialog) e.preventDefault(); });
    dialog.showModal();
    document.getElementById('betaAcceptBtn').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo base_url('beta_controller/ajax_confirm'); ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.ok) { dialog.close(); }
                else { btn.disabled = false; }
            } catch (e) { btn.disabled = false; }
        };
        xhr.onerror = function () { btn.disabled = false; };
        xhr.send();
    });
});
</script>
<?php endif; ?>

<?php
// ── Logged-in modal priority ────────────────────────────────────────────────
// Show pending micro-events to logged-in users (after the beta prompt).
if ($this->session->userdata('is_logged_in') && !$_beta_show) {
    $this->load->model('users_model');
    $_me_player_id = $this->users_model->get_user_id();
    if ($_me_player_id) {
        $CI =& get_instance();
        $CI->load->model('micro_events_model');
        $siteLang = $this->session->userdata('site_lang') ?: 'english';
        $this->lang->load('micro_events', $siteLang);
        $_pending_event = $CI->micro_events_model->get_pending_micro_event_DB($_me_player_id);
        if ($_pending_event) {
            $this->load->view('micro_events_modal', ['micro_event' => $_pending_event]);
        }
    }
}
?>

<!-- Mobile / small-tablet warning popup ─────────────────────────────────
     Shown once (per browser) whenever the viewport width is ≤ 768 px.
     The close button is intentionally small and low-contrast so it is
     slightly hard to spot, as requested. -->
<dialog id="mobileWarningModal" aria-labelledby="mobileWarningTitle" aria-modal="true" style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.55);display:none;align-items:center;justify-content:center;border:none;padding:0;max-width:100%;max-height:100%;width:100%;height:100%">
    <div role="document" style="background:var(--fallback-b1,#1d232a);color:var(--fallback-bc,#a6adbb);border-radius:0.75rem;padding:1.5rem 1.75rem 1.25rem;max-width:340px;width:90%;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.5)">
        <h2 id="mobileWarningTitle" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;">Mobile device notice</h2>
        <p style="margin:0 0 1rem;font-size:1rem;line-height:1.5;font-weight:500">
            ⚠️ Mobile is not currently supported.<br>
            <span style="font-size:0.85rem;opacity:0.75">Please use a desktop or laptop browser for the best experience.</span>
        </p>
        <!-- WCAG 2.2: 1.4.3 contrast ≥4.5:1, 2.5.8 target ≥24×24px -->
        <button id="mobileWarningClose" aria-label="Close mobile warning" style="position:absolute;top:6px;right:8px;background:transparent;border:none;cursor:pointer;font-size:1rem;color:rgba(200,200,200,0.9);padding:4px 8px;line-height:1;min-width:24px;min-height:24px;display:flex;align-items:center;justify-content:center;">&#x2715;</button>
    </div>
</dialog>
<script>
(function () {
    var STORAGE_KEY = 'sm_mobile_warning_dismissed';
    function shouldShow() {
        try { if (localStorage.getItem(STORAGE_KEY)) return false; } catch (e) {}
        return window.innerWidth <= 768;
    }
    function init() {
        if (!shouldShow()) return;
        var dialog = document.getElementById('mobileWarningModal');
        if (!dialog) return;
        dialog.style.display = 'flex';
        var closeBtn = document.getElementById('mobileWarningClose');
        if (!closeBtn) return;
        closeBtn.addEventListener('click', function () {
            dialog.style.display = 'none';
            try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
</script>


<?php if (!$this->session->userdata('is_logged_in')): ?>
<!-- Sticky Sign-Up CTA bar (shown to guests, dismissible via localStorage) -->
<div id="guest-cta-bar" class="guest-cta-bar" role="complementary" aria-label="Sign up prompt" hidden>
    <div class="guest-cta-bar-inner">
        <span class="guest-cta-bar-text">
            <i class="fa-solid fa-snowflake2 mr-1"></i>
            <?php echo htmlspecialchars($this->lang->line('home')['guest_cta_bar_text'] ?? 'Ski-Manager is free to play – no download, no credit card.', ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <a href="<?php echo base_url('register_controller'); ?>" class="btn btn-primary btn-sm guest-cta-bar-btn">
            <i class="fa-solid fa-user-plus mr-1"></i><?php echo htmlspecialchars($this->lang->line('home')['guest_cta_bar_btn'] ?? 'Create Free Account', ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <button type="button" id="guest-cta-bar-close" class="guest-cta-bar-close" aria-label="Dismiss">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
<script>
(function () {
    var BAR_KEY = 'sm_guest_cta_dismissed';
    var bar = document.getElementById('guest-cta-bar');
    if (!bar) return;
    try { if (localStorage.getItem(BAR_KEY)) return; } catch (e) {}
    // Show after 3 s so it doesn't distract on initial load
    setTimeout(function () {
        bar.removeAttribute('hidden');
    }, 3000);
    var closeBtn = document.getElementById('guest-cta-bar-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            bar.setAttribute('hidden', '');
            try { localStorage.setItem(BAR_KEY, '1'); } catch (e) {}
        });
    }
}());
</script>
<?php endif; ?>

<?php $this->load->view('includes/footer'); ?>
