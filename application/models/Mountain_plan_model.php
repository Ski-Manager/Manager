<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mountain_plan_model
 *
 * Handles all database interactions for the Mountain Master Plan System.
 * Plans go through the lifecycle: draft → submitted → approved → active.
 * Revising an approved or active plan incurs a financial cost and a
 * reputation penalty (see MASTER_PLAN_REVISION_COST and
 * MASTER_PLAN_REVISION_REP_PENALTY in config.php).
 */
class Mountain_plan_model extends CI_Model {

    // -------------------------------------------------------------------------
    // Read methods
    // -------------------------------------------------------------------------

    /**
     * get_active_plan_DB   Returns the single active (or most-recently created)
     *                      plan for a resort, or NULL if none exists.
     *
     * @param int $id_resort
     * @return object|null
     */
    public function get_active_plan_DB($id_resort) {
        try {
            $pk = $this->_pk_col();
            $this->db->select('game_master_plans.*');
            if ($pk !== 'id_master_plan') {
                $this->db->select($pk . ' AS id_master_plan', FALSE);
            }
            $row = $this->db
                ->from('game_master_plans')
                ->where('id_resort', (int)$id_resort)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get()
                ->row();
            return $this->_normalise_pk($row);
        } catch (Exception $e) {
            log_message('error', 'Mountain_plan_model::get_active_plan_DB – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * get_plan_by_id_DB    Returns a single plan row by primary key, ensuring
     *                      it belongs to the given resort.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return object|null
     */
    public function get_plan_by_id_DB($id_master_plan, $id_resort) {
        try {
            $pk = $this->_pk_col();
            $this->db->select('game_master_plans.*');
            if ($pk !== 'id_master_plan') {
                $this->db->select($pk . ' AS id_master_plan', FALSE);
            }
            $row = $this->db
                ->from('game_master_plans')
                ->where($pk,          (int)$id_master_plan)
                ->where('id_resort',  (int)$id_resort)
                ->limit(1)
                ->get()
                ->row();
            return $this->_normalise_pk($row);
        } catch (Exception $e) {
            log_message('error', 'Mountain_plan_model::get_plan_by_id_DB – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * get_all_plans_DB     Returns all plans for a resort, newest first.
     *
     * @param int $id_resort
     * @return array
     */
    public function get_all_plans_DB($id_resort) {
        try {
            $pk = $this->_pk_col();
            $this->db->select('game_master_plans.*');
            if ($pk !== 'id_master_plan') {
                $this->db->select($pk . ' AS id_master_plan', FALSE);
            }
            $rows = $this->db
                ->from('game_master_plans')
                ->where('id_resort', (int)$id_resort)
                ->order_by('created_at', 'desc')
                ->get()
                ->result();
            return array_map([$this, '_normalise_pk'], $rows);
        } catch (Exception $e) {
            log_message('error', 'Mountain_plan_model::get_all_plans_DB – ' . $e->getMessage());
            return [];
        }
    }

    // -------------------------------------------------------------------------
    // Write methods
    // -------------------------------------------------------------------------

    /**
     * create_plan_DB   Inserts a new draft plan for the resort.
     *
     * @param int    $id_resort
     * @param string $plan_name
     * @param string $expansion_strategy
     * @param string $environmental_notes
     * @param int    $zoning_limit_slopes
     * @param int    $zoning_limit_lifts
     * @param int    $zoning_limit_buildings
     * @return int|false  Inserted ID on success, false on failure
     */
    public function create_plan_DB($id_resort, $plan_name, $expansion_strategy,
                                   $environmental_notes, $zoning_limit_slopes,
                                   $zoning_limit_lifts, $zoning_limit_buildings) {
        $data = [
            'id_resort'              => (int)$id_resort,
            'plan_name'              => $plan_name,
            'expansion_strategy'     => $expansion_strategy,
            'environmental_notes'    => $environmental_notes,
            'zoning_limit_slopes'    => (int)$zoning_limit_slopes,
            'zoning_limit_lifts'     => (int)$zoning_limit_lifts,
            'zoning_limit_buildings' => (int)$zoning_limit_buildings,
            'status'                 => 'draft',
            'change_count'           => 0,
        ];
        $this->db->trans_start();
        $this->db->insert('game_master_plans', $data);
        $inserted_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $inserted_id;
    }

    /**
     * update_plan_DB   Updates an existing draft plan's editable fields.
     *                  Only drafts may be freely updated.
     *
     * @param int    $id_master_plan
     * @param int    $id_resort
     * @param string $plan_name
     * @param string $expansion_strategy
     * @param string $environmental_notes
     * @param int    $zoning_limit_slopes
     * @param int    $zoning_limit_lifts
     * @param int    $zoning_limit_buildings
     * @return bool
     */
    public function update_plan_DB($id_master_plan, $id_resort, $plan_name,
                                   $expansion_strategy, $environmental_notes,
                                   $zoning_limit_slopes, $zoning_limit_lifts,
                                   $zoning_limit_buildings) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->set('plan_name',              $plan_name);
        $this->db->set('expansion_strategy',     $expansion_strategy);
        $this->db->set('environmental_notes',    $environmental_notes);
        $this->db->set('zoning_limit_slopes',    (int)$zoning_limit_slopes);
        $this->db->set('zoning_limit_lifts',     (int)$zoning_limit_lifts);
        $this->db->set('zoning_limit_buildings', (int)$zoning_limit_buildings);
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * submit_plan_DB   Transitions the plan from 'draft' to 'submitted' and
     *                  deducts the submission cost from the resort's cash.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function submit_plan_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->set('status',       'submitted');
        $this->db->set('submitted_at', date('Y-m-d H:i:s'));
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'draft');
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();

        if ($affected > 0) {
            // Deduct submission cost
            $this->db->set('cash', 'cash - ' . (int)MASTER_PLAN_SUBMISSION_COST, FALSE);
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resorts');
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * approve_plan_DB  Transitions the plan from 'submitted' to 'approved'.
     *                  Called either by a cron or when the player requests review.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function approve_plan_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->set('status',      'approved');
        $this->db->set('approved_at', date('Y-m-d H:i:s'));
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'submitted');
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * activate_plan_DB Transitions the plan from 'approved' to 'active'.
     *                  Only one plan per resort should be active at a time.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function activate_plan_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->set('status',       'active');
        $this->db->set('activated_at', date('Y-m-d H:i:s'));
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'approved');
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * revise_plan_DB   Reverts an approved or active plan back to 'draft' so
     *                  the player can amend it.  Charges MASTER_PLAN_REVISION_COST
     *                  and deducts MASTER_PLAN_REVISION_REP_PENALTY reputation.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function revise_plan_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();

        // Revert to draft and increment change_count
        $this->db->set('status',       'draft');
        $this->db->set('change_count', 'change_count + 1', FALSE);
        $this->db->set('submitted_at', NULL);
        $this->db->set('approved_at',  NULL);
        $this->db->set('activated_at', NULL);
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where_in('status',      ['approved', 'active']);
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();

        if ($affected > 0) {
            // Deduct revision cost and reputation penalty
            $this->db->set('cash',       'cash - '       . (int)MASTER_PLAN_REVISION_COST,        FALSE);
            $this->db->set('reputation', 'reputation - ' . (int)MASTER_PLAN_REVISION_REP_PENALTY, FALSE);
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resorts');
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * delete_plan_DB   Deletes a draft plan.  Only drafts may be deleted.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function delete_plan_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'draft');
        $this->db->delete('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * cancel_submission_DB Reverts a submitted plan back to 'draft' so the
     *                      player can amend it without paying the revision fee.
     *                      The original submission fee is non-refundable.
     *
     * @param int $id_master_plan
     * @param int $id_resort
     * @return bool
     */
    public function cancel_submission_DB($id_master_plan, $id_resort) {
        $pk = $this->_pk_col();
        $this->db->trans_start();
        $this->db->set('status',       'draft');
        $this->db->set('submitted_at', NULL);
        $this->db->where($pk, (int)$id_master_plan);
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'submitted');
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $affected > 0;
    }

    /**
     * duplicate_plan_DB    Creates a new draft plan by copying the fields of an
     *                      existing plan (any status).
     *
     * @param int $id_master_plan  Source plan ID
     * @param int $id_resort
     * @return int|false  Inserted ID on success, false on failure
     */
    public function duplicate_plan_DB($id_master_plan, $id_resort) {
        $source = $this->get_plan_by_id_DB((int)$id_master_plan, (int)$id_resort);
        if (!$source) {
            return false;
        }
        $data = [
            'id_resort'              => (int)$id_resort,
            'plan_name'              => $source->plan_name . ' (copy)',
            'expansion_strategy'     => $source->expansion_strategy,
            'environmental_notes'    => $source->environmental_notes,
            'zoning_limit_slopes'    => (int)$source->zoning_limit_slopes,
            'zoning_limit_lifts'     => (int)$source->zoning_limit_lifts,
            'zoning_limit_buildings' => (int)$source->zoning_limit_buildings,
            'status'                 => 'draft',
            'change_count'           => 0,
        ];
        $this->db->trans_start();
        $this->db->insert('game_master_plans', $data);
        $inserted_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $inserted_id;
    }

    /**
     * check_expired_plans_DB   Marks active plans as 'expired' when they have
     *                          been active for at least MASTER_PLAN_DURATION_DAYS.
     *                          Called on every page view of the plan overview.
     *
     * @param int $id_resort
     * @return int  Number of plans expired
     */
    public function check_expired_plans_DB($id_resort) {
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . (int)MASTER_PLAN_DURATION_DAYS . ' days'));
        $this->db->trans_start();
        $this->db->set('status', 'expired');
        $this->db->where('id_resort',       (int)$id_resort);
        $this->db->where('status',          'active');
        $this->db->where('activated_at <=', $cutoff);
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE) ? $affected : 0;
    }

    /**
     * check_approval_due_DB    Auto-approves submitted plans that have been
     *                          waiting for at least MASTER_PLAN_APPROVAL_DAYS days.
     *                          Called by the nightly cron.
     *
     * @param int $id_resort
     * @return int  Number of plans approved
     */
    public function check_approval_due_DB($id_resort) {
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . (int)MASTER_PLAN_APPROVAL_DAYS . ' days'));
        $this->db->trans_start();
        $this->db->set('status',      'approved');
        $this->db->set('approved_at', date('Y-m-d H:i:s'));
        $this->db->where('id_resort',      (int)$id_resort);
        $this->db->where('status',         'submitted');
        $this->db->where('submitted_at <=', $cutoff);
        $this->db->update('game_master_plans');
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE) ? $affected : 0;
    }

    // -------------------------------------------------------------------------
    // Private schema-compatibility helpers
    // -------------------------------------------------------------------------

    /**
     * _pk_col  Returns the actual primary-key column name in game_master_plans.
     *          On installations where the table was originally created with the
     *          column named 'id_plan' (before the fix migration renamed it), this
     *          falls back to 'id_plan' so all queries continue to work.
     *
     * @return string
     */
    private function _pk_col() {
        static $pk = null;
        if ($pk === null) {
            $fields = $this->db->field_data('game_master_plans');
            $columns = $fields ? array_column($fields, 'name') : [];
            $pk = in_array('id_master_plan', $columns, TRUE) ? 'id_master_plan' : 'id_plan';
        }
        return $pk;
    }

    /**
     * _normalise_pk    Ensures the returned stdClass row always exposes the
     *                  property 'id_master_plan', even when the underlying
     *                  column is still named 'id_plan' (old schema).
     *
     * @param  object|null $row
     * @return object|null
     */
    private function _normalise_pk($row) {
        if ($row !== null && !isset($row->id_master_plan) && isset($row->id_plan)) {
            $row->id_master_plan = $row->id_plan;
        }
        return $row;
    }
}
