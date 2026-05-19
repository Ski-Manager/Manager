<?php
/**
 * 
 */
class Genepis_controller extends CI_Controller{
    
    private $siteLang;  // To use the siteLang variable globally
    
    /**
     * __construct
     */
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');            // Store current language in variable
        } else {
            $siteLang = 'english';                                      // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        // Loads the different language files
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('genepis',$siteLang);
        $ci->lang->load('email',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('genepis_model');
    }
    

    public function index($action = NULL, $class = NULL){
       
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $data['uid'] = $currentUserID;
        $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
        $player_info_data = $player_info->row();
        $data['current_balance'] = $player_info_data->genepis;
        $data['invite_link'] = $this->get_referral_link($currentUserID);
        $data['action'] = $action;
        $data['class'] = $class;
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $data['comment_language'] = 'comment_'.$player_preferred_lang;
              
        // Displaying the account view
        $data['main_content'] = 'genepis';
        $this->load->view('templates/default',$data);  
    }
   
    function buy($id = NULL){
        show_404();
    }
    
    protected function get_referral_link($currentUserID) {
        $referral_key = $this->users_model->get_referral_key($currentUserID);
        $invite_link = '<a href="'.base_url().'register_controller?key='.$referral_key.'">'.base_url().'register_controller?key='.$referral_key.'</a>';
        return $invite_link;
    }
    
    public function invite_friends(){
               
   
        if (isset ($_POST['invite_friends'])) {             // if we POST something from the invite friend form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('name', $this->lang->line('contact_form')['name'], 'trim|required|min_length[3]|max_length[35]|callback_different_from_input[contact_name]');
            $this->form_validation->set_rules('email', $this->lang->line('home')['email'], 'trim|required|max_length[45]|valid_email');
            $this->form_validation->set_rules('friend1', $this->lang->line('genepis')['friend1_field'], 'trim|required|max_length[45]|valid_email|callback_check_previous_invite_sent');
            $this->form_validation->set_rules('friend2', $this->lang->line('genepis')['friend2_field'], 'trim|max_length[45]|callback_valid_email_or_default[friend2]|callback_check_previous_invite_sent');
            $this->form_validation->set_rules('friend3', $this->lang->line('genepis')['friend3_field'], 'trim|max_length[45]|callback_valid_email_or_default[friend3]|callback_check_previous_invite_sent');
            

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data_array = array(
                    'signup_error_name'        => form_error('name'),
                    'signup_error_email'          => form_error('email'),
                    'signup_error_friend1'       => form_error('friend1'),
                    'signup_error_friend2'       => form_error('friend2'),
                    'signup_error_friend3'       => form_error('friend3')
                );
                echo json_encode(array('valid' => false, 'data' => '', 'errors' => $data_array));
            }
            else {                  // all fields are correct
                // Defining some variables
                $name = trim($this->input->post('name', TRUE));
                $sender_email = trim($this->input->post('email', TRUE));
                $friend1 = trim($this->input->post('friend1', TRUE));
                $friend2 = trim($this->input->post('friend2', TRUE));
                $friend3 = trim($this->input->post('friend3', TRUE));
                $currentUserID = $this->users_model->get_user_id();
                $email_sent1 = '';
                $email_sent2 = '';
                $email_sent3 = '';
                
                // Array containing data to insert in DB for spam protection
                $new_invite_data = array(
                    'id_player'        => $currentUserID,
                    'email_entered_referrer'          => $sender_email,
                    'email_referral'       => $friend1
                );
                
                // Sending emails only if value is different from default (i.e. valid email entered and not already sent)
                if ($friend1 != $this->lang->line('genepis')['friend1_field']) {
                    $email_sent1 = $this->send_invitation_email($name, $sender_email, $friend1);             // We send the email invitation to friend1
                }
                if ($friend2 != $this->lang->line('genepis')['friend2_field']) {
                    $email_sent2 = $this->send_invitation_email($name, $sender_email, $friend2);             // We send the email invitation to friend2
                }
                if ($friend3 != $this->lang->line('genepis')['friend3_field']) {
                    $email_sent3 = $this->send_invitation_email($name, $sender_email, $friend3);             // We send the email invitation to friend3
                }
                if ($email_sent1 === TRUE || $email_sent2 === TRUE || $email_sent3 === TRUE) { // at least one email was sent     
                    $insert_invite_DB = $this->genepis_model->insert_invite_DB($new_invite_data);       // Insert spam protection in DB
                    echo json_encode(array('valid' => true, 'data' => '<div class="mini-alert alert-success text-center">'.$this->lang->line('genepis')['friends_invited'].'</div>'));
                }
                else {
                    echo json_encode(array('valid' => false, 'data' => '<div class="mini-alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed'].'</div>'));
                }    
            }
        }
        else {
            echo json_encode(array('valid' => false, 'data' => '<div class="mini-alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed'].'</div>'));
        }
    }

    
    /**
     * send_invitation_email Send the invitation email to the friend(s)
     * 
     */
    public function send_invitation_email($name, $sender_email, $friend_email) {

        $currentUserID = $this->users_model->get_user_id();
        $invite_link   = $this->get_referral_link($currentUserID);

        // Body: "[name] invited you to play Ski-Manager." + game description (without the trailing link)
        $body = htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . $this->lang->line('genepis')['invitation_body1'];

        // Build styled HTML email body
        $message = build_html_email(
            $this->lang->line('genepis')['dear_friend'],
            $this->lang->line('genepis')['invitation_heading'],
            $body,
            $invite_link,
            $this->lang->line('genepis')['invitation_cta']
        );

        // Send via Brevo REST API so the email appears in Brevo's transactional logs
        return send_brevo_email($friend_email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('genepis')['invitation_subject'], $message);
    }
    
    
    /**
     * different_from_input Checks if the entered value is different from the default one (and rejects the form validation)
     * 
     * @param type $input_value         The value present in the field when validating the form
     * @param type $field_name          The field name
     * @return boolean                  FALSE if the name is the same (not valid). TRUE is name is different from default (valid)
     */
    public function different_from_input($input_value, $field_name){
        
        $field = $field_name.'_field';
        
        if ($input_value == $this->lang->line($field)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    public function valid_email_or_default($input_value, $field_name){ 
        $field = $field_name.'_field';
            // If the email is valid or if the text is default, return true
        if (filter_var($input_value, FILTER_VALIDATE_EMAIL)  || $input_value == $this->lang->line('genepis')[$field]) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    public function check_previous_invite_sent($input_value){ 
        
        $check_previous_invite_sent = $this->genepis_model->check_previous_invite_sent_DB($input_value);
        $num_results = $check_previous_invite_sent->num_rows();
                
        if ($num_results == 0) { 
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    function get_genepis($currentUserID = NULL){
        if ($currentUserID == NULL)
            $currentUserID = $this->users_model->get_user_id();         
        $genepis = $this->users_model->get_user_genepis_amount($currentUserID);   
        echo json_encode(array('genepis' => $genepis));
        
    }
}
