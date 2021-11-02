<?php
    require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/header.php';
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