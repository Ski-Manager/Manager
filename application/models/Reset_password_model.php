<?php

class Reset_password_model extends CI_Model{
    
    
    public function get_username_or_email_reset_password($posted_value, $posted_field, $column_to_check){
        $query = $this->db
            ->select($column_to_check.', '.$posted_field)
            ->from('game_players')
            ->where($posted_field, $posted_value)
            ->get();
        if ($query->num_rows() > 0)
            return $query;   
        else
            return FALSE;
    }
    
    public function insert_reset_code_DB($table, $reset_data){        
        if ($insert = $this->db->insert($table, $reset_data)) {                      
            return true;
        }
        else
            return false;
    }
    
    public function validate_email_reset_password($table, $email_address, $email_code, $column) {
        $this->db->select('*');
        $this->db->where('email', $email_address);  
        $this->db->where($column, $email_code); 
        $this->db->limit(1); 
        $this->db->from($table);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query;   
        else
            return FALSE;
    }
    
    
    public function update_password($email_address, $password) {
        $this->db->set('password', $password);
        $this->db->where('email' , $email_address);                              
        $this->db->update('game_players');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function confirm_reset_code_DB($table, $email, $email_code, $column) {
        $this->db->set('confirmed', '1');
        $this->db->set('confirmed_timestamp', gmdate("Y-m-d H:i:s", time()));                     
        $this->db->where($column , $email_code);                              
        $this->db->where('email' , $email);
        $this->db->update($table);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
}

?>