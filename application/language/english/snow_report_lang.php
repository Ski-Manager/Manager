<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------
// Snow Report page strings (English)
// -----------------------------------------------------------------------

$lang['snow_report']['title']  = 'Snow Report';
$lang['snow_report']['intro']  = 'Publish a daily snow conditions report for your resort. A quality report boosts your reputation and lets visitors know what to expect on the slopes.';

// Form labels
$lang['snow_report']['publish_heading']  = 'Publish Today\'s Snow Report';
$lang['snow_report']['snow_depth_label'] = 'Average snow depth (cm)';
$lang['snow_report']['fresh_snow_label'] = 'Fresh snow in last 24 h (cm)';
$lang['snow_report']['conditions_label'] = 'Overall conditions';
$lang['snow_report']['coverage_label']   = 'Piste coverage (%)';
$lang['snow_report']['note_label']       = 'Note for visitors (optional)';
$lang['snow_report']['note_placeholder'] = 'e.g. Powder morning on the north face. All lifts operating.';
$lang['snow_report']['publish_button']   = 'Publish Report';

// Conditions options
$lang['snow_report']['conditions_poor']      = 'Poor';
$lang['snow_report']['conditions_fair']      = 'Fair';
$lang['snow_report']['conditions_good']      = 'Good';
$lang['snow_report']['conditions_excellent'] = 'Excellent';

// Reputation bonuses per tier (for display)
$lang['snow_report']['rep_bonus_poor']      = 'No bonus';
$lang['snow_report']['rep_bonus_fair']      = '+2 reputation';
$lang['snow_report']['rep_bonus_good']      = '+5 reputation';
$lang['snow_report']['rep_bonus_excellent'] = '+10 reputation';

// Status messages
$lang['snow_report']['already_published_today'] = 'You have already published a snow report today. Come back tomorrow!';
$lang['snow_report']['publish_error']           = 'An error occurred while saving your report. Please try again.';
$lang['snow_report']['publish_success_no_bonus']= 'Snow report published successfully!';
$lang['snow_report']['publish_success_bonus']   = 'Snow report published! Your resort earned +%d reputation.';

// Latest / history section
$lang['snow_report']['latest_heading']   = 'Latest Report';
$lang['snow_report']['history_heading']  = 'Report History';
$lang['snow_report']['no_reports_yet']   = 'No snow reports published yet. Be the first to let visitors know your conditions!';
$lang['snow_report']['date_col']         = 'Date';
$lang['snow_report']['depth_col']        = 'Depth (cm)';
$lang['snow_report']['fresh_col']        = 'Fresh (cm)';
$lang['snow_report']['conditions_col']   = 'Conditions';
$lang['snow_report']['coverage_col']     = 'Coverage (%)';
$lang['snow_report']['rep_bonus_col']    = 'Rep bonus';
$lang['snow_report']['note_col']         = 'Note';

// Tips panel
$lang['snow_report']['tips_heading']  = 'Tips';
$lang['snow_report']['tip_1']         = 'Publishing a report every day builds trust with visitors and keeps your resort in the spotlight.';
$lang['snow_report']['tip_2']         = 'Excellent conditions award +10 reputation when you publish.';
$lang['snow_report']['tip_3']         = 'An honest report that matches actual conditions improves long-term guest satisfaction.';

// Current game data panel
$lang['snow_report']['game_data_heading']     = 'Current Game Data';
$lang['snow_report']['game_snow_level_label'] = 'Snow level';
$lang['snow_report']['game_fresh_snow_label'] = 'Snowfall today';
$lang['snow_report']['game_temperature_label']= 'Temperature';
$lang['snow_report']['game_weather_label']    = 'Weather';
$lang['snow_report']['game_data_hint']        = 'These values are pre-filled from your resort\'s current game data. You may adjust them before publishing.';

// Activity log
$lang['snow_report']['log_type']      = 'Snow Report';
