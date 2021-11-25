<?php

require_once './../views/header.php';
require './../controllers/OpenFileController.php';
require './../controllers/SessionController.php';

use classes\OpenFileController;
use classes\SessionController;

set_time_limit(0);

if (isset($_POST) && isset($_POST['token']) && $_POST['token'] != '') {
    if (!isset($_COOKIE['token']))
    setcookie('token', $_POST['token'], time() + 28800000, '/');
    $session = new SessionController($_POST['token']);
    if ($session === NULL || $session->error) {
        echo 'Error: the token you sent is invalid';
        return;
    }
} else {
    echo "Error: make sure you've set your personal token";
    return;
}

session_start();

if (isset($_FILES)):
    $json_data = null;
    if (isset($_FILES['openFile']) && isset($_FILES['openFile']['tmp_name'])) {
        if (!$session->checkSessionFolderExists()) {
            echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
            return;
        }
        $filePath = $session->getSessionFolderPath() . '/' . basename($_FILES['openFile']['tmp_name']);
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
    $stream = fopen($session->getSessionFolderPath() . '/' . $csvName, 'w');
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
                fwrite($stream,
                    "'Id'," .
                    "'Iteration'," .
                    "'URL'," .
                    "'Depth'," .
                    "'Times URL found'," .
                    "'Inlink'," .
                    "'Status'," .
                    "'Load time'," .
                    "'Title'," . "'Title size'," .
                    $openFile->getAllMetaDescriptionTitlesInCSV() . "'Nb Meta robots (M.r.)'," . $openFile->getAllMetaRobotsTitlesInCSV() .
                    "'Nb hreflang'," . $openFile->getAllHreflangTitlesInCSV() . "'Nb canonicals'," .
                    $openFile->getAllCanonicalTitlesInCSV() .
                    "'Nb outlinks'," . $openFile->getAllLinksTitlesInCSV() .
                    $openFile->getAllHeadTitlesInCSV() .
                    $openFile->getAllUserSelectorTitlesInCSV() .
                "\n");
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
            <?php                                                                     
                fwrite($stream,
                    "'" . $i . "','" .
                    $openFile->getIteration($i) . "','" .
                    $openFile->getUrl($i) . "','" .
                    $openFile->getUrlDepth($i) . "','" .
                    $openFile->getTimesUrlFound($i) . "','" .
                    $openFile->getUrlPredecessor($i) . "','" .
                    $openFile->getStatus($i) . "','" .
                    $openFile->getResponseTime($i) . "','" .
                    $openFile->getTitle($i) . "','" . $openFile->getTitleSize($i) . "','" .
                    /*$openFile->getMetaDescription($i) . "','" . $openFile->getMetaDescriptionCharSize($i) . "','" .*/
                    $openFile->getAllMetaRobotsSize($i) . "'," . $openFile->getAllMetaRobotsDataInCSV($i) .
                    $openFile->getAllHreflangSize($i) . "," . $openFile->getAllHreflangDataInCSV($i) . "'" .
                    $openFile->getAllCanonicalsSize($i) . "'," . $openFile->getAllCanonicalDataInCSV($i) . "'" .
                    $openFile->getAllLinksSize($i) . "'," . $openFile->getAllLinksDataInCSV($i) .
                    $openFile->getAllHeadDataInCSV($i) .
                    $openFile->getAllUserSelectorDataInCSV($i) .
                "\n");
                endfor;
                fclose($stream);
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

require_once './../views/footer.php';

session_destroy();