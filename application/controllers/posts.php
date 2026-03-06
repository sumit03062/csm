<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property Post_model $Post_model
 * @property Category_model $Category_model
 * @property User_model $User_model
 * @property CI_DB_query_builder $db
 * @property CI_Pagination $pagination
 * @property CI_URI $uri
 * 
 */

class Posts extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Post_model');
        $this->load->model('Category_model');
        $this->load->library('form_validation');

        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    //    Show

    public function index()
    {
        $this->load->library('pagination');
        
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        // Get total count
        if ($role == 'admin') {
            $total_posts = $this->Post_model->get_total_posts();
        } else {
            $total_posts = count($this->Post_model->get_user_posts($user_id));
        }

        // Pagination config
        $config['base_url'] = base_url('posts/index');
        $config['total_rows'] = $total_posts;
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';

        $this->pagination->initialize($config);

        $offset = $this->uri->segment(3, 0);

        if ($role == 'admin') {
            $data['posts'] = $this->Post_model->get_all_posts($config['per_page'], $offset);
        } else {
            $data['posts'] = $this->Post_model->get_user_posts($user_id, $config['per_page'], $offset);
        }

        $data['pagination'] = $this->pagination->create_links();
        $data['title'] = 'Posts';
        $data['content'] = 'posts/index';

        $this->load->view('components/main', $data);
    }

    //    create

    public function create()
    {
        $data['title'] = 'Create Post';
        $data['content'] = 'posts/create';
        $data['categories'] = $this->Category_model->get_all_categories();

        $this->load->view('components/main', $data);
    }

    //    Store

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[3]');
        $this->form_validation->set_rules('content', 'Content', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,published]');

        if ($this->form_validation->run() == FALSE) {
            redirect('posts/create');
        }

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'title' => $this->input->post('title', TRUE),
            'content' => $this->input->post('content', TRUE),
            'status' => $this->input->post('status', TRUE)
        ];

        $this->Post_model->insert_post($data);
        
        // Get the last inserted post ID
        $post_id = $this->db->insert_id();
        
        // Assign categories if provided
        $categories = $this->input->post('categories');
        if (!empty($categories)) {
            $this->Category_model->assign_categories($post_id, $categories);
        }

        $this->session->set_flashdata('success', 'Post created successfully');
        redirect('posts');
    }

    // Edit

    public function edit($id)
    {
        $post = $this->Post_model->get_post($id);

        if (!$post) {
            show_404();
        }

        // Only owner or admin
        if (
            $this->session->userdata('role') != 'admin' &&
            $post->user_id != $this->session->userdata('user_id')
        ) {

            show_error('Unauthorized access');
        }

        $data['post'] = $post;
        $data['title'] = 'Edit Post';
        $data['content'] = 'posts/edit';
        $data['categories'] = $this->Category_model->get_all_categories();
        $data['post_categories'] = $this->Category_model->get_post_categories($id);

        $this->load->view('components/main', $data);
    }

    //   Update

    public function update($id)
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[3]');
        $this->form_validation->set_rules('content', 'Content', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,published]');

        if ($this->form_validation->run() == FALSE) {
            redirect('posts/edit/' . $id);
        }

        $data = [
            'title' => $this->input->post('title', TRUE),
            'content' => $this->input->post('content', TRUE),
            'status' => $this->input->post('status', TRUE)
        ];

        $this->Post_model->update_post($id, $data);
        
        // Update categories
        $categories = $this->input->post('categories');
        if (empty($categories)) {
            $categories = array();
        }
        $this->Category_model->assign_categories($id, $categories);

        $this->session->set_flashdata('success', 'Post updated successfully');
        redirect('posts');
    }

    //    Delete

    public function delete($id)
    {
        $post = $this->Post_model->get_post($id);

        if (!$post) {
            show_404();
        }

        // Only owner or admin
        if (
            $this->session->userdata('role') != 'admin' &&
            $post->user_id != $this->session->userdata('user_id')
        ) {

            show_error('Unauthorized access');
        }

        $this->Post_model->delete_post($id);

        redirect('posts');
    }


    public function filter()
    {
        $author = $this->input->post('author', TRUE);
        $date = $this->input->post('date', TRUE);

        $data['posts'] = $this->Post_model->filter_posts($author, $date);
        
        $this->load->view('posts/_table_rows', $data);
    }

    public function search()
    {
        $keyword = $this->input->post('keyword', TRUE);

        if (strlen(trim($keyword)) < 2) {
            echo '';
            return;
        }

        $data['posts'] = $this->Post_model->search_posts($keyword);
        
        $this->load->view('posts/_table_rows', $data);
    }
}
