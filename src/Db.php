<?php

namespace peeto\WordleBot;

class Db {
    
    protected $db;
    protected $params;
    protected $hasLetters;
    
    function __construct(array $config) {
        $this->db = new \mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database']);
        $this->params = [];
        $this->hasLetters = [];
    }
    
    public function getLetterStats(): array {
        $sql = "SELECT letter, `usage` FROM view_wordle_letter_stats;";
        
        if ($results = $this->db->query($sql)) {
            return $results->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    protected function getSearchQuery(): string {
        return "SELECT UCASE(word) AS word
            FROM view_wordle_word_stats
            WHERE 1";
    }
    
    protected function getSortQuery(bool $sort) {
        if ($sort) return " ORDER BY word";
        return "";
    }
    
    public function clearSearchParameters() {
        $this->params = [];
        $this->hasLetters = [];
    }
    
    public function addSearchParameter(string $type, string $letter, int $position) {
        $pos = (int)$position;
        if ($pos < 1 || $pos > 5) $pos = 1;
        
        switch ($type) {
            case 'missing':
                if (!in_array($letter, $this->hasLetters)) {
                    $this->params[] = " AND word NOT LIKE '%" .
                        $this->db->real_escape_string(strtolower($letter)) .
                        "%'";
                }
                break;
            case 'found':
                $this->params[] = " AND l" . $pos . " = '" .
                    $this->db->real_escape_string(strtolower($letter)) .
                    "'";
                $this->hasLetters[] = $letter;
                break;
            case 'has':
                $this->params[] = " AND word LIKE '%" .
                    $this->db->real_escape_string(strtolower($letter)) .
                    "%'";
                $this->params[] = " AND l" . $pos . " <> '" .
                    $this->db->real_escape_string(strtolower($letter)) .
                    "'";
                $this->hasLetters[] = $letter;
                break;
            default:
                break;
        }
        
        $this->params = array_unique($this->params);
    }
    
    public function doSearch(bool $sort): array {
        $sql = $this->getSearchQuery();
        
        foreach ($this->params as $param) {
            $sql .= $param;
        }
        
        $sql .= $this->getSearchQuery($sort);
        
        $sql .= ";";
        
        if ($results = $this->db->query($sql)) {
            return $results->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
}

