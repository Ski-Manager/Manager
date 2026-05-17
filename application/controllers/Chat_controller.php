<?php

class Chat_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('admin_pages', $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $this->load->model('admin/admin_chat_model');
        $this->load->model('users_model');
        $this->admin_chat_model->ensure_table_exists();

        // Only logged-in players can view their messages
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');
    }

    /**
     * index    Show all messages sent to the current logged-in player
     */
    public function index() {
        $username = $this->session->userdata('login_username');
        $data['messages'] = $this->admin_chat_model->get_messages_for_user($username);
        $this->admin_chat_model->mark_all_read($username);
        $data['current_username'] = $username;
        $data['is_admin'] = $this->users_model->check_if_admin($username);
        $data['main_content'] = 'chat';
        $this->load->view('templates/default', $data);
    }

    /**
     * reply_to_admin   POST handler: a logged-in player replies to an admin message
     */
    public function reply_to_admin() {
        $username   = $this->session->userdata('login_username');
        $id_message = (int)$this->input->post('id_message', TRUE);
        $message    = trim($this->input->post('message', TRUE));

        if ($id_message <= 0 || $message === '') {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_fields']]);
            return;
        }

        // Verify the original message exists and was sent to this user
        $original = $this->admin_chat_model->get_message_by_id($id_message);
        if (!$original || $original->recipient_username !== $username) {
            echo json_encode(['returned' => false, 'error' => 'Invalid message']);
            return;
        }

        $result = $this->admin_chat_model->reply_to_message($username, $original->sender_username, $message, $id_message);

        if ($result) {
            echo json_encode(['returned' => true]);
        } else {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_send']]);
        }
    }

    /**
     * send_message     POST handler: admin sends a message to a specific username (admin players only)
     */
    public function send_message() {
        $username = $this->session->userdata('login_username');
        if (!$this->users_model->check_if_admin($username)) {
            echo json_encode(['returned' => false, 'error' => 'Forbidden']);
            return;
        }

        $recipient = trim($this->input->post('recipient_username', TRUE));
        $message   = trim($this->input->post('message', TRUE));

        if ($recipient === '' || $message === '') {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_fields']]);
            return;
        }

        if (!$this->admin_chat_model->username_exists($recipient)) {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_user_not_found']]);
            return;
        }

        $result = $this->admin_chat_model->send_message($username, $recipient, $message);

        if ($result) {
            echo json_encode(['returned' => true]);
        } else {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_send']]);
        }
    }
}
