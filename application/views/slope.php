<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<?php
//echo 'slope_not_found:'.$slope_not_found;
if (!isset($action) || $action != 'slope_not_found') {  // if slope in URL doesn't exist (maybe someone tried to access the page directly)
    // ONLY WHEN BUILDING
    if (isset($slope_to_build) && $slope_to_build == true) {  // if the slope needs to be built, we show the form
        echo '<h2 class="h2">'.$this->lang->line('slope')['build_title'].$id_sector;
        if (isset($action) && $action == 'sector_locked')               // If the sector is locked, we display "Locked"
            echo ' ('.$this->lang->line('resort')['locked'].')  ';
        echo '</h2>';
    } 
    
    if (isset($header)){
        echo $header;
    }
    // ONLY WHEN NOT BUILDING - ONLY DISPLAYING
    else if (!isset($slope_to_build) || $slope_to_build == false) {
        echo '<h2 class="h2">'.$slope_name_to_show.$this->lang->line('resort')['info_title_loc'].$id_sector.' ';
        if (isset($edit_name_button))
            echo $edit_name_button;
        echo '</h2><br>';
    }
    
    echo '<div id="chars" class="hidden"></div>';           // default max size, will be updated when typing characters
    
    echo '<div>';
    echo '<h3 class="h3">'.$this->lang->line('slope')['build_info'].'</h3><br>';
     
    // ALWAYS SHOW THE INFO
    if (isset($header_difficulty)){
        echo $header_difficulty;
    }
    else {
        echo '<strong>'.$this->lang->line('slope')['difficulty'].':</strong> '.$slope_difficulty.' ';
        if (isset($edit_difficulty_button))
            echo $edit_difficulty_button;
        echo '<br>';
    }
        echo '<strong>'.$this->lang->line('slope')['diff_type_column'].':</strong> '.$slope_type.'<br>';
        echo '<strong>'.$this->lang->line('home')['status'].':</strong> '.$pre_slope_status.'<div style="display:inline;" data-countdown="'.$slope_status.'">'.$slope_status.'</div>'.$post_slope_status.'<br>';
        echo '<strong>'.$this->lang->line('slope')['length'].':</strong> '.$slope_length.' '.$this->lang->line('slope')['length_unit'].'<br>';
        echo '<strong>'.$this->lang->line('home')['cost'].':</strong> '.$slope_cost.' €<br>';
        echo '<strong>'.$this->lang->line('home')['building_time'].':</strong> '.$slope_building_time.'<br>';
        echo '<strong>'.$this->lang->line('home')['reward'].':</strong> '.$reputation.' '.$this->lang->line('home')['mini_reputation'].'<br>';
        echo '<strong>'.$this->lang->line('slope')['condition'].':</strong> '.$slope_condition.'<br>';
        echo $this->session->flashdata('msg');
    // ONLY WHEN BUILDING, show the FORM
    if (isset($slope_to_build) && $slope_to_build == true && (!isset($action) || $action != 'not_enough_money')) { // if the slope needs to be built, we show the form
        if (isset($action) && $action == 'sector_locked') {
            echo '<br>'.$this->lang->line('resort')['sector_locked'];
        }
        else {
            echo '<div id="build_slope_form">';
            echo form_open('slope_controller/build_slope/'.$currentResortID.'');
            echo form_hidden('buildForm', 'buildForm');
            echo form_hidden('id_slope', $id_slope);
            echo form_hidden('id_sector', $id_sector);
            echo '<br>';
            $data_input = array(
                'name'        => 'slope_choose_name',
                'id'          => 'slope_choose_name',
                'value'       => set_value('slope_choose_name', $slope_name_to_show),
                'size'        => '35'
            );
            echo form_label($this->lang->line('slope')['choose_name'], 'slope_choose_name');
            echo '<br>';
            echo form_input($data_input);
            if (isset($slope_error_name))
                echo $slope_error_name;
            echo form_submit($this->lang->line('slope')['build'], $this->lang->line('slope')['build'], "class='btn btn-success'"); 
            echo form_close();
            echo '</div>';
            echo $this->session->flashdata('msg');
            
            
            
            
            
        }
    }
    
    echo '</div>';
    echo '<div>';
    echo '<img src="'.base_url('img/icons/slope_'.$slope_difficulty_english.'.png').'" align="top" class="valigntop" alt="'.htmlspecialchars($slope_difficulty, ENT_QUOTES, 'UTF-8').'" title="'.htmlspecialchars($slope_difficulty, ENT_QUOTES, 'UTF-8').'">';
    echo '</div>';
    
    echo '<div class="md:col-span-12">';
    if (isset($action) && $action == 'not_enough_money') { // the user doesn't have enough money
        echo '<div class="alert alert-error text-center">'.$this->lang->line('home')['not_enough_money'].'</div>';
    }
    if (isset($action) && $action == 'ongoing_construction_slope') { // there is another slope being built
        echo $this->lang->line('slope')['ongoing_construction_slope'];
    }
    else if( isset($action) && ($action == 'rush_completed' || $action == 'not_enough_genepis' || $action == 'already_completed')){
        echo $this->lang->line('slope')[$action];
   // else if (isset($slope_built) && $slope_built == false) {  // if the slope is NOT built
        if (isset($infoSlope))
                echo $infoSlope;
   // }
   // echo '<br><br>$slope_built: '.$slope_built;
   // echo '$infoSlope: '.$infoSlope;
}
else if( $action == 'slope_not_found'){
    echo $this->lang->line('slope')['not_found'];
}

}
echo '</div>';
?></div>