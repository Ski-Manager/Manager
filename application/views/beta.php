<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
         
        <?php
        echo '<h2 class="h2">'.$title.'</h2>'; 
        echo '<div class="md:col-span-12">'.$introBeta.'</div>';
        ?>

        
        <!-- Brevo Signup Form -->
    <?php $this->config->load('brevo'); $brevoSignupFormId = htmlspecialchars($this->config->item('brevo_signup_form_id'), ENT_QUOTES, 'UTF-8'); ?>
    <link rel="preload" href="<?php echo base_url().'css/sendinblue_forms.css';?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?php echo base_url().'css/sendinblue_forms.css';?>"></noscript>
    <div id="sib_embed_signup">
        <div id="padding_top_bot_15 sib_embed_signup">
            <div class="forms-builder-wrapper">
                <input type="hidden" id="sib_embed_signup_lang" value="<?php echo $short_lang;?>">
                <input type="hidden" id="sib_embed_invalid_email_message" value="<?php echo $this->lang->line('home')['email_invalid'];?>">
                <input type="hidden" name="primary_type" id="primary_type" value="email">
                <div id="sib_loading_gif_area" style="position: absolute;z-index: 9999;display: none;"></div>
                <form class="description" id="theform" name="theform"
                    action="https://sibforms.com/serve/<?php echo $brevoSignupFormId; ?>"
                    method="post" onsubmit="return false;">
                    <input type="hidden" name="from_url" id="from_url" value="yes">
                    <input type="hidden" name="ORIGIN" id="ORIGIN" value="<?php $origin = $this->input->get('origin', TRUE); if ($origin !== null) echo htmlspecialchars($origin, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="sib-container rounded ui-sortable" style="border-radius: 4px; position: relative; margin: 0px; text-align: left; border-width: 0px !important; border-color: transparent !important;">
                        <div class="view-messages" style=" margin:5px 0;"> </div>
                        <div class="primary-group email-group forms-builder-group ui-sortable" style="">
                            <div class="grid grid-cols-12 gap-3 mandatory-email" style="position: relative; padding: 10px 15px;">
                                <div class="lbl-tinyltr padding_top_bot_15">
                                    <?php echo $this->lang->line('closed')['enter_email'];?>
                                </div>
                                <input type="text" name="email" id="email" value="" placeholder="<?php echo $this->lang->line('contact_form')['email_field']; ?>" class="input w-full" style="max-width: 400px;">
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                        <div class="captcha forms-builder-group" style="display: block;">
                            <div class="grid grid-cols-12 gap-3" style="padding: 10px 15px;">
                                <div id="gcaptcha" style="transform: scale(1); margin-left: 0px;"></div>
                            </div>
                        </div>
                        <div style="font-weight: bold; display: block;">
                            <button class="btn btn-success btn-lg" type="submit" data-editfield="subscribe" style=""><?php echo $this->lang->line('beta')['register'];?></button>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var sib_prefix = 'sib'; var sib_dateformat = 'dd-mm-yyyy';
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit&hl=en"></script>
    <script>
        var verifyCallback = function(response) { if(response.length > 0) { $(".captcha > div.row > div.message_area").remove(); } }; var myCallBack = function() { if($('.captcha > div.row > div#gcaptcha').length > 0) { var captcha = grecaptcha.render('gcaptcha', { 'sitekey' : '6LehL1gUAAAAAOm-yjCciSsmK5qqy3812HjkdyuQ', 'theme' : 'light', 'callback' : verifyCallback, }); } };
    </script>
    <script type='text/javascript' src='https://sibforms.com/forms/end-form/build/main.js'></script>
    <!-- End: Brevo Signup Form -->
    
            <div class="md:col-span-12 padding_top_bot_15">
                <?php echo $this->lang->line('beta')['link_email'].'<br>';
                echo '<h3 class="h3">'.$this->lang->line('beta')['meaning_beta'].'</h3>';
                echo $this->lang->line('beta')['report_bugs'].'<br>';
                echo $this->lang->line('beta')['report_feedback'];
                echo '<h3 class="h3">'.$this->lang->line('beta')['how_to_report'].'</h3>';
                echo $this->lang->line('beta')['how_to_report_answer'];
                echo '<h3 class="h3">'.$this->lang->line('beta')['good_to_know_title'].'</h3>';
                echo $this->lang->line('beta')['good_to_know_text'].'<br>';
                echo $this->lang->line('beta')['reset_progress']; ?>
            </div>
        </div>


</div>  