<?php

require_once '../views/header.php';

if (isset($_COOKIE) && isset($_COOKIE['token'])) {
    setcookie('token', '', -1, '/');
    echo '<br><br>Your token has been unset!';
} else {
    echo '<br><br>No token is active at the moment.<br>Therefore nothing has been unset!';
}


require_once '../views/footer.php';