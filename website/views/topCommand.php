<?php

require './header.php';

exec('ps faux | grep -e node -e chromium', $output);

for ($i = 0; $i < count($output); $i++) {
    echo htmlentities($output[$i]) . '<br>';
}

echo '<br><br><br>';

if (isset($_GET) && isset($_GET['filename'])) {
    $filename = pathinfo($_GET['filename'])['filename'];
    if (isset($_COOKIE) && isset($_COOKIE['token'])) {
        $token = $_COOKIE['token'];
        for ($i = 0; $i < count($output); $i++) {
            if (strpos($output[$i], $token) !== false && strpos($output[$i], $filename) !== false) {
                echo $output[$i] . '<br>';
                echo $output[$i + 1] .'<br>';
            }
        }
    } else {
        echo 'Error: no token registered.';
        return;
    }
} else {
    echo 'Error: there has been error processing the file.';
    return;
}

echo "Info: the selected robot has successfully been stopped. The changes may take a while to save in the files page.";