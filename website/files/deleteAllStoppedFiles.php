<?php
session_start();
require_once '../controllers/FilesController.php';
require_once '../controllers/SessionController.php';

use classes\FilesController;
use classes\SessionController;

if (isset($_POST) && isset($_POST['token'])) {
    $session = new SessionController($_POST['token']);
    $files = new FilesController($session->session_id);

    for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++) {
        if ($files->getFileStatus($fileNb) === "Stopped") {
            unlink($files->getFileRelativePath($fileNb));
        }
    }
} else {
    echo 'Error: something happened when processing your request. Please try again.';
    return;
}

session_destroy();