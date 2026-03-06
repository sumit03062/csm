<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function get_all_posts($limit = 0, $offset = 0)
    {
        $query = $this->db
            ->select('posts.*, users.name')
            ->from('posts')
            ->join('users','users.id = posts.user_id')
            ->where('posts.status', 'published')
            ->order_by('posts.created_at','DESC');
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->get()->result();
    }

    public function get_user_posts($user_id, $limit = 0, $offset = 0)
    {
        $query = $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC');
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->get('posts')->result();
    }

    public function get_published_user_posts($user_id, $limit = 0, $offset = 0)
    {
        $query = $this->db
            ->where('user_id', $user_id)
            ->where('status', 'published')
            ->order_by('created_at', 'DESC');
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->get('posts')->result();
    }

    public function get_post($id)
    {
        return $this->db
            ->where('id',$id)
            ->get('posts')
            ->row();
    }

    public function insert_post($data)
    {
        return $this->db->insert('posts',$data);
    }

    public function update_post($id,$data)
    {
        return $this->db
            ->where('id',$id)
            ->update('posts',$data);
    }

    public function delete_post($id)
    {
        return $this->db
            ->where('id',$id)
            ->delete('posts');
    }

    public function get_total_posts()
    {
        return $this->db->count_all('posts');
    }

    public function filter_posts($author = '', $date = '')
    {
        // Validate input length to prevent resource exhaustion
        $author = substr(trim($author), 0, 100);
        $date = substr(trim($date), 0, 10);
        
        // Validate date format if provided
        if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        }

        $this->db->select('posts.*, users.name');
        $this->db->from('posts');
        $this->db->join('users', 'users.id = posts.user_id');

        if (!empty($author)) {
            $this->db->like('users.name', $author);
        }

        if (!empty($date)) {
            $this->db->where('DATE(posts.created_at)', $date);
        }

        // Get role from session through CI
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        $user_id = $CI->session->userdata('user_id');

        // Users can only see their own posts or published posts
        if ($role != 'admin' && !empty($role)) {
            $this->db->where('posts.user_id', $user_id);
        } else {
            // Admin sees all, others see only published
            $this->db->where('posts.status', 'published');
        }

        return $this->db->get()->result();
    }

    public function search_posts($keyword = '', $limit = 0, $offset = 0)
    {
        // Validate and trim input
        $keyword = trim($keyword);
        if (strlen($keyword) < 2) {
            return array();
        }
        
        $keyword = substr($keyword, 0, 100);
        
        $query = $this->db
            ->select('posts.*, users.name')
            ->from('posts')
            ->join('users', 'users.id = posts.user_id')
            ->where("(posts.title LIKE '%{$keyword}%' OR posts.content LIKE '%{$keyword}%')")
            ->order_by('posts.created_at', 'DESC');
        
        // Get role from session
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        $user_id = $CI->session->userdata('user_id');
        
        // Users can only see their own posts or published posts
        if ($role != 'admin') {
            $query->where('posts.user_id', $user_id);
        } else {
            // Admin sees all, others see only published
            $query->where('posts.status', 'published');
        }
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->get()->result();
    }

    public function get_search_result_count($keyword = '')
    {
        $keyword = trim($keyword);
        if (strlen($keyword) < 2) {
            return 0;
        }
        
        $keyword = substr($keyword, 0, 100);
        
        $this->db->where("(posts.title LIKE '%{$keyword}%' OR posts.content LIKE '%{$keyword}%')");
        
        // Get role from session
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        $user_id = $CI->session->userdata('user_id');
        
        // Users can only see their own posts or published posts
        if ($role != 'admin') {
            $this->db->where('posts.user_id', $user_id);
        } else {
            $this->db->where('posts.status', 'published');
        }
        
        return $this->db->count_all_results('posts');
    }
}
