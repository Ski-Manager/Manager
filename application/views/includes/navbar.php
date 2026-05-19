<div class="btn-toolbar toolbar_div w-full" role="toolbar" aria-label="<?php echo $this->lang->line('home')['main_nav']; ?>">

    <!-- Sidebar open button (mobile only — left side) -->
    <button class="btn btn-info md:hidden mr-1" id="menu-toggle"
            aria-label="Open sidebar menu" aria-expanded="false">
        <i class="fa-solid fa-grip-lines-vertical text-xl"></i>
    </button>

    <!-- Mobile hamburger toggle for nav links (right side) -->
    <button class="btn btn-info md:hidden ml-auto mr-1" id="navbar-mobile-toggle"
            aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navbar-collapsible">
        <i class="fa-solid fa-bars text-xl"></i>
    </button>

    <!-- Collapsible wrapper: all nav items live here -->
    <div id="navbar-collapsible" class="navbar-collapsible-items hidden md:flex flex-wrap gap-2 w-full w-md-auto">

    <!-- 1st button -->
    <div>
        <a href="<?php echo base_url() . 'home_controller'; ?>" class="btn btn-info" data-nav-section="home">
            <i class="fa-solid fa-house mr-1"></i><span class="side_padding">
                <?php echo $this->lang->line('navbar')['home']; ?>
            </span>
        </a>
    </div>

    <?php
    $logged_status = $this->session->userdata('is_logged_in');
    if (isset($logged_status) && $logged_status == true) {
        // Only for logged in users
    ?>

        <!-- 2nd button -->
        <div>
            <a href="<?php echo base_url() . 'resort_controller'; ?>" class="btn btn-info" data-nav-section="resort">
                <i class="fa-solid fa-snowflake mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['resort']; ?>
                </span>
            </a>
        </div>

        <!-- 3rd button -->
        <div class="dropdown">
            <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="buildings">
                <i class="fa-solid fa-city mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['buildings']; ?>
                </span>
            </button>
            <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['buildings']; ?>">
                <li>
                    <a href="<?php echo base_url() . 'resort_map_controller'; ?>">
                        <i class="fa-solid fa-mountain-sun mr-1"></i><?php echo $this->lang->line('navbar')['build_slopes'] ?? 'Build Slopes'; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'building_access_controller'; ?>">
                        <i class="fa-solid fa-signs-post mr-1"></i><?php echo $this->lang->line('navbar')['accessibility']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'trail_snowmaking_controller'; ?>">
                        <i class="fa-solid fa-snowflake mr-1"></i><?php echo $this->lang->line('navbar')['snowmaking']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'lift_tech_controller'; ?>">
                        <i class="fa-solid fa-gear mr-1"></i><?php echo $this->lang->line('navbar')['lift_tech']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'upgrades_controller'; ?>">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i><?php echo $this->lang->line('navbar')['upgrades']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'terrain_engineering_controller'; ?>">
                        <i class="fa-solid fa-bezier-curve mr-1"></i><?php echo $this->lang->line('navbar')['terrain_engineering']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'rd_controller'; ?>">
                        <i class="fa-solid fa-flask mr-1"></i><?php echo $this->lang->line('navbar')['rd']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'mountain_cam_controller'; ?>">
                        <i class="fa-solid fa-video mr-1"></i><?php echo $this->lang->line('navbar')['mountain_cams']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'night_skiing_controller'; ?>">
                        <i class="fa-solid fa-moon mr-1"></i><?php echo $this->lang->line('navbar')['nightSkiing']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'energy_controller'; ?>">
                        <i class="fa-solid fa-bolt mr-1"></i><?php echo $this->lang->line('navbar')['energy']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'lift_line_controller'; ?>">
                        <i class="fa-solid fa-users mr-1"></i><?php echo $this->lang->line('navbar')['liftLine']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'maintenance_controller'; ?>">
                        <i class="fa-solid fa-screwdriver-wrench mr-1"></i><?php echo $this->lang->line('navbar')['maint_depth']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'restaurant_controller'; ?>">
                        <i class="fa-solid fa-mug-hot mr-1"></i><?php echo $this->lang->line('navbar')['restaurants']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'hotel_controller'; ?>">
                        <i class="fa-regular fa-building mr-1"></i><?php echo $this->lang->line('navbar')['hotels']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'accommodation_controller'; ?>">
                        <i class="fa-solid fa-house-chimney-window mr-1"></i><?php echo $this->lang->line('navbar')['accommodation']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'rental_controller'; ?>">
                        <i class="fa-solid fa-bag-shopping mr-1"></i><?php echo $this->lang->line('navbar')['rentals']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'leisure_controller'; ?>">
                        <i class="fa-solid fa-gamepad mr-1"></i><?php echo $this->lang->line('navbar')['leisure']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'luxury_controller'; ?>">
                        <i class="fa-solid fa-gem mr-1"></i><?php echo $this->lang->line('navbar')['luxury']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'medical_controller'; ?>">
                        <i class="fa-solid fa-heart-pulse mr-1"></i><?php echo $this->lang->line('navbar')['medical']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'facilities_controller'; ?>">
                        <i class="fa-solid fa-hospital mr-1"></i><?php echo $this->lang->line('navbar')['facilities']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'event_venues_controller'; ?>">
                        <i class="fa-regular fa-calendar-days mr-1"></i><?php echo $this->lang->line('navbar')['event_venues']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'transportation_controller'; ?>">
                        <i class="fa-solid fa-bus mr-1"></i><?php echo $this->lang->line('navbar')['transportation']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'crowding_controller'; ?>">
                        <i class="fa-solid fa-users mr-1"></i><?php echo $this->lang->line('navbar')['crowding']; ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Revenue & Prestige button -->
        <div class="dropdown">
            <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="revenue_prestige">
                <i class="fa-solid fa-coins mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['revenue_prestige']; ?>
                </span>
            </button>
            <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['revenue_prestige']; ?>">
                <li>
                    <a  href="<?php echo base_url() . 'environment_controller'; ?>">
                        <i class="fa-solid fa-tree mr-1"></i><?php echo $this->lang->line('navbar')['environment']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'off_season_controller'; ?>">
                        <i class="fa-regular fa-sun mr-1"></i><?php echo $this->lang->line('navbar')['off_season']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'real_estate_controller'; ?>">
                        <i class="fa-solid fa-city mr-1"></i><?php echo $this->lang->line('navbar')['real_estate']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'sponsorship_controller'; ?>">
                        <i class="fa-solid fa-award mr-1"></i><?php echo $this->lang->line('navbar')['sponsorship']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'retail_controller'; ?>">
                        <i class="fa-solid fa-store mr-1"></i><?php echo $this->lang->line('navbar')['retail']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'scenic_lift_controller'; ?>">
                        <i class="fa-solid fa-camera mr-1"></i><?php echo $this->lang->line('navbar')['scenic_lift']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'celebrity_visit_controller'; ?>">
                        <i class="fa-regular fa-star mr-1"></i><?php echo $this->lang->line('navbar')['celebrity_visit']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'insurance_controller'; ?>">
                        <i class="fa-solid fa-shield-halved mr-1"></i><?php echo $this->lang->line('navbar')['insurance']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'government_controller'; ?>">
                        <i class="fa-solid fa-building-columns mr-1"></i><?php echo $this->lang->line('navbar')['government']; ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- 5th button -->
        <div class="dropdown">
            <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="management">
                <i class="fa-solid fa-arrow-trend-up mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['management']; ?>
                </span>
            </button>
            <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['management']; ?>">
                <li>
                    <a  href="<?php echo base_url() . 'finances_controller'; ?>">
                        <i class="fa-solid fa-euro-sign mr-1"></i><?php echo $this->lang->line('navbar')['finances']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'logs_controller'; ?>">
                        <i class="fa-solid fa-book mr-1"></i><?php echo $this->lang->line('navbar')['logs']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'weather_controller'; ?>">
                        <i class="fa-solid fa-cloud mr-1"></i><?php echo $this->lang->line('navbar')['weather']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'microclimate_controller'; ?>">
                        <i class="fa-solid fa-wind mr-1"></i><?php echo $this->lang->line('navbar')['microclimate']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'climate_change_controller'; ?>">
                        <i class="fa-solid fa-temperature-arrow-up mr-1"></i><?php echo $this->lang->line('navbar')['climate_change']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'snow_report_controller'; ?>">
                        <i class="fa-solid fa-snowflake mr-1"></i><?php echo $this->lang->line('navbar')['snow_report'] ?? 'Snow Report'; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'marketing_controller'; ?>">
                        <i class="fa-solid fa-bullhorn mr-1"></i><?php echo $this->lang->line('navbar')['marketing']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'guest_ai_controller'; ?>">
                        <i class="fa-solid fa-robot mr-1"></i><?php echo $this->lang->line('navbar')['guest_ai']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'visitor_needs_controller'; ?>">
                        <i class="fa-regular fa-face-smile mr-1"></i><?php echo $this->lang->line('navbar')['visitor_needs']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'ski_school_controller'; ?>">
                        <i class="fa-solid fa-chalkboard-user mr-1"></i><?php echo $this->lang->line('navbar')['ski_school'] ?? 'Ski School'; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'demand_curve_controller'; ?>">
                        <i class="fa-solid fa-arrow-trend-down mr-1"></i><?php echo $this->lang->line('navbar')['demand_curve']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'empire_controller'; ?>">
                        <i class="fa-solid fa-globe mr-1"></i><?php echo $this->lang->line('navbar')['empire']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'reporting_controller'; ?>">
                        <i class="fa-solid fa-chart-bar mr-1"></i><?php echo $this->lang->line('navbar')['analysis']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'data_dashboard_controller'; ?>">
                        <i class="fa-solid fa-gauge-high mr-1"></i><?php echo $this->lang->line('navbar')['data_dashboard']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'lift_network_controller'; ?>">
                        <i class="fa-solid fa-diagram-project mr-1"></i><?php echo $this->lang->line('navbar')['lift_network']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'tournaments_controller'; ?>">
                        <i class="fa-solid fa-trophy mr-1"></i><?php echo $this->lang->line('navbar')['tournaments']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'competitors_controller'; ?>">
                        <i class="fa-regular fa-flag mr-1"></i><?php echo $this->lang->line('navbar')['competitors']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'special_events_controller'; ?>">
                        <i class="fa-solid fa-calendar-day mr-1"></i><?php echo $this->lang->line('navbar')['special_events']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'guest_skill_controller'; ?>">
                        <i class="fa-solid fa-user-check mr-1"></i><?php echo $this->lang->line('navbar')['guest_skill']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'mountain_plan_controller'; ?>">
                        <i class="fa-regular fa-map mr-1"></i><?php echo $this->lang->line('navbar')['mountain_plan']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a  href="<?php echo base_url() . 'resort_map_controller'; ?>">
                        <i class="fa-solid fa-map mr-1"></i><?php echo $this->lang->line('navbar')['trail_map']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'ski_quiz_controller'; ?>">
                        <i class="fa-solid fa-circle-question mr-1"></i><?php echo $this->lang->line('navbar')['ski_quiz']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'vip_loyalty_controller'; ?>">
                        <i class="fa-regular fa-star mr-1"></i><?php echo $this->lang->line('navbar')['vip_loyalty']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'emergency_controller'; ?>">
                        <i class="fa-solid fa-hospital mr-1"></i><?php echo $this->lang->line('navbar')['emergency']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'crisis_controller'; ?>">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i><?php echo $this->lang->line('navbar')['crisis_events']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'season_pass_controller'; ?>">
                        <i class="fa-solid fa-ticket mr-1"></i><?php echo $this->lang->line('navbar')['season_pass']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sm-divider"><hr class="divider"></li>
                <li>
                    <a  href="<?php echo base_url() . 'overview_staff_controller'; ?>">
                        <i class="fa-solid fa-users mr-1"></i><?php echo $this->lang->line('navbar')['overview_staff']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'hire_staff_controller'; ?>">
                        <i class="fa-solid fa-user-plus mr-1"></i><?php echo $this->lang->line('navbar')['hire']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'groomer_controller'; ?>">
                        <i class="fa-solid fa-truck mr-1"></i><?php echo $this->lang->line('navbar')['groomers']; ?>
                    </a>
                </li>
                <?php if (!is_easy_mode()): ?>
                <li>
                    <a  href="<?php echo base_url() . 'skibus_controller'; ?>">
                        <i class="fa-solid fa-bus mr-1"></i><?php echo $this->lang->line('navbar')['skibuses']; ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- 6th button (logged-in account menu) -->
        <div class="dropdown">
            <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="account">
                <i class="fa-solid fa-user-circle mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['account']; ?>
                </span>
            </button>
            <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['account']; ?>">
                <li>
                    <a  href="<?php echo base_url() . 'leaderboard'; ?>">
                        <i class="fa-solid fa-trophy mr-1"></i><?php echo $this->lang->line('navbar')['leaderboard']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'minigames_controller'; ?>">
                        <i class="fa-solid fa-gamepad mr-1"></i><?php echo $this->lang->line('navbar')['minigames']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'achievements_controller'; ?>">
                        <i class="fa-solid fa-award mr-1"></i><?php echo $this->lang->line('navbar')['achievements']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'seasonal_objectives_controller'; ?>">
                        <i class="fa-solid fa-list-check mr-1"></i><?php echo $this->lang->line('navbar')['seasonal_objectives']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'daily_bonus_controller'; ?>">
                        <i class="fa-solid fa-fire mr-1"></i><?php echo $this->lang->line('navbar')['daily_bonus']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'account_controller'; ?>">
                        <i class="fa-solid fa-gear mr-1"></i><?php echo $this->lang->line('navbar')['account_options']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'genepis_controller'; ?>">
                        <i class="fa-solid fa-seedling mr-1"></i><?php echo $this->lang->line('navbar')['genepis']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'help_controller'; ?>">
                        <i class="fa-regular fa-circle-question mr-1"></i><?php echo $this->lang->line('navbar')['help']; ?>
                    </a>
                </li>
                <li class="sm-divider"><hr class="divider"></li>
                <li>
                    <a class="text-error" href="<?php echo base_url() . 'login_controller/logout'; ?>">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i><?php echo $this->lang->line('login_form')['login_logout'] ?? 'Logout'; ?>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Search box (logged-in users) -->
        <div id="nav-search-wrapper" role="search" aria-label="Page search">
            <div id="nav-search-input-group">
                <i class="fa-solid fa-magnifying-glass" id="nav-search-icon" aria-hidden="true"></i>
                <input type="text"
                       id="nav-search-input"
                       autocomplete="off"
                       spellcheck="false"
                       placeholder="<?php echo htmlspecialchars($this->lang->line('navbar')['search_placeholder'] ?? 'Search pages…', ENT_QUOTES, 'UTF-8'); ?>"
                       aria-label="<?php echo htmlspecialchars($this->lang->line('navbar')['search_label'] ?? 'Search', ENT_QUOTES, 'UTF-8'); ?>"
                       aria-autocomplete="list"
                       aria-controls="nav-search-results"
                       aria-expanded="false">
                <kbd id="nav-search-shortcut-hint" aria-hidden="true" title="Press / to search">/</kbd>
                <button type="button" id="nav-search-clear" aria-label="<?php echo htmlspecialchars($this->lang->line('navbar')['search_clear'] ?? 'Clear search', ENT_QUOTES, 'UTF-8'); ?>">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <ul id="nav-search-results" role="listbox" aria-label="Search results"></ul>
        </div>

    <?php } ?>

    <?php
    // Only for non-logged in users
    $logged_status = $this->session->userdata('is_logged_in');
    if (!isset($logged_status) || $logged_status != true) {
    ?>
        <!-- 6th button (signup for guests) -->
        <div>
            <a href="<?php echo base_url() . 'register_controller'; ?>" class="btn btn-info" data-nav-section="signup">
                <i class="fa-solid fa-user-plus mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['signup']; ?>
                </span>
            </a>
        </div>

        <!-- Mobile-only inline login form (sidebar login is not accessible on small screens) -->
        <div class="block md:hidden w-full border-t border-info pt-2 mt-1">
            <?php echo form_open('login_controller/checkLogin'); ?>
            <?php echo form_hidden('signin', 'signin'); ?>
            <label class="input input-sm mb-1 flex items-center gap-2">
                <i class="fa-regular fa-user text-info" aria-hidden="true"></i>
                <input type="text" name="login_username" class="grow"
                       placeholder="<?php echo htmlspecialchars($this->lang->line('home')['username'] ?? 'Username', ENT_QUOTES, 'UTF-8'); ?>"
                       aria-label="<?php echo htmlspecialchars($this->lang->line('home')['username'] ?? 'Username', ENT_QUOTES, 'UTF-8'); ?>"
                       autocomplete="username" required>
            </label>
            <label class="input input-sm mb-1 flex items-center gap-2">
                <i class="fa-solid fa-lock text-info" aria-hidden="true"></i>
                <input type="password" name="login_password" class="grow"
                       placeholder="<?php echo htmlspecialchars($this->lang->line('home')['password'] ?? 'Password', ENT_QUOTES, 'UTF-8'); ?>"
                       aria-label="<?php echo htmlspecialchars($this->lang->line('home')['password'] ?? 'Password', ENT_QUOTES, 'UTF-8'); ?>"
                       autocomplete="current-password" required>
            </label>
            <button type="submit" class="btn btn-primary btn-sm w-full">
                <i class="fa-solid fa-right-to-bracket mr-1"></i>
                <?php echo $this->lang->line('login_form')['login_text'] ?? 'Login'; ?>
            </button>
            <?php echo form_close(); ?>
        </div>
    <?php } ?>

    <!-- Theme toggle -->
    <label class="swap swap-rotate btn btn-ghost btn-circle" aria-label="Toggle light/dark theme">
        <input type="checkbox" id="theme-toggle" />
        <!-- sun icon – indicates light mode (shown when dark mode is active) -->
        <svg class="swap-on h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/>
        </svg>
        <!-- moon icon – indicates dark mode (shown when light mode is active) -->
        <svg class="swap-off h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
        </svg>
    </label>

    <!-- Language selector -->
    <?php $this->load->view('includes/lang_dropdown'); ?>

    <!-- More dropdown (Contact & Feature Suggestion) -->
    <div class="dropdown">
        <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="more">
            <i class="fa-solid fa-ellipsis mr-1"></i><span class="side_padding">
                <?php echo $this->lang->line('navbar')['more']; ?>
            </span>
        </button>
        <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['more']; ?>">
            <li>
                <a  href="<?php echo base_url('guide'); ?>">
                    <i class="fa-solid fa-book-open mr-1"></i><?php echo $this->lang->line('navbar')['guide'] ?? 'Game Guide'; ?>
                </a>
            </li>
            <li>
                <a  href="<?php echo base_url('about'); ?>">
                    <i class="fa-solid fa-circle-info mr-1"></i><?php echo $this->lang->line('navbar')['about'] ?? 'About'; ?>
                </a>
            </li>
            <li>
                <a  href="<?php echo base_url('contact'); ?>">
                    <i class="fa-regular fa-envelope mr-1"></i><?php echo $this->lang->line('navbar')['contact']; ?>
                </a>
            </li>
            <li>
                <a  href="<?php echo base_url() . 'blogs_controller'; ?>">
                    <i class="fa-solid fa-newspaper mr-1"></i><?php echo $this->lang->line('navbar')['blog'] ?? 'Blog &amp; News'; ?>
                </a>
            </li>
            <li>
                <a  href="<?php echo base_url() . 'feature_suggestion_controller'; ?>">
                    <i class="fa-solid fa-lightbulb mr-1"></i><?php echo $this->lang->line('navbar')['feature_suggestion']; ?>
                </a>
            </li>
            <li>
                <a  href="https://github.com/Ski-Manager-net/Manager" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-screwdriver-wrench mr-1"></i><?php echo $this->lang->line('navbar')['help_build'] ?? 'Help Build Ski-Manager'; ?>
                </a>
            </li>
            <?php if (isset($logged_status) && $logged_status == true): ?>
            <li>
                <a  href="<?php echo base_url() . 'chat_controller'; ?>">
                    <i class="fa-regular fa-comment-dots mr-1"></i><?php echo $this->lang->line('navbar')['chat_inbox'] ?? 'Messages'; ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- ADMIN BUTTONS -->
    <?php if ($this->session->userdata('is_admin') == '1') { ?>
        <!-- 8th button -->
        <div class="dropdown">
            <button class="btn btn-info dropdown-trigger" type="button" tabindex="0" aria-expanded="false" aria-haspopup="true" data-nav-section="admin">
                <i class="fa-solid fa-shield-halved mr-1"></i><span class="side_padding">
                    <?php echo $this->lang->line('navbar')['admin']; ?>
                </span>
            </button>
            <ul class="dropdown-content menu bg-base-100 rounded-box shadow z-50 min-w-52" aria-label="<?php echo $this->lang->line('navbar')['admin']; ?>">
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_player_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_player_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_resort_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_resort_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_stats_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_stats_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_maintenance_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_maintenance_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_building_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_building_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_equipment_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_equipment_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_lift_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_lift_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_slope_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_slope_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_staff_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_staff_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_location_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_location_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_news_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_news_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_queries_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_queries_controller']; ?>
                    </a>
                </li>
                <li>
                    <a  href="<?php echo base_url() . 'admin/admin_chat_controller'; ?>">
                        <?php echo $this->lang->line('navbar')['admin_chat_controller']; ?>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>

    </div><!-- /#navbar-collapsible -->

</div> <!-- .btn-toolbar -->

<script>
(function () {
    /* ── Navbar dropdown manager ──────────────────────────────────────────
       Directly sets inline style.display on .dropdown-content so no CSS
       class specificity battle is needed. Inline styles always win.
    ── */
    function closeAll(exceptContent) {
        document.querySelectorAll('.sm-navbar .dropdown-content, .navbar-collapsible-items .dropdown-content').forEach(function (c) {
            if (c === exceptContent) return;
            c.style.display = '';
            c.classList.remove('sm-2col');
            var btn = c.closest('.dropdown') && c.closest('.dropdown').querySelector('.dropdown-trigger');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    function adjustOverflow(dropdown, content) {
        content.classList.remove('sm-2col');
        var btn = dropdown.querySelector('.dropdown-trigger');
        if (!btn) return;
        var btnRect = btn.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight;
        if (content.scrollHeight > vh - btnRect.bottom - 8) {
            content.classList.add('sm-2col');
        }
    }

    document.querySelectorAll('.dropdown-trigger').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var dropdown = btn.closest('.dropdown');
            if (!dropdown) return;
            var content = dropdown.querySelector('.dropdown-content');
            if (!content) return;
            var isOpen = content.style.display === 'block';
            closeAll(null);
            if (!isOpen) {
                content.style.display = 'block';
                btn.setAttribute('aria-expanded', 'true');
                adjustOverflow(dropdown, content);
            } else {
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) closeAll(null);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll(null);
    });
})();
</script>
