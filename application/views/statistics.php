<div class="w-full">
    <h2 class="h2"><?php echo $this->lang->line('statistics')['title']; ?></h2>
    <p><?php echo $this->lang->line('statistics')['intro']; ?></p>

    <?php if ($resort_built): ?>

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <!-- Peak Lift Usage -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_lift_usage_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_lift_usage_desc']; ?></p>
            <div id="chart_lift_usage"></div>
        </div>

        <hr>

        <!-- Revenue per Lift -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_revenue_per_lift_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_revenue_per_lift_desc']; ?></p>
            <div id="chart_revenue_per_lift"></div>
        </div>

        <hr>

        <!-- Most Popular Slope -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_slope_popularity_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_slope_popularity_desc']; ?></p>
            <div id="chart_slope_popularity"></div>
        </div>

        <hr>

        <!-- Guest Satisfaction -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_satisfaction_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_satisfaction_desc']; ?></p>
            <div id="chart_satisfaction"></div>
        </div>

        <hr>

        <!-- Weather History -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_weather_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_weather_desc']; ?></p>
            <div id="chart_weather_history"></div>
        </div>

        <hr>

        <!-- Daily Visitor Count -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_visitor_count_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_visitor_count_desc']; ?></p>
            <div id="chart_visitor_count"></div>
        </div>

        <hr>

        <!-- Daily Revenue vs Expenses -->
        <div class="md:col-span-12">
            <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_revenue_expenses_title']; ?></h4>
            <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_revenue_expenses_desc']; ?></p>
            <div id="chart_revenue_expenses"></div>
        </div>

    </div>

    <input type="hidden" id="currentResortId" value="<?php echo $currentResortId; ?>">

    <?php endif; ?>
</div>
