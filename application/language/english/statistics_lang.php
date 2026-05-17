<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['statistics']['title']    = 'Statistics Dashboard';
$lang['statistics']['intro']    = 'Detailed charts giving you an in-depth view of your resort\'s performance. Data is updated each night.';

// Chart titles
$lang['statistics']['chart_lift_usage_title']       = 'Peak Lift Usage';
$lang['statistics']['chart_revenue_per_lift_title'] = 'Revenue per Lift';
$lang['statistics']['chart_slope_popularity_title'] = 'Most Popular Slopes';
$lang['statistics']['chart_satisfaction_title']     = 'Guest Satisfaction';
$lang['statistics']['chart_weather_title']          = 'Weather History';

// New charts
$lang['statistics']['chart_visitor_count_title']    = 'Daily Visitor Count';
$lang['statistics']['chart_visitor_count_desc']     = 'Number of visitors per day over the current season or the last 7 days.';
$lang['statistics']['chart_revenue_expenses_title'] = 'Daily Revenue vs Expenses';
$lang['statistics']['chart_revenue_expenses_desc']  = 'Revenue and expenses per day over the current season or the last 7 days.';

// Chart descriptions
$lang['statistics']['chart_lift_usage_desc']        = 'Effective passenger throughput of each open lift, adjusted for current condition.';
$lang['statistics']['chart_revenue_per_lift_desc']  = 'Estimated skipass revenue contribution and operating cost per lift based on throughput.';
$lang['statistics']['chart_slope_popularity_desc']  = 'Open slopes ranked by current condition — higher condition reflects heavier usage and maintenance.';
$lang['statistics']['chart_satisfaction_desc']      = 'Resort reputation over time, reflecting overall guest satisfaction.';
$lang['statistics']['chart_weather_desc']           = 'Snow level (cm) history over the current season or the last 7 days.';

// Labels
$lang['statistics']['lift']               = 'Lift';
$lang['statistics']['slope']              = 'Slope';
$lang['statistics']['date']               = 'Date';
$lang['statistics']['yesterday']          = 'Yesterday';
$lang['statistics']['reputation']         = 'Reputation';
$lang['statistics']['effective_throughput'] = 'Effective Throughput (persons/h)';
$lang['statistics']['estimated_revenue']  = 'Estimated Revenue (€)';
$lang['statistics']['operating_cost']     = 'Operating Cost (€)';
$lang['statistics']['condition']          = 'Condition (%)';
$lang['statistics']['snow_level']         = 'Snow Level';
$lang['statistics']['persons_per_hour']   = 'Persons / hour';
$lang['statistics']['euros']              = 'Amount (€)';
$lang['statistics']['condition_pct']      = 'Condition (%)';
$lang['statistics']['snow_level_cm']      = 'Snow Level (cm)';
$lang['statistics']['visitors']           = 'Visitors';
$lang['statistics']['visitors_label']     = 'Visitors (persons / day)';
$lang['statistics']['revenue_label']      = 'Revenue (€)';
$lang['statistics']['expenses_label']     = 'Expenses (€)';
