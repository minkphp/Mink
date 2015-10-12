<!DOCTYPE html>
<html>
<head>
    <title>Cookies page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
	<?php
	foreach ($_COOKIE as $key => $value) {
		unset($_COOKIE[$key]);
		$_COOKIE[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	?>
    <?php echo str_replace('>', '', var_export($_COOKIE, true)); ?>
</body>
</html>
