<?php

    require_once __DIR__ . '/header.php';
    require '../controllers/OpenFileController.php';

    use classes\OpenFileController;

    if (isset($_GET) && isset($_POST)) {
        /*new OpenFileController();*/
        echo 'hello';
    } else {
        echo "There has been an error while processing your request.
        Please try again and if the problem presists, contact support.";
        return;
    }

    require_once __DIR__ . '/footer.php';