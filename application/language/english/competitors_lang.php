<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['competitors']['title']   = 'Competitor Resorts';
$lang['competitors']['intro']   = 'Nearby AI-driven ski resorts compete with you for tourists every day. '
    . 'They run their own marketing campaigns, offer cheaper lift tickets and invest in mega lifts to lure skiers away from your resort. '
    . 'Monitor their activity and take action to protect your market share. '
    . 'Use <strong>Counter-Marketing</strong> to undercut their advertising efforts, or '
    . '<strong>Invest in a Mega Lift</strong> to make your infrastructure more attractive than theirs. '
    . 'The combined pressure from all competitors reduces your daily visitor count by the penalty shown below.';

// Penalty banner
$lang['competitors']['current_penalty_label'] = 'Current competitor pressure on your resort:';
$lang['competitors']['current_penalty_desc']  = 'visitor reduction per day from nearby competition.';
$lang['competitors']['no_penalty']            = 'Your resort currently faces no competitive pressure — great work!';

// Table columns
$lang['competitors']['col_name']            = 'Competitor';
$lang['competitors']['col_reputation']      = 'Reputation';
$lang['competitors']['col_ticket_price']    = 'Ticket Price';
$lang['competitors']['col_marketing']       = 'Marketing Campaign';
$lang['competitors']['col_ticket_discount'] = 'Ticket Discount';
$lang['competitors']['col_lift_investment'] = 'Mega Lift Level';
$lang['competitors']['col_actions']         = 'Actions';

// Buttons
$lang['competitors']['btn_counter_marketing']       = 'Counter Campaign';
$lang['competitors']['btn_counter_marketing_title'] = 'Run a counter-marketing campaign to reduce this competitor\'s advertising level';
$lang['competitors']['btn_mega_lift']               = 'Mega Lift';
$lang['competitors']['btn_mega_lift_title']         = 'Invest in a mega lift to outcompete this resort\'s infrastructure';

// Cost display
$lang['competitors']['cost_label']                  = 'Action costs:';
$lang['competitors']['cost_counter_marketing_label'] = 'Counter-marketing campaign:';
$lang['competitors']['cost_mega_lift_label']         = 'Mega lift investment:';

// Success messages
$lang['competitors']['success_counter_marketing'] = 'Counter-marketing campaign launched! The competitor\'s marketing level has been reduced.';
$lang['competitors']['success_mega_lift']          = 'Mega lift investment completed! The competitor\'s lift advantage has been reduced.';

// Error messages
$lang['competitors']['error_not_found']       = 'Competitor not found.';
$lang['competitors']['error_not_enough_cash'] = 'Not enough cash to perform this action.';
$lang['competitors']['error_action_failed']   = 'The action could not be completed. Please try again.';

// No competitors
$lang['competitors']['no_competitors'] = 'No competitor resorts assigned yet. Check back soon!';

// Activity log entries
$lang['competitors']['log_counter_marketing'] = 'Counter-marketing campaign launched against a competitor resort. Cost:';
$lang['competitors']['log_mega_lift']          = 'Mega lift investment made to outcompete a rival resort. Cost:';
