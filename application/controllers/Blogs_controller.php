<?php

class Blogs_controller extends CI_Controller {

    const POSTS_PER_PAGE = 6;

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('site_lang')) {
            $siteLang = $this->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $this->lang->load('login_form', $siteLang);
        $this->lang->load('navbar', $siteLang);
        $this->lang->load('home', $siteLang);
        $this->load->model('Blogs_model');
    }

    public function index() {
        $per_page = self::POSTS_PER_PAGE;
        $current_page = max(1, (int)($this->input->get('page') ?? 1));
        $offset = ($current_page - 1) * $per_page;

        // Search support
        $search_query = trim((string)($this->input->get('q') ?? ''));
        $data['search_query'] = $search_query;

        if ($search_query !== '') {
            $total       = $this->Blogs_model->count_search_posts($search_query);
            $total_pages = (int)ceil($total / $per_page);
            $data['posts'] = $this->Blogs_model->search_posts($search_query, $per_page, $offset);
        } else {
            $total       = $this->Blogs_model->count_posts();
            $total_pages = (int)ceil($total / $per_page);
            $data['posts'] = $this->Blogs_model->get_posts($per_page, $offset);
        }

        $data['current_page'] = $current_page;
        $data['total_pages']  = $total_pages;
        $data['main_content'] = 'blogs';
        $this->load->view('templates/default', $data);
    }

}
