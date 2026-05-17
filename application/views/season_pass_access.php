<?php
// Show the Season Ski Passes tab when redirected back after a save attempt so
// users can see the confirmation or error message and their updated values.
// Also show it when navigating here via the Season Ski Passes navbar link.
$show_season_tab = (isset($sp_infoMessage) && $sp_infoMessage !== '')
                || (isset($sp_active_tab) && $sp_active_tab === 'season_pass');
?>
<div class="w-full">
    <!-- DaisyUI tab navigation: label-based so icons can be included in the tab button.
         Each <label class="tab"> wraps a hidden radio input; the adjacent .tab-content
         div is shown/hidden by DaisyUI's CSS-only adjacent-sibling selector. -->
    <div class="tabs tabs-bordered mb-3" id="seasonAccessTab">

        <!-- ===== Accessibility (Season Passes & Access) tab ===== -->
        <label class="tab" id="access-tab">
            <input type="radio" name="season_access_tabs"
                   <?php echo $show_season_tab ? '' : 'checked'; ?> />
            <i class="fa-solid fa-signs-post mr-1"></i><?php echo $this->lang->line('navbar')['accessibility']; ?>
        </label>
        <div class="tab-content" id="access-panel">
            <?php $this->load->view('buildingAccess'); ?>
        </div>

        <!-- ===== Season Ski Passes tab ===== -->
        <label class="tab" id="season-pass-tab">
            <input type="radio" name="season_access_tabs"
                   <?php echo $show_season_tab ? 'checked' : ''; ?> />
            <i class="fa-solid fa-id-card mr-1"></i><?php echo $this->lang->line('building')['season_pass_title']; ?>
        </label>
        <div class="tab-content" id="season-pass-panel">
            <?php
            // Pass sp_infoMessage as infoMessage to the season_pass sub-view via CI3 data array
            // (local variable assignment is not visible inside nested $this->load->view() calls)
            $this->load->view('season_pass', isset($sp_infoMessage) ? ['infoMessage' => $sp_infoMessage] : []);
            ?>
        </div>

    </div><!-- .tabs -->
</div>
