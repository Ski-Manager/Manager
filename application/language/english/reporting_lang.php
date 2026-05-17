<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Reporting view
$lang['reporting']['intro']	= 'On this page you can order a complete analysis of your resort. A group of experts will produce a concise report and inform you about all possible improvements to improve its profitability.<br>After the order is placed, the report will be available on the next day (after 01:00am GMT time) and be available forever.';
$lang['reporting']['cost_report']	= 'The cost for one report is';
$lang['reporting']['example_below']	= 'You can find an example of a report here: ';
$lang['reporting']['order_report']	= 'You can order today\'s analysis by clicking on the button below';
$lang['reporting']['order']	= 'Order';
$lang['reporting']['report_ordered']	= '<div class="alert alert-success text-center">Analysis successfully ordered! Come back after 01:00am GMT to view it.</div>';
$lang['reporting']['report_not_ordered']	= '<div class="alert alert-danger text-center">The analysis couldn\'t be ordered! Try again or contact us at contact@ski-manager.net.</div>';
$lang['reporting']['not_enough_genepis']	= '<div class="alert alert-warning text-center">You do not have enough Génépis to order the analysis. Get more Génépis from the <a href="'.base_url().'genepis_controller">Génépis page</a>.</div>';
$lang['reporting']['already_ordered_today']	= '<div class="alert alert-warning text-center">You have already ordered an analysis today. Come back after 01:00am GMT to view the report.</div>';
$lang['reporting']['date']	= 'Date';
$lang['reporting']['status']	= 'Status';
$lang['reporting']['view']	= 'View';
$lang['reporting']['download']	= 'Download';
$lang['reporting']['or']	= 'or';
$lang['reporting']['resort_analysis']	= 'Resort analysis';


// Night main scripts
$lang['reporting']['avg_quality_slopes']	= 'Average quality of the slopes deserving the lift';
$lang['reporting']['is_only']                   = 'is only';
$lang['reporting']['improve_their_quality']	= 'Improve their quality to attract more tourists to this lift';
$lang['reporting']['no_slopes_connected']	= 'There are no slopes connected to the lift';
$lang['reporting']['build_more_slopes']         = 'Build some more slopes close to this lift to attract more tourists.';
$lang['reporting']['too_few_slopes_connected']	= 'There are too few slopes connected to the lift';
$lang['reporting']['adequate_slopes_connected']	= 'There is an adequate number of slopes connected to the lift';
$lang['reporting']['build_few_slopes']          = 'Build a few more slopes close to this lift to attract even more tourists.';
$lang['reporting']['not_necessary_slopes']	= 'It is not necessary to build more slopes close to this lift.';
$lang['reporting']['unknown_error_lift']	= 'Unknown error with lift';
$lang['reporting']['the_lift']                  = 'With the number of connected slopes and current level, the lift';
$lang['reporting']['could_attract_max']                  = 'can handle up to';
$lang['reporting']['tourists']                  = 'tourists';
$lang['reporting']['also_includes']                  = 'This also includes tourists coming for the day only.';
$lang['reporting']['all_your_lifts']                  = 'All your lifts';
$lang['reporting']['your_resort_hotel_capacity']                  = 'Your resort has a housing capacity of';
$lang['reporting']['thanks_to_access_resort_housing_capacity']                  = 'Thanks to the bonus given by the access to your resort, the actual carrying capacity is';
$lang['reporting']['no_hotel_in_resort']                  = 'You have no hotels in your resort, this is heavily limiting your ability to attract tourists.';
$lang['reporting']['lift_capacity_too_low']                  = 'Your lifts are not able to handle all the tourists visiting your resort for the day or staying in your hotels. Increase your lift capacity.';
$lang['reporting']['infrastructure_capacity_too_low']                  = 'Your lifts could handle more tourists but the current number of tourists is limited by the infrastructure in your resort. Build more hotels, upgrade the existing ones or improve the access to your resort to attract more tourists.';
$lang['reporting']['no_open_lifts']                  = 'There are no open lifts in your resort. Open more lifts to attract tourists';
$lang['reporting']['no_slopes_deserving']                  = 'There are no slopes deserved by lift';
$lang['reporting']['build_more_slopes']                  = 'Build more slopes around this lift to increase revenues.';
$lang['reporting']['risk_injury_slope']                  = 'There is a risk of injury of';
$lang['reporting']['on_slope']                  = 'on slope';
$lang['reporting']['reduce_injuries_tip']                  = 'Improve the quality of the slope by making sure you have an efficient ski patrol allocated to it and that there is sufficient snow in your resort. Assigning up to '.MAX_PATROL_PER_SLOPE.' ski patrols per slope provides a safety bonus.';
$lang['reporting']['danger_today']                  = 'Yesterday\'s weather was too dangerous to go skiing and this resulted in three times more injuries than usual. Make sure you close your resort when the weather is bad or subscribe to Extended Forecast to get the auto-close function. See the Weather page for more info.';
$lang['reporting']['your_skibus_can_only']                  = 'Your skibuses can only handle';
$lang['reporting']['your_skibus_can_handle']                  = 'Your skibuses can handle';
$lang['reporting']['perc_of_tourists']                  = '% of the tourists in your resort.';
$lang['reporting']['buy_more_skibuses']                 = 'Buy more skibuses or upgrade existing ones to earn more money.';
$lang['reporting']['enough_skibuses']                  = 'You have enough skibuses to handle all the tourists in your resort.';
$lang['reporting']['no_driver_or_skibus']                  = 'You don\'t have any skibus with a driver assigned. Buy at least one skibus and hire one driver. Then assign the driver to the skibus on the staff overview page.';
$lang['reporting']['no_instructors']                  = 'You don\'t have any ski instructor assigned to a sector of your resort. Hire at least one ski/Snowboard instructor and assign it to a sector of your resort on the staff overview page.';
$lang['reporting']['avg_efficiency_drivers']                  = 'The average efficiency of your bus drivers is only';
$lang['reporting']['get_better_drivers']                  = 'Hire more efficient drivers if you want to increase the revenues generated by your skibuses.';
$lang['reporting']['avg_efficiency_instructors']                  = 'The average efficiency of your ski/snowboard instructors is only';
$lang['reporting']['get_better_instructors']                  = 'Hire more efficient ski/snowboard instructors if you want to increase the revenues generated by the ski schools.';
$lang['reporting']['no_building_type']                  = 'You don\'t have any';
$lang['reporting']['building_buildings']                  = 'Increase your revenues by building such building.';
$lang['reporting']['enough_building_type']                  = 'You have enough';
$lang['reporting']['not_enough_building_type']                  = 'Your buildings of type';
$lang['reporting']['of_your_visitors']                  = 'of your visitors.';
$lang['reporting']['can_only_handle']                  = 'can only handle';
$lang['reporting']['build_some_more']                  = 'Build some more to handle all the tourists in your resort and increase revenues.';
$lang['reporting']['to_handle_visitors']                  = 'to handle all the tourists in your resort.';
$lang['reporting']['or_no_visitors']                  = 'or there were no visitors in your resort yesterday.';

// Reporting script

$lang['reporting']['resort_closed']                  = 'Your resort was closed yesterday, thus no tourists has been visiting your resort. Open your Tourist Information Center at the Access & Parkings page to open your resort.';
$lang['reporting']['there_is_only']                  = 'There is only';
$lang['reporting']['low_snow_level']                  = 'cm of snow in your resort and the quality of you slopes may decrease. Build more snow cannons to make sure the snow level stays high.';
$lang['reporting']['your_lift']                  = 'The lift';
$lang['reporting']['your_slope']                  = 'The slope';
$lang['reporting']['has_low_condition']                  = 'has a low condition';
$lang['reporting']['not_operate_well']                  = 'and does not operate at its maximum capacity. Repair the lift by clicking on it at the Resort page and then choose "repair".';
$lang['reporting']['more_injuries']                  = 'and will not attract as many tourists as a slope in good condition. Injuries also tend to happen more on such slopes. Try to increase the slope quality by hiring better snow groomer drivers or buying/upgrading snow cannons (if the snow level in the resort is low).';
$lang['reporting']['is_closed']                  = 'was closed yesterday. Open it to attract tourists and generate more revenues.';

$lang['reporting']['report_intro_text']                  = 'A group of experts has assessed your resort to help you improve its profitability and reputation. You can find the different topics and comments below:';
$lang['reporting']['type']                  = 'Type';
$lang['reporting']['comments']                  = 'Comments';
$lang['reporting']['report_for']                  = 'report for';
$lang['reporting']['produced_on']                  = 'produced on';

$lang['reporting']['up_to']                  = 'Up to';
$lang['reporting']['can_be_handled_lifts_downhil_slopes']                  = 'can be handled by your lifts snowparks, bordercrosses, downhill slopes and luge slopes.';
$lang['reporting']['have_used_cross_country_slopes']                  = 'have been skiing on your cross-country slopes.';
$lang['reporting']['no_cross_country_clopes']                  = 'You have no cross-country slopes in your resort. If you have unlocked new slope types, build some to attract tourists and generate more revenues.';


$lang['reporting']['reporting_wiki']                  = 'For more information and the list of analyzed metrics, check the <a href="'.base_url().'reporting_controller">reporting page</a>.';

// AI Guest Flow Simulation
$lang['reporting']['guest_flow_snow_factor']            = 'AI Guest Flow – snow level factor applied:';
$lang['reporting']['guest_flow_restaurant_factor']      = 'Restaurant proximity factor applied:';
$lang['reporting']['black_diamond_injury_warning']    = 'Black Diamond slope "%s" has a higher injury risk due to its extreme difficulty. Ensure you have an efficient ski patrol assigned.';
$lang['reporting']['black_diamond_rep_bonus']         = 'Your resort earned %d reputation points from %d open Black Diamond slope(s) attracting expert guests.';
// Natural hazards
$lang['reporting']['avalanche_risk_steep']     = 'Avalanche warning: steep slope';
$lang['reporting']['avalanche_condition_loss'] = 'lost condition due to an avalanche. Hire ski patrol and ensure adequate snowpack management.';
$lang['reporting']['storm_damaged_lift']       = 'Storm damage: lift';
$lang['reporting']['storm_condition_loss']     = 'lost condition due to storm damage. Assign a mechanic to keep the lift in good shape.';
$lang['reporting']['ice_slopes_affected']      = 'Freezing conditions caused ice accumulation on slopes, reducing their condition. Make sure snow cannons are operational to maintain adequate snowpack.';
