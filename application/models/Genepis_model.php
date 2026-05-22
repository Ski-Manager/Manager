<?php

class Genepis_model extends CI_Model{
    
    /**
     * insert_invite_DB     Add the invitation in DB to avoid re-sending to same email (spam)
     * 
     * @param type $new_invite_data     Array containing all the data we need to insert
     * @return type
     */
    public function insert_invite_DB($new_invite_data){        
        if ($insert = $this->db->insert('game_invite_sent', $new_invite_data)) {                      // We add the invite in the DB.
            return true;
        }
        else
            return false;
    }
    
    public function check_previous_invite_sent_DB($input_value) {
        $this->db->select('*');
        $this->db->where('email_referral' , $input_value);  
        $this->db->limit(1); 
        $this->db->from('game_invite_sent');
        $query = $this->db->get();
        return $query;
    }
       
}

?>