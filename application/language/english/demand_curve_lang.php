<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['demand_curve']['title']                  = 'Dynamic Demand Curve';
$lang['demand_curve']['intro']                  = 'The number of visitors to your resort each day is driven by several factors. Understanding this demand curve allows you to set the right skipass price during peak periods to maximise revenue.';

$lang['demand_curve']['current_factors']        = 'Today\'s Demand Factors';
$lang['demand_curve']['factor']                 = 'Factor';
$lang['demand_curve']['value']                  = 'Current value';
$lang['demand_curve']['multiplier']             = 'Multiplier';
$lang['demand_curve']['effect']                 = 'Effect on visitors';

$lang['demand_curve']['weather']                = 'Weather';
$lang['demand_curve']['reputation']             = 'Reputation';
$lang['demand_curve']['peak_season']            = 'Season / Holidays';
$lang['demand_curve']['price']                  = 'Skipass price';
$lang['demand_curve']['competition']            = 'Competition';

$lang['demand_curve']['day_of_season']          = 'Day';
$lang['demand_curve']['unknown']                = 'Unknown';

$lang['demand_curve']['price_in_slope_calc']    = 'Built-in';
$lang['demand_curve']['price_tooltip']          = 'The price coefficient is applied directly in the slope visitor calculation. Lower prices attract more visitors; higher prices reduce demand.';
$lang['demand_curve']['coming_soon']            = 'Coming soon';
$lang['demand_curve']['competition_tooltip']    = 'Competitor resorts will reduce your visitor share. This factor will be active once the Competitor Resorts system is enabled.';

$lang['demand_curve']['combined_multiplier']    = 'Combined demand multiplier (weather × reputation × season)';
$lang['demand_curve']['combined_note']          = 'Price coefficient and crowding cap are applied separately in slope calculations.';

$lang['demand_curve']['daily']                  = 'Daily';
$lang['demand_curve']['weekly']                 = 'Weekly';

// Peak-season schedule
$lang['demand_curve']['season_schedule']        = 'Peak-Season Demand Schedule';
$lang['demand_curve']['season_schedule_intro']  = 'Visitor demand follows a realistic ski-resort curve across the 135-day season. Plan your skipass pricing around these periods to maximise revenue.';

$lang['demand_curve']['period']                 = 'Period';
$lang['demand_curve']['days']                   = 'Days';
$lang['demand_curve']['status']                 = 'Status';
$lang['demand_curve']['current']                = 'Current';

$lang['demand_curve']['season_opening']         = 'Season opening';
$lang['demand_curve']['shoulder_period']        = 'Shoulder period';
$lang['demand_curve']['christmas_peak']         = 'Christmas / New Year peak';
$lang['demand_curve']['post_christmas']         = 'Post-Christmas lull';
$lang['demand_curve']['feb_holidays']           = 'February school holidays';
$lang['demand_curve']['late_season']            = 'Late season';
$lang['demand_curve']['season_closing']         = 'Season closing';

$lang['demand_curve']['pricing_tip']            = '💡 Tip: Raise your skipass price during Christmas peak (days 41–50) and February school holidays (days 61–90) to maximise revenue when demand is high.';
