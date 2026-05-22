<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

// Common to all staff pages
$lang['common_staff']['titleMain']		= 'Staff';

// Overview Staff page
$lang['overviewStaff']['title']		= 'Overview Staff';
$lang['overviewStaff']['intro']	= 'On this page you have an overview of the currently hired employees. You can also fire staff if you have too many. When fireing an employee you will neeed to pay a 3-months salary penalty. Don\'t forget to assign each employee to their equipment, slope or lift. The equipments, lifts or slopes only show up in the menu once they are completely built or delivered.';
$lang['overviewStaff']['assigned_to']		= 'Assigned to';
$lang['overviewStaff']['hiring_date']		= 'Hiring date';
$lang['overviewStaff']['fire']                   = 'Fire';
$lang['overviewStaff']['no_staff_hired']		= '<div class="alert alert-warning text-center">You haven\'t hired any employee yet. Do so from the <a href="'.base_url().'hire_staff_controller/">hiring page</a></div>';
$lang['overviewStaff']['staff_fired']            = 'The employee has been fired successfully! Three months of salary have been taken from your bank account.';
$lang['overviewStaff']['staff_not_fired']	= '<div class="alert alert-danger text-center">The employee couldn\'t be fired.</div>';
$lang['overviewStaff']['not_updated_already_assigned']	= 'There is already someone assigned to this slope, lift or equipment. Choose another one.';
$lang['overviewStaff']['fire_tooltip']           = 'Firing an employee will cost you three times his monthly salary. This action cannot be undone.';
$lang['overviewStaff']['confirm_fire']           = 'Are you sure you want to fire this employee?<br>You will have to pay three times his monthly salary.';
$lang['overviewStaff']['updated']           = 'Updated';
$lang['overviewStaff']['available']           = 'available';

// Hire Staff page
$lang['hireStaff']['title']		= 'Hire Staff';
$lang['hireStaff']['hire']                   = 'Hire';
$lang['hireStaff']['intro']		= 'On this page you can hire new employees directly from the employment agency. The more efficient the employee is, the more you have to pay him!<br>Once you have hired an employee you need to assign him to a specific equipment, slope or lift on the <a href="'.base_url().'overview_staff_controller">Overview page</a>';
$lang['hireStaff']['staff_hired']            = '<div class="alert alert-success text-center">The employee has been hired successfully! Don\'t forget to assign him to a specific equipment, slope or lift on the <a href="'.base_url().'overview_staff_controller">Overview page</a></div>';
$lang['hireStaff']['staff_not_hired']	= '<div class="alert alert-danger text-center">The employee couldn\'t be hired.</div>';

// Staff type
$lang['hireStaff']['skipatrol']		= 'Ski patrol';
$lang['hireStaff']['skiinstructor']		= 'Ski/Snowboard instructor';
$lang['hireStaff']['liftmechanic']		= 'Lift mechanic';
$lang['hireStaff']['mechanicGroomer']		= 'Snow groomer mechanic';
$lang['hireStaff']['driver']		= 'Bus driver';
$lang['hireStaff']['snowmaker']		= 'Snowmaking operator';

// Headers
$lang['common_staff']['position']		= 'Position';
$lang['common_staff']['efficiency']		= 'Efficiency';
$lang['common_staff']['salary']                 = 'Salary';

$lang['hireStaff']['too_many_instructors']		= '<div class="alert alert-warning text-center">You have reached the maximum number of ski instructors in your resort. Build more slopes to hire more instructors.</div>';
$lang['hireStaff']['not_enough_money']		= '<div class="alert alert-warning text-center">Not enough money to perform this action.</div>';

// Candidate pool
$lang['hireStaff']['no_candidates']            = '<em>No candidates available at the moment. Refresh the pool to see new applicants.</em>';
$lang['hireStaff']['candidate_unavailable']    = '<div class="alert alert-warning text-center">This candidate is no longer available. The pool has been refreshed.</div>';
$lang['hireStaff']['refresh_pool']             = 'Refresh pool';
$lang['hireStaff']['refresh_tooltip']          = 'Replace current candidates with new applicants from the employment agency. Costs '.CANDIDATE_REFRESH_COST.' €.';
$lang['hireStaff']['refresh_confirm']          = 'Replace current candidates? This costs '.CANDIDATE_REFRESH_COST.' €.';
$lang['hireStaff']['pool_refreshed']           = 'New candidates are now available!';
$lang['hireStaff']['pool_expires_info']        = 'Candidates are available for %d days.';
$lang['hireStaff']['not_enough_money_short']   = 'Not enough money.';

// Contract
$lang['hireStaff']['contract_duration']        = 'Contract';
$lang['hireStaff']['contract_months_fmt']      = '%d months';
$lang['hireStaff']['min_contract']             = 'Min. contract';
$lang['hireStaff']['per_month']                = 'month';
$lang['hireStaff']['hire_bonus']               = 'Signing bonus';

// Specializations
$lang['hireStaff']['spec_speed']               = 'Speed';
$lang['hireStaff']['spec_safety']              = 'Safety';
$lang['hireStaff']['spec_precision']           = 'Precision';
$lang['hireStaff']['spec_endurance']           = 'Endurance';
$lang['hireStaff']['spec_trainer']             = 'Trainer';
$lang['hireStaff']['spec_tech']                = 'Tech';
$lang['hireStaff']['spec_none']                = 'None';
// Specialization descriptions
$lang['hireStaff']['spec_desc_speed']          = 'Responds faster to incidents — reduces response time for this employee.';
$lang['hireStaff']['spec_desc_safety']         = 'Extra focus on safety — reduces accident probability at assigned location.';
$lang['hireStaff']['spec_desc_precision']      = 'Meticulous work — higher repair quality and fewer breakdowns over time.';
$lang['hireStaff']['spec_desc_endurance']      = 'Resistant to difficult conditions — lower morale penalty during storms.';
$lang['hireStaff']['spec_desc_trainer']        = 'Expert instructor — guests improve skills faster at this resort.';
$lang['hireStaff']['spec_desc_tech']           = 'Tech-savvy mechanic — equipment assigned to this staff lasts longer.';
$lang['hireStaff']['spec_desc_none']           = 'No specialization.';

// Traits
$lang['hireStaff']['trait_hardworking']        = 'Hardworking';
$lang['hireStaff']['trait_easygoing']          = 'Easy-going';
$lang['hireStaff']['trait_sensitive']          = 'Sensitive';
$lang['hireStaff']['trait_ambitious']          = 'Ambitious';
$lang['hireStaff']['trait_loyal']              = 'Loyal';
$lang['hireStaff']['trait_none']               = 'None';
// Trait descriptions
$lang['hireStaff']['trait_desc_hardworking']   = 'Gains experience faster — reaches skill milestones sooner.';
$lang['hireStaff']['trait_desc_easygoing']     = 'Recovers morale quickly — bounces back after bad days.';
$lang['hireStaff']['trait_desc_sensitive']     = 'Morale drops faster in bad weather or when understaffed.';
$lang['hireStaff']['trait_desc_ambitious']     = 'Morale boosts on skill level-up — thrives on progression.';
$lang['hireStaff']['trait_desc_loyal']         = 'Less morale penalty for long contracts or when firing cost applies.';
$lang['hireStaff']['trait_desc_none']          = 'No particular trait.';

// Skill levels (for overview page tooltips)
$lang['common_staff']['skill_level']           = 'Career Level';
$lang['common_staff']['experience']            = 'Experience';

// Staff Morale
$lang['overviewStaff']['morale']                 = 'Morale';
$lang['overviewStaff']['on_strike']              = 'On strike!';
$lang['overviewStaff']['strike_alert']           = '⚠ Strike alert!';
$lang['overviewStaff']['strike_count']           = '%d employee(s) are currently on strike and are not working.';
$lang['overviewStaff']['low_morale_warning']     = '⚠ Staff morale is low. Repairs will be slower and the risk of accidents is increased. Raise salaries or improve working conditions to prevent a strike.';
$lang['overviewStaff']['morale_tooltip']         = 'Staff morale (0-100). Affected by pay, workload and weather. Low morale causes slow repairs, higher accident risk, and possible strike.';

// Staff Training
$lang['overviewStaff']['train']                  = 'Train';
$lang['overviewStaff']['train_tooltip']          = 'Send this staff member to a training session to boost their career level. Costs ' . STAFF_TRAINING_COST . ' € and awards ' . STAFF_TRAINING_XP . ' XP. Once per ' . STAFF_TRAINING_COOLDOWN_HOURS . ' hours.';
$lang['overviewStaff']['train_info']             = 'You can train any staff member to accelerate their career progression. Each session costs';
$lang['overviewStaff']['train_info_xp']          = 'and awards';
$lang['overviewStaff']['train_success']          = 'Training complete! XP awarded.';
$lang['overviewStaff']['train_level_up']         = 'Career level up →';
$lang['overviewStaff']['train_max_level']        = 'This staff member has already reached the maximum career level.';
$lang['overviewStaff']['train_cooldown']         = 'This staff member was recently trained. Next session available at:';
$lang['overviewStaff']['train_not_enough_cash']  = 'Not enough cash to pay for the training session.';
$lang['overviewStaff']['train_error']            = 'An error occurred. Please try again.';
$lang['overviewStaff']['train_log_entry']        = 'Staff training session:';




