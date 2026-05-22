<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Seed the game_staff_candidates table with the global agency pool
 *
 * These rows have id_resort = NULL (global agency pool).  When a resort first
 * visits the Hire Staff page the controller calls Staff_model::seed_candidates_for_resort_db()
 * to clone a subset of these into resort-specific rows with a 7-day expiry.
 *
 * This migration only seeds the global pool so that fresh installs have
 * candidates immediately.  Each resort's rolling pool is generated on-demand
 * in the controller.
 */
class Migration_Seed_staff_candidates extends CI_Migration {

    public function up() {
        $now = date('Y-m-d H:i:s');
        // available_until = NULL on global rows (never expire)
        $far_future = NULL;

        $rows = [
            // -------- Ski Patrol --------
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Marcus Reid',       'name_french'=>'Marcus Reid',       'efficiency'=>50,'salary'=>2000,'hire_bonus'=>0,   'specialization'=>'safety',    'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Sophie Blanc',      'name_french'=>'Sophie Blanc',      'efficiency'=>55,'salary'=>2200,'hire_bonus'=>0,   'specialization'=>'speed',     'trait'=>'easygoing',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Luca Ferretti',     'name_french'=>'Luca Ferretti',     'efficiency'=>70,'salary'=>3000,'hire_bonus'=>500, 'specialization'=>'precision', 'trait'=>'loyal',       'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Hana Koval',        'name_french'=>'Hana Koval',        'efficiency'=>75,'salary'=>3200,'hire_bonus'=>500, 'specialization'=>'endurance', 'trait'=>'sensitive',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Étienne Moreau',    'name_french'=>'Étienne Moreau',    'efficiency'=>80,'salary'=>3500,'hire_bonus'=>800, 'specialization'=>'safety',    'trait'=>'ambitious',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skipatrol',      'name_english'=>'Alicia Dupont',     'name_french'=>'Alicia Dupont',     'efficiency'=>90,'salary'=>4000,'hire_bonus'=>1500,'specialization'=>'speed',     'trait'=>'hardworking', 'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],

            // -------- Ski Instructor --------
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Tom Villeneuve',    'name_french'=>'Tom Villeneuve',    'efficiency'=>50,'salary'=>1800,'hire_bonus'=>0,   'specialization'=>'trainer',   'trait'=>'easygoing',   'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Mia Schneider',     'name_french'=>'Mia Schneider',     'efficiency'=>55,'salary'=>2000,'hire_bonus'=>0,   'specialization'=>'endurance', 'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Carlos Ruiz',       'name_french'=>'Carlos Ruiz',       'efficiency'=>65,'salary'=>2400,'hire_bonus'=>300, 'specialization'=>'trainer',   'trait'=>'ambitious',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Ingrid Olsen',      'name_french'=>'Ingrid Olsen',      'efficiency'=>70,'salary'=>2700,'hire_bonus'=>500, 'specialization'=>'precision', 'trait'=>'loyal',       'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Jean-Pierre Favre', 'name_french'=>'Jean-Pierre Favre', 'efficiency'=>85,'salary'=>3300,'hire_bonus'=>1000,'specialization'=>'trainer',   'trait'=>'sensitive',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'skiinstructor',  'name_english'=>'Priya Sharma',      'name_french'=>'Priya Sharma',      'efficiency'=>90,'salary'=>3600,'hire_bonus'=>1500,'specialization'=>'endurance', 'trait'=>'hardworking', 'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],

            // -------- Lift Mechanic --------
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Bruno Wagner',      'name_french'=>'Bruno Wagner',      'efficiency'=>50,'salary'=>2200,'hire_bonus'=>0,   'specialization'=>'tech',      'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Yuki Tanaka',       'name_french'=>'Yuki Tanaka',       'efficiency'=>55,'salary'=>2400,'hire_bonus'=>0,   'specialization'=>'precision', 'trait'=>'loyal',       'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Arnaud Lefort',     'name_french'=>'Arnaud Lefort',     'efficiency'=>70,'salary'=>3300,'hire_bonus'=>500, 'specialization'=>'tech',      'trait'=>'ambitious',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Zara Patel',        'name_french'=>'Zara Patel',        'efficiency'=>75,'salary'=>3500,'hire_bonus'=>700, 'specialization'=>'speed',     'trait'=>'easygoing',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Dieter Braun',      'name_french'=>'Dieter Braun',      'efficiency'=>90,'salary'=>4400,'hire_bonus'=>1500,'specialization'=>'precision', 'trait'=>'hardworking', 'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'liftmechanic',   'name_english'=>'Amara Osei',        'name_french'=>'Amara Osei',        'efficiency'=>100,'salary'=>5000,'hire_bonus'=>2000,'specialization'=>'tech',     'trait'=>'loyal',       'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],

            // -------- Groomer Mechanic --------
            ['id_resort'=>NULL,'position'=>'mechanicGroomer','name_english'=>'Paul Renard',       'name_french'=>'Paul Renard',       'efficiency'=>50,'salary'=>2000,'hire_bonus'=>0,   'specialization'=>'tech',      'trait'=>'easygoing',   'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'mechanicGroomer','name_english'=>'Klara Novak',       'name_french'=>'Klara Novak',       'efficiency'=>55,'salary'=>2200,'hire_bonus'=>0,   'specialization'=>'precision', 'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'mechanicGroomer','name_english'=>'Rémi Garnier',      'name_french'=>'Rémi Garnier',      'efficiency'=>70,'salary'=>3000,'hire_bonus'=>400, 'specialization'=>'speed',     'trait'=>'loyal',       'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'mechanicGroomer','name_english'=>'Fatima Hassan',     'name_french'=>'Fatima Hassan',     'efficiency'=>75,'salary'=>3200,'hire_bonus'=>600, 'specialization'=>'endurance', 'trait'=>'ambitious',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'mechanicGroomer','name_english'=>'Lars Eriksson',     'name_french'=>'Lars Eriksson',     'efficiency'=>90,'salary'=>4000,'hire_bonus'=>1500,'specialization'=>'tech',      'trait'=>'sensitive',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],

            // -------- Bus Driver --------
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Mohamed Ali',       'name_french'=>'Mohamed Ali',       'efficiency'=>50,'salary'=>1600,'hire_bonus'=>0,   'specialization'=>'safety',    'trait'=>'easygoing',   'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Nina Schulz',       'name_french'=>'Nina Schulz',       'efficiency'=>55,'salary'=>1800,'hire_bonus'=>0,   'specialization'=>'speed',     'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Vincent Paquet',    'name_french'=>'Vincent Paquet',    'efficiency'=>70,'salary'=>2400,'hire_bonus'=>300, 'specialization'=>'endurance', 'trait'=>'loyal',       'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Aiko Watanabe',     'name_french'=>'Aiko Watanabe',     'efficiency'=>75,'salary'=>2600,'hire_bonus'=>500, 'specialization'=>'safety',    'trait'=>'ambitious',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Roberto Costa',     'name_french'=>'Roberto Costa',     'efficiency'=>90,'salary'=>3200,'hire_bonus'=>1200,'specialization'=>'speed',     'trait'=>'sensitive',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'driver',         'name_english'=>'Elsa Lindqvist',    'name_french'=>'Elsa Lindqvist',    'efficiency'=>100,'salary'=>3600,'hire_bonus'=>1800,'specialization'=>'endurance', 'trait'=>'loyal',      'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],

            // -------- Snowmaking Operator --------
            ['id_resort'=>NULL,'position'=>'snowmaker',      'name_english'=>'Tomas Horak',       'name_french'=>'Tomas Horak',       'efficiency'=>50,'salary'=>1800,'hire_bonus'=>0,   'specialization'=>'precision', 'trait'=>'hardworking', 'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'snowmaker',      'name_english'=>'Camille Petit',     'name_french'=>'Camille Petit',     'efficiency'=>55,'salary'=>2000,'hire_bonus'=>0,   'specialization'=>'endurance', 'trait'=>'easygoing',   'contract_months'=>3, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'snowmaker',      'name_english'=>'Igor Petrovic',     'name_french'=>'Igor Petrovič',     'efficiency'=>70,'salary'=>2700,'hire_bonus'=>400, 'specialization'=>'tech',      'trait'=>'loyal',       'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'snowmaker',      'name_english'=>'Sara Lindberg',     'name_french'=>'Sara Lindberg',     'efficiency'=>75,'salary'=>2900,'hire_bonus'=>600, 'specialization'=>'safety',    'trait'=>'ambitious',   'contract_months'=>6, 'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
            ['id_resort'=>NULL,'position'=>'snowmaker',      'name_english'=>'Pierre Guichard',   'name_french'=>'Pierre Guichard',   'efficiency'=>90,'salary'=>3600,'hire_bonus'=>1500,'specialization'=>'precision', 'trait'=>'sensitive',   'contract_months'=>12,'available_until'=>$far_future,'is_hired'=>0,'created_at'=>$now],
        ];

        foreach ($rows as $row) {
            // Only insert if the same global candidate doesn't already exist
            $exists = $this->db
                ->where('position',     $row['position'])
                ->where('name_english', $row['name_english'])
                ->where('id_resort IS NULL', NULL, FALSE)
                ->count_all_results('game_staff_candidates');
            if ($exists === 0) {
                $this->db->insert('game_staff_candidates', $row);
            }
        }
    }

    public function down() {
        // Remove global (id_resort = NULL) seed rows
        $this->db
            ->where('id_resort IS NULL', NULL, FALSE)
            ->delete('game_staff_candidates');
    }
}
