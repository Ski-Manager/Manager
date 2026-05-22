<div class="w-full">
    <?php

// General page title
echo '<h2 class="h2">'.$this->lang->line('weather')['title'].'</h2>';
echo $this->lang->line('weather')['intro_top'];


?>     
    <!-- START account Block -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="md:col-span-12">
        <?php
        echo $this->lang->line('weather')['intro'];
        echo '<br>';
        echo $this->lang->line('weather')['intro2'];
        echo '<br>';
        echo '<br>';
        echo $this->lang->line('weather')['genepis_advantages1'];
        echo '<br>';
        echo $this->lang->line('weather')['genepis_advantages2'];
        echo '<br>';
        echo $this->lang->line('weather')['genepis_advantages3'];
        echo '<br>';
        echo '<br>';
        echo $this->lang->line('weather')['subscribe_extended_forecast_tooltip'].' '.$button_subscribe;
        echo '<br>';
        echo '<br>';
        ?>
        </div>
        
        <div class="md:col-span-12">
           
                    <span id="forecast_table"><?php echo $table; ?></span>

        </div>
        
        <?php if (isset($current_snow_level)): ?>
        <div class="md:col-span-12 mt-2">
            <p><i class="fa-solid fa-snowflake" aria-hidden="true"></i> <strong><?php echo $this->lang->line('weather')['current_snow_level']; ?>:</strong> <?php echo $current_snow_level; ?> cm</p>
            <?php if (isset($snow_quality_key)): ?>
            <p>
                <strong><?php echo $this->lang->line('weather')['snow_quality_label']; ?>:</strong>
                <span class="badge badge-<?php echo htmlspecialchars($snow_quality_badge, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo $this->lang->line('weather')['snow_quality_'.$snow_quality_key]; ?>
                </span>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div id="dialog-confirm_14day_forecast" style="display:none;">
<?php echo $this->lang->line('weather')['confirm_14day_forecast'];?>
</div>
    
               
    </div>
</div>

<script type="text/javascript">
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.forecast_not_subscribed = <?php echo json_encode($this->lang->line('weather')['forecast_not_subscribed']); ?>;
Settings.subscribe                = <?php echo json_encode($this->lang->line('weather')['subscribe']); ?>;
</script>
    
    
    
    
    <!-- END account block -->