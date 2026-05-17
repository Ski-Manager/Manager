<?php

class Blogs_model extends CI_Model {

    public function get_posts($limit = 10, $offset = 0) {
        $this->db->select('*');
        $this->db->from('game_news');
        $this->db->where('active', '1');
        $this->db->order_by('created_date', 'desc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_posts() {
        $this->db->from('game_news');
        $this->db->where('active', '1');
        return $this->db->count_all_results();
    }

    /**
     * Search posts by title or content (case-insensitive).
     *
     * @param string $query   Search term
     * @param int    $limit   Max rows per page
     * @param int    $offset  Row offset for pagination
     * @return array
     */
    public function search_posts($query, $limit = 10, $offset = 0) {
        $this->db->select('*');
        $this->db->from('game_news');
        $this->db->where('active', '1');
        $this->db->group_start();
        $this->db->like('title_english', $query);
        $this->db->or_like('title_french', $query);
        $this->db->or_like('content_english', $query);
        $this->db->or_like('content_french', $query);
        $this->db->group_end();
        $this->db->order_by('created_date', 'desc');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Count posts matching a search query.
     *
     * @param string $query  Search term
     * @return int
     */
    public function count_search_posts($query) {
        $this->db->from('game_news');
        $this->db->where('active', '1');
        $this->db->group_start();
        $this->db->like('title_english', $query);
        $this->db->or_like('title_french', $query);
        $this->db->or_like('content_english', $query);
        $this->db->or_like('content_french', $query);
        $this->db->group_end();
        return $this->db->count_all_results();
    }

}
