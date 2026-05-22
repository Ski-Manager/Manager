<div class="w-full">
    <?php
    echo '<legend>'.$this->lang->line('adminMaintenance')['title'].':</legend>';
    ?>
    <div class="w-full container-border padding_top_bot_15">
        <div class="md:col-span-12">
            <?php echo $this->session->flashdata('msg');?>
            Reset auto-increment values on all tables: <?php echo $reset_autoincrement_button.'<br>'; ?>  
            Manual Database Backup: <?php echo $db_backup_button.'<br>'; ?>  
            Reset game: <?php echo $reset_game_button.'<br>'; ?>  
            Run DB Migrations: <?php echo $run_migration_button.'<br>'; ?>
            </div>
        <?php
        
        echo '<div class="md:col-span-12">';
            echo '<h3 class="h3">Results query:</h3>'; 
            echo $result_queries;
        echo '</div>';
        
        echo '<div class="md:col-span-12">';
            echo '<h3 class="h3">'.$this->lang->line('adminMaintenance')['empty_tables'].': </h3>'; 
            foreach ($empty_table_button as $table_name) {
                echo '<div class="md:col-span-9">';
                echo '<div class="md:col-span-4">'.$table_name[0].'</div><div class="col-md-1">'.$table_name[1].'</div><div class="md:col-span-6">'.$table_name[2].'</div>';  
                echo '</div>';
            }  
        echo '</div>'; 
        ?>
    </div>
</div>