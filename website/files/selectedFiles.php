<?php

require_once '../controllers/SessionController.php';
require_once '../controllers/FilesController.php';

use classes\FilesController;
use classes\SessionController;

if (isset($_POST)) {
    if (isset($_POST['token'])) {
        $session = new SessionController($_POST['token']);
        session_start();
        $files = new FilesController($session->session_id);
    } else {
        return'Error: your request couldn\'t be processed. Please try again.';
    }
    $concernedFiles = array();

    for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++) {
        if (isset($_POST[$fileNb])) {
            array_push($concernedFiles, $_POST[$fileNb]);
        }
    }
    var_dump($concernedFiles);die;
}

//header("Location: /website/views/getFiles.php");