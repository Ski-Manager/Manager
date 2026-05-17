<div class="side_padding_10">
<?php
    $achievements_sidebar = $this->session->userdata('achievements');
    if (!is_array($achievements_sidebar)) $achievements_sidebar = [];
    $to_claim = (int)$this->session->userdata('achievements_to_claim');
    $lang_ach = $this->lang->line('achievements');
?>

    <!-- Header -->
    <a href="<?php echo base_url('achievements_controller'); ?>"
       class="flex items-center justify-between text-xs font-bold uppercase tracking-wide opacity-80 hover:opacity-100 mb-2 px-1">
        <span><?php echo $lang_ach['titleMain']; ?></span>
        <?php if ($to_claim > 0): ?>
            <span class="badge badge-warning badge-sm"><?php echo $to_claim; ?></span>
        <?php endif; ?>
    </a>

    <?php if (empty($achievements_sidebar)): ?>
        <p class="text-xs opacity-50 text-center px-1">&mdash;</p>
    <?php else: ?>
        <ul class="space-y-2">
        <?php foreach ($achievements_sidebar as $i => $ach):
            $pct       = min(100, max(0, (int)$ach['progress']));
            $bar_class = $pct >= 100 ? 'progress-success' : 'progress-info';
        ?>
            <li>
                <a href="<?php echo base_url('achievements_controller') . '#status-' . $ach['id_achievement']; ?>"
                   id="linkname-<?php echo $i; ?>"
                   class="text-xs font-semibold hover:underline block truncate">
                    <?php echo htmlspecialchars($ach['name'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <progress id="progress-bar-<?php echo $i; ?>"
                          class="progress <?php echo $bar_class; ?> w-full"
                          style="height:6px"
                          value="<?php echo $pct; ?>" max="100"
                          title="<?php echo $pct; ?>%"></progress>
                <div class="flex justify-between text-xs opacity-60 mt-0.5">
                    <span id="progress-<?php echo $i; ?>"><?php echo $ach['progress']; ?>%</span>
                    <span id="button-<?php echo $i; ?>"><?php echo $ach['button']; ?></span>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div><!-- /#sidebar-achievements -->

