<?php

require_once './header.php';

?>

<div>
    <div>
        <br>
        <h2>Get your data with your token</h2><br><br>
    </div>
    <div>
        <form action="./../files/showScrapings.php" method="post">
            <p>Token<br>
            (Input the token you were given after launching a robot)</p>
            <input class="home_inputs" type="text" name="token"><br>
            <input type="submit" name="submit">
        </form>
    </div>
</div>


<?php

require_once './footer.php';