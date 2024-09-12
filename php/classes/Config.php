<?php
    final class Config{
        public function __construct() {
        }

        public function getApiPath(){
            $data = file_get_contents( "../../config/apis.json");// __DIR__ .
            $config = json_decode($data, true);
            return $config['billing_api'];
        }
    }
    
?>