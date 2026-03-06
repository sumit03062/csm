<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function get_user_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->get('users')
            ->row();
    }

    public function insert_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function get_total_users()
    {
        return $this->db->count_all('users');
    }

    public function get_user($id)
    {
        return $this->db
            ->where('id',$id)
            ->get('users')
            ->row();
    }

    public function get_all_users()
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->get('users')
            ->result();
    }

    public function update_user($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('users', $data);
    }

    public function delete_user($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete('users');
    }

}