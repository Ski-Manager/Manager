<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['resort']['text']		= 'Welcome to ';
$lang['resort']['no_resort']		= '<legend>You don\'t have a resort yet, create one.</legend>';
$lang['resort']['choose_name']		= 'Choose your resort name:';
$lang['resort']['name_field']		= 'Name';
$lang['resort']['name_field_error']		= 'Name';
$lang['resort']['difficulty_field']		= 'Difficulty';
$lang['resort']['difficulty_field_error']		= 'Difficulty';
$lang['resort']['resort_country']		= 'Choose in which country the resort will be located:';
$lang['resort']['country_field']		= 'Country';
$lang['resort']['country_field_error']		= 'Country';
$lang['resort']['description']		= 'Enter a short presentation of to your resort:';
$lang['resort']['description_field']		= 'Enter a short presentation of your resort';
$lang['resort']['description_field_error']		= 'Description';
$lang['resort']['create']		= 'Create my resort';
$lang['resort']['update']		= 'Update my resort';
$lang['resort']['creation_failed']		= '<div class="alert alert-danger text-center">The resort has not been created. Try again or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['resort']['creation_successful']		= '<div class="alert alert-success text-center">The resort has been created. You can find the details below.<br> It\'s now time to start building! For a good start it is advised to build a chairlift serving two slopes. Next step would be to hire a few employees and then build two standard hotels.<br> For the rest, have a look at the achievements if you don\'t know what to do next!</div>';
$lang['resort']['update_failed']		= '<div class="alert alert-danger text-center">The resort has not been updated. Check the fields below or contact us at '.CONST_ADMIN_EMAIL .'</div>';
$lang['resort']['update_successful']		= '<div class="alert alert-success text-center">The resort has been updated. You can find the details below.</div>';
$lang['resort']['location_show']		= 'Location: ';
$lang['resort']['description_show']		= 'Description: ';
$lang['resort']['sector_unlocked']		= 'Unlocked';
$lang['resort']['locked']		= 'Locked';
$lang['resort']['sector_locked']		= '<span class="alert alert-danger text-center">This sector is locked, thus you cannot build this slope</span>';
$lang['resort']['sector_locked_lift']		= '<span class="alert alert-danger text-center">This sector is locked, thus you cannot build this lift</span>';
$lang['resort']['bad_action']		= '<div class="alert alert-danger text-center">This action cannot be performed.</div>';
//$lang['check_if_slope_name_taken']		= '<div class="alert alert-danger text-center">This name is already in use, please choose another one.</div>';
$lang['resort']['lift_unsellable']		= 'You cannot sell a lift or destroy a slope when it is under maintenance or construction.';
$lang['resort']['item_not_sold']		= 'The lift couldn\'t be sold';
$lang['resort']['item_opened']		= '<div class="alert alert-success text-center">Opened successfully</div>';
$lang['resort']['item_closed']		= '<div class="alert alert-success text-center">Closed successfully</div>';

$lang['resort']['sell_tooltip']		= 'When selling a lift, only 10% of its value is refunded. This action cannot be undone.';
$lang['resort']['destroy_tooltip']		= 'When destroying a slope, you will not get any money back but no cost will be taken. This action cannot be undone.';
$lang['resort']['destroy_item']		= 'Destroy';
$lang['resort']['confirm_sell_item']           = 'Are you sure you want to sell this lift?<br>You will only get 10% of its value back. This includes the dismantling and resale of the lift.';
$lang['resort']['confirm_destroy_item']           = 'Are you sure you want to destroy this slope?<br>You will not get any money back but no cost will be taken.';
$lang['resort']['item_sold']            = 'The lift has been sold.';
$lang['resort']['item_destroyed']            = 'The slope has been destroyed.';
$lang['alpha_dash_space_resort']    = "The Name field may only contain alpha-numeric characters, underscores, and dashes/spaces.";
$lang['resort']['invalid_resort']    = "The Name field contains invalid characters.<br>";
$lang['resort']['missing_resort']    = "You need to enter a Resort name.";
$lang['resort']['info_title_loc']		= ' is located in Sector ';

$lang['resort']['show_sectors']		= 'Show sectors';
$lang['resort']['map_credits']		= 'Map courtesy of Mapsynergy.';

$lang['resort']['trail_map']		= 'Trail map';
$lang['resort']['click_start_building']		= 'Click on the trail map to start building!';
$lang['resort']['built_in_sector']		= 'built in this sector';

// resort map password gate
$lang['resort_map']['password_title']       = 'Trail Map – Restricted Access';
$lang['resort_map']['password_intro']       = 'This section is currently restricted. Please enter the password to access the trail map.';
$lang['resort_map']['password_placeholder'] = 'Enter password…';
$lang['resort_map']['password_submit']      = 'Unlock';
$lang['resort_map']['password_error']       = 'Incorrect password. Please try again.';
$lang['resort_map']['sector_6_building']    = 'We are currently building &amp; adding Sector 6 — a brand-new area of the resort.';

// resort map
$lang['resort_map']['resort_map_title']		= 'Trail map';
$lang['resort_map']['to_build_lift_title']		= 'Build a new lift';
$lang['resort_map']['to_build_lift_instructions']		= 'Click on the Lifts button below, choose the lift type, grip type and number of seats. You can then see the base characteristics in the table.<br>Once you are happy with your lift, place it on the map by clicking on a purple dotted line.';
$lang['resort_map']['to_build_slope_title']		= 'Build a new slope';
$lang['resort_map']['to_build_slope_instructions']		= 'Click on the Slopes button below, choose the slope type, choose the slope difficulty and then click on a purple dotted line on the map to select the slope to build.';
$lang['resort_map']['to_build_title']		= 'Validation';
$lang['resort_map']['to_build_instructions']		= 'Once your lift/slope is placed on the map, you can see the expected building time, cost and length underneath the map.<br>Click Build to start the construction.';
$lang['resort_map']['to_build_tips_title']		= 'Tips';
$lang['resort_map']['to_build_tips_instructions']		= 'You can zoom in/out using the mouse wheel or the -/+ button.<br>You can show/hide the sectors using the layer button on the right side of the trail map.';

$lang['resort_map']['selected_segment_id']		= 'Selected segment ID';
$lang['resort_map']['approx_length']		= 'Approximate length';
$lang['resort_map']['approx_building_time']		= 'Estimated building time';
$lang['resort_map']['approx_price']		= 'Approximate price';
$lang['resort_map']['build_lift_page_title']	= 'Build a new lift';
$lang['resort_map']['build_slope_page_title']	= 'Build a new slope';


$lang['resort']['grooming_requirements']		= 'Grooming requirements';
$lang['resort']['grooming_cap_avail']		= 'Grooming capacity available';
$lang['resort']['groomers_available']		= 'Groomer(s) available';
$lang['resort']['required']		= 'required';
$lang['resort']['available']		= 'available';


$lang['resort']['summary_intro']		= '<h4>The table below shows an overview of all the buildings available in your resort.<br>You can also see the total capacity of each building type and the maximum number of tourists which can visit your resort before the buildings are overcowded.</h4>';

//$lang['resort']['slope_type_not_claimed']		= "Coming soon - stay tuned!";
$lang['resort']['slope_type_not_claimed']		= "Claim the reward for achievement 'Unlock new slope types' to unlock this type of slope";
//$lang['resort']['slope_type_locked']		= "Coming soon - stay tuned!";
$lang['resort']['slope_type_locked']		= "Complete the achievement 'Unlock new slope types' to unlock this type of slope";

$lang['resort']['welcome_new_user_title'] = 'Welcome to Ski-Manager!';
$lang['resort']['welcome_new_user_body']  = 'Start by creating your ski resort below. If you\'re new, our step-by-step tutorial will help you get up and running quickly.';

// Altitude & Microclimate
$lang['resort']['altitude_label']       = 'Altitude';
$lang['resort']['altitude_help']        = 'Higher altitude resorts have more reliable snow but higher build costs and greater wind risk.';
$lang['resort']['altitude_low']         = 'Low altitude (< 1 000 m)';
$lang['resort']['altitude_medium']      = 'Medium altitude (1 000 – 2 000 m)';
$lang['resort']['altitude_high']        = 'High altitude (> 2 000 m)';
$lang['resort']['aspect_label']         = 'Slope aspect';
$lang['resort']['aspect_help']          = 'South-facing slopes melt faster; north-facing slopes retain snow longer.';
$lang['resort']['aspect_north']         = 'North-facing';
$lang['resort']['aspect_south']         = 'South-facing';
$lang['resort']['aspect_east']          = 'East-facing';
$lang['resort']['aspect_west']          = 'West-facing';
$lang['resort']['microclimate_info']    = 'Microclimate';
$lang['resort']['altitude_build_cost_info'] = 'Build cost multiplier due to altitude';
$lang['resort']['wind_risk_high']       = 'High wind risk';
$lang['resort']['wind_risk_medium']     = 'Moderate wind risk';
$lang['resort']['wind_risk_low']        = 'Low wind risk';
// Microclimate editing
$lang['resort']['microclimate_edit_title']       = 'Edit Microclimate Settings';
$lang['resort']['microclimate_first_change_free']= 'Your first change is free! After this, each subsequent change will cost more.';
$lang['resort']['microclimate_change_cost_info'] = 'This change will cost <strong>%s €</strong>. Your current balance: <strong>%s €</strong>.';
$lang['resort']['microclimate_save_free']        = 'Save (free)';
$lang['resort']['microclimate_save_cost']        = 'Save (costs %s €)';
$lang['resort']['microclimate_update_success']   = '<strong>Microclimate settings updated successfully.</strong>';
$lang['resort']['microclimate_no_cash']          = 'You do not have enough money to change the microclimate settings.';
$lang['resort']['microclimate_update_failed']    = 'The microclimate settings could not be updated. Please try again.';
$lang['resort']['microclimate_edit_via_page']    = 'To change Altitude or Slope Aspect, use the <a href="'.base_url('microclimate_controller').'">Microclimate</a> page.';
// Legacy System
$lang['resort']['legacy_rating_label']        = 'Historical Rating';
$lang['resort']['legendary_mountain_badge']   = '⭐ Legendary Mountain';
$lang['resort']['legendary_mountain_desc']    = 'This resort has achieved Legendary Mountain status after 20 seasons of excellence.';

// Star Rating System
$lang['resort']['star_rating_label']          = 'Star Rating';
$lang['resort']['star_rating_tooltip']        = 'Your star rating is based on your resort\'s reputation. Earn more reputation to unlock higher star ratings.';
// Quick stat cards
$lang['resort']['stat_open_slopes']  = 'Open Slopes';
$lang['resort']['stat_open_lifts']   = 'Open Lifts';
$lang['resort']['stat_staff']        = 'Staff';
$lang['resort']['stat_active_runs']  = 'active runs';
$lang['resort']['stat_operating']    = 'operating';
$lang['resort']['stat_hired']        = 'hired';

// Social Sharing
$lang['resort']['share_resort']      = 'Share';
$lang['resort']['share_resort_text'] = 'Check out my ski resort "%name%" in %country% on Ski-Manager! 🎿 #SkiManager';
