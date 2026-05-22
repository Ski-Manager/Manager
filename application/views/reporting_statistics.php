<div class="w-full">

    <!-- DaisyUI radio-based tabs -->
    <div class="tabs tabs-bordered mb-3" id="reportingStatsTab">

        <!-- ===== Resort Analysis tab ===== -->
        <label class="tab" id="reporting-tab">
            <input type="radio" name="reporting_stats_tabs" checked />
            <i class="fa-solid fa-chart-bar mr-1"></i><?php echo $this->lang->line('home')['reporting_title']; ?>
        </label>
        <div class="tab-content" id="reporting-panel">
            <h2 class="h2"><?php echo $this->lang->line('home')['reporting_title']; ?></h2>
            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
                <?php
                echo $this->lang->line('reporting')['intro'].'<br><br>';
                echo $this->lang->line('reporting')['cost_report'].' '.COST_GENEPIS_REPORT.' '.$this->lang->line('navbar')['genepis'].'.<br>';
                echo $this->lang->line('reporting')['example_below'];
                echo anchor(base_url().'files/reports/3d397ea1-457e-44da-9003-fb4357cb0f17.pdf', 'View example').'<br><br>';
                echo $this->lang->line('reporting')['reporting_wiki'].'<br><br>';
                echo $this->lang->line('reporting')['order_report'].'<br><br>';
                echo '<button id="order_report" class="btn btn-lg btn-primary">'.$this->lang->line('reporting')['order'].'</button>';
                echo '<p/>';
                foreach ($list_reports->result() as $list_reports_data) {
                    echo '<b>'.$this->lang->line('reporting')['date'].':</b> '.$list_reports_data->date.' ';
                    echo '<b>'.$this->lang->line('reporting')['status'].':</b> '.$list_reports_data->status.' ';
                    if ($list_reports_data->status == 'created') {
                        echo '<a href="'.base_url().'files/reports/'.$list_reports_data->uuid_report.'.pdf" class="btn btn-primary">'.$this->lang->line('reporting')['view'].'</a> ';
                    }
                    echo '<br><br>';
                }
                ?>
                <div id="result_order"></div>
            </div>
        </div>

        <!-- ===== Statistics tab ===== -->
        <label class="tab" id="stats-tab">
            <input type="radio" name="reporting_stats_tabs" />
            <i class="fa-solid fa-chart-pie mr-1"></i><?php echo $this->lang->line('statistics')['title']; ?>
        </label>
        <div class="tab-content" id="stats-panel">
            <h2 class="h2"><?php echo $this->lang->line('statistics')['title']; ?></h2>
            <p><?php echo $this->lang->line('statistics')['intro']; ?></p>

            <?php if ($resort_built): ?>
            <div class="card bg-base-100 shadow-sm"><div class="card-body">

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_lift_usage_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_lift_usage_desc']; ?></p>
                    <div id="chart_lift_usage"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_revenue_per_lift_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_revenue_per_lift_desc']; ?></p>
                    <div id="chart_revenue_per_lift"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_slope_popularity_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_slope_popularity_desc']; ?></p>
                    <div id="chart_slope_popularity"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_satisfaction_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_satisfaction_desc']; ?></p>
                    <div id="chart_satisfaction"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_weather_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_weather_desc']; ?></p>
                    <div id="chart_weather_history"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_visitor_count_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_visitor_count_desc']; ?></p>
                    <div id="chart_visitor_count"></div>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="h4"><?php echo $this->lang->line('statistics')['chart_revenue_expenses_title']; ?></h4>
                    <p class="text-base-content/60"><?php echo $this->lang->line('statistics')['chart_revenue_expenses_desc']; ?></p>
                    <div id="chart_revenue_expenses"></div>
                </div>

            </div>

            <input type="hidden" id="currentResortId" value="<?php echo $currentResortId; ?>">

            <?php endif; ?>
        </div>

    </div><!-- .tabs -->
</div>
