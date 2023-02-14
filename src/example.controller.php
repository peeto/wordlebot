<?php

use peeto\WordleBot;

$wordle = new Wordlebot([
    'host'     => 'localhost',
    'username' => 'chris',
    'password' => 'eon33flux',
    'database' => 'wordle'
]);

if ($_REQUEST['route']=='search') {
    $data = json_decode( file_get_contents('php://input'), true );
    echo json_encode($wordle->search($data));
} elseif ($_REQUEST['route']=='letterstats') {
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
<?php
$wordle->render([
    'includejquery' => true
]);
?>
</body>
</html>

<?php
}
