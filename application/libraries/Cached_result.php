<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cached_result
 *
 * Mimics the CI_DB_result interface so that model methods can return cached
 * data while keeping all existing callers working without modification.
 */
class Cached_result {

    private $rows = [];

    public function __construct($result_array = []) {
        $this->rows = is_array($result_array) ? $result_array : [];
    }

    public function result() {
        return array_map(function ($row) { return (object) $row; }, $this->rows);
    }

    public function result_array() {
        return $this->rows;
    }

    public function row($index = 0) {
        return isset($this->rows[$index]) ? (object) $this->rows[$index] : null;
    }

    public function row_array($index = 0) {
        return $this->rows[$index] ?? null;
    }

    public function num_rows() {
        return count($this->rows);
    }
}
