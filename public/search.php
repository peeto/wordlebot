<?php

include '../vendor/autoload.php';

use peeto\WordleBot;

include 'example.config.php';
$wordle = new WordleBot\WordleBot($config);
$wordle->autoSearch();
