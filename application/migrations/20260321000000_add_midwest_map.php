<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Midwest Map
 *
 * - Creates game_map_types table to support multiple resort map styles.
 * - Inserts Alpine (1) and Midwest (2) map type entries.
 * - Adds id_map_type column to game_sectors and game_resorts.
 * - Inserts Midwest sectors 7–11 with boundary paths.
 * - Inserts representative Midwest slopes, lifts, and locations.
 *
 * Coordinate system (shared with existing map):
 *   x = 0 (left) → 1000 (right)
 *   y = 0 (bottom / base area) → 500 (top / mountain peak)
 * SVG image y-axis is inverted: SVG y = 500 − game y.
 */
class Migration_Add_midwest_map extends CI_Migration {

    public function up() {

        // ── 1. game_map_types ─────────────────────────────────────────────────
        if (!$this->db->table_exists('game_map_types')) {
            $this->dbforge->add_field([
                'id_map_type'      => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name_english'     => ['type' => 'VARCHAR', 'constraint' => 80],
                'name_french'      => ['type' => 'VARCHAR', 'constraint' => 80],
                'map_image'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'mini_map_image'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'default_center_x' => ['type' => 'INT', 'default' => 300],
                'default_center_y' => ['type' => 'INT', 'default' => 170],
                'default_zoom'     => ['type' => 'INT', 'default' => 1],
            ]);
            $this->dbforge->add_key('id_map_type', TRUE);
            $this->dbforge->create_table('game_map_types');
        }

        // Alpine (type 1) — existing map
        if ($this->db->where('id_map_type', 1)->count_all_results('game_map_types') === 0) {
            $this->db->insert('game_map_types', [
                'id_map_type'      => 1,
                'name_english'     => 'Alpine',
                'name_french'      => 'Alpin',
                'map_image'        => 'img/images/map.jpg',
                'mini_map_image'   => 'img/images/mini_map.jpg',
                'default_center_x' => 300,
                'default_center_y' => 170,
                'default_zoom'     => 1,
            ]);
        }

        // Midwest (type 2) — new map
        if ($this->db->where('id_map_type', 2)->count_all_results('game_map_types') === 0) {
            $this->db->insert('game_map_types', [
                'id_map_type'      => 2,
                'name_english'     => 'Midwest',
                'name_french'      => 'Midwest',
                'map_image'        => 'img/images/midwest_map.svg',
                'mini_map_image'   => NULL,
                'default_center_x' => 500,
                'default_center_y' => 200,
                'default_zoom'     => 1,
            ]);
        }

        // ── 2. Add id_map_type to game_sectors ───────────────────────────────
        if (!$this->db->field_exists('id_map_type', 'game_sectors')) {
            $this->dbforge->add_column('game_sectors', [
                'id_map_type' => [
                    'type'       => 'INT',
                    'unsigned'   => TRUE,
                    'default'    => 1,
                    'null'       => FALSE,
                    'after'      => 'id_sector',
                ],
            ]);
        }

        // ── 3. Add id_map_type to game_resorts ───────────────────────────────
        if (!$this->db->field_exists('id_map_type', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', [
                'id_map_type' => [
                    'type'    => 'INT',
                    'unsigned' => TRUE,
                    'default' => 1,
                    'null'    => FALSE,
                ],
            ]);
        }

        // ── 4. Midwest Sectors (7–11) ─────────────────────────────────────────
        // Sector paths use [lng, lat] = [x, y] game-coordinate pairs.
        // Polygons are closed (first point = last point).
        $midwest_sectors = [
            [
                'id_sector'   => 7,
                'id_map_type' => 2,
                'color'       => '#EF4444', // red — summit / most challenging
                // Summit strip: x=200–800, y=320–430
                'path'        => '[200,320],[800,320],[800,430],[200,430],[200,320]',
            ],
            [
                'id_sector'   => 8,
                'id_map_type' => 2,
                'color'       => '#3B82F6', // blue — main intermediate runs
                // Main hill: x=150–850, y=170–340
                'path'        => '[150,170],[850,170],[850,340],[150,340],[150,170]',
            ],
            [
                'id_sector'   => 9,
                'id_map_type' => 2,
                'color'       => '#F97316', // orange — terrain park
                // Park corridor: x=690–890, y=90–270
                'path'        => '[690,90],[890,90],[890,270],[690,270],[690,90]',
            ],
            [
                'id_sector'   => 10,
                'id_map_type' => 2,
                'color'       => '#22C55E', // green — beginner / bunny hill
                // Left beginner area: x=80–450, y=70–200
                'path'        => '[80,70],[450,70],[450,200],[80,200],[80,70]',
            ],
            [
                'id_sector'   => 11,
                'id_map_type' => 2,
                'color'       => '#14B8A6', // teal — nordic / cross-country
                // Flat base loop: x=50–950, y=30–120
                'path'        => '[50,30],[950,30],[950,120],[50,120],[50,30]',
            ],
        ];

        foreach ($midwest_sectors as $sector) {
            if ($this->db->where('id_sector', $sector['id_sector'])->count_all_results('game_sectors') === 0) {
                $this->db->insert('game_sectors', $sector);
            }
        }

        // ── 5. Midwest Locations (map connection points) ──────────────────────
        // area values match existing color_array in home.js (0=grey, 1=blue, 2=gold, 3=red, etc.)
        $midwest_locations = [
            // Summit area (sector 7)
            ['id_location' => 201, 'id_group' => 201, 'id_sector' => 7, 'x_coordinates' => 490, 'y_coordinates' => 425, 'length' => 0,   'area' => 3],
            ['id_location' => 202, 'id_group' => 202, 'id_sector' => 7, 'x_coordinates' => 680, 'y_coordinates' => 415, 'length' => 0,   'area' => 3],
            // Mid-mountain (sector 8)
            ['id_location' => 203, 'id_group' => 203, 'id_sector' => 8, 'x_coordinates' => 480, 'y_coordinates' => 280, 'length' => 500, 'area' => 2],
            ['id_location' => 204, 'id_group' => 204, 'id_sector' => 8, 'x_coordinates' => 380, 'y_coordinates' => 285, 'length' => 480, 'area' => 2],
            ['id_location' => 205, 'id_group' => 205, 'id_sector' => 8, 'x_coordinates' => 540, 'y_coordinates' => 275, 'length' => 490, 'area' => 2],
            // Base area (sector 8)
            ['id_location' => 206, 'id_group' => 206, 'id_sector' => 8, 'x_coordinates' => 456, 'y_coordinates' => 92,  'length' => 0,   'area' => 0],
            ['id_location' => 207, 'id_group' => 207, 'id_sector' => 8, 'x_coordinates' => 370, 'y_coordinates' => 95,  'length' => 0,   'area' => 0],
            ['id_location' => 208, 'id_group' => 208, 'id_sector' => 8, 'x_coordinates' => 530, 'y_coordinates' => 90,  'length' => 0,   'area' => 0],
            // Terrain park (sector 9)
            ['id_location' => 209, 'id_group' => 209, 'id_sector' => 9, 'x_coordinates' => 760, 'y_coordinates' => 250, 'length' => 0,   'area' => 2],
            ['id_location' => 210, 'id_group' => 210, 'id_sector' => 9, 'x_coordinates' => 845, 'y_coordinates' => 225, 'length' => 0,   'area' => 2],
            ['id_location' => 211, 'id_group' => 211, 'id_sector' => 9, 'x_coordinates' => 750, 'y_coordinates' => 95,  'length' => 0,   'area' => 1],
            ['id_location' => 212, 'id_group' => 212, 'id_sector' => 9, 'x_coordinates' => 843, 'y_coordinates' => 92,  'length' => 0,   'area' => 1],
            // Beginner area (sector 10)
            ['id_location' => 213, 'id_group' => 213, 'id_sector' => 10, 'x_coordinates' => 200, 'y_coordinates' => 195, 'length' => 0,   'area' => 1],
            ['id_location' => 214, 'id_group' => 214, 'id_sector' => 10, 'x_coordinates' => 162, 'y_coordinates' => 192, 'length' => 0,   'area' => 1],
            ['id_location' => 215, 'id_group' => 215, 'id_sector' => 10, 'x_coordinates' => 200, 'y_coordinates' => 75,  'length' => 0,   'area' => 0],
            ['id_location' => 216, 'id_group' => 216, 'id_sector' => 10, 'x_coordinates' => 162, 'y_coordinates' => 72,  'length' => 0,   'area' => 0],
            // Nordic trailhead (sector 11)
            ['id_location' => 217, 'id_group' => 217, 'id_sector' => 11, 'x_coordinates' => 100, 'y_coordinates' => 80,  'length' => 0,   'area' => 0],
            ['id_location' => 218, 'id_group' => 218, 'id_sector' => 11, 'x_coordinates' => 900, 'y_coordinates' => 78,  'length' => 0,   'area' => 0],
        ];

        foreach ($midwest_locations as $loc) {
            if ($this->db->where('id_location', $loc['id_location'])->count_all_results('game_locations') === 0) {
                $this->db->insert('game_locations', $loc);
            }
        }

        // ── 6. Midwest Slopes ─────────────────────────────────────────────────
        // slope_type: 1=downhill, 2=snowpark, 3=boardercross, 4=crosscountry, 5=luge
        $midwest_slopes = [
            // Sector 7 — Summit runs (steep by midwest standards)
            [
                'id_sector'      => 7,
                'name_english'   => 'The Headwall',
                'name_french'    => 'Le Mur',
                'slope_type'     => 1,
                'start_location' => 201,
                'end_location'   => 206,
                'length'         => 480,
                'reputation'     => 25,
                'path'           => '[480,425],[477,390],[474,355],[476,310],[479,275],[480,240],[480,200],[479,170],[480,142],[481,110],[483,92]',
            ],
            [
                'id_sector'      => 7,
                'name_english'   => 'Backside Burn',
                'name_french'    => 'La Brûlure',
                'slope_type'     => 1,
                'start_location' => 202,
                'end_location'   => 208,
                'length'         => 420,
                'reputation'     => 22,
                'path'           => '[678,415],[672,380],[665,345],[660,308],[656,272],[652,237],[650,200],[648,168],[645,135],[642,110],[640,92]',
            ],
            // Sector 8 — Main intermediate runs
            [
                'id_sector'      => 8,
                'name_english'   => 'Prairie Run',
                'name_french'    => 'La Piste des Prairies',
                'slope_type'     => 1,
                'start_location' => 203,
                'end_location'   => 206,
                'length'         => 600,
                'reputation'     => 18,
                'path'           => '[476,280],[474,250],[472,220],[473,190],[474,165],[475,140],[476,115],[476,92]',
            ],
            [
                'id_sector'      => 8,
                'name_english'   => 'Midwest Express',
                'name_french'    => 'L\'Express du Midwest',
                'slope_type'     => 1,
                'start_location' => 204,
                'end_location'   => 207,
                'length'         => 590,
                'reputation'     => 16,
                'path'           => '[378,285],[375,255],[372,225],[372,195],[373,168],[374,142],[374,118],[373,95]',
            ],
            [
                'id_sector'      => 8,
                'name_english'   => 'Blue Cruiser',
                'name_french'    => 'La Croisière Bleue',
                'slope_type'     => 1,
                'start_location' => 205,
                'end_location'   => 208,
                'length'         => 570,
                'reputation'     => 16,
                'path'           => '[540,275],[538,245],[536,215],[536,185],[537,158],[538,132],[538,108],[537,90]',
            ],
            // Sector 9 — Terrain park
            [
                'id_sector'      => 9,
                'name_english'   => 'The Park',
                'name_french'    => 'Le Parc',
                'slope_type'     => 2, // snowpark (polygon)
                'start_location' => 209,
                'end_location'   => 211,
                'length'         => 380,
                'reputation'     => 20,
                // Polygon outlining park corridor
                'path'           => '[728,250],[790,250],[790,98],[728,98],[728,250]',
            ],
            [
                'id_sector'      => 9,
                'name_english'   => 'Boardercross',
                'name_french'    => 'Planche Cross',
                'slope_type'     => 3,
                'start_location' => 210,
                'end_location'   => 212,
                'length'         => 400,
                'reputation'     => 18,
                'path'           => '[843,225],[843,200],[845,172],[848,148],[850,122],[852,100],[843,92]',
            ],
            // Sector 10 — Beginner area
            [
                'id_sector'      => 10,
                'name_english'   => 'Easy Rider',
                'name_french'    => 'La Piste Facile',
                'slope_type'     => 1,
                'start_location' => 213,
                'end_location'   => 215,
                'length'         => 410,
                'reputation'     => 8,
                'path'           => '[200,195],[199,175],[197,155],[196,135],[196,115],[197,95],[198,75]',
            ],
            [
                'id_sector'      => 10,
                'name_english'   => 'Magic Carpet Run',
                'name_french'    => 'La Piste du Tapis',
                'slope_type'     => 1,
                'start_location' => 214,
                'end_location'   => 216,
                'length'         => 370,
                'reputation'     => 5,
                'path'           => '[162,192],[161,172],[160,152],[159,132],[159,112],[159,92],[160,72]',
            ],
            // Sector 11 — Nordic / cross-country loop
            [
                'id_sector'      => 11,
                'name_english'   => 'Prairie Loop',
                'name_french'    => 'La Boucle des Prairies',
                'slope_type'     => 4, // cross country
                'start_location' => 217,
                'end_location'   => 218,
                'length'         => 3200,
                'reputation'     => 12,
                'path'           => '[100,80],[200,75],[350,68],[500,65],[650,68],[800,74],[900,78],[920,60],[800,50],[600,45],[400,45],[200,50],[80,60],[100,80]',
            ],
        ];

        foreach ($midwest_slopes as $slope) {
            $exists = $this->db
                ->where('id_sector', $slope['id_sector'])
                ->where('name_english', $slope['name_english'])
                ->count_all_results('game_slopes');
            if ($exists === 0) {
                $this->db->insert('game_slopes', $slope);
            }
        }

        // ── 7. Midwest unlock achievements (one per sector) ──────────────────
        $midwest_achievements = [
            ['sector' => '7', 'name_en' => 'The Summit Awaits',   'name_fr' => 'Le Sommet Attend',      'desc_en' => 'Unlock Sector 7 — The Summit.',           'desc_fr' => 'Débloquez le secteur 7 — Le Sommet.'],
            ['sector' => '8', 'name_en' => 'Main Street Skier',   'name_fr' => 'Le Skieur de la Piste', 'desc_en' => 'Unlock Sector 8 — The Main Slope.',        'desc_fr' => 'Débloquez le secteur 8 — La Piste Principale.'],
            ['sector' => '9', 'name_en' => 'Park Rat',            'name_fr' => 'Le Rat du Parc',        'desc_en' => 'Unlock Sector 9 — The Terrain Park.',      'desc_fr' => 'Débloquez le secteur 9 — Le Parc.'],
            ['sector' => '10','name_en' => 'First Tracks',        'name_fr' => 'Premières Traces',      'desc_en' => 'Unlock Sector 10 — The Beginner Area.',    'desc_fr' => 'Débloquez le secteur 10 — Zone Débutants.'],
            ['sector' => '11','name_en' => 'Nordic Spirit',       'name_fr' => 'Esprit Nordique',       'desc_en' => 'Unlock Sector 11 — The Nordic Trails.',    'desc_fr' => 'Débloquez le secteur 11 — Pistes Nordiques.'],
        ];

        foreach ($midwest_achievements as $ach) {
            $exists = $this->db
                ->like('requires', '"sector":"' . $ach['sector'] . '"', 'both')
                ->like('requires', '"action":"unlock_sector"', 'both')
                ->count_all_results('achievements');
            if ($exists === 0) {
                $this->db->insert('achievements', [
                    'name_english'        => $ach['name_en'],
                    'name_french'         => $ach['name_fr'],
                    'description_english' => $ach['desc_en'],
                    'description_french'  => $ach['desc_fr'],
                    'requires'            => json_encode(['action' => 'unlock_sector', 'sector' => $ach['sector'], 'achievement_list' => '']),
                    'reward_reputation'   => 0,
                    'reward_cash'         => 0,
                    'reward_genepis'      => 0,
                    'unlocked_count'      => 0,
                    'image_url'           => '',
                    'display_on_page'     => 0,
                ]);
            }
        }
    }

    public function down() {
        // Remove midwest sectors
        $this->db->where_in('id_sector', [7, 8, 9, 10, 11])->delete('game_sectors');
        // Remove midwest locations
        $this->db->where_in('id_location', range(201, 218))->delete('game_locations');
        // Remove midwest slopes by sector
        $this->db->where_in('id_sector', [7, 8, 9, 10, 11])->delete('game_slopes');
        // Remove midwest achievements
        foreach (['7','8','9','10','11'] as $s) {
            $this->db
                ->like('requires', '"sector":"' . $s . '"', 'both')
                ->like('requires', '"action":"unlock_sector"', 'both')
                ->delete('achievements');
        }
        // Remove columns and table
        if ($this->db->field_exists('id_map_type', 'game_sectors'))  $this->dbforge->drop_column('game_sectors',  'id_map_type');
        if ($this->db->field_exists('id_map_type', 'game_resorts'))  $this->dbforge->drop_column('game_resorts',  'id_map_type');
        if ($this->db->table_exists('game_map_types'))               $this->dbforge->drop_table('game_map_types');
    }
}
