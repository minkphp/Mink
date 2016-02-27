<!DOCTYPE html>
<html>
<head>
    <title>Basic Form</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    Previous cookie: <?php
        require_once 'utils.php';
        echo isset($_COOKIE['srvr_cookie']) ? html_escape_value($_COOKIE['srvr_cookie']) : 'NO';
    ?>
</body>
</html>
