<?php

class Contact_model extends CI_Model{
  
    public function insert_captcha_DB ($data) {
        $this->db->trans_start();
            $query = $this->db->insert('captcha', $data);
            $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return TRUE;
        }
    }
    
    
    
    public function retrieve_captcha_DB($word, $ip_address, $expiration){                    // gets the slope information
        $this->db->trans_start();
        $query = $this->db
            ->select('*')
            ->from('captcha')
            ->where('word', $word)
            ->where('ip_address', $ip_address)
            ->where('captcha_time >', $expiration)
            ->get();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    

    public function delete_old_captcha_DB($expiration){
        $this->db->trans_start();
        $this->db->where('captcha_time < ', $expiration);
        $this->db->delete('captcha');
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