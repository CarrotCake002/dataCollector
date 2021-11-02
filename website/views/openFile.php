<?php
    require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/header.php';

    $savefilesData = scandir($_SERVER['DOCUMENT_ROOT'] . "/savefiles/");

    for ($i = 0; $i < count($savefilesData); $i++) {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/savefiles/" . $savefilesData[$i]) && !strpos($_SERVER['DOCUMENT_ROOT'] . "/savefiles/" . $savefilesData[$i], ".")) {
            echo $savefilesData[$i] . " --> " . date("d/m/y G:i:s", filemtime($_SERVER['DOCUMENT_ROOT'] . "/savefiles/" . $savefilesData[$i]));
            $dirDate = date_create(date("d-m-y G:i:s", filemtime($_SERVER['DOCUMENT_ROOT'] . "/savefiles/" . $savefilesData[$i])));
        }
        echo "<br>";
    }
    echo " --> " . date("d/m/y G:i:s");
    $dateInterval = date_diff(date_create(date("d-m-y G:i:s")), $dirDate);
    echo "<br>";
    var_dump($dateInterval);
    if ($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0 || $dateInterval->h > 8)
        echo "<br>bigger";
    die;
?>

<script>
    const xhttp = new XMLHttpRequest();
    xhttp.open("GET", "deleteFiles.php", true);
    xhttp.send();
</script>

<div>
    <div id="resultsh2">
        <br>
        <h2>Which file do you want to open?</h2>
    </div>
    <div>
        <form action="/website/robot/getRobotResults.php" method="post" enctype="multipart/form-data">
            <input id="openFile" type="file" name="openFile">
            <input type="submit" name="submit">
        </form>
    </div>
</div>

<?php
    require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/footer.php';
?>