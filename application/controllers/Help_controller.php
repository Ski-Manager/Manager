<?php
class Help_controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('help', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('login_form', $siteLang);
    }

    public function index() {
        $data['title']     = $this->lang->line('help')['title'];
        $data['introHelp'] = $this->lang->line('help')['intro'];
        $data['main_content'] = 'help';
        $this->load->view('templates/default', $data);
    }
}
