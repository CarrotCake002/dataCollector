<?php

    require_once __DIR__ . '/header.php';
    require '../controllers/OpenFileController.php';

    use classes\OpenFileController;

    if (isset($_GET)):
        if (isset($_GET['object'])) {
            $objectNb = $_GET['object'];
        } else {
            echo "There has been an error obtaining the necessary data.
                Make sure the url syntax is correct and the url exists";
        }
        if (isset($_GET['filename'])) {
            @$json = file_get_contents('../../savefiles/' . $_GET['filename']);
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

        <table>
            <tr>
                <th>Iteration</th>
                <th>URL</th>
                <th>Depth</th>
                <th>Predecessor</th>
                <th>Status</th>
                <th>Load time</th>
                <th>Title</th>
                <th>Nb Meta description</th>
                <th>Meta description</th>
                <th>Nb hreflang</th>
                <th>hreflang</th>
                <th>Nb links</th>
                <th>Links</th>
                <th>Nb custom selectors</th>
                <th>Custom Selectors</th>
            </tr>
            <tr>
                <td><?= $openFile->getIteration($objectNb) ?></td>
                <td><a href="<?= $openFile->getUrl($objectNb) ?>"><?= $openFile->getUrl($objectNb) ?></a></td>
                <td><?= $openFile->getUrlDepth($objectNb) ?></td>
                <td>
                    <a href="<?= $openFile->getUrlPredecessor($objectNb)?>"><?= $openFile->getUrlPredecessor($objectNb)?></a><br>
                    (<a href="<?= '/website/views/getLinkDetails.php/?object=' . $openFile->getObjectFromUrl($openFile->getUrlPredecessor($objectNb)) . '&filename=' . $_GET['filename']?>">details</a>)
                </td>
                <td><?= $openFile->getStatus($objectNb) ?></td>
                <td><?= $openFile->getResponseTime($objectNb) ?></td>
                <td><?= $openFile->getTitle($objectNb) ?></td>
                <td><?= $openFile->getTitleSize($objectNb) ?></td>
                <td><?= $openFile->getAllMetaSize($objectNb) ?></td>
                <td class="array_display"><?= $openFile->displayAllMeta($objectNb) ?> </td>
                <td><?= $openFile->displayAllMetaCharSizes($objectNb) ?></td>
                <td><?= $openFile->getAllHreflangSize($objectNb) ?></td>
                <td class="array_display"><?= $openFile->displayAllHreflang($objectNb) ?></td>
                <td><?= $openFile->displayAllHreflangCharSizes($objectNb) ?></td>
                <td><?= $openFile->getAllLinksSize($objectNb) ?></td>
                <td class="array_display"><?= $openFile->displayAllLinks($objectNb) ?></td>
                <td><?= $openFile->displayAllLinkCharSizes($objectNb) ?></td>
                <td><?= $openFile->getAllUserSelectorCount($objectNb) ?></td>
                <td class="array_display"><?= $openFile->displayAllUserSelector($objectNb) ?></td>
            </tr>
        </table>

<?php
    else:
        echo "There has been an error while processing your request.
                Please try again and if the problem presists, contact the creator.";
        return;
    endif;

    require_once __DIR__ . '/footer.php';
