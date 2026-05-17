<div class="w-full">
    <?php

// General page title
echo '<h2 class="h2">'.$this->lang->line('home')['genepis_title'].'</h2>';

?>     
    <!-- START account Block -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <?php // Info Messages for specific actions
            if (isset($action))
                echo '<div class="alert alert-'.$class.' text-center">'.$this->lang->line('genepis')[$action].'</div>';
                ?>
        <div class="col-span-12 md:col-span-10">
        <?php
        echo '<h3 class="h3">'.$this->lang->line('genepis')['intro_1'].'</h3>';
        echo '<h4 class="h4">'.$this->lang->line('genepis')['intro_1_text'].'</h4>';
        echo '<ul>';
        echo '<li>'.$this->lang->line('genepis')['several_loans'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['better_banks'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['improved_forecast'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['auto_close_resort'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['report_analysis'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['more'].'</li>';
        echo '</ul>';
        echo '<h3 class="h3">'.$this->lang->line('genepis')['get_genepis'].'</h3>';
        echo '<ul>';
        echo '<li>'.$this->lang->line('genepis')['get_genepis_1'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['get_genepis_2'].'</li>';
        echo '<li>'.$this->lang->line('genepis')['get_genepis_5'].'</li>';
        //echo '<li>'.$this->lang->line('genepis')['get_genepis_3'].'</li>';
        //echo '<li>'.$this->lang->line('genepis')['get_genepis_4'].'</li>';
        //echo '<li>'.$this->lang->line('genepis')['get_genepis_5'].'</li>';
        echo '</ul>';?>
        </div>
        <div class="md:col-span-2">
             <img src="<?php echo base_url('img/images/genepis.jpg'); ?>"  title="<?php $this->lang->line('home')['genepis_title'];?>" alt="<?php $this->lang->line('home')['genepis_title'];?>" height="364" width="200" />
        </div>
            
        
        <div class="md:col-span-12">
        <?php 
        echo $this->lang->line('genepis')['current_balance'].' <b>';
        echo $current_balance;
        echo ' '.$this->lang->line('home')['genepis_title'].'</b>.<br>';
        echo $this->lang->line('genepis')['see_history_genepis'];
        ?>
        </div>
        
        <div class="md:col-span-12 padding_top_bot_15">
            <?php 
            echo '<h3 class="h3">'.$this->lang->line('genepis')['invite_title'].'</h3>';
            echo $this->lang->line('genepis')['invite_friends'].': ';
            echo $invite_link;
            echo '<br>';
            echo $this->lang->line('genepis')['or_send_email'].': ';

            $attributes = array('id' => 'invite_friends_form');
            echo form_open('genepis_controller/invite_friends', $attributes);

            echo '<div class="md:col-span-12">';
            $id_name = array('id' => 'name', 'size' => '35');
            echo form_input('name', set_value('name', $this->lang->line('genepis')['name_field']), $id_name);
            echo '<span id="signup_error_name"></span>';
            echo '</div>';

            echo '<div class="md:col-span-12">';
            $id_email = array('id' => 'email', 'size' => '35');
            echo form_input('email', set_value('email', $this->lang->line('contact_form')['email_field']), $id_email);
            echo '<span id="signup_error_email"></span>';
            echo '</div>';

            echo '<div class="md:col-span-12">';
            $id_friend1 = array('id' => 'friend1', 'size' => '35');
            echo form_input('friend1', set_value('friend1', $this->lang->line('genepis')['friend1_field']), $id_friend1);
            echo '<span id="signup_error_friend1"></span>';
            echo '</div>';

            echo '<div class="md:col-span-12">';
            $id_friend2 = array('id' => 'friend2', 'size' => '35');
            echo form_input('friend2', set_value('friend2', $this->lang->line('genepis')['friend2_field']), $id_friend2);
            echo '<span id="signup_error_friend2"></span>';
            echo '</div>';

            echo '<div class="md:col-span-12">';
            $id_friend3 = array('id' => 'friend3', 'size' => '35');
            echo form_input('friend3', set_value('friend3', $this->lang->line('genepis')['friend3_field']), $id_friend3);
            echo '<span id="signup_error_friend3"></span>';
            echo '</div>';

            echo '<div class="md:col-span-12">';
            echo form_hidden('invite_friends', 'invite_friends');
            $id_invite_button = array('id' => 'submit_invite_friends', 'class' => 'btn btn-success');
            echo form_submit('submit_invite_friends', $this->lang->line('genepis')['invite'], $id_invite_button);   
            echo '</div>';        
            ?>

         <!--   <div class="md:col-span-12 padding_top_bot_15">

                <?php if(!empty($products)){ foreach($products as $row){ ?>
                    <div class="col-span-12 sm:col-span-2 lg:col-span-2">
                        <div class="card">
                            <img src="<?php echo base_url('img/icons/'.$row['image']); ?>" alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" />
                            <div class="caption">
                                <h4 class="h4 float-right"><?php echo $row['price']; ?> USD</h4>
                                <h4 class="h4"><?php echo $row['name']; ?></h4>
                                <h2 class="h2 center"><?php if (isset($row[$comment_language]) && $row[$comment_language] != '') echo $row[$comment_language]; else echo '<br>'; ?></h2>
                            </div>
                            
                            <div class="ratings">
                                <a href="<?php echo base_url('genepis_controller/buy/'.$row['id']); ?>">
                                    <img data-type="product" id ="<?php echo $row['id']; ?>" src="<?php echo base_url('img/icons/paypal_buy_now.png'); ?>" alt="Buy now with PayPal" />
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } }else{ ?>
                    <p>Product(s) not found...</p>
                <?php } ?>

            </div>
			-->
            
        </div>
        
        <div class="md:col-span-12">
        <span id="result_invite"></span>
        </div>
        
       
        
    
    </div>
          
</div>
    
    
    
    
    <!-- END account block -->