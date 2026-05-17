 <?php
    $online     = (int)$this->session->userdata('online_players');
    $registered = (int)$this->session->userdata('registered_players');
    $lang_lf    = $this->lang->line('login_form');
?>
<div id="player_info" class="side_padding_10">
    <div class="divider my-1"></div>
    <div class="flex flex-col gap-1">
        <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_lf['online_players']; ?>">
            <span class="text-xs opacity-70">🟢</span>
            <span class="text-xs flex-1"><?php echo $lang_lf['online_players']; ?></span>
            <span class="badge badge-sm badge-success font-mono"><?php echo $online; ?></span>
        </div>
        <div class="icon_sidebar tooltip tooltip-bottom" data-tip="<?php echo $lang_lf['registered_players']; ?>">
            <span class="text-xs opacity-70">👥</span>
            <span class="text-xs flex-1"><?php echo $lang_lf['registered_players']; ?></span>
            <span class="badge badge-sm badge-neutral font-mono"><?php echo $registered; ?></span>
        </div>
    </div>
</div><!-- /#sidebar-online-players -->

