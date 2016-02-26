<!DOCTYPE html>
<html>
<head>
    <title>Basic Form Saving</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <h1>Anket for <?php echo htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8') ?></h1>
    <span id="first">Firstname: <?php echo htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8') ?></span>
    <span id="last">Lastname: <?php echo htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8') ?></span>
</body>
</html>
