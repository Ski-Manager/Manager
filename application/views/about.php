<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="w-full padding_top_bot_15">

        <div class="md:col-span-12">
            <h3 class="h3"><?php echo $this->lang->line('about')['page_title']; ?></h3>
            <p class="text-base-content/60"><?php echo $this->lang->line('about')['last_updated']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['intro_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['intro_text']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['mission_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['mission_text']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['features_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['features_text']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('about')['feature_1']; ?></li>
                <li><?php echo $this->lang->line('about')['feature_2']; ?></li>
                <li><?php echo $this->lang->line('about')['feature_3']; ?></li>
                <li><?php echo $this->lang->line('about')['feature_4']; ?></li>
                <li><?php echo $this->lang->line('about')['feature_5']; ?></li>
                <li><?php echo $this->lang->line('about')['feature_6']; ?></li>
            </ul>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['history_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['history_text']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['technology_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['technology_text']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['community_title']; ?></h4>
            <p><?php echo $this->lang->line('about')['community_text']; ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['ads_title']; ?></h4>
            <p><?php echo sprintf($this->lang->line('about')['ads_text'], base_url('privacy')); ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['contact_title']; ?></h4>
            <p><?php echo sprintf($this->lang->line('about')['contact_text'], base_url('contact')); ?></p>
        </div>

        <div class="md:col-span-12 padding_top_bot_15">
            <h4 class="h4"><?php echo $this->lang->line('about')['legal_title']; ?></h4>
            <p><?php echo sprintf($this->lang->line('about')['legal_text'], base_url('terms'), base_url('privacy'), base_url('cookies')); ?></p>
        </div>

    </div>
</div>
