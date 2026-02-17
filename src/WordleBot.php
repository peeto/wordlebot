<?php

namespace peeto\WordleBot;

use peeto\WordleBot\Db;

class WordleBot {
    
    protected $db;
    protected $id;
    protected $routeurl;
    
    function __construct(array $config) {
        $this->db = new Db( $config );
        if (isset($config['id'])) $this->id = $config['id'];
        if (isset($config['routeurl'])) $this->routeurl = $config['routeurl'];
    }
    
    public function getLetterStats(): array {
        return $this->db->getLetterStats();
    }
    
    public function search(array $data = []): array {
        if (isset($data['letters'])) foreach ($data['letters'] as $param) {
            if (
                    isset($param['state'])
                    && isset($param['letter'])
                    && isset($param['position'])
                    && isset($param['word'])
                ) {
                $this->db->addSearchParameter($param['state'], $param['letter'], $param['position'], $param['word']);
            }
        }
        return $this->db->doSearch(
            isset($data['sort']) && $data['sort']=='1'
        );
    }
    
    public function autoSearch(): void {
        $data = json_decode( file_get_contents('php://input'), true );
        echo json_encode($this->search($data));
        exit();
    }
    
    public function setId(string $id): void {
        $this->id = $id;
    }
    
    public function getId(): string {
        return ($this->id != '') ? $this->id : 'wordlebot';
    }
    
    public function setRouteURL(string $setRouteURL): void {
        $this->routeurl = $setRouteURL;
    }
    
    public function getRouteURL(): string {
        return ($this->routeurl != '') ? $this->routeurl : '?route=search';
    }
    
    protected function getView(string $name, string $ext = 'php'): string {
        $path = 'views/' . $name . '.' . $ext;
        if (!file_exists($path)) return '';
        
        ob_start();
        include $path;
        return ob_get_clean();        
    }
    
    public function getCSS(): string {
        return $this->getView('css');
    }
    
    public function getJavascriptLib(): string {
        return $this->getView('javascript', 'js');
    }
    
    public function getJavascript(): string {
        return $this->getView('javascript');
    }
    
    public function getHTML(): string {
        return $this->getView('html');
    }
    
    public function renderUI(): void {
        echo '<style type="text/css">';
        echo "\r\n";
        echo $this->getCSS();
        echo '</style>';
        echo "\r\n";
        echo $this->getHTML();
        echo '<script language="javascript">';
        echo "\r\n";
        echo $this->getJavascriptLib();
        echo $this->getJavascript();
        echo '</script>';
        echo "\r\n";
    }
    
}

