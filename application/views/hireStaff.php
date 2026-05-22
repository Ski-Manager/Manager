<div class="w-full">
    <?php
// General page title
echo $title;
echo $introHireStaff;

// Info Messages for specific actions
if (isset($infoMessage) && $infoMessage !== '') {
    $lang_msg = $this->lang->line('hireStaff');
    if (isset($lang_msg[$infoMessage]))
        echo '<p>'.$lang_msg[$infoMessage].'</p>';
}
?>

    <!-- Staff Listing from game_staff: DaisyUI radio-based tabs, one per position type -->
    <div class="tabs tabs-bordered mb-3" id="hireStaffTabs">
        <?php foreach ($staff_positions as $i => $pos):
            $staff_list = $staff_by_position[$pos] ?? [];
            $label_key = $this->lang->line('hireStaff');
            $tab_label = isset($label_key[$pos]) ? $label_key[$pos] : htmlspecialchars($pos, ENT_QUOTES, 'UTF-8');
        ?>
        <label class="tab">
            <input type="radio" name="hire_staff_tabs" <?php echo ($i === 0) ? 'checked' : ''; ?> />
            <?php echo $tab_label; ?>
        </label>
        <div class="tab-content" id="tab-<?php echo htmlspecialchars($pos, ENT_QUOTES, 'UTF-8'); ?>">

            <?php if (empty($staff_list)): ?>
                <div class="alert alert-info"><?php echo $this->lang->line('hireStaff')['no_candidates']; ?></div>
            <?php else: ?>
            <div class="grid gap-3 mt-1">
                <?php foreach ($staff_list as $s): ?>
                <div class="col-span-12 md:col-span-6 xl:col-span-4">
                    <div class="card h-full shadow-sm">
                        <div class="card-header flex justify-between items-center">
                            <strong><?php echo htmlspecialchars($s->display_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span class="badge badge-neutral"><?php echo $s->efficiency; ?>% <?php echo $this->lang->line('common_staff')['efficiency']; ?></span>
                        </div>
                        <div class="card-body">
                            <ul class="list-none mb-0 small">
                                <li>💶 <strong><?php echo $this->lang->line('common_staff')['salary']; ?>:</strong> <?php echo number_format($s->salary); ?> €/<?php echo $this->lang->line('hireStaff')['per_month']; ?></li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo base_url(); ?>hire_staff_controller/hire_staff/<?php echo $s->id_staff; ?>/<?php echo $s->salary; ?>"
                               class="btn btn-success btn-sm">
                                <?php echo $this->lang->line('hireStaff')['hire']; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>
    <!-- END staff listing -->

</div>

 