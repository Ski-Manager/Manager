<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['transport_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['transport_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'transport_settings_saved',
        'transport_invalid_settings',
        'transport_save_error',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}
?>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['transport_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['transport_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['transport_mechanic_shuttle']; ?></li>
                <li><?php echo $this->lang->line('building')['transport_mechanic_ski_storage']; ?></li>
                <li><?php echo $this->lang->line('building')['transport_mechanic_gondola']; ?></li>
                <li><?php echo $this->lang->line('building')['transport_mechanic_visitors']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:580px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['transport_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('transportation_controller/save'); ?>">
                <input type="hidden" name="transportation_form" value="1">

                <!-- Shuttle level -->
                <div class="mb-3">
                    <label for="shuttle_level" class="label">
                        <?php echo $this->lang->line('building')['transport_shuttle_label']; ?>
                        <small class="text-base-content/60">(0–<?php echo $shuttle_max_level; ?>)</small>
                    </label>
                    <select id="shuttle_level" name="shuttle_level" class="select select-sm" style="max-width:280px;">
                        <?php for ($lvl = 0; $lvl <= $shuttle_max_level; $lvl++): ?>
                            <option value="<?php echo $lvl; ?>" <?php echo ($shuttle_level == $lvl) ? 'selected' : ''; ?>>
                                <?php echo $this->lang->line('building')['transport_shuttle_level_' . $lvl]; ?>
                                (<?php echo $shuttle_daily_costs[$lvl]; ?> €/<?php echo $this->lang->line('building')['transport_per_day']; ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['transport_shuttle_help']; ?></div>
                </div>

                <!-- Ski storage toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="ski_storage" name="ski_storage" value="1"
                               <?php echo ($ski_storage == 1) ? 'checked' : ''; ?>>
                        <label for="ski_storage">
                            <?php echo $this->lang->line('building')['transport_ski_storage_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60">
                        <?php echo $this->lang->line('building')['transport_ski_storage_help']; ?>
                        (<?php echo $ski_storage_daily_cost; ?> €/<?php echo $this->lang->line('building')['transport_per_day']; ?>
                        &mdash; +<?php echo $ski_storage_family_rep; ?> <?php echo $this->lang->line('building')['transport_rep_families']; ?>)
                    </div>
                </div>

                <!-- Gondola link toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="gondola_link" name="gondola_link" value="1"
                               <?php echo ($gondola_link == 1) ? 'checked' : ''; ?>>
                        <label for="gondola_link">
                            <?php echo $this->lang->line('building')['transport_gondola_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60">
                        <?php echo $this->lang->line('building')['transport_gondola_help']; ?>
                        (<?php echo $gondola_daily_cost; ?> €/<?php echo $this->lang->line('building')['transport_per_day']; ?>
                        &mdash; +<?php echo $gondola_family_rep; ?> <?php echo $this->lang->line('building')['transport_rep_families']; ?>,
                        +<?php echo $gondola_pro_rep; ?> <?php echo $this->lang->line('building')['transport_rep_pros']; ?>)
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['transport_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Current status ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:580px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['transport_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:540px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_shuttle_status']; ?></td>
                    <td><strong><?php echo $this->lang->line('building')['transport_shuttle_level_' . $shuttle_level]; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_shuttle_daily_cost']; ?></td>
                    <td><strong><?php echo $shuttle_daily_costs[$shuttle_level]; ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_ski_storage_status']; ?></td>
                    <td>
                        <?php if ($ski_storage == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['transport_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['transport_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_gondola_status']; ?></td>
                    <td>
                        <?php if ($gondola_link == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['transport_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['transport_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_visitor_bonus_label']; ?></td>
                    <td><strong>+<?php echo $visitor_bonus_pct * $shuttle_level; ?>%</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['transport_nightly_rep_label']; ?></td>
                    <td>
                        <?php
                        $rep_families = $shuttle_family_rep[$shuttle_level]
                            + ($ski_storage == 1 ? $ski_storage_family_rep : 0)
                            + ($gondola_link == 1 ? $gondola_family_rep : 0);
                        $rep_pros     = $shuttle_pro_rep[$shuttle_level]
                            + ($gondola_link == 1 ? $gondola_pro_rep : 0);
                        echo '+' . $rep_families . ' ' . $this->lang->line('building')['transport_rep_families']
                           . ' / +' . $rep_pros . ' ' . $this->lang->line('building')['transport_rep_pros'];
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</div>

<style>
.container-border {
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 20px;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.padding_top_bot_15 {
    padding-top: 15px;
    padding-bottom: 15px;
}
</style>
