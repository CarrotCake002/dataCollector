<?php
session_start();
require_once './../views/header.php';
require '../controllers/SessionController.php';
require '../controllers/FilesController.php';

use classes\SessionController;
use classes\FilesController;


$files = new FilesController();

if (isset($_POST) && isset($_POST['submit']) && isset($_POST['token'])) {
    $session = new SessionController($_POST['token']);
    if ($session->error)
        echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
        return;
} else {
    echo "Error: a problem occured while fetching the data, please try again.";
    return;
}


?>




<h1 style="text-align: center; font-size: 48px">Here you can see all your files</h1>

<table>
    <tr>
        <th>File name</th>
        <th>status</th>
        <th>download</th>
    </tr>
    <?php  ?>
    <tr>
        <td>

        </td>
    </tr>
</table>



<?php

require_once './../views/footer.php';