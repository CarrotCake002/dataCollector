<?php
require_once '../views/header.php';

set_time_limit(0);

if (isset($_POST)) {
    if (isset($_POST['domain']) && $_POST['domain'] !== '') {
    $query = 'node ../../scrapingRobot/main.js -D "' . $_POST['domain'] . '" ';
    } else {
        echo "Error: you need to specify a domain to scrape.";
        return;
    }
    if (isset($_POST['savefile']) && $_POST['savefile'] !== '')
        $query = $query . ' -f "' . $_POST['savefile'] . '" ';
    if (isset($_POST['includeEntering']) && $_POST['includeEntering'] !== '')
        $query = $query . ' -i "' . $_POST['includeEntering'] . '" ';
    if (isset($_POST['excludeEntering']) && $_POST['excludeEntering'] !== '')
        $query = $query . ' -x "' . $_POST['excludeEntering'] . '" ';
    if (isset($_POST['includeSaving']) && $_POST['includeSaving'] !== '')
        $query = $query . ' -sL "' . $_POST['includeSaving'] . '" ';
    if (isset($_POST['excludeSaving']) && $_POST['excludeSaving'] !== '')
        $query = $query . ' -nL "' . $_POST['excludeSaving'] . '" ';
    if (isset($_POST['userSelectors']) && $_POST['userSelectors'] !== '')
        $query = $query . ' -s "' . $_POST['userSelectors'] . '" ';
    if (isset($_POST['maxDepth']) && $_POST['maxDepth'] !== '')
        $query = $query . ' -d "' . $_POST['maxDepth'] . '" ';
    if (isset($_POST['clickItems']) && $_POST['clickItems'] !== '')
        $query = $query . ' -c "' . $_POST['clickItems'] . '" ';
    if (isset($_POST['sitemapLink']) && $_POST['sitemapLink'] !== '')
        $query = $query . ' -m "' . $_POST['sitemapLink'] . '" ';
    if (isset($_POST['startingUrl']) && $_POST['startingUrl'] !== '')
        $query = $query . ' -u "' . $_POST['startingUrl'] . '" ';
    if (isset($_POST['formSavefile']) && $_POST['formSavefile'] === 'on')
        $query = $query . ' -f ';
    if (isset($_POST['headless']) && $_POST['headless'] === 'on')
        $query = $query . ' -H ';
    if (isset($_POST['allSelectors']) && $_POST['allSelectors'] === 'on')
        $query = $query . ' -o ';
    if (isset($_POST['getLinkArticle']) && $_POST['getLinkArticle'] === 'on')
        $query = $query . ' -gArticle ';
    if (isset($_POST['getMeta']) && $_POST['getMeta'] === 'on')
        $query = $query . ' -gMeta ';
    if (isset($_POST['getHeads']) && $_POST['getHeads'] === 'on')
        $query = $query . ' -gHeads ';
    if (isset($_POST['getHreflang']) && $_POST['getHreflang'] === 'on')
        $query = $query . ' -gHreflang ';
    if (isset($_POST['getCanonical']) && $_POST['getCanonical'] === 'on')
        $query = $query . ' -gCanonical ';
    if (isset($_POST['getTitle']) && $_POST['getTitle'] === 'on')
        $query = $query . ' -gTitle ';
    $res = exec($query);
    echo $res;

} else {
    echo "An unknown error has occured. Please, try again and if the problem persists contact the creator.";
}

isset($_POST['savefile']) && $_POST['savefile'] !== '' ? $savefile = $_POST['savefile'] : $savefile = "default";

?>
<br><br>
<a href="<?= "/savefiles/" . $savefile . ".json"; ?>" download="<?= $savefile ?>">Download your data!</a>

<?php
require_once '../views/footer.php';