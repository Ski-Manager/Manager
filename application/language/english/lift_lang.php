<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['lift']['name']		= 'Lift';
$lang['lift']['diff_type_column']		= 'Type';
$lang['lift']['length_speed_column']		= 'Speed';
$lang['lift']['throughput']		= 'Throughput';
$lang['lift']['capacity_seats']		= 'Capacity (seats)';
$lang['lift']['speed_unit']		= 'm/s';
$lang['lift']['throughput_unit']		= 'skiers per hour';
$lang['lift']['build_info']		= 'Lift information:';
$lang['lift']['build_title']		= 'Building a new lift in sector ';
$lang['lift']['build']		= 'Build lift';
$lang['lift']['bad_level']		= '<div class="alert alert-danger text-center">You can\'t upgrade to that level.</div>';
$lang['lift']['bad_status']		= '<div class="alert alert-danger text-center">You can\'t upgrade during while being on maintenance or while building.</div>';
$lang['lift']['lift_upgraded']		= '<div class="alert alert-success text-center">The lift upgrade has started.</div>';
$lang['lift']['lift_built']		= '<div class="alert alert-success text-center">The lift construction has started.</div>';
$lang['lift']['lift_edit_successful']		= '<div class="alert alert-success text-center">Lift edited successfully</div>';
$lang['lift']['lift_built_failed']		= '<div class="alert alert-danger text-center">The lift construction couldn\'t start. Try again or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['lift_upgraded_failed']		= '<div class="alert alert-danger text-center">The lift upgrade couldn\'t start. Try again or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['repair_failed']		= '<div class="alert alert-danger text-center">The lift repair couldn\'t start. Try again or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['not_found']		= '<div class="alert alert-danger text-center">The lift has not been found. Try again or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['no_deserving_lift']		= 'There are no open lifts serving this slope';
$lang['lift']['and']		= ' and ';
$lang['lift']['no_assigned_skipatrol']		= 'There are no ski patrols assigned to this slope';
$lang['lift']['no_deserving_mechanic']		= 'There are no mechanics assigned to this lift';
$lang['lift']['assigned_skipatrol_sing']	= 'There is one ski patrol assigned to this slope';
$lang['lift']['deserving_lift_sing']		= 'There is one lift serving this slope';
$lang['lift']['deserving_lift_part1_plur']		= 'There are ';
$lang['lift']['deserving_mechanic_sing']		= ' mechanic assigned to this lift';
$lang['lift']['assigned_skipatrol_part2_plur']		= ' ski patrols assigned to this slope';
$lang['lift']['deserving_lift_part2_plur']		= ' lifts serving this slope';
$lang['lift']['deserving_mechanic_part2_plur']		= ' mechanics assigned to this lift';
$lang['lift']['name_edit']		= 'Lift name:';
$lang['lift']['name_changed']		= '<div class="alert alert-success text-center">Name updated.</div>';
$lang['lift']['repaired']		= 'The lift has been placed in maintenance mode while it is being repaired.';
$lang['lift']['no_mechanics']		= 'There are no lift mechanics assigned to this lift. You need to assign a mechanic in order to repair it.';
$lang['lift']['ongoing_construction_lift']		= '<div class="alert alert-warning text-center">There is another lift being built or repaired right now. You cannot build more than one lift at a time.</div>';
$lang['lift']['out_of_order']		= 'Out of order';
$lang['lift']['out_of_order_text']		= 'This lift condition is below 20% and cannot operate. Make sure a lift mechanic is assigned to this lift and repair it.';
$lang['lift']['repair']		= 'Repair';
$lang['lift']['no_mechanics_assigned']		= 'There are no lift mechanics assigned to this lift. Assign a lift mechanic in order to repair the lift. You can do so from the staff overview page.';
$lang['lift']['confirm_destroy_item_part1']           = 'Do you want to repair this lift for ';
$lang['lift']['confirm_destroy_item_part2']           = '€? The maintenance is going to take 6 hours. After the maintenance, the lift will be automatically closed and you will need to open it again.';
$lang['lift']['repair_lift']           = 'Repair lift';
$lang['lift']['choose_name']		= 'Choose a name for your new lift';
$lang['lift']['grip_type']		= 'Grip type';


$lang['lift']['rush_completed']		= '<div class="alert alert-success text-center">You have successfully rushed the construction/upgrade of the lift.</div>';
$lang['lift']['already_completed']		= '<div class="alert alert-warning text-center">The construction/upgrade is already completed.</div>';
$lang['lift']['not_enough_genepis']		= '<div class="alert alert-warning text-center">You don\'t have enough Génépis to perform this action.</div>';


$lang['lift']['no_lift_or_patrol_needed_slope_type']		= 'No lifts or ski patrols are needed for this type of slope.';

$lang['lift']['modular_upgrades_title']  = 'Modular Upgrades';
$lang['lift']['modular_installed']       = 'Installed';
$lang['lift']['modular_install_btn']     = 'Install';
$lang['lift']['modular_already_installed'] = 'Already installed';
$lang['lift']['modular_not_available']   = 'Unavailable while lift is under construction or maintenance.';
$lang['lift']['modular_installed_ok']    = '<div class="alert alert-success text-center">Module installed successfully.</div>';
$lang['lift']['modular_installed_fail']  = '<div class="alert alert-danger text-center">Module installation failed. Try again or contact us at '.CONST_ADMIN_EMAIL.'</div>';
$lang['lift']['modular_already_exists']  = '<div class="alert alert-warning text-center">This module is already installed on this lift.</div>';
$lang['lift']['modular_not_enough_money'] = '<div class="alert alert-danger text-center">You do not have enough money to install this module.</div>';
$lang['lift']['modular_bad_lift_status'] = '<div class="alert alert-danger text-center">You cannot install a module while the lift is under construction or maintenance.</div>';
$lang['lift']['modular_cost']            = 'Cost';
$lang['lift']['modular_speed_bonus']     = 'Speed bonus';
$lang['lift']['modular_throughput_bonus'] = 'Throughput bonus';
$lang['lift']['modular_capacity_bonus']  = 'Capacity bonus';
$lang['lift']['modular_reputation_bonus'] = 'Reputation bonus';
$lang['lift']['modular_daily_cost']      = 'Additional daily cost';

$lang['lift']['age']                        = 'Age';
$lang['lift']['age_unit']                   = 'season(s)';
$lang['lift']['wear']                       = 'Wear';
$lang['lift']['efficiency_penalty']         = 'Efficiency drop (age)';
$lang['lift']['maintenance_multiplier']     = 'Maintenance cost factor';
$lang['lift']['end_of_life_badge']          = 'End of Life';
$lang['lift']['end_of_life_notification']   = ' has reached its end of life and must be replaced.';
$lang['lift']['end_of_life_notification']   = ' has reached its end of life and must be replaced.';
$lang['lift']['end_of_life_notification']   = ' has reached its end of life and must be replaced.';

// Lift Network Efficiency System
$lang['lift']['network_title']                 = 'Lift Network Efficiency';
$lang['lift']['network_intro']                 = 'Analyse how efficiently your lift network moves guests around the resort. Poor layout reduces guest satisfaction — use these metrics to optimise placement.';
$lang['lift']['network_no_lifts']              = 'No lifts have been built yet. Build some lifts to see your network efficiency metrics.';
$lang['lift']['network_total_lifts']           = 'Total lifts built';
$lang['lift']['network_open_lifts']            = 'Open lifts';

$lang['lift']['transfer_efficiency']           = 'Transfer Efficiency';
$lang['lift']['transfer_efficiency_help']      = 'Share of your total lift throughput that is currently operational. A low value means many lifts are closed, under maintenance or out of order.';
$lang['lift']['transfer_efficiency_good']      = 'Excellent — the vast majority of your lift capacity is in service.';
$lang['lift']['transfer_efficiency_warning']   = 'Fair — consider opening or repairing idle lifts.';
$lang['lift']['transfer_efficiency_bad']       = 'Poor — too many lifts are offline. Guests cannot reach key slopes, reducing satisfaction.';

$lang['lift']['bottleneck_score']              = 'Bottleneck Score';
$lang['lift']['bottleneck_score_help']         = 'How balanced is the throughput across your different sectors. A low score means one sector is significantly under-served compared to others.';
$lang['lift']['bottleneck_score_good']         = 'Great balance — all sectors are well served.';
$lang['lift']['bottleneck_score_warning']      = 'Moderate imbalance — some sectors are under-served. Consider adding capacity there.';
$lang['lift']['bottleneck_score_bad']          = 'Severe bottleneck — at least one sector has much lower throughput than the rest, hurting guest flow.';

$lang['lift']['network_redundancy']            = 'Network Redundancy';
$lang['lift']['network_redundancy_help']       = 'Share of your built lifts that serve at least one slope. Lifts that serve no slopes are wasted infrastructure.';
$lang['lift']['network_redundancy_good']       = 'Good — nearly all lifts are linked to slopes.';
$lang['lift']['network_redundancy_warning']    = 'Some lifts are not linked to any slope. Assign slopes to maximise their value.';
$lang['lift']['network_redundancy_bad']        = 'Many lifts have no slope assigned. They contribute nothing to guest flow.';

$lang['lift']['overlap_waste']                 = 'Overlap Waste';
$lang['lift']['overlap_waste_help']            = 'Share of slope assignments that duplicate coverage already provided by another lift. High overlap wastes resources that could serve new slopes.';
$lang['lift']['overlap_waste_good']            = 'Low overlap — your lifts cover distinct slopes efficiently.';
$lang['lift']['overlap_waste_warning']         = 'Moderate overlap — some slopes are served by multiple lifts. Verify this is intentional.';
$lang['lift']['overlap_waste_bad']             = 'High overlap — many slope assignments are redundant. Redistribute lift coverage to unexplored areas.';

$lang['lift']['network_satisfaction_note']     = 'Guest satisfaction is affected by network quality. Aim for high Transfer Efficiency and Bottleneck Score, good Redundancy and minimal Overlap Waste.';

$lang['lift']['network_score']      = 'Overall Network Score';
$lang['lift']['network_score_help'] = 'Weighted composite: Transfer Efficiency 30%, Bottleneck Score 30%, Network Redundancy 20%, Overlap Efficiency 20%. Aim for 80% or higher.';

// Capacity Recommendations
$lang['lift']['network_capacity_title']       = 'Capacity Recommendations';
$lang['lift']['network_capacity_intro']       = 'Open lifts that are not running at maximum capacity. Upgrading them reduces queue times and improves guest satisfaction. Lifts already at their maximum level should be considered for replacement with a higher-capacity type.';
$lang['lift']['network_capacity_none']        = 'All open lifts are already running at maximum capacity.';
$lang['lift']['network_capacity_current']     = 'Current:';
$lang['lift']['network_capacity_riders']      = 'riders/hr';
$lang['lift']['network_capacity_upgrade_tip'] = 'Max capacity:';
$lang['lift']['network_capacity_upgrade_btn'] = 'Upgrade';
$lang['lift']['network_capacity_replace_tip'] = 'Already at maximum level — if queues are long, consider replacing with a higher-capacity lift type.';
