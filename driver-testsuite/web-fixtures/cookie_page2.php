<!DOCTYPE html>
<html>
<head>
    <title>Basic Form</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    Previous cookie: <?php echo isset($_COOKIE['srvr_cookie']) ? htmlspecialchars($_COOKIE['srvr_cookie'], ENT_QUOTES, 'UTF-8') : 'NO'; ?>
</body>
</html>
