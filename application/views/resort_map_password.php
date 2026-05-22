<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <div class="md:col-span-12 padding_top_bot_15">
            <h2 class="h2"><?php echo $this->lang->line('resort_map')['password_title']; ?></h2>
            <p><?php echo $this->lang->line('resort_map')['password_intro']; ?></p>
            <p class="text-info"><?php echo $this->lang->line('resort_map')['sector_6_building']; ?></p>
        </div>

        <?php if ($this->session->flashdata('resort_map_error')): ?>
        <div class="md:col-span-12 padding_top_bot_15">
            <p class="text-error"><?php echo $this->lang->line('resort_map')['password_error']; ?></p>
        </div>
        <?php endif; ?>

        <div class="w-full padding_top_bot_15">
            <?php
            $attributes = ['class' => '', 'name' => 'resort_map_unlock'];
            echo form_open('resort_map_controller/unlock', $attributes);
            echo '<div class="md:col-span-12">';
            echo form_password('resort_map_password', '', 'placeholder="' . $this->lang->line('resort_map')['password_placeholder'] . '" class="password" size="25"');
            echo '</div>';
            echo '<div class="md:col-span-12"><br>';
            echo form_submit('resort_map_submit', $this->lang->line('resort_map')['password_submit'], 'class="btn btn-primary"');
            echo '</div>';
            echo form_close();
            ?>
        </div>

    </div>
</div>
