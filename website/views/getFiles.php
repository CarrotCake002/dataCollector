<script src="../js/getFiles.js"></script>

<?php

require_once './header.php';

?>

<div>
    <div>
        <br>
        <h2 class="formBlock" >Get your data with your token</h2><br><br>
    </div>
    <div class="formBlock">
        <form action="./../files/showScrapings.php" method="post">
            <p >Token<br>
            (Input the token you were given after launching a robot)</p>
            <?php if (isset($_COOKIE['token'])):?>
                <input class="home_inputs" type="text" name="token" value="<?=$_COOKIE['token']?>">
            <?php else: ?>
                <input class="home_inputs" type="text" name="token"><br>
            <?php endif; ?>
            <input type="submit" name="submit" id="getFilesForm">
        </form>
    </div>
</div>

<?php

if (isset($_COOKIE) && isset($_COOKIE['token'])):?>
    <script> submitForm(); </script>
<?php else: ?>
    <script> showForm(); </script>
<?php endif;

require_once './footer.php';