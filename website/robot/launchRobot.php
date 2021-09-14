<?php
require_once '../views/header.php';

set_time_limit(0);

if (isset($_POST)) {
    $query = 'node ../../scrapingRobot/main.js -D "' . $_POST['domain'] . '" ';

    if (isset($_POST['savefile']) && $_POST['savefile'] !== '')
        $query = $query . ' -S "' . $_POST['savefile'] . '" ';
    if (isset($_POST['include']) && $_POST['include'] !== '')
        $query = $query . ' -i "' . $_POST['include'] . '" ';
    if (isset($_POST['exclude']) && $_POST['exclude'] !== '')
        $query = $query . ' -x "' . $_POST['exclude'] . '" ';
    if (isset($_POST['userSelectors']) && $_POST['userSelectors'] !== '')
        $query = $query . ' -s "' . $_POST['userSelectors'] . '" ';
    if (isset($_POST['formSavefile']) && $_POST['formSavefile'] === 'on')
        $query = $query . ' -f ';
    if (isset($_POST['headless']) && $_POST['headless'] === 'on')
        $query = $query . ' -H ';
    if (!isset($_POST['allSelectors']) || !$_POST['allSelectors'] === 'on')
        $query = $query . ' -o ';

    $res = exec($query);

} else {
    echo "An unknown error has occured. Please, try again and if the problem persists contact the creator.";
}

require_once '../views/footer.php';