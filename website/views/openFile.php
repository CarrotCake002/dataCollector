<?php
    require_once 'header.php';
    require_once 'footer.php';
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
        <form action="../robot/getRobotResults.php" method="post" enctype="multipart/form-data">
            <input id="openFile" type="file" name="openFile">
            <input type="submit" name="submit">
        </form>
    </div>
</div>

<?php
?>