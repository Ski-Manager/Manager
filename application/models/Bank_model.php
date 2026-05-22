<?php

class Bank_model extends CI_Model{
    
        
    /**
     * get_generic_equipment_data    Gets the generic equipment data based on type and level
     * 
     * @param type $type        Equipments type
     * @param type $level       Level of the equipment
     * @return type             Return the genetic info
     */
    public function get_generic_bank_data(){
        $query = $this->db
            ->select('*')
            ->from('game_banks')
            ->get();
        return $query;
    }
    
    public function get_specific_bank_data($id_bank){
        $query = $this->db
            ->select('*')
            ->from('game_banks')
            ->where('id_bank', $id_bank)
            ->get();
        return $query;
    }
    
    
    public function get_ongoing_loan_player($currentResortID){
        $query = $this->db
            ->select('*')
            ->from('game_signed_loans')
            ->where('id_resort', $currentResortID)
            ->where('reimbursed', '0')
            ->get();
        return $query;
    }
    
    public function count_ongoing_standard_loan_player($currentResortID){
        $query = $this->db
            ->select('*')
            ->from('game_signed_loans')
            ->join('game_banks as banks_tbl', 'banks_tbl.id_bank = game_signed_loans.id_bank ', 'inner')
            ->where('banks_tbl.genepis_required', '0')
            ->where('game_signed_loans.reimbursed', '0')
            ->where('game_signed_loans.id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    
    public function count_ongoing_vip_loan_player($currentResortID){
        $query = $this->db
            ->select('*')
            ->from('game_signed_loans')
            ->join('game_banks as banks_tbl', 'banks_tbl.id_bank = game_signed_loans.id_bank ', 'inner')
            ->where('banks_tbl.genepis_required >', '0')
            ->where('game_signed_loans.reimbursed', '0')
            ->where('game_signed_loans.id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    public function get_ongoing_loans_to_finalize(){
        $query = $this->db
            ->select('*')
            ->from('game_signed_loans')
            ->where('reimbursed', '0')
            ->where('payments_left', '0')
            ->get();
        return $query;
    }
    
    /**
     * count_ongoing_loan_player    Counts how many ongoing loan the player has
     * 
     * @param type $currentResortID        Resort ID to check
     * @return type             Return the number of loans (should be 0 or 1)
     */
    public function count_ongoing_loan_player($currentResortID){
           $this->db->where('id_resort', $currentResortID) ;   
            $this->db->where('reimbursed', '0');
            $count = $this->db->count_all_results('game_signed_loans');
        return $count;
    }
    
    /**
     * get_generic_bank_info    Gets the generic equipment data based on the bank ID
     * 
     * @param type $id_bank        ID of the bank we need the info
     * @return type             Return the genetic info
     */
    public function get_generic_bank_info($id_bank){
        $query = $this->db
            ->select('*')
            ->from('game_banks')
            ->where('id_bank', $id_bank)
            ->get();
        return $query;
    }
    
    
    /**
     * get_loan_info    Gets the info about an ongoing loan
     * 
     * @param type get_loan_info        ID of the loan we need the info
     * @return type             Return the info
     */
    public function get_loan_info($id_loan, $currentResortID){
        $query = $this->db
            ->select('*')
            ->from('game_signed_loans')
            ->where('id_loan', $id_loan)
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    
    public function finalize_loan($reimbursed_date, $current_resort, $id_loan){
        $this->db->trans_start();
        $this->db->set('reimbursed_date', $reimbursed_date);
        $this->db->set('reimbursed', '1');
        $this->db->where('id_resort', $current_resort);
        $this->db->where('id_loan', $id_loan);
        $this->db->update('game_signed_loans');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    /**
     * signup_loan_DB       Insert a new loan in the database
     * @param type $new_loan_data        Info containing the new loan
     * 
     * @return type Returns the result of the query
     */
    public function signup_loan_DB($new_loan_data){                      
        $this->db->trans_start(); 
        $insert = $this->db->insert('game_signed_loans', $new_loan_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $insert;
        }           
    }

    // -------------------------------------------------------------------------
    // Investment account methods
    // -------------------------------------------------------------------------

    /**
     * get_investment_DB    Returns the investment row for a resort (or NULL)
     */
    public function get_investment_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_resort_investments')
            ->where('id_resort', $id_resort)
            ->get();
    }

    /**
     * upsert_investment_DB    Creates or updates the investment balance for a resort
     *
     * @param int $id_resort
     * @param int $new_balance   New absolute balance value in €
     * @param string $now        MySQL datetime string
     * @return bool
     */
    public function upsert_investment_DB($id_resort, $new_balance, $now) {
        $this->db->trans_start();
        $existing = $this->get_investment_DB($id_resort);
        if ($existing->num_rows() > 0) {
            $this->db->set('balance', $new_balance);
            $this->db->set('updated_at', $now);
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resort_investments');
        } else {
            $this->db->insert('game_resort_investments', [
                'id_resort'  => $id_resort,
                'balance'    => $new_balance,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_all_investments_DB   Returns all active investment rows (balance > 0)
     */
    public function get_all_investments_DB() {
        return $this->db
            ->select('*')
            ->from('game_resort_investments')
            ->where('balance >', 0)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Loan history
    // -------------------------------------------------------------------------

    /**
     * get_loan_history_DB  Returns paid-off loans for a resort, most recent first
     */
    public function get_loan_history_DB($id_resort) {
        return $this->db
            ->select('game_signed_loans.*, game_banks.name_english, game_banks.name_french')
            ->from('game_signed_loans')
            ->join('game_banks', 'game_banks.id_bank = game_signed_loans.id_bank', 'inner')
            ->where('game_signed_loans.id_resort', $id_resort)
            ->where('game_signed_loans.reimbursed', '1')
            ->order_by('game_signed_loans.reimbursed_date', 'DESC')
            ->limit(10)
            ->get();
    }
}

?>