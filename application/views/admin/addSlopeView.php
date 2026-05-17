<div class="w-full">
<legend><?php echo $title; ?></legend>

<?php echo $this->session->flashdata('msg'); ?>

<div class="w-full container-border padding_top_bot_15">
    <div class="w-full padding_top_bot_15">
        <?php
        $attributes = array("class" => "", "name" => "add_new_slope");
        echo form_open("admin/admin_slope_controller/add_new_slope_validation", $attributes);
        ?>
        <fieldset>

            <!-- ── Section 1: Identification ── -->
            <div class="form-group group_form">
                <div class="md:col-span-2">
                    <label for="id_sector" class="label"><strong><?php echo $this->lang->line('admin_page')['id_sector']; ?></strong></label>
                    <input name="id_sector" id="id_sector" type="number" min="1"
                           value="<?php echo set_value('id_sector', $max_sector); ?>"
                           class="input input-sm" style="width:60px;" />
                    <?php if (isset($add_slope_error_id_sector) && $add_slope_error_id_sector != '') echo '<br><span class="text-error">'.$add_slope_error_id_sector.'</span>'; ?>
                </div>
                <div class="md:col-span-4">
                    <label for="name_english" class="label"><strong><?php echo $this->lang->line('admin_page')['name_english']; ?></strong></label>
                    <input name="name_english" id="name_english" type="text" maxlength="45"
                           value="<?php echo set_value('name_english', $slope_id_english); ?>"
                           class="input input-sm w-full" />
                    <?php if (isset($add_slope_error_name_english) && $add_slope_error_name_english != '') echo '<br><span class="text-error">'.$add_slope_error_name_english.'</span>'; ?>
                </div>
                <div class="md:col-span-4">
                    <label for="name_french" class="label"><strong><?php echo $this->lang->line('admin_page')['name_french']; ?></strong></label>
                    <input name="name_french" id="name_french" type="text" maxlength="45"
                           value="<?php echo set_value('name_french', $slope_id_french); ?>"
                           class="input input-sm w-full" />
                    <?php if (isset($add_slope_error_name_french) && $add_slope_error_name_french != '') echo '<br><span class="text-error">'.$add_slope_error_name_french.'</span>'; ?>
                </div>
                <div class="md:col-span-2 mb-3">
                    <label for="slope_type" class="label"><strong><?php echo $this->lang->line('home')['slope_type']; ?></strong></label>
                    <select id="slope_type" name="slope_type" class="select select-sm w-full">
                        <?php echo $slope_type_select; ?>
                    </select>
                    <?php if (isset($add_slope_error_slope_type) && $add_slope_error_slope_type != '') echo '<br><span class="text-error">'.$add_slope_error_slope_type.'</span>'; ?>
                </div>
            </div>

            <!-- ── Section 2: Technical details ── -->
            <div class="form-group group_form">
                <div class="md:col-span-2">
                    <label for="length" class="label"><strong><?php echo $this->lang->line('slope')['length']; ?></strong></label>
                    <input name="length" id="length" type="number" min="1"
                           value="<?php echo set_value('length'); ?>"
                           class="input input-sm" style="width:90px;" />
                    <?php echo $this->lang->line('slope')['length_unit']; ?>
                    <?php if (isset($add_slope_error_length) && $add_slope_error_length != '') echo '<br><span class="text-error">'.$add_slope_error_length.'</span>'; ?>
                </div>
                <div class="md:col-span-2">
                    <label for="reputation" class="label"><strong><?php echo $this->lang->line('home')['reputation']; ?></strong></label>
                    <input name="reputation" id="reputation" type="number" min="0"
                           value="<?php echo set_value('reputation'); ?>"
                           class="input input-sm" style="width:90px;" />
                    <?php if (isset($add_slope_error_reputation) && $add_slope_error_reputation != '') echo '<br><span class="text-error">'.$add_slope_error_reputation.'</span>'; ?>
                </div>
                <div class="md:col-span-3 mb-3">
                    <label for="start_location" class="label"><strong><?php echo $this->lang->line('home')['start_location']; ?></strong></label>
                    <select id="start_location" name="start_location" class="select select-sm w-full">
                        <?php echo $start_location_select; ?>
                    </select>
                    <?php if (isset($add_slope_error_start_location) && $add_slope_error_start_location != '') echo '<br><span class="text-error">'.$add_slope_error_start_location.'</span>'; ?>
                </div>
                <div class="md:col-span-3 mb-3">
                    <label for="end_location" class="label"><strong><?php echo $this->lang->line('home')['end_location']; ?></strong></label>
                    <select id="end_location" name="end_location" class="select select-sm w-full">
                        <?php echo $end_location_select; ?>
                    </select>
                    <?php if (isset($add_slope_error_end_location) && $add_slope_error_end_location != '') echo '<br><span class="text-error">'.$add_slope_error_end_location.'</span>'; ?>
                </div>
            </div>

            <!-- ── Section 3: Path ── -->
            <div class="form-group group_form">
                <div class="md:col-span-12">
                    <label for="path" class="label">
                        <strong><?php echo $this->lang->line('home')['path']; ?></strong>
                        <small class="text-base-content/60">
                            – format: <code>[x,y],[x,y],[x,y]…</code>&nbsp;
                            | <span id="coord_count">0</span> point(s)
                        </small>
                    </label>
                    <textarea name="path" id="path" rows="6" class="textarea w-full"
                              placeholder="e.g.  [100,200],[150,250],[200,300]"
                              oninput="updateCoordCount(this)"><?php echo set_value('path'); ?></textarea>
                    <?php if (isset($add_slope_error_path) && $add_slope_error_path != '') echo '<span class="text-error">'.$add_slope_error_path.'</span>'; ?>
                </div>
            </div>

            <!-- ── Buttons ── -->
            <div class="md:col-span-12 padding_top_bot_15" style="display:flex; justify-content:space-between;">
                <a href="<?php echo base_url('admin/admin_slope_controller'); ?>">
                    <button type="button" class="btn btn-primary">Back</button>
                </a>
                <?php echo form_hidden('add_new_slope', 'add_new_slope'); ?>
                <input name="submit" type="submit" class="btn btn-success"
                       value="<?php echo $this->lang->line('home')['confirm']; ?>" />
            </div>

        </fieldset>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
function updateCoordCount(textarea) {
    var matches = textarea.value.match(/\[-?\d+,-?\d+\]/g);
    document.getElementById('coord_count').textContent = matches ? matches.length : 0;
}
window.addEventListener('DOMContentLoaded', function () {
    var ta = document.getElementById('path');
    if (ta) updateCoordCount(ta);
});
</script>
</div>