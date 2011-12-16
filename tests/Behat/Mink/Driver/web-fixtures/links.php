<?php

$base = (isset($_SERVER['WEB_FIXTURES_HOST'])) ? $_SERVER['WEB_FIXTURES_HOST'] : preg_replace('/^(.*)\/links.php/U', '\1', $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Links page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <a href="<?php echo $base; ?>/redirector.php">Redirect me to</a>
    <a href="<?php echo $base; ?>/randomizer.php">Random number page</a>
    <a href="<?php echo $base; ?>/basic_form.php">
        <img src="basic_form" alt="basic form image"/>
    </a>
</body>
</html>
