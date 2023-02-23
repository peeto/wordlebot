<?php

namespace peeto\WordleBot;

use peeto\WordleBot\Db;

class WordleBot {
    
    protected $db;
    
    function __construct(array $config) {
        $this->db = new Db( $config );
    }
    
    public function getLetterStats() {
        return $this->db->getLetterStats();
    }
    
    public function search( $data = []) {
        foreach ($data['letters'] as $param) {
            $this->db->addSearchParameter($param['state'], $param['letter'], $param['position']);
        }
        return $this->db->doSearch($data['sort']=='1');
    }
    
    public function render(array $options = []) {
        include 'view.php';
    }
    
}

