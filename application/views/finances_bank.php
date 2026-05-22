<div class="w-full">

    <!-- DaisyUI radio-based tab navigation -->
    <div class="tabs tabs-bordered mb-3" id="financeBankTab">

        <!-- ===== Finances tab ===== -->
        <label class="tab" id="finances-tab">
            <input type="radio" name="finance_bank_tabs" checked />
            <i class="fa-solid fa-euro-sign mr-1"></i><?php echo $this->lang->line('finances')['title']; ?>
        </label>
        <div class="tab-content" id="finances-panel">
            <?php
            echo $title;
            echo $introFinances;

            if ($resort_built) {
            ?>
            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
                <div class="w-full">
                    <div class="overflow-x-auto">
                    <table class="table table-zebra building" align="center">
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
                            </tr>
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
                    </div><!-- close overflow-x-auto -->

                    <div class="w-full">
                        <div id="dual_chart_revenues_expenses"></div>
                        <div id="single_chart_affluence"></div>
                        <div id="single_chart_reputation"></div>
                        <div id="single_chart_snow_level"></div>
                        <div id="pie_chart_revenues"></div>
                    </div>

                    <input type="hidden" id="currentResortId" value="<?php echo $currentResortId;?>">
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- ===== Bank tab ===== -->
        <label class="tab" id="bank-tab">
            <input type="radio" name="finance_bank_tabs" />
            <i class="fa-solid fa-building-columns mr-1"></i><?php echo $this->lang->line('bank')['titleMain']; ?>
        </label>
        <div class="tab-content" id="bank-panel">
            <?php
            echo $title_bank;
            echo $introBank;

            if ($hideBank != true) { ?>
            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
                <div class="w-1/6 flex items-center justify-center"> <?php echo $bankLogo;?> </div>
                <div class="w-5/6"> <?php echo $bankDesc;?>  </div>
                <div class="w-5/6 padding_top_bot_15"> <?php echo $max_daily_payment_text;?>  </div>
                <div class="w-full">
                    <?php if (isset($infoMessage)) echo $this->lang->line($infoMessage); ?>
                    <div class="overflow-x-auto">
                    <table class="table table-zebra building_6th" align="center">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?php echo $bankName[0];?></th>
                                <th><?php echo $bankName[1];?></th>
                                <th><?php echo $bankName[2];?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['min_loan'];?></th>
                                <td id="min_loan_0" data-min_loan="<?php echo $bankMinLoan_raw[0];?>"><?php echo $bankMinLoan[0];?> €</td>
                                <td id="min_loan_1" data-min_loan="<?php echo $bankMinLoan_raw[1];?>"><?php echo $bankMinLoan[1];?> €</td>
                                <td id="min_loan_2" data-min_loan="<?php echo $bankMinLoan_raw[2];?>"><?php echo $bankMinLoan[2];?> €</td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['max_loan'];?></th>
                                <td id="max_loan_0" data-max_loan="<?php echo $bankMaxLoan_raw[0];?>"><?php echo $bankMaxLoan[0];?> €</td>
                                <td id="max_loan_1" data-max_loan="<?php echo $bankMaxLoan_raw[1];?>"><?php echo $bankMaxLoan[1];?> €</td>
                                <td id="max_loan_2" data-max_loan="<?php echo $bankMaxLoan_raw[2];?>"><?php echo $bankMaxLoan[2];?> €</td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['interest_rate'];?></th>
                                <td><span id="interest_rate_0"><?php echo $bankInterestRate[0];?></span> %</td>
                                <td><span id="interest_rate_1"><?php echo $bankInterestRate[1];?></span> %</td>
                                <td><span id="interest_rate_2"><?php echo $bankInterestRate[2];?></span> %</td>
                            </tr>
                            <tr>
                                <th><div class="tooltip inline-block" data-tip="<?php echo $this->lang->line('genepis')['genepis_tooltip']; ?>"><?php echo $this->lang->line('bank')['genepis_required'];?> <a href="<?php echo base_url().'genepis_controller';?>"><img src="<?php echo base_url('img/icons/help.png'); ?>" alt="<?php echo $this->lang->line('genepis')['genepis_tooltip']; ?>"></a></div></th>
                                <td><?php echo $genepis_required[0];?></td>
                                <td><?php echo $genepis_required[1];?></td>
                                <td><?php echo $genepis_required[2];?></td>
                            </tr>
                            <tr>
                                <th><label for="to_borrow" class="label"><?php echo $this->lang->line('bank')['amount_to_borrow'];?></label></th>
                                <td colspan="3" class="overflow_visible">
                                    <input class="range w-full" id="to_borrow" type="range" min="100000" max="10000000" step="100000" value="500000"/>
                                    <span id="to_borrow_CurrentSliderValLabel"><span id="to_borrow_SliderVal"><?php echo number_format('500000', 0, ',', ' ');?> </span> €</span>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['loan_duration'];?></th>
                                <td colspan="3" class="overflow_visible">
                                    <input class="range w-full" id="loan_duration" type="range" min="7" max="270" step="1" value="70"/>
                                    <span id="loan_duration_CurrentSliderValLabel"><span id="loan_duration_SliderVal"><?php echo number_format('70', 0, ',', ' ');?></span> <?php echo $this->lang->line('home')['days'];?></span>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['daily_payment'];?></th>
                                <td><span id="daily_payment_0"></span></td>
                                <td><span id="daily_payment_1"></span></td>
                                <td><span id="daily_payment_2"></span></td>
                            </tr>
                            <tr>
                                <th></th>
                                <?php
                                echo $bankButton[0];
                                echo $bankButton[1];
                                echo $bankButton[2];
                                echo form_close();
                                ?>
                            </tr>
                        </tbody>
                    </table>
                    </div><!-- close overflow-x-auto -->
                </div>

                <div id="ongoing_loans_table">
                    <?php if ($ongoing_loans_display == true) echo $body_ongoing_loans_table; ?>
                </div>

                <?php if ($loan_history_display == true) { ?>
                <div id="loan_history_table">
                    <?php echo $body_loan_history_table; ?>
                </div>
                <?php } ?>

                <div class="w-full padding_top_bot_15">
                    <h3 class="h3"><?php echo $this->lang->line('bank')['investment_title'];?></h3>
                    <p><?php echo $this->lang->line('bank')['investment_desc'];?></p>
                    <div class="overflow-x-auto">
                    <table class="table table-zebra" align="center">
                        <tbody>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['investment_annual_rate'];?></th>
                                <td><?php echo $investment_annual_rate;?> %</td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['investment_balance'];?></th>
                                <td><span id="investment_balance_display"><?php echo $investment_balance_display;?></span> €</td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['investment_min_deposit'];?></th>
                                <td><?php echo $investment_min_deposit;?> €</td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bank')['investment_max_balance'];?></th>
                                <td><?php echo $investment_max_balance;?> €</td>
                            </tr>
                            <tr>
                                <th><label for="investment_amount" class="label"><?php echo $this->lang->line('bank')['investment_amount'];?></label></th>
                                <td class="overflow_visible">
                                    <input class="range w-full" id="investment_amount" type="range"
                                        min="<?php echo $investment_min_deposit_raw;?>"
                                        max="<?php echo $investment_max_balance_raw;?>"
                                        step="<?php echo $investment_min_deposit_raw;?>"
                                        value="<?php echo $investment_min_deposit_raw;?>"/>
                                    <span id="investment_amount_label"><span id="investment_amount_val"><?php echo $investment_min_deposit;?></span> €</span>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <a href="?action=deposit_investment" class="deposit_investment-dialog">
                                        <button class="btn btn-success"><?php echo $this->lang->line('bank')['investment_deposit_btn'];?></button>
                                    </a>
                                    &nbsp;
                                    <a href="?action=withdraw_investment" class="withdraw_investment-dialog"
                                       data-balance="<?php echo $investment_balance;?>">
                                        <button class="btn btn-warning"><?php echo $this->lang->line('bank')['investment_withdraw_btn'];?></button>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div><!-- close overflow-x-auto -->
                </div>
            </div>
            <?php
            echo $this->session->flashdata('msg');
            }
            if (isset($infoMessage) && $infoMessage == 'tourist_info_required')
                echo $this->lang->line('building')[$infoMessage];
            ?>

            <div id="dialog-confirm-signup_loan" style="display:none;">
                <?php echo $this->lang->line('bank')['confirm_signup_loan'];?>
            </div>
            <div id="dialog-confirm-payoff_loan" style="display:none;"></div>
            <div id="dialog-confirm-deposit_investment" style="display:none;"></div>
            <div id="dialog-confirm-withdraw_investment" style="display:none;"></div>

            <?php if ($hideBank != true) { ?>
            <script type="text/javascript">
            Settings.investment_deposit_label    = <?php echo json_encode($this->lang->line('bank')['investment_deposit_btn']); ?>;
            Settings.investment_withdraw_label   = <?php echo json_encode($this->lang->line('bank')['investment_withdraw_btn']); ?>;
            Settings.investment_confirm_deposit  = <?php echo json_encode($this->lang->line('bank')['investment_confirm_deposit']); ?>;
            Settings.investment_confirm_withdraw = <?php echo json_encode($this->lang->line('bank')['investment_confirm_withdraw']); ?>;
            Settings.investment_success          = <?php echo json_encode($this->lang->line('bank')['investment_success']); ?>;
            Settings.investment_error            = <?php echo json_encode($this->lang->line('bank')['investment_error']); ?>;
            Settings.investment_min_deposit_raw  = <?php echo (int)$investment_min_deposit_raw; ?>;
            Settings.investment_max_balance_raw  = <?php echo (int)$investment_max_balance_raw; ?>;
            Settings.investment_balance          = <?php echo (int)$investment_balance; ?>;
            </script>
            <?php } ?>
        </div>

    </div><!-- .tabs -->
</div>
