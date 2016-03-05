<!DOCTYPE html>
<html>
<head>
    <title>Cookies page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <?php
        require_once 'utils.php';
        echo str_replace('>', '', var_export(html_escape_value($_COOKIE), true));
    ?>
</body>
</html>
