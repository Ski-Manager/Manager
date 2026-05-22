<!-- Sidebar -->
<aside id="sidebar-wrapper" aria-label="<?php echo $this->lang->line('home')['sidebar_nav']; ?>">
    <!-- Mobile-only header with close button -->
    <div class="sidebar-mobile-header" id="sidebar-mobile-header">
        <span class="sidebar-mobile-title">
            <i class="fa-solid fa-mountain-sun mr-2" aria-hidden="true"></i>Ski-Manager
        </span>
        <button id="sidebar-mobile-close" aria-label="Close menu" class="sidebar-close-btn">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <ul class="sidebar-nav">

        <li>
            <?php
            $is_logged_in = $this->session->userdata('is_logged_in');

            // Check if the user is logged in
            if (isset($is_logged_in) && $is_logged_in == true) {
                // User is logged in
                $this->view('includes/sidebar_player_info');
                $this->view('includes/sidebar_achievements');
            } else {
                // User is not logged in
                $this->view('loginForm');
            }
            ?>
        </li>

        <li><?php $this->view('includes/sidebar_online_players'); ?></li>

        </ul>
</aside>
<!-- /#sidebar-wrapper -->
