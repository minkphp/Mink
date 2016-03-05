<!DOCTYPE html>
<html>
<head>
    <title>Basic Form Saving</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <?php
        require_once 'utils.php';
    ?>
    <h1>Anket for <?php echo html_escape_value($_POST['first_name']) ?></h1>

    <span id="first">Firstname: <?php echo html_escape_value($_POST['first_name']) ?></span>
    <span id="last">Lastname: <?php echo html_escape_value($_POST['last_name']) ?></span>
</body>
</html>
