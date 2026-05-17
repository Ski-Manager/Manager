<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add sector 6
 *
 * - Inserts sector 6 into game_sectors so that slopes/lifts can be
 *   assigned to it and it appears on the resort map.
 * - Inserts an achievement into the achievements table that, when
 *   claimed, unlocks sector 6 for the player's resort.
 *
 * The ACTIVE_SECTORS constant in application/config/config.php has
 * been updated from '5' to '6' so that all sector loops and checks
 * include the new sector.
 */
class Migration_Add_sector_6 extends CI_Migration {

    public function up() {
        // ── 1. Add sector 6 row to game_sectors ──────────────────────────────
        // 'path' holds the map polygon boundary coordinates in the format
        // "[lng,lat],[lng,lat],..." used by the Leaflet drawSector() helper.
        // Leave it NULL here; the admin can populate it via the database
        // once the geographic boundaries are determined.
        $sector_exists = $this->db
            ->where('id_sector', 6)
            ->count_all_results('game_sectors');

        if ($sector_exists === 0) {
            $this->db->insert('game_sectors', [
                'id_sector' => 6,
                'color'     => '#A855F7',   // purple-500 – distinguishable on the map
                'path'      => null,
            ]);
        }

        // ── 2. Add unlock-sector-6 achievement ───────────────────────────────
        // 'requires' JSON keys understood by mycustom_helper::call_achievements_check():
        //   action          – must be "unlock_sector"
        //   sector          – the sector number to unlock
        //   achievement_list – comma-separated IDs of prerequisite achievements
        //                     that must all be claimed to trigger this one.
        //                     Set to "" until the admin configures the chain.
        $ach_exists = $this->db
            ->like('requires', '"sector":"6"', 'both')
            ->like('requires', '"action":"unlock_sector"', 'both')
            ->count_all_results('achievements');

        if ($ach_exists === 0) {
            $this->db->insert('achievements', [
                'name_english'        => 'Sector 6 Unlocked',
                'name_french'         => 'Secteur 6 Débloqué',
                'description_english' => 'Complete the required achievements to unlock sector 6.',
                'description_french'  => 'Complétez les succès requis pour débloquer le secteur 6.',
                'requires'            => json_encode(['action' => 'unlock_sector', 'sector' => '6', 'achievement_list' => '']),
                'reward_reputation'   => 0,
                'reward_cash'         => 0,
                'reward_genepis'      => 0,
                'unlocked_count'      => 0,
                'image_url'           => '',
                'display_on_page'     => 0,
            ]);
        }
    }

    public function down() {
        $this->db->where('id_sector', 6)->delete('game_sectors');
        $this->db
            ->like('requires', '"sector":"6"', 'both')
            ->like('requires', '"action":"unlock_sector"', 'both')
            ->delete('achievements');
    }
}
