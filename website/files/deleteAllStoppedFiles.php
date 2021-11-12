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
        if (unlink($files->getFileRelativePath($_POST['fileNb'])) === false) {
            echo 'Error: couldn\'t delete the file you specified.';
            return;
        }
    }
} else {
    echo 'Error: something happened when processing your request. Please try again.';
    return;
}