    <div class="side_padding_10 sidebar-player-panel">
<?php
    $resort_status_block = get_resort_status_block();
    $username = htmlspecialchars($this->session->userdata('login_username'), ENT_QUOTES, 'UTF-8');
    $lang_lf  = $this->lang->line('login_form');
    $lang_h   = $this->lang->line('home');

    $display_snow_level = max(0, (int)$this->session->userdata('snow_level'));
    $day_of_season_val  = (int)$this->session->userdata('day_of_season');
    $day_of_season_pct  = min(100, round($day_of_season_val / 135 * 100));
?>

    <!-- Username greeting -->
    <div class="text-center text-sm font-semibold truncate px-1 mb-1">
        <?php echo $lang_lf['login_welcome'] . ' '; ?>
        <span class="text-info"><?php echo $username; ?></span>
    </div>

    <!-- Tourist info / countdown -->
    <?php if (isset($pre_touristInfoBuildingStatus)) echo $pre_touristInfoBuildingStatus; ?>
    <div class="text-center text-xs opacity-70" data-countdown="<?php echo $resort_status_block['touristInfoBuildingStatus']; ?>">
        <?php
        if (isset($resort_status_block['status_sidebar_construction']))
            echo $resort_status_block['status_sidebar_construction'];
        else
            echo $resort_status_block['tourist_info_status_to_show'];
        ?>
    </div>
    <?php if (isset($post_touristInfoBuildingStatus)) echo $post_touristInfoBuildingStatus; ?>

    <div class="divider my-1"></div>

    <!-- Stat rows -->
    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_lf['login_cash']; ?>">
        <i class="fa-solid fa-money-bill-1-wave"></i>
        <span class="font-mono text-xs"><span id="cash_div"><?php echo number_format($this->session->userdata('cash'), 0, ',', ' '); ?></span> €</span>
    </div>

    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_lf['login_snow_level']; ?>">
        <img src="<?php echo base_url('img/icons/snow_level.png'); ?>" alt="" aria-hidden="true" width="16" height="16">
        <span class="font-mono text-xs"><span id="snow_level_div"><?php echo $display_snow_level; ?></span> cm</span>
    </div>

    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_h['reputation']; ?>">
        <img src="<?php echo base_url('img/icons/reputation.png'); ?>" alt="" aria-hidden="true" width="16" height="16">
        <span class="font-mono text-xs"><span id="reputation_div"><?php echo number_format($this->session->userdata('reputation'), 0, ',', ' '); ?></span></span>
    </div>

    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_h['prestige']; ?>">
        <img src="<?php echo base_url('img/icons/prestige.png'); ?>" alt="" aria-hidden="true" width="16" height="16">
        <span class="font-mono text-xs"><span id="prestige_div"><?php echo number_format($this->session->userdata('prestige'), 0, ',', ' '); ?></span></span>
    </div>

    <!-- Day of season with DaisyUI progress -->
    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_h['day_of_season']; ?>">
        <i class="fa-solid fa-calendar"></i>
        <span class="text-xs min-w-0 flex-1">
            <?php echo $day_of_season_val; ?>/135 &mdash; <?php echo $lang_lf['login_season'] . number_format($this->session->userdata('season'), 0, ',', ' '); ?>
            <progress class="progress progress-info w-full mt-0.5" style="height:4px"
                value="<?php echo $day_of_season_pct; ?>" max="100"
                title="<?php echo $day_of_season_pct; ?>%"></progress>
        </span>
    </div>

    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_lf['alluence_yesterday']; ?>">
        <i class="fa-solid fa-people-group"></i>
        <span class="font-mono text-xs"><?php echo number_format($this->session->userdata('affluence'), 0, ',', ' '); ?></span>
    </div>

    <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_h['genepis_title']; ?>">
        <img src="<?php echo base_url('img/icons/genepis.png'); ?>" alt="" aria-hidden="true" width="16" height="16">
        <span class="font-mono text-xs"><span id="genepis_div"><?php echo number_format($this->session->userdata('genepis'), 0, ',', ' '); ?></span></span>
    </div>

    <div class="divider my-1"></div>

    <!-- Logout -->
    <div class="text-center">
        <?php echo form_open('login_controller/checkLogin'); ?>
        <a href="<?php echo base_url('login_controller/logout'); ?>" class="btn btn-xs btn-outline btn-error w-full">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="side_padding"><?php echo $lang_lf['login_logout']; ?></span>
        </a>
        <?php echo form_close(); ?>
    </div>

</div><!-- /#sidebar-player-panel -->
