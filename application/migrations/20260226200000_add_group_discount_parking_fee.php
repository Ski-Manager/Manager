<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add group discount and parking fee columns
 *
 * Adds to game_resorts:
 *   - group_discount_pct : group booking discount percentage (0 = disabled)
 *   - parking_fee        : parking fee per vehicle per day (€)
 */
class Migration_Add_group_discount_parking_fee extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'group_discount_pct' => [
                'type'    => 'TINYINT',
                'unsigned' => TRUE,
                'null'    => FALSE,
                'default' => 0,
                'after'   => 'family_discount_pct',
            ],
            'parking_fee' => [
                'type'    => 'TINYINT',
                'unsigned' => TRUE,
                'null'    => FALSE,
                'default' => 10,
                'after'   => 'group_discount_pct',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'group_discount_pct');
        $this->dbforge->drop_column('game_resorts', 'parking_fee');
    }
}
