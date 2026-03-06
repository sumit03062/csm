<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function get_random_joke()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://official-joke-api.appspot.com/random_joke",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'CodeIgniter/3.0'
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            log_message('error', 'API cURL Error: ' . $error);
            curl_close($curl);
            return "Unable to fetch joke - Connection error";
        }

        curl_close($curl);

        if ($http_code !== 200) {
            log_message('error', 'API HTTP Error: ' . $http_code);
            return "Unable to fetch joke - Server returned error (HTTP " . $http_code . ")";
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'API JSON Decode Error: ' . json_last_error_msg());
            return "Unable to parse API response";
        }

        return $data['setup'] . " " . $data['punchline'] ?? "No joke available";
    }

}