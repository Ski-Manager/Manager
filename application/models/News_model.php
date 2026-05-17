<?php

class News_model extends CI_Model{

   
    public function get_active_news_language($siteLang){
        $this->db->select('*');
        $this->db->from('game_news');
        $this->db->where('active', '1');
        $this->db->order_by('created_date', 'desc');
        $query = $this->db->get();
        return $query;
    }
   
    
}

?>