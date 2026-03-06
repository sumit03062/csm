<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    public function get_all_categories()
    {
        return $this->db
            ->order_by('name', 'ASC')
            ->get('categories')
            ->result();
    }

    public function get_category($id)
    {
        return $this->db
            ->where('id', $id)
            ->get('categories')
            ->row();
    }

    public function get_post_categories($post_id)
    {
        return $this->db
            ->select('categories.*')
            ->from('categories')
            ->join('post_categories', 'post_categories.category_id = categories.id')
            ->where('post_categories.post_id', $post_id)
            ->get()
            ->result();
    }

    public function assign_categories($post_id, $category_ids = array())
    {
        // Delete existing categories for this post
        $this->db->where('post_id', $post_id)->delete('post_categories');
        
        // Add new categories
        if (!empty($category_ids)) {
            foreach ($category_ids as $category_id) {
                $data = array(
                    'post_id' => $post_id,
                    'category_id' => $category_id
                );
                $this->db->insert('post_categories', $data);
            }
        }
        
        return true;
    }

    public function get_posts_by_category($category_id, $limit = 0, $offset = 0)
    {
        $query = $this->db
            ->select('posts.*, users.name')
            ->from('posts')
            ->join('users', 'users.id = posts.user_id')
            ->join('post_categories', 'post_categories.post_id = posts.id')
            ->where('post_categories.category_id', $category_id)
            ->where('posts.status', 'published')
            ->order_by('posts.created_at', 'DESC');
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->get()->result();
    }

    public function get_total_posts_by_category($category_id)
    {
        return $this->db
            ->where('post_categories.category_id', $category_id)
            ->where('posts.status', 'published')
            ->from('posts')
            ->join('post_categories', 'post_categories.post_id = posts.id')
            ->count_all_results();
    }
}
?>
