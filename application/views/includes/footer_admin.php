

<div class="wrapper">
    <div class="w-full">
                        <div class="text-center py-2">
                            <?php echo $this->lang->line('home')['copyright']; ?>
                            &mdash;
                            <a href="<?php echo base_url('about'); ?>"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'À propos' : 'About'; ?></a>
                            &middot;
                            <a href="<?php echo base_url('contact'); ?>"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Contact' : 'Contact'; ?></a>
                            &middot;
                            <a href="<?php echo base_url('privacy'); ?>"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Politique de Confidentialité' : 'Privacy Policy'; ?></a>
                            &middot;
                            <a href="<?php echo base_url('cookies'); ?>"><?php echo ($this->session->userdata('site_lang') === 'french') ? 'Politique de Cookies' : 'Cookie Policy'; ?></a>
                        </div>
        </div>
</div>

<!-- Smartlink -->
<div class="text-center py-1">
    <a href="https://www.profitablecpmratenetwork.com/pb8atatzsi?key=0ffcac281ea74e89e0049db7801944f8" target="_blank" rel="noopener noreferrer">Ski-Manager</a>
</div>

<!-- jQuery 3.7.1 from CDN – deferred to remove from critical request chain.
     onerror loads the local fallback (also deferred) if the CDN is unreachable. -->
<script defer src="https://code.jquery.com/jquery-3.7.1.min.js"
        crossorigin="anonymous"
        onerror="(function(){var s=document.createElement('script');s.defer=true;s.src='<?php echo base_url(); ?>js/jquery.min.js';document.head.appendChild(s);}())"></script>

<!-- Google Charts loader -->
<script defer src="https://www.gstatic.com/charts/loader.js"></script>
  <!-- jQuery UI 1.13.3 from CDN – replaces local unminified 504 KB jquery-ui.js -->
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"
          crossorigin="anonymous"></script>




<script type="text/javascript">
 var Settings = {
    base_url: '<?php echo base_url(); ?>',
    item_deleted: '<?php echo $this->lang->line('admin_page')['item_deleted']; ?>',
    item_duplicated: '<?php echo $this->lang->line('admin_page')['item_duplicated']; ?>',
    item_activated: '<?php echo $this->lang->line('admin_page')['item_activated']; ?>',
    delete: '<?php echo $this->lang->line('admin_page')['delete']; ?>',
    cancel: '<?php echo $this->lang->line('home')['cancel']; ?>',
    days: "<?php echo $this->lang->line('home')['days']; ?>",
    something_went_wrong: "<?php echo $this->lang->line('home')['something_went_wrong']; ?>"
  }
</script>



<!-- Legacy interaction shim for old modal/collapse markup while views move to DaisyUI -->
<script src="<?php echo base_url();?>js/sm-bootstrap-shim.js" type="text/javascript"></script>

<!-- DataTables must load before home_admin.js which calls .DataTable() at initialisation -->
<script defer src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script defer src="https://cdn.datatables.net/2.3.7/js/dataTables.dataTables.min.js"></script>

<!-- For general javascript functions -->
<script defer src="<?php echo base_url();?>js/home_admin.js"></script>
  
<script>
document.addEventListener('DOMContentLoaded', function _smAdminMenuInit() {
    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', _smAdminMenuInit, { once: true });
        return;
    }
    var menuBtn = document.getElementById('menu-toggle');
    var icon = menuBtn ? menuBtn.querySelector('.sidebar-toggle-icon') : null;

    function syncAdminSidebarIcon() {
        if (!icon) { return; }
        var isToggled = menuBtn.classList.contains('toggle_open');
        icon.className = 'bi sidebar-toggle-icon ' +
            (isToggled ? 'bi-layout-sidebar' : 'bi-layout-sidebar-reverse');
    }

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $("#menu-toggle").toggleClass("toggle_open");
        syncAdminSidebarIcon();
    });

    syncAdminSidebarIcon();
});
    </script>

<!-- Toast notification container (DaisyUI toast) -->
<div id="sm-toast-container" class="toast toast-top toast-end sm-toast-container" aria-live="polite" aria-atomic="true"></div>

<!-- Global toast notification utility (DaisyUI Toast) -->
<script>
/**
 * smToast(message, type, duration)
 * type: 'success' | 'error' | 'danger' | 'warning' | 'info'  (default: 'info')
 * duration: ms before auto-dismiss (default: 4000)
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

        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.classList.add('sm-toast-show'); });
        });

        var dismissTimer = null;
        toast.querySelector('button').addEventListener('click', function () {
            dismiss(toast);
        }, { once: true });

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
 
<!-- Social Bar -->
<script defer src="https://pl29335453.profitablecpmratenetwork.com/8b/15/b7/8b15b7777520cccd8631a36a6f49d717.js"></script>

    </body>
</html>
         
