<?php

class Admin_player_model extends CI_Model{
    
    /**
     * get_player_Data      Gets all player's info and resort ID for all players (with and without resorts)
     * 
     * @return type     Returns query's result
     */
    public function get_player_Data($limit = 25, $offset = 0){
        $this->db->select('players_tbl.*, game_resorts.id_resort');
        $this->db->from('game_resorts');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'right');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * get_player_count     Gets the total number of players
     *
     * @return int          Total player count
     */
    public function get_player_count(){
        $this->db->select('COUNT(DISTINCT players_tbl.id_player) as count', FALSE);
        $this->db->from('game_resorts');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'right');
        $query = $this->db->get();
        return (int) $query->row()->count;
    }
    
    /**
     * delete_player_db         Deletes a specific player from the database
     * 
     * @param type $id_player   ID of the player to be deleted
     * @return boolean          Returns true or false
     */
    public function delete_player_db($id_player){
        $this->db->trans_start();
        if ($id_player != 'all') {
            $this->db->where('id_player', $id_player);
            $this->db->limit(1); 
        }
        else if ($id_player == 'all') {
            $this->db->not_like('id_player', '1');    // ID 1 is Javo, we keep this player
        }
        $this->db->delete('game_players');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    /**
     * delete_player_facebook_db         Deletes a specific player from the database in the facebook table
     * 
     * @param type $oauth_uid    oauth ID of the player (facebook ID) to be deleted
     * @return boolean          Returns true or false
     */
    public function delete_player_facebook_db($oauth_uid){
        $this->db->trans_start();
        if ($oauth_uid != 'all') {
            $this->db->where('oauth_uid', $oauth_uid);
            $this->db->limit(1); 
        }
        else if ($oauth_uid == 'all') {
            $this->db->not_like('oauth_uid', '10155723409662787');    // ID 10155723409662787 is Javo, we keep this player
        }
        $this->db->delete('game_oauth_users');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    /**
     * delete_player_linked_auth_db         Deletes a specific player from the linking table 
     * 
     * @param type $regular_login_id    ID the player (regular ID) to be deleted
     * @return boolean          Returns true or false
     */
    public function delete_player_linked_auth_db($regular_login_id){
        $this->db->trans_start();
        if ($regular_login_id != 'all') {
            $this->db->where('regular_login_id', $regular_login_id);
            $this->db->limit(1); 
        }
        else if ($regular_login_id == 'all') {
            $this->db->not_like('regular_login_id', '1');    // ID 1 is Javo, we keep this player
        }
        $this->db->delete('game_linked_auth');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * duplicate_player_db      Duplicates the user in the database
     *      The function creates a temporary table with all the row information from the original table (depending on user ID).
     *      The id_player is then set to NULL to avoid duplicates of primary_key.
     *      The username and email are appended with a random integer to avoid duplicates.
     *      We drop the temporary table at the end but also at the beginning if it exists.
     * 
     * @param type $id_player       Id of the player to duplicate
     * @param type $rand_key        Random integer to be appended at the end of certain columns
     * @return boolean              Returns true or error message
     */
    public function duplicate_player_db($id_player, $rand_key){
        $this->db->trans_start();
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp");
        $this->db->query("CREATE TEMPORARY TABLE tmp SELECT * from game_players WHERE id_player = ".$this->db->escape($id_player));
        $this->db->query("UPDATE tmp SET id_player = NULL");
        $this->db->query("UPDATE tmp SET username = concat(username,'_".$this->db->escape($rand_key)."')");
        $this->db->query("UPDATE tmp SET email = concat(email,'_".$this->db->escape($rand_key)."')");
        $this->db->query("INSERT INTO game_players SELECT * FROM tmp ");
        $this->db->query("DROP TABLE tmp;");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    /**
     * duplicate_resort_db      Duplicates the resort in the database
     *      The function creates a temporary table with all the row information from the original table (depending on last inserted user ID).
     *      The id_resort is then set to NULL to avoid duplicates of primary_key.
     *      The id player is set to the last inserted user ID to avoid having two resorts for one player
     *      We drop the temporary table at the end but also at the beginning if it exists.
     * 
     * @param type $id_player       Id of the player to duplicate
     * @param type $rand_key        Random integer to be appended at the end of certain columns
     * @return boolean              Returns true or error message
     */
    public function duplicate_resort_db($id_player, $id_resort, $rand_key, $last_inserted_id_player){
        $this->db->trans_start();
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp");
        $this->db->query("CREATE TEMPORARY TABLE tmp SELECT * from game_resorts WHERE id_player = ".$this->db->escape($id_player));
        $this->db->query("UPDATE tmp SET id_resort = NULL");
        $this->db->query("UPDATE tmp SET id_player = ".$this->db->escape($last_inserted_id_player));
        $this->db->query("UPDATE tmp SET resort_name = concat(resort_name,'_".$this->db->escape($rand_key)."')");
        $this->db->query("INSERT INTO game_resorts SELECT * FROM tmp ");
        $this->db->query("DROP TABLE tmp;");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    /**
     * duplicate_history_stats      Duplicates the resort in the database
     *      The function creates a temporary table with all the row information from the original table (depending on last inserted user ID).
     *      The id_resort is then set to NULL to avoid duplicates of primary_key.
     *      The id player is set to the last inserted user ID to avoid having two resorts for one player
     *      We drop the temporary table at the end but also at the beginning if it exists.
     * 
     * @param type $last_inserted_id_resort       Last inserted resort ID
     * @param type $rand_key        Random integer to be appended at the end of certain columns
     * @return boolean              Returns true or error message
     */
    public function duplicate_history_stats($last_inserted_id_resort, $original_id_resort, $table_name){
        $this->db->trans_start();
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp");
        $this->db->query("CREATE TEMPORARY TABLE tmp SELECT * from `".$table_name."` WHERE id_resort = ".$this->db->escape($original_id_resort));
        $this->db->query("UPDATE tmp SET id_resort = ".$this->db->escape($last_inserted_id_resort));
        $this->db->query("UPDATE tmp SET id = NULL");
        $this->db->query("INSERT INTO `".$table_name."` SELECT * FROM tmp ");
        $this->db->query("DROP TABLE tmp;");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * duplicate_achievements      Duplicates the started/completed achievements in the database
     *      The function creates a temporary table with all the row information from the original table (depending on last inserted user ID).
     *      The id_user_achievements is then set to NULL to avoid duplicates of primary_key.
     *      The id player is set to the last inserted user ID to avoid having two resorts for one player
     *      We drop the temporary table at the end but also at the beginning if it exists.
     * 
     * @param type $last_inserted_id_player       Last inserted player ID
     * @param type $original_id_player       Original player ID
     * @param type $table_name        Table name to duplicate
     * @return boolean              Returns true or error message
     */
    public function duplicate_achievements($last_inserted_id_player, $original_id_player, $table_name){
        $this->db->trans_start();
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp");
        $this->db->query("CREATE TEMPORARY TABLE tmp SELECT * from `".$table_name."` WHERE id_player = ".$this->db->escape($original_id_player));
        $this->db->query("UPDATE tmp SET id_player = ".$this->db->escape($last_inserted_id_player));
        $this->db->query("UPDATE tmp SET id_user_achievements = NULL");
        $this->db->query("INSERT INTO `".$table_name."` SELECT * FROM tmp ");
        $this->db->query("DROP TABLE tmp;");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    /**
     * duplicate_buildings_and_items      Duplicates buildings, slopes and lifts in the database for the player ID
     *      The function creates a temporary table with all the row information from the original table (depending on last inserted user ID).
     *      The $id_column_table is then set to NULL to avoid duplicates of primary_key.
     *      The id player is set to the last inserted user ID to avoid having two items for one player
     *      We drop the temporary table at the end but also at the beginning if it exists.
     * 
     * @param type $last_inserted_id_resort       Id of the resort to get the new row
     * @param type $original_id_resort            Id of the resort to duplicate
     * @param type $table_name                    Table name to be handled
     * @param type $id_column_table               id of the first column of the table
     * @param type $rand_key                      A random value is use to make the name unique
     * @return boolean              Returns true or error message
     */
    public function duplicate_buildings_and_items($last_inserted_id_resort, $original_id_resort, $table_name, $id_column_table, $rand_key){
        $this->db->trans_start();
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp");
        $this->db->query("CREATE TEMPORARY TABLE tmp SELECT * from `".$table_name."` WHERE id_resort = ".$this->db->escape($original_id_resort));
        $this->db->query("UPDATE tmp SET id_resort = ".$this->db->escape($last_inserted_id_resort));
        $this->db->query("UPDATE tmp SET `".$id_column_table."` = NULL");
        // Only if dealing with slopes or lifts (because other tables don't have a custom name column 
        if ($table_name == "game_created_slopes" || $table_name == "game_created_lifts" || $table_name == "game_purchased_equipments")
            $this->db->query("UPDATE tmp SET custom_name = concat(custom_name,'_".$this->db->escape($rand_key)."')");
        else if ($table_name == 'game_hired_staff')
            $this->db->query("UPDATE tmp SET id_item_assigned = NULL");
        $this->db->query("INSERT INTO `".$table_name."` SELECT * FROM tmp ");
        //$this->db->query("DROP TABLE tmp;");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * get_last_inserted_id_player      Gets the last inserted ID player based on the random key and original username
     * 
     * @param type $username            Original username
     * @param type $rand_key            Random key previously generated
     * @return type                     Returns the ID player directly (integer)
     */
    public function get_last_inserted_id_player($username, $rand_key){
        $new_username = $username.'_'.$rand_key;
        $this->db->select('id_player');
        $this->db->from('game_players');
        $this->db->where('username', $new_username);
        $this->db->order_by('id_player', 'desc');
        $this->db->limit(1); 
        $query = $this->db->get();
        $result = $query->row();
        $result_id_player = $result->id_player;
        return $result_id_player;
    }
    
    /**
     * get_last_inserted_id_resort     Gets the last inserted ID resort based on the last inserted ID player
     * 
     * @param type $id_player           last inserted ID player
     * @return type                     Returns the ID resort directly (integer)
     */
    public function get_last_inserted_id_resort($id_player){
        $this->db->select('id_resort');
        $this->db->from('game_resorts');
        $this->db->where('id_player', $id_player);
        $this->db->limit(1); 
        $query = $this->db->get();
        $result = $query->row();
        $result_id_resort = $result->id_resort;
        return $result_id_resort;
    }
    
    /**
     * get_new_player_staff      Gets the last inserted ID resort based on the last inserted ID player
     * 
     * @param type $id_resort           last inserted resort player
     * @return type                     Returns the ID resort directly (integer)
     */
    public function get_new_player_staff($id_resort){
        $this->db->select('*');
        $this->db->from('game_hired_staff');
        $this->db->where('id_resort', $id_resort);
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * select_deserved_slopes       Selects all lifts  information for provided player  
     *            This function will be used to get information about the deserved slopes for each lift
     * @param type $id_resort       ID of the resort
     * @return type                 Query's result
     */
    public function select_deserved_slopes($id_resort){
        $this->db->select('*');
        $this->db->from('game_created_lifts');
        $this->db->where('id_resort', $id_resort);
        $query = $this->db->get();
        return $query->result();
    }
       
    
    /**
     * assign_deserved_slope                        Assignes a deserved_slope to the newly created lift of the duplicated player
     * 
     * @param type $last_inserted_id_resort         Newly duplicated resort (new one)
     * @param type $column_name                     Column name, depending of loop (deserved_slope_1....)
     * @param type $id_slope_to_assign              ID of the new slope to assign
     * @param type $id_created_lifts                ID of the lift to which the slope needs to be assigned to
     * @return boolean                              Return true or error message
     */
    public function assign_deserved_slope($last_inserted_id_resort, $column_name, $id_slope_to_assign, $id_created_lifts){
        $this->db->trans_start();
        $this->db->set($column_name, $id_slope_to_assign);
        $this->db->where('id_resort' , $last_inserted_id_resort);                              
        $this->db->where('id_created_lifts' , $id_created_lifts);                              
        $this->db->update('game_created_lifts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * get_last_unassigned_item_id              Gets the last unassigned ID item (which should be assigned in a future function)
     * 
     * @param type $last_inserted_id_resort     Newly duplicated resort (new one)
     * @param type $table                       Table to run the query on (id_created_lifts of id_created_slopes of staff?...)
     * @param type $id_column                   ID of the item which is common to the staff table and to the item table
     * @return type                             Returns query's result
     */
    public function get_last_unassigned_item_id($last_inserted_id_resort, $table, $id_column){
        $this->db->select($id_column);
        $this->db->from($table.' AS game_item');
        $this->db->where('NOT EXISTS (Select id_item_assigned FROM game_hired_staff as game_staff WHERE game_item.'.$id_column.' = game_staff.id_item_assigned)', NULL, FALSE);
        $this->db->where('game_item.id_resort = '.$last_inserted_id_resort.'');
        $this->db->limit(1); 
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_assigned_id_sector($reference_player_id){
        $this->db->select('id_item_assigned');
        $this->db->from('game_hired_staff');
        $this->db->where('type_item_assigned', 'sector');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * assign_staff_to_item             Assigns a staff to a specific item
     * @param type $id_resort           ID of the resort
     * @param type $id_to_assign        ID of the item to assign
     * @param type $id_hired_staff      ID of the staff that will get the item assigned
     * @return boolean                  Returns true or error message
     */
    public function assign_staff_to_item($id_resort, $id_to_assign, $id_hired_staff){
        $this->db->trans_start();
        $this->db->set('id_item_assigned', $id_to_assign);
        $this->db->where('id_resort' , $id_resort);                              
        $this->db->where('id_hired_staff' , $id_hired_staff);                              
        $this->db->update('game_hired_staff');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    public function activate_player($id_player){
        $this->db->trans_start();
        $this->db->set('activated', '1');
        $this->db->where('id_player' , $id_player);                                         
        $this->db->update('game_players');
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