<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Group Discount & Parking Fee Constants
|--------------------------------------------------------------------------
|
| Constants for the group ski-pass discount and parking fee features
| added to the Building Access Controller.
|
*/

// Ski pass price limits
const MIN_SKIPASS_DAILY              = 10;   // Minimum daily ski pass price (€)
const MAX_SKIPASS_DAILY              = 100;  // Maximum daily ski pass price (€)
const MIN_SKIPASS_WEEKLY             = 70;   // Minimum weekly ski pass price (€)
const MAX_SKIPASS_WEEKLY             = 700;  // Maximum weekly ski pass price (€)

// Group discount defaults
const DEFAULT_GROUP_DISCOUNT_PCT     = 0;    // Group discount disabled by default
const DEFAULT_PARKING_FEE            = 10;   // Default parking fee (€/vehicle/day)

// Group discount pricing constants
const GROUP_VISITOR_FRACTION         = 0.15; // Fraction of daily visitors eligible for group discount (15%)
const GROUP_DISCOUNT_DEMAND_BONUS    = 0.30; // Demand increase per 1% of group discount (0.3% more visitors per 1% discount)
const MIN_GROUP_DISCOUNT_PCT         = 0;    // 0 = disabled
const MAX_GROUP_DISCOUNT_PCT         = 30;   // Maximum group discount (%)

// Parking fee constants
const MIN_PARKING_FEE                = 5;    // Minimum parking fee (€/vehicle/day)
const MAX_PARKING_FEE                = 50;   // Maximum parking fee (€/vehicle/day)
const PARKING_FEE_DEMAND_FACTOR      = 0.02; // Demand reduction per €1 above DEFAULT_PARKING_FEE (2% fewer cars per €1)

// CodeIgniter requires config files to define a $config array
$config['default_group_discount_pct']  = DEFAULT_GROUP_DISCOUNT_PCT;
$config['default_parking_fee']         = DEFAULT_PARKING_FEE;
$config['group_visitor_fraction']      = GROUP_VISITOR_FRACTION;
$config['group_discount_demand_bonus'] = GROUP_DISCOUNT_DEMAND_BONUS;
$config['min_group_discount_pct']      = MIN_GROUP_DISCOUNT_PCT;
$config['max_group_discount_pct']      = MAX_GROUP_DISCOUNT_PCT;
$config['min_parking_fee']             = MIN_PARKING_FEE;
$config['max_parking_fee']             = MAX_PARKING_FEE;
$config['parking_fee_demand_factor']   = PARKING_FEE_DEMAND_FACTOR;

// Night Skiing Quality Degradation
defined('NIGHT_SKIING_QUALITY_LOSS_BASE') or define('NIGHT_SKIING_QUALITY_LOSS_BASE', 1);
defined('NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR') or define('NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR', 0.5);

$config['night_skiing_quality_loss_base'] = NIGHT_SKIING_QUALITY_LOSS_BASE;
$config['night_skiing_quality_loss_brightness_factor'] = NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR;
