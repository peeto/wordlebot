<?php

namespace wordlebot;

require_once 'db.php';

class wordlebot {
    
    protected $db;
    
    function __construct(array $config) {
        $this->db = new db( $config );
    }
    
    public function getLetterStats() {
        return $this->db->getLetterStats();
    }
    
    public function search( $data = []) {
        foreach ($data as $param) {
            $this->db->addSearchParameter($param['state'], $param['letter'], $param['position']);
        }
        return $this->db->doSearch();
    }
    
    public function render() {
        include dirname( dirname(__FILE__) ) . '/views/view.php';
    }
    
}

