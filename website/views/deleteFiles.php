<?php

session_start();

require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/SessionController.php';

use classes\SessionController;

$session = new SessionController();

$dir = $session->getSessionFolderPath();
$files = glob($dir . '/' . '*.{json,tmp,xlsx}', GLOB_BRACE);

foreach($files as $file){
    unlink($file);
}

if (!rmdir($session->getSessionFolderPath()))
    echo "An error has occured deleting the session folder";die;