<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_admin_chat extends CI_Migration {

    public function up() {
        if (!$this->db->table_exists('admin_chat_messages')) {
            $this->dbforge->add_field([
                'id_message' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'sender_username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 25,
                ],
                'recipient_username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 25,
                ],
                'message' => [
                    'type' => 'TEXT',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'is_read' => [
                    'type'    => 'TINYINT',
                    'default' => 0,
                ],
            ]);
            $this->dbforge->add_key('id_message', TRUE);
            $this->dbforge->create_table('admin_chat_messages');
        }
    }

    public function down() {
        $this->dbforge->drop_table('admin_chat_messages', TRUE);
    }
}
