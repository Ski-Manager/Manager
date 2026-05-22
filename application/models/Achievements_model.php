<?php

class Achievements_model extends CI_Model{
    
    
    
    /**
     * get_achievements_player      Get all achievements and the status for the user
     * Not used for now. Can be used if we only want to display unlocked achievements
     * 
     * @return type                 Returns the query's results
     */
    public function get_achievements_player($currentUserID, $name_language, $progress, $claimed, $limit){
        $this->db->select('user_achievements_tbl.progress, user_achievements_tbl.claimed, user_achievements_tbl.unlocked_datetime, achievements.id_achievement, achievements.'.$name_language.'');
        $this->db->from('achievements');
        $this->db->join('user_achievements as user_achievements_tbl', 'achievements.id_achievement = user_achievements_tbl.id_achievement', 'inner');
        $this->db->where('user_achievements_tbl.id_player', $currentUserID);
        if ($progress != '')
            $this->db->where('user_achievements_tbl.progress', $progress);
        else if ($progress == '') {
            $this->db->where('user_achievements_tbl.progress!=', 100);
            $this->db->where('user_achievements_tbl.progress!=', 0);    // to avoid showing "access to sector X"
        }
        if ($claimed != '' || $claimed == 0)
            $this->db->where('user_achievements_tbl.claimed', $claimed);
        $this->db->where('achievements.display_on_page', 1);
        $this->db->order_by('id_achievement', 'asc');
        if ((int) $limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * get_all_achievements_data      Get all achievements and the status for the user
     * 
     * @param type $name_language           Name column for the specific language (name_english, name_french...)
     * @param type $description_language    Description column for the specific language (description_english, description_french...)
     * @return type                 Returns the query's results
     */
    public function get_all_achievements_data($name_language, $description_language, $display_on_page_value){
        $this->db->select('id_achievement, '.$name_language.', '.$description_language.', unlocked_count, image_url, reward_reputation, reward_cash, reward_genepis');
        $this->db->where('display_on_page', $display_on_page_value);
        $this->db->from('achievements');
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * get_all_other_achievements_data      Get all achievements and the status for the user
     * 
     * @param type $name_language           Name column for the specific language (name_english, name_french...)
     * @param type $description_language    Description column for the specific language (description_english, description_french...)
     * @return type                 Returns the query's results
     */
    public function get_all_other_achievements_data($currentUserID, $name_language, $description_language, $limit, $ids_to_exclude){
        $this->db->select('id_achievement, '.$name_language.', '.$description_language);
        $this->db->from('achievements');
        $this->db->where_not_in('id_achievement', $ids_to_exclude);
        $this->db->order_by('id_achievement', 'asc');
        if ((int) $limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query;
    }
   
    
    public function get_specific_achievements_data($id_achievement){
        $this->db->select('*');
        $this->db->from('achievements');
        $this->db->where('id_achievement', $id_achievement);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     *  get_achievements_status_player      Get achievements status for the player from the user_achievements table
     * 
     * @param type $id_achievement          ID of the achievement
     * @param type $currentUserID           Current user ID
     * @return type
     */
    public function get_achievements_status_player($id_achievement, $currentUserID){
        $this->db->select('*');
        $this->db->from('user_achievements');
        $this->db->where('id_achievement', $id_achievement);
        $this->db->where('id_player', $currentUserID);
        $query = $this->db->get();
        return $query;
    }
    
    
    public function get_claimed_achievements_player($currentUserID, $achievement_list_array){
        $this->db->select('id_achievement, claimed');
        $this->db->from('user_achievements');
        $this->db->where_in('id_achievement', $achievement_list_array);
        $this->db->where('claimed', '1');
        $this->db->where('id_player', $currentUserID);
        $query = $this->db->get();
        return $query;
    }
   
}

?>
