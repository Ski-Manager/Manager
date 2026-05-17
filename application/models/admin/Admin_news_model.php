<?php

class Admin_news_model extends CI_Model{
    
    /**
     * get_news_Data      Gets all news
     * 
     * @return type     Returns query's result
     */
    public function get_news_Data($id_news = NULL){
        $this->db->select('*');
        $this->db->from('game_news');
        if ($id_news != NULL ) {
            $this->db->where('id_news', $id_news);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_news_Data_id($id_news){
        $this->db->select('*');
        $this->db->from('game_news');
        $this->db->where('id_news', $id_news);
        $query = $this->db->get();
        return $query;
    }
    
    public function edit_news_admin ($id_news, $title_english, $title_french, $content_english, $content_french, $active, $original_id_news, $table) {
        $this->db->trans_start();                                          
        $this->db->set('id_news', $id_news);                                         
        $this->db->set('title_english', $title_english);                                             
        $this->db->set('title_french', $title_french);                                             
        $this->db->set('content_english', $content_english);                                             
        $this->db->set('content_french', $content_french);                                      
        $this->db->set('active', $active);                             
        $this->db->where('id_news', $original_id_news);                       
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