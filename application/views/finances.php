<div class="w-full">
    <?php

// General page title
echo $title;
echo $introFinances;
    
    if ($resort_built) {
?>     
    <!-- START Finance table Block -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="overflow-x-auto">
            <table class="table building" align="center">
                    <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('finances')['revenues'];?></th>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['category'];?></th>
                            <th><?php echo $this->lang->line('finances')['yesterday'];?></th>
                            <th><?php echo $this->lang->line('finances')['last7days'];?></th>
                            <th><?php echo $this->lang->line('finances')['season'];?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="border-t-left-radius: 0px;"><?php echo $this->lang->line('finances')['skipass'];?></th>
                            <td><?php echo number_format($yesterday_rev_skipass, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_skipass, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_skipass, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['skibus'];?></th>
                            <td><?php echo number_format($yesterday_rev_skibus, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_skibus, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_skibus, 0, ',', ' ');?> €</td>
                        </tr
                        <tr>
                            <th><?php echo $this->lang->line('finances')['instructor'];?></th>
                            <td><?php echo number_format($yesterday_rev_instructor, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_instructor, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_instructor, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['parking'];?></th>
                            <td><?php echo number_format($yesterday_rev_parking, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_parking, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_parking, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th style="border-b-left-radius: 0px;"><?php echo $this->lang->line('finances')['hotel'];?></th>
                            <td><?php echo number_format($yesterday_rev_hotel, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_hotel, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_hotel, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['restaurant'];?></th>
                            <td><?php echo number_format($yesterday_rev_restaurant, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_restaurant, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_restaurant, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['leisure'];?></th>
                            <td><?php echo number_format($yesterday_rev_leisure, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_leisure, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_leisure, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['luxury'];?></th>
                            <td><?php echo number_format($yesterday_rev_luxury, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_luxury, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_luxury, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['medical'];?></th>
                            <td><?php echo number_format($yesterday_rev_medical, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_medical, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_medical, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['rental'];?></th>
                            <td><?php echo number_format($yesterday_rev_rental, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_rental, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_rental, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['real_estate'];?></th>
                            <td><?php echo number_format($yesterday_rev_real_estate ?? 0, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_real_estate ?? 0, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_real_estate ?? 0, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['loan'];?></th>
                            <td><?php echo number_format($yesterday_rev_loan, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_loan, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_loan, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['achievements'];?></th>
                            <td><?php echo number_format($yesterday_rev_achievements, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_achievements, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_achievements, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['marketing'];?></th>
                            <td><?php echo number_format($yesterday_rev_marketing, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_marketing, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_marketing, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['tournaments'];?></th>
                            <td><?php echo number_format($yesterday_rev_tournaments, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_tournaments, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_tournaments, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['other'];?></th>
                            <td><?php echo number_format($yesterday_rev_other, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_other, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_other, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['off_season'];?></th>
                            <td><?php echo number_format($yesterday_rev_off_season, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_off_season, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_off_season, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['idle_income'] ?? 'Idle Income';?></th>
                            <td><?php echo number_format($yesterday_rev_idle ?? 0, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_rev_idle ?? 0, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_rev_idle ?? 0, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['total_income'];?></th>
                            <td><?php echo number_format($yesterday_revenue, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_revenue, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_revenue, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('finances')['expenses'];?></th>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['salaries'];?></th>
                            <td><?php echo number_format($yesterday_cost_salaries, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_salaries, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_salaries, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['upkeep'];?></th>
                            <td><?php echo number_format($yesterday_cost_upkeep, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_upkeep, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_upkeep, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['purchases'];?></th>
                            <td><?php echo number_format($yesterday_cost_purchases, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_purchases, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_purchases, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['loan'];?></th>
                            <td><?php echo number_format($yesterday_cost_loans, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_loans, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_loans, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['taxes'];?></th>
                            <td><?php echo number_format($yesterday_cost_taxes, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_taxes, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_taxes, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['marketing'];?></th>
                            <td><?php echo number_format($yesterday_cost_marketing, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_marketing, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_marketing, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['tournaments'];?></th>
                            <td><?php echo number_format($yesterday_cost_tournaments, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_cost_tournaments, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_cost_tournaments, 0, ',', ' ');?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('finances')['total_expenses'];?></th>
                            <td><?php echo number_format($yesterday_expenses, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($last7days_expenses, 0, ',', ' ');?> €</td>
                            <td><?php echo number_format($season_expenses, 0, ',', ' ');?> €</td>
                        </tr>
                    </tbody>
                </table>
        </div>
      <!-- END Finance table block -->          

    <!-- START Finance graph -->
            <div id="dual_chart_revenues_expenses"></div>
                <div id="single_chart_affluence"></div>
                <div id="single_chart_reputation"></div>
                <div id="single_chart_snow_level"></div>
                <div id="pie_chart_revenues"></div>
    <!-- END Finance graph -->
    <input type="hidden" id="currentResortId" value="<?php echo $currentResortId;?>" >
    </div>
    <?php } ?>
</div>
