<?php
session_start();
require_once './../views/header.php';
require '../controllers/SessionController.php';
require '../controllers/FilesController.php';

use classes\SessionController;
use classes\FilesController;

if (isset($_POST) && isset($_POST['submit']) && isset($_POST['token'])) {
    $session = new SessionController($_POST['token']);
    if ($session->error) {
        echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
        return;
    }
} else {
    echo "Error: a problem occured while fetching the data, please try again.";
    return;
}

$files = new FilesController($session->session_id);


?>


<h1 style="text-align: center; font-size: 48px">Here you can see all your files</h1>

<table>
    <tr>
        <th>File name</th>
        <th>Size (kB)</th>
        <th>Last updated</th>
        <th>Status</th>
        <th>Download</th>
        <th>Delete</th>
    </tr>
    <?php for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++): ?>
    <tr>
        <td><?= $files->getFileName($fileNb) ?></td>
        <td><?= $files->getFileSize($fileNb) ?></td>
        <td><?= $files->getFileLastUpdate($fileNb) ?></td>
        <td></td>
        <td><img src="../../assets/download_button.png" alt="download" style="width: 25px;"></td>
        <td><img src="../../assets/delete_red_bin.png" alt="delete" style="width: 25px;"></td>
    </tr>
    <?php endfor; ?>
</table>



<?php

require_once './../views/footer.php';