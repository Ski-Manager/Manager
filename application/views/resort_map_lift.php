<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <?php
    if (isset($error_msg)) {
        echo $this->lang->line($error_msg);
    }
    echo $this->session->flashdata('msg');
    echo '<div>';
        echo '<h2 class="h2">'.$this->lang->line('resort_map')['build_lift_page_title'].'</h2>';
    echo '</div>';
    
    echo '<div class="md:col-span-12 padding15-no-top">';
        echo '<h4 class="h4">'.$this->lang->line('resort_map')['to_build_lift_title'].'</h4>';
        echo $this->lang->line('resort_map')['to_build_lift_instructions'];
        echo '<h4 class="h4">'.$this->lang->line('resort_map')['to_build_title'].'</h4>';
        echo $this->lang->line('resort_map')['to_build_instructions'];
        echo '<h4 class="h4">'.$this->lang->line('resort_map')['to_build_tips_title'].'</h4>';
        echo $this->lang->line('resort_map')['to_build_tips_instructions'].'<br>';
    echo '</div>';
    
    echo '<div class="min_height">';
        echo '<div class="md:col-span-12">';
                // lifts
                echo '<div class="join-vertical col-md-1" role="group">
                    <button type="button" class="btn btn-secondary button_menu '.$build_lift_button_status.'" id="build_lift">'.$this->lang->line('home')['big_lifts'].'</button></div>';
                echo '<div id="lift_types_button" class="join-vertical md:col-span-2"></div>';
                echo '<div id="grip_types_button" class="join-vertical md:col-span-2"></div>';
                echo '<div id="lift_capacity" class="join-vertical col-md-1"></div>';
                echo '<div class="col-md-1" style="width:3%;"></div>';
                echo '<div id="lift_table_info" class="col-md-5""></div>';
        echo '</div>';   
        echo '<div id="spacing" class="md:col-span-12"><br></div>';
    echo '</div>'; 
    
        if (isset($infoMessage) && $infoMessage != '') {
        echo '<div class="md:col-span-12">'.$infoMessage.'</div>';
    }
    
        echo '<div class="md:col-span-12"><br>';
        echo '<div id="mapid"></div>';
        echo '<div class="md:col-span-9 map_credit">'.$this->lang->line('resort')['map_credits'].' (<a href="http://www.skimap.com" target="_blank">www.skimap.com</a>)</div>';
        echo '<div class="md:col-span-4">';              
            echo '<strong>'.$this->lang->line('resort_map')['selected_segment_id'].': </strong><span id="id_group_location"></span><br>';
            echo '<strong>'.$this->lang->line('resort_map')['approx_length'].': </strong><span id="location_length"></span><br>';
            echo '<strong>'.$this->lang->line('resort_map')['approx_building_time'].': </strong><span id="estimated_building_time"></span><br>';
            echo '<strong>'.$this->lang->line('resort_map')['approx_price'].': </strong><span id="total_price"></span>';
        echo '</div>';
        echo '<div class="md:col-span-8">';        
            $attributes = array('id' => 'buildForm');
            echo form_open('lift_controller/build_lift', $attributes);
            echo form_hidden('buildForm', 'buildForm');
            echo form_hidden('form_id_group_location', '');
            echo form_hidden('form_id_lift_type', '');
            echo form_hidden('form_id_grip_type', '');
            echo form_hidden('form_capacity', '');
            echo form_hidden('form_lift_length_meters', '');
            $attributes_submit = array('id' => 'build_button', 'class' => 'btn-lg btn btn-success'.$build_button_status, 'disabled');
            echo form_submit($this->lang->line('building')['build'], $this->lang->line('building')['build'], $attributes_submit); 
            echo form_close();
        echo '</div>';
     ?>   
        
    <script type="text/javascript">
    var slope_meter_price = <?php echo json_encode(SLOPE_METER_PRICE); ?>;
    var slope_meter_building_time = <?php echo json_encode(SLOPE_METER_BUILDING_TIME); ?>;
    var accelerator_factor = <?php echo ACCELERATOR_FACTOR; ?>;
    var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
    Settings.meters = '<?php echo $this->lang->line('slope')['length_unit']; ?>';
    </script>
        
    <div id="chars_present" class="hidden"></div>

    <div id="dialog-confirm-sell" style="display:none;">
    <?php echo $this->lang->line('resort')['confirm_sell_item'];?>
    </div>

    <div id="dialog-confirm-destroy" style="display:none;">
    <?php echo $this->lang->line('resort')['confirm_sell_item'];?>
    </div>

</div>
</div>
</div>
