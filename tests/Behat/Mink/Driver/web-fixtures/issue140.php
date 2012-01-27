<?php if (!empty($_POST)) {
    setcookie ("tc", $_POST['cookie_value']);
}
else if (isset($_GET["show_value"])) {
    echo $_COOKIE["tc"];
    die();
} ?>
<form method="post">
    <input name="cookie_value">
    <input type="submit" value="Set cookie">
</form>
