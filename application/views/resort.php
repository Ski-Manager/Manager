<script>
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.sell_item    = <?php echo json_encode($this->lang->line('home')['sell_item'] ?? 'Sell'); ?>;
Settings.destroy_item = <?php echo json_encode($this->lang->line('resort')['destroy_item'] ?? 'Destroy'); ?>;
Settings.share_resort      = <?php echo json_encode($this->lang->line('resort')['share_resort'] ?? 'Share'); ?>;
Settings.share_resort_text = <?php echo json_encode($this->lang->line('resort')['share_resort_text'] ?? 'Check out my ski resort "%name%" in %country% on Ski-Manager! 🎿 #SkiManager'); ?>;
</script>
<div class="w-full">
    <div class="card resort-main-card mb-3">
        <div class="card-body">
        <div class="flex items-center gap-3 mb-4">
            <div class="avatar placeholder">
                <div class="bg-primary text-primary-content rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fa-solid fa-mountain-sun text-lg"></i>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold leading-tight"><?php echo $this->lang->line('home')['resort_controller_title']; ?></h2>
                <p class="text-sm opacity-60"><?php echo $this->lang->line('resort')['page_subtitle'] ?? 'Manage your resort\'s slopes, lifts and facilities'; ?></p>
            </div>
        </div>
        <?php
        if (isset($error_msg)) {
            echo $this->lang->line($error_msg);
        }
        echo $this->session->flashdata('msg');
        echo $this->session->flashdata('daily_bonus_msg');
        echo $this->session->flashdata('idle_income_msg');

        if (isset($user_has_resort) && $user_has_resort == true) {  // resort already created, we show the info
            if (isset($infoMessage)) {
                echo $this->lang->line($infoMessage);
            }
            if (isset($error))
                echo $this->lang->line($error);

            if (isset($infoResort)) {
                echo $infoResort;
            }
            if (isset($legacy_bonus_applied) && $legacy_bonus_applied > 0) {
                echo '<div class="alert alert-success text-center mt-3"><strong>'.number_format($legacy_bonus_applied, 0, ',', ' ').' €</strong> '.$this->lang->line('logs')['legendary_mountain_bonus_applied'].'</div>';
            }
        } else if (!isset($user_has_resort) || $user_has_resort == false) { // No resort created, we show the creation form
            // Welcome banner for new users who have not yet created a resort
            ?>
            <div class="alert alert-info resort-welcome-alert alert-dismissible fade show mb-3" role="alert">
                <h5 class="alert-heading">
                    <i class="fa-regular fa-stars mr-1"></i>
                    <?php echo $this->lang->line('resort')['welcome_new_user_title'] ?? 'Welcome to Ski-Manager!'; ?>
                </h5>
                <p class="mb-2">
                    <?php echo $this->lang->line('resort')['welcome_new_user_body'] ?? 'Start by creating your ski resort below. If you\'re new, our step-by-step tutorial will help you get up and running quickly.'; ?>
                </p>
                <a href="<?php echo base_url('tutorial_controller'); ?>" class="btn btn-sm btn-primary mr-2">
                    <i class="fa-regular fa-map mr-1"></i>
                    <?php echo $this->lang->line('navbar')['tutorial'] ?? 'Tutorial'; ?>
                </a>
                <a href="<?php echo base_url('help_controller'); ?>" class="btn btn-sm btn-outline">
                    <i class="fa-regular fa-circle-question mr-1"></i>
                    <?php echo $this->lang->line('navbar')['help'] ?? 'Help'; ?>
                </a>
                <button type="button" class="btn btn-ghost btn-xs ml-2" onclick="this.closest('.alert').remove();" aria-label="Close">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <?php
            if (isset($infoResort)) {
                echo $infoResort;
            }

            echo '<div id="create_resort_form" class="card mt-4">';
            if (!isset($resort_mode) || $resort_mode != 'edit') {
                echo '<div class="card-header"><h5 class="h5 mb-0"><i class="fa-regular fa-building mr-2"></i>'.$this->lang->line('resort')['create'].'</h5></div>';
            } else {
                echo '<div class="card-header"><h5 class="h5 mb-0"><i class="fa-solid fa-pen-to-square mr-2"></i>'.$this->lang->line('resort')['update'].'</h5></div>';
            }
            echo '<div class="card-body">';
            $attributes = array('id' => 'resort_form');
            if (!isset($resort_mode) || $resort_mode != 'edit') {
                echo form_open('resort_controller/create_resort_preparation', $attributes);
                echo form_hidden('createResort', 'createResort');                    // to show which form we post
            } else {
                echo form_open('resort_controller/update_resort', $attributes);
                echo form_hidden('updateResort', 'updateResort');                    // to show which form we post
            }

            echo '<div class="mb-3">';
            echo form_label($this->lang->line('resort')['name_field'], 'resort_name', array('class' => 'label'));
            $id_resort = array('id' => 'resort_name', 'class' => 'input w-full max-w-lg');
            echo form_input('resort_name', set_value('resort_name', ''), $id_resort);
            echo '<div class="text-sm opacity-60">' . $this->lang->line('alpha_dash_space_resort') . '</div>';
            echo '<span class="errorTxt"></span>';
            if (isset($resort_error_name))
                echo $resort_error_name;
            echo '</div>';

            echo '<div class="mb-3">';
            echo form_label($this->lang->line('resort')['country_field'], 'resort_country', array('class' => 'label'));
            echo form_input('resort_country', set_value('resort_country', ''), array('id' => 'resort_country', 'class' => 'input w-full max-w-lg'));
            if (isset($resort_error_country))
                echo '<div class="text-error small mt-1">' . $resort_error_country . '</div>';
            echo '</div>';

            echo '<div class="mb-3">';
            echo form_label($this->lang->line('resort')['description'], 'resort_description', array('class' => 'label'));
            $data = array(
                'name'        => 'resort_description',
                'id'          => 'resort_description',
                'value'       => set_value('resort_description', ''),
                'rows'        => '6',
                'cols'        => '50',
                'class'       => 'textarea w-full max-w-lg'
            );
            echo form_textarea($data);
            echo '<div id="chars">500</div>';                // default max size, will be updated when typing characters
            if (isset($resort_error_description))
                echo $resort_error_description;
            echo '</div>';

            if (!isset($resort_mode) || $resort_mode != 'edit') {
                // Altitude selection (creation only – editable later via Microclimate page)
                echo '<div class="mb-3">';
                echo form_label($this->lang->line('resort')['altitude_label'].' <span class="text-base-content/60 small">('.$this->lang->line('resort')['altitude_help'].')</span>', 'resort_altitude', array('class' => 'label'));
                $altitude_options = [
                    'low'    => $this->lang->line('resort')['altitude_low'],
                    'medium' => $this->lang->line('resort')['altitude_medium'],
                    'high'   => $this->lang->line('resort')['altitude_high'],
                ];
                $current_altitude = isset($resort_altitude) ? $resort_altitude : 'medium';
                echo form_dropdown('resort_altitude', $altitude_options, set_value('resort_altitude', $current_altitude), 'id="resort_altitude" class="select"');
                echo '</div>';

                // Aspect selection (creation only – editable later via Microclimate page)
                echo '<div class="mb-3">';
                echo form_label($this->lang->line('resort')['aspect_label'].' <span class="text-base-content/60 small">('.$this->lang->line('resort')['aspect_help'].')</span>', 'resort_aspect', array('class' => 'label'));
                $aspect_options = [
                    'north' => $this->lang->line('resort')['aspect_north'],
                    'south' => $this->lang->line('resort')['aspect_south'],
                    'east'  => $this->lang->line('resort')['aspect_east'],
                    'west'  => $this->lang->line('resort')['aspect_west'],
                ];
                $current_aspect = isset($resort_aspect) ? $resort_aspect : 'north';
                echo form_dropdown('resort_aspect', $aspect_options, set_value('resort_aspect', $current_aspect), 'id="resort_aspect" class="select"');
                echo '</div>';
            } else {
                echo '<div class="mb-3">';
                echo '<p class="text-base-content/60 small">';
                echo $this->lang->line('resort')['microclimate_edit_via_page'];
                echo '</p>';
                echo '</div>';
            }

            echo '<div class="mt-3">';
            if (!isset($resort_mode) || $resort_mode != 'edit') {
                echo form_submit($this->lang->line('resort')['create'], $this->lang->line('resort')['create'], 'class="btn btn-success"');
            } else {
                echo form_submit($this->lang->line('resort')['update'], $this->lang->line('resort')['update'], 'class="btn btn-success"');
            }
            echo form_close();

            echo '</div>';   // closes mt-3
            echo '</div>';   // closes card-body
            echo '</div>';   // closes create_resort_form card
        }

        if (isset($summaryBuildings)) {
            echo '<div class="mt-4">';
            echo '<div class="card">';
            echo '<div class="card-header"><h5 class="font-semibold flex items-center gap-2"><i class="fa-solid fa-building me-1"></i>'.$this->lang->line('resort')['summary_intro'].'</h5></div>';
            echo '<div class="card-body p-0">';
            echo $summaryBuildings;
            echo '</div></div>';
            echo '</div>';
        }

        if (isset($legendary_status) && $legendary_status == 1) {
            echo '<div class="mt-4">';
            echo '<div class="alert alert-warning flex items-center gap-2" role="alert">';
            echo '<span class="text-2xl">⭐</span>';
            echo '<div>';
            echo '<strong>'.$this->lang->line('resort')['legendary_mountain_badge'].'</strong> &mdash; ';
            echo $this->lang->line('resort')['legendary_mountain_desc'];
            if (isset($legacy_rating) && $legacy_rating !== NULL) {
                echo ' <span class="badge badge-warning">'.$this->lang->line('resort')['legacy_rating_label'].': '.(int)$legacy_rating.'/100</span>';
            }
            echo '</div></div>';
            echo '</div>';
        } elseif (isset($legacy_rating) && $legacy_rating !== NULL) {
            echo '<div class="mt-4">';
            echo '<div class="alert alert-info" role="alert">';
            echo $this->lang->line('resort')['legacy_rating_label'].': <strong>'.(int)$legacy_rating.'/100</strong>';
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div id="chars_present" class="hidden"></div>

        <div id="dialog-confirm-sell" style="display:none;">
            <?php echo $this->lang->line('resort')['confirm_sell_item']; ?>
        </div>

        <div id="dialog-confirm-destroy" style="display:none;">
            <?php echo $this->lang->line('resort')['confirm_destroy_item']; ?>
        </div>

    </div><!-- /.resort-main-card card-body -->
    </div><!-- /.resort-main-card -->
</div><!-- /.w-full -->
