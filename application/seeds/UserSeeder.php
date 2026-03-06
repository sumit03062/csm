<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 
 
 * @property CI_DB_query_builder $db
 */

class UserSeeder {

    public function run()
    {
        $CI =& get_instance();

        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'name' => 'Simple User',
                'email' => 'user@test.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'user'
            ]
        ];

        $CI->db->insert_batch('users', $data);
    }
}