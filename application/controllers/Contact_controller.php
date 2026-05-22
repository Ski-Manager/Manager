<?php
class Contact_controller extends CI_Controller {
    
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
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('contact_form_lang',$siteLang);
        $this->load->model('contact_model');
    }
    
    
    public function index(){
        
        $data['captcha'] = $this->get_captcha();
       
        $data['main_content'] = 'contactForm';
        $this->load->view('templates/default',$data); 
    }
    
    public function get_captcha() {
        $capache_config = array(
            'img_path'      => './img/captcha_folder/',
            'img_url'       => base_url('img/captcha_folder/'),
            'img_width' => 210,
            'img_height' => 45,
            'font_size' => 20,
            'font_path' => FCPATH. '/fonts/arial.ttf',
            'expiration' => 7200,
        );
        // Common captcha area if failing or passing
        $captcha_array = array();
        $captcha_array['label'] = $this->lang->line('contact_form')['captcha_label'].'<br>';
        $captcha_array['refresh'] = ' <i class="fa-solid fa-arrows-rotate reload-captcha activate_button"></i>';
        $captcha_array['input'] = ' <input type="text" name="captcha" value="" size="12"/>';
        
        /* Generate the captcha */
        $captcha = create_captcha();
        if ($captcha !== FALSE) {
            // Data to insert in the captcha table in the DB
            $data = array(
                    'captcha_time'  => $captcha['time'],
                    'ip_address'    => $this->input->ip_address(),
                    'word'          => $captcha['word']
            );
            // Insert the data in DB
            $insertCaptchaDB = $this->contact_model->insert_captcha_DB($data);
            // Building catptcha area
            
            $captcha_array['img'] = '<span class="captcha-img">'.$captcha['image'].'</span>';
        }
        else {    // Something went wrong, returning error message
            $captcha_array['img'] = $this->lang->line('contact_form')['captcha_not_created'];
        }
        
        if (isset($_POST['origin']) && $_POST['origin'] == 'javascript') {    // Called from reload button, need to format in JSON
                echo json_encode(array('img' => $captcha_array['img']));
        }
        else { // Not calles via Javascript, return result to PHP function (normal page - not Reload button)
            return $captcha_array;
        }
    }
    
    public function check_contact_form(){
        
        $data['captcha'] = $this->get_captcha();
        
        //set validation rules
        $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('name', $this->lang->line('contact_form')['name'], 'trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('home')['email'], 'trim|required|valid_email|max_length[254]');
        $this->form_validation->set_rules('subject', $this->lang->line('contact_form')['subject'], 'trim|required|max_length[200]|xss_clean');
        $this->form_validation->set_rules('message', $this->lang->line('contact_form')['message'], 'trim|required|max_length[2000]|xss_clean');

        //run validation on form input
        if ($this->form_validation->run() == FALSE){
            //validation fails
            $data['contact_error_name'] = form_error('name');
            $data['contact_error_email'] = form_error('email');
            $data['contact_error_subject'] = form_error('subject');
            $data['contact_error_message'] = form_error('message');
            $data['main_content'] = 'contactForm';
            $this->load->view('templates/default',$data);
        }
        else{   // Validation form OK (excluding captcha)
        // Checking Captcha
        
            // First, delete old captchas
            $expiration = time() - 7200; // Two hour limit
            // Delete captchas older than 2 hours in DB
            $delete_old_captcha_DB = $this->contact_model->delete_old_captcha_DB($expiration);
            
            // Then see if a captcha exists
            $retrieveCaptchaDB = $this->contact_model->retrieve_captcha_DB($_POST['captcha'], $this->input->ip_address(), $expiration);
            $row = $retrieveCaptchaDB->num_rows();

            if ($row == 0){  // No matching captcha
                $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['captcha_missing'].'</div>');
                $data['main_content'] = 'contactForm';
                $this->load->view('templates/default',$data);
            }
            else {  // Captcha OK. Ready to send email

                //get the form data
                $name = $this->input->post('name', TRUE);
                $from_email = $this->input->post('email', TRUE);
                $subject = $this->input->post('subject', TRUE);
                $message = $this->input->post('message', TRUE);

                //set to_email id to which you want to receive mails
                $to_email = CONST_ADMIN_EMAIL;

                // Send via Amazon SES SMTP. The from address must be a verified
                // SES sender; the submitter's address is preserved as Reply-To.
                $sent = send_ses_email(
                    $to_email,
                    CONST_NOREPLY_EMAIL,
                    'Ski-Manager',
                    $subject,
                    $message,
                    $from_email,
                    $name
                );

                if ($sent) {
                    // mail sent
                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('contact_form')['email_sent_success'].'</div>');
                    redirect('contact_controller');
                }
                else {
                    //error
                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed'].'</div>');
                    redirect('contact_controller');
                }
            }
    
        }
    }
    
    }
    
