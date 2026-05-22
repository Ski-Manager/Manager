<?php

class Admin_news_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        // Authentication for admins
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == ADMIN_USERNAME && $_SERVER['PHP_AUTH_PW'] == ADMIN_PASSWORD){
            // nothing to display
        }
        else {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }
        // constructor
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('admin_pages',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('news',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_news_model');
        $this->load->model('admin/admin_slope_model');
        $this->load->model('news_model');
    }
    
    public function index(){
        // Get all information from all the created newss
        $data['data_news'] = $this->admin_news_model->get_news_Data();
        
        // Create the "delete all" button
        $data['delete_button_all'] = '<a href="?action=delete" data-item_type="news" class="delete-dialog-admin-items-all btn-danger">'.$this->lang->line('admin_page')['delete_all'].'</a>';
        
        // Create the delete button
        $data['delete_button'] = '<a href="?action=delete" class="delete-dialog-admin-items btn-danger">'.$this->lang->line('admin_page')['delete'].'</a>';
        $data['main_content'] = 'admin/adminNewsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_news'];
        $data['main_content'] = 'admin/addNewsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function edit_news($id_news){
        $data_news = $this->admin_news_model->get_news_Data_id($id_news);
        $data_news_array = $data_news->row();
        $data['id_news'] = $id_news;
        $data['original_id_news'] = $id_news;
        $data['title_english'] = $data_news_array->title_english;
        $data['title_french'] = $data_news_array->title_french;
        $data['content_english'] = $data_news_array->content_english;
        $data['content_french'] = $data_news_array->content_french;
        $data['active'] = $data_news_array->active;
        $data['page_type'] = 'news';
        $data['main_content'] = 'admin/editNewsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new_news_validation() {
        if (isset ($_POST['add_new_news'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('title_english', $this->lang->line('admin_page')['title_english'], 'trim|required|max_length[50]');
                $this->form_validation->set_rules('title_french', $this->lang->line('admin_page')['title_french'], 'trim|required|max_length[50]');
                $this->form_validation->set_rules('content_english', $this->lang->line('admin_page')['content_english'], 'trim|required|max_length[750]');
                $this->form_validation->set_rules('content_french', $this->lang->line('admin_page')['content_french'], 'trim|required|max_length[750]');
                $this->form_validation->set_rules('active', $this->lang->line('admin_page')['active'], 'trim|required|max_length[1]');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_news_error_title_english'] = form_error('title_english');
                    $data['add_news_error_title_french'] = form_error('title_french');
                    $data['add_news_error_content_english'] = form_error('content_english');
                    $data['add_news_error_content_french'] = form_error('content_french');
                    $data['add_news_error_active'] = form_error('active');
                    $this->session->set_flashdata('msg','');

                    $data['title_english'] = $this->input->post('title_english', TRUE);
                    $data['title_french'] = $this->input->post('title_french', TRUE);
                    $data['content_english'] = $this->input->post('content_english', TRUE);
                    $data['content_french'] = $this->input->post('content_french', TRUE);
                    $data['active'] = $this->input->post('active', TRUE);
                    $data['title'] = $this->lang->line('admin_page')['add_new_news'];
                    $data['page_type'] = 'news';
                    $data['main_content'] = 'admin/addNewsView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $data_insert = array (
                        'title_english' => $this->input->post('title_english', TRUE),
                        'title_french' => $this->input->post('title_french', TRUE),
                        'content_english' => $this->input->post('content_english', TRUE),
                        'content_french' => $this->input->post('content_french', TRUE),
                        'active' => $this->input->post('active', TRUE)
                    );
                    $query = $this->admin_slope_model->add_item_admin($data_insert, 'game_news');       //Add news to game_news table
                    
                    if($query === true){               // If the query succeedded
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['news_added'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_news'];
                        $data['page_type'] = 'news';
                        $data['main_content'] = 'admin/addNewsView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['news_not_added'].'</div>');
                        $data['page_type'] = 'news';
                        $data['main_content'] = 'admin/addNewsView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_news_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['news_not_added_no_post'].'</div>');
            $data['title'] = $this->lang->line('admin_page')['add_new_news'];
            $data['page_type'] = 'news';
            $data['main_content'] = 'admin/addNewsView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    public function edit_news_validation() {
        if (isset ($_POST['edit_news_admin'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_news', $this->lang->line('admin_page')['id_lift'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('title_english', $this->lang->line('admin_page')['title_english'], 'trim|required|max_length[50]');
                $this->form_validation->set_rules('title_french', $this->lang->line('admin_page')['title_french'], 'trim|required|max_length[50]');
                $this->form_validation->set_rules('content_english', $this->lang->line('admin_page')['content_english'], 'trim|required|max_length[750]');
                $this->form_validation->set_rules('content_french', $this->lang->line('admin_page')['content_french'], 'trim|required|max_length[750]');
                $this->form_validation->set_rules('active', $this->lang->line('admin_page')['active'], 'trim|required|max_length[1]');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_news_error_id_news'] = form_error('id_news');
                    $data['add_news_error_title_english'] = form_error('title_english');
                    $data['add_news_error_title_french'] = form_error('title_french');
                    $data['add_news_error_content_english'] = form_error('content_english');
                    $data['add_news_error_content_french'] = form_error('content_french');
                    $data['add_news_error_active'] = form_error('active');
                    $this->session->set_flashdata('msg','');

                    $data['original_id_news'] = $this->input->post('original_id_news', TRUE);
                    $data['id_news'] = $this->input->post('id_news', TRUE);
                    $data['title_english'] = $this->input->post('title_english', TRUE);
                    $data['title_french'] = $this->input->post('title_french', TRUE);
                    $data['content_english'] = $this->input->post('content_english', TRUE);
                    $data['content_french'] = $this->input->post('content_french', TRUE);
                    $data['active'] = $this->input->post('active', TRUE);
                    $data['title'] = $this->lang->line('admin_page')['add_new_news'];
                    $data['page_type'] = 'news';
                    $data['main_content'] = 'admin/addNewsView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $original_id_news = $this->input->post('original_id_news', TRUE);
                    $id_news = $this->input->post('id_news', TRUE);
                    $title_english = $this->input->post('title_english', TRUE);
                    $title_french = $this->input->post('title_french', TRUE);
                    $content_english = $this->input->post('content_english', TRUE);
                    $content_french = $this->input->post('content_french', TRUE);
                    $active = $this->input->post('active', TRUE);
                    
                    $query = $this->admin_news_model->edit_news_admin($id_news, $title_english, $title_french, $content_english, $content_french, $active, $original_id_news, 'game_news');       //Add news to game_news table
                    
                    if($query === true){               // If the query succeedded
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['news_updated'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_news'];
                        $data['page_type'] = 'news';
                        $data['main_content'] = 'admin/addNewsView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['news_not_updated'].'</div>');
                        $data['page_type'] = 'news';
                        $data['main_content'] = 'admin/addNewsView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_news_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['news_not_updated_no_post'].'</div>');
            $data['title'] = $this->lang->line('admin_page')['add_new_news'];
            $data['page_type'] = 'news';
            $data['main_content'] = 'admin/addNewsView';
            $this->load->view('templates/default_admin',$data);
        }
    }    
    
}

?>