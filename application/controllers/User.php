<?php

defined('BASEPATH') or exit('No direct script access allowed');


/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 * @property Post_model $Post_model
 */

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('Post_model');
        $this->load->model('User_model');
        $this->load->library('form_validation');

        // check login
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        // check user role
        if ($this->session->userdata('role') != 'user') {
            show_error('Unauthorized access');
        }
    }
    

    public function dashboard()
    {
        $user_id = $this->session->userdata('user_id');
        
        $data['title'] = 'User Dashboard';
        $data['content'] = 'user/dashboard';

        // Get user's posts
        $data['posts'] = $this->Post_model->get_user_posts($user_id);
        $data['my_posts'] = count($data['posts']);
        
        // Additional stats
        $data['total_users'] = $this->User_model->get_total_users();

        $this->load->view('components/main', $data);
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user($user_id);

        if (!$user) {
            show_404();
        }

        $data['user'] = $user;
        $data['title'] = 'My Profile';
        $data['content'] = 'user/profile';

        $this->load->view('components/main', $data);
    }

    public function profile_update()
    {
        $user_id = $this->session->userdata('user_id');

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            redirect('user/profile');
        }

        $data = [
            'name' => $this->input->post('name', TRUE),
            'email' => $this->input->post('email', TRUE)
        ];

        $password = $this->input->post('password', TRUE);
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->User_model->update_user($user_id, $data);

        $this->session->set_flashdata('success', 'Profile updated successfully');
        redirect('user/profile');
    }
    
}