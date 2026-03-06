<?php

class Seeder {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function call($seeder)
    {
        require_once APPPATH."seeds/".$seeder.".php";
        $seed = new $seeder();
        $seed->run();
    }

}