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
            FROM wordle_words
            WHERE 1";
    }
    
    protected function getSortQuery(bool $sort): string {
        $sql = '';
        if ($sort) {
            $sql = " ORDER BY word";
        } else {
            $sql = " ORDER BY `usage` DESC";
        }
        return $sql;
    }
    
    public function clearSearchParameters() {
        $this->params = [];
    }
    
    protected function getParameterType(string $type): string {
        switch ($type) {
            case 'missing':
            case 'found':
            case 'has':
                return $type;
                break;
            default:
                return '';
                break;
        }
    }
    
    protected function getParameterPosition(int $position): int {
        $pos = (int)$position;
        if ($pos < 1 || $pos > 5) $pos = 1;
        
        return $pos;
    }
    
    public function addSearchParameter(string $type, string $letter, int $position, string $word): void {
        $this->params[strtoupper($letter)][] = [
            'type' => $this->getParameterType($type),
            'position' => $this->getParameterPosition($position),
            'word' => strtoupper($word)
        ];
    }
    
    protected function getParameterQuery(): string {
        $sql = [];
        foreach($this->params as $letter => $data) {
            $count = 0;
            $letter = $this->db->real_escape_string($letter);
            
            foreach($data as $occurance) {
                if ($occurance['type']=='found' || $occurance['type']=='has') {
                    $count++;
                }
            }
            
            if (!$count) {
                $sql[] = " AND word NOT LIKE '%" .
                    $letter .
                    "%'";
            } else {
                foreach($data as $occurance) {
                    if ($occurance['type']=='found') {
                        $sql[] = " AND l" . $occurance['position'] . " = '" .
                            $letter . "'";
                            //. " AND l" . $occurance['position'] . "count >= " .
                            // $count
                    } else {
                        $sql[] = " AND word LIKE '%" . $letter . "%'";
                        $sql[] = " AND l" . $occurance['position'] . " <> '" .
                            $letter . "'";
                    }
                }
            }
        }
        $sql = array_unique($sql);
        
        return implode($sql);
    }
    
    public function doSearch(bool $sort): array {
        
        $sql = $this->getSearchQuery();
        $sql .= $this->getParameterQuery();
        $sql .= $this->getSortQuery($sort);
        $sql .= ";";
        
        if ($results = $this->db->query($sql)) {
            return $results->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
}

