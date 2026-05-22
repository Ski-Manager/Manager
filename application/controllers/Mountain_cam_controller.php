<?php
/**
 * Mountain_cam_controller
 *
 * Manages the Mountain Cams (Webcam) feature:
 *   - Enable / disable live mountain webcams
 *   - Set number of cameras (1–10)
 *   - Set camera quality level (1=Standard, 2=HD, 3=4K)
 *   - Toggle live-stream mode (still images vs. live video stream)
 *   - Toggle social media sharing (auto-share snapshots for extra reputation)
 *   - Toggle night vision mode (infrared cameras for evening/night feeds)
 *   - Toggle weather overlay (live weather data overlaid on feeds)
 */
class Mountain_cam_controller extends CI_Controller {

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
        $ci->lang->load('logs',       $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('logs_model');
        $this->load->model('mountain_cam_model');
    }

    /**
     * index    Mountain cams management page.
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash && empty($data['infoMessage'])) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'mountain_cam';

        $settings = $this->mountain_cam_model->get_settings_DB($currentResortID);

        $data['is_enabled']   = (int)$settings->is_enabled;
        $data['num_cams']     = (int)$settings->num_cams;
        $data['cam_quality']  = (int)$settings->cam_quality;
        $data['stream_mode']  = (int)$settings->stream_mode;
        $data['social_media'] = (int)$settings->social_media;
        $data['night_vision']    = (int)$settings->night_vision;
        $data['weather_overlay'] = (int)$settings->weather_overlay;

        // Resort open/closed: check tourist_info building status (id_status=1 means open)
        $tourist_data = $this->building_model->get_building_data_for_player($currentResortID, 'tourist_info', '1');
        $data['resort_is_open'] = ($tourist_data->num_rows() > 0 && $tourist_data->row()->id_status == '1') ? 1 : 0;

        $data['min_cams']     = MOUNTAIN_CAM_MIN_CAMS;
        $data['max_cams']     = MOUNTAIN_CAM_MAX_CAMS;
        $data['valid_qualities'] = MOUNTAIN_CAM_VALID_QUALITIES;

        // Compute effective daily cost: base + (num_cams - 1) * per_cam, scaled by quality,
        // stream mode, social media, night vision, and weather overlay
        $quality_cost        = MOUNTAIN_CAM_QUALITY_COST_MULT[(int)$settings->cam_quality] ?? 1.0;
        $stream_mult         = ($data['stream_mode']      == 1) ? MOUNTAIN_CAM_STREAM_COST_MULT            : 1.0;
        $social_cost         = ($data['social_media']     == 1) ? MOUNTAIN_CAM_SOCIAL_COST_PER_DAY         : 0;
        $night_vision_cost   = ($data['night_vision']     == 1) ? MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY   : 0;
        $weather_overlay_cost = ($data['weather_overlay'] == 1) ? MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY : 0;
        $data['actual_daily_cost'] = round(
            (MOUNTAIN_CAM_DAILY_COST_BASE + max(0, (int)$settings->num_cams - 1) * MOUNTAIN_CAM_DAILY_COST_PER_CAM)
            * $quality_cost * $stream_mult
            + $social_cost + $night_vision_cost + $weather_overlay_cost
        );

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves mountain-cam settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('mountain_cam_form')) {
            redirect('mountain_cam_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $is_enabled  = ($this->input->post('is_enabled',  TRUE) == '1') ? 1 : 0;
        $num_cams    = (int)$this->input->post('num_cams',    TRUE);
        $cam_quality = (int)$this->input->post('cam_quality', TRUE);
        $stream_mode  = ($this->input->post('stream_mode',      TRUE) == '1') ? 1 : 0;
        $social_media = ($this->input->post('social_media',     TRUE) == '1') ? 1 : 0;
        $night_vision    = ($this->input->post('night_vision',    TRUE) == '1') ? 1 : 0;
        $weather_overlay = ($this->input->post('weather_overlay', TRUE) == '1') ? 1 : 0;

        if ($num_cams < MOUNTAIN_CAM_MIN_CAMS || $num_cams > MOUNTAIN_CAM_MAX_CAMS
            || !in_array($cam_quality, MOUNTAIN_CAM_VALID_QUALITIES, TRUE)) {
            $this->session->set_flashdata('infoMessage', 'mountain_cam_invalid_settings');
            redirect('mountain_cam_controller');
            return;
        }

        $saved = $this->mountain_cam_model->save_settings_DB(
            $currentResortID,
            $is_enabled,
            $num_cams,
            $cam_quality,
            $stream_mode,
            $social_media,
            $night_vision,
            $weather_overlay
        );

        if ($saved) {
            $log_msg = $this->lang->line('logs')['mountain_cam_saved'];
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $log_msg,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $log_msg,
            ]);
        }

        $this->session->set_flashdata('infoMessage', $saved ? 'mountain_cam_settings_saved' : 'mountain_cam_save_error');
        redirect('mountain_cam_controller');
    }
}
