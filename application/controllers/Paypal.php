<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Controller{
    
     function  __construct(){
        parent::__construct();
        
        // Load paypal library & product model
        $this->load->library('paypal_lib');
        $this->load->model('product');
        $this->load->model('logs_model');
        $this->Log_filename = gmdate('Y-m-d H-i-s', time())."";
        
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('genepis',$siteLang);
        
     }
     
    function success(){
        // Get the transaction data
        $paypalInfo = $this->input->get();
        //var_dump($paypalInfo);
        $data['item_name']      = $paypalInfo['item_name'];
        $data['item_number']    = $paypalInfo['item_number'];
        $data['txn_id']         = $paypalInfo["tx"];
        $data['payment_amt']    = $paypalInfo["amt"];
        $data['currency_code']  = $paypalInfo["cc"];
        $data['status']         = $paypalInfo["st"];
        
        $id_player = $this->users_model->get_user_id();  
        $id_resort = $this->users_model->get_resort_id($id_player);  
        $amount_query = $this->product->get_amount_genepis_from_item_number($data['item_number']);  
        $amount_genepis = $amount_query['amount_genepis'];
        $amount_cash = $amount_query['amount_cash'];
        
        $data['amount_genepis']         = $amount_genepis;
        $data['amount_cash']         = $amount_cash;

        $grant_query1 = $this->grant_genepis($id_player, $amount_genepis);
        $grant_query2 = $this->grant_cash($id_resort, $amount_cash);
        
        if ($grant_query1 == 1 || $grant_query2 == 1) {
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs',$player_preferred_lang);
            
            if ($amount_genepis > 0) {
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['you_have_purchased'].' '.$amount_genepis.' '.$this->lang->line('logs')['genepis']) );   // Add a log row to the game_player_logs table      
                $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['you_have_purchased'].' '.$amount_genepis.' '.$this->lang->line('logs')['genepis']) );   // Add a log row to the game_player_logs table      
                $info_message = "Player ID ".$id_player." ".$this->lang->line('logs')['has_purchased'].' '.$amount_genepis.' '.$this->lang->line('logs')['genepis']." for ".$paypalInfo["amt"].$paypalInfo["cc"]."with transaction ID: ".$paypalInfo["tx"].". Status: ".$paypalInfo["st"].".\n";
                $this->logToFile($this->Log_filename, 'INFO', "[id_player_".$id_player."]", 'genepis_purchase_success', $info_message);
            }
            
            if ($amount_cash > 0) {
                $switch_to_sandbox_mode = $this->switch_to_sandbox_mode($id_player);
                
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['you_have_purchased'].' '.number_format($amount_cash, 0, ',', ' ').'€ ') );   // Add a log row to the game_player_logs table      
                $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['you_have_purchased'].' '.number_format($amount_cash, 0, ',', ' ').'€') );   // Add a log row to the game_player_logs table      
                $info_message = "Player ID ".$id_player." ".$this->lang->line('logs')['has_purchased']." ".number_format($amount_cash, 0, ',', ' ')."€ for ".$paypalInfo["amt"].$paypalInfo["cc"]."with transaction ID: ".$paypalInfo["tx"].". Status: ".$paypalInfo["st"].".\n";
                $this->logToFile($this->Log_filename, 'INFO', "[id_player_".$id_player."]", 'genepis_purchase_success', $info_message);
            }
        }
        
        // Pass the transaction data to view
        $this->load->view('paypal/success', $data);
    }
     
    
    protected function switch_to_sandbox_mode($id_player){
        $this->db->trans_start();
        $this->db->set('sandbox_mode', '1');
        $this->db->where('id_player' , $id_player);                      
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    protected function grant_genepis($id_player, $amount_genepis){
        $this->db->trans_start();
        $this->db->set('genepis', 'genepis + '.$amount_genepis, FALSE);
        $this->db->where('id_player' , $id_player);                      
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    protected function grant_cash($id_resort, $amount_cash){
        $this->db->trans_start();
        $this->db->set('cash', 'cash + '.$amount_cash, FALSE);
        $this->db->where('id_resort' , $id_resort);                      
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    
     function cancel(){
        // Load payment failed view
        $this->load->view('paypal/cancel');
     }
     
     function ipn(){
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
        
        if(!empty($paypalInfo)){
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

            // Check whether the transaction is valid
            if($ipnCheck){
                // Insert the transaction data in the database
                $data['user_id']        = $paypalInfo["custom"];
                $data['product_id']        = $paypalInfo["item_number"];
                $data['txn_id']            = $paypalInfo["txn_id"];
                $data['payment_gross']    = $paypalInfo["mc_gross"];
                $data['currency_code']    = $paypalInfo["mc_currency"];
                $data['payer_email']    = $paypalInfo["payer_email"];
                $data['payment_status'] = $paypalInfo["payment_status"];

                $this->product->insertTransaction($data);
            }
        }
    }
    
    function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        write_file(FCPATH . '/application/controllers/logs/Paypal_'.$log_filename.'.log', $data_formatted, "a+");
    }
}