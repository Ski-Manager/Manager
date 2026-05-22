<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_help_development_popup_status extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_players', [
            'help_development_popup_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_players', 'help_development_popup_status');
    }
}
