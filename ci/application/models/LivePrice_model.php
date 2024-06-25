<?php
class LivePrice_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database(); //db load
    }

    public function get_live_price($symbol) {
        $this->db->where('symbol', $symbol);
        $query = $this->db->get('live_prices'); 
        return $query->row_array();
    }

    public function update_live_price($data) {
        $update_data = array(
            'symbol' => $data['symbol'],
            'bid_price' => $data['bidPrice'],
            'bid_qty' => $data['bidQty'],
            'ask_price' => $data['askPrice'],
            'ask_qty' => $data['askQty'],
            'time_updated' => date('Y-m-d H:i:s', $data['time'] / 1000) // Convert timestamp to MySQL datetime format
        );
       // echo "<pre>";
       // print_r($data);
       // die();
        $this->db->where('symbol', $data['symbol']);
        $this->db->update('live_prices', $update_data);
     
      // updated here successful;
    }

    public function insert_live_price($data) {
       // echo "<pre>";
       // print_r($data);
       // die();
       // $this->db->insert('live_prices', $data);
       
       // Created inser_data array 
       $insert_data = array(
            'symbol' => $data['symbol'],
            'bid_price' => $data['bidPrice'],
            'bid_qty' => $data['bidQty'],
            'ask_price' => $data['askPrice'],
            'ask_qty' => $data['askQty'],
            'time_updated' => date('Y-m-d H:i:s', $data['time'] / 1000) // Convert timestamp to MySQL datetime format
        );

    // Insert Live Price into table
    $this->db->insert('live_prices', $insert_data);
    }
}
