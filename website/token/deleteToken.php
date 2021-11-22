<?php

use classes\FilesController;

require_once '../views/header.php';
require '../controllers/FilesController.php';


if (isset($_COOKIE) && isset($_COOKIE['token'])) {
    $session_id = $_COOKIE['token'];
} else {
    echo '<br><br>You don\'t have any active tokens at the moment';
    return;
}

if (!$session_id)
    return;
$files = new FilesController($session_id);

for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++) {
    if (unlink($files->getFileRelativePath($fileNb)) === false) {
        echo 'Error: file could not be deleted.<br>';
    }
}
rmdir('../../savefiles/' . $files->folder);
setcookie('token', '', -1, '/');

echo 'Your token has been successfully deleted!';


require_once '../views/footer.php';