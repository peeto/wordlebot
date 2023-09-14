<?php

include '../vendor/autoload.php';

use peeto\WordleBot;

if (file_exists('config.php')) {
    include 'config.php';
} else {
    include 'example.config.php';
}
$wordle = new WordleBot\WordleBot($config);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>WordleBot</title>
        <style>
            <?= $wordle->getCSS(); ?>
        </style>
        <script>
            <?= $wordle->getJavascriptLib(); ?>
        </script>
    </head>
    <body>
        <h2>WordleBot</h2>
        <p>
            This is an example. There is another <a href="/">here</a>.
        </p>
        <?= $wordle->getHTML(); ?>
        <script>
            <?= $wordle->getJavascript(); ?>
        </script>
    </body>
</html>
