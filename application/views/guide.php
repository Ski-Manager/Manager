<div class="card bg-base-100 shadow-sm"><div class="card-body mb-3">

<div class="w-full padding_top_bot_15">

    <div class="md:col-span-12">
        <h1 class="h3"><?php echo $this->lang->line('guide')['page_title']; ?></h1>
        <p class="text-base-content/60"><?php echo $this->lang->line('guide')['last_updated']; ?></p>
    </div>

    <!-- Table of Contents -->
    <div class="md:col-span-12 padding_top_bot_15">
        <h2 class="h4"><?php echo $this->lang->line('guide')['toc_title']; ?></h2>
        <ol class="list-decimal list-inside space-y-1 pl-2">
            <li><a href="#getting-started"><?php echo $this->lang->line('guide')['gs_title']; ?></a></li>
            <li><a href="#slopes"><?php echo $this->lang->line('guide')['slopes_title']; ?></a></li>
            <li><a href="#lifts"><?php echo $this->lang->line('guide')['lifts_title']; ?></a></li>
            <li><a href="#buildings"><?php echo $this->lang->line('guide')['buildings_title']; ?></a></li>
            <li><a href="#staff"><?php echo $this->lang->line('guide')['staff_title']; ?></a></li>
            <li><a href="#finances"><?php echo $this->lang->line('guide')['finance_title']; ?></a></li>
            <li><a href="#weather"><?php echo $this->lang->line('guide')['weather_title']; ?></a></li>
            <li><a href="#snowmaking"><?php echo $this->lang->line('guide')['snow_title']; ?></a></li>
            <li><a href="#competitions"><?php echo $this->lang->line('guide')['comp_title']; ?></a></li>
            <li><a href="#leaderboard"><?php echo $this->lang->line('guide')['lb_title']; ?></a></li>
            <li><a href="#tips"><?php echo $this->lang->line('guide')['tips_title']; ?></a></li>
        </ol>
    </div>

    <!-- 1 Getting Started -->
    <div class="md:col-span-12 padding_top_bot_15" id="getting-started">
        <h2 class="h4"><?php echo $this->lang->line('guide')['gs_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['gs_body']; ?></p>
        <div class="space-y-3 mt-3">
            <div>
                <h3 class="font-semibold"><?php echo $this->lang->line('guide')['gs_step1_title']; ?></h3>
                <p><?php echo $this->lang->line('guide')['gs_step1']; ?></p>
            </div>
            <div>
                <h3 class="font-semibold"><?php echo $this->lang->line('guide')['gs_step2_title']; ?></h3>
                <p><?php echo $this->lang->line('guide')['gs_step2']; ?></p>
            </div>
            <div>
                <h3 class="font-semibold"><?php echo $this->lang->line('guide')['gs_step3_title']; ?></h3>
                <p><?php echo $this->lang->line('guide')['gs_step3']; ?></p>
            </div>
            <div>
                <h3 class="font-semibold"><?php echo $this->lang->line('guide')['gs_step4_title']; ?></h3>
                <p><?php echo $this->lang->line('guide')['gs_step4']; ?></p>
            </div>
        </div>
    </div>

    <!-- 2 Slopes -->
    <div class="md:col-span-12 padding_top_bot_15" id="slopes">
        <h2 class="h4"><?php echo $this->lang->line('guide')['slopes_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['slopes_body']; ?></p>
        <h3 class="font-semibold mt-3"><?php echo $this->lang->line('guide')['slopes_diff_title']; ?></h3>
        <ul class="list-disc list-inside space-y-1 pl-2 mt-1">
            <li><?php echo $this->lang->line('guide')['slopes_green']; ?></li>
            <li><?php echo $this->lang->line('guide')['slopes_blue']; ?></li>
            <li><?php echo $this->lang->line('guide')['slopes_red']; ?></li>
            <li><?php echo $this->lang->line('guide')['slopes_black']; ?></li>
        </ul>
        <p class="mt-2"><?php echo $this->lang->line('guide')['slopes_maint']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['slopes_custom']; ?></p>
    </div>

    <!-- 3 Lifts -->
    <div class="md:col-span-12 padding_top_bot_15" id="lifts">
        <h2 class="h4"><?php echo $this->lang->line('guide')['lifts_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['lifts_body']; ?></p>
        <h3 class="font-semibold mt-3"><?php echo $this->lang->line('guide')['lift_types_title']; ?></h3>
        <ul class="list-disc list-inside space-y-1 pl-2 mt-1">
            <li><?php echo $this->lang->line('guide')['lift_surface']; ?></li>
            <li><?php echo $this->lang->line('guide')['lift_chair']; ?></li>
            <li><?php echo $this->lang->line('guide')['lift_gondola']; ?></li>
        </ul>
        <p class="mt-2"><?php echo $this->lang->line('guide')['lift_tech']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['lift_closed']; ?></p>
    </div>

    <!-- 4 Buildings -->
    <div class="md:col-span-12 padding_top_bot_15" id="buildings">
        <h2 class="h4"><?php echo $this->lang->line('guide')['buildings_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['buildings_body']; ?></p>
        <ul class="list-disc list-inside space-y-1 pl-2 mt-2">
            <li><?php echo $this->lang->line('guide')['bld_ticket']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_hotel']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_restaurant']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_school']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_rental']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_spa']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_patrol']; ?></li>
            <li><?php echo $this->lang->line('guide')['bld_snowmaking']; ?></li>
        </ul>
    </div>

    <!-- 5 Staff -->
    <div class="md:col-span-12 padding_top_bot_15" id="staff">
        <h2 class="h4"><?php echo $this->lang->line('guide')['staff_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['staff_body']; ?></p>
        <h3 class="font-semibold mt-3"><?php echo $this->lang->line('guide')['staff_types_title']; ?></h3>
        <ul class="list-disc list-inside space-y-1 pl-2 mt-1">
            <li><?php echo $this->lang->line('guide')['staff_inst']; ?></li>
            <li><?php echo $this->lang->line('guide')['staff_patrol']; ?></li>
            <li><?php echo $this->lang->line('guide')['staff_mechanic']; ?></li>
            <li><?php echo $this->lang->line('guide')['staff_groomer']; ?></li>
            <li><?php echo $this->lang->line('guide')['staff_manager']; ?></li>
        </ul>
        <p class="mt-2"><?php echo $this->lang->line('guide')['staff_upgrades']; ?></p>
    </div>

    <!-- 6 Finances -->
    <div class="md:col-span-12 padding_top_bot_15" id="finances">
        <h2 class="h4"><?php echo $this->lang->line('guide')['finance_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['finance_body']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['finance_pricing']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['finance_loans']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['finance_reports']; ?></p>
    </div>

    <!-- 7 Weather -->
    <div class="md:col-span-12 padding_top_bot_15" id="weather">
        <h2 class="h4"><?php echo $this->lang->line('guide')['weather_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['weather_body']; ?></p>
        <ul class="list-disc list-inside space-y-1 pl-2 mt-2">
            <li><?php echo $this->lang->line('guide')['weather_winter']; ?></li>
            <li><?php echo $this->lang->line('guide')['weather_spring']; ?></li>
            <li><?php echo $this->lang->line('guide')['weather_summer']; ?></li>
            <li><?php echo $this->lang->line('guide')['weather_autumn']; ?></li>
        </ul>
        <p class="mt-2"><?php echo $this->lang->line('guide')['weather_events']; ?></p>
    </div>

    <!-- 8 Snowmaking -->
    <div class="md:col-span-12 padding_top_bot_15" id="snowmaking">
        <h2 class="h4"><?php echo $this->lang->line('guide')['snow_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['snow_body']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['snow_guns']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['snow_strategy']; ?></p>
    </div>

    <!-- 9 Competitions -->
    <div class="md:col-span-12 padding_top_bot_15" id="competitions">
        <h2 class="h4"><?php echo $this->lang->line('guide')['comp_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['comp_body']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['comp_types']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['comp_prep']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['comp_reward']; ?></p>
    </div>

    <!-- 10 Leaderboard -->
    <div class="md:col-span-12 padding_top_bot_15" id="leaderboard">
        <h2 class="h4"><?php echo $this->lang->line('guide')['lb_title']; ?></h2>
        <p><?php echo $this->lang->line('guide')['lb_body']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['lb_tabs']; ?></p>
        <p class="mt-2"><?php echo $this->lang->line('guide')['lb_sandbox']; ?></p>
    </div>

    <!-- 11 Tips -->
    <div class="md:col-span-12 padding_top_bot_15" id="tips">
        <h2 class="h4"><?php echo $this->lang->line('guide')['tips_title']; ?></h2>
        <ol class="list-decimal list-inside space-y-2 pl-2 mt-2">
            <li><?php echo $this->lang->line('guide')['tip_1']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_2']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_3']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_4']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_5']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_6']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_7']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_8']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_9']; ?></li>
            <li><?php echo $this->lang->line('guide')['tip_10']; ?></li>
        </ol>
    </div>

    <!-- CTA -->
    <div class="md:col-span-12 padding_top_bot_15 text-center">
        <a href="<?php echo base_url('signup'); ?>" class="btn btn-primary mr-2">Play for Free</a>
        <a href="<?php echo base_url('about'); ?>" class="btn btn-info">About Ski Manager</a>
    </div>

</div>
</div></div>
