<?php

class Maintenance_model extends CI_Model{
    
    public function add_email_opening_DB($data){                      
        $this->db->trans_start(); 
        $insert = $this->db->insert('game_email_opening', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $insert;
        }           
    }  
    
     
    
    
    
}

?>