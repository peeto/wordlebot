<?php

include '../vendor/autoload.php';

use peeto\WordleBot;

if (file_exists('config.php')) {
    include 'config.php';
} else {
    include 'example.config.php';
}
$config['routeurl'] = '?route=search';
$wordle = new WordleBot\WordleBot($config);

if (isset($_REQUEST['route']) && $_REQUEST['route']=='search') {
    $wordle->autoSearch();
} elseif (isset($_REQUEST['route']) && $_REQUEST['route']=='letterstats') {
    echo json_encode($wordle->getLetterStats());
} else {

?>
<!DOCTYPE html>
<html>
    <head>
        <title>WordleBot</title>
    </head>
    <body>
        <h2>WordleBot</h2>
        <p>
            This is an example. There is another <a href="/example.php">here</a>.
        </p>
        <?php
        $wordle->renderUI();
        ?>
    </body>
</html>

<?php
}
