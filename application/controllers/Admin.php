<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 * @property Post_model $Post_model
 * @property Api_model $Api_model
 */

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('User_model');
        $this->load->model('Post_model');
        $this->load->model('Api_model');
        $this->load->library('form_validation');

        // check login
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        // check admin role
        if ($this->session->userdata('role') != 'admin') {
            show_error('Unauthorized access');
        }
    }

    public function dashboard()
    {
        $data['title'] = 'Admin Dashboard';
        $data['content'] = 'admin/dashboard';

        // Dashboard statistics
        $data['total_users'] = $this->User_model->get_total_users();
        $data['total_posts'] = $this->Post_model->get_total_posts();

        // Latest posts (show only 5 most recent)
        $data['posts'] = $this->Post_model->get_all_posts(5, 0);

        $data['latest_posts'] = count($data['posts']);

        // API Integration
        $data['random_joke'] = $this->Api_model->get_random_joke();

        $this->load->view('components/main', $data);
    }

    public function users()
    {
        $data['title'] = 'Manage Users';
        $data['content'] = 'admin/users';
        $data['users'] = $this->User_model->get_all_users();

        $this->load->view('components/main', $data);
    }

    public function user_create()
    {
        $data['title'] = 'Create User';
        $data['content'] = 'admin/user_form';

        $this->load->view('components/main', $data);
    }

    public function user_store()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,user]');

        if ($this->form_validation->run() == FALSE) {
            redirect('admin/user_create');
        }

        $data = [
            'name' => $this->input->post('name', TRUE),
            'email' => $this->input->post('email', TRUE),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'role' => $this->input->post('role', TRUE)
        ];

        $this->User_model->insert_user($data);

        $this->session->set_flashdata('success', 'User created successfully');
        redirect('admin/users');
    }

    public function user_edit($id)
    {
        $user = $this->User_model->get_user($id);

        if (!$user) {
            show_404();
        }

        $data['user'] = $user;
        $data['title'] = 'Edit User';
        $data['content'] = 'admin/user_form';

        $this->load->view('components/main', $data);
    }

    public function user_update($id)
    {
        $user = $this->User_model->get_user($id);

        if (!$user) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,user]');

        if ($this->form_validation->run() == FALSE) {
            redirect('admin/user_edit/' . $id);
        }

        $data = [
            'name' => $this->input->post('name', TRUE),
            'email' => $this->input->post('email', TRUE),
            'role' => $this->input->post('role', TRUE)
        ];

        $password = $this->input->post('password', TRUE);
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->User_model->update_user($id, $data);

        $this->session->set_flashdata('success', 'User updated successfully');
        redirect('admin/users');
    }

    public function user_delete($id)
    {
        $user = $this->User_model->get_user($id);

        if (!$user) {
            show_404();
        }

        // Prevent deleting the last admin
        if ($user->role == 'admin') {
            $admin_count = $this->db->where('role', 'admin')->count_all_results('users');
            if ($admin_count <= 1) {
                $this->session->set_flashdata('error', 'Cannot delete the last admin user');
                redirect('admin/users');
            }
        }

        $this->User_model->delete_user($id);

        $this->session->set_flashdata('success', 'User deleted successfully');
        redirect('admin/users');
    }

    
}

