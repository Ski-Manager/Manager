<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

// Common to all buildings
$lang['common_buildings']['titleMain']		= 'Buildings';

// Access Resort buildings
$lang['access_resort']['title']		= 'Access & Transport';
$lang['access_resort']['intro']		= 'Useful information regarding buildings to access the resort. The better the infrastructure of the resort is, the more tourists will be attracted and will generate revenues.';

// Tourist info
$lang['tourist_info']['title']		= 'Tourist Information Center';
$lang['touristinfo']['title']		= 'Tourist Information Center';
$lang['touristinfo']['title_sing']		= 'Tourist Information Center';
$lang['tourist_info']['title_sing']		= 'Tourist Information Center';
$lang['tourist_info']['desc']		= 'The tourist information center needs to be built before any other building. It is required to attract tourists into the resort. This is also where you set the price for the skipass and can open/close the resort.<br>When the resort is closed, no tourists will visit your resort so you won\t generate any revenue and you won\t have to pay the staff.';
$lang['tourist_info']['skiPassLabel']                   = 'Ski pass price';
$lang['tourist_info']['oneDay']                         = '1 day';
$lang['tourist_info']['oneWeek']                        = '1 week';
$lang['tourist_info']['inEuros']                        = '(in euros)';
$lang['tourist_info']['price_updated']                  = 'Price updated!';
$lang['tourist_info']['price_not_updated']              = 'Price limit reached!';
$lang['tourist_info']['dynamic_pricing_saved']          = 'Dynamic pricing settings saved!';
$lang['tourist_info']['dynamic_pricing_title']          = 'Dynamic Pricing';
$lang['tourist_info']['dynamic_pricing_desc']           = 'Attract more visitors with family discounts and generate premium revenue with VIP passes.';
$lang['tourist_info']['vip_pass_label']                 = 'VIP pass price (€, 0 = disabled)';
$lang['tourist_info']['vip_pass_help']                  = 'A premium daily pass for ' . (int)(VIP_VISITOR_FRACTION * 100) . '% of your visitors at a higher price. Set to 0 to disable.';
$lang['tourist_info']['family_discount_label']          = 'Family discount (%, 0 = disabled)';
$lang['tourist_info']['family_discount_help']           = 'Offer a discount to ' . (int)(FAMILY_VISITOR_FRACTION * 100) . '% of your daily visitors to attract more families. Each 1% discount boosts demand by ' . (FAMILY_DISCOUNT_DEMAND_BONUS) . '%.';
$lang['tourist_info']['group_discount_label']           = 'Group discount (%, 0 = disabled)';
$lang['tourist_info']['group_discount_help']            = 'Offer a discount to ' . (int)(GROUP_VISITOR_FRACTION * 100) . '% of your daily visitors arriving in groups. Each 1% discount boosts group demand by ' . (GROUP_DISCOUNT_DEMAND_BONUS) . '%.';
$lang['tourist_info']['save_skipass_price']             = 'Save ski pass price';
$lang['tourist_info']['save_dynamic_pricing']           = 'Save pricing';
$lang['tourist_info']['current_prices_title']           = 'Current ski pass prices';
$lang['tourist_info']['daily_label']                    = 'Daily';
$lang['tourist_info']['weekly_label']                   = 'Weekly';
$lang['tourist_info']['effective_price_label']          = 'Effective price (with prestige bonus)';
$lang['tourist_info']['resort_open']                    = 'Resort is open';
$lang['tourist_info']['resort_closed']                  = 'Resort is closed';
$lang['tourist_info']['resort_construction']            = 'Under construction';
$lang['tourist_info']['resort_closed_warning']          = '⚠️ Your resort is currently closed. No revenue or visitors will be recorded until you reopen it.';
$lang['tourist_info']['open_resort_tip']                = 'Open the resort to start welcoming tourists and generating revenue.';
$lang['tourist_info']['close_resort_tip']               = 'Close the resort to pause tourist visits and staff payments.';

// Resort access
$lang['access']['accessResortTitle']		= 'Access to the resort';
$lang['access']['title']		= 'Access building';
$lang['access']['title_sing']		= 'Access building';
$lang['access']['accessResortDesc']		= 'How tourists get to your resort is really important. Improve the infrastructure to attract more tourists.';
$lang['access']['infrastructure']                 = 'Infrastructure';
$lang['access']['current_benefit_title']          = 'Current benefit';
$lang['access']['visitor_bonus_label']            = 'Max visitor bonus';
$lang['access']['no_access_built']                = 'No access infrastructure built yet. Build level 1 to start attracting more tourists.';

$lang['access']['thanks_prestige_bonus']                 = 'Thanks to the bonus given by your resort\'s Prestige, each tourist is expected to spend an additional';
$lang['access']['after_purchasing_skipass']                 = 'after purchasing their skipass. This means that each tourist will spend';
$lang['access']['for_the_daily_skipass']                 = 'for the daily skipass and';
$lang['access']['for_the_weekly_skipass']                 = 'for the weekly skipass.';

// Parking
$lang['parking']['parkingTitle']                   = 'Parking';
$lang['parking']['title']		= 'Parking';
$lang['parking']['title_sing']		= 'Parking';
$lang['parking']['parkingDesc']		= 'Make sure the tourists coming by car can park in your resort and you will make more money. You can only build one parking area for the resort so make sure you upgrade it!';
$lang['parking']['current_income_title']           = 'Current income';
$lang['parking']['max_daily_income_label']         = 'Max daily income';
$lang['parking']['no_parking_built']               = 'No parking built yet. Build level 1 to start earning income from parked cars.';
$lang['parking']['fee_title']                      = 'Parking Fee';
$lang['parking']['fee_desc']                       = 'Set the fee charged per vehicle per day. A higher fee earns more per car but reduces demand. The sweet spot is ' . DEFAULT_PARKING_FEE . ' €.';
$lang['parking']['fee_label']                      = 'Parking fee (€/vehicle/day)';
$lang['parking']['fee_help']                       = 'Min ' . MIN_PARKING_FEE . ' €, Max ' . MAX_PARKING_FEE . ' €. Higher fees reduce the number of cars using your parking.';
$lang['parking']['fee_save_btn']                   = 'Save fee';
$lang['parking']['fee_updated']                    = 'Parking fee updated!';
$lang['parking']['fee_not_updated']                = 'Parking fee update failed!';

// Building building Actions
$lang['building']['building_opened']                = '<div class="alert alert-success text-center">The building has been opened successfully.</div>';
$lang['building']['building_closed']                = '<div class="alert alert-success text-center">The building has been closed successfully.</div>';
$lang['building']['building_not_opened']		= '<div class="alert alert-danger text-center\">The building couldn\'t be opened.</div>';
$lang['building']['building_not_closed']		= '<div class="alert alert-danger text-center">The building couldn\'t be closed.</div>';
$lang['building']['building_not_existing']		= '<div class="alert alert-danger text-center">This building doesn\'t exist.</div>';
$lang['building']['build']                          = 'Build';
$lang['building']['building']                          = 'Building';
$lang['building']['building_built']                 = '<div class="alert alert-success text-center">The construction of the building has started successfully.</div>';
$lang['building']['building_already_built']		= '<div class="alert alert-danger text-center">There is already an ongoing construction.</div>';
$lang['building']['building_one_at_a_time']		= '<div class="alert alert-danger text-center">You can only build one building of each type simultaneously. Try again when the current building is completed.</div>';
$lang['building']['building_not_built']		= '<div class="alert alert-danger text-center">The building couldn\'t be built.</div>';

// Building Upgrade actions
$lang['building']['upgrade']                        = 'Upgrade';
$lang['building']['upgraded']		= 'Upgraded';
$lang['building']['upgrading']		= 'Upgrading';
$lang['building']['building_upgraded']              = '<div class="alert alert-success text-center">The building upgrade has started successfully.</div>';
$lang['building']['building_not_upgraded']		= '<div class="alert alert-danger text-center">The building couldn\'t be upgraded.</div>';
$lang['building']['building_not_built_previous']    = '<div class="alert alert-danger text-center">You can\'t upgrade to this level before building the previous one first.</div>';

$lang['building']['bad_action']		= '<div class="alert alert-danger text-center">This action cannot be performed.</div>';
// General
$lang['building']['current_level']                  = 'Current level:';
$lang['building']['there_are']                  = 'There are ';
$lang['building']['active_text_cannon']                  = ' active snow cannons ';
$lang['building']['inactive_text_cannon']                  = ' inactive snow cannons ';
$lang['building']['start_all']                  = 'Start all';
$lang['building']['stop_all']                  = 'Stop all';
$lang['building']['cannons_started']                  = '<div class="alert alert-success text-center">All snow cannons have been started.</div>';
$lang['building']['cannons_stopped']                  = '<div class="alert alert-success text-center">All snow cannons have been stopped.</div>';
$lang['building']['not_enough_money']		= 'Not enough money to perform this action.';

$lang['building']['max_income']                  = 'Max daily income';
$lang['building']['max_bonus_affluence']                  = 'Max visitor bonus';
$lang['building']['max_income_per_building']                  = 'Max daily income per building';
$lang['building']['daily_cost_per_cannon']                  = 'Daily cost per cannon';
$lang['building']['quantity']                  = 'Quantity';
$lang['building']['tourist_info_required']        = '<div class="alert alert-danger text-center">You need to build the Tourist Information Center before building anything else. Click <a href="'.base_url().'building_access_controller">here</a> to access the Tourist Information Center page.</div>';
$lang['building']['no_tourist_info']        = 'Tourist Information Center not built. Build it to get the resort status.';
$lang['building']['the_achievement']        = 'The achievement';
$lang['building']['ach_not_completed']        = 'is required to built this building type and is currently not completed.';
$lang['building']['ach_not_completed_tournaments']        = 'is required to start arranging tournaments but it is currently not completed.';
$lang['building']['current_progress_is']        = 'Current progress is ';
$lang['building']['ach_not_claimed']        = 'is completed but you haven\'t claimed your reward.';
$lang['building']['achievement_link_info']        = 'Check the <a href="'.base_url().'achievements_controller">achievements page</a> for more details.</div>';

// Hotel
$lang['hotel']['title']                 = 'Hotels';
$lang['hotel']['title_sing']                 = 'Hotel';
$lang['hotel']['intro']                 = 'Information regarding your hotels.';
$lang['hotel']['desc']		= 'Hotels and residences provide a place to stay for your visitors. You need to have sufficient capacity to host all the tourists. It is possible to build several hotels and you can upgrade them to be more attractive or increase the hosting capacity.<br>You need to have the Tourist Information Center built in order to start building hotels.';

// Restaurant
$lang['restaurant']['title']                 = 'Restaurants';
$lang['restaurant']['title_sing']                 = 'Restaurant';
$lang['restaurant']['intro']                 = 'Information regarding your restaurants.';
$lang['restaurant']['desc']		= 'Snacks and restaurants allow your visitors to eat wherever they are located in your resort; on the slopes or in the valley. It is possible to build several restaurants and you can upgrade some of them to attract different types of customers.<br>You need to have the Tourist Information Center built in order to start building restaurants.';

// Rental
$lang['rental']['title']                 = 'Ski rentals';
$lang['rental']['title_sing']                 = 'Ski rental';
$lang['rental']['intro']                 = 'Information regarding your ski rentals.';
$lang['rental']['desc']		= 'Ski rentals allow visitors to rent equipment for their vacation. Providing skis, snowboards, shoes, clothing, helmets, and more, they also generate a lot of money. It is possible to build several ski rentals and you can upgrade them to attract different types of customers.<br>You need to have the Tourist Information Center built in order to start building rental skis.';

// Leisure
$lang['leisure']['title']                 = 'Leisure';
$lang['leisure']['title_sing']                 = 'Leisure';
$lang['leisure']['intro']                 = 'Information regarding your leisure buildings.';
$lang['leisure']['desc']		= 'Leisure buildings provide your visitors plenty of activities to enjoy their free time. It is possible to build several leisure buildings and upgrade them to make sure all your visitors find their favorite activity.<br>You need to have the Tourist Information Center built in order to start building leisure buildings.';

// Luxury
$lang['luxury']['title']                 = 'Luxury';
$lang['luxury']['title_sing']                 = 'Luxury facility';
$lang['luxury']['intro']                 = 'Information regarding your luxury facilities.';
$lang['luxury']['desc']		= 'Luxury facilities cater to an exclusive clientele of high-spending VIP guests. Offer VIP chalets, helicopter skiing, private ski instructors, and exclusive lounges to attract a small number of guests who generate massive profits. Only a fraction of your visitors will seek these premium services, but the revenue per guest far exceeds that of standard buildings.<br>You need to have the Tourist Information Center built in order to start building luxury facilities.';

// Medical
$lang['medical']['title']                 = 'Medical';
$lang['medical']['title_sing']                 = 'Medical';
$lang['medical']['intro']                 = 'Information regarding your medical buildings.';
$lang['medical']['desc']		= 'Medical buildings ensure your injured visitors are cured in good conditions. If there are not cured on time, the reputation of your resort might decrease and you might need to pay fines. It is possible to build several medical buildings and upgrade them to increase the capacity.<br>You need to have the Tourist Information Center built in order to start building medical buildings.';

// Resort Facilities
$lang['facility']['title']                 = 'Resort Facilities';
$lang['facility']['title_sing']            = 'Resort Facility';
$lang['facility']['intro']                 = 'Information regarding your resort facilities.';
$lang['facility']['desc']		= 'Resort facilities such as spas, wellness centres and fitness rooms give your visitors a premium experience and a place to relax after a day on the slopes. They generate steady income and improve your resort\'s reputation. It is possible to build several facilities and upgrade them to accommodate more guests.<br>You need to have the Tourist Information Center built in order to start building resort facilities.';

// Snow cannons
$lang['cannon']['title']                 = 'Snow cannons';
$lang['cannon']['title_sing']                 = 'Snow cannon';
$lang['cannon']['mini_title']            = 'snow cannons';
$lang['cannon']['intro']                 = 'Information regarding your snow cannons.';
$lang['cannon']['desc']                 = 'Snow cannons add some snow to your resort every night. To simplify the mechanism, the level of snow will be increased evenly through the whole resort. It is possible to build several snow cannons and upgrade them to increase the amount of snow added.<br>You need to have the Tourist Information Center built in order to start building snow cannons.';

$lang['building']['snow_output_per_cannon']       = 'Snow output (cm/night)';
$lang['building']['current_snow_level']           = 'Current snow level in resort';
$lang['building']['cm']                           = 'cm';
$lang['building']['cannon_number']                = '#';
$lang['building']['cannon_level_col']             = 'Level';
$lang['building']['cannon_snow_output_col']       = 'Snow output (cm/night)';
$lang['building']['cannon_daily_cost_col']        = 'Daily cost (€)';
$lang['building']['cannon_status_col']            = 'Status';
$lang['building']['cannon_action_col']            = 'Action';
$lang['building']['cannon_status_active']         = 'Active';
$lang['building']['cannon_status_inactive']       = 'Inactive';
$lang['building']['cannon_status_construction']   = 'Under construction';
$lang['building']['start_cannon']                 = 'Start';
$lang['building']['stop_cannon']                  = 'Stop';
$lang['building']['cannon_started']               = '<div class="alert alert-success text-center">The snow cannon has been started.</div>';
$lang['building']['cannon_stopped']               = '<div class="alert alert-success text-center">The snow cannon has been stopped.</div>';
$lang['building']['individual_cannons_title']     = 'Individual cannon management';

// Snow cannon summary
$lang['building']['cannon_summary_title']         = 'Production summary (active cannons)';
$lang['building']['cannon_total_snow_output']     = 'Total snow output per night';
$lang['building']['cannon_total_daily_cost']      = 'Total daily operating cost';

// Snow target level
$lang['building']['snow_target_title']            = 'Snow target level';
$lang['building']['snow_target_current']          = 'Current target';
$lang['building']['snow_target_none']             = 'No target set — cannons will add snow every night up to the maximum level.';
$lang['building']['snow_target_disabled']         = 'no target';
$lang['building']['save_snow_target']             = 'Save target';
$lang['building']['snow_target_info']             = 'When the resort snow level reaches this target, cannons will stop adding snow overnight. Set to 0 to disable and always add snow.';
$lang['building']['snow_target_saved']            = '<div class="alert alert-success text-center">Snow target saved successfully.</div>';

// Snow auto-start threshold
$lang['building']['snow_auto_start_title']        = 'Auto-start threshold';
$lang['building']['snow_auto_start_current']      = 'Current threshold';
$lang['building']['snow_auto_start_none']         = 'No auto-start threshold set — cannons will only run when started manually.';
$lang['building']['snow_auto_start_disabled']     = 'no threshold';
$lang['building']['save_snow_auto_start']         = 'Save threshold';
$lang['building']['snow_auto_start_info']         = 'When the resort snow level drops below this value overnight, all inactive cannons will be started automatically. Set to 0 to disable.';
$lang['building']['snow_auto_start_saved']        = '<div class="alert alert-success text-center">Auto-start threshold saved successfully.</div>';

// Snow level history
$lang['building']['snow_history_title']           = 'Snow level history (last ' . SNOW_HISTORY_DAYS . ' days)';
$lang['building']['snow_history_date']            = 'Date';
$lang['building']['snow_history_level']           = 'Snow level (cm)';
$lang['building']['snow_history_none']            = 'No history data available yet.';

// Low snow warning
$lang['building']['low_snow_warning']             = 'Warning: snow level is critically low! Start your snow cannons to restore snow coverage.';

// Night skiing
$lang['building']['night_skiing_title']           = 'Night skiing';
$lang['building']['night_skiing_page_intro']      = 'Night skiing allows your slopes to remain open after dark, attracting additional visitors and generating extra revenue.';
$lang['building']['night_skiing_desc']            = 'Night skiing allows your slopes to remain open after dark, attracting additional visitors and generating extra revenue.';
$lang['building']['night_skiing_on']              = 'Night skiing: ON';
$lang['building']['night_skiing_off']             = 'Night skiing: OFF';
$lang['building']['enable_night_skiing']          = 'Enable night skiing';
$lang['building']['disable_night_skiing']         = 'Disable night skiing';
$lang['building']['night_skiing_info']            = 'Night skiing generates a bonus on your nightly skipass revenue. The bonus and electricity cost both scale with the number of open slopes.';
$lang['building']['night_skiing_enabled']         = '<div class="alert alert-success text-center">Night skiing has been enabled. Your slopes will now be open after dark!</div>';
$lang['building']['night_skiing_disabled']        = '<div class="alert alert-warning text-center">Night skiing has been disabled.</div>';
$lang['building']['night_skiing_needs_cannon']    = '<div class="alert alert-danger text-center">You need at least one active snow cannon to enable night skiing.</div>';
$lang['building']['night_skiing_status_label']    = 'Current status';
$lang['building']['night_skiing_key_figures']     = 'Key figures';
$lang['building']['night_skiing_open_slopes_label']   = 'Open slopes';
$lang['building']['night_skiing_revenue_bonus_label'] = 'Revenue bonus per night';
$lang['building']['night_skiing_electricity_label']   = 'Electricity cost per night';
$lang['building']['night_skiing_base_label']          = 'base';
$lang['building']['night_skiing_per_slope_label']     = 'slope';
$lang['building']['night_skiing_skipass_daily_label']  = 'Daily skipass price';
$lang['building']['night_skiing_skipass_weekly_label'] = 'Weekly skipass price';
$lang['building']['night_skiing_how_it_works']    = 'How it works';
$lang['building']['night_skiing_how_it_works_desc'] = 'When night skiing is enabled, your resort earns a bonus on the nightly skipass revenue. The base bonus is ' . (int)(NIGHT_SKIING_REVENUE_BONUS * 100) . '%, plus ' . (int)(NIGHT_SKIING_SLOPE_REVENUE_FACTOR * 100) . '% for each additional slope beyond the first. The electricity cost is ' . number_format(NIGHT_SKIING_ELECTRICITY_COST, 0, ',', ' ') . ' € base plus ' . number_format(NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE, 0, ',', ' ') . ' € per open slope per night. Night skiing can be toggled on or off at any time.';
$lang['building']['night_skiing_resort_settings']     = 'Resort settings';
$lang['building']['night_skiing_start_hour_label']    = 'Start hour';
$lang['building']['night_skiing_end_hour_label']      = 'End hour';
$lang['building']['night_skiing_ticket_price_label']  = 'Night ticket price';
$lang['building']['night_skiing_save_settings']       = 'Save settings';
$lang['building']['night_skiing_trails_enabled_label'] = 'Slopes with night skiing';
$lang['building']['night_skiing_no_open_slopes']      = 'No open slopes available for night skiing configuration.';
$lang['building']['night_skiing_trails_title']        = 'Slope lighting settings';
$lang['building']['night_skiing_trails_intro']        = 'Configure the lighting for each open slope. Enable night skiing per slope and choose the light type, brightness, and pole spacing.';
$lang['building']['night_skiing_trail_name']          = 'Slope';
$lang['building']['night_skiing_trail_enabled']       = 'Night skiing';
$lang['building']['night_skiing_light_type_label']    = 'Light type';
$lang['building']['night_skiing_brightness_label']    = 'Brightness';
$lang['building']['night_skiing_pole_spacing_label']  = 'Pole spacing';
$lang['building']['night_skiing_on_short']            = 'ON';
$lang['building']['night_skiing_off_short']           = 'OFF';
$lang['building']['night_skiing_light_led']           = 'LED';
$lang['building']['night_skiing_light_halogen']       = 'Halogen';
$lang['building']['night_skiing_light_metal_halide']  = 'Metal halide';
$lang['building']['night_skiing_configure']           = 'Configure';
$lang['building']['night_skiing_configure_trail']     = 'Configure slope';
$lang['building']['night_skiing_light_type_help']     = 'LED lights are energy-efficient. Halogen lights are cheaper but consume more electricity. Metal halide lights provide the brightest illumination.';
$lang['building']['night_skiing_brightness_help']     = 'Higher brightness improves visitor experience but increases electricity costs.';
$lang['building']['night_skiing_pole_spacing_help']   = 'Closer pole spacing provides better coverage but increases costs.';
$lang['building']['night_skiing_spacing_15']          = 'Dense (15 m)';
$lang['building']['night_skiing_spacing_25']          = 'Standard (25 m)';
$lang['building']['night_skiing_spacing_35']          = 'Sparse (35 m)';
$lang['building']['cancel']                           = 'Cancel';
$lang['building']['night_skiing_settings_saved']      = '<div class="alert alert-success text-center">Night skiing settings saved successfully.</div>';
$lang['building']['night_skiing_settings_invalid']    = '<div class="alert alert-danger text-center">Invalid night skiing settings. Please check the values and try again.</div>';
$lang['building']['night_skiing_trail_saved']         = '<div class="alert alert-success text-center">Slope night skiing settings saved successfully.</div>';

// Night skiing – entertainment and safety level
$lang['building']['night_skiing_per_night_label']     = 'night';
$lang['building']['night_skiing_entertainment_label'] = 'Evening entertainment';
$lang['building']['night_skiing_entertainment_help']  = 'Add entertainment to attract more night visitors and boost revenue. Higher tiers cost more but yield a larger revenue multiplier.';
$lang['building']['night_skiing_ent_none']    = 'None';
$lang['building']['night_skiing_ent_basic']   = 'Basic (hot drinks & background music)';
$lang['building']['night_skiing_ent_premium'] = 'Premium (live music & full bar)';
$lang['building']['night_skiing_safety_label'] = 'Safety level';
$lang['building']['night_skiing_safety_help']  = 'Higher safety investment reduces accident risk, improves visitor satisfaction, and earns a nightly reputation bonus.';
$lang['building']['night_skiing_safety_1'] = 'Standard';
$lang['building']['night_skiing_safety_2'] = 'Enhanced';
$lang['building']['night_skiing_safety_3'] = 'Maximum';

// Night skiing – ski school
$lang['building']['night_skiing_school_label']          = 'Night ski school';
$lang['building']['night_skiing_school_toggle']         = 'Enable night ski school';
$lang['building']['night_skiing_school_price_label']    = 'Lesson price per person';
$lang['building']['night_skiing_school_per_person_label'] = 'person';
$lang['building']['night_skiing_school_help']           = 'Offer ski lessons during night sessions. About ' . (int)(NIGHT_SKIING_SCHOOL_VISITOR_FRACTION * 100) . '% of night visitors sign up, generating extra revenue and a +' . NIGHT_SKIING_SCHOOL_REPUTATION_BONUS . ' reputation bonus per night.';

// Night skiing – weather auto-suspend
$lang['building']['night_skiing_weather_suspend_label'] = 'Auto-suspend on rain';
$lang['building']['night_skiing_weather_suspend_help']  = 'When enabled, night skiing is automatically suspended on rainy nights to protect visitor safety. No revenue or costs are incurred on suspended nights.';

// Night skiing – torchlight descent
$lang['building']['night_skiing_torchlight_label']      = 'Torchlight descent';
$lang['building']['night_skiing_torchlight_toggle']     = 'Enable torchlight descent';
$lang['building']['night_skiing_torchlight_help']       = 'Organise a guided torchlight descent each night. Costs ' . NIGHT_SKIING_TORCHLIGHT_COST . ' €/night and brings +' . (int)(NIGHT_SKIING_TORCHLIGHT_VISITOR_BONUS * 100) . '% more night visitors while earning +' . NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS . ' reputation per night.';

// Night skiing – photography package
$lang['building']['night_skiing_photo_label']           = 'Night photography package';
$lang['building']['night_skiing_photo_toggle']          = 'Enable photography package';
$lang['building']['night_skiing_photo_price_label']     = 'Package price per person';
$lang['building']['night_skiing_photo_per_person_label'] = 'person';
$lang['building']['night_skiing_photo_help']            = 'Offer guided night photography sessions. About ' . (int)(NIGHT_SKIING_PHOTO_VISITOR_FRACTION * 100) . '% of night visitors sign up, generating extra revenue and a +' . NIGHT_SKIING_PHOTO_REPUTATION_BONUS . ' reputation bonus per night.';

// Event venues common page
$lang['event_venues_buildings']['titleMain']                 = 'Event venues';
$lang['event_venues_buildings']['intro']		= 'All necessary buildings to organize world-class tournaments are available here. Check out the <a href="'.base_url().'tournaments_controller">tournaments page</a> to check out which buildings and levels are required.';

$lang['housing_complex']['title']		= 'Housing complex';
$lang['housing_complex']['title_sing']		= 'Housing complex';
$lang['housing_complex']['desc']		= 'This housing complex can host all the participating athletes, officials and trainers of your tournaments. By building the complex, you leave the regular hotels to the tourists visiting your resort. Upgrading the complex to the maximum level will grant the status of Olympic Village.';

$lang['icerink']['title']		= 'Ice rink';
$lang['icerink']['title_sing']		= 'Ice rink';
$lang['icerink']['desc']		= 'The ice rink is an arena ice surface where athletes participating in tournaments can practice ice skating and similar activities. The ice in the ice rink if of high quality, justifying the high operating costs.';

$lang['curling_center']['title']		= 'Curling center';
$lang['curling_center']['title_sing']		= 'Curling center';
$lang['curling_center']['desc']		= 'The curling center is an arena composed of one or several curling sheets made of ice where athletes can play curling during tournaments. The ice of the curling center is not as high quality as the ice rink, thus the lower operating costs.';

$lang['open_stage']['title']		= 'Open stage';
$lang['open_stage']['title_sing']		= 'Open stage';
$lang['open_stage']['desc']		= 'The open stage is the place when celebrations take place during your tournaments. Upgrading the open stage to higher levels will allow larger music concerts and events, with famous artists.';



$lang['building']['building_type']                   = 'Building type';
$lang['building']['total_capacity']                 = 'Total capacity';
$lang['building']['max_tourists']                 = 'Max tourists';
$lang['building']['total_capacity_help']                 = 'This is the total capacity for each building type';
$lang['building']['max_tourists_help']                 = 'This is maximum number of tourists which can visit your resort before the specific building type is overcrowded. You should see to have this number above your daily visitors.<br>Not all tourists need a rental, a restaurant or medical assistance; That\'s why the two last columns differ in most cases.';

$lang['building']['rush_completed']		= '<div class="alert alert-success text-center">You have successfully rushed the construction/upgrade of the building.</div>';
$lang['building']['already_completed']		= '<div class="alert alert-warning text-center">The construction/upgrade is already completed.</div>';
$lang['building']['not_enough_genepis']		= '<div class="alert alert-warning text-center">You don\'t have enough Génépis to perform this action.</div>';

// Trail snowmaking
$lang['building']['trail_sm_title']              = 'Snowmaking management';
$lang['building']['trail_sm_intro']              = 'Manage your resort-wide snowmaking operations. Monitor your snow level, control snow cannons, set an operating mode, and review tonight\'s projected output.';
$lang['building']['trail_sm_summary_title']      = 'Active equipment summary';
$lang['building']['trail_sm_total_output']       = 'Total snow output per night';
$lang['building']['trail_sm_total_daily_cost']   = 'Total daily operating cost';
$lang['building']['trail_sm_night']              = 'night';
$lang['building']['trail_sm_catalogue_title']    = 'Equipment types';
$lang['building']['trail_sm_brands_title']       = 'Brands';
$lang['building']['trail_sm_trails_title']       = 'Trail snowmaking management';
$lang['building']['trail_sm_col_trail']          = 'Trail';
$lang['building']['trail_sm_col_type']           = 'Type';
$lang['building']['trail_sm_col_equip']          = 'Equipment';
$lang['building']['trail_sm_col_brand']          = 'Brand';
$lang['building']['trail_sm_col_output']         = 'Snow output';
$lang['building']['trail_sm_col_daily']          = 'Daily cost';
$lang['building']['trail_sm_col_status']         = 'Status';
$lang['building']['trail_sm_col_actions']        = 'Actions';
$lang['building']['trail_sm_col_purchase']       = 'Purchase cost';
$lang['building']['trail_sm_col_output_mult']    = 'Output';
$lang['building']['trail_sm_col_cost_mult']      = 'Cost';
$lang['building']['trail_sm_none']               = 'No equipment';
$lang['building']['trail_sm_no_slopes']          = 'You don\'t have any trails yet. Build some slopes first to add snowmaking equipment.';
$lang['building']['trail_sm_type_label']         = 'Type';
$lang['building']['trail_sm_brand_label']        = 'Brand';
$lang['building']['trail_sm_buy']                = 'Purchase';
$lang['building']['trail_sm_active']             = 'Active';
$lang['building']['trail_sm_inactive']           = 'Inactive';
$lang['building']['trail_sm_start']              = 'Start';
$lang['building']['trail_sm_stop']               = 'Stop';
$lang['building']['trail_sm_remove']             = 'Remove';
$lang['building']['trail_sm_confirm_remove']     = 'Are you sure you want to remove this snowmaking equipment?';
$lang['building']['trail_sm_purchased']          = '<div class="alert alert-success text-center">Snowmaking equipment purchased and installed successfully.</div>';
$lang['building']['trail_sm_purchased_log']      = 'Purchased trail snowmaking equipment.';
$lang['building']['trail_sm_already_equipped']   = '<div class="alert alert-warning text-center">This trail already has snowmaking equipment. Remove the existing equipment first.</div>';
$lang['building']['trail_sm_bad_type']           = '<div class="alert alert-danger text-center">Invalid equipment type or brand selected.</div>';
$lang['building']['trail_sm_started']            = '<div class="alert alert-success text-center">Snowmaking equipment started.</div>';
$lang['building']['trail_sm_stopped']            = '<div class="alert alert-warning text-center">Snowmaking equipment stopped.</div>';
$lang['building']['trail_sm_removed']            = '<div class="alert alert-success text-center">Snowmaking equipment removed from trail.</div>';
// Merged snowmaking page
$lang['building']['trail_sm_cannon_section_title']   = 'Snow Cannons (Resort-wide)';
$lang['building']['trail_sm_cannon_link']            = 'Manage Snow Cannons';
$lang['building']['trail_sm_cannon_none']            = 'No snow cannons built yet. <a href="%s">Build snow cannons</a> to add resort-wide snowmaking capacity.';
$lang['building']['trail_sm_cannon_active']          = 'Active cannons';
$lang['building']['trail_sm_cannon_total_output']    = 'Total cannon output';
$lang['building']['trail_sm_section_title']          = 'Per-Trail Snowmaking Equipment';
// Temperature feature
$lang['building']['trail_sm_temp_col']               = 'Temperature req.';
$lang['building']['trail_sm_temp_below_freezing']    = '≤ 0 °C (below freezing)';
$lang['building']['trail_sm_temp_any']               = 'Any temperature';
$lang['building']['trail_sm_temp_warning']           = 'Above-freezing weather: snow cannons cannot produce snow tonight.';
$lang['building']['trail_sm_equip_suspended']        = 'Suspended (too warm)';
// Brand why-choose section
$lang['building']['trail_sm_brand_why_title']        = 'Why Choose Each Brand?';
$lang['building']['trail_sm_col_brand_desc']         = 'About the brand';
$lang['building']['trail_sm_col_why_choose']         = 'Why choose it?';
// Bulk actions and new features
$lang['building']['trail_sm_start_all']              = 'Start All';
$lang['building']['trail_sm_stop_all']               = 'Stop All';
$lang['building']['trail_sm_all_started']            = '<div class="alert alert-success text-center">All trail snowmaking equipment started.</div>';
$lang['building']['trail_sm_all_stopped']            = '<div class="alert alert-warning text-center">All trail snowmaking equipment stopped.</div>';
$lang['building']['trail_sm_upgrade']                = 'Upgrade';
$lang['building']['trail_sm_upgrade_label']          = 'Change equipment / brand';
$lang['building']['trail_sm_upgraded']               = '<div class="alert alert-success text-center">Trail snowmaking equipment upgraded successfully.</div>';
$lang['building']['trail_sm_upgraded_log']           = 'Upgraded trail snowmaking equipment.';
// Snow level on snowmaking page
$lang['building']['trail_sm_snow_level_title']       = 'Current Snow Level';
$lang['building']['snow_target_label']               = 'Snow target (stop snowmaking when reached)';
// Guest Skill Progression
$lang['building']['guest_skill_title']                  = 'Guest Skill Progression';
$lang['building']['guest_skill_page_intro']             = 'Over seasons, your guests improve their skiing skills. More experienced guests spend more, boosting your skipass revenue.';
$lang['building']['guest_skill_distribution_title']     = 'Guest skill distribution';
$lang['building']['guest_skill_seasons_played_label']   = 'Seasons completed: ';
$lang['building']['guest_skill_level']                  = 'Skill level';
$lang['building']['guest_skill_share']                  = 'Share';
$lang['building']['guest_skill_revenue_bonus_label']    = 'Revenue bonus per guest';
$lang['building']['guest_skill_beginner']               = 'Beginner';
$lang['building']['guest_skill_intermediate']           = 'Intermediate';
$lang['building']['guest_skill_advanced']               = 'Advanced';
$lang['building']['guest_skill_current_multiplier']     = 'Current skipass revenue multiplier:';
$lang['building']['guest_skill_how_it_works']           = 'How it works';
$lang['building']['guest_skill_levelup_beginner']       = 'Each season, %d%% of beginner guests level up to Intermediate.';
$lang['building']['guest_skill_levelup_intermediate']   = 'Each season, %d%% of Intermediate guests level up to Advanced.';
$lang['building']['guest_skill_loyalty_note']           = 'Advanced guests bring long-term loyalty: higher skipass spending, return visitors, and a stronger season pass system.';

// Lift Line Management
$lang['building']['lift_line_title']              = 'Lift Line Management';
$lang['building']['lift_line_page_intro']         = 'Manage how your resort handles lift queues. Set a queue tolerance, enable a VIP fast pass lane, and reduce the risk of lift breakdowns from overloading.';
$lang['building']['lift_line_how_it_works']       = 'How it works';
$lang['building']['lift_line_how_it_works_desc']  = 'Each night the game calculates how long guests waited in line. If the average queue time exceeds your tolerance threshold, your resort loses reputation. A VIP fast pass lane lets a portion of guests bypass the queue, reducing the penalty. Lifts that are heavily overloaded also have a chance of being forced into maintenance.';
$lang['building']['lift_line_mechanic_queue']     = 'Queue time is estimated from your lifts\' daily throughput versus total visitors.';
$lang['building']['lift_line_mechanic_vip']       = 'VIP fast pass: ' . (int)(LIFT_LINE_VIP_BYPASS_RATIO * 100) . '% of guests pay the fast pass price and skip the queue, reducing the reputation penalty by ' . (int)(LIFT_LINE_VIP_REP_REDUCTION * 100) . '%.';
$lang['building']['lift_line_mechanic_breakdown'] = 'Overloaded lifts (more than ' . (int)(LIFT_LINE_OVERLOAD_RATIO * 100) . '% of capacity) have a ' . LIFT_LINE_BREAKDOWN_CHANCE . '% daily chance of being forced into maintenance.';
$lang['building']['lift_line_mechanic_reputation'] = 'Reputation loss: ' . LIFT_LINE_REP_PENALTY_PER_MIN . ' point per minute the queue exceeds your tolerance, up to ' . LIFT_LINE_MAX_REP_PENALTY . ' points per day.';
$lang['building']['lift_line_settings_title']     = 'Queue settings';
$lang['building']['lift_line_tolerance_label']    = 'Queue tolerance';
$lang['building']['lift_line_tolerance_help']     = 'Guests are willing to wait this many minutes before leaving. Lower = stricter penalty if queues are long.';
$lang['building']['lift_line_vip_enable_label']   = 'Enable VIP fast pass lane';
$lang['building']['lift_line_vip_help']           = 'When enabled, a portion of guests pay the fast pass price, bypassing the queue. This reduces queue-related reputation penalties.';
$lang['building']['lift_line_vip_price_label']    = 'VIP fast pass price';
$lang['building']['lift_line_vip_per_guest']      = 'guest/day';
$lang['building']['lift_line_save_btn']           = 'Save settings';
$lang['building']['lift_line_key_figures']        = 'Current settings';
$lang['building']['lift_line_vip_status_label']   = 'VIP fast pass';
$lang['building']['lift_line_vip_on']             = 'ON';
$lang['building']['lift_line_vip_off']            = 'OFF';
$lang['building']['lift_line_rep_penalty_label']  = 'Reputation penalty';
$lang['building']['lift_line_rep_penalty_desc']   = LIFT_LINE_REP_PENALTY_PER_MIN . ' pt / min over tolerance (max ' . LIFT_LINE_MAX_REP_PENALTY . ' pt/day)';
$lang['building']['lift_line_breakdown_label']    = 'Breakdown risk';
$lang['building']['lift_line_breakdown_desc']     = LIFT_LINE_BREAKDOWN_CHANCE . '% chance/day when lift is overloaded';
$lang['building']['lift_line_settings_saved']     = '<div class="alert alert-success text-center">Lift line settings saved successfully.</div>';
$lang['building']['lift_line_invalid_settings']   = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['lift_line_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// ============================================================
// Crowding System
// ============================================================
$lang['building']['crowding_title']                 = 'Crowding Management';
$lang['building']['crowding_page_intro']            = 'Control how many visitors your resort can handle each day. Set a capacity limit, define a crowd alert threshold, and enable timed entry to protect your guests\' experience and your reputation.';
$lang['building']['crowding_how_it_works']          = 'How it works';
$lang['building']['crowding_how_it_works_desc']     = 'Each night the game compares your daily visitors against your capacity limit. If the crowd exceeds your alert threshold, your resort loses reputation. Enabling timed entry halves the penalty and earns a small reputation bonus when your resort is well-managed.';
$lang['building']['crowding_mechanic_threshold']    = 'Crowd alert threshold: penalty applies when visitors exceed this % of your capacity limit.';
$lang['building']['crowding_mechanic_timed_entry']  = 'Timed entry: caps the effective crowd, halves any reputation penalty, and earns a ' . CROWDING_TIMED_ENTRY_REP_BONUS . '-point reputation bonus when crowding is well managed.';
$lang['building']['crowding_mechanic_reputation']   = 'Reputation loss: ' . CROWDING_REP_PENALTY_PER_PCT . ' point per % of visitors over the threshold, up to ' . CROWDING_MAX_REP_PENALTY . ' points per day.';
$lang['building']['crowding_mechanic_bonus']        = 'Reputation bonus: earn +' . CROWDING_TIMED_ENTRY_REP_BONUS . ' reputation when timed entry is on and crowding stays within the threshold.';
$lang['building']['crowding_settings_title']        = 'Crowding settings';
$lang['building']['crowding_capacity_label']        = 'Daily capacity limit';
$lang['building']['crowding_capacity_help']         = 'Maximum number of visitors your resort targets per day. Penalties apply when this number is exceeded above the alert threshold.';
$lang['building']['crowding_threshold_label']       = 'Crowd alert threshold';
$lang['building']['crowding_threshold_help']        = 'Reputation penalty starts when daily visitors reach this percentage of the capacity limit. Higher = more tolerant of crowding.';
$lang['building']['crowding_timed_entry_label']     = 'Enable timed entry';
$lang['building']['crowding_timed_entry_help']      = 'When enabled, the resort manages visitor flow to reduce crowding. Halves reputation penalties and earns a bonus when crowding is controlled.';
$lang['building']['crowding_save_btn']              = 'Save settings';
$lang['building']['crowding_key_figures']           = 'Current settings';
$lang['building']['crowding_visitors_per_day']      = 'visitors/day';
$lang['building']['crowding_timed_entry_status_label'] = 'Timed entry';
$lang['building']['crowding_timed_entry_on']        = 'ON';
$lang['building']['crowding_timed_entry_off']       = 'OFF';
$lang['building']['crowding_rep_penalty_label']     = 'Reputation penalty';
$lang['building']['crowding_rep_penalty_desc']      = CROWDING_REP_PENALTY_PER_PCT . ' pt / % over threshold (max ' . CROWDING_MAX_REP_PENALTY . ' pt/day)';
$lang['building']['crowding_rep_bonus_label']       = 'Reputation bonus';
$lang['building']['crowding_rep_bonus_desc']        = '+' . CROWDING_TIMED_ENTRY_REP_BONUS . ' pt when timed entry is on and crowding is within threshold';
$lang['building']['crowding_settings_saved']        = '<div class="alert alert-success text-center">Crowding settings saved successfully.</div>';
$lang['building']['crowding_invalid_settings']      = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['crowding_save_error']            = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// ============================================================
// Environmental System
// ============================================================
$lang['building']['env_title']              = 'Environmental Management';
$lang['building']['env_page_intro']         = 'Monitor and improve your resort\'s environmental impact. Keep carbon emissions and noise pollution low to avoid fines and expansion restrictions, and invest in green technologies to boost your eco reputation.';

// Status cards
$lang['building']['env_eco_reputation_title'] = 'Eco Reputation';
$lang['building']['env_eco_reputation_desc']  = 'Your overall environmental reputation (0–100). Boosted by green investments and harmed by high pollution.';
$lang['building']['env_carbon_title']         = 'Carbon Footprint';
$lang['building']['env_noise_title']          = 'Noise Pollution';

// Thresholds / consequences
$lang['building']['env_carbon_fine_at']       = 'Daily fine above';
$lang['building']['env_carbon_fine_desc']     = '500 € fine';
$lang['building']['env_carbon_restrict_at']   = 'Expansion restricted above';
$lang['building']['env_carbon_restrict_desc'] = '1 000 € fine + no new builds';
$lang['building']['env_expansion_restricted_warning'] = '<strong>Expansion restricted!</strong> Your carbon footprint is too high. Reduce pollution to unlock new construction.';
$lang['building']['env_noise_fine_at']        = 'Fine triggered above';
$lang['building']['env_noise_fine_desc']      = '300 € fine when wildlife zone is active';

// Wildlife zone
$lang['building']['env_wildlife_title']   = 'Wildlife Protection Zone';
$lang['building']['env_wildlife_desc']    = 'Designate part of your resort as a wildlife protection zone. Boosts eco reputation (+5) but increases noise fines if sound levels are high.';
$lang['building']['env_wildlife_on']      = 'Wildlife zone: ACTIVE';
$lang['building']['env_wildlife_off']     = 'Wildlife zone: INACTIVE';
$lang['building']['env_wildlife_enable']  = 'Activate wildlife zone';
$lang['building']['env_wildlife_disable'] = 'Deactivate wildlife zone';
$lang['building']['env_wildlife_zone_enabled']  = '<div class="alert alert-success text-center">Wildlife protection zone has been activated.</div>';
$lang['building']['env_wildlife_zone_disabled'] = '<div class="alert alert-warning text-center">Wildlife protection zone has been deactivated.</div>';

// Green Investments
$lang['building']['env_investments_title'] = 'Green Investments';

// Solar panels
$lang['building']['env_solar_title']       = 'Solar Panels';
$lang['building']['env_solar_desc']        = 'Install solar panels on your resort facilities. Reduces carbon footprint by 20 % and boosts eco reputation (+5).';
$lang['building']['env_solar_active']      = 'Solar panels installed';
$lang['building']['env_solar_cost_label']  = 'Investment cost';
$lang['building']['env_solar_buy']         = 'Install solar panels';
$lang['building']['env_solar_installed']   = '<div class="alert alert-success text-center">Solar panels have been installed. Your carbon footprint will decrease from tonight.</div>';
$lang['building']['env_solar_already_installed'] = '<div class="alert alert-info text-center">Solar panels are already installed.</div>';

// Electric groomers
$lang['building']['env_electric_groomer_title']       = 'Electric Snow Groomers';
$lang['building']['env_electric_groomer_desc']        = 'Purchase an electric snow groomer. It produces far less carbon and noise than a diesel groomer (+2 CO₂e vs +10, +1 dB vs +5).';
$lang['building']['env_electric_groomer_owned']       = 'Electric groomers owned';
$lang['building']['env_electric_groomer_cost_label']  = 'Cost per groomer';
$lang['building']['env_electric_groomer_buy']         = 'Buy electric groomer';
$lang['building']['env_electric_groomer_purchased']   = '<div class="alert alert-success text-center">Electric groomer purchased. It will contribute to a lower carbon footprint from tonight.</div>';

// Not enough cash
$lang['building']['env_not_enough_cash'] = '<div class="alert alert-warning text-center">Not enough cash to complete this purchase.</div>';

// Calculation table
$lang['building']['env_how_calculated_title'] = 'How is it calculated?';
$lang['building']['env_source']               = 'Source';
$lang['building']['env_carbon_impact']        = 'Carbon impact';
$lang['building']['env_noise_impact']         = 'Noise impact';
$lang['building']['env_source_lift']          = 'Active lift';
$lang['building']['env_source_cannon']        = 'Active snow cannon';
$lang['building']['env_source_groomer']       = 'Diesel groomer';
$lang['building']['env_source_electric_groomer'] = 'Electric groomer';
$lang['building']['env_source_solar']         = 'Solar panels';
$lang['building']['env_source_wildlife']      = 'Wildlife zone';
$lang['building']['env_source_wildlife_noise_note'] = 'Fine if noise > threshold';
$lang['building']['env_source_reforestation']      = 'Reforestation';
$lang['building']['env_source_reforestation_unit'] = 'batch';
$lang['building']['env_source_water_recycling']    = 'Water recycling system';
$lang['building']['env_source_water_recycling_note'] = '(cannon noise)';
$lang['building']['env_updated_nightly']      = 'Values are recalculated every night by the system.';

// Green Certification
$lang['building']['env_green_cert_badge']    = 'Green Certified';
$lang['building']['env_green_cert_desc']     = 'Awarded when eco reputation reaches 80 or above.';
$lang['building']['env_green_cert_achieved'] = 'Your resort has earned <strong>Green Certification</strong>! Guests appreciate your environmental commitment.';

// Reforestation Program
$lang['building']['env_reforestation_title']       = 'Reforestation Program';
$lang['building']['env_reforestation_desc']        = 'Plant trees around your resort. Each batch absorbs CO₂ (-'.ENV_TREE_CARBON_REDUCTION.' CO₂e/night) and boosts eco reputation (+'.ENV_TREE_REP_BONUS.'). Maximum '.ENV_MAX_TREE_COUNT.' batches.';
$lang['building']['env_reforestation_owned']       = 'Batches planted';
$lang['building']['env_reforestation_cost_label']  = 'Cost per batch';
$lang['building']['env_reforestation_buy']         = 'Plant trees';
$lang['building']['env_reforestation_max']         = 'Maximum reforestation reached';
$lang['building']['env_reforestation_planted']     = '<div class="alert alert-success text-center">Trees planted! Your carbon footprint will decrease from tonight.</div>';
$lang['building']['env_reforestation_max_reached'] = '<div class="alert alert-info text-center">You have reached the maximum number of reforestation batches.</div>';

// Water Recycling System
$lang['building']['env_water_recycling_title']             = 'Water Recycling System';
$lang['building']['env_water_recycling_desc']              = 'Install a water recycling system that collects snowmelt and reuses it for the snow cannons. Reduces cannon noise by '.(int)(ENV_WATER_RECYCLING_NOISE_REDUCTION * 100).' % and boosts eco reputation (+'.ENV_WATER_RECYCLING_REP_BONUS.').';
$lang['building']['env_water_recycling_active']            = 'Water recycling installed';
$lang['building']['env_water_recycling_cost_label']        = 'Investment cost';
$lang['building']['env_water_recycling_buy']               = 'Install water recycling';
$lang['building']['env_water_recycling_installed']         = '<div class="alert alert-success text-center">Water recycling system installed. Cannon noise will decrease from tonight.</div>';
$lang['building']['env_water_recycling_already_installed'] = '<div class="alert alert-info text-center">The water recycling system is already installed.</div>';
// ---------------------------------------------------------------
// Off-Season Management
// ---------------------------------------------------------------
$lang['off_season']['title']              = 'Off-Season Activities';
$lang['off_season']['title_sing']         = 'Off-Season Activity';
$lang['off_season']['intro']              = 'Keep your resort thriving all year long! Build summer activities to generate revenue even when the slopes are closed. These facilities attract visitors during the off-season and prevent winter-only income dependence.';
$lang['off_season']['max_daily_income']   = 'Max daily income';

// Mountain Biking
$lang['mountain_biking']['title']         = 'Mountain Biking';
$lang['mountain_biking']['title_sing']    = 'Mountain Biking Facility';
$lang['mountain_biking']['desc']          = 'Mountain biking trails and parks attract thrill-seekers during the summer months. Build a network of trails to welcome enthusiasts and generate off-season revenue.<br>You need to have the Tourist Information Center built in order to start building mountain biking facilities.';

// Hiking
$lang['hiking']['title']                  = 'Hiking';
$lang['hiking']['title_sing']             = 'Hiking Trail';
$lang['hiking']['desc']                   = 'Hiking trails open your resort to nature lovers during summer. A well-developed hiking network brings families and outdoor enthusiasts, generating steady off-season income.<br>You need to have the Tourist Information Center built in order to start building hiking facilities.';

// Festival
$lang['festival']['title']                = 'Festivals';
$lang['festival']['title_sing']           = 'Festival Venue';
$lang['festival']['desc']                 = 'Festival grounds and open-air concert venues transform your resort into a summer entertainment hub. Hosting concerts, food festivals, and cultural events attracts large crowds and boosts off-season revenue significantly.<br>You need to have the Tourist Information Center built in order to start building festival venues.';

// Wedding Venue
$lang['wedding_venue']['title']           = 'Weddings';
$lang['wedding_venue']['title_sing']      = 'Wedding Venue';
$lang['wedding_venue']['desc']            = 'A stunning mountain backdrop makes your resort the perfect wedding destination. From intimate garden ceremonies to luxury alpine celebrations, wedding venues deliver premium off-season revenue with a small but high-spending clientele.<br>You need to have the Tourist Information Center built in order to start building wedding venues.';

// Alpine Coaster
$lang['alpine_coaster']['title']          = 'Alpine Coaster';
$lang['alpine_coaster']['title_sing']     = 'Alpine Coaster';
$lang['alpine_coaster']['desc']           = 'An alpine coaster (mountain luge) is a thrilling summer attraction that families and adrenaline seekers love. Running on fixed tracks down the mountain, it operates from spring to autumn and generates consistent off-season revenue.<br>You need to have the Tourist Information Center built in order to start building an alpine coaster.';
// Mountain Master Plan System
$lang['building']['plan_title']                    = 'Mountain Master Plan';
$lang['building']['plan_intro']                    = 'Before embarking on major expansions, define a long-term development strategy for your resort. Your plan must receive government approval before it becomes active.';
$lang['building']['plan_how_it_works']             = 'How it works';
$lang['building']['plan_how_it_works_desc']        = 'The Mountain Master Plan sets out your 5-year development vision, covering zoning limits, environmental considerations and infrastructure targets. A plan must be approved before building activity is unrestricted.';
$lang['building']['plan_step_draft']               = 'Draft your plan: set name, expansion strategy, environmental notes, and zoning limits.';
$lang['building']['plan_step_submit']              = 'Submit for government review: costs';
$lang['building']['plan_step_review']              = 'Government review period';
$lang['building']['plan_step_activate']            = 'Activate the approved plan to begin your expansion.';
$lang['building']['plan_step_expire']              = 'Plans automatically expire after';
$lang['building']['plan_days']                     = 'days';
$lang['building']['plan_revision_warning']         = 'Warning:';
$lang['building']['plan_revision_desc']            = 'Changing a plan after approval costs';
$lang['building']['plan_reputation']               = 'reputation';
$lang['building']['plan_create_new']               = '+ New plan';
$lang['building']['plan_none']                     = 'You have no development plans yet. Create your first plan to get started.';
$lang['building']['plan_new_title']                = 'Create a new Mountain Master Plan';
$lang['building']['plan_edit_title']               = 'Edit Mountain Master Plan';
$lang['building']['plan_field_name']               = 'Plan name';
$lang['building']['plan_field_name_help']          = 'Give your plan a concise, descriptive name (max 100 characters).';
$lang['building']['plan_expansion_strategy']       = '5-year expansion strategy';
$lang['building']['plan_expansion_strategy_help']  = 'Describe the long-term development targets: new slopes, lifts, buildings, and infrastructure you plan to build over the next 5 years.';
$lang['building']['plan_environmental_notes']      = 'Environmental approval notes';
$lang['building']['plan_environmental_notes_help'] = 'Describe the environmental measures you will take: wildlife protection, deforestation limits, snowmaking impact, etc.';
$lang['building']['plan_zoning_limits']            = 'Zoning limits (maximum new additions over 5 years)';
$lang['building']['plan_zoning_slopes']            = 'Slopes';
$lang['building']['plan_zoning_lifts']             = 'Lifts';
$lang['building']['plan_zoning_buildings']         = 'Buildings';
$lang['building']['plan_zoning_limits_help']       = 'Set the maximum number of new slopes, lifts and buildings permitted under this plan.';
$lang['building']['plan_btn_save']                 = 'Save plan';
$lang['building']['plan_btn_edit']                 = 'Edit';
$lang['building']['plan_btn_submit']               = 'Submit for review';
$lang['building']['plan_btn_delete']               = 'Delete';
$lang['building']['plan_btn_activate']             = 'Activate plan';
$lang['building']['plan_btn_revise']               = 'Revise plan';
$lang['building']['plan_btn_withdraw']             = 'Withdraw';
$lang['building']['plan_btn_duplicate']            = 'Duplicate';
$lang['building']['plan_confirm_submit']           = 'Submit this plan for government review?';
$lang['building']['plan_confirm_delete']           = 'Delete this draft plan? This action cannot be undone.';
$lang['building']['plan_confirm_activate']         = 'Activate this approved plan?';
$lang['building']['plan_confirm_revise']           = 'Revise this plan? This will revert it to draft status and incur a cost and reputation penalty.';
$lang['building']['plan_confirm_withdraw']         = 'Withdraw this plan from government review? The submission fee is non-refundable.';
$lang['building']['plan_confirm_duplicate']        = 'Create a new draft copy of this plan?';
$lang['building']['plan_status_draft']             = 'Draft';
$lang['building']['plan_status_submitted']         = 'Submitted';
$lang['building']['plan_status_approved']          = 'Approved';
$lang['building']['plan_status_active']            = 'Active';
$lang['building']['plan_status_expired']           = 'Expired';
$lang['building']['plan_revised_badge']            = 'Revised';
$lang['building']['plan_created_on']               = 'Created on';
$lang['building']['plan_submitted_on']             = 'Submitted on';
$lang['building']['plan_approved_on']              = 'Approved on';
$lang['building']['plan_activated_on']             = 'Activated on';
$lang['building']['plan_expires_on']               = 'Expires on:';
$lang['building']['plan_expired_on']               = 'Expired on:';
$lang['building']['plan_review_pending']           = 'Government review pending. Approval within';
$lang['building']['plan_currently_active']         = '✓ Currently active';
// Feedback messages
$lang['building']['plan_created']                  = '<div class="alert alert-success text-center">Your development plan has been created successfully.</div>';
$lang['building']['plan_saved']                    = '<div class="alert alert-success text-center">Your development plan has been saved.</div>';
$lang['building']['plan_deleted']                  = '<div class="alert alert-success text-center">The draft plan has been deleted.</div>';
$lang['building']['plan_submitted']                = '<div class="alert alert-success text-center">Your plan has been submitted for government review. The review takes up to ' . MASTER_PLAN_APPROVAL_DAYS . ' days.</div>';
$lang['building']['plan_activated']                = '<div class="alert alert-success text-center">Your development plan is now active. Good luck with your expansion!</div>';
$lang['building']['plan_revised']                  = '<div class="alert alert-warning text-center">Your plan has been reverted to draft for revision. The revision fee and reputation penalty have been applied.</div>';
$lang['building']['plan_withdrawn']                = '<div class="alert alert-warning text-center">Your plan has been withdrawn from government review and returned to draft. The submission fee is non-refundable.</div>';
$lang['building']['plan_duplicated']               = '<div class="alert alert-success text-center">A new draft copy of your plan has been created.</div>';
$lang['building']['plan_not_editable']             = '<div class="alert alert-danger text-center">This plan cannot be edited. Only draft plans can be modified.</div>';
$lang['building']['plan_not_submittable']          = '<div class="alert alert-danger text-center">This plan cannot be submitted. Only draft plans can be submitted.</div>';
$lang['building']['plan_not_activatable']          = '<div class="alert alert-danger text-center">This plan cannot be activated. Only approved plans can be activated.</div>';
$lang['building']['plan_not_revisable']            = '<div class="alert alert-danger text-center">This plan cannot be revised. Only approved or active plans can be revised.</div>';
$lang['building']['plan_not_deletable']            = '<div class="alert alert-danger text-center">This plan cannot be deleted. Only draft plans can be deleted.</div>';
$lang['building']['plan_not_withdrawable']         = '<div class="alert alert-danger text-center">This plan cannot be withdrawn. Only submitted plans can be withdrawn.</div>';
$lang['building']['plan_not_enough_cash']          = '<div class="alert alert-danger text-center">You do not have enough cash to perform this action.</div>';
$lang['building']['plan_validation_error']         = '<div class="alert alert-danger text-center">Invalid plan data. Please check all fields and try again.</div>';
// Log messages
$lang['building']['plan_log_created']              = 'Created a new Mountain Master Plan.';
$lang['building']['plan_log_submitted']            = 'Submitted Mountain Master Plan for government review.';
$lang['building']['plan_log_activated']            = 'Activated Mountain Master Plan.';
$lang['building']['plan_log_revised']              = 'Revised Mountain Master Plan (revision fee and reputation penalty applied).';
$lang['building']['plan_log_withdrawn']            = 'Withdrew Mountain Master Plan from government review.';
$lang['building']['plan_log_duplicated']           = 'Duplicated a Mountain Master Plan as a new draft.';

// ---------------------------------------------------------------------------
// Power & Energy System
// ---------------------------------------------------------------------------
$lang['building']['energy_title']             = 'Power & Energy Management';
$lang['building']['energy_page_intro']        = 'Manage your resort\'s electricity supply and demand. Lifts and snow cannons consume power every day. Build renewable energy sources to reduce your grid electricity bill.';

// Balance section
$lang['building']['energy_balance_title']     = 'Daily Energy Balance';
$lang['building']['energy_consumption_label'] = 'Consumption';
$lang['building']['energy_production_label']  = 'Production';
$lang['building']['energy_lifts_label']       = 'Lifts (open)';
$lang['building']['energy_cannons_label']     = 'Snow cannons (active)';
$lang['building']['energy_total_consumption'] = 'Total consumption';
$lang['building']['energy_solar_label']       = 'Solar panels';
$lang['building']['energy_hydro_label']       = 'Hydro plant';
$lang['building']['energy_total_production']  = 'Total production';
$lang['building']['energy_grid_section']      = 'Grid electricity (fallback)';
$lang['building']['energy_grid_kwh_label']    = 'Bought from grid';
$lang['building']['energy_grid_rate_label']   = 'Grid rate';
$lang['building']['energy_daily_grid_cost']   = 'Daily grid cost';
$lang['building']['energy_daily_savings']     = 'Savings vs. 100 % grid';
$lang['building']['energy_unit_lifts']        = 'lifts';
$lang['building']['energy_unit_cannons']      = 'cannons';
$lang['building']['energy_unit_panels']       = 'panels';
$lang['building']['energy_day']               = 'day';
$lang['building']['energy_built']             = 'Built';
$lang['building']['energy_not_built']         = 'Not built';
$lang['building']['energy_night_skiing_note'] = 'Note: Night skiing electricity is billed separately on the Night Skiing page.';

// Solar panels
$lang['building']['energy_solar_manage_title']    = 'Solar Panels';
$lang['building']['energy_solar_desc']            = 'Each solar panel unit generates clean electricity every day, reducing your grid bill. Maximum ' . ENERGY_SOLAR_PANEL_MAX . ' units.';
$lang['building']['energy_solar_current']         = 'Current panels';
$lang['building']['energy_solar_output_per_panel']= 'Output per panel';
$lang['building']['energy_solar_cost_per_panel']  = 'Cost per panel';
$lang['building']['energy_solar_buy_btn']         = 'Buy 1 solar panel';
$lang['building']['energy_solar_sell_btn']        = 'Sell 1 solar panel';
$lang['building']['energy_solar_sell_confirm']    = 'Sell one solar panel for a 50 % refund?';
$lang['building']['energy_refund']                = 'refund';
$lang['building']['energy_solar_max_reached']     = '<div class="alert alert-warning text-center">You have reached the maximum number of solar panels (' . ENERGY_SOLAR_PANEL_MAX . ').</div>';
$lang['building']['energy_solar_none_to_sell']    = '<div class="alert alert-warning text-center">You have no solar panels to sell.</div>';
$lang['building']['energy_solar_panel_bought']    = '<div class="alert alert-success text-center">Solar panel purchased successfully.</div>';
$lang['building']['energy_solar_panel_sold']      = '<div class="alert alert-success text-center">Solar panel sold. Refund added to your cash.</div>';

// Hydro plant
$lang['building']['energy_hydro_manage_title']    = 'Hydro Plant';
$lang['building']['energy_hydro_desc']            = 'A hydro plant produces a large amount of electricity every day from flowing water. One-time investment, no ongoing cost.';
$lang['building']['energy_hydro_status_label']    = 'Status';
$lang['building']['energy_hydro_output']          = 'Daily output';
$lang['building']['energy_hydro_cost_label']      = 'Build cost';
$lang['building']['energy_hydro_build_btn']       = 'Build hydro plant';
$lang['building']['energy_hydro_build_confirm']   = 'Build the hydro plant? This is a one-time investment with no refund if demolished.';
$lang['building']['energy_hydro_demolish_btn']    = 'Demolish hydro plant';
$lang['building']['energy_hydro_demolish_confirm']= 'Demolish the hydro plant? You will not receive any refund.';
$lang['building']['energy_hydro_already_built']   = '<div class="alert alert-warning text-center">The hydro plant is already built.</div>';
$lang['building']['energy_hydro_not_built']       = '<div class="alert alert-warning text-center">There is no hydro plant to demolish.</div>';
$lang['building']['energy_hydro_built']           = '<div class="alert alert-success text-center">Hydro plant built successfully! It will start producing electricity tonight.</div>';
$lang['building']['energy_hydro_demolished']      = '<div class="alert alert-success text-center">Hydro plant demolished.</div>';

// Grid electricity
$lang['building']['energy_grid_title']     = 'Grid Electricity';
$lang['building']['energy_grid_desc']      = 'The power grid is always available as a fallback. You are automatically charged for any electricity your resort needs beyond what your own sources produce.';
$lang['building']['energy_grid_always_on'] = 'Always available';
// Realistic Snowmaking System — water reservoir, electricity, staff, temperature
$lang['building']['snowmaking_requirements_title']   = 'Snowmaking Requirements';
$lang['building']['snowmaking_water_label']          = 'Water reservoir';
$lang['building']['snowmaking_water_low']            = '<div class="alert alert-warning">⚠️ Water reservoir is low! Snowmaking production will be reduced. Snowing or rainy nights refill the reservoir automatically.</div>';
$lang['building']['snowmaking_water_empty']          = '<div class="alert alert-danger">🚫 Water reservoir is empty! No artificial snow will be produced tonight. Wait for precipitation to refill it.</div>';
$lang['building']['snowmaking_staff_label']          = 'Snowmaking operators';
$lang['building']['snowmaking_staff_missing']        = '<div class="alert alert-danger">🚫 No snowmaking operator hired! Hire at least '.SNOWMAKING_MIN_STAFF.' "Snowmaking Operator" staff to run the cannons. <a href="'.base_url().'hire_staff_controller">Go to Hire Staff →</a></div>';
$lang['building']['snowmaking_staff_ok']             = 'Snowmaking operators on duty';
$lang['building']['snowmaking_electricity_label']    = 'Electricity cost per night';
$lang['building']['snowmaking_temp_label']           = 'Temperature requirement';
$lang['building']['snowmaking_temp_ok']              = 'Below freezing — production active';
$lang['building']['snowmaking_temp_blocked']         = '<div class="alert alert-warning">🌡️ Temperature above freezing — snow cannons cannot produce snow tonight.</div>';
$lang['building']['snowmaking_cannon_elec_per']      = '€ per active cannon per night';
$lang['building']['snowmaking_trail_elec_per']       = '€ per active trail unit per night';
$lang['building']['snowmaking_water_refill_info']    = 'Once purchased, the reservoir refills automatically: +'.SNOWMAKING_WATER_REFILL_SNOW.'% on snowing nights, +'.SNOWMAKING_WATER_REFILL_RAIN.'% on rainy nights.';
$lang['building']['water_reservoir_not_purchased']   = '<div class="alert alert-danger">🚫 No water reservoir! You must purchase a water reservoir before any snowmaking equipment will work. See the "Water Reservoir" section below.</div>';
$lang['building']['water_reservoir_buy_title']       = 'Water Reservoir';
$lang['building']['water_reservoir_buy_desc']        = 'A water reservoir is required for all snowmaking operations (snow cannons). Without it, no artificial snow can be produced. Once purchased it is permanent and refills automatically with precipitation.';
$lang['building']['water_reservoir_buy_cost']        = 'Purchase cost';
$lang['building']['water_reservoir_buy_btn']         = 'Purchase Water Reservoir';
$lang['building']['water_reservoir_buy_confirm']     = 'Purchase a water reservoir for '.number_format(WATER_RESERVOIR_COST, 0, ',', ' ').' €? This is a one-time cost.';
$lang['building']['water_reservoir_purchased']       = '<div class="alert alert-success">✅ Water reservoir purchased! Your snowmaking equipment can now operate.</div>';
$lang['building']['water_reservoir_already_purchased'] = '<div class="alert alert-warning text-center">The water reservoir is already purchased.</div>';
$lang['building']['water_reservoir_required_for_snowmaking'] = '<div class="alert alert-info">Purchase a water reservoir (see section above) to unlock snowmaking equipment.</div>';
$lang['building']['water_reservoir_purchased_log']   = 'Purchased a water reservoir.';
// Snowmaking Mode
$lang['building']['snowmaking_mode_title']           = 'Snowmaking Mode';
$lang['building']['snowmaking_mode_intro']           = 'Choose the operating mode for your snow cannons. Eco mode lowers output and saves costs; Boost mode maximises snow production at higher electricity cost. The active mode is highlighted.';
$lang['building']['snowmaking_mode_normal']          = 'Normal (100% output, 100% cost)';
$lang['building']['snowmaking_mode_eco']             = 'Eco (70% output, 70% cost)';
$lang['building']['snowmaking_mode_boost']           = 'Boost (140% output, 160% cost)';
$lang['building']['snowmaking_mode_col_mode']        = 'Mode';
$lang['building']['snowmaking_mode_col_output']      = 'Snow output';
$lang['building']['snowmaking_mode_col_cost']        = 'Electricity cost';
$lang['building']['save_snowmaking_mode']            = 'Save mode';
$lang['building']['snowmaking_mode_saved']           = '<div class="alert alert-success text-center">Snowmaking mode saved.</div>';
// Tonight\'s Projected Output
$lang['building']['snowmaking_projected_title']              = 'Tonight\'s Projected Snowmaking Output';
$lang['building']['snowmaking_projected_output']             = 'Projected snow addition';
$lang['building']['snowmaking_projected_elec']               = 'Electricity cost tonight';
$lang['building']['snowmaking_projected_blocked_temp']       = 'Temperature is above freezing — snow cannons cannot operate tonight.';
$lang['building']['snowmaking_projected_blocked_staff']      = 'No snowmaking operator hired — cannons cannot run. Hire at least '.SNOWMAKING_MIN_STAFF.' operator first.';
$lang['building']['snowmaking_projected_blocked_water_empty'] = 'Water reservoir is empty — no snow will be produced tonight.';
$lang['building']['snowmaking_projected_blocked_no_reservoir'] = 'No water reservoir purchased — cannons cannot operate. Purchase a water reservoir first.';
$lang['building']['snowmaking_page_link']            = 'Snowmaking management';
// Snowmaking Efficiency
$lang['building']['snowmaking_efficiency_title']     = 'Snowmaking Efficiency';
$lang['building']['snowmaking_efficiency_intro']     = 'Efficiency shows how much snow (in cm) your cannons produce per 100 € spent on electricity tonight. Higher is better.';
$lang['building']['snowmaking_efficiency_label']     = 'Efficiency';
$lang['building']['snowmaking_efficiency_unit']      = 'cm per 100 €';
// Snowmaking Schedule
$lang['building']['snowmaking_schedule_title']       = 'Snowmaking Schedule';
$lang['building']['snowmaking_schedule_intro']       = 'Select which nights of the week your snow cannons are allowed to run. Deselecting nights saves electricity costs on those days.';
$lang['building']['save_snowmaking_schedule']        = 'Save schedule';
$lang['building']['snowmaking_schedule_saved']       = '<div class="alert alert-success text-center">Snowmaking schedule saved.</div>';
$lang['building']['snowmaking_schedule_days']        = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
// Emergency Municipal Water Refill
$lang['building']['municipal_refill_title']             = 'Emergency Municipal Water Refill';
$lang['building']['municipal_refill_intro']             = 'In drought conditions or when snowmelt is low, you can use municipal water as an emergency backup to refill the reservoir instantly. This is expensive and harms your eco reputation.';
$lang['building']['municipal_refill_locked_msg']        = 'Locked — requires Resort Level %d (%d open lifts needed). Expand your resort to unlock this feature.';
$lang['building']['municipal_refill_no_reservoir_msg']  = 'You must purchase a water reservoir before using municipal water.';
$lang['building']['municipal_refill_not_needed_msg']    = 'Municipal refill is only available as an emergency measure when the reservoir drops below %d%%. Your reservoir is currently above this threshold.';
$lang['building']['municipal_refill_available_title']   = 'Emergency refill available';
$lang['building']['municipal_refill_available_desc']    = 'Add +%d%% to the reservoir for <strong>%s €</strong>. Environmental impact: −%d eco reputation, −%d resort reputation.';
$lang['building']['municipal_refill_confirm']           = 'Use municipal water to refill the reservoir for %s €? This will apply eco and reputation penalties.';
$lang['building']['municipal_refill_btn']               = 'Use Municipal Water (+'.MUNICIPAL_WATER_REFILL_AMOUNT.'%)';
$lang['building']['municipal_refill_notes']             = 'Using municipal water is 2–3× more expensive than natural precipitation and has a negative environmental impact. Use only when no other option is available.';
$lang['building']['municipal_refill_success']           = '<div class="alert alert-warning">⚠️ Emergency municipal water used. The reservoir has been refilled (+'.MUNICIPAL_WATER_REFILL_AMOUNT.'%). Eco and reputation penalties applied.</div>';
$lang['building']['municipal_refill_locked']            = '<div class="alert alert-danger">🔒 Municipal water refill is locked. Requires Resort Level '.MUNICIPAL_WATER_UNLOCK_LIFTS.'+.</div>';
$lang['building']['municipal_refill_no_reservoir']      = '<div class="alert alert-danger">🚫 Purchase a water reservoir first before using municipal water.</div>';
$lang['building']['municipal_refill_not_needed']        = '<div class="alert alert-info">ℹ️ Municipal water refill is only available when the reservoir is below '.MUNICIPAL_WATER_MAX_RESERVOIR_PCT.'%.</div>';
$lang['building']['municipal_refill_log']               = 'Used emergency municipal water to refill the snowmaking reservoir.';
// Real Estate Development
$lang['building']['real_estate_title']                 = 'Private Real Estate Development';
$lang['building']['real_estate_intro']                 = 'Develop ski-in ski-out properties, luxury chalets, and condos. Sell them for a one-time revenue or keep them to generate long-term passive rental income.';
$lang['building']['real_estate_develop_title']         = 'Develop a new property';
$lang['building']['real_estate_develop_intro']         = 'Choose a property type to start construction. Only one property can be built at a time.';
$lang['building']['real_estate_my_properties']         = 'My properties';
$lang['building']['real_estate_no_properties']         = 'You have not developed any properties yet. Start by building one above!';
$lang['building']['real_estate_how_it_works']          = 'How it works';
$lang['building']['real_estate_how_it_works_desc']     = 'Develop properties by paying the build cost. After the construction period, the property generates daily rental income automatically (paid each night). You can sell any renting property at any time for a one-time revenue. A property tax is deducted from the daily rent. Only one property can be under construction at a time.';
// Property types
$lang['building']['real_estate_type_ski_in_ski_out']   = 'Ski-in / Ski-out property';
$lang['building']['real_estate_type_luxury_chalet']    = 'Luxury chalet';
$lang['building']['real_estate_type_condo']            = 'Condo';
// Property detail labels
$lang['building']['real_estate_build_cost']            = 'Build cost';
$lang['building']['real_estate_build_time']            = 'Construction time';
$lang['building']['real_estate_sale_price']            = 'Sale price (one-time)';
$lang['building']['real_estate_daily_rent']            = 'Daily rent (net)';
$lang['building']['real_estate_after_tax']             = 'after';
$lang['building']['real_estate_days']                  = 'days';
$lang['building']['real_estate_day']                   = 'day';
// Status labels
$lang['building']['real_estate_status_under_construction'] = 'Under construction';
$lang['building']['real_estate_status_renting']        = 'Renting';
$lang['building']['real_estate_status_for_sale']       = 'For sale';
$lang['building']['real_estate_status_sold']           = 'Sold';
// Table columns
$lang['building']['real_estate_col_type']              = 'Type';
$lang['building']['real_estate_col_status']            = 'Status';
$lang['building']['real_estate_col_completion']        = 'Completion date';
$lang['building']['real_estate_col_net_rent']          = 'Net daily rent';
$lang['building']['real_estate_col_actions']           = 'Actions';
// Buttons
$lang['building']['real_estate_develop_btn']           = 'Develop';
$lang['building']['real_estate_sell_btn']              = 'Sell';
$lang['building']['real_estate_keep_for_rent']         = 'Keep for rent';
// Confirm messages
$lang['building']['real_estate_confirm_develop']       = 'Are you sure you want to start construction?';
$lang['building']['real_estate_confirm_sell']          = 'Are you sure you want to sell this property?';
// Action result messages
$lang['building']['real_estate_construction_started']  = '<div class="alert alert-success text-center">Construction started! The property will be ready to rent or sell once completed.</div>';
$lang['building']['real_estate_construction_ongoing']  = '<div class="alert alert-warning text-center">There is already a property under construction. Wait for it to be completed before starting a new one.</div>';
$lang['building']['real_estate_construction_in_progress'] = 'A property is currently under construction. You can start a new one once it is completed.';
$lang['building']['real_estate_not_enough_money']      = '<div class="alert alert-danger text-center">You do not have enough money to develop this property.</div>';
$lang['building']['real_estate_bad_type']              = '<div class="alert alert-danger text-center">Invalid property type selected.</div>';
$lang['building']['real_estate_bad_action']            = '<div class="alert alert-danger text-center">This action cannot be performed.</div>';
$lang['building']['real_estate_sold']                  = '<div class="alert alert-success text-center">Property sold successfully! The revenue has been added to your account.</div>';
$lang['building']['real_estate_set_renting']           = '<div class="alert alert-success text-center">Property is now set to renting and will generate daily income.</div>';
$lang['building']['real_estate_set_for_sale']          = '<div class="alert alert-info text-center">Property is now listed for sale.</div>';
// Log messages
$lang['building']['real_estate_develop_log']           = 'Started construction of a';
$lang['building']['real_estate_sold_log']              = 'Sold a';
$lang['building']['real_estate_rent_log']              = 'Passive rental income from real estate:';
// Require tourist info
$lang['building']['tourist_info_required']             = '<div class="alert alert-warning text-center">You need to build a Tourist Information Center before developing real estate.</div>';
// Town Development
$lang['building']['town_title']                   = 'Local Town Development';
$lang['building']['town_intro']                   = 'The nearby town grows alongside your resort. Open hotels, build reputation, and keep the town thriving to unlock infrastructure bonuses and higher property values.';
$lang['building']['town_status_label']            = 'Current Town Status';
$lang['building']['town_level_label']             = 'Level';
$lang['building']['town_level_0']                 = 'No Town';
$lang['building']['town_level_1']                 = 'Hamlet';
$lang['building']['town_level_2']                 = 'Village';
$lang['building']['town_level_3']                 = 'Town';
$lang['building']['town_level_4']                 = 'Resort Town';
$lang['building']['town_level_5']                 = 'Alpine City';
$lang['building']['town_max_level']               = 'Maximum town level reached – your resort has a thriving Alpine City!';
$lang['building']['town_progress_label']          = 'Growth points:';
$lang['building']['town_points_label']            = 'pts';
$lang['building']['town_points_needed']           = 'pts needed';
$lang['building']['town_next_level_label']        = 'Next level';
$lang['building']['town_key_figures']             = 'Town Statistics';
$lang['building']['town_property_value_label']    = 'Property value index';
$lang['building']['town_property_value_help']     = '100% = baseline; rises with each level';
$lang['building']['town_infrastructure_label']    = 'Infrastructure level';
$lang['building']['town_open_hotels_label']       = 'Open hotels';
$lang['building']['town_levels_title']            = 'Town Levels Overview';
$lang['building']['town_col_level']               = 'Level';
$lang['building']['town_col_name']                = 'Name';
$lang['building']['town_col_points']              = 'Points';
$lang['building']['town_col_property']            = 'Property value';
$lang['building']['town_how_it_works']            = 'How the Town Grows';
$lang['building']['town_how_it_works_desc']       = 'Every night the nightly job recalculates your town\'s growth based on your resort\'s activity:';
$lang['building']['town_growth_hotel_tip']        = 'Each open hotel adds <strong>%d growth points</strong> per night.';
$lang['building']['town_growth_reputation_tip']   = 'Each reputation point adds <strong>%.1f growth points</strong> per night.';
$lang['building']['town_neglect_tip']             = 'If you have no hotels open, the town decays and your resort\'s reputation is penalised each night.';
$lang['building']['town_neglect_warning_title']   = 'Town Neglect Warning!';
$lang['building']['town_neglect_warning_desc']    = 'Your town is being neglected — no hotels are open. The town is decaying and your resort loses <strong>%d reputation points</strong> per night until hotels are opened.';

// Insurance
$lang['building']['insurance_title']                  = 'Insurance';
$lang['building']['insurance_page_intro']             = 'Protect your resort against financial losses from lift accidents and storm damage. Choose a plan, pay a daily premium, and let insurance cover part of the cost when incidents occur.';
$lang['building']['insurance_how_it_works']           = 'How it works';
$lang['building']['insurance_how_it_works_desc']      = 'Each night the game charges a premium based on your selected plan. When a covered incident occurs, the insurance pays a cash payout directly into your resort account.';
$lang['building']['insurance_mechanic_premium']       = 'A daily premium is deducted from your cash while your plan is active.';
$lang['building']['insurance_mechanic_lift_accident'] = 'Basic &amp; Premium: a lift-accident claim pays a flat cash amount to help cover repair costs.';
$lang['building']['insurance_mechanic_storm']         = 'Premium only: each storm-damaged lift triggers an additional payout per affected lift.';
$lang['building']['insurance_mechanic_claims']        = 'Every claim is recorded and affects your finances — track them on this page.';
$lang['building']['insurance_plans_title']            = 'Plan comparison';
$lang['building']['insurance_col_plan']               = 'Plan';
$lang['building']['insurance_col_premium']            = 'Daily premium';
$lang['building']['insurance_col_lift_payout']        = 'Lift-accident payout';
$lang['building']['insurance_col_storm_payout']       = 'Storm payout (per lift)';
$lang['building']['insurance_plan_none']              = 'None';
$lang['building']['insurance_plan_basic']             = 'Basic';
$lang['building']['insurance_plan_premium']           = 'Premium';
$lang['building']['insurance_plan_none_desc']         = 'No coverage, no daily cost.';
$lang['building']['insurance_plan_basic_desc']        = 'Covers lift accidents. Daily premium: ' . number_format(INSURANCE_DAILY_PREMIUM_BASIC, 0, '.', ' ') . ' €. Payout: ' . number_format(INSURANCE_LIFT_PAYOUT_BASIC, 0, '.', ' ') . ' €/claim.';
$lang['building']['insurance_plan_premium_desc']      = 'Covers lift accidents &amp; storm damage. Daily premium: ' . number_format(INSURANCE_DAILY_PREMIUM_PREMIUM, 0, '.', ' ') . ' €. Payout: ' . number_format(INSURANCE_LIFT_PAYOUT_PREMIUM, 0, '.', ' ') . ' €/accident + ' . number_format(INSURANCE_STORM_PAYOUT_PER_LIFT, 0, '.', ' ') . ' €/storm-damaged lift.';
$lang['building']['insurance_per_day']                = 'day';
$lang['building']['insurance_per_lift']               = 'lift';
$lang['building']['insurance_select_plan_title']      = 'Select your plan';
$lang['building']['insurance_save_btn']               = 'Save plan';
$lang['building']['insurance_status_title']           = 'Current insurance status';
$lang['building']['insurance_active_plan_label']      = 'Active plan';
$lang['building']['insurance_daily_cost_label']       = 'Daily premium';
$lang['building']['insurance_total_claims_label']     = 'Total claims paid';
$lang['building']['insurance_total_claimed_label']    = 'Total amount claimed';
$lang['building']['insurance_settings_saved']         = '<div class="alert alert-success text-center">Insurance plan saved successfully.</div>';
$lang['building']['insurance_save_error']             = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// VIP & Loyalty Programs
$lang['building']['vip_loyalty_title']                  = 'VIP & Loyalty Programs';
$lang['building']['vip_loyalty_page_intro']             = 'Reward your most frequent skiers and offer premium services to VIP guests. A well-run loyalty programme attracts repeat visitors, while VIP services generate premium revenue and reputation.';
$lang['building']['vip_loyalty_how_it_works']           = 'How it works';
$lang['building']['vip_loyalty_how_it_works_desc']      = 'Each night the game evaluates your VIP and loyalty settings and applies revenue, costs, and reputation changes accordingly. Enabling more services increases complexity and cost but also boosts reputation and income.';
$lang['building']['vip_loyalty_mechanic_loyalty']       = 'Loyalty programme: ' . (int)(VIP_LOYALTY_VISITOR_PCT * 100) . '% of daily visitors are considered loyal guests and receive your configured discount. This costs revenue but earns +' . VIP_LOYALTY_REP_BONUS . ' reputation per night.';
$lang['building']['vip_loyalty_mechanic_private_lift']  = 'Private lift: reserved lift access for VIP guests. Costs ' . VIP_PRIVATE_LIFT_COST . ' €/night, earns +' . VIP_PRIVATE_LIFT_REP_BONUS . ' reputation and generates premium lift revenue.';
$lang['building']['vip_loyalty_mechanic_premium_slopes']= 'Premium slopes: exclusive slope access for VIP guests. Costs ' . VIP_PREMIUM_SLOPES_COST . ' €/night and earns +' . VIP_PREMIUM_SLOPES_REP_BONUS . ' reputation per night.';
$lang['building']['vip_loyalty_mechanic_concierge']     = 'Concierge service: personal assistance for VIP guests. Costs ' . VIP_CONCIERGE_COST . ' €/night, earns +' . VIP_CONCIERGE_REP_BONUS . ' reputation, and generates ' . VIP_CONCIERGE_REVENUE_PER_VISITOR . ' €/concierge guest.';
$lang['building']['vip_loyalty_mechanic_airport_transfer'] = 'Airport transfer: premium shuttle service from the nearest airport for VIP guests. Costs ' . VIP_AIRPORT_TRANSFER_COST . ' €/night, earns +' . VIP_AIRPORT_TRANSFER_REP_BONUS . ' reputation, and generates ' . VIP_AIRPORT_TRANSFER_REVENUE_PER_VISITOR . ' €/transfer guest.';
$lang['building']['vip_loyalty_mechanic_apreski_lounge']   = 'Après-ski lounge: exclusive evening entertainment venue for VIP guests. Costs ' . VIP_APRESKI_LOUNGE_COST . ' €/night, earns +' . VIP_APRESKI_LOUNGE_REP_BONUS . ' reputation, and generates ' . VIP_APRESKI_LOUNGE_REVENUE_PER_VISITOR . ' €/lounge guest.';
$lang['building']['vip_loyalty_settings_title']         = 'Programme settings';
$lang['building']['vip_loyalty_enable_loyalty_label']   = 'Enable loyalty discount programme';
$lang['building']['vip_loyalty_enable_loyalty_help']    = 'When enabled, frequent skiers receive a discount on their ski pass, increasing satisfaction and reputation at a small revenue cost.';
$lang['building']['vip_loyalty_discount_label']         = 'Loyalty discount';
$lang['building']['vip_loyalty_discount_help']          = 'Percentage discount granted to loyal returning guests on their ski pass price.';
$lang['building']['vip_loyalty_private_lift_label']     = 'VIP private lift service';
$lang['building']['vip_loyalty_private_lift_help']      = 'Costs %d €/night to operate. Awards +%d reputation per night and generates premium revenue from VIP lift guests.';
$lang['building']['vip_loyalty_premium_slopes_label']   = 'VIP premium slope access';
$lang['building']['vip_loyalty_premium_slopes_help']    = 'Costs %d €/night to operate. Awards +%d reputation per night from offering exclusive slope access.';
$lang['building']['vip_loyalty_concierge_label']        = 'VIP concierge service';
$lang['building']['vip_loyalty_concierge_help']         = 'Costs %d €/night to operate. Awards +%d reputation per night and generates concierge service revenue.';
$lang['building']['vip_loyalty_airport_transfer_label'] = 'VIP airport transfer service';
$lang['building']['vip_loyalty_airport_transfer_help']  = 'Costs %d €/night to operate. Awards +%d reputation per night and generates premium transfer revenue from VIP guests arriving from the airport.';
$lang['building']['vip_loyalty_apreski_lounge_label']   = 'VIP après-ski lounge';
$lang['building']['vip_loyalty_apreski_lounge_help']    = 'Costs %d €/night to operate. Awards +%d reputation per night and generates lounge revenue from VIP evening entertainment guests.';
$lang['building']['vip_loyalty_save_btn']               = 'Save settings';
$lang['building']['vip_loyalty_key_figures']            = 'Current settings';
$lang['building']['vip_loyalty_loyalty_status_label']   = 'Loyalty programme';
$lang['building']['vip_loyalty_on']                     = 'ON';
$lang['building']['vip_loyalty_off']                    = 'OFF';
$lang['building']['vip_loyalty_rep_gain_label']         = 'Nightly reputation gain';
$lang['building']['vip_loyalty_rep_gain_desc']          = 'Up to +' . (VIP_LOYALTY_REP_BONUS + VIP_PRIVATE_LIFT_REP_BONUS + VIP_PREMIUM_SLOPES_REP_BONUS + VIP_CONCIERGE_REP_BONUS + VIP_AIRPORT_TRANSFER_REP_BONUS + VIP_APRESKI_LOUNGE_REP_BONUS) . ' pts/night (all services active)';
$lang['building']['vip_loyalty_settings_saved']         = '<div class="alert alert-success text-center">VIP & Loyalty settings saved successfully.</div>';
$lang['building']['vip_loyalty_invalid_settings']       = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['vip_loyalty_save_error']             = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';

// ============================================================
// Celebrity / VIP Visits
// ============================================================
$lang['building']['celebrity_visit_title']                  = 'VIP & Celebrity Visits';
$lang['building']['celebrity_visit_intro']                  = 'Sometimes a famous face drops in on your resort. Influencers, pro skiers, and film crews can give your reputation a huge boost – or cause a PR disaster if your lifts let you down.';
$lang['building']['celebrity_visit_how_it_works']           = 'How it works';
$lang['building']['celebrity_visit_how_it_works_desc']      = 'Each night there is a random chance that a celebrity visits your resort. The reputation impact depends on your slope conditions and whether any lifts are out of order at the time.';
$lang['building']['celebrity_visit_mechanic_chance']        = 'There is a <strong>%d%%</strong> chance each night of a celebrity visit.';
$lang['building']['celebrity_visit_mechanic_types']         = 'Possible visitor types: <strong>Social-media influencer</strong>, <strong>Pro skier</strong>, <strong>Film crew</strong>.';
$lang['building']['celebrity_visit_mechanic_good_slopes']   = 'If your slopes are in good condition, your resort earns <strong>+%d reputation</strong> (big spike!).';
$lang['building']['celebrity_visit_mechanic_base']          = 'If your slopes are average, your resort still earns a modest <strong>+%d reputation</strong>.';
$lang['building']['celebrity_visit_mechanic_lift_fail']     = 'If any lift is in maintenance during the visit, your resort loses <strong>%d reputation</strong> (huge PR disaster!).';
$lang['building']['celebrity_visit_history_title']          = 'Visit History (last %d days)';
$lang['building']['celebrity_visit_no_history']             = 'No celebrity visits in this period. Keep your resort in top shape!';
$lang['building']['celebrity_visit_col_date']               = 'Date';
$lang['building']['celebrity_visit_col_type']               = 'Visitor';
$lang['building']['celebrity_visit_col_slopes']             = 'Slopes';
$lang['building']['celebrity_visit_col_lift']               = 'Lifts';
$lang['building']['celebrity_visit_col_rep']                = 'Reputation';
$lang['building']['celebrity_visit_type_influencer']        = 'Influencer';
$lang['building']['celebrity_visit_type_pro_skier']         = 'Pro Skier';
$lang['building']['celebrity_visit_type_film_crew']         = 'Film Crew';
$lang['building']['celebrity_visit_slopes_good']            = 'Good';
$lang['building']['celebrity_visit_slopes_avg']             = 'Average';
$lang['building']['celebrity_visit_lift_failed']            = 'Failed';
$lang['building']['celebrity_visit_lift_ok']                = 'OK';
// Accessibility & Transportation
// ============================================================
$lang['building']['transport_title']              = 'Accessibility & Transportation';
$lang['building']['transport_page_intro']         = 'Manage shuttle services, ski storage, and gondola links between resort sections. Better transport improves guest satisfaction, especially for families.';
$lang['building']['transport_how_it_works']       = 'How it works';
$lang['building']['transport_how_it_works_desc']  = 'Each night the game evaluates your transport infrastructure and awards reputation bonuses based on what you have built. Shuttles and gondolas attract both families and professional skiers, while ski storage facilities are especially valued by families.';
$lang['building']['transport_mechanic_shuttle']   = 'Shuttle level 1–3: buses, trams, or premium shuttles between resort sections reduce guest frustration and earn nightly reputation.';
$lang['building']['transport_mechanic_ski_storage'] = 'Ski storage: a dedicated storage room earns a nightly reputation bonus from families who appreciate not carrying equipment everywhere.';
$lang['building']['transport_mechanic_gondola']   = 'Gondola link: an inter-section gondola earns reputation from both families and expert skiers who value fast cross-resort access.';
$lang['building']['transport_mechanic_visitors']  = 'Good transport attracts more visitors: each shuttle level adds ' . (int)(TRANSPORT_VISITOR_BONUS_PER_LEVEL * 100) . '% to your daily visitor count.';
$lang['building']['transport_settings_title']     = 'Transport settings';
$lang['building']['transport_shuttle_label']      = 'Shuttle service level';
$lang['building']['transport_shuttle_help']       = 'Higher levels provide better transport between resort sections and increase visitor numbers.';
$lang['building']['transport_shuttle_level_0']    = 'No shuttle';
$lang['building']['transport_shuttle_level_1']    = 'Basic bus';
$lang['building']['transport_shuttle_level_2']    = 'Tram';
$lang['building']['transport_shuttle_level_3']    = 'Premium shuttle';
$lang['building']['transport_ski_storage_label']  = 'Ski storage facility';
$lang['building']['transport_ski_storage_help']   = 'Provide a dedicated area for guests to store skis and boots overnight.';
$lang['building']['transport_gondola_label']      = 'Gondola link between sections';
$lang['building']['transport_gondola_help']       = 'A gondola connecting different resort sections for quick cross-resort access.';
$lang['building']['transport_save_btn']           = 'Save settings';
$lang['building']['transport_key_figures']        = 'Current status';
$lang['building']['transport_shuttle_status']     = 'Shuttle level';
$lang['building']['transport_shuttle_daily_cost'] = 'Shuttle daily cost';
$lang['building']['transport_ski_storage_status'] = 'Ski storage';
$lang['building']['transport_gondola_status']     = 'Gondola link';
$lang['building']['transport_visitor_bonus_label'] = 'Visitor bonus (shuttles)';
$lang['building']['transport_nightly_rep_label']  = 'Nightly reputation bonus';
$lang['building']['transport_rep_families']       = 'rep (families)';
$lang['building']['transport_rep_pros']           = 'rep (pros)';
$lang['building']['transport_per_day']            = 'day';
$lang['building']['transport_on']                 = 'ON';
$lang['building']['transport_off']                = 'OFF';
$lang['building']['transport_settings_saved']     = '<div class="alert alert-success text-center">Transportation settings saved successfully.</div>';
$lang['building']['transport_invalid_settings']   = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['transport_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// Accommodation Upgrades
$lang['building']['accommodation_title']               = 'Accommodation Upgrades';
$lang['building']['accommodation_page_intro']          = 'Upgrade your resort\'s accommodation to attract more guests and boost reputation. Choose between cabins, lodges, or luxury hotels — each with increasing maintenance costs and benefits.';
$lang['building']['accommodation_how_it_works']        = 'How it works';
$lang['building']['accommodation_how_it_works_desc']   = 'Selecting an accommodation tier activates it and charges a nightly maintenance cost. In return your resort earns daily reputation points and attracts additional visitors.';
$lang['building']['accommodation_mechanic_cost']       = 'A nightly maintenance fee is deducted automatically each night while the accommodation is enabled.';
$lang['building']['accommodation_mechanic_rep']        = 'Enabled accommodation adds reputation points every night.';
$lang['building']['accommodation_mechanic_visitors']   = 'Higher-tier accommodation attracts a larger percentage of additional visitors each day.';
$lang['building']['accommodation_mechanic_upgrade']    = 'Upgrading to a new tier costs a one-time fee and immediately activates the new level.';
$lang['building']['accommodation_current_status']      = 'Current Status';
$lang['building']['accommodation_type_label']          = 'Accommodation type';
$lang['building']['accommodation_none']                = 'None';
$lang['building']['accommodation_type_cabin']          = 'Cabin';
$lang['building']['accommodation_type_lodge']          = 'Lodge';
$lang['building']['accommodation_type_luxury_hotel']   = 'Luxury Hotel';
$lang['building']['accommodation_status_label']        = 'Status';
$lang['building']['accommodation_on']                  = 'ACTIVE';
$lang['building']['accommodation_off']                 = 'INACTIVE';
$lang['building']['accommodation_nightly_cost_label']  = 'Nightly maintenance cost';
$lang['building']['accommodation_rep_bonus_label']     = 'Nightly reputation bonus';
$lang['building']['accommodation_visitor_bonus_label'] = 'Visitor bonus';
$lang['building']['accommodation_disable_btn']         = 'Disable accommodation';
$lang['building']['accommodation_enable_btn']          = 'Enable accommodation';
$lang['building']['accommodation_upgrade_title']       = 'Choose Accommodation Tier';
$lang['building']['accommodation_upgrade_desc']        = 'Select a tier below to upgrade. A one-time fee is charged when switching to a new tier.';
$lang['building']['accommodation_col_type']            = 'Type';
$lang['building']['accommodation_col_cost']            = 'Upgrade cost';
$lang['building']['accommodation_col_maintenance']     = 'Maintenance';
$lang['building']['accommodation_col_rep']             = 'Rep bonus / night';
$lang['building']['accommodation_col_visitors']        = 'Visitor bonus';
$lang['building']['accommodation_current_badge']       = 'Current';
$lang['building']['accommodation_per_night']           = 'night';
$lang['building']['accommodation_upgrade_btn']         = 'Upgrade';
// Result messages
$lang['building']['accommodation_upgraded']            = '<div class="alert alert-success text-center">Accommodation upgraded successfully!</div>';
$lang['building']['accommodation_enabled']             = '<div class="alert alert-success text-center">Accommodation enabled.</div>';
$lang['building']['accommodation_disabled']            = '<div class="alert alert-warning text-center">Accommodation disabled.</div>';
$lang['building']['accommodation_invalid_type']        = '<div class="alert alert-danger text-center">Invalid accommodation type selected.</div>';
$lang['building']['accommodation_not_enough_money']    = '<div class="alert alert-danger text-center">You do not have enough money to upgrade accommodation.</div>';
$lang['building']['accommodation_no_type_selected']    = '<div class="alert alert-danger text-center">No accommodation type selected yet. Choose a tier first.</div>';
$lang['building']['accommodation_save_error']          = '<div class="alert alert-danger text-center">An error occurred. Please try again.</div>';
// ============================================================
// Scenic Lifts
// ============================================================
$lang['building']['scenic_lift_title']              = 'Scenic Lifts';
$lang['building']['scenic_lift_page_intro']         = 'Offer a sightseeing gondola service to earn extra revenue from tourists and boost your resort\'s reputation every day.';
$lang['building']['scenic_lift_how_it_works']       = 'How it works';
$lang['building']['scenic_lift_how_it_works_desc']  = 'When the scenic lift service is enabled, a share of your daily visitors buys a sightseeing ticket and rides the gondola. Revenue is credited each night. While the service is running you also pay a daily operating cost and gain a small reputation bonus.';
$lang['building']['scenic_lift_mechanic_revenue']   = 'Daily revenue: ' . (int)(SCENIC_LIFT_TOURIST_RATIO * 100) . '% of visitors × ticket price.';
$lang['building']['scenic_lift_mechanic_cost']      = 'Daily operating cost: ' . SCENIC_LIFT_DAILY_COST . ' € regardless of visitor numbers.';
$lang['building']['scenic_lift_mechanic_reputation'] = 'Reputation bonus: +' . SCENIC_LIFT_REP_BONUS_PER_DAY . ' per day while the service is active.';
$lang['building']['scenic_lift_settings_title']     = 'Scenic lift settings';
$lang['building']['scenic_lift_enable_label']       = 'Enable scenic gondola service';
$lang['building']['scenic_lift_enable_help']        = 'When enabled, tourists buy sightseeing tickets every day.';
$lang['building']['scenic_lift_ticket_price_label'] = 'Sightseeing ticket price';
$lang['building']['scenic_lift_per_person']         = 'person/day';
$lang['building']['scenic_lift_save_btn']           = 'Save settings';
$lang['building']['scenic_lift_key_figures']        = 'Current settings';
$lang['building']['scenic_lift_status_label']       = 'Service status';
$lang['building']['scenic_lift_on']                 = 'ON';
$lang['building']['scenic_lift_off']                = 'OFF';
$lang['building']['scenic_lift_daily_revenue_label'] = 'Daily revenue (estimate)';
$lang['building']['scenic_lift_daily_revenue_desc']  = (int)(SCENIC_LIFT_TOURIST_RATIO * 100) . '% of visitors × ticket price × capacity';
$lang['building']['scenic_lift_daily_cost_label']   = 'Daily operating cost';
$lang['building']['scenic_lift_rep_bonus_label']    = 'Reputation bonus';
$lang['building']['scenic_lift_rep_bonus_desc']     = '+' . SCENIC_LIFT_REP_BONUS_PER_DAY . ' pt / day while active';
$lang['building']['scenic_lift_capacity_label']     = 'Gondola capacity level';
$lang['building']['scenic_lift_capacity_help']      = 'Controls how many gondola cars run (1 = minimal, 5 = maximum). Higher levels increase tourist throughput and daily operating cost by ' . SCENIC_LIFT_CAPACITY_COST_PER_LEVEL . ' € per level above the default (' . SCENIC_LIFT_DEFAULT_CAPACITY . ').';
$lang['building']['scenic_lift_mechanic_capacity']  = 'Gondola capacity (1–5): scales tourist throughput by level/' . SCENIC_LIFT_DEFAULT_CAPACITY . ' and adjusts the daily cost by ±' . SCENIC_LIFT_CAPACITY_COST_PER_LEVEL . ' € per step.';
$lang['building']['scenic_lift_discount_label']     = 'Seasonal off-peak discount';
$lang['building']['scenic_lift_discount_help']      = 'When enabled, a ' . (int)((1 - SCENIC_LIFT_DISCOUNT_PRICE_FACTOR) * 100) . '% price discount is applied during low-demand periods, attracting ' . (int)((SCENIC_LIFT_DISCOUNT_VISITOR_BOOST - 1) * 100) . '% more off-peak sightseers.';
$lang['building']['scenic_lift_mechanic_discount']  = 'Seasonal discount: during off-peak periods a ' . (int)((1 - SCENIC_LIFT_DISCOUNT_PRICE_FACTOR) * 100) . '% discount draws ' . (int)((SCENIC_LIFT_DISCOUNT_VISITOR_BOOST - 1) * 100) . '% more visitors (slight revenue trade-off for higher footfall).';
// Tour theme
$lang['building']['scenic_lift_tour_theme_label']       = 'Tour theme';
$lang['building']['scenic_lift_tour_theme_help']        = 'Choose a themed experience for the gondola. Each theme adds visitors or revenue at an extra daily cost, and grants additional reputation.';
$lang['building']['scenic_lift_theme_standard']         = 'Standard (no extra cost)';
$lang['building']['scenic_lift_theme_nature']           = 'Nature & Wildlife (+' . (int)((SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST - 1) * 100) . '% visitors, +' . SCENIC_LIFT_THEME_NATURE_REP_BONUS . ' rep/day, +' . SCENIC_LIFT_THEME_NATURE_EXTRA_COST . ' €/day)';
$lang['building']['scenic_lift_theme_sunset']           = 'Sunset & Starlight (+' . (int)((SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR - 1) * 100) . '% ticket price, +' . SCENIC_LIFT_THEME_SUNSET_REP_BONUS . ' rep/day, +' . SCENIC_LIFT_THEME_SUNSET_EXTRA_COST . ' €/day)';
$lang['building']['scenic_lift_theme_adventure']        = 'Adventure & Glacier (+' . (int)((SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST - 1) * 100) . '% visitors, +' . SCENIC_LIFT_THEME_ADVENTURE_REP_BONUS . ' rep/day, +' . SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST . ' €/day)';
$lang['building']['scenic_lift_tour_theme_current']     = 'Tour theme';
$lang['building']['scenic_lift_mechanic_theme']         = 'Tour theme: choose Standard, Nature (+' . (int)((SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST - 1) * 100) . '% visitors), Sunset (+' . (int)((SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR - 1) * 100) . '% price), or Adventure (+' . (int)((SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST - 1) * 100) . '% visitors) to trade daily cost for higher earnings and reputation.';
// Photography package
$lang['building']['scenic_lift_photo_label']            = 'Photography package';
$lang['building']['scenic_lift_photo_help']             = 'On-gondola photo service: each scenic visitor generates an extra ' . SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR . ' € in photo sales, at an additional daily operating cost of ' . SCENIC_LIFT_PHOTO_DAILY_COST . ' €.';
$lang['building']['scenic_lift_mechanic_photo']         = 'Photography package: adds ' . SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR . ' € per visitor in photo revenue (+' . SCENIC_LIFT_PHOTO_DAILY_COST . ' €/day operating cost).';
$lang['building']['scenic_lift_photo_current']          = 'Photography package';
// VIP gondola
$lang['building']['scenic_lift_vip_label']              = 'VIP gondola mode';
$lang['building']['scenic_lift_vip_help']               = 'Restrict rides to VIP guests only: visitor count drops to ' . (int)(SCENIC_LIFT_VIP_VISITOR_FACTOR * 100) . '% of normal, but each ticket commands ' . SCENIC_LIFT_VIP_PRICE_MULTIPLIER . '× the regular price. Grants +' . SCENIC_LIFT_VIP_REP_BONUS . ' extra reputation per day (+' . SCENIC_LIFT_VIP_DAILY_COST . ' €/day cost).';
$lang['building']['scenic_lift_mechanic_vip']           = 'VIP gondola: ' . (int)(SCENIC_LIFT_VIP_VISITOR_FACTOR * 100) . '% of visitors at ' . SCENIC_LIFT_VIP_PRICE_MULTIPLIER . '× ticket price, +' . SCENIC_LIFT_VIP_REP_BONUS . ' rep/day, +' . SCENIC_LIFT_VIP_DAILY_COST . ' €/day cost.';
$lang['building']['scenic_lift_vip_current']            = 'VIP gondola mode';
$lang['building']['scenic_lift_settings_saved']     = '<div class="alert alert-success text-center">Scenic lift settings saved successfully.</div>';
$lang['building']['scenic_lift_invalid_settings']   = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['scenic_lift_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// Emergency & Rescue System
// ============================================================
$lang['building']['emergency_title']              = '🚑 Emergency &amp; Rescue System';
$lang['building']['emergency_page_intro']         = 'Manage avalanche rescue teams and medical stations to protect your guests. Response time affects safety, reputation and your financial exposure when incidents occur.';
$lang['building']['emergency_how_it_works']       = 'How it works';
$lang['building']['emergency_how_it_works_desc']  = 'Each night the game simulates emergency readiness. A faster response time boosts reputation; a slow one costs reputation. There is a ' . EMERGENCY_INCIDENT_CHANCE_PCT . '% nightly chance of an incident — without insurance you face a large fine.';
$lang['building']['emergency_mechanic_rescue']    = 'Avalanche rescue team reduces response time by up to ' . EMERGENCY_RESCUE_RESPONSE_REDUCTION[3] . ' min (advanced level).';
$lang['building']['emergency_mechanic_medical']   = 'Medical stations reduce response time by up to ' . EMERGENCY_MEDICAL_RESPONSE_REDUCTION[3] . ' min (advanced level).';
$lang['building']['emergency_mechanic_insurance'] = 'Insurance costs ' . EMERGENCY_INSURANCE_DAILY_COST . ' €/night but caps incident fines at ' . EMERGENCY_FINE_WITH_INSURANCE . ' € instead of ' . EMERGENCY_FINE_NO_INSURANCE . ' €.';
$lang['building']['emergency_mechanic_reputation'] = 'Response &lt; ' . EMERGENCY_RESPONSE_FAST_THRESHOLD . ' min: +' . EMERGENCY_REP_FAST_RESPONSE_BONUS . ' rep/night. Response &gt; ' . EMERGENCY_RESPONSE_POOR_THRESHOLD . ' min: ' . EMERGENCY_REP_POOR_RESPONSE_PENALTY . ' rep/night.';
$lang['building']['emergency_settings_title']     = 'Emergency settings';
$lang['building']['emergency_rescue_label']       = 'Avalanche rescue team';
$lang['building']['emergency_rescue_help']        = 'Higher levels reduce response time and daily operating cost scales accordingly.';
$lang['building']['emergency_medical_label']      = 'Medical stations';
$lang['building']['emergency_medical_help']       = 'On-slope medical stations further reduce response time.';
$lang['building']['emergency_insurance_label']    = 'Enable risk insurance';
$lang['building']['emergency_insurance_help']     = 'Insurance limits the financial damage of incidents but costs a daily premium.';
$lang['building']['emergency_save_btn']           = 'Save settings';
$lang['building']['emergency_status_title']       = 'Current status';
$lang['building']['emergency_level_0']            = 'None';
$lang['building']['emergency_level_1']            = 'Basic';
$lang['building']['emergency_level_2']            = 'Standard';
$lang['building']['emergency_level_3']            = 'Advanced';
$lang['building']['emergency_per_night']          = 'night';
$lang['building']['emergency_response_time_label'] = 'Response time';
$lang['building']['emergency_response_fast']      = 'Fast';
$lang['building']['emergency_response_average']   = 'Average';
$lang['building']['emergency_response_poor']      = 'Poor';
$lang['building']['emergency_insurance_status_label'] = 'Insurance';
$lang['building']['emergency_insurance_on']       = 'Active';
$lang['building']['emergency_insurance_off']      = 'Not covered';
$lang['building']['emergency_daily_cost_label']   = 'Total daily cost';
$lang['building']['emergency_incident_chance_label'] = 'Incident probability';
$lang['building']['emergency_fine_label']         = 'Fine per incident';
$lang['building']['emergency_fine_insured_note']  = 'covered by insurance';
$lang['building']['emergency_rep_effect_label']   = 'Nightly reputation effect';
$lang['building']['emergency_rep_per_night']      = 'pts/night';
$lang['building']['emergency_settings_saved']     = '<div class="alert alert-success text-center">Emergency settings saved successfully.</div>';
$lang['building']['emergency_invalid_settings']   = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['emergency_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';

// Season Ski Passes
// ============================================================
$lang['building']['season_pass_title']             = 'Season Ski Passes';
$lang['building']['season_pass_page_intro']        = 'Offer season ski passes to your guests. Season pass holders pay upfront for unlimited access throughout the ski season, providing you with predictable daily revenue and a loyal customer base.';
$lang['building']['season_pass_how_it_works']      = 'How it works';
$lang['building']['season_pass_how_it_works_desc'] = 'At the start of each season a number of passes are sold based on your resort\'s reputation and the price you set. Revenue is spread evenly across the season as daily income.';
$lang['building']['season_pass_mechanic_sales']    = 'Pass sales depend on your reputation and the price: higher reputation = more buyers; lower price = more buyers.';
$lang['building']['season_pass_mechanic_revenue']  = 'Daily revenue = passes sold × price ÷ ' . SEASON_PASS_SEASON_LENGTH . ' days.';
$lang['building']['season_pass_mechanic_loyalty']  = 'Loyalty bonus: selling at least ' . SEASON_PASS_HIGH_SALES_THRESHOLD . ' passes earns +' . SEASON_PASS_LOYALTY_REP_BONUS . ' reputation every night.';
$lang['building']['season_pass_mechanic_renewal']  = 'Passes are automatically re-sold at the start of every new season.';
$lang['building']['season_pass_mechanic_early_bird'] = 'Early-bird discount: when enabled, the advertised discount encourages early purchases and boosts total pass sales by +' . (int)(SEASON_PASS_EARLY_BIRD_SALES_BOOST * 100) . '%.';
$lang['building']['season_pass_settings_title']    = 'Season pass settings';
$lang['building']['season_pass_enable_label']      = 'Enable season ski passes';
$lang['building']['season_pass_enable_help']       = 'When enabled, season passes go on sale each season and generate daily revenue.';
$lang['building']['season_pass_price_label']       = 'Season pass price';
$lang['building']['season_pass_price_help']        = 'Price per season pass (€). Higher prices mean more revenue per pass but fewer passes sold.';
$lang['building']['season_pass_save_btn']          = 'Save settings';
$lang['building']['season_pass_key_figures']       = 'Current overview';
$lang['building']['season_pass_status_label']      = 'Season passes';
$lang['building']['season_pass_on']                = 'ON';
$lang['building']['season_pass_off']               = 'OFF';
$lang['building']['season_pass_estimated_sales']   = 'Estimated passes (next season)';
$lang['building']['season_pass_passes_sold_label'] = 'Passes sold (current season)';
$lang['building']['season_pass_passes_unit']       = 'passes';
$lang['building']['season_pass_daily_revenue_label'] = 'Estimated daily revenue';
$lang['building']['season_pass_per_day']           = '/ day';
$lang['building']['season_pass_loyalty_label']     = 'Loyalty bonus';
$lang['building']['season_pass_loyalty_desc']      = '+' . SEASON_PASS_LOYALTY_REP_BONUS . ' reputation/night when ' . SEASON_PASS_HIGH_SALES_THRESHOLD . '+ passes are sold';
$lang['building']['season_pass_early_bird_label']            = 'Early-bird discount';
$lang['building']['season_pass_early_bird_help']             = 'When enabled, an early-bird discount is advertised to attract buyers before the season starts. This boosts pass sales by +%d%%.';
$lang['building']['season_pass_early_bird_discount_label']   = 'Early-bird discount rate';
$lang['building']['season_pass_early_bird_discount_label_short'] = 'discount';
$lang['building']['season_pass_early_bird_discount_help']    = 'The discount percentage offered to early buyers. A higher discount attracts more buyers but reduces revenue per pass.';
$lang['building']['season_pass_settings_saved']    = '<div class="alert alert-success text-center">Season pass settings saved successfully.</div>';
$lang['building']['season_pass_invalid_settings']  = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['season_pass_save_error']        = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// Retail & Amenities
$lang['building']['retail_title']              = 'Retail & Amenities';
$lang['building']['retail_page_intro']         = 'Manage your slope-side shops, cafes, and bars. Set stock levels and pricing strategies to maximise revenue, and enable seasonal items for a winter boost.';
$lang['building']['retail_how_it_works']       = 'How it works';
$lang['building']['retail_how_it_works_desc']  = 'Each open shop generates daily revenue based on its stock level, pricing strategy, and current popularity. Popularity drifts nightly — high stock and affordable prices keep guests happy, while low stock or premium prices erode it over time.';
$lang['building']['retail_mechanic_stock']     = 'Stock level (1–5): higher stock keeps shelves full, boosting popularity and revenue potential.';
$lang['building']['retail_mechanic_pricing']   = 'Pricing strategy: Budget (+popularity, ×0.7 revenue), Standard (neutral, ×1.0), Premium (−popularity, ×1.4 revenue).';
$lang['building']['retail_mechanic_popularity']= 'Popularity (0–100) drifts each night depending on stock and pricing. Revenue is scaled by popularity ÷ 50.';
$lang['building']['retail_mechanic_seasonal']  = 'Seasonal items (warm clothing, souvenirs, gear) give a +' . (int)(RETAIL_SEASONAL_BONUS * 100 - 100) . '% revenue bonus when the resort is open.';
// Shop type names
$lang['building']['retail_shop_ski_shop']          = '🎿 Ski Shop';
$lang['building']['retail_shop_souvenir_shop']     = '🧸 Souvenir Shop';
$lang['building']['retail_shop_cafe']              = '☕ Café';
$lang['building']['retail_shop_bar']               = '🍺 Bar';
// Shop type descriptions
$lang['building']['retail_shop_ski_shop_desc']          = 'Sells gear, warm clothing, and ski equipment. Popular during the ski season.';
$lang['building']['retail_shop_souvenir_shop_desc']     = 'Sells resort souvenirs and keepsakes. Attracts visitors looking for mementos.';
$lang['building']['retail_shop_cafe_desc']              = 'Slope-side café serving hot drinks and snacks. A favourite for skiers on a break.';
$lang['building']['retail_shop_bar_desc']               = 'Après-ski bar for relaxing after a day on the slopes.';
// Form labels
$lang['building']['retail_enable_label']       = 'Open this shop';
$lang['building']['retail_stock_label']        = 'Stock level';
$lang['building']['retail_stock_help']         = 'Controls how well-stocked the shop is. Higher stock improves popularity and revenue but costs more to maintain.';
$lang['building']['retail_pricing_label']      = 'Pricing strategy';
$lang['building']['retail_pricing_budget']     = 'Budget (affordable)';
$lang['building']['retail_pricing_standard']   = 'Standard';
$lang['building']['retail_pricing_premium']    = 'Premium (high-end)';
$lang['building']['retail_pricing_help']       = 'Budget: more popular, lower margin. Premium: higher margin, popularity penalty.';
$lang['building']['retail_seasonal_label']     = 'Stock seasonal items';
$lang['building']['retail_seasonal_help']      = 'Warm clothing, resort souvenirs, and seasonal gear. Adds a +' . (int)(RETAIL_SEASONAL_BONUS * 100 - 100) . '% revenue bonus when the ski resort is open.';
$lang['building']['retail_popularity_label']   = 'Popularity';
$lang['building']['retail_open']               = 'Open';
$lang['building']['retail_closed']             = 'Closed';
$lang['building']['retail_save_btn']           = 'Save settings';
// Revenue guide
$lang['building']['retail_revenue_guide_title'] = 'Revenue Reference';
$lang['building']['retail_revenue_guide_desc']  = 'Base daily revenue at stock level 3, standard pricing, popularity 50, and resort open:';
$lang['building']['retail_col_shop']            = 'Shop';
$lang['building']['retail_col_base_rev']        = 'Base revenue / day';
$lang['building']['retail_col_seasonal_bonus']  = 'With seasonal items';
$lang['building']['retail_revenue_guide_note']  = 'Actual revenue varies with stock level, popularity, and pricing strategy. Values shown assume stock 3, standard pricing, popularity 50.';
// Action result messages
$lang['building']['retail_settings_saved']     = '<div class="alert alert-success text-center">Retail settings saved successfully.</div>';
$lang['building']['retail_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';
// Maintenance Depth
$lang['building']['maint_depth_title']               = 'Maintenance Depth';
$lang['building']['maint_depth_intro']               = 'Choose a preventive maintenance plan for your lifts. Each night, lifts may suffer random mechanical failures based on their type, age, and daily usage. A better maintenance plan lowers failure chances and reduces repair bills. Your lift mechanics\' skill level provides an additional repair discount.';
$lang['building']['maint_depth_how_it_works']        = 'How it works';
$lang['building']['maint_depth_how_it_works_desc']   = 'Every night, each open lift is assessed for a potential random mechanical failure.';
$lang['building']['maint_depth_mechanic_type']       = 'Lift type: gondolas and cable cars have higher failure rates than simple surface lifts.';
$lang['building']['maint_depth_mechanic_age']        = 'Age: the older the lift, the higher its daily failure probability.';
$lang['building']['maint_depth_mechanic_usage']      = 'Usage: lifts carrying visitors beyond 50 % of their capacity gain an extra failure chance.';
$lang['building']['maint_depth_mechanic_staff']      = 'Staff skill: lift mechanics with high efficiency reduce repair costs by up to ' . (int)(MAINT_STAFF_MAX_REPAIR_DISCOUNT * 100) . ' %.';
$lang['building']['maint_depth_plans_title']         = 'Select a maintenance plan';
$lang['building']['maint_depth_plan_basic']          = 'Basic';
$lang['building']['maint_depth_plan_basic_desc']     = 'Reactive only. No daily cost. Failures are repaired at full price.';
$lang['building']['maint_depth_plan_standard']       = 'Standard';
$lang['building']['maint_depth_plan_standard_desc']  = 'Regular inspections. Daily fee per lift. Reduces repair costs.';
$lang['building']['maint_depth_plan_preventive']     = 'Preventive';
$lang['building']['maint_depth_plan_preventive_desc'] = 'Full preventive programme. Higher daily fee. Cuts failure chance in half and reduces repair costs.';
$lang['building']['maint_depth_cost']                = 'Daily cost';
$lang['building']['maint_depth_free']                = 'Free';
$lang['building']['maint_depth_none']                = 'None';
$lang['building']['maint_depth_failure_reduction']   = 'Failure reduction';
$lang['building']['maint_depth_repair_discount']     = 'Repair discount';
$lang['building']['maint_depth_per_lift_day']        = 'lift / day';
$lang['building']['maint_depth_save_btn']            = 'Save plan';
$lang['building']['maint_depth_key_figures']         = 'Current status';
$lang['building']['maint_depth_current_plan']        = 'Active plan';
$lang['building']['maint_depth_open_lifts']          = 'Open lifts';
$lang['building']['maint_depth_daily_plan_cost']     = 'Daily plan cost';
$lang['building']['maint_depth_avg_mechanic_eff']    = 'Avg. mechanic efficiency';
$lang['building']['maint_depth_staff_discount']      = 'repair discount from staff';
$lang['building']['maint_depth_base_failure']        = 'Base failure chance';
// Feedback messages
$lang['building']['maint_depth_saved']               = '<div class="alert alert-success text-center">Maintenance plan saved successfully.</div>';
$lang['building']['maint_depth_invalid_plan']        = '<div class="alert alert-danger text-center">Invalid maintenance plan selected.</div>';
$lang['building']['maint_depth_save_error']          = '<div class="alert alert-danger text-center">An error occurred while saving the maintenance plan.</div>';
// Logs
$lang['building']['maint_depth_failure_log']         = ' suffered a mechanical failure and has been placed in maintenance.';
$lang['building']['maint_depth_plan_cost_log']       = 'Preventive maintenance plan cost charged: ';

// ============================================================
// Sponsorship & Branding
// ============================================================
$lang['building']['sponsorship_title']             = '🏅 Sponsorship & Branding';
$lang['building']['sponsorship_page_intro']        = 'Sign contracts with equipment, apparel, and event sponsors to earn daily revenue and unlock resort bonuses. Keep your reputation high to maintain sponsor satisfaction.';
$lang['building']['sponsorship_how_it_works']      = 'How it works';
$lang['building']['sponsorship_how_it_works_desc'] = 'Each sponsor contract generates a daily revenue payment and may unlock a gameplay bonus. Nightly, the game checks whether your resort reputation meets the sponsor\'s minimum threshold and adjusts their brand satisfaction accordingly. If satisfaction reaches 0, the sponsor cancels the contract.';
$lang['building']['sponsorship_mechanic_revenue']  = 'Each active contract pays a fixed daily revenue based on its contract level.';
$lang['building']['sponsorship_mechanic_bonus']    = 'Certain sponsors provide bonuses: cheaper lift maintenance, extra visitors, or daily reputation gains.';
$lang['building']['sponsorship_mechanic_satisfaction'] = 'Brand satisfaction (0–100) rises by ' . SPONSORSHIP_SATISFACTION_GAIN . ' pts/day when your reputation meets the requirement, and falls by ' . SPONSORSHIP_SATISFACTION_LOSS . ' pts/day when it does not.';
$lang['building']['sponsorship_mechanic_cancel']   = 'A sponsor walks away (and costs you ' . SPONSORSHIP_CANCEL_REP_PENALTY . ' reputation) if their satisfaction reaches 0.';

// Sponsor type names
$lang['building']['sponsorship_type_lift_equipment'] = '⚙️ Equipment Sponsor';
$lang['building']['sponsorship_type_apparel']        = '🎿 Apparel Sponsor';
$lang['building']['sponsorship_type_energy_drink']   = '⚡ Energy Drink Sponsor';
$lang['building']['sponsorship_type_resort_map']     = '🗺️ Resort Map Advertising';
$lang['building']['sponsorship_type_event_title']    = '🏆 Event Title Sponsor';

// Sponsor type descriptions
$lang['building']['sponsorship_desc_lift_equipment'] = 'An equipment brand co-sponsors lift maintenance, reducing your daily upkeep costs.';
$lang['building']['sponsorship_desc_apparel']        = 'A ski apparel brand promotes your resort, attracting more visitors each day.';
$lang['building']['sponsorship_desc_energy_drink']   = 'A popular energy drink brand places ads throughout the resort, generating flat daily revenue.';
$lang['building']['sponsorship_desc_resort_map']     = 'Resort map advertising: brands pay to feature on your printed and digital trail maps.';
$lang['building']['sponsorship_desc_event_title']    = 'Become a named event venue partner, earning daily revenue and a small daily reputation boost.';

// Bonus labels
$lang['building']['sponsorship_bonus_maintenance']  = 'lift maintenance saving';
$lang['building']['sponsorship_bonus_visitors']     = 'visitor boost';
$lang['building']['sponsorship_bonus_reputation']   = 'reputation/day';
$lang['building']['sponsorship_bonus_revenue_only'] = 'Daily revenue only';

// Contract levels
$lang['building']['sponsorship_level_1'] = 'Basic';
$lang['building']['sponsorship_level_2'] = 'Standard';
$lang['building']['sponsorship_level_3'] = 'Premium';

// Table column headers
$lang['building']['sponsorship_col_sponsor']      = 'Sponsor';
$lang['building']['sponsorship_col_level']        = 'Level';
$lang['building']['sponsorship_col_revenue']      = 'Daily revenue';
$lang['building']['sponsorship_col_bonus']        = 'Bonus';
$lang['building']['sponsorship_col_satisfaction'] = 'Brand satisfaction';
$lang['building']['sponsorship_col_action']       = 'Action';
$lang['building']['sponsorship_col_min_rep']      = 'Min. reputation';
$lang['building']['sponsorship_col_sign_cost']    = 'Signing fee';
$lang['building']['sponsorship_per_day']          = 'day';

// Sections
$lang['building']['sponsorship_active_title']    = 'Active Contracts';
$lang['building']['sponsorship_available_title'] = 'Available Sponsors';
$lang['building']['sponsorship_available_desc']  = 'Pay a one-off signing fee to activate a contract. You can hold one contract per sponsor type at a time.';

// Buttons
$lang['building']['sponsorship_sign_btn']           = 'Sign contract';
$lang['building']['sponsorship_terminate_btn']      = 'Terminate';
$lang['building']['sponsorship_select_level']       = 'Select level';
$lang['building']['sponsorship_terminate_confirm']  = 'Are you sure you want to terminate this contract?';
$lang['building']['sponsorship_already_active']     = '✓ Contract active';

// Flash messages
$lang['building']['sponsorship_signed']              = '<div class="alert alert-success text-center">Sponsor contract signed successfully!</div>';
$lang['building']['sponsorship_terminated']          = '<div class="alert alert-warning text-center">Sponsor contract terminated.</div>';
$lang['building']['sponsorship_invalid_type']        = '<div class="alert alert-danger text-center">Invalid sponsor type.</div>';
$lang['building']['sponsorship_invalid_level']       = '<div class="alert alert-danger text-center">Invalid contract level.</div>';
$lang['building']['sponsorship_rep_too_low']         = '<div class="alert alert-danger text-center">Your resort reputation is too low for this contract level.</div>';
$lang['building']['sponsorship_insufficient_funds']  = '<div class="alert alert-danger text-center">Insufficient funds to pay the signing fee.</div>';
$lang['building']['sponsorship_error']               = '<div class="alert alert-danger text-center">An error occurred. Please try again.</div>';

// Log messages
$lang['building']['sponsorship_signed_log']          = 'Signed sponsor contract:';
$lang['building']['sponsorship_revenue_log']         = 'Sponsorship revenue received:';
$lang['building']['sponsorship_cancelled_log']       = 'Sponsor cancelled contract (brand satisfaction reached 0):';
$lang['building']['sponsorship_rep_bonus_log']       = 'Reputation bonus from event title sponsor:';


// Mountain Cams (Webcams)
$lang['building']['mountain_cam_title']              = 'Mountain Webcams';
$lang['building']['mountain_cam_page_intro']         = 'Install live mountain webcams to let potential visitors check snow conditions online, boosting visitor numbers and your resort\'s reputation every day.';
$lang['building']['mountain_cam_how_it_works']       = 'How it works';
$lang['building']['mountain_cam_how_it_works_desc']  = 'When mountain cams are active, the live feeds attract skiers browsing conditions online and convert them into visitors. More cameras and higher quality further increase demand. A daily operating cost covers bandwidth and maintenance.';
$lang['building']['mountain_cam_mechanic_visitors']  = 'Daily visitor boost: +' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . '% per camera (quality multiplied).';
$lang['building']['mountain_cam_mechanic_cost']      = 'Daily cost: ' . MOUNTAIN_CAM_DAILY_COST_BASE . ' € for the first camera, +' . MOUNTAIN_CAM_DAILY_COST_PER_CAM . ' € per additional camera (quality multiplied).';
$lang['building']['mountain_cam_mechanic_reputation'] = 'Reputation bonus: +' . MOUNTAIN_CAM_REP_BONUS_PER_DAY . ' per day while cams are active.';
$lang['building']['mountain_cam_mechanic_quality']   = 'Quality level: higher quality increases both visitor demand and daily cost.';
$lang['building']['mountain_cam_mechanic_stream']    = 'Live stream mode: activating live-stream increases visitor demand by +' . (int)(( MOUNTAIN_CAM_STREAM_VISITOR_MULT - 1) * 100) . '% but raises daily cost by ' . (int)(( MOUNTAIN_CAM_STREAM_COST_MULT - 1) * 100) . '%.';
$lang['building']['mountain_cam_mechanic_social']    = 'Social media sharing: automatically share snapshots online for +' . MOUNTAIN_CAM_SOCIAL_REP_BONUS . ' reputation/day at an extra +' . MOUNTAIN_CAM_SOCIAL_COST_PER_DAY . ' €/day.';
$lang['building']['mountain_cam_mechanic_night_vision']     = 'Night vision mode: equip cameras with infrared night-vision to boost visitor demand by +' . (int)((MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT - 1) * 100) . '% at an extra +' . MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY . ' €/day.';
$lang['building']['mountain_cam_mechanic_weather_overlay']  = 'Weather overlay: display live weather data on feeds for +' . MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS . ' reputation/day at an extra +' . MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY . ' €/day.';
$lang['building']['mountain_cam_settings_title']     = 'Webcam settings';
$lang['building']['mountain_cam_enable_label']       = 'Enable mountain webcams';
$lang['building']['mountain_cam_enable_help']        = 'When enabled, live feeds attract additional visitors each day.';
$lang['building']['mountain_cam_num_cams_label']     = 'Number of cameras';
$lang['building']['mountain_cam_num_cams_help']      = 'Each additional camera adds +' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . '% visitor demand and +' . MOUNTAIN_CAM_DAILY_COST_PER_CAM . ' € daily cost.';
$lang['building']['mountain_cam_quality_label']      = 'Camera quality';
$lang['building']['mountain_cam_quality_1']          = 'Standard';
$lang['building']['mountain_cam_quality_2']          = 'HD';
$lang['building']['mountain_cam_quality_3']          = '4K';
$lang['building']['mountain_cam_quality_help']       = 'Higher quality increases visitor boost and daily cost.';
$lang['building']['mountain_cam_save_btn']           = 'Save settings';
$lang['building']['mountain_cam_key_figures']        = 'Current settings';
$lang['building']['mountain_cam_status_label']       = 'Webcam status';
$lang['building']['mountain_cam_on']                 = 'ON';
$lang['building']['mountain_cam_off']                = 'OFF';
$lang['building']['mountain_cam_visitor_boost_label'] = 'Daily visitor boost';
$lang['building']['mountain_cam_visitor_boost_desc'] = '+' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . '% per camera × quality multiplier';
$lang['building']['mountain_cam_daily_cost_label']   = 'Daily operating cost';
$lang['building']['mountain_cam_rep_bonus_label']    = 'Reputation bonus';
$lang['building']['mountain_cam_rep_bonus_per_day']  = 'pt / day while active';
$lang['building']['mountain_cam_stream_label']       = 'Live stream mode';
$lang['building']['mountain_cam_stream_help']        = 'Live-stream mode boosts visitor demand by +' . (int)(( MOUNTAIN_CAM_STREAM_VISITOR_MULT - 1) * 100) . '% but increases daily cost by ' . (int)(( MOUNTAIN_CAM_STREAM_COST_MULT - 1) * 100) . '%.';
$lang['building']['mountain_cam_stream_visitor_note'] = 'visitor boost';
$lang['building']['mountain_cam_social_label']       = 'Social media sharing';
$lang['building']['mountain_cam_social_help']        = 'Automatically share webcam snapshots on social media for +' . MOUNTAIN_CAM_SOCIAL_REP_BONUS . ' reputation/day (+' . MOUNTAIN_CAM_SOCIAL_COST_PER_DAY . ' €/day extra).';
$lang['building']['mountain_cam_night_vision_label'] = 'Night vision mode';
$lang['building']['mountain_cam_night_vision_help']  = 'Equip cameras with infrared night-vision to broadcast evening and night-skiing conditions, boosting visitor demand by +' . (int)((MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT - 1) * 100) . '% (+' . MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY . ' €/day extra).';
$lang['building']['mountain_cam_weather_overlay_label'] = 'Weather overlay';
$lang['building']['mountain_cam_weather_overlay_help']  = 'Display live temperature, snowfall and wind data directly on the webcam feeds to build visitor confidence for +' . MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS . ' reputation/day (+' . MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY . ' €/day extra).';
$lang['building']['mountain_cam_settings_saved']     = '<div class="alert alert-success text-center">Mountain webcam settings saved successfully.</div>';
$lang['building']['mountain_cam_invalid_settings']   = '<div class="alert alert-danger text-center">Invalid settings. Please check the values and try again.</div>';
$lang['building']['mountain_cam_save_error']         = '<div class="alert alert-danger text-center">An error occurred while saving. Please try again.</div>';

// Ski Resort Quiz
$lang['building']['sqz_title']               = 'Ski Resort Quiz';
$lang['building']['sqz_intro']               = 'Test your skiing and ski resort knowledge with this multiple-choice trivia quiz. Answer 10 questions and see how you score!';
$lang['building']['sqz_secret_title']        = 'Enter Secret Code';
$lang['building']['sqz_secret_desc']         = 'This area is protected. Enter the secret code to access the Ski Resort Quiz.';
$lang['building']['sqz_secret_label']        = 'Secret code';
$lang['building']['sqz_secret_placeholder']  = 'Enter code…';
$lang['building']['sqz_secret_submit']       = 'Unlock';
$lang['building']['sqz_secret_error']        = '<div class="alert alert-danger">Incorrect secret code. Please try again.</div>';
$lang['building']['sqz_instructions']        = 'Answer 10 multiple-choice questions about skiing and ski resort management. Select the correct answer for each question. Good luck!';
$lang['building']['sqz_btn_start']           = 'Start Quiz';
$lang['building']['sqz_btn_next']            = 'Next Question';
$lang['building']['sqz_btn_restart']         = 'Play Again';
$lang['building']['sqz_question']            = 'Question';
$lang['building']['sqz_score']               = 'Score';
$lang['building']['sqz_feedback_correct']    = '✅ Correct! Well done.';
$lang['building']['sqz_feedback_incorrect']  = '❌ Incorrect. The correct answer is highlighted in green.';
$lang['building']['sqz_result_title']        = 'Quiz Complete!';
$lang['building']['sqz_result_score_label']  = 'Your score:';
$lang['building']['sqz_result_gold']         = 'Excellent! You\'re a true ski resort expert! 🎿';
$lang['building']['sqz_result_silver']       = 'Good effort! You know your way around the mountain.';
$lang['building']['sqz_result_bronze']       = 'Not bad! A bit more practice on the slopes and you\'ll do better.';
$lang['building']['sqz_result_try_again']    = 'Time to hit the beginner slopes again! Keep practising.';


// ── Government & Regulations ────────────────────────────────────────────────
$lang['building']['gov_title']                   = 'Government &amp; Regulations';
$lang['building']['gov_page_intro']              = 'Balance profit against compliance. Environmental protection can limit your expansion, safety inspections carry fines or rewards, and the government adjusts its regulation tax rate every season. Stay compliant and earn eco-subsidies.';
$lang['building']['gov_compliance_title']        = 'Compliance Score';
$lang['building']['gov_compliance_desc']         = 'Your overall regulatory compliance. Keep it high to avoid expansion blocks and benefit from lower audit risk.';
$lang['building']['gov_expansion_blocked_warning'] = '⚠ Expansion blocked by government due to low compliance.';
$lang['building']['gov_tax_rate_title']          = 'Regulation Tax Rate';
$lang['building']['gov_tax_rate_desc']           = 'Applied nightly to yesterday\'s gross revenue. Rate is reset each new season.';
$lang['building']['gov_tax_rate_range']          = 'Possible range';
$lang['building']['gov_tax_rate_season']         = 'Rate set in season';
$lang['building']['gov_audit_title']             = 'Safety Inspection Audit';
$lang['building']['gov_audit_pass']              = '✅ Passed';
$lang['building']['gov_audit_fail']              = '❌ Failed';
$lang['building']['gov_audit_none']              = 'No audit yet';
$lang['building']['gov_audit_last_date']         = 'Last audit';
$lang['building']['gov_audit_desc']              = 'Random daily chance of a safety inspection. Pass to earn a cash reward; fail and pay a fine.';
$lang['building']['gov_subsidy_title']           = 'Eco-Friendly Subsidy';
$lang['building']['gov_subsidy_desc']            = 'The government awards a subsidy each new season when your eco reputation meets the threshold.';
$lang['building']['gov_subsidy_eco_threshold']   = 'Eco reputation required';
$lang['building']['gov_subsidy_your_eco']        = 'Your eco reputation';
$lang['building']['gov_subsidy_available_label'] = 'Subsidy ready to claim';
$lang['building']['gov_subsidy_claim_btn']       = 'Claim Subsidy';
$lang['building']['gov_subsidy_none']            = 'No subsidy available';
$lang['building']['gov_subsidy_how_to_earn']     = 'Improve your eco reputation above the threshold to earn a subsidy at the start of the next season.';
$lang['building']['gov_subsidy_claimed']         = '<div class="alert alert-success">Subsidy successfully claimed!</div>';
$lang['building']['gov_no_subsidy_available']    = '<div class="alert alert-warning">No subsidy is currently available.</div>';
$lang['building']['gov_subsidy_claimed_log']     = 'Eco-friendly subsidy claimed:';
$lang['building']['gov_how_it_works_title']      = 'How it works';
$lang['building']['gov_mechanic_compliance']     = 'Your <strong>compliance score</strong> rises or falls each night based on your eco reputation and expansion status.';
$lang['building']['gov_mechanic_expansion']      = '<strong>Environmental protection</strong>: if compliance falls below ' . GOV_COMPLIANCE_BLOCK_THRESHOLD . ', the government blocks resort expansion.';
$lang['building']['gov_mechanic_audit']          = '<strong>Safety inspections</strong>: there is a ' . GOV_AUDIT_CHANCE . '% daily chance of a surprise audit. Compliance ≥ ' . GOV_AUDIT_PASS_THRESHOLD . ' means a pass (+' . GOV_AUDIT_PASS_REWARD . ' €); below that you fail (-' . GOV_AUDIT_FAIL_FINE . ' €).';
$lang['building']['gov_mechanic_subsidy']        = '<strong>Eco subsidies</strong>: when a new season starts and your eco reputation ≥ ' . GOV_SUBSIDY_ECO_THRESHOLD . ', you receive a ' . number_format(GOV_SUBSIDY_AMOUNT, 0, '.', ' ') . ' € subsidy to claim.';
$lang['building']['gov_mechanic_tax']            = '<strong>Regulation tax</strong>: an additional ' . GOV_TAX_RATE_MIN . '–' . GOV_TAX_RATE_MAX . '% tax on yesterday\'s gross revenue is applied each night. The rate is randomly reassigned each new season.';
$lang['building']['gov_table_event']             = 'Event';
$lang['building']['gov_table_effect']            = 'Effect';
$lang['building']['gov_row_eco_high']            = 'Eco reputation ≥ 70';
$lang['building']['gov_row_eco_low']             = 'Eco reputation &lt; 30';
$lang['building']['gov_row_expansion_restricted'] = 'Carbon expansion restriction';
$lang['building']['gov_row_audit_pass']          = 'Audit passed';
$lang['building']['gov_row_audit_fail']          = 'Audit failed';
$lang['building']['gov_row_tax_rate']            = 'Regulation tax rate';
$lang['building']['gov_row_tax_rate_note']       = 'of yesterday\'s gross revenue (resets each season)';
$lang['building']['gov_row_subsidy']             = 'Eco subsidy (new season)';
$lang['building']['gov_row_subsidy_note']        = 'when eco reputation ≥ ' . GOV_SUBSIDY_ECO_THRESHOLD;
$lang['building']['gov_unit_compliance']         = 'compliance';
$lang['building']['gov_unit_night']              = 'night';
$lang['building']['gov_updated_nightly']         = 'Compliance, audits, taxes and subsidies are all processed automatically each night.';
$lang['building']['gov_stats_title']             = 'Lifetime Summary';
$lang['building']['gov_stats_fines']             = 'Total regulation fines paid';
$lang['building']['gov_stats_subsidies']         = 'Total subsidies received';

// Night skiing event strings
$lang['building']['night_skiing_events_title']         = 'Special Night Events';
$lang['building']['night_skiing_events_intro']         = 'Schedule one-off special events to boost visitor numbers, revenue, and reputation for a single night.';
$lang['building']['night_skiing_event_dj_night']       = 'DJ Night';
$lang['building']['night_skiing_event_race_night']     = 'Race Night';
$lang['building']['night_skiing_event_torchlight_parade'] = 'Torchlight Parade';
$lang['building']['night_skiing_event_schedule']       = 'Schedule';
$lang['building']['night_skiing_event_cancel']         = 'Cancel';
$lang['building']['night_skiing_event_scheduled']      = 'Scheduled';
$lang['building']['night_skiing_event_completed']      = 'Completed';
$lang['building']['night_skiing_event_cancelled']      = 'Cancelled';
$lang['building']['night_skiing_event_no_events']      = 'No events scheduled.';
$lang['building']['night_skiing_grooming_label']       = 'Night grooming surcharge';
$lang['building']['night_skiing_dynamic_demand_label'] = 'Night visitor demand';
$lang['building']['night_skiing_trail_cards_title']    = 'Slope Lighting';
$lang['building']['night_skiing_trail_cards_intro']    = 'Configure lighting for each slope. Toggle night skiing per slope or click Configure for detailed settings.';
$lang['building']['night_skiing_live_preview_title']   = 'Live Revenue Preview';
$lang['building']['night_skiing_est_visitors']         = 'Est. night visitors';
$lang['building']['night_skiing_est_revenue']          = 'Est. total revenue';
$lang['building']['night_skiing_est_costs']            = 'Est. total costs';
$lang['building']['night_skiing_est_net']              = 'Est. net';
$lang['building']['night_skiing_forecast_title']       = 'Visitor Forecast';
$lang['building']['night_skiing_forecast_explainer']   = 'Estimates tonight\'s night-skiing visitors based on price, hours, events and weather factors.';
$lang['building']['night_skiing_cost_breakdown_title'] = 'Nightly Cost Breakdown';
$lang['building']['night_skiing_revenue_trends_title'] = 'Revenue Trends';
$lang['building']['night_skiing_revenue_trends_help']  = 'Recent night-skiing revenue split between tickets, school lessons, photos and events.';
$lang['building']['night_skiing_tonight_event_badge']  = 'Tonight\'s event';
$lang['building']['night_skiing_event_type_label']     = 'Event Type';
$lang['building']['night_skiing_event_date_label']     = 'Date';
$lang['building']['night_skiing_schedule_event_btn']   = 'Schedule Event';
$lang['building']['night_skiing_upcoming_events_title']= 'Upcoming Events';
$lang['building']['night_skiing_quality_impact']       = 'Condition Loss';
$lang['building']['night_skiing_quality_impact_help']  = 'Higher brightness causes faster snow degradation.';
