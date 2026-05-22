<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Daily_bonus_controller
 *
 * Shows the player's current login streak and daily bonus tiers.
 * The player must press the "Claim Daily Bonus" button on this page each day
 * to claim their reward and advance their streak.
 */
class Daily_bonus_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',        $siteLang);
        $ci->lang->load('login_form',  $siteLang);
        $ci->lang->load('navbar',      $siteLang);
        $ci->lang->load('daily_bonus', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('daily_bonus_model');
    }

    /**
     * index  Displays the daily login bonus page.
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        $currentUserID = $this->users_model->get_user_id();
        $info          = $this->daily_bonus_model->get_streak_info($currentUserID);
        $tiers         = $this->daily_bonus_model->get_bonus_tiers();

        $today            = gmdate('Y-m-d');
        $current_streak   = $info ? (int)$info->login_streak    : 0;
        $last_bonus_date  = $info ? $info->last_bonus_date       : NULL;
        $claimed_today    = ($last_bonus_date === $today);

        $data['current_streak']     = $current_streak;
        $data['claimed_today']      = $claimed_today;
        $data['last_bonus_date']    = $last_bonus_date;
        $data['bonus_tiers']        = $tiers;
        $data['next_bonus']         = $this->daily_bonus_model->calculate_bonus(
            $claimed_today ? ($current_streak + 1) : max(1, $current_streak)
        );
        $data['claim_success_cash']   = $this->session->flashdata('claim_success_cash');
        $data['claim_success_streak'] = $this->session->flashdata('claim_success_streak');
        $data['main_content']         = 'daily_bonus';

        $this->load->view('templates/default', $data);
    }

    /**
     * claim  POST handler: the player presses the button to claim today's daily bonus.
     */
    public function claim() {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('daily_bonus_controller');
            return;
        }

        $currentUserID = $this->users_model->get_user_id();
        if (!$currentUserID) {
            redirect('daily_bonus_controller');
            return;
        }

        $result = $this->daily_bonus_model->check_and_claim((int)$currentUserID);

        if ($result['claimed']) {
            $this->session->set_flashdata('claim_success_cash',   $result['cash']);
            $this->session->set_flashdata('claim_success_streak', $result['streak']);
        }

        redirect('daily_bonus_controller');
    }
}
