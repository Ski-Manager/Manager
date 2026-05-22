<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_chat_model extends CI_Model {

    /**
     * ensure_table_exists  Creates admin_chat_messages if it does not yet exist.
     *                      Called from the controller constructor so the table is
     *                      always present before any model method runs.
     */
    public function ensure_table_exists() {
        if (!$this->db->table_exists('admin_chat_messages')) {
            $this->db->query('
                CREATE TABLE `admin_chat_messages` (
                    `id_message`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `sender_username`    VARCHAR(25)  NOT NULL,
                    `recipient_username` VARCHAR(25)  NOT NULL,
                    `message`            TEXT         NOT NULL,
                    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `is_read`            TINYINT      NOT NULL DEFAULT 0,
                    `reply_to_id`        INT UNSIGNED NULL DEFAULT NULL,
                    PRIMARY KEY (`id_message`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ');
        } else {
            $fields = $this->db->list_fields('admin_chat_messages');
            if (!in_array('message', $fields)) {
                $this->db->query('ALTER TABLE `admin_chat_messages` ADD COLUMN `message` TEXT NULL');
            }
            if (!in_array('reply_to_id', $fields)) {
                $this->db->query('ALTER TABLE `admin_chat_messages` ADD COLUMN `reply_to_id` INT UNSIGNED NULL DEFAULT NULL');
            }
            if (!in_array('id_message', $fields)) {
                if (in_array('id', $fields)) {
                    $this->db->query('ALTER TABLE `admin_chat_messages` CHANGE `id` `id_message` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
                } else {
                    $this->db->query('ALTER TABLE `admin_chat_messages` DROP PRIMARY KEY');
                    $this->db->query('ALTER TABLE `admin_chat_messages` ADD COLUMN `id_message` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
                }
            }
        }
    }

    /**
     * Send a message from an admin to a specific user
     */
    public function send_message($sender_username, $recipient_username, $message) {
        $data = [
            'sender_username'    => $sender_username,
            'recipient_username' => $recipient_username,
            'message'            => $message,
            'created_at'         => gmdate('Y-m-d H:i:s'),
            'is_read'            => 0,
        ];
        return $this->db->insert('admin_chat_messages', $data);
    }

    /**
     * Get all messages sent (admin view)
     */
    public function get_all_messages($limit = 100, $offset = 0) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('admin_chat_messages')->result();
    }

    /**
     * Get total count of all messages (for pagination)
     */
    public function get_message_count() {
        return $this->db->count_all('admin_chat_messages');
    }

    /**
     * Get messages for a specific recipient (player view) — includes replies sent by the user
     */
    public function get_messages_for_user($recipient_username) {
        $this->db->group_start();
        $this->db->where('recipient_username', $recipient_username);
        $this->db->or_where('sender_username', $recipient_username);
        $this->db->group_end();
        $this->db->order_by('created_at', 'ASC');
        return $this->db->get('admin_chat_messages')->result();
    }

    /**
     * Count unread messages for a specific recipient
     */
    public function count_unread($recipient_username) {
        $this->db->where('recipient_username', $recipient_username);
        $this->db->where('is_read', 0);
        return $this->db->count_all_results('admin_chat_messages');
    }

    /**
     * Mark all messages for a recipient as read
     */
    public function mark_all_read($recipient_username) {
        $this->db->where('recipient_username', $recipient_username);
        $this->db->where('is_read', 0);
        return $this->db->update('admin_chat_messages', ['is_read' => 1]);
    }

    /**
     * Delete a message by ID
     */
    public function delete_message($id_message) {
        $this->db->where('id_message', (int)$id_message);
        return $this->db->delete('admin_chat_messages');
    }

    /**
     * Get a single message by ID
     */
    public function get_message_by_id($id_message) {
        $this->db->where('id_message', (int)$id_message);
        $result = $this->db->get('admin_chat_messages');
        return $result->num_rows() > 0 ? $result->row() : null;
    }

    /**
     * Send a reply from a player back to the admin who sent the original message
     */
    public function reply_to_message($sender_username, $recipient_username, $message, $reply_to_id) {
        $data = [
            'sender_username'    => $sender_username,
            'recipient_username' => $recipient_username,
            'message'            => $message,
            'created_at'         => gmdate('Y-m-d H:i:s'),
            'is_read'            => 0,
            'reply_to_id'        => (int)$reply_to_id,
        ];
        return $this->db->insert('admin_chat_messages', $data);
    }

    /**
     * Check if a username exists in the players table
     */
    public function username_exists($username) {
        $this->db->where('username', $username);
        return $this->db->count_all_results('game_players') > 0;
    }
}
