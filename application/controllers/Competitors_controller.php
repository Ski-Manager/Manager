<?php
/**
 * Competitors_controller
 *
 * Allows players to view nearby AI-controlled competitor resorts, run
 * counter-marketing campaigns and invest in mega lifts to reduce the
 * competitive pressure on their own resort.
 */
class Competitors_controller extends CI_Controller {

    private $siteLang;

    /** Cost constants */
    const COST_COUNTER_MARKETING = 5000;
    const COST_MEGA_LIFT         = 20000;

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
        $ci->lang->load('competitors', $siteLang);
        $ci->lang->load('logs',        $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('competitors_model');
    }

    /**
     * index  Main page – lists competitor resorts and available actions.
     */
    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // Lazily assign competitors to this resort on first visit
        $this->competitors_model->assign_competitors_to_resort($currentResortID);

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $name_lang = 'name_' . $player_preferred_lang;

        $competitors = $this->competitors_model->get_player_competitors($currentResortID, $name_lang);
        $cash        = $this->competitors_model->get_resort_cash($currentResortID);
        $penalty     = $this->competitors_model->get_competitor_penalty($currentResortID);

        $data['currentUserID']     = $currentUserID;
        $data['competitors']       = $competitors;
        $data['cash']              = $cash;
        $data['penalty']           = round($penalty, 1);
        $data['cost_marketing']    = self::COST_COUNTER_MARKETING;
        $data['cost_mega_lift']    = self::COST_MEGA_LIFT;
        $data['action_msg']        = $this->session->flashdata('action_msg');
        $data['action_class']      = $this->session->flashdata('action_class') ?: 'success';
        $data['main_content']      = 'competitors';
        $this->load->view('templates/default', $data);
    }

    /**
     * counter_marketing  Player runs a counter-marketing campaign against a
     *                    specific competitor.  Costs COST_COUNTER_MARKETING €.
     *
     * @param int $id_player_competitor
     */
    public function counter_marketing($id_player_competitor) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_player_competitor = (int) $id_player_competitor;

        $competitor_row = $this->competitors_model
            ->get_single_player_competitor($id_player_competitor, $currentResortID);

        if ($competitor_row->num_rows() === 0) {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_not_found']);
            $this->session->set_flashdata('action_class', 'danger');
            redirect('competitors_controller');
        }

        $cash = $this->competitors_model->get_resort_cash($currentResortID);
        if ($cash < self::COST_COUNTER_MARKETING) {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_not_enough_cash']);
            $this->session->set_flashdata('action_class', 'danger');
            redirect('competitors_controller');
        }

        $ok1 = $this->competitors_model->deduct_cash_DB($currentResortID, self::COST_COUNTER_MARKETING);
        $ok2 = $this->competitors_model->counter_marketing_DB($id_player_competitor, $currentResortID);

        if ($ok1 && $ok2) {
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['marketing'],
                'data'      => $this->lang->line('competitors')['log_counter_marketing']
                               . ' ' . number_format(self::COST_COUNTER_MARKETING, 0, '.', ' ') . ' €.',
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['marketing'],
                'data'      => $this->lang->line('competitors')['log_counter_marketing']
                               . ' ' . number_format(self::COST_COUNTER_MARKETING, 0, '.', ' ') . ' €.',
            ]);
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['success_counter_marketing']);
            $this->session->set_flashdata('action_class', 'success');
        } else {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_action_failed']);
            $this->session->set_flashdata('action_class', 'danger');
        }
        redirect('competitors_controller');
    }

    /**
     * invest_mega_lift  Player invests in a mega lift to outcompete a specific
     *                   rival.  Costs COST_MEGA_LIFT €.
     *
     * @param int $id_player_competitor
     */
    public function invest_mega_lift($id_player_competitor) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_player_competitor = (int) $id_player_competitor;

        $competitor_row = $this->competitors_model
            ->get_single_player_competitor($id_player_competitor, $currentResortID);

        if ($competitor_row->num_rows() === 0) {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_not_found']);
            $this->session->set_flashdata('action_class', 'danger');
            redirect('competitors_controller');
        }

        $cash = $this->competitors_model->get_resort_cash($currentResortID);
        if ($cash < self::COST_MEGA_LIFT) {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_not_enough_cash']);
            $this->session->set_flashdata('action_class', 'danger');
            redirect('competitors_controller');
        }

        $ok1 = $this->competitors_model->deduct_cash_DB($currentResortID, self::COST_MEGA_LIFT);
        $ok2 = $this->competitors_model->invest_mega_lift_DB($id_player_competitor, $currentResortID);

        if ($ok1 && $ok2) {
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $this->lang->line('competitors')['log_mega_lift']
                               . ' ' . number_format(self::COST_MEGA_LIFT, 0, '.', ' ') . ' €.',
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $this->lang->line('competitors')['log_mega_lift']
                               . ' ' . number_format(self::COST_MEGA_LIFT, 0, '.', ' ') . ' €.',
            ]);
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['success_mega_lift']);
            $this->session->set_flashdata('action_class', 'success');
        } else {
            $this->session->set_flashdata('action_msg',
                $this->lang->line('competitors')['error_action_failed']);
            $this->session->set_flashdata('action_class', 'danger');
        }
        redirect('competitors_controller');
    }
}
