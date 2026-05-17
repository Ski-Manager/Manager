<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <h2 class="h2"><?php echo $this->lang->line('resort')['microclimate_info']; ?></h2>

        <?php
        $altitude  = isset($altitude)  ? $altitude  : 'medium';
        $aspect    = isset($aspect)    ? $aspect    : 'north';
        $change_count     = isset($change_count)     ? (int)$change_count     : 0;
        $next_change_cost = isset($next_change_cost) ? (int)$next_change_cost : 0;
        $current_cash     = isset($current_cash)     ? (int)$current_cash     : 0;
        $wind_risk_map = [
            'low'    => 'wind_risk_low',
            'medium' => 'wind_risk_medium',
            'high'   => 'wind_risk_high',
        ];
        $wind_risk_key = isset($wind_risk_map[$altitude]) ? $wind_risk_map[$altitude] : 'wind_risk_medium';
        ?>

        <?php if ($this->session->flashdata('microclimate_success')): ?>
            <div class="alert alert-success"><?php echo $this->lang->line('resort')['microclimate_update_success']; ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('microclimate_error') == 'no_cash'): ?>
            <div class="alert alert-error"><?php echo $this->lang->line('resort')['microclimate_no_cash']; ?></div>
        <?php elseif ($this->session->flashdata('microclimate_error') == 'failed'): ?>
            <div class="alert alert-error"><?php echo $this->lang->line('resort')['microclimate_update_failed']; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-12 gap-3 mt-3">

            <!-- Altitude card -->
            <div class="md:col-span-6 lg:col-span-3 mb-3">
                <div class="card h-full">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-circle-arrow-up mr-1"></i>
                            <?php echo $this->lang->line('resort')['altitude_label']; ?>
                        </h5>
                        <p class="card-text">
                            <?php echo $this->lang->line('resort')['altitude_'.$altitude]; ?>
                        </p>
                        <small class="text-base-content/60">
                            <?php echo $this->lang->line('resort')['altitude_help']; ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Slope aspect card -->
            <div class="md:col-span-6 lg:col-span-3 mb-3">
                <div class="card h-full">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-compass mr-1"></i>
                            <?php echo $this->lang->line('resort')['aspect_label']; ?>
                        </h5>
                        <p class="card-text">
                            <?php echo $this->lang->line('resort')['aspect_'.$aspect]; ?>
                        </p>
                        <small class="text-base-content/60">
                            <?php echo $this->lang->line('resort')['aspect_help']; ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Wind risk card -->
            <div class="md:col-span-6 lg:col-span-3 mb-3">
                <div class="card h-full">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-wind mr-1"></i>
                            <?php echo $this->lang->line('resort')[$wind_risk_key]; ?>
                        </h5>
                        <p class="card-text">
                            <?php
                            $badge_class = ($altitude === 'high') ? 'error' : (($altitude === 'medium') ? 'warning' : 'success');
                            echo '<span class="badge badge-'.$badge_class.'">'.$this->lang->line('resort')[$wind_risk_key].'</span>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Build cost multiplier card -->
            <div class="md:col-span-6 lg:col-span-3 mb-3">
                <div class="card h-full">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-coins mr-1"></i>
                            <?php echo $this->lang->line('resort')['altitude_build_cost_info']; ?>
                        </h5>
                        <p class="card-text text-2xl">
                            x<?php echo number_format(get_altitude_build_cost_multiplier($altitude), 2); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- .row -->

        <!-- Edit Microclimate section -->
        <div class="grid grid-cols-12 gap-3 mt-4">
            <div class="md:col-span-8 lg:col-span-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h5 mb-0">
                            <i class="fa-solid fa-pen-to-square mr-1"></i>
                            <?php echo $this->lang->line('resort')['microclimate_edit_title']; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($change_count === 0): ?>
                            <div class="alert alert-info">
                                <?php echo $this->lang->line('resort')['microclimate_first_change_free']; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <?php
                                echo sprintf(
                                    $this->lang->line('resort')['microclimate_change_cost_info'],
                                    number_format($next_change_cost, 0, '.', ' '),
                                    number_format($current_cash, 0, '.', ' ')
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open('microclimate_controller/update'); ?>
                        <?php echo form_hidden('update_microclimate', '1'); ?>

                        <div class="mb-3">
                            <?php echo form_label($this->lang->line('resort')['altitude_label'], 'resort_altitude', ['class' => 'label']); ?>
                            <?php
                            $altitude_options = [
                                'low'    => $this->lang->line('resort')['altitude_low'],
                                'medium' => $this->lang->line('resort')['altitude_medium'],
                                'high'   => $this->lang->line('resort')['altitude_high'],
                            ];
                            echo form_dropdown('resort_altitude', $altitude_options, $altitude, 'id="resort_altitude" class="select"');
                            ?>
                        </div>

                        <div class="mb-3">
                            <?php echo form_label($this->lang->line('resort')['aspect_label'], 'resort_aspect', ['class' => 'label']); ?>
                            <?php
                            $aspect_options = [
                                'north' => $this->lang->line('resort')['aspect_north'],
                                'south' => $this->lang->line('resort')['aspect_south'],
                                'east'  => $this->lang->line('resort')['aspect_east'],
                                'west'  => $this->lang->line('resort')['aspect_west'],
                            ];
                            echo form_dropdown('resort_aspect', $aspect_options, $aspect, 'id="resort_aspect" class="select"');
                            ?>
                        </div>

                        <?php
                        $submit_attrs = [
                            'name'  => 'submit_microclimate',
                            'value' => ($change_count === 0)
                                ? $this->lang->line('resort')['microclimate_save_free']
                                : sprintf($this->lang->line('resort')['microclimate_save_cost'], number_format($next_change_cost, 0, '.', ' ')),
                            'class' => 'btn btn-primary',
                        ];
                        if ($change_count > 0 && $current_cash < $next_change_cost) {
                            $submit_attrs['disabled'] = 'disabled';
                        }
                        echo form_submit($submit_attrs);
                        ?>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div><!-- .row edit -->

    </div>
</div>
