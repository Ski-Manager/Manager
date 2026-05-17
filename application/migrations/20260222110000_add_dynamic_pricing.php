<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add dynamic pricing columns to game_resorts table
 *
 * Adds vip_pass_price and family_discount_pct to support:
 *  - VIP passes (premium daily ticket at a higher price)
 *  - Family discounts (percentage discount applied to a share of daily visitors,
 *    boosting demand while reducing per-visitor daily revenue)
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `vip_pass_price`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 *     ADD COLUMN `family_discount_pct` TINYINT  UNSIGNED NOT NULL DEFAULT 0;
 */
class Migration_Add_dynamic_pricing extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'vip_pass_price' => [
                'type'       => 'SMALLINT',
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'skipass_weekly',
            ],
            'family_discount_pct' => [
                'type'       => 'TINYINT',
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'vip_pass_price',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'vip_pass_price');
        $this->dbforge->drop_column('game_resorts', 'family_discount_pct');
    }
}
