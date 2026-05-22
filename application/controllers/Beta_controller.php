<?php
class Beta_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('maintenance',$siteLang);
        $ci->lang->load('beta',$siteLang);
        $this->load->model('maintenance_model');
    }
    
    public function index(){
         
        $data['title'] = $this->lang->line('beta')['title'];
        $data['introBeta'] = $this->lang->line('beta')['intro'];
        $data['short_lang'] = substr($this->session->userdata('site_lang'), 0, 2);
        
        $data['main_content'] = 'beta';
        $this->load->view('templates/default',$data); 
    }

    /**
     * accept  Shows the beta T&C page after login (if the bypass cookie is not yet set).
     *         Requires the user to be logged in.
     */
    public function accept() {
        if (!$this->session->userdata('is_logged_in')) {
            redirect('home_controller');
            return;
        }

        // Already accepted this session – skip straight to the game
        if (!empty($_COOKIE['beta_access']) && $_COOKIE['beta_access'] === 'beta_access_granted') {
            redirect('resort_controller');
            return;
        }

        $data['main_content'] = 'beta_accept';
        $this->load->view('templates/default', $data);
    }

    /**
     * confirm  Sets the bypass cookie (beta accepted) and redirects to the resort.
     *          Requires the user to be logged in.
     */
    public function confirm() {
        if (!$this->session->userdata('is_logged_in')) {
            redirect('home_controller');
            return;
        }

        $bypass_token       = getenv('MAINTENANCE_BYPASS_TOKEN') ?: 'e4a1b6c3d9f7a2b8c5d1e3f4b6a7c8d9';
        $bypass_cookie_name = 'maintenance_bypass';
        $bypass_lifetime    = 7 * 24 * 60 * 60; // 7 days

        $beta_cookie_name = 'beta_access';
        $beta_lifetime    = 24 * 60 * 60; // 1 day

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                  || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        if (PHP_VERSION_ID >= 70300) {
            setcookie($bypass_cookie_name, $bypass_token, [
                'expires'  => time() + $bypass_lifetime,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'None',
            ]);
            setcookie($beta_cookie_name, 'beta_access_granted', [
                'expires'  => time() + $beta_lifetime,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'None',
            ]);
        } else {
            setcookie($bypass_cookie_name, $bypass_token,
                time() + $bypass_lifetime, '/', '', $secure, true);
            setcookie($beta_cookie_name, 'beta_access_granted',
                time() + $beta_lifetime, '/', '', $secure, true);
        }

        redirect('resort_controller');
    }

    /**
     * ajax_confirm  Sets the bypass cookie via AJAX and returns JSON {"ok": true}.
     *               Requires the user to be logged in.
     */
    public function ajax_confirm() {
        if (!$this->session->userdata('is_logged_in')) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false]));
            return;
        }

        $bypass_token       = getenv('MAINTENANCE_BYPASS_TOKEN') ?: 'e4a1b6c3d9f7a2b8c5d1e3f4b6a7c8d9';
        $bypass_cookie_name = 'maintenance_bypass';
        $bypass_lifetime    = 7 * 24 * 60 * 60; // 7 days

        $beta_cookie_name = 'beta_access';
        $beta_lifetime    = 24 * 60 * 60; // 1 day

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                  || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        if (PHP_VERSION_ID >= 70300) {
            setcookie($bypass_cookie_name, $bypass_token, [
                'expires'  => time() + $bypass_lifetime,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'None',
            ]);
            setcookie($beta_cookie_name, 'beta_access_granted', [
                'expires'  => time() + $beta_lifetime,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'None',
            ]);
        } else {
            setcookie($bypass_cookie_name, $bypass_token,
                time() + $bypass_lifetime, '/', '', $secure, true);
            setcookie($beta_cookie_name, 'beta_access_granted',
                time() + $beta_lifetime, '/', '', $secure, true);
        }

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['ok' => true]));
    }
}