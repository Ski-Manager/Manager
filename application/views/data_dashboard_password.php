<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <div class="md:col-span-12 padding_top_bot_15">
            <h2 class="h2"><?php echo $this->lang->line('data_dashboard')['password_title']; ?></h2>
            <p><?php echo $this->lang->line('data_dashboard')['password_intro']; ?></p>
        </div>

        <?php if ($this->session->flashdata('dashboard_error')): ?>
        <div class="md:col-span-12 padding_top_bot_15">
            <p class="text-error"><?php echo $this->lang->line('data_dashboard')['password_error']; ?></p>
        </div>
        <?php endif; ?>

        <div class="w-full padding_top_bot_15">
            <?php
            $attributes = ['class' => '', 'name' => 'data_dashboard_unlock'];
            echo form_open('data_dashboard_controller/unlock', $attributes);
            echo '<div class="md:col-span-12">';
            echo form_password('dashboard_password', '', 'placeholder="' . $this->lang->line('data_dashboard')['password_placeholder'] . '" class="password" size="25"');
            echo '</div>';
            echo '<div class="md:col-span-12"><br>';
            echo form_submit('dashboard_submit', $this->lang->line('data_dashboard')['password_submit'], 'class="btn btn-primary"');
            echo '</div>';
            echo form_close();
            ?>
        </div>

    </div>
</div>
