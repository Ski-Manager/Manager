<div class="w-full">
    <?php

// General page title
echo $title;
echo $introBank;

// start ONLY IF TOURIST INFO BUILT
if ($hideBank != true) { ?>        
    <!-- START bank Block -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-16 flex items-center justify-center"><?php echo $bankLogo;?></div>
                <div><?php echo $bankDesc;?></div>
            </div>
            <div class="mb-3"><?php echo $max_daily_payment_text;?></div>
            <div class="w-full">
             <?php   if (isset($infoMessage))
                        echo $this->lang->line($infoMessage);
             ?>
            <div class="overflow-x-auto">
                <table class="table building_6th" align="center">
                    <thead>
                        <tr>
                            <th class="md:col-span-3"></th>
                            <th class="md:col-span-3"><?php echo $bankName[0];?></th>
                            <th class="md:col-span-3"><?php echo $bankName[1];?></th>
                            <th class="md:col-span-3"><?php echo $bankName[2];?></th>
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
                            <th><div class="tooltip tooltip-bottom" style="display:inline;" data-tip="<?php echo $this->lang->line('genepis')['genepis_tooltip']; ?>"><?php echo $this->lang->line('bank')['genepis_required'];?> <a href="<?php echo base_url().'genepis_controller';?>"><img src="<?php echo base_url('img/icons/help.png'); ?>" alt="Help"></a></div></th>
                            <td><?php echo $genepis_required[0];?></td>
                            <td><?php echo $genepis_required[1];?></td>
                            <td><?php echo $genepis_required[2];?></td>
                        </tr>
                        <tr>
                            <th><label for="name" class="label"><?php echo $this->lang->line('bank')['amount_to_borrow'];?></label></th>
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
            </div>
            </div>
            <div id="ongoing_loans_table">
            <?php if ($ongoing_loans_display == true) {
                echo $body_ongoing_loans_table;
            } ?>
            </div>

            <!-- START Loan History -->
            <?php if ($loan_history_display == true) { ?>
            <div id="loan_history_table">
                <?php echo $body_loan_history_table; ?>
            </div>
            <?php } ?>
            <!-- END Loan History -->

            <!-- START Investment / Savings Account -->
            <div class="mt-4">
                <h3 class="h3"><?php echo $this->lang->line('bank')['investment_title'];?></h3>
                <p><?php echo $this->lang->line('bank')['investment_desc'];?></p>
                <div class="overflow-x-auto">
                <table class="table" align="center">
                    <tbody>
                        <tr>
                            <th class="md:col-span-4"><?php echo $this->lang->line('bank')['investment_annual_rate'];?></th>
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
                </div>
            </div>
            <!-- END Investment / Savings Account -->
    </div>
    <!-- END bank block -->
    <?php
     echo $this->session->flashdata('msg');
}
// end ONLY IF TOURIST INFO BUILT
// Info Messages related to the bank type
    if (isset($infoMessage) && $infoMessage == 'tourist_info_required')
        echo $this->lang->line('building')[$infoMessage];
    ?>

            

            
    <div id="dialog-confirm-signup_loan" style="display:none;">
        <?php echo $this->lang->line('bank')['confirm_signup_loan'];?>
        </div>
    <div id="dialog-confirm-payoff_loan" style="display:none;">
</div>
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
 