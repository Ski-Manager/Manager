<?php

class Admin_maintenance_model extends CI_Model{
    
    // TEST
    public function selected_users_to_update($equipment_id, $level){                    
        $query = $this->db
            ->select('game_resorts.id_player')
            ->from('game_resorts')
            ->join('game_purchased_equipments as t2', 't2.id_resort = game_resorts.id_resort', 'inner')
            ->where('t2.id_equipment', $equipment_id)
            ->where('t2.level', $level)
            ->get();
        return $query;
    }
    
    public function selected_users_to_update2(){                    
        $query = $this->db
            ->select('id_resort')
            ->from('game_resort_season')
            ->where('season', '2')
            ->where('start_date <', '2019-04-24 00:00:00')
            ->get();
        return $query;
    }
    
    public function give_money($id_resort, $give_value){                  
        $this->db->where('id_resort', $id_resort);
        $this->db->limit(1); 
        $this->db->set('cash', 'cash + '.$give_value, FALSE);
        $this->db->update('game_resorts');
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    public function select_existing_achievements($id_player, $id_achievement){                    
        $query = $this->db
            ->select('id_achievement')
            ->from('user_achievements')
            ->where('id_player', $id_player)
            ->where('id_achievement', $id_achievement)
            ->get();
        return $query;
    }
  
    
    public function insert_achievement($data){                      
        $this->db->trans_start(); 
        $insert = $this->db->insert('user_achievements', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $insert;
        }           
    }  
    // TEST
    
    
    
    
    /**
     * select_all_tables      Select all tables in DB
     * 
     * @return type     Returns query's result
     */
    public function select_all_tables(){
        $this->db->select('table_type, table_name, table_rows');
        $this->db->from('information_schema.tables');
        $this->db->where('TABLE_SCHEMA', 'u853012228_skiman');
        $query = $this->db->get();
        return $query;
    }
    
    public function count_rows_table($table){
        $this->db->from($table);
        $count = $this->db->count_all_results();
        return $count;
    }
    
    /**
     * empty_table_DB       Empty table in DB (TRUNCATE)
     * 
     * @param type $table
     * @return type
     */
    public function empty_table_DB($table){
        $query = $this->db->truncate($table);
        return $query;
    }
    
    public function reset_auto_increment($data){                      
        $this->db->trans_start(); 
        $this->db->query('ALTER TABLE '.$data.' AUTO_INCREMENT 1');
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