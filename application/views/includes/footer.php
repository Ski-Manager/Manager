<?php if ($this->config->item('google_signin_enabled')): ?>
<!-- Google Identity Services – only loaded on pages that include a Google Sign-In
     button, reducing unused JavaScript on pages that don't need it. -->
<script>
(function() {
    if (document.getElementById('g_id_onload') || document.querySelector('.g_id_signin')) {
        var _gsi = document.createElement('script');
        _gsi.async = true;
        _gsi.src = 'https://accounts.google.com/gsi/client';
        document.head.appendChild(_gsi);
    }
}());
</script>
<script>
function handleGoogleCredential(response) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = (typeof Settings !== 'undefined' && Settings.base_url ? Settings.base_url : '/') + 'login_controller/googleCallback';
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id_token';
    input.value = response.credential;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php endif; ?>

<!-- jQuery 3.7.1 from CDN – deferred to remove from critical request chain.
     onerror loads the local fallback (also deferred) if the CDN is unreachable. -->
<script defer src="https://code.jquery.com/jquery-3.7.1.min.js"
        crossorigin="anonymous"
        onerror="(function(){var s=document.createElement('script');s.defer=true;s.src='<?php echo htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8'); ?>js/jquery.min.js';document.head.appendChild(s);}())"></script>

<?php
// Controllers that are public-only and do not use jQuery UI dialogs or sliders.
// jQuery UI is skipped on these pages to avoid loading unused JavaScript.
$_jqui_skip_controllers = [
    'home_controller', 'index2_controller',
    'register_controller', 'login_controller',
    'contact_controller', 'blogs_controller',
    'help_controller', 'terms_controller',
    'tutorial_controller', 'leaderboard_controller',
    'feature_suggestion_controller',
];
$_needs_jqueryui = !in_array($this->router->class, $_jqui_skip_controllers, true);
?>
<?php if ($_needs_jqueryui): ?>
<!-- jQuery UI 1.13.3 CSS + JS from CDN – only loaded on pages that use dialogs or sliders -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.3/themes/smoothness/jquery-ui.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.3/themes/smoothness/jquery-ui.min.css"></noscript>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"
        crossorigin="anonymous"
        onerror="(function(){var s=document.createElement('script');s.defer=true;s.src='<?php echo htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8'); ?>js/jquery-ui.js';document.head.appendChild(s);}())"></script>
<?php endif; ?>

<!-- Date functions – deferred; countdown init waits for DOMContentLoaded -->
<script defer src="<?php echo base_url(); ?>js/date.min.js?v=<?php echo asset_version('js/date.min.js'); ?>" type="text/javascript"></script>

<!-- Leaflet map – only loaded on the resort-map page to avoid downloading unused
     JS on every other page. -->
<?php if ($this->router->class === 'resort_map_controller'): ?>
<link rel="preload" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" as="style" crossorigin="" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin=""></noscript>
<script defer src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>
<script defer src="<?php echo base_url(); ?>js/leaflet.curve.js?v=<?php echo asset_version('js/leaflet.curve.js'); ?>" type="text/javascript"></script>
<script defer src="<?php echo base_url(); ?>js/leaflet.geometryutil.js?v=<?php echo asset_version('js/leaflet.geometryutil.js'); ?>" type="text/javascript"></script>
<script defer src="<?php echo base_url(); ?>js/leaflet-distance-marker.js?v=<?php echo asset_version('js/leaflet-distance-marker.js'); ?>" type="text/javascript"></script>
<?php endif; ?>

<!-- jQuery Validate plugin – only loaded on pages with forms that need validation -->
<?php if (!in_array($this->router->class, $_jqui_skip_controllers, true)): ?>
<script defer src="https://cdn.jsdelivr.net/npm/jquery-validation@1.22.1/dist/jquery.validate.min.js"></script>
<?php endif; ?>

<?php
// Controllers that use the [data-countdown] jQuery countdown + moment.js.
// Public/informational pages do not display countdown timers so we skip
// these two heavy libraries to reduce main-thread parse/eval time.
$_countdown_controllers = [
    'building_controller', 'building_access_controller',
    'equipment_controller', 'event_venues_controller',
    'lift_controller', 'off_season_controller',
    'slope_controller', 'slope_upgrade_controller',
    'snowmaking_upgrade_controller', 'terrain_engineering_controller',
    'staff_upgrade_controller', 'marketing_upgrade_controller',
    'lift_tech_controller', 'night_skiing_controller',
    'maintenance_controller', 'lift_line_controller',
    'trail_snowmaking_controller', 'rd_controller',
    'restaurant_controller', 'hotel_controller',
    'accommodation_controller', 'rental_controller',
    'leisure_controller', 'luxury_controller',
    'medical_controller', 'facilities_controller',
    'transportation_controller', 'energy_controller',
    'real_estate_controller', 'retail_controller',
    'scenic_lift_controller', 'crowding_controller',
];
$_needs_countdown = in_array($this->router->class, $_countdown_controllers, true);
?>
<?php if ($_needs_countdown): ?>
<!-- Countdown – deferred; init runs after DOMContentLoaded (see below) -->
<script defer src="<?php echo base_url(); ?>js/jquery.countdown.js?v=<?php echo asset_version('js/jquery.countdown.js'); ?>"></script>

<!-- MomentJS + timezone – deferred; countdown init runs after DOMContentLoaded -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.45/moment-timezone-with-data.min.js"></script>
<?php endif; ?>

<!-- Hotjar Tracking Code – deferred until after page load AND only loaded
     when the user has accepted cookies to prevent third-party cookie warnings. -->
<script>
function _smLoadHotjar() {
    if (window._smHotjarLoaded) { return; }
    window._smHotjarLoaded = true;
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:876438,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
}
window.addEventListener('load', function() {
    var _hjChoice;
    try { _hjChoice = localStorage.getItem('sm_cookie_choice'); } catch (e) {}
    if (_hjChoice === 'accepted') {
        _smLoadHotjar();
    }
});
</script>

<!-- Countdown timezone setup – runs after DOMContentLoaded so deferred moment.js/date.js
     and jquery.countdown.js are guaranteed to be available.
     If the jQuery CDN failed and the local fallback is still loading when
     DOMContentLoaded fires, the handler re-registers itself on window.load
     (by which time all resources – including the fallback – are ready). -->
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function _smCountdownInit() {
    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', _smCountdownInit, { once: true });
        return;
    }

$('[data-countdown]').each(function () {
    var $this = $(this),
        finalDate = $(this).data('countdown');

    var is_a_date_format = Date.parseExact(finalDate, "yyyy-M-d H:m:s");

    if (is_a_date_format) {
        finalDate = moment.tz(finalDate, "Etc/UTC");
        $this.countdown(finalDate.toDate(), function (event) {
            if (event.elapsed) {
                $this.html(event.strftime('<?php echo '<a href="' . base_url() . $this->router->class . '"><div class="tooltip tooltip-bottom" style="display:inline;" data-tip="' . $this->lang->line('home')['wait_tooltip'] . '">' . $this->lang->line('home')['wait'] . '</div></a>'; ?>'));
            } else {
                if (event.strftime('%D') == 0) {
                    $this.html(event.strftime('%H:%M:%S'));
                } else {
                    $this.html(event.strftime('%D <?php echo $this->lang->line('home')['days']; ?> %H:%M:%S'));
                }
            }
        });
    }
});
});
</script>

<!-- Provide base_url to JS before home.js loads, so AJAX calls (e.g. leaderboard/data) use the correct URL -->
<script type="text/javascript">
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.base_url = '<?php echo base_url(); ?>';
// Achievement claim settings (needed for sidebar claim buttons on all pages)
Settings.achievement_claimed   = Settings.achievement_claimed   || <?php echo json_encode($this->lang->line('achievements')['achievement_claimed']   ?? ''); ?>;
Settings.achievement_completed = Settings.achievement_completed || <?php echo json_encode($this->lang->line('achievements')['achievement_completed'] ?? ''); ?>;
Settings.already_claimed       = Settings.already_claimed       || <?php echo json_encode($this->lang->line('achievements')['already_claimed']       ?? ''); ?>;
Settings.not_completed         = Settings.not_completed         || <?php echo json_encode($this->lang->line('achievements')['not_completed']         ?? ''); ?>;
Settings.share_achievement     = Settings.share_achievement     || <?php echo json_encode($this->lang->line('achievements')['share_achievement']     ?? ''); ?>;
Settings.share_achievement_text = Settings.share_achievement_text || <?php echo json_encode($this->lang->line('achievements')['share_achievement_text'] ?? ''); ?>;
// Toast notification messages for home.js
Settings.equipment_not_sold    = Settings.equipment_not_sold    || <?php echo json_encode($this->lang->line('common_equipment')['equipment_not_sold']   ?? ''); ?>;
Settings.lift_repaired         = Settings.lift_repaired         || <?php echo json_encode($this->lang->line('lift')['repaired']                          ?? ''); ?>;
Settings.not_enough_money      = Settings.not_enough_money      || <?php echo json_encode($this->lang->line('home')['not_enough_money']                  ?? ''); ?>;
Settings.no_mechanics          = Settings.no_mechanics          || <?php echo json_encode($this->lang->line('lift')['no_mechanics']                       ?? ''); ?>;
Settings.ongoing_special_event = Settings.ongoing_special_event || <?php echo json_encode($this->lang->line('special_events')['ongoing_event']           ?? ''); ?>;
Settings.loan_not_signed_up    = Settings.loan_not_signed_up    || <?php echo json_encode($this->lang->line('bank')['loan_not_signed_up']                ?? ''); ?>;
Settings.loan_not_payed_off    = Settings.loan_not_payed_off    || <?php echo json_encode($this->lang->line('bank')['loan_not_payed_off']                ?? ''); ?>;
Settings.is_logged_in          = <?php echo $this->session->userdata('is_logged_in') ? 'true' : 'false'; ?>;
var active_sectors = <?php echo (int)ACTIVE_SECTORS; ?>;
</script>

<!-- General JS – deferred so it downloads in parallel; Settings/active_sectors
     are defined in the inline block above and will be available when this runs. -->
<script defer src="<?php echo base_url(); ?>js/home.min.js?v=<?php echo asset_version('js/home.min.js'); ?>"></script>

<!-- CrazyGames SDK integration — handles auto-login and auth listeners -->
<script defer src="<?php echo base_url(); ?>js/crazygames.js?v=<?php echo asset_version('js/crazygames.js'); ?>"></script>

<!-- Legacy interaction shim for old modal/collapse markup while views move to DaisyUI. -->
<script defer src="<?php echo base_url(); ?>js/sm-bootstrap-shim.js?v=<?php echo asset_version('js/sm-bootstrap-shim.js'); ?>"></script>

<?php if ($this->router->class === 'register_controller'): ?>
<!-- Country picker – searchable flag dropdown, only needed on the signup page -->
<script defer src="<?php echo base_url(); ?>js/country-picker.js?v=<?php echo asset_version('js/country-picker.js'); ?>"></script>
<?php endif; ?>

<?php
// Controllers that actually initialise a DataTable (hire-staff, activity logs,
// leaderboard).  All other pages skip both DataTables JS files to avoid loading
// unused JavaScript.  Keep in sync with $_dt_css_controllers in header.php.
$_datatables_controllers = [
    'leaderboard_controller', 'logs_controller', 'hire_staff_controller',
];
$_needs_datatables = in_array($this->router->class, $_datatables_controllers, true);
?>
<?php if ($_needs_datatables): ?>
<!-- DataTables – only loaded on pages that initialise a DataTable -->
<script defer src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script defer src="https://cdn.datatables.net/2.3.7/js/dataTables.dataTables.min.js"></script>
<?php endif; ?>

<!-- App UA detection: adds is-app class so CSS can adapt for the Android app -->
<script>
(function () {
    if (/SkiManagerApp/.test(navigator.userAgent)) {
        document.documentElement.classList.add('is-app');
    }
}());
</script>

<!-- Sidebar toggle – vanilla JS so it runs independently of deferred jQuery -->
<script>
(function () {
    var SIDEBAR_KEY = 'sm_sidebar';
    var isMobile = function () { return window.innerWidth < 768; };

    function setBackdrop(show) {
        var bd = document.getElementById('sidebar-mobile-backdrop');
        if (!bd) return;
        bd.classList[show ? 'add' : 'remove']('active');
    }

    function applyMobileOpen(open) {
        var sidebar = document.getElementById('sidebar-wrapper');
        var menuBtn = document.getElementById('menu-toggle');
        if (sidebar) { sidebar.classList[open ? 'add' : 'remove']('mobile-open'); }
        if (menuBtn) { menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
        setBackdrop(open);
        document.body.style.overflow = open ? 'hidden' : '';
    }

    function applySidebarState(collapsed) {
        if (isMobile()) {
            /* On mobile use the overlay drawer model, not the desktop push model */
            applyMobileOpen(!collapsed);
            return;
        }
        var action = collapsed ? 'add' : 'remove';
        var wrapper = document.querySelector('.wrapper');
        var sidebar = document.getElementById('sidebar-wrapper');
        var content = document.getElementById('page-content-wrapper');
        var menuBtn = document.getElementById('menu-toggle');
        if (wrapper) { wrapper.classList[action]('toggled'); }
        if (sidebar) { sidebar.classList[action]('toggled'); }
        if (content) { content.classList[action]('toggled'); }
        if (menuBtn) {
            menuBtn.classList[action]('toggle_open');
            menuBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
    }

    /* On mobile: always start closed. On desktop: restore saved state. */
    var saved;
    if (isMobile()) {
        saved = 'collapsed';
    } else {
        try { saved = localStorage.getItem(SIDEBAR_KEY) || 'expanded'; } catch (e) { saved = 'expanded'; }
    }
    applySidebarState(saved === 'collapsed');

    var menuToggle = document.getElementById('menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            if (isMobile()) {
                var sidebar = document.getElementById('sidebar-wrapper');
                var isOpen = sidebar && sidebar.classList.contains('mobile-open');
                applyMobileOpen(!isOpen);
            } else {
                var isToggled = !document.getElementById('sidebar-wrapper').classList.contains('toggled');
                applySidebarState(isToggled);
                try { localStorage.setItem(SIDEBAR_KEY, isToggled ? 'collapsed' : 'expanded'); } catch (e) {}
            }
        });
    }

    /* Close mobile drawer when backdrop is tapped */
    var backdrop = document.getElementById('sidebar-mobile-backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function () { applyMobileOpen(false); });
    }

    /* Close button inside sidebar */
    var sidebarCloseBtn = document.getElementById('sidebar-mobile-close');
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function () { applyMobileOpen(false); });
    }

    /* On resize from mobile → desktop, clean up mobile-open state */
    window.addEventListener('resize', function () {
        if (!isMobile()) {
            setBackdrop(false);
            document.body.style.overflow = '';
            var sidebar = document.getElementById('sidebar-wrapper');
            if (sidebar) sidebar.classList.remove('mobile-open');
        }
    });
}());
</script>

<!-- Site footer -->
<footer class="site-footer" role="contentinfo">
    <div class="wrapper">
        <div class="w-full">
            <!-- Footer columns -->
            <div class="grid grid-cols-12 gap-3 py-4 footer-columns">
                <!-- About column -->
                <div class="col-span-12 sm:col-span-6 md:col-span-3 mb-4 mb-md-0">
                    <h2 class="footer-heading"><i class="fa-solid fa-snowflake2 mr-1"></i> Ski-Manager</h2>
                    <p class="footer-about-text"><?php echo $this->lang->line('home')['footer_about_text'] ?? 'Free online ski resort management game. Build, manage and grow your dream ski resort.'; ?></p>
                </div>
                <!-- Game links -->
                <div class="col-span-12 sm:col-span-6 md:col-span-3 mb-4 mb-md-0">
                    <h2 class="footer-heading">Game</h2>
                    <ul class="footer-nav-list">
                        <li><a href="<?php echo base_url('home_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['home'] ?? 'Home'; ?></a></li>
                        <li><a href="<?php echo base_url('leaderboard_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['leaderboard'] ?? 'Leaderboard'; ?></a></li>
                        <li><a href="<?php echo base_url('tutorial_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['tutorial'] ?? 'Tutorial'; ?></a></li>
                        <li><a href="<?php echo base_url('blogs_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['blog'] ?? 'Blog &amp; News'; ?></a></li>
                        <li><a href="<?php echo base_url('register_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['signup'] ?? 'Sign Up'; ?></a></li>
                    </ul>
                </div>
                <!-- Info links -->
                <div class="col-span-12 sm:col-span-6 md:col-span-3 mb-4 mb-md-0">
                    <h2 class="footer-heading">Info</h2>
                    <ul class="footer-nav-list">
                        <li><a href="<?php echo base_url('about'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['about'] ?? 'About'; ?></a></li>
                        <li><a href="<?php echo base_url('help_controller'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['help'] ?? 'Help'; ?></a></li>
                        <li><a href="<?php echo base_url('contact'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['contact'] ?? 'Contact'; ?></a></li>
                        <li><a href="<?php echo base_url('privacy'); ?>" class="footer-link"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Politique de Confidentialité' : 'Privacy Policy'; ?></a></li>
                        <li><a href="<?php echo base_url('cookies'); ?>" class="footer-link"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Politique de Cookies' : 'Cookie Policy'; ?></a></li>
                        <li><a href="#" class="footer-link" onclick="smReopenCookieBanner();return false;"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Gérer les cookies' : 'Manage Cookies'; ?></a></li>
                        <li><a href="<?php echo base_url('terms'); ?>" class="footer-link"><?php echo $this->lang->line('navbar')['terms'] ?? 'Terms of Service'; ?></a></li>
                        <li><a href="https://stats.uptimerobot.com/Bv1Gxznd1N" target="_blank" rel="noopener noreferrer" class="footer-link"><?php echo $this->lang->line('navbar')['status'] ?? 'Status'; ?></a></li>
                    </ul>
                </div>
                <!-- Community / social -->
                <div class="col-span-12 sm:col-span-6 md:col-span-3 mb-4 mb-md-0">
                    <h2 class="footer-heading">Community</h2>
                    <div class="flex flex-col gap-2">
                        <a href="https://discordapp.com/channels/593708638212718632/593708638695325696" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                            <i class="fa-brands fa-discord mr-2"></i>Discord
                        </a>
                        <a href="https://www.facebook.com/Ski-Manager-1377272882355150/" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                            <i class="fa-brands fa-facebook mr-2"></i>Facebook
                        </a>
                        <a href="https://github.com/Ski-Manager-net/Manager/issues" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                            <i class="fa-solid fa-bug mr-2"></i><?php echo $this->lang->line('home')['report_bug'] ?? 'Report a Bug'; ?>
                        </a>
                        <a href="https://github.com/Ski-Manager-net/Manager" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                            <i class="fa-solid fa-screwdriver-wrench mr-2"></i><?php echo $this->lang->line('navbar')['help_build'] ?? 'Help Build Ski-Manager'; ?>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Bottom bar -->
            <div class="footer-bottom flex flex-wrap justify-center items-center gap-3 py-2">
                <span><?php echo $this->lang->line('home')['copyright']; ?></span>
                <a href="https://www.profitablecpmratenetwork.com/pb8atatzsi?key=0ffcac281ea74e89e0049db7801944f8" target="_blank" rel="noopener noreferrer" class="footer-link">Ski-Manager</a>
            </div>
        </div>
    </div>
</footer>

<!-- Native Banner -->
<div class="wrapper">
    <div class="native-banner-wrapper" style="margin:1rem auto;max-width:728px;">
        <script async="async" data-cfasync="false" src="https://pl29335451.profitablecpmratenetwork.com/71c805c153658d01357edade6f353ba1/invoke.js"></script>
        <div id="container-71c805c153658d01357edade6f353ba1"></div>
    </div>
</div>

<!-- Slim page-loading progress bar -->
<div id="page-progress-bar" role="progressbar" aria-hidden="true"></div>

<!-- Back-to-top button -->
<button id="back-to-top" type="button" aria-label="Back to top" title="Back to top">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- Report a Bug floating button -->
<a id="report-bug-btn" href="https://github.com/Ski-Manager-net/Manager/issues" target="_blank" rel="noopener noreferrer" title="Report a Bug" aria-label="Report a Bug">
    <i class="fa-solid fa-bug" aria-hidden="true"></i>
</a>

<!-- Open source announcement -->
<div id="sm-open-source-popup" role="dialog" aria-labelledby="sm-open-source-title" aria-live="polite">
    <button id="sm-open-source-close" type="button" aria-label="<?php echo ($this->session->userdata('site_lang') === 'french') ? 'Fermer' : 'Close'; ?>">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>
    <div class="sm-open-source-icon" aria-hidden="true">
        <i class="fa-brands fa-github"></i>
    </div>
    <div class="sm-open-source-copy">
        <p id="sm-open-source-title" class="sm-open-source-title">
            <?php echo ($this->session->userdata('site_lang') === 'french') ? 'Ski-Manager est maintenant open source' : 'Ski-Manager is now open source'; ?>
        </p>
        <p class="sm-open-source-text">
            <?php echo ($this->session->userdata('site_lang') === 'french') ? 'Consultez le code, proposez des changements ou suivez le developpement sur GitHub.' : 'View the code, suggest changes, or follow development on GitHub.'; ?>
        </p>
        <a class="sm-open-source-link" href="https://github.com/Ski-Manager-net/Manager" target="_blank" rel="noopener noreferrer">
            GitHub <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>
</div>

<!-- Toast notification container (DaisyUI toast) -->
<div id="sm-toast-container" class="toast toast-top toast-end sm-toast-container" aria-live="polite" aria-atomic="true"></div>

<script>
(function () {
    var popup = document.getElementById('sm-open-source-popup');
    var close = document.getElementById('sm-open-source-close');
    if (!popup || !close) { return; }

    var dismissed = false;
    try { dismissed = localStorage.getItem('sm_open_source_notice_dismissed') === '1'; } catch (e) {}
    if (!dismissed) {
        requestAnimationFrame(function () {
            popup.classList.add('sm-open-source-popup-visible');
        });
    }

    close.addEventListener('click', function () {
        popup.classList.remove('sm-open-source-popup-visible');
        try { localStorage.setItem('sm_open_source_notice_dismissed', '1'); } catch (e) {}
    });
}());
</script>
<!-- Site-wide UX/UI enhancements – wrapped in DOMContentLoaded so deferred jQuery is ready.
     Falls back to window.load when the jQuery CDN fails and the local fallback is still
     loading at DOMContentLoaded time. -->
<script>
document.addEventListener('DOMContentLoaded', function _smUIInit() {
    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', _smUIInit, { once: true });
        return;
    }
(function ($) {
    // ── 0. Mobile navbar toggle ──────────────────────────────────────────────
    var $mobileToggle = $('#navbar-mobile-toggle');
    var $collapsible  = $('#navbar-collapsible');
    $mobileToggle.on('click', function () {
        var isOpen = $collapsible.hasClass('show');
        $collapsible.toggleClass('show', !isOpen);
        $mobileToggle.attr('aria-expanded', !isOpen ? 'true' : 'false');
    });

    // Close the mobile nav when any navigation link inside it is tapped,
    // so the menu collapses and the new page content is immediately visible.
    $collapsible.on('click', 'a[href]', function () {
        if (window.innerWidth < 768) {
            $collapsible.removeClass('show');
            $mobileToggle.attr('aria-expanded', 'false');
        }
    });

    // ── 2. Active navbar item ────────────────────────────────────────────────
    (function () {
        // Strip base path so this works whether app is at / or /subdir/
        var basePath = (Settings.base_url || '/').replace(/^https?:\/\/[^\/]+/, '').replace(/\/?$/, '/');
        var relPath = window.location.pathname;
        if (relPath.indexOf(basePath) === 0) {
            relPath = relPath.slice(basePath.length);
        } else {
            relPath = relPath.replace(/^\/+/, '');
        }
        var seg = relPath.split('/')[0] || '';

        // Maps controller name → nav section. Add new controllers here as needed.
        var sectionMap = {
            home_controller: 'home',
            resort_controller: 'resort',
            building_access_controller: 'buildings',
            trail_snowmaking_controller: 'buildings',
            lift_tech_controller: 'buildings',
            slope_upgrade_controller: 'buildings',
            snowmaking_upgrade_controller: 'buildings',
            marketing_upgrade_controller: 'buildings',
            staff_upgrade_controller: 'buildings',
            upgrades_controller: 'buildings',
            rd_controller: 'buildings',
            night_skiing_controller: 'buildings',
            restaurant_controller: 'buildings',
            hotel_controller: 'buildings',
            accommodation_controller: 'buildings',
            rental_controller: 'buildings',
            leisure_controller: 'buildings',
            luxury_controller: 'buildings',
            medical_controller: 'buildings',
            facilities_controller: 'buildings',
            event_venues_controller: 'buildings',
            energy_controller: 'buildings',
            transportation_controller: 'buildings',
            crowding_controller: 'buildings',
            maintenance_controller: 'buildings',
            lift_line_controller: 'buildings',
            real_estate_controller: 'revenue_prestige',
            retail_controller: 'revenue_prestige',
            scenic_lift_controller: 'revenue_prestige',
            celebrity_visit_controller: 'revenue_prestige',
            insurance_controller: 'revenue_prestige',
            environment_controller: 'revenue_prestige',
            off_season_controller: 'revenue_prestige',
            sponsorship_controller: 'revenue_prestige',
            overview_staff_controller: 'staff',
            hire_staff_controller: 'staff',
            groomer_controller: 'staff',
            skibus_controller: 'staff',
            guest_skill_controller: 'management',
            finances_controller: 'management',
            bank_controller: 'management',
            logs_controller: 'management',
            weather_controller: 'management',
            climate_change_controller: 'management',
            marketing_controller: 'management',
            guest_ai_controller: 'management',
            visitor_needs_controller: 'management',
            demand_curve_controller: 'management',
            empire_controller: 'management',
            reporting_controller: 'management',
            lift_network_controller: 'management',
            tournaments_controller: 'management',
            competitors_controller: 'management',
            statistics_controller: 'management',
            data_dashboard_controller: 'management',
            special_events_controller: 'management',
            mountain_plan_controller: 'management',
            crisis_controller: 'management',
            vip_loyalty_controller: 'management',
            emergency_controller: 'management',
            season_pass_controller: 'management',
            leaderboard: 'account',
            achievements_controller: 'account',
            seasonal_objectives_controller: 'account',
            account_controller: 'account',
            genepis_controller: 'account',
            help_controller: 'account',
            register_controller: 'signup',
            contact_controller: 'more',
            contact: 'more',
            about: 'more',
            blogs_controller: 'more',
            blog: 'more',
            feature_suggestion_controller: 'more'
        };
        var section = sectionMap[seg];
        if (section) {
            $('[data-nav-section="' + section + '"]').addClass('nav-active');
        }
    }());

    // ── 3. Auto-dismiss flash alerts ─────────────────────────────────────────
    $(document).ready(function () {
        $('.alert-success, .alert-info').not('[data-no-autodismiss]').each(function () {
            var el = this;
            setTimeout(function () {
                /* Use a CSS opacity transition instead of jQuery fadeTo+slideUp so
                   no height read (forced reflow) is triggered on every animation frame. */
                el.classList.add('sm-alert-hiding');
                /* 350 ms > the 300 ms opacity transition in .alert to ensure the
                   element is fully hidden before removal. */
                setTimeout(function () {
                    if (el.parentNode) { el.parentNode.removeChild(el); }
                }, 350);
            }, 5000);
        });
    });

    // ── 4. Back-to-top button ────────────────────────────────────────────────
    var $btn = $('#back-to-top');
    var scrollTicking = false;
    $(window).on('scroll.backtotop', function () {
        if (!scrollTicking) {
            window.requestAnimationFrame(function () {
                if (window.scrollY > 200) {
                    $btn.addClass('visible');
                } else {
                    $btn.removeClass('visible');
                }
                scrollTicking = false;
            });
            scrollTicking = true;
        }
    });
    $btn.on('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ── 5. Form submit button loading state ──────────────────────────────────
    $(document).on('submit', 'form', function (e) {
        var $form = $(this);
        // Preserve the clicked button's name/value before disabling it
        var submitter = e.originalEvent && e.originalEvent.submitter;
        if (submitter && submitter.name) {
            $form.find('input[type="hidden"][data-from-submitter]').remove();
            $('<input>').attr({ type: 'hidden', name: submitter.name, value: submitter.value, 'data-from-submitter': '1' }).appendTo($form);
        }
        $form.find('[type="submit"]').each(function () {
            var $btn = $(this);
            if ($btn.data('sm-loading-set')) { return; }
            $btn.data('sm-loading-set', true);
            var originalText = $btn.val() || $btn.html();
            $btn.prop('disabled', true);
            if ($btn.is('input')) {
                $btn.val('…');
            } else {
                $btn.html('<span class="loading loading-spinner loading-xs mr-1" role="status" aria-hidden="true"></span>' + originalText);
            }
            /* Re-enable after 10 s as a safety fallback (e.g. validation errors
               that keep the user on the same page without a full page reload). */
            setTimeout(function () {
                if ($btn.data('sm-loading-set')) {
                    $btn.prop('disabled', false);
                    if ($btn.is('input')) {
                        $btn.val(originalText);
                    } else {
                        $btn.html(originalText);
                    }
                    $btn.removeData('sm-loading-set');
                }
            }, 10000);
        });
    });

    // ── 6. Horizontal scroll shadow for wide tables ──────────────────────────
    // Wraps horizontally-scrollable game tables in a .sm-scroll-shadow-wrap
    // container and removes the right-edge shadow when the user has scrolled
    // to the end of the overflowing content.
    // Uses a rAF defer so layout is complete before measuring overflow.
    (function () {
        function initScrollShadows() {
            var scrollables = document.querySelectorAll(
                'table.building, table.building_6th, table.building_7th, ' +
                'table.staff, table.achievements, table.cannon_table, ' +
                'table.tournaments, table.blue_header, table.mini_table'
            );
            scrollables.forEach(function (tbl) {
                /* Only wrap tables that actually overflow their container.
                   The table has display:block + overflow-x:auto on mobile
                   so scrollWidth/clientWidth comparison is valid on the element. */
                var scrollEl = tbl;
                /* If the computed style doesn't indicate scroll, check parent */
                var cs = window.getComputedStyle(tbl);
                if (cs.overflowX !== 'auto' && cs.overflowX !== 'scroll') {
                    scrollEl = tbl.parentElement;
                    if (!scrollEl) { return; }
                    var pcs = window.getComputedStyle(scrollEl);
                    if (pcs.overflowX !== 'auto' && pcs.overflowX !== 'scroll') { return; }
                }
                if (scrollEl.scrollWidth <= scrollEl.clientWidth) { return; }
                var parent = scrollEl.parentNode;
                if (!parent || parent.classList.contains('sm-scroll-shadow-wrap')) { return; }
                var wrap = document.createElement('div');
                wrap.className = 'sm-scroll-shadow-wrap';
                parent.insertBefore(wrap, scrollEl);
                wrap.appendChild(scrollEl);

                /* Remove shadow when scrolled to the right end */
                function checkEnd() {
                    var atEnd = (scrollEl.scrollLeft + scrollEl.clientWidth) >= (scrollEl.scrollWidth - 2);
                    wrap.classList.toggle('sm-scroll-end', atEnd);
                }
                scrollEl.addEventListener('scroll', checkEnd, { passive: true });
                checkEnd();
            });
        }
        /* Defer to allow CSS + layout to settle before measuring */
        requestAnimationFrame(function () {
            requestAnimationFrame(initScrollShadows);
        });
    }());

    // ── 7. Sidebar toggle icon rotation ─────────────────────────────────────
    // Update the sidebar toggle icon (bi-layout-sidebar-reverse ↔
    // bi-layout-sidebar) to reflect the current sidebar state.
    // applySidebarState() already updates aria-expanded and toggled classes;
    // here we additionally keep the icon direction in sync.
    (function () {
        var menuBtn = document.getElementById('menu-toggle');
        if (!menuBtn) { return; }
        var icon = menuBtn.querySelector('.sidebar-toggle-icon');
        if (!icon) { return; }

        function syncIcon() {
            /* #sidebar-wrapper gets the 'toggled' class via applySidebarState() */
            var sidebar = document.getElementById('sidebar-wrapper');
            var isCollapsed = sidebar && sidebar.classList.contains('toggled');
            if (window.innerWidth >= 768) {
                /* Desktop: show "expand" icon when sidebar is collapsed */
                icon.className = 'bi sidebar-toggle-icon ' +
                    (isCollapsed ? 'bi-layout-sidebar' : 'bi-layout-sidebar-reverse');
            } else {
                /* Mobile: sidebar is always narrow, show collapse icon */
                icon.className = 'bi sidebar-toggle-icon bi-layout-sidebar';
            }
        }

        /* Sync once after the sidebar state has been restored from localStorage */
        requestAnimationFrame(syncIcon);
        menuBtn.addEventListener('click', function () {
            /* Slight delay so the toggled class has been applied first */
            setTimeout(syncIcon, 50);
        });
    }());

}(jQuery));
}); // end DOMContentLoaded
</script>

<!-- Navbar page search -->
<script>
(function () {
    var input   = document.getElementById('nav-search-input');
    var list    = document.getElementById('nav-search-results');
    var clearBtn = document.getElementById('nav-search-clear');
    if (!input || !list) { return; }

    var base = (typeof Settings !== 'undefined' && Settings.base_url) ? Settings.base_url : '/';

    // All navigable pages: [label, controller_path]
    var pages = <?php
        $nav = $this->lang->line('navbar');
        $pages = [
            [$nav['home']               ?? 'Home',                       'home_controller'],
            [$nav['resort']             ?? 'Resort',                     'resort_controller'],
            [$nav['accessibility']      ?? 'Access & parkings',          'building_access_controller'],
            [$nav['snowmaking']         ?? 'Snowmaking',                 'trail_snowmaking_controller'],
            [$nav['lift_tech']          ?? 'Lift technology',            'lift_tech_controller'],
            [$nav['upgrades']           ?? 'Upgrades',                    'upgrades_controller'],
            [$nav['rd']                 ?? 'Experimental R&D',           'rd_controller'],
            [$nav['nightSkiing']        ?? 'Night skiing',               'night_skiing_controller'],
            [$nav['energy']             ?? 'Energy management',          'energy_controller'],
            [$nav['liftLine']           ?? 'Lift line management',       'lift_line_controller'],
            [$nav['maint_depth']        ?? 'Maintenance depth',          'maintenance_controller'],
            [$nav['restaurants']        ?? 'Restaurants',                'restaurant_controller'],
            [$nav['hotels']             ?? 'Hotels',                     'hotel_controller'],
            [$nav['accommodation']      ?? 'Accommodation Upgrades',     'accommodation_controller'],
            [$nav['rentals']            ?? 'Ski rentals',                'rental_controller'],
            [$nav['leisure']            ?? 'Leisure',                    'leisure_controller'],
            [$nav['luxury']             ?? 'Luxury',                     'luxury_controller'],
            [$nav['medical']            ?? 'Medical',                    'medical_controller'],
            [$nav['facilities']         ?? 'Resort facilities',          'facilities_controller'],
            [$nav['event_venues']       ?? 'Event venues',               'event_venues_controller'],
            [$nav['environment']        ?? 'Environment',                'environment_controller'],
            [$nav['off_season']         ?? 'Off-Season',                 'off_season_controller'],
            [$nav['real_estate']        ?? 'Real estate',                'real_estate_controller'],
            [$nav['transportation']     ?? 'Transportation',             'transportation_controller'],
            [$nav['crowding']           ?? 'Crowding Management',        'crowding_controller'],
            [$nav['sponsorship']        ?? 'Sponsorship',                'sponsorship_controller'],
            [$nav['retail']             ?? 'Retail & Amenities',         'retail_controller'],
            [$nav['scenic_lift']        ?? 'Scenic Lifts',               'scenic_lift_controller'],
            [$nav['celebrity_visit']    ?? 'Celebrity Visits',           'celebrity_visit_controller'],
            [$nav['insurance']          ?? 'Insurance',                  'insurance_controller'],
            [$nav['overview_staff']     ?? 'Overview staff',             'overview_staff_controller'],
            [$nav['hire']               ?? 'Hire staff',                 'hire_staff_controller'],
            [$nav['groomers']           ?? 'Snow groomers',              'groomer_controller'],
            [$nav['skibuses']           ?? 'Ski buses',                  'skibus_controller'],
            [$nav['finances']           ?? 'Finances',                   'finances_controller'],
            [$nav['bank']               ?? 'Bank',                       'bank_controller'],
            [$nav['logs']               ?? 'Activity',                   'logs_controller'],
            [$nav['weather']            ?? 'Weather',                    'weather_controller'],
            [$nav['climate_change']     ?? 'Climate Change',             'climate_change_controller'],
            [$nav['marketing']          ?? 'Marketing',                  'marketing_controller'],
            [$nav['guest_ai']           ?? 'Guest AI',                   'guest_ai_controller'],
            [$nav['visitor_needs']      ?? 'Visitor Needs',              'visitor_needs_controller'],
            [$nav['demand_curve']       ?? 'Demand curve',               'demand_curve_controller'],
            [$nav['empire']             ?? 'Empire',                     'empire_controller'],
            [$nav['analysis']           ?? 'Resort analysis',            'reporting_controller'],
            [$nav['lift_network']       ?? 'Lift Network',               'lift_network_controller'],
            [$nav['tournaments']        ?? 'Tournaments',                'tournaments_controller'],
            [$nav['competitors']        ?? 'Competitor Resorts',         'competitors_controller'],
            [$nav['statistics']         ?? 'Statistics',                 'statistics_controller'],
            [$nav['data_dashboard']     ?? 'Data Dashboard',             'data_dashboard_controller'],
            [$nav['special_events']     ?? 'Special Events',             'special_events_controller'],
            [$nav['guest_skill']        ?? 'Guest Skills',               'guest_skill_controller'],
            [$nav['mountain_plan']      ?? 'Mountain Master Plan',       'mountain_plan_controller'],
            [$nav['crisis_events']      ?? 'Crisis Events',              'crisis_controller'],
            [$nav['vip_loyalty']        ?? 'VIP & Loyalty',              'vip_loyalty_controller'],
            [$nav['emergency']          ?? 'Emergency & Rescue',         'emergency_controller'],
            [$nav['season_pass']        ?? 'Season Ski Passes',          'season_pass_controller'],
            [$nav['leaderboard']        ?? 'Leaderboard',                'leaderboard'],
            [$nav['achievements']       ?? 'Achievements',               'achievements_controller'],
            [$nav['seasonal_objectives']?? 'Seasonal Objectives',        'seasonal_objectives_controller'],
            [$nav['account_options']    ?? 'Account options',            'account_controller'],
            [$nav['help']               ?? 'Help',                       'help_controller'],
            [$nav['blog']               ?? 'Blog & News',                'blogs_controller'],
            [$nav['contact']            ?? 'Contact',                    'contact_controller'],
        ];
        echo json_encode($pages, JSON_UNESCAPED_UNICODE);
    ?>;

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function highlightMatch(label, query) {
        var idx = label.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) { return escapeHtml(label); }
        return escapeHtml(label.slice(0, idx))
             + '<span class="search-match">' + escapeHtml(label.slice(idx, idx + query.length)) + '</span>'
             + escapeHtml(label.slice(idx + query.length));
    }

    function updateClearBtn() {
        if (clearBtn) {
            clearBtn.style.display = input.value ? 'block' : 'none';
        }
    }

    function renderResults(query) {
        list.innerHTML = '';
        updateClearBtn();
        if (!query) { list.classList.remove('show'); input.setAttribute('aria-expanded', 'false'); return; }
        var q = query.toLowerCase();
        var matches = pages.filter(function (p) {
            return p[0].toLowerCase().indexOf(q) !== -1;
        });
        if (matches.length === 0) {
            var empty = document.createElement('li');
            empty.className = 'nav-search-empty';
            empty.setAttribute('aria-live', 'polite');
            empty.textContent = '<?php echo addslashes($this->lang->line('navbar')['search_no_results'] ?? 'No results found'); ?>';
            list.appendChild(empty);
            list.classList.add('show');
            input.setAttribute('aria-expanded', 'true');
            return;
        }
        matches.forEach(function (p, idx) {
            var li = document.createElement('li');
            var a  = document.createElement('a');
            a.className = 'dropdown-item';
            a.href = base + p[1];
            a.setAttribute('role', 'option');
            a.id = 'nav-search-opt-' + idx;
            a.innerHTML = '<i class="fa-solid fa-arrow-right search-icon" aria-hidden="true"></i>' + highlightMatch(p[0], query);
            li.appendChild(a);
            list.appendChild(li);
        });
        list.classList.add('show');
        input.setAttribute('aria-expanded', 'true');
    }

    input.addEventListener('input', function () {
        renderResults(this.value.trim());
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            renderResults('');
            input.focus();
        });
    }

    input.addEventListener('keydown', function (e) {
        var items = list.querySelectorAll('.dropdown-item');
        var focused = list.querySelector('.dropdown-item:focus');
        var idx = Array.prototype.indexOf.call(items, focused);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            var next = items[idx + 1] || items[0];
            if (next) { next.focus(); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            var prev = items[idx - 1] || items[items.length - 1];
            if (prev) { prev.focus(); }
        } else if (e.key === 'Escape') {
            list.classList.remove('show');
            input.setAttribute('aria-expanded', 'false');
            input.value = '';
            updateClearBtn();
            input.blur();
        }
    });

    // Press "/" to focus the search (when not already in an input)
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA' && !document.activeElement.isContentEditable) {
            e.preventDefault();
            input.focus();
            input.select();
        }
    });

    document.addEventListener('click', function (e) {
        var wrapper = document.getElementById('nav-search-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            list.classList.remove('show');
            input.setAttribute('aria-expanded', 'false');
        }
    });
}());
</script>

<!-- Theme toggle -->
<script>
(function () {
    var STORAGE_KEY = 'sm_theme';
    var toggle = document.getElementById('theme-toggle');
    if (!toggle) { return; }

    // NOTE: DARK_THEMES must stay in sync with the list in the inline script in header.php.
    var DARK_THEMES = ['dark'];

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        var isDark = DARK_THEMES.indexOf(theme) !== -1;
        document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        toggle.checked = isDark;
        try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
    }

    var saved = 'light';
    try { saved = localStorage.getItem(STORAGE_KEY) || 'light'; } catch (e) {}
    if (saved !== 'light' && DARK_THEMES.indexOf(saved) === -1) { saved = 'light'; }
    applyTheme(saved);

    toggle.addEventListener('change', function () {
        applyTheme(toggle.checked ? 'dark' : 'light');
    });
}());
</script>

<!-- Page loading progress bar -->
<script>
/* Remove the 'preload' class added in the <head> inline script once the page
   is interactive so that CSS transitions/animations are re-enabled after all
   initial state (sidebar, dark-mode) has been applied without forced reflows. */
(function () {
    function removePreload() {
        document.documentElement.classList.remove('preload');
    }
    if (document.readyState === 'complete') {
        removePreload();
    } else {
        window.addEventListener('load', removePreload, { once: true });
    }
}());
document.addEventListener('DOMContentLoaded', function _smProgressBarInit() {
    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', _smProgressBarInit, { once: true });
        return;
    }
(function ($) {
    var bar = document.getElementById('page-progress-bar');
    if (!bar) { return; }
    var timer = null;

    function startBar() {
        clearTimeout(timer);
        bar.style.transition = 'none';
        bar.style.transform = 'scaleX(0)';
        bar.style.opacity = '1';
        /* Double-rAF: the first frame lets the browser apply the reset styles;
           the second re-enables transitions so the animation starts cleanly,
           avoiding a synchronous forced reflow (offsetWidth read). */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                bar.style.transition = 'transform 0.25s ease, opacity 0.3s ease';
                setTimeout(function () { bar.style.transform = 'scaleX(0.4)'; }, 20);
                setTimeout(function () { bar.style.transform = 'scaleX(0.7)'; }, 250);
                setTimeout(function () { bar.style.transform = 'scaleX(0.85)'; }, 600);
            });
        });
    }

    function finishBar() {
        bar.style.transform = 'scaleX(1)';
        timer = setTimeout(function () {
            bar.style.opacity = '0';
            setTimeout(function () { bar.style.transform = 'scaleX(0)'; }, 350);
        }, 300);
    }

    /* Trigger bar on any internal link click */
    $(document).on('click', 'a[href]', function (e) {
        var href = this.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript') === 0 ||
                href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) { return; }
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) { return; }
        if (this.target === '_blank') { return; }
        startBar();
    });

    /* Complete bar when page is fully loaded */
    $(window).on('load', function () { finishBar(); });

    /* Also complete on DOM ready in case load already fired */
    $(document).ready(function () {
        setTimeout(finishBar, 100);
    });

}(jQuery));
}); // end DOMContentLoaded
</script>

<!-- Global toast notification utility (DaisyUI Toast) -->
<script>
/**
 * smToast(message, type, duration)
 * type: 'success' | 'error' | 'danger' | 'warning' | 'info'  (default: 'info')
 * duration: ms before auto-dismiss (default: 4000)
 *
 * Usage from any page script:
 *   smToast('Saved!', 'success');
 *   smToast('Something went wrong.', 'error');
 */
window.smToast = (function () {
    var container = document.getElementById('sm-toast-container');

    return function smToast(message, type, duration) {
        if (!container) { return; }
        type     = type     || 'info';
        duration = duration !== undefined ? duration : 4000;

        /* Map legacy 'danger' alias to DaisyUI 'error' */
        var alertType = type === 'danger' ? 'error' : type;

        var iconMap = {
            success: 'bi-check-circle-fill',
            error:   'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info:    'bi-info-circle-fill'
        };
        var icon = iconMap[alertType] || iconMap.info;

        var iconEl = document.createElement('i');
        iconEl.className = 'bi ' + icon;
        iconEl.setAttribute('aria-hidden', 'true');

        var msgEl = document.createElement('span');
        msgEl.textContent = message;

        var closeEl = document.createElement('button');
        closeEl.type = 'button';
        closeEl.className = 'btn btn-ghost btn-xs ml-auto';
        closeEl.setAttribute('aria-label', 'Close');
        var closeIcon = document.createElement('i');
        closeIcon.className = 'fa-solid fa-xmark';
        closeIcon.setAttribute('aria-hidden', 'true');
        closeEl.appendChild(closeIcon);

        var toast = document.createElement('div');
        toast.className = 'alert alert-' + alertType + ' sm-toast-item';
        toast.setAttribute('role', alertType === 'error' ? 'alert' : 'status');
        toast.setAttribute('aria-live', alertType === 'error' ? 'assertive' : 'polite');
        toast.appendChild(iconEl);
        toast.appendChild(msgEl);
        toast.appendChild(closeEl);

        container.appendChild(toast);

        /* Trigger enter animation */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.classList.add('sm-toast-show'); });
        });

        /* Close button */
        var dismissTimer = null;
        toast.querySelector('button').addEventListener('click', function () {
            dismiss(toast);
        }, { once: true });

        /* Auto-dismiss */
        if (duration > 0) {
            dismissTimer = setTimeout(function () {
                dismissTimer = null;
                dismiss(toast);
            }, duration);
        }

        function dismiss(el) {
            if (!el || el.dataset.smToastDismissing === '1') { return; }
            el.dataset.smToastDismissing = '1';
            if (dismissTimer !== null) {
                clearTimeout(dismissTimer);
                dismissTimer = null;
            }
            el.classList.add('sm-toast-hide');
            setTimeout(function () {
                if (el.parentNode) { el.parentNode.removeChild(el); }
            }, 350);
        }
    };
}());
</script>

<!-- Unsaved changes banner -->
<div id="sm-unsaved-banner" role="alert" aria-live="polite">
    <i class="fa-solid fa-triangle-exclamation flex-shrink-0" aria-hidden="true"></i>
    <span class="sm-unsaved-msg"><?php echo $this->lang->line('home')['unsaved_changes'] ?? 'You have unsaved changes.'; ?></span>
    <button class="sm-unsaved-discard" id="sm-unsaved-discard">
        <?php echo $this->lang->line('home')['unsaved_discard'] ?? 'Discard'; ?>
    </button>
</div>

<!-- Unsaved-changes guard -->
<script>
(function () {
    // ── Unsaved changes guard ────────────────────────────────────────────────
    var banner   = document.getElementById('sm-unsaved-banner');
    var discard  = document.getElementById('sm-unsaved-discard');
    var dirty    = false;

    /* Mark a form dirty on any input change; exclude login/search/navigation forms */
    document.addEventListener('change', function (e) {
        var form = e.target && e.target.closest ? e.target.closest('form') : null;
        if (!form) { return; }
        /* Skip forms explicitly opted-out */
        if (form.hasAttribute('data-no-dirty-watch')) { return; }
        /* Skip pure navigation/action forms (no text/select/textarea inputs) */
        var contentField = form.querySelector('input:not([type="hidden"]):not([type="submit"]):not([name="signin"]), textarea, select');
        if (!contentField) { return; }
        /* Skip the login form by name */
        if (form.getAttribute('name') === 'login_form') { return; }
        if (!dirty) {
            dirty = true;
            if (banner) { banner.classList.add('sm-unsaved-visible'); }
        }
    });

    /* On form submit: clear dirty flag so banner doesn't show */
    document.addEventListener('submit', function () {
        dirty = false;
        if (banner) { banner.classList.remove('sm-unsaved-visible'); }
    });

    /* Discard button clears dirty state */
    if (discard) {
        discard.addEventListener('click', function () {
            dirty = false;
            if (banner) { banner.classList.remove('sm-unsaved-visible'); }
        });
    }

    /* Warn when navigating away with unsaved changes */
    window.addEventListener('beforeunload', function (e) {
        if (dirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

}());
</script>

<!-- ── Global UX enhancements ──────────────────────────────────────────────
     1. Auto-dismiss success/info alerts after 5 s with a shrinking progress bar
     2. Form submit → disable button + show loading spinner (prevents double-submit)
     3. Smooth scroll-to-first-error for server-side validation messages
─────────────────────────────────────────────────────────────────────────── -->
<script>
(function () {

    /* ── 1. Auto-dismiss alerts ──────────────────────────────────────────── */
    var AUTO_DISMISS_MS = 6000;
    var FADE_MS         = 400;

    function autoDismissAlert(el) {
        // Inject a shrinking progress bar inside the alert
        var bar = document.createElement('div');
        bar.style.cssText = 'position:absolute;bottom:0;left:0;height:3px;width:100%;'
            + 'background:currentColor;opacity:0.35;transition:width ' + AUTO_DISMISS_MS + 'ms linear;border-radius:0 0 0.5rem 0.5rem;';
        el.style.position = 'relative';
        el.appendChild(bar);
        // Trigger bar shrink on next frame
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { bar.style.width = '0%'; });
        });
        setTimeout(function () {
            el.style.transition = 'opacity ' + FADE_MS + 'ms ease, max-height ' + FADE_MS + 'ms ease, margin ' + FADE_MS + 'ms ease, padding ' + FADE_MS + 'ms ease';
            el.style.overflow   = 'hidden';
            el.style.opacity    = '0';
            el.style.maxHeight  = el.offsetHeight + 'px';
            requestAnimationFrame(function () {
                el.style.maxHeight = '0';
                el.style.margin    = '0';
                el.style.padding   = '0';
            });
            setTimeout(function () { el.remove(); }, FADE_MS + 50);
        }, AUTO_DISMISS_MS);
    }

    // Target DaisyUI + Bootstrap alert variants that should auto-dismiss
    var AUTO_DISMISS_SELECTORS = [
        '.alert-success', '.alert-info',
        // Exclude errors, warnings so player can read them
    ];
    document.querySelectorAll(AUTO_DISMISS_SELECTORS.join(',')).forEach(function (el) {
        // Skip if it has a Bootstrap dismiss button (user controls it) or already has a timer
        if (el.querySelector('[data-bs-dismiss]') || el.dataset.noAutoDismiss) return;
        autoDismissAlert(el);
    });

    /* ── 2. Form submit → loading state ─────────────────────────────────── */
    // Exclude: search forms, filter forms (no-spinner), forms with data-no-spinner
    var SPINNER_SVG = '<svg class="animate-spin inline-block w-4 h-4 mr-1 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>';

    document.querySelectorAll('form:not([data-no-spinner])').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            // Preserve the clicked button's name/value before disabling it
            var submitter = e.submitter;
            if (submitter && submitter.name) {
                var existing = form.querySelector('input[type="hidden"][data-from-submitter]');
                if (existing) { existing.parentNode.removeChild(existing); }
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = submitter.name;
                hidden.value = submitter.value;
                hidden.setAttribute('data-from-submitter', '1');
                form.appendChild(hidden);
            }
            // Find the primary submit button in this form
            var btn = form.querySelector('[type="submit"].btn, button[type="submit"], input[type="submit"]');
            if (!btn || btn.dataset.spinnerActive) return;
            btn.dataset.spinnerActive = '1';
            btn.disabled = true;

            if (btn.tagName === 'INPUT') {
                btn.value = btn.getAttribute('data-loading-text') || 'Please wait…';
            } else {
                btn.dataset.origHtml = btn.innerHTML;
                btn.innerHTML = SPINNER_SVG + (btn.getAttribute('data-loading-text') || 'Please wait…');
            }
        });
    });

    /* ── 3. Scroll to first validation error on page load ───────────────── */
    var firstErr = document.querySelector('.alert-error, .text-error, .errorTxt:not(:empty)');
    if (firstErr) {
        setTimeout(function () {
            firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 150);
    }

}());
</script>

<!-- Cookie consent banner -->
<div id="sm-cookie-banner" role="dialog" aria-modal="true" aria-labelledby="sm-cookie-banner-title"
     aria-label="<?php echo ($this->session->userdata('site_lang') === 'french') ? 'Avis sur les cookies' : 'Cookie notice'; ?>">
    <div class="sm-cookie-banner-inner">
        <p class="sm-cookie-banner-title" id="sm-cookie-banner-title">
            🍪 <?php echo ($this->session->userdata('site_lang') === 'french') ? 'Utilisation des cookies' : 'Cookie Notice'; ?>
        </p>
        <p class="sm-cookie-banner-text">
            <?php if ($this->session->userdata('site_lang') === 'french'): ?>
                Des fournisseurs tiers, dont Google, utilisent des cookies pour diffuser des publicités personnalisées en fonction de vos visites sur ce site et sur d'autres sites. Nous utilisons également des cookies pour améliorer votre expérience et analyser le trafic.
                <a href="<?php echo base_url('cookies'); ?>" class="sm-cookie-banner-link">Politique de cookies</a> · <a href="<?php echo base_url('privacy'); ?>" class="sm-cookie-banner-link">Confidentialité</a>
            <?php else: ?>
                Third-party vendors, including Google, use cookies to serve personalised ads based on your prior visits to this and other websites. We also use cookies to enhance your experience and analyse traffic.
                <a href="<?php echo base_url('cookies'); ?>" class="sm-cookie-banner-link">Cookie Policy</a> · <a href="<?php echo base_url('privacy'); ?>" class="sm-cookie-banner-link">Privacy Policy</a>
            <?php endif; ?>
        </p>
        <div class="sm-cookie-banner-actions">
            <button id="sm-cookie-accept" class="sm-cookie-btn sm-cookie-btn-accept" type="button">
                <?php echo ($this->session->userdata('site_lang') === 'french') ? 'Accepter' : 'Accept'; ?>
            </button>
            <button id="sm-cookie-decline" class="sm-cookie-btn sm-cookie-btn-decline" type="button">
                <?php echo ($this->session->userdata('site_lang') === 'french') ? 'Refuser' : 'Decline'; ?>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var banner = document.getElementById('sm-cookie-banner');
    if (!banner) { return; }
    var choice;
    try { choice = localStorage.getItem('sm_cookie_choice'); } catch (e) { choice = null; }
    if (!choice) {
        banner.classList.add('sm-cookie-banner-visible');
    }
    document.getElementById('sm-cookie-accept').addEventListener('click', function () {
        try { localStorage.setItem('sm_cookie_choice', 'accepted'); } catch (e) {}
        banner.classList.remove('sm-cookie-banner-visible');
        // Update Google Consent Mode v2 to allow analytics and ad cookies
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'ad_storage':         'granted',
                'analytics_storage':  'granted',
                'ad_user_data':       'granted',
                'ad_personalization': 'granted'
            });
        }
        // Load Hotjar now that consent has been granted
        if (typeof _smLoadHotjar === 'function') {
            _smLoadHotjar();
        }
    });
    document.getElementById('sm-cookie-decline').addEventListener('click', function () {
        try { localStorage.setItem('sm_cookie_choice', 'declined'); } catch (e) {}
        banner.classList.remove('sm-cookie-banner-visible');
    });
}());

window.smReopenCookieBanner = function () {
    var b = document.getElementById('sm-cookie-banner');
    if (b) {
        try { localStorage.removeItem('sm_cookie_choice'); } catch (e) {}
        b.classList.add('sm-cookie-banner-visible');
    }
};
</script>

<!-- Social Bar -->
<script defer src="https://pl29335453.profitablecpmratenetwork.com/8b/15/b7/8b15b7777520cccd8631a36a6f49d717.js"></script>

</body>
</html>
