<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        
        <?php
        echo '<h1 class="h1">'.$this->lang->line('closed')['title'].'</h1>';        
        echo '<div class="md:col-span-12">'.$this->lang->line('home')['intro'].'</div>';        
        ?>
        
        <div class="offset-1 col-span-6 md:col-span-8 padding_top_bot_15 padding15-no-top">
            <?php echo '<picture>'
                . '<source srcset="'.base_url('img/images/homeintroimage.avif').'" type="image/avif">'
                . '<img class="img-fluid" src="'.base_url('img/images/homeintroimage.png').'" alt="'.htmlspecialchars($this->lang->line('home')['introImage'] ?? 'Ski resort management game', ENT_QUOTES, 'UTF-8').'" title="'.htmlspecialchars($this->lang->line('home')['introImage'], ENT_QUOTES, 'UTF-8').'" width="800" height="436" fetchpriority="high"/>'
                . '</picture>'; ?>
        </div>
            
        <div class="offset-1 col-span-6 md:col-span-4 padding_top_bot_15">
            <?php
            echo $this->lang->line('closed')['description1'].'<br><br>';
            echo '<span class="bold font16">'.$this->lang->line('closed')['description2'].' ';
            echo $beta_link;
            echo $this->lang->line('closed')['this_page'].'.</a></span>';
            ?>
        </div>
        
     <div class="offset-1 col-span-6 md:col-span-4 padding15-padding_top_bot_15-top">
            <?php echo '<a href="https://www.facebook.com/Ski-Manager-1377272882355150/"><img class="img-fluid social_networks" src="'.base_url('img/icons/facebook58.png').'" alt="'.htmlspecialchars($this->lang->line('home')['ski-manager'], ENT_QUOTES, 'UTF-8').'" width="58" height="58"/><span class="bold font16 padding15-no-top">'.$this->lang->line('home')['ski-manager'].'</span></a>';
             ?>
         </div> 
        

    </div>

</div>