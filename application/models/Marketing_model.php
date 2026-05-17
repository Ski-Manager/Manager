<?php

class Marketing_model extends CI_Model{
    
    
    public function get_all_active_campaigns() {
        $this->db->select('*');
        $this->db->from('game_marketing_campaigns');
        $this->db->where('active', '1');
        $query = $this->db->get();
        return $query;
    }
    
    
    public function select_last_ran_campaign_player($currentResortID) {
        $this->db->select('*');
        $this->db->from('game_started_campaigns');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('last_executed', 'DESC');
        $this->db->limit('1');
        $query = $this->db->get();
        return $query;
    }
    
    public function select_ongoing_campaign_player($currentResortID) {
        $this->db->select('*');
        $this->db->from('game_started_campaigns');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('last_executed', 'DESC');
        $query = $this->db->get();
        return $query;
    }
    
    
    public function get_specific_campaign_info($id_campaign) {
        $this->db->select('*');
        $this->db->from('game_marketing_campaigns');
        $this->db->where('id_campaign', $id_campaign);
        $query = $this->db->get();
        return $query;
    }
    
    public function update_campaign_DB($currentResortID, $id_started_campaign){
        $this->db->trans_start();
        $this->db->set('level', 'level + 1', FALSE);
        $this->db->set('last_executed', gmdate('Y-m-d H:i:s'));
        $this->db->where('id_resort' , $currentResortID);
        $this->db->where('id_started_campaign' , $id_started_campaign);                      
        $this->db->update('game_started_campaigns');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function mark_campaign_completed_DB($currentResortID, $id_started_campaign){
        $this->db->trans_start();
        $this->db->set('completed', 1);
        $this->db->where('id_resort' , $currentResortID);
        $this->db->where('id_started_campaign' , $id_started_campaign);                      
        $this->db->update('game_started_campaigns');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    
    public function reward_campaign_to_resort_DB($currentResortID, $reward_cash, $reward_reputation){
        $this->db->trans_start();
        $this->db->set('cash', 'cash+'.$reward_cash,FALSE);
        $this->db->set('reputation', 'reputation+'.$reward_reputation,FALSE);
        $this->db->where('id_resort' , $currentResortID);      
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    public function reward_campaign_to_player_DB($currentUserID, $reward_genepis){
        $this->db->trans_start();
        $this->db->set('genepis', 'genepis+'.$reward_genepis,FALSE);                        
        $this->db->where('id_player' , $currentUserID);                              
        $this->db->update('game_players');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    public function reward_campaign_insert_affluence_bonus_DB($currentResortID, $data){
        $this->db->trans_start();
        $this->db->insert('game_affluence_bonus', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function check_affluence_bonus_exists($currentResortID) {
        $this->db->select('*');
        $this->db->from('game_affluence_bonus');
        $this->db->where('id_resort' , $currentResortID);                              
        $this->db->where('date' , gmdate('Y-m-d'));
        $query = $this->db->get();
        return $query;
    }
    
    public function reward_campaign_update_affluence_bonus_DB($currentResortID, $affluence_bonus){
        $this->db->trans_start();
        $this->db->set('affluence_bonus', 'affluence_bonus+'.$affluence_bonus,FALSE);                        
        $this->db->where('id_resort' , $currentResortID);                              
        $this->db->where('date' , gmdate('Y-m-d'));                              
        $this->db->update('game_affluence_bonus');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function start_campaign_DB($data){
        $insert = $this->db->insert('game_started_campaigns', $data);
        return $insert;
    }
    
    public function get_campaign_history_with_info_DB($currentResortID, $name_language) {
        $allowed_lang_columns = ['name_english', 'name_french'];
        if (!in_array($name_language, $allowed_lang_columns)) {
            $name_language = 'name_english';
        }
        $this->db->select('sc.id_started_campaign, sc.id_campaign, sc.level, sc.last_executed, sc.completed, mc.'.$name_language.' as campaign_name');
        $this->db->from('game_started_campaigns sc');
        $this->db->join('game_marketing_campaigns mc', 'sc.id_campaign = mc.id_campaign', 'left');
        $this->db->where('sc.id_resort', $currentResortID);
        $this->db->order_by('sc.last_executed', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query;
    }

    public function get_campaign_stats_DB($currentResortID) {
        $this->db->select('COUNT(*) as total_published, MAX(level) as max_level');
        $this->db->from('game_started_campaigns');
        $this->db->where('id_resort', $currentResortID);
        $query = $this->db->get();
        return $query;
    }




}

?>