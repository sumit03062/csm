<?php

class Seed extends CI_Controller {

    public function index()
    {
        $this->load->library('seeder');
        $this->seeder->call('UserSeeder');

        echo "Seeder executed successfully";
    }
} 