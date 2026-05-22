<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulk_email_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // HTTP Basic Auth (same pattern as all other admin controllers)
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == ADMIN_USERNAME && $_SERVER['PHP_AUTH_PW'] == ADMIN_PASSWORD) {
            // authenticated
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
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);

        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)
            redirect('home_controller');

        $this->load->library('email');
        $this->load->config('brevo');
    }

    public function index() {
        $data['main_content'] = 'admin/bulk_email_form';
        $this->load->view('templates/default_admin', $data);
    }

    public function send_bulk_email() {
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);

        $users = $this->users_model->get_all_users();

        $failed = 0;
        foreach ($users as $user) {
            $this->email->clear();
            $this->email->from('noreply@ski-manager.net', 'Ski-Manager');
            $this->email->to($user->email);
            $this->email->subject($subject);
            $this->email->message('Hello ' . $user->username . ', ' . $message);
            if (!$this->email->send()) {
                $failed++;
            }

            // Respect rate limit
            usleep(1000000 / 14); // 14 emails/sec max
        }

        if ($failed > 0) {
            $this->session->set_flashdata('error_message', 'Bulk email completed with ' . $failed . ' failure(s).');
        } else {
            $this->session->set_flashdata('success_message', 'Bulk email sent successfully.');
        }
        redirect('admin/bulk_email_controller');
    }

    /**
     * get_users_count  AJAX endpoint – returns total number of registered users as JSON.
     * Called by the bulk-email form before starting chunk processing.
     */
    public function get_users_count() {
        $count = $this->users_model->get_users_count();
        $this->output->set_content_type('application/json');
        echo json_encode(['total' => $count]);
    }

    /**
     * send_bulk_email_chunk  AJAX endpoint – sends emails to a small batch of users.
     *
     * POST params:
     *   subject  string
     *   message  string
     *   offset   int   (0-based row offset)
     *   limit    int   (batch size, capped at 50)
     *
     * Returns JSON: { sent: N, failed: N, done: bool }
     */
    public function send_bulk_email_chunk() {
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);
        $offset  = (int) $this->input->post('offset');
        $limit   = (int) $this->input->post('limit');

        $this->output->set_content_type('application/json');

        if (empty($subject) || empty($message)) {
            echo json_encode(['error' => 'Subject and message are required.', 'sent' => 0, 'failed' => 0, 'done' => true]);
            return;
        }

        if ($limit <= 0 || $limit > 50) {
            $limit = 20;
        }

        $users  = $this->users_model->get_users_paginated($limit, $offset);
        $sent   = 0;
        $failed = 0;

        if ($users) {
            foreach ($users as $user) {
                $this->email->clear();
                $this->email->from('noreply@ski-manager.net', 'Ski-Manager');
                $this->email->to($user->email);
                $this->email->subject($subject);
                $this->email->message('Hello ' . $user->username . ', ' . $message);
                if ($this->email->send()) {
                    $sent++;
                } else {
                    $failed++;
                }

                // Respect Brevo rate limit (14 emails/sec max)
                usleep(1000000 / 14);
            }
        }

        $done = ($users === FALSE || count($users) < $limit);
        echo json_encode(['sent' => $sent, 'failed' => $failed, 'done' => $done]);
    }
}
