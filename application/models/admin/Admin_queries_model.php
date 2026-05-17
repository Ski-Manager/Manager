<?php

class Admin_queries_model extends CI_Model{
    
    
    public function run_query_on_db($amount, $column, $table, $to_player){
        $this->db->trans_start();
        $this->db->set($column, $column.' + '.$amount, FALSE);
        if ($to_player != '')
            $this->db->where('id_player' , $to_player);                      
        $this->db->update($table);
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
}

?>