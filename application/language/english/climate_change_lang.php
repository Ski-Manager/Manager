<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['climate_change']['title']                  = 'Climate Change';
$lang['climate_change']['intro']                  = 'As the years pass, climate change affects your resort. Monitor the warming trend and invest in adaptation strategies to stay competitive.';
$lang['climate_change']['climate_change']         = 'Climate Change';
$lang['climate_change']['climate_level_label']    = 'Climate Level';
$lang['climate_change']['current_season_label']   = 'Current Season';

// Level description (0-3+ maps to descriptors shown in the card)
$lang['climate_change']['level_desc_0']           = 'Climate conditions are normal. No significant impact on your resort yet.';
$lang['climate_change']['level_desc_1']           = 'Mild warming detected. Winters are slightly warmer and each season is 2 days shorter. Keep an eye on your snow levels.';
$lang['climate_change']['level_desc_2']           = 'Moderate warming. Natural snowfall is reduced and snowmaking costs are rising.';
$lang['climate_change']['level_desc_3']           = 'Severe climate stress. Glaciers are retreating, seasons are shortening. Investment in adaptation is strongly recommended.';

// Effects
$lang['climate_change']['active_effects']         = 'Active Effects';
$lang['climate_change']['effect_snow_penalty']    = 'Natural snow reduction';
$lang['climate_change']['effect_cost_mult']       = 'Snowmaking cost multiplier';
$lang['climate_change']['effect_glacier']         = 'Glacier loss (slope degradation)';
$lang['climate_change']['effect_season']          = 'Shorter season';
$lang['climate_change']['no_effect']              = 'None';

// Adaptation investments
$lang['climate_change']['adaptation_title']       = 'Adaptation Strategies';
$lang['climate_change']['adaptation_intro']       = 'Invest in the following strategies to reduce the impact of climate change on your resort.';

$lang['climate_change']['snowmaking_invest_label'] = 'Expand Snowmaking';
$lang['climate_change']['snowmaking_invest_desc']  = 'Invest in high-efficiency snowmaking infrastructure. Reduces the snowmaking cost penalty by 50%.';

$lang['climate_change']['altitude_invest_label']   = 'Move Higher';
$lang['climate_change']['altitude_invest_desc']    = 'Develop higher-altitude terrain to access more reliable snow. Reduces natural snow penalty by 50%.';

$lang['climate_change']['diversify_invest_label']  = 'Diversify Revenue';
$lang['climate_change']['diversify_invest_desc']   = 'Invest in non-ski activities (summer tourism, events, wellness). Reduces the overall visitor impact by 50% when climate effects are active.';

$lang['climate_change']['cost']                   = 'Cost';
$lang['climate_change']['invest_btn']             = 'Invest';
$lang['climate_change']['invested']               = 'Invested';
$lang['climate_change']['invested_in']            = 'Invested in';

// Messages
$lang['climate_change']['invest_success']         = 'Investment successful! Your adaptation strategy is now active.';
$lang['climate_change']['invest_failed']          = 'Investment failed. Please try again.';
$lang['climate_change']['already_invested']       = 'You have already made this investment.';
$lang['climate_change']['not_enough_cash']        = 'You do not have enough cash for this investment.';
$lang['climate_change']['invalid_invest']         = 'Invalid investment type.';

// Notification logs
$lang['climate_change']['climate_level_up']       = 'Climate level increased to';
$lang['climate_change']['climate_effects_msg']    = 'Climate change is affecting your resort. Check the Climate Change page for details.';
$lang['climate_change']['season_shorter']         = 'Due to climate change, this season is shorter.';
