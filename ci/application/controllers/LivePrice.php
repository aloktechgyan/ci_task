<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LivePrice extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url'); // Load URL Helper
        $this->load->model('LivePrice_model'); // Load Model "LivePrice_model"
    }

    public function index() {
        // Load the HTML view file 
        $this->load->view('index');
    }
    public function update_price($symbol) {
        // API endpoint details
        $api_url = 'https://io.pixelsoftwares.com/task_php_api.php';
        $token = 'ab4086ecd47c568d5ba5739d4078988f';

        // Prepare API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('symbol' => $symbol)));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('token: ' . $token));

        // Execute API request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
        curl_close($ch);

        // Check if curl request was successful
        if ($response === false) {
            $error_message = curl_error($ch);
            log_message('error', 'Curl error: ' . $error_message);
            show_error('Curl error: ' . $error_message, 500);
        }

        // Check HTTP status code
        if ($http_code !== 200) {
            show_error('HTTP request failed with status code: ' . $http_code, 500);
        }

        // Decode JSON response
        $data = json_decode($response, true);
        // echo "<pre>";
        // print_r($data);
        // die();
     
        // Check if JSON decoding was successful
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            show_error('Failed to parse JSON response', 500);
        }

        // Check if API response contains expected data
        if (isset($data['status']) && $data['status'] == 1 && isset($data['data'])) {
            $price_data = $data['data'];

            // Check if data exists, update or insert
            $existing_price = $this->LivePrice_model->get_live_price($symbol);

            if ($existing_price) {
                $this->LivePrice_model->update_live_price($price_data);
            } else {
                $this->LivePrice_model->insert_live_price($price_data);
            }

            // // Pass price_data to the view
            // $data['bidPrice'] = $price_data['bidPrice']; // Assuming bidPrice is what you want to display
            // $this->load->view('index', $data); // Load 'index' view with data

             // Return JSON response with bidPrice
             $this->output->set_content_type('application/json');
             $this->output->set_output(json_encode(array('status' => 1, 'message' => 'Success', 'bidPrice' => $price_data['bidPrice'])));
         } else {
             $this->output->set_status_header(500);
             $this->output->set_output(json_encode(array('status' => 0, 'message' => 'Invalid API response format')));
         }
    }
}
