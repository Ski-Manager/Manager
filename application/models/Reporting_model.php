<?php

class Reporting_model extends CI_Model{
    
    
    public function get_report($resortID, $date){
        $query = $this->db
            ->select('*')
            ->from('game_reporting_data')
            ->where('date', $date)
            ->where('id_resort', $resortID)
            ->order_by('type', 'asc')
            ->get();
       return $query->result();
    }
    
    public function check_report_date($resortID, $date){
        $query = $this->db
            ->select('*')
            ->from('game_reports')
            ->where('date', $date)
            ->where('id_resort', $resortID)
            ->get();
       return $query;
    }
    
    public function list_Reports($resortID){
        $query = $this->db
            ->select('*')
            ->from('game_reports')
            ->where('id_resort', $resortID)
            ->order_by('date', 'desc')
            ->get();
       return $query;
    }
    
    public function get_all_reports_to_generate($yesterday_GMT){
        $query = $this->db
            ->select('*')
            ->from('game_reports')
            ->where('date <=', $yesterday_GMT)
            ->where('status', 'pending')
            ->get();
       return $query;
    }
    
    
    public function delete_old_reports($date){
        $this->db->trans_start();
        $this->db->where('date <=', $date);
        $this->db->delete('game_reporting_data');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
        
        
    }
    
    
    public function order_report_db($data){
        if ($insert = $this->db->insert('game_reports', $data)) {                    
            return true;
        }
        else
            return false;
    }
    
    public function update_report_generated($uuid_report){ 
        $this->db->trans_start();
        $this->db->set('status', 'created');
        $this->db->where('uuid_report' , $uuid_report);  
        $this->db->limit(1); 
        $this->db->update('game_reports');
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