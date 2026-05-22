<?php

class Tutorial_controller extends CI_Controller {

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
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('tutorial', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }
    }

    public function index() {
        $t = $this->lang->line('tutorial');
        $data['title']   = '<h2>' . $t['title'] . '</h2>';
        $data['tutorial'] = $t;
        $data['main_content'] = 'tutorial';
        $this->load->view('templates/default', $data);
    }
}
