<?php
require_once '../views/header.php';

set_time_limit(0);

$query = "node ../../scrapingRobot/main.js -D " . $_POST['domain'] . " -a -f";

if (isset($_POST['savefile']) && $_POST['savefile'] !== '')
    $query = $query . " -S " . $_POST['savefile'];
if (isset($_POST['include']) && $_POST['include'] !== '')
    $query = $query . " -i " . $_POST['include'];
if (isset($_POST['exclude']) && $_POST['exclude'] !== '')
    $query = $query . " -x " . $_POST['exclude'];
if (isset($_POST['userSelectors']) && $_POST['userSelectors'] !== '')
    $query = $query . " -s " . $_POST['userSelectors'];

echo $query;

$res = exec($query);

echo "<br><br><br><br><br>" . $res;

require_once '../views/footer.php';