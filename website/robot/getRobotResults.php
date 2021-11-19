<?php


require_once './../views/header.php';
require './../controllers/OpenFileController.php';
require './../controllers/SessionController.php';
//require './../controllers/SpreadsheetController.php';

use classes\OpenFileController;
use classes\SessionController;
//use classes\SpreadsheetController;

set_time_limit(0);

if (isset($_POST) && isset($_POST['token']) && $_POST['token'] != '') {
    if (!isset($_COOKIE['token']))
        setcookie('token', $_POST['token'], time() + 28800000, '/');
    $session = new SessionController($_POST['token']);
    if ($session->error) {
        echo 'Error: the token you sent is invalid';
        return;
    }
} else {
    echo "Error: make sure you've set your personal token";
    return;
}

var_dump('die');die;

session_start();

if (isset($_FILES)):
    $json_data = null;
    if (isset($_FILES['openFile']) && isset($_FILES['openFile']['tmp_name'])) {
        if (!$session->checkSessionFolderExists()) {
            echo "Error: the token you sent is invalid.";
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

    $sheet = new SpreadsheetController('robotResults');
    $stream = fopen($session->getSessionFolderPath() . '/' . str_replace('.json', '.csv', $_FILES['openFile']['name']), 'w');
?>

<div id="tableBlock">
        <form action="/website/views/getAllLinkDetails.php" method="GET">
            <input type="text" name="filename" id="filename" value="<?= $filePath ?>">
            <input type="submit" name="submit" value="Get all link details">
        </form>
        <button class="copyTableButton" onclick="copyLinksTable()">Copy Table contents</button>
        <a href="<?= './../../savefiles/' . $session->getSessionFolderName() . '/' . $sheet->sheetName . '.xlsx' ?>" download="<?= $sheet->sheetName ?>" class="downloadDataExcel" ><br><br>Download an excel with all data!</a>
        <table id="allLinksTable">
            <tr>
                <th>Id<?= $sheet->setCellValue('A1', 'Id') ?></th>
                <th>Iteration<?= $sheet->setCellValue('B1', 'Iteration') ?></th>
                <th>URL<?= $sheet->setCellValue('C1', 'Url') ?></th>
                <th>Depth<?= $sheet->setCellValue('D1', 'Depth') ?></th>
                <th>Status<?= $sheet->setCellValue('E1', 'Status') ?></th>
                <th>Nb links found<?= $sheet->setCellValue('F1', 'Nb links found') ?></th>
                <th>More details</th>
            </tr>

            <?php
                fputcsv($stream, ['Id',
                'Iteration',
                'URL',
                'Depth',
                'Times URL found',
                'Predecessor',
                'Status',
                'Load time',
                'Title', 'Title size',
                'Nb Meta tags', 'Meta description', 'Meta description size',
                'Meta tags', 'Meta tags sizes', 'Meta index', 'Meta follow', 'Meta sponsored', 'Meta ugc', 'Meta noopener',
                'Nb hreflang', 'Hreflang',
                'Canonical',
                'Nb links', 'Links', 'Link <a> tags', 'Link target=blank',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'Custom selectors']);
                $objectCount = $openFile->getObjectCount();

                if ($objectCount < 1) {
                    echo "<br><br><b>There is no data to display. Make sure you accessed the correct file </b>";
                    die;
                }
                for ($i = 1; $i <= $objectCount; $i++): ?>

            <tr>
                <td><?= $i; $sheet->setCellValue('A' . $i + 1, $i) ?></td>
                <td><?= $openFile->getIteration($i); $sheet->setCellValue('B' . $i + 1, $openFile->getIteration($i)) ?></td>
                <td><a href="<?= $openFile->getURl($i) ?>"><?= $openFile->getUrl($i); $sheet->setCellValue('C' . $i + 1, $openFile->getUrl($i)) ?></a></td>
                <td><?= $openFile->getUrlDepth($i); $sheet->setCellValue('D' . $i + 1, $openFile->getUrlDepth($i)) ?></td>
                <td><?= $openFile->getStatus($i); $sheet->setCellValue('E' . $i + 1, $openFile->getStatus($i)) ?></td>
                <td><?= $openFile->getAllLinksSize($i); $sheet->setCellValue('F' . $i + 1, $openFile->getAllLinksSize($i)) ?></td>
                <td><a href="<?= '/website/views/getLinkDetails.php/?object=' . $i . '&filename=' . $filePath ?>">Click for more details</a></td>
            </tr>
            <?php
                fputcsv($stream,[
                    $i,
                    $openFile->getIteration($i),
                    $openFile->getUrl($i),
                    $openFile->getUrlDepth($i),
                    $openFile->getTimesUrlFound($i),
                    $openFile->getUrlPredecessor($i),
                    $openFile->getStatus($i),
                    $openFile->getResponseTime($i),
                    $openFile->getTitle($i), $openFile->getTitleSize($i),
                    $openFile->getAllMetaSize($i), $openFile->getMetaDescription($i), $openFile->getMetaDescriptionCharSize($i),
                    $openFile->getAllMetaInStr($i), $openFile->getAllMetaSizesInStr($i), $openFile->getMetaIndexInStr($i), $openFile->getMetaFollowInStr($i),
                    $openFile->getMetaSponsoredInStr($i), $openFile->getMetaUgcInStr($i), $openFile->getMetaNoopenerInStr($i), $openFile->getAllHreflangSize($i),
                    $openFile->getAllHreflangInStr($i), $openFile->getAllCanonicalsInStr($i), $openFile->getAllLinksSize($i), $openFile->getAllLinksInStr($i),
                    $openFile->getAllLinkArticleInStr($i), $openFile->getAllLinkTargetBlankInStr($i), $openFile->getAllHeadsTypeInStr($i, 0),
                    $openFile->getAllHeadsTypeInStr($i, 1), $openFile->getAllHeadsTypeInStr($i, 2), $openFile->getAllHeadsTypeInStr($i, 3),
                    $openFile->getAllHeadsTypeInStr($i, 4), $openFile->getAllHeadsTypeInStr($i, 5), $openFile->getAllUserSelectorsInStr($i)
                ], ',', '"', '\\');
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
$sheet->saveSpreadSheet($session->getSessionFolderPath() . '/');
endif;

require_once './../views/footer.php';

session_destroy();