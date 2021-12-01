<?php

require_once '../controllers/FilesController.php';
require_once '../controllers/SessionController.php';

use classes\FilesController;
use classes\SessionController;

if (isset($_POST) && isset($_POST['token']) && isset($_POST['fileNb'])) {
    $session = new SessionController($_POST['token']);
    session_start();
    $files = new FilesController($session->session_id);

    if (unlink($files->getFileRelativePath($_POST['fileNb']))) {
        $files->updateFileList();
    } else {
        echo 'Error: couldn\'t delete the file you specified.';
        return;
    }
} else {
    echo 'Error: something happened when processing your request. Please try again.';
    return;
}

session_destroy();