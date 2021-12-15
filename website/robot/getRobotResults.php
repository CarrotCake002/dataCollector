<?php

require_once './../views/header.php';
require './../controllers/OpenFileController.php';
require './../controllers/SessionController.php';

use classes\OpenFileController;
use classes\SessionController;

set_time_limit(0);

if (isset($_FILES)):

    if (isset($_POST) && isset($_POST['token']) && $_POST['token'] != '') {
        if (!isset($_COOKIE['token']))
        setcookie('token', $_POST['token'], time() + 60 * 60 * 24 * 30, '/');
        $session = new SessionController($_POST['token']);
        if ($session === NULL || $session->error) {
            echo 'Error: the token you sent is invalid';
            return;
        }
        session_start();
    } else {
        echo "Error: make sure you've set your personal token";
        return;
    }

    $json_data = null;
    if (isset($_FILES['openFile']) && isset($_FILES['openFile']['tmp_name'])) {
        if (!$session->checkSessionFolderExists()) {
            echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
            return;
        }
        // should look for possible errors here with big files
        $filePath = '../../savefiles/' . $session->getSessionFolderName() . '/' . basename($_FILES['openFile']['tmp_name']) . '.tmp';
        $move = move_uploaded_file($_FILES['openFile']['tmp_name'], $filePath);
        if ($move === false) {
            echo "Error: there has been an error moving the file.";
            return;
        }
        @ $json_data = file_get_contents($filePath);
        if ($json_data === false) {
            echo "Error: the file you sent doesn't exist.";
            return;
        }
        $json_data = json_decode($json_data, true);
        if ($json_data === null) {
            echo "Error: the savefile format is not correct. Make sure there are no errors in the syntax.";
            return;
        }
        $openFile = new OpenFileController($json_data);
    } else {
        echo "Error: there has been a problem with the file's name.";
        return;
    }
    $csvName = str_replace('.json', '.csv', $_FILES['openFile']['name']);
?>

<div id="tableBlock">
        <form action="/website/views/getAllLinkDetails.php" method="GET">
            <input type="text" name="filename" id="filename" value="<?= $filePath ?>">
            <input type="submit" name="submit" value="Get all link details">
        </form>
        <button class="copyTableButton" onclick="copyLinksTable()">Copy Table contents</button>
        <a href="<?= '/savefiles/' . $session->getSessionFolderName() . '/' . $csvName  ?>" download="<?= $csvName ?>" ><br><br>Download a CSV with all data here!</a>
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
                    echo "<br><br><b>There is no data to display. Make sure you accessed the correct file </b>";
                    die;
                }
                for ($i = 1; $i <= $objectCount; $i++): ?>

                <tr>
                    <td><?= $i ?></td>
                    <td><?= $openFile->getIteration($i) ?></td>
                    <td><a href="<?= $openFile->getURl($i) ?>"><?= $openFile->getUrl($i) ?></a></td>
                    <td><?= $openFile->getUrlDepth($i) ?></td>
                    <td><?= $openFile->getStatus($i) ?></td>
                    <td><?= $openFile->getAllLinksSize($i) ?></td>
                    <td><a href="<?= '/website/views/getLinkDetails.php/?object=' . $i . '&filename=' . $filePath ?>">Click for more details</a></td>
                </tr>

            <?php endfor; ?>
        </table>
    </div>

    <script>
        function copyLinksTable() {
            var copy = document.getElementById('allLinksTable');
            
            window.getSelection().selectAllChildren(copy);
            document.execCommand('Copy');
        }

        function createCsvFile() {
            $.ajax({
                method: "POST",
                type: "POST",
                url: '../files/createCsvFile.php',
                data: {
                    'token': "<?= $session->session_id ?>",
                    'filename': "<?= $csvName ?>",
                    'dataFile': "<?= basename($_FILES['openFile']['tmp_name']) ?>"
                },
                success: function (response) {
                    //location.reload();
                    console.log(response);
                },
                error: function (err) {
                    alert("Error: couldn't create the CSV file.");
                    console.log(err);
                }
            });
        }
        createCsvFile();
    </script>

<?php
else:
    echo "Error: there was an error collecting the file data.";
endif;

require_once './../views/footer.php';

session_destroy();