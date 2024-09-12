<?php
    final class Billing{
        private $api_path;
        public function __construct() {
            $data = file_get_contents( "../../config/apis.json");
            $config = json_decode($data, true);
            $api_url = $config['billing_api'];
            $this->api_path = $config['billing_api'];
        }
    }  