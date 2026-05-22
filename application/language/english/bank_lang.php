<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');


$lang['bank']['titleMain']		= 'Bank';
$lang['bank']['bank_name']		= 'Bank name';
$lang['bank']['intro']                  = 'On this page you can get some extra cash to help your resort develop faster.';
$lang['bank']['desc']                   = 'You can borrow money from a bank. You can choose the duration of the loan as well as the amount to borrow. Make sure you can afford it! It is possible to payoff a loan earlier, assuming you have sufficient funds.';
$lang['bank']['min_loan']		= 'Minimum loan';
$lang['bank']['max_loan']		= 'Maximum loan';
$lang['bank']['interest_rate']		= 'Interest rate';
$lang['bank']['genepis_required']		= 'Génépis required';
$lang['bank']['daily_payment']          = 'Daily payment';
$lang['bank']['amount_to_borrow']	= 'Amount to borrow';
$lang['bank']['loan_duration']		= 'Loan duration';
$lang['bank']['sign_up']		= 'Sign up';
$lang['bank']['ongoing_loans']		= 'Ongoing loans';
$lang['bank']['signed_on']		= 'Signed on';
$lang['bank']['left_to_pay']		= 'Left to pay';
$lang['bank']['last_payment_date']		= 'Last payment date';
$lang['bank']['sign_up_tooltip']	= 'Sign up for a loan with this bank';
$lang['bank']['amount_too_low']		= 'Borrowing too little for this bank';
$lang['bank']['amount_too_high']	= 'Borrowing too much for this bank';
$lang['bank']['confirm_signup_loan']            = 'Are you sure you want to sign up for this loan?';
$lang['bank']['confirm_signup_do_you_want']	= 'Do you want to sign a loan with';
$lang['bank']['with_daily_payment']	= 'with a daily payment of';
$lang['bank']['during']                 = 'during';
$lang['bank']['loan_signed_up']                 = 'The loan has been signed up.';
$lang['bank']['loan_not_signed_up']                 = 'The loan couldn\'t be signed. Try again or contact us at '.CONST_ADMIN_EMAIL;
$lang['bank']['not_enough_revenue']                 = 'Your current revenues are too low to subscribe to this offer. In order to prevent indebtedness, the loan has not been signed. Try again when your revenues are higher or lower the daily payment';
$lang['bank']['ongoing_loans_error']                 = 'You can only subscribe to one standard loan at a time or choose one to two loans from the VIP bank (requires <a href="'.base_url().'genepis_controller">Génépis</a>). There is also a maximum of three simultaneous loans (1 standard + 2 VIP or 3 VIP).';

$lang['bank']['payoff']                 = 'Pay off';
$lang['bank']['payoff_now']                 = 'Pay off now';
$lang['bank']['payoff_help']                 = 'Paying off this loan immediately will cost you';
$lang['bank']['confirm_payoff_do_you_want']                 = 'Are you sure you want to pay off this loan earlier?';
$lang['bank']['will_be_directly_taken']                 = 'will be directly taken from your account.';
$lang['bank']['not_enough_money_payoff']                 = 'You don\'t have enough money to pay off this loan today. You need';
$lang['bank']['for_this_action']                 = 'for this action.';
$lang['bank']['loan_not_payed_off']                 = 'The loan couldn\'t be paid off right now. Try again or contact us at '.CONST_ADMIN_EMAIL;
$lang['bank']['based_last_week_profit']                 = 'Based on your last week profit, you can borrow money with a maximum daily payment of';

// Loan history
$lang['bank']['loan_history']       = 'Loan history';
$lang['bank']['borrowed_amount']    = 'Borrowed amount';
$lang['bank']['reimbursed_on']      = 'Reimbursed on';

// Investment / savings account
$lang['bank']['investment_title']            = 'Savings account';
$lang['bank']['investment_desc']             = 'Deposit idle cash into a savings account and earn daily interest. Withdraw at any time.';
$lang['bank']['investment_annual_rate']      = 'Annual interest rate';
$lang['bank']['investment_balance']          = 'Current balance';
$lang['bank']['investment_min_deposit']      = 'Minimum deposit';
$lang['bank']['investment_max_balance']      = 'Maximum balance';
$lang['bank']['investment_amount']           = 'Amount';
$lang['bank']['investment_deposit_btn']      = 'Deposit';
$lang['bank']['investment_withdraw_btn']     = 'Withdraw';
$lang['bank']['investment_confirm_deposit']  = 'Are you sure you want to deposit';
$lang['bank']['investment_confirm_withdraw'] = 'Are you sure you want to withdraw';
$lang['bank']['investment_success']          = 'Operation successful.';
$lang['bank']['investment_error']            = 'Operation failed. Please try again.';
$lang['bank']['investment_deposited']        = 'Deposited into savings account:';
$lang['bank']['investment_withdrawn']        = 'Withdrawn from savings account:';
$lang['bank']['investment_interest_log']     = 'Daily interest earned on savings:';
$lang['bank']['investment_min_deposit_error']= 'The amount is below the minimum deposit.';
$lang['bank']['investment_max_balance_error']= 'This deposit would exceed the maximum allowed balance.';
$lang['bank']['investment_withdraw_error']   = 'Invalid withdrawal amount.';

