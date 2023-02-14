<?php

namespace peeto\WordleBot;

class Db {
    
    protected $db;
    protected $params;
    
    function __construct(array $config) {
        $this->db = new \mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database']);
        $this->params = [];
    }
    
    public function getLetterStats(): array {
        $sql = "SELECT letter, `usage` FROM view_wordle_letter_stats;";
        
        if ($results = $this->db->query($sql)) {
            return $results->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    protected function getSearchQuery(): string {
        return "SELECT word
            FROM view_wordle_word_stats
            WHERE 1";
    }
    
    public function clearSearchParameters() {
        $this->params = [];
    }
    
    public function addSearchParameter(string $type, string $letter, int $position) {
        $pos = (int)$position;
        if ($pos < 1 || $pos > 5) $pos = 1;
        
        switch ($type) {
            case 'missing':
                $this->params[] = " AND word NOT LIKE '%" .
                    $this->db->real_escape_string($letter) .
                    "%'";
                break;
            case 'found':
                $this->params[] = " AND l" . $pos . " = '" .
                    $this->db->real_escape_string($letter) .
                    "'";
                break;
            case 'has':
                $this->params[] = " AND word LIKE '%" .
                    $this->db->real_escape_string($letter) .
                    "%'";
                $this->params[] = " AND l" . $pos . " <> '" .
                    $this->db->real_escape_string($letter) .
                    "'";
                break;
            default:
                break;
        }
        
        $this->params = array_unique($this->params);
    }
    
    public function doSearch(): array {
        $sql = $this->getSearchQuery();
        
        foreach ($this->params as $param) {
            $sql .= $param;
        }
        
        $sql .= ";";
        
        if ($results = $this->db->query($sql)) {
            return $results->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
}

