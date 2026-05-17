<?php

class Admin_chat_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // HTTP Basic Auth for admin area
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } elseif ($_SERVER['PHP_AUTH_USER'] == ADMIN_USERNAME && $_SERVER['PHP_AUTH_PW'] == ADMIN_PASSWORD) {
            // valid
        } else {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }

        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('admin_pages', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $this->load->model('admin/admin_chat_model');
        $this->load->model('users_model');
        $this->admin_chat_model->ensure_table_exists();

        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)
            redirect('home_controller');
    }

    /**
     * index    Show the admin chat interface (sent messages + compose form)
     */
    public function index($offset = 0) {
        $per_page = 25;

        $this->load->library('pagination');
        $total = $this->admin_chat_model->get_message_count();

        $config['base_url']   = base_url('admin/admin_chat_controller/index');
        $config['total_rows'] = $total;
        $config['per_page']   = $per_page;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);

        $data['messages']   = $this->admin_chat_model->get_all_messages($per_page, $offset);
        $data['pagination'] = $this->pagination->create_links();
        $data['main_content'] = 'admin/adminChatView';
        $this->load->view('templates/default_admin', $data);
    }

    /**
     * send_message     POST handler: admin sends a message to a specific username
     */
    public function send_message() {
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

        $sender = $this->session->userdata('login_username');
        $result = $this->admin_chat_model->send_message($sender, $recipient, $message);

        if ($result) {
            echo json_encode(['returned' => true]);
        } else {
            echo json_encode(['returned' => false, 'error' => $this->lang->line('admin_page')['chat_error_send']]);
        }
    }

    /**
     * delete_message   POST handler: admin deletes a message
     */
    public function delete_message() {
        $id_message = (int)$this->input->post('id_message', TRUE);
        if ($id_message <= 0) {
            echo json_encode(['returned' => false]);
            return;
        }
        $result = $this->admin_chat_model->delete_message($id_message);
        echo json_encode(['returned' => (bool)$result]);
    }
}
