<div class="w-full">
    <?php
echo '<legend>'.$title.':</legend>';
?>
<!-- START resort table -->
    <div class="w-full container-border padding_top_bot_15">
        <?php echo '<a href="'.base_url('admin/admin_'.$page_type.'_controller/add_new').'"><button class="btn btn-primary" name"add_button">Add new</button></a>'; ?>

            <div class="md:col-span-12"> 
                <?php echo $table; ?>
            </div>
    </div>

    <div id="dialog-confirm-items" style="display:none;">
        <?php echo $this->lang->line('admin_page')['confirm_delete'];?>
    </div>
</div>  
