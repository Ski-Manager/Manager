<?php
class db_init {
    public function set_sql_mode() {
        $CI =& get_instance();
        $CI->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
    }
}
