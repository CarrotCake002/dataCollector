<?php

session_start();

require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/header.php';
require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/OpenFileController.php';
require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/SessionController.php';

use classes\OpenFileController;
use classes\SessionController;

$session = new SessionController();

if (isset($_FILES)):
    $json_data = null;
    if (isset($_FILES['openFile']) && isset($_FILES['openFile']['tmp_name'])) {
        if (!$session->checkSessionFolderExists()) {
            if (!$session->createSessionFolder()) {
                echo "Error: the session folder couldn't be created.";
                return;
            }
        }
        $filePath = $session->getSessionFolderPath() . '/' . basename($_FILES['openFile']['tmp_name']);
        $move = move_uploaded_file($_FILES['openFile']['tmp_name'], $filePath);
        if ($move === false) {
            echo "There has been an error moving the file.";
            return;
        }
        @ $json_data = file_get_contents($filePath);
        if ($json_data === false) {
            echo "The file you sent doesn't exist.";
            return;
        }
        $json_data = json_decode($json_data, true);
        if ($json_data === null) {
            echo "The savefile format is not correct. Make sure there are no errors in the syntax.";
            return;
        }
        $openFile = new OpenFileController($json_data);
    } else {
        echo "There has been a problem with the file's name.";
        return;
    }
?>


<div id="tableBlock">
        <form action="../views/getAllLinkDetails.php" method="GET">
            <input type="text" name="filename" id="filename" value="<?= $filePath ?>">
            <input type="submit" name="submit" value="Get all link details">
        </form>
        <button class="copyTableButton" onclick="copyLinksTable()">Copy Table contents</button>
        <table id="allLinksTable">
            <tr>
                <th>Id</th>
                <th>Iteration</th>
                <th>URL</th>
                <th>Depth</th>
                <th>Status</th>
                <th>Nb links found</th>
                <th>More details</th>
            </tr>

            <?php
                $objectCount = $openFile->getObjectCount();

                if ($objectCount < 1) {
                    echo "<br><br><b>There is no data to display. Make sure you acessed the correct file </b>";
                    die;
                }
                for ($i = 1; $i < $objectCount; $i++): ?>

            <tr>
                <td><?= $i ?></td>
                <td><?= $openFile->getIteration($i) ?></td>
                <td><a href="<?= $openFile->getURl($i) ?>"><?= $openFile->getUrl($i) ?></a></td>
                <td><?= $openFile->getUrlDepth($i) ?></td>
                <td><?= $openFile->getStatus($i) ?></td>
                <td><?= $openFile->getAllLinksSize($i) ?></td>
                <td><a href="<?= '/website/views/getLinkDetails.php/?object=' . $i . '&filename=' . $filePath ?>">Click for more details</a></td>
            </tr>
            <?php
                endfor;
            ?>
        </table>
    </div>

    <script>
        function copyLinksTable() {
            var copy = document.getElementById('allLinksTable');
            
            window.getSelection().selectAllChildren(copy);
            document.execCommand('Copy');
        }
    </script>

<?php
endif;

require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/footer.php';