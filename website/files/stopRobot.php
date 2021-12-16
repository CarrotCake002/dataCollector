<?php

require './../views/header.php';

exec('ps faux | grep -e node -e chromium', $output);

if (isset($_GET) && isset($_GET['filename'])) {
    $filename = pathinfo($_GET['filename'])['filename'];
    if (isset($_COOKIE) && isset($_COOKIE['token'])) {
        $token = $_COOKIE['token'];
        for ($i = 0; $i < count($output); $i++) {
            if (strpos($output[$i], $token) !== false && strpos($output[$i], $filename) !== false) {
                exec('kill -9 ' . intval(str_replace('www ', '', $output[$i]))); 
                exec('kill -9 ' . intval(str_replace('www ', '', $output[$i + 1])));
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
?>
<script>
    alert("File successfully stopped. However, it may take a while for it to change the status to 'Stopped'.");
    window.location.href = "/website/views/getFiles.php";
</script>