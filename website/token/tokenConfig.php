<?php

require_once '../views/header.php';

?>

<script>
    function unsetToken() {
        document.location.href = '/website/token/unsetToken.php';
    }

    function deleteToken() {
        document.location.href = '/website/token/deleteToken.php';
    }
</script>

<h1>Token settings</h1>

<p>When you input a token for the first time it will be saved in your computer until it's automatically removed after 8h.</p>
<p>If you delete your token, all data associated to it will be deleted.</p>
<p>Alternatively you can choose to change your token. This will simply ask you for your token next time you go to another page,
but the token will still be active and no files will be deleted.</p>
<br><br>

<?php if (isset($_COOKIE) && isset($_COOKIE['token'])): ?>

<div class="tokenButtonsBox">
    <button class="tokenButton" onclick="unsetToken()">Unset token</button>
    <button class="tokenButton" onclick="deleteToken()">Delete token data</button>
</div>

<?php else: ?>

<div class="tokenButtonsBox">
    <h3><b>You don't have any token set at the moment.</b></h3>
</div>

<?php

endif;

require_once '../views/footer.php';