<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['guide']['page_title']        = 'Ski Manager – Complete Game Guide';
$lang['guide']['last_updated']      = 'Last updated: 2025';
$lang['guide']['toc_title']         = 'Table of Contents';

// 1 Getting started
$lang['guide']['gs_title']          = 'Getting Started';
$lang['guide']['gs_body']           = 'Ski Manager is a free-to-play browser-based ski resort management simulation. You start with an empty mountain, a modest cash reserve, and the freedom to build your dream resort from the ground up. No installation is required – just create a free account and you are on the slopes in minutes. The game runs entirely in your browser and automatically saves your progress.';
$lang['guide']['gs_step1_title']    = 'Create Your Account';
$lang['guide']['gs_step1']          = 'Click <strong>Sign Up</strong> on the homepage, choose a username, and verify your e-mail address. Once confirmed, you land on your resort overview and can start building immediately.';
$lang['guide']['gs_step2_title']    = 'Name Your Resort';
$lang['guide']['gs_step2']          = 'Head to <strong>Resort Settings</strong> and give your mountain a unique name and choose a country. The resort name appears on the leaderboard and in your public resort profile, so pick something memorable.';
$lang['guide']['gs_step3_title']    = 'Build Your First Slope';
$lang['guide']['gs_step3']          = 'Every resort needs at least one open slope to welcome skiers. Open the <strong>Slopes</strong> menu, choose a difficulty (green, blue, red, or black), and confirm the build. Each slope has a construction cost and a daily maintenance fee, so keep an eye on your budget.';
$lang['guide']['gs_step4_title']    = 'Add a Ski Lift';
$lang['guide']['gs_step4']          = 'Skiers need a way up the mountain. Open the <strong>Lifts</strong> menu and build a basic surface lift or chairlift. Lift capacity directly affects how many visitors can ski per day, which drives revenue.';

// 2 Slopes
$lang['guide']['slopes_title']      = 'Slopes & Trails';
$lang['guide']['slopes_body']       = 'Slopes are the core product of your resort. They attract skiers, generate revenue, and define the character of your mountain. Each slope has a difficulty rating, a length, a maintenance cost, and a grooming schedule.';
$lang['guide']['slopes_diff_title'] = 'Difficulty Ratings';
$lang['guide']['slopes_green']      = '<strong>Green (Beginner):</strong> Low construction cost, attracts families and beginners. Lower revenue per visit but very high visitor volume.';
$lang['guide']['slopes_blue']       = '<strong>Blue (Intermediate):</strong> The most popular category. A good mix of blues will keep the majority of skiers happy.';
$lang['guide']['slopes_red']        = '<strong>Red (Advanced):</strong> Higher revenue per visit. Attracts enthusiasts willing to pay premium prices.';
$lang['guide']['slopes_black']      = '<strong>Black (Expert):</strong> Highest revenue per run but the smallest audience. Pair with a ski patrol building to reduce injury incidents.';
$lang['guide']['slopes_maint']      = 'Grooming a slope overnight costs money but significantly raises skier satisfaction. If you skip grooming, satisfaction drops and visitors leave early – costing you more in lost revenue than the grooming fee.';
$lang['guide']['slopes_custom']     = 'Select a slope on the <strong>Resort Map</strong> to view its details and build it. Available slopes are pre-placed on the terrain and can be built with different difficulty levels.';

// 3 Lifts
$lang['guide']['lifts_title']       = 'Ski Lifts';
$lang['guide']['lifts_body']        = 'Lifts are the arteries of your resort. Without sufficient lift capacity, bottlenecks form, wait times grow, and skiers leave unsatisfied. Choosing the right lift type for each zone is key to an efficient mountain layout.';
$lang['guide']['lift_types_title']  = 'Lift Types';
$lang['guide']['lift_surface']      = '<strong>Surface Lift / T-Bar:</strong> Cheapest to build and maintain. Ideal for beginner slopes. Very limited capacity, only suitable for low-traffic zones.';
$lang['guide']['lift_chair']        = '<strong>Chairlift:</strong> The workhorse of any resort. Medium cost, medium capacity. Available in 2-seat and 4-seat variants; upgrade to detachable quads for significantly higher throughput.';
$lang['guide']['lift_gondola']      = '<strong>Gondola / Cable Car:</strong> Highest capacity and most prestigious. Expensive to build but greatly boosts resort reputation and handles very high skier volumes.';
$lang['guide']['lift_tech']         = 'Upgrade lift technology in the <strong>Lift Tech</strong> menu to reduce mechanical failures, lower maintenance costs, and increase hourly capacity. Investing in modern lift systems pays for itself within a few seasons.';
$lang['guide']['lift_closed']       = 'When the resort is closed (off-season or by manual decision), all lifts automatically stop. Use the closed period for maintenance and upgrades at a reduced cost.';

// 4 Buildings
$lang['guide']['buildings_title']   = 'Buildings & Infrastructure';
$lang['guide']['buildings_body']    = 'Beyond slopes and lifts, a successful resort needs a full ecosystem of buildings. Buildings generate additional revenue, attract specific visitor types, and unlock new game mechanics.';
$lang['guide']['bld_ticket']        = '<strong>Ticket Office:</strong> Required to charge lift tickets. Set daily, half-day, and season-pass prices. Price too high and visitors turn away; price too low and you leave money on the table.';
$lang['guide']['bld_hotel']         = '<strong>Hotel / Lodge:</strong> Allows overnight stays, converting day visitors into multi-day guests. Each hotel level increases capacity and the nightly rate you can charge.';
$lang['guide']['bld_restaurant']    = '<strong>Restaurant / Cafeteria:</strong> Boosts visitor satisfaction and generates food & beverage revenue. Visitors who eat on-site stay longer and spend more overall.';
$lang['guide']['bld_school']        = '<strong>Ski School:</strong> Converts beginner visitors into paying lesson students. High-margin revenue that also boosts beginner visitor satisfaction.';
$lang['guide']['bld_rental']        = '<strong>Equipment Rental:</strong> Lets non-equipped visitors hire skis and boots. Opens your resort to visitors who would otherwise not come.';
$lang['guide']['bld_spa']           = '<strong>Wellness Spa:</strong> Premium amenity that attracts high-spending visitors and raises your resort prestige score.';
$lang['guide']['bld_patrol']        = '<strong>Ski Patrol:</strong> Reduces accident rates on difficult slopes. Without ski patrol, injury incidents lower reputation and can attract negative press events.';
$lang['guide']['bld_snowmaking']    = '<strong>Snowmaking Facility:</strong> Essential for resorts at lower elevations. Allows you to open slopes earlier in the season and stay open during warm spells.';

// 5 Staff
$lang['guide']['staff_title']       = 'Staff & Human Resources';
$lang['guide']['staff_body']        = 'Your staff are the backbone of the resort. Each employee type fills a specific role and directly affects guest satisfaction, safety, and operational efficiency. Hiring the right mix of staff is as important as building the right slopes.';
$lang['guide']['staff_types_title'] = 'Staff Roles';
$lang['guide']['staff_inst']        = '<strong>Ski Instructors:</strong> Required for the Ski School building. The more instructors you employ, the more students you can accept each day. Experienced instructors earn higher salaries but also command higher lesson fees.';
$lang['guide']['staff_patrol']      = '<strong>Ski Patrol:</strong> Reduce accident frequency on black and red slopes. Each patrol officer covers a set number of runs per day.';
$lang['guide']['staff_mechanic']    = '<strong>Lift Mechanics:</strong> Keep lifts running. Without enough mechanics, lift breakdowns become frequent, stranding skiers and tanking satisfaction.';
$lang['guide']['staff_groomer']     = '<strong>Slope Groomers:</strong> Groom runs overnight. More groomers means more slopes groomed per night, maintaining a higher average slope quality.';
$lang['guide']['staff_manager']     = '<strong>Resort Manager:</strong> A premium staff member who passively boosts all department efficiencies. Hire one as soon as budget allows.';
$lang['guide']['staff_upgrades']    = 'Upgrade staff in the <strong>Staff Upgrades</strong> screen to give certifications and equipment, raising their effectiveness without having to hire additional headcount.';

// 6 Finances
$lang['guide']['finance_title']     = 'Finances & Pricing';
$lang['guide']['finance_body']      = 'Managing your resort\'s cash flow is central to long-term success. Revenue comes from lift tickets, food & beverage, ski school, rentals, and accommodation. Expenses include staff wages, building maintenance, lift upkeep, slope grooming, and loan repayments.';
$lang['guide']['finance_pricing']   = 'Set competitive prices based on your reputation and the quality of your offering. The in-game demand curve panel shows how price changes affect expected visitor numbers. Aim for the sweet spot that maximises total revenue, not just margin per visitor.';
$lang['guide']['finance_loans']     = 'The in-game bank offers loans to fund major expansions. Use loans strategically – borrow to build revenue-generating assets (a new gondola, a hotel wing) rather than to cover operational deficits.';
$lang['guide']['finance_reports']   = 'Check the <strong>Reporting</strong> dashboard daily to track revenue trends, visitor counts, and expense breakdowns. Weekly and seasonal summaries help you spot problems before they become crises.';

// 7 Weather & Seasons
$lang['guide']['weather_title']     = 'Weather & Seasons';
$lang['guide']['weather_body']      = 'Ski Manager simulates a full four-season calendar. Snow conditions, visitor demand, and event availability all change with the in-game calendar. Planning around the seasons is essential for maintaining profitability year-round.';
$lang['guide']['weather_winter']    = '<strong>Winter:</strong> Peak season. High visitor numbers, maximum lift-ticket revenue, full competition calendar. Prioritise grooming and staffing.';
$lang['guide']['weather_spring']    = '<strong>Spring:</strong> Melting snow reduces slope count. Revenue drops. Good time for upgrades with lifts and slopes temporarily closed at reduced cost.';
$lang['guide']['weather_summer']    = '<strong>Summer:</strong> No skiing, but you can open mountain-bike trails and hiking infrastructure (if available) for summer revenue. The resort can be set to maintenance mode to reduce ongoing costs.';
$lang['guide']['weather_autumn']    = '<strong>Autumn:</strong> Pre-season. Snowmaking becomes critical to open slopes before competitors. Early opening boosts reputation significantly.';
$lang['guide']['weather_events']    = 'Random weather events – blizzards, rain-on-snow, warm fronts – can force slope closures. The <strong>Climate Change</strong> module adds long-term precipitation trends that affect high-altitude and low-altitude resorts differently.';

// 8 Snowmaking
$lang['guide']['snow_title']        = 'Snowmaking';
$lang['guide']['snow_body']         = 'Snowmaking transforms your resort from a weather-dependent operation into a reliable, season-extended destination. Build snowmaking infrastructure in the <strong>Buildings</strong> menu and assign snowmaking capacity to individual slopes via the Snowmaking panel.';
$lang['guide']['snow_guns']         = 'Snow guns are assigned per slope. Each gun covers a certain area per hour when operating conditions are met (temperature below −2 °C, no heavy rain). Running snow guns consumes significant electricity, so schedule operations during off-peak hours when energy costs are lower.';
$lang['guide']['snow_strategy']     = 'Focus snowmaking resources on your most profitable slopes first. A fully covered groomed blue slope that opens a week before natural snow arrives can recover the entire snowmaking investment in a single season.';

// 9 Competitions & Tournaments
$lang['guide']['comp_title']        = 'Competitions & Tournaments';
$lang['guide']['comp_body']         = 'Hosting ski competitions is one of the highest-profile activities in Ski Manager. Tournaments bring a surge of visitors, generate substantial prize-pool revenue, and dramatically boost resort reputation when run successfully.';
$lang['guide']['comp_types']        = 'Available event types include slalom, giant slalom, super-G, downhill, and freestyle. Higher-level events have stricter requirements – you need a certain number of certified slopes, a minimum staff headcount, and specific buildings like a timing shack and a medal podium area.';
$lang['guide']['comp_prep']         = 'Preparation matters. Make sure all required slopes are groomed and open the day before the event. Have enough ski patrol on duty. Any closed slope or lift on event day incurs a reputation penalty.';
$lang['guide']['comp_reward']       = 'Winning tournaments unlocks trophies displayed on your resort profile and earns points toward the global leaderboard. Consecutive successful events raise your tournament prestige tier, unlocking access to international championship events.';

// 10 Leaderboard
$lang['guide']['lb_title']          = 'Leaderboard & Competition';
$lang['guide']['lb_body']           = 'The global leaderboard ranks all resorts by reputation. Reputation is earned by opening slopes, building facilities, hiring staff, hosting competitions, and achieving in-game milestones. The leaderboard resets each season, giving every player the chance to climb the rankings.';
$lang['guide']['lb_tabs']           = 'Three leaderboard tabs are available: <strong>Global</strong> (all players ranked by reputation), <strong>By Region</strong> (filtered to your resort\'s country), and <strong>By Slopes</strong> (ranked by the number of open slopes). Each tab tells a different story about resort performance.';
$lang['guide']['lb_sandbox']        = 'The sandbox mode leaderboard is separate from the standard leaderboard. Sandbox mode gives players unlimited funds to experiment freely without competing against standard players.';

// 11 Tips & Tricks
$lang['guide']['tips_title']        = 'Tips & Tricks';
$lang['guide']['tip_1']             = 'Always keep at least two weeks of operating costs in your cash reserve. An unexpected lift breakdown or an early warm spell can drain your budget fast.';
$lang['guide']['tip_2']             = 'Reputation is more valuable than cash. Prioritise actions that raise reputation (new slopes, new buildings, hosting events) over actions that save marginal costs.';
$lang['guide']['tip_3']             = 'Balance your slope difficulty mix. A resort with only black runs will repel 80 % of the skier population. Aim for roughly 40 % blue, 30 % green, 20 % red, 10 % black.';
$lang['guide']['tip_4']             = 'Upgrade lifts before slopes. A high-capacity gondola feeding three blue runs generates more total revenue than six blue runs served by a single surface lift.';
$lang['guide']['tip_5']             = 'Check the demand curve before adjusting ticket prices. Even a 5 % price drop can increase visitor volume by 15 %, yielding more net revenue.';
$lang['guide']['tip_6']             = 'Use the off-season for upgrades. Construction and upgrade costs are reduced when slopes are closed, and there is no visitor disruption.';
$lang['guide']['tip_7']             = 'Hire a Resort Manager early. The passive efficiency bonus compounds across all departments and pays back the salary many times over.';
$lang['guide']['tip_8']             = 'Watch the weather forecast. If a warm front is approaching, close low-altitude slopes proactively rather than waiting for the system to force-close them, which costs more reputation.';
$lang['guide']['tip_9']             = 'Complete the daily bonus challenges each day for free cash injections that can fund upgrades without taking out a loan.';
$lang['guide']['tip_10']            = 'Join the community Discord and forum. Experienced players share maps, pricing strategies, and event scheduling tips that can shave months off your learning curve.';
