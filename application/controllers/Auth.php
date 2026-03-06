<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 * @property Post_model $Post_model
 */

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function login()
    {
        if ($this->input->post()) {

            $this->form_validation->set_rules('email','Email','required|valid_email');
            $this->form_validation->set_rules('password','Password','required');

            if ($this->form_validation->run() == TRUE) {

                $email = $this->input->post('email', TRUE);
                $password = $this->input->post('password', TRUE);

                $user = $this->User_model->get_user_by_email($email);

                if ($user && password_verify($password, $user->password)) {

                    $session_data = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'logged_in' => TRUE
                    ];

                    $this->session->set_userdata($session_data);

                    if ($user->role == 'admin') {
                        redirect('admin/dashboard');
                    } else {
                        redirect('user/dashboard');
                    }

                } else {
                    $this->session->set_flashdata('error','Invalid email or password');
                    redirect('login');
                }

            }
        }

        $data['title'] = 'Login';
        $data['content'] = 'auth/login';
        $this->load->view('components/main',$data);
    }

    public function register()
    {
        if ($this->input->post()) {

            $this->form_validation->set_rules('name','Name','required|min_length[3]');
            $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password','Password','required|min_length[6]');

            if ($this->form_validation->run() == TRUE) {

                $data = [
                    'name' => $this->input->post('name', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                    'role' => 'user'  // Always 'user', never from input
                ];

                $this->User_model->insert_user($data);

                $this->session->set_flashdata('success','Registration successful. Please login.');
                redirect('login');
            }
        }

        $data['title'] = 'Register';
        $data['content'] = 'auth/register';
        $this->load->view('components/main',$data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}