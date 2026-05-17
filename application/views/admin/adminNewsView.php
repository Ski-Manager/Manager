<div class="w-full">
    <?php
echo '<legend>'.$this->lang->line('admin_page')['newslist'].':</legend>';
?>
<!-- START news table -->
    <div class="w-full container-border padding_top_bot_15">
            <div class="md:col-span-12"> 
                <?php echo $delete_button_all; ?>
                <?php echo '<a href="'.base_url('admin/admin_news_controller/add_new').'"><button class="btn btn-primary" name"add_button">Add new</button></a>'; ?>

                <table align="center" id="myTable1" class="table table-zebra myTableLeaderboard">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('admin_page')['id_building'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['title_english'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['title_french'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['content_english'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['content_french'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['active'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['created_date'];?></th>
                            <th><?php echo $this->lang->line('admin_page')['Actions'];?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data_news as $rec_news) {
                            echo '<tr data-id_item="'.$rec_news->id_news.'" data-item_type="news">';
                                echo '<td>'.$rec_news->id_news.'</td>'
                                        . '<td>'. $rec_news->title_english.'</td>'
                                        . '<td>'. $rec_news->title_french.'</td>'
                                        . '<td>'. $rec_news->content_english.'</td>'
                                        . '<td>'. $rec_news->content_french.'</td>'
                                        . '<td>'. $rec_news->active.'</td>'
                                        . '<td>'. $rec_news->created_date.'</td>';
                                echo '<td>'.$delete_button;
                                echo '<a href="'.base_url('admin/admin_news_controller/edit_news/'.$rec_news->id_news).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table> 
            </div>
    </div>

<div id="dialog-confirm-items" style="display:none;">
<?php echo $this->lang->line('admin_page')['confirm_delete'];?>
</div>

</div>  