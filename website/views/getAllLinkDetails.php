<?php

require_once __DIR__ . '/header.php';
require '../controllers/OpenFileController.php';

use classes\OpenFileController;

if (isset($_GET)) :
    if (isset($_GET['filename'])) {
        @$json = file_get_contents($_GET['filename']);
        if ($json === false) {
            echo "The file you sent doesn't exist";
            return;
        }
        $json_data = json_decode($json, true);
        if ($json_data === null) {
            echo "The savefile format is not correct. Make sure there are no errors in the syntax.";
            return;
        }
        $openFile = new OpenFileController($json_data);
    } else {
        echo "The program encountered an error while opening the data file. Make sure you didn't delete the saved data.";
    }
?>

    <div id="table_container">
        <button class="copyTableButton" onclick="copyDetailsTable()">Copy Table contents</button>
        <table id="details_table">
            <tr>
                <th>Id</th>
                <th>Iteration</th>
                <th>URL</th>
                <th>Depth</th>
                <th>Times URL found</th>
                <th>Predecessor</th>
                <th>Status</th>
                <th>Load time (sec)</th>
                <th>Title <?= $openFile->addTabsToSizeCols(15) ?></th>
                <th>Title size</th>
                <th>Nb hreflang</th>
                <th>Canonical</th>
                <th>Nb links</th>
            </tr>

            <?php for ($objectNb = 1; $objectNb < $openFile->getObjectCount() - 1; $objectNb++): ?>
                <tr>
                    <td><?= $objectNb ?></td>
                    <td><?= $openFile->getIteration($objectNb) ?></td>
                    <td><a href="<?= $openFile->getUrl($objectNb) ?>"><?= $openFile->getUrl($objectNb) ?></a></td>
                    <td><?= $openFile->getUrlDepth($objectNb) ?></td>
                    <td><?= $openFile->getTimesUrlFound($objectNb) ?></td>
                    <td>
                        <a href="<?= $openFile->getUrlPredecessor($objectNb) ?>"><?= $openFile->getUrlPredecessor($objectNb) ?></a>
                        (<a href="<?= '/website/views/getLinkDetails.php/?object=' . $openFile->getObjectFromUrl($openFile->getUrlPredecessor($objectNb)) . '&filename=' . $_GET['filename'] ?>">details</a>)
                    </td>
                    <td><?= $openFile->getStatus($objectNb) ?></td>
                    <td><?= $openFile->getResponseTime($objectNb) ?></td>
                    <td><?= $openFile->getTitle($objectNb) ?></td>
                    <td><?= $openFile->getTitleSize($objectNb) ?></td>
                    <td><?= $openFile->getAllHreflangSize($objectNb) ?></td>
                    <td><?= $openFile->displayAllCanonicals($objectNb) ?></td>
                    <td><?= $openFile->getAllLinksSize($objectNb) ?></td>
                </tr>
                <?php endfor; ?>
        </table>
    </div>

    <script>
        function copyDetailsTable() {
            var copy = document.getElementById('details_table');

            window.getSelection().selectAllChildren(copy);
            document.execCommand('Copy');
        }
    </script>

<?php
else :
    echo "There has been an error while processing your request.
        Please try again and if the problem presists, contact the creator.";
    return;
endif;

require_once __DIR__ . '/footer.php';
