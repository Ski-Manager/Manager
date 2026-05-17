<div class="w-full">
    <?php

echo '<legend>'.$this->lang->line('admin_page')['admin_queries'].'</legend>';
?>
<!-- START resort table -->
    <div class="w-full container-border padding_top_bot_15">
            <div class="md:col-span-12"> 
                <?php echo $this->session->flashdata('msg');?>
                <?php $attributes = array("class" => "", "name" => "run_queries");
                echo form_open("admin/admin_queries_controller/run_queries", $attributes);?>
                
                <label for="amount"><?php echo $this->lang->line('admin_page')['add_amount']; ?></label>
                <input name="amount" id="amount" type="text" size="15" placeholder="enter integer" class="input input-sm"/>
                <label for="column"><?php echo $this->lang->line('admin_page')['to_column']; ?></label>
                <select id="column" name="column" class="select select-sm"> 
                    <option value="cash">cash (game_resorts)</option>
                    <option value="reputation">reputation (game_resorts)</option>
                    <option value="snow_level">snow_level (game_resorts)</option>
                    <option value="genepis">genepis (game_players)</option>
                </select>
                <label for="to_player"><?php echo $this->lang->line('admin_page')['to_player']; ?></label>
                <input name="to_player" id="to_player" type="text" size="15" placeholder="ID or empty for all" class="input input-sm"/>
                <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
<?php echo form_close(); ?>
            </div>
        
    </div>


</div>  