<?php

session_start();

require '../controllers/SessionController.php';

use classes\SessionController;

$session = new SessionController();

$dir = $session->getSessionFolderPath();
$files = glob($dir . '/' . '*.{json,tmp}', GLOB_BRACE);

foreach($files as $file){
    unlink($file);
}

?>