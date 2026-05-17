<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ski_quiz_controller
 *
 * Provides a secret-code-protected interactive ski resort trivia quiz where
 * players answer multiple-choice questions about skiing and ski resort
 * management to test their knowledge.
 *
 * Access is gated by the SKI_QUIZ_SECRET_CODE constant defined in
 * application/config/config.php.
 */
class Ski_quiz_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }

        $ci->lang->load('home',       $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',     $siteLang);
        $ci->lang->load('building',   $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
    }

    // -------------------------------------------------------------------------
    // index – secret-code gate then the quiz
    // -------------------------------------------------------------------------

    /**
     * index   Shows the secret-code entry form or, if already unlocked for
     *         this session, the full ski resort trivia quiz.
     */
    public function index() {
        $data = [];
        $data['unlocked']     = (bool)$this->session->userdata('sqz_unlocked');
        $data['main_content'] = 'ski_quiz';

        $currentUserID         = $this->users_model->get_user_id();
        $data['currentUserID'] = $currentUserID;

        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // unlock – POST handler for the secret-code form
    // -------------------------------------------------------------------------

    /**
     * unlock   Validates the submitted secret code and, on success, stores a
     *          session flag so the quiz page becomes accessible.
     */
    public function unlock() {
        $submitted_code = trim($this->input->post('secret_code', TRUE));

        if ($submitted_code !== '' && $submitted_code === SKI_QUIZ_SECRET_CODE) {
            $this->session->set_userdata('sqz_unlocked', true);
            redirect('ski_quiz_controller');
        } else {
            $this->session->set_flashdata('sqz_error', true);
            redirect('ski_quiz_controller');
        }
    }
}
