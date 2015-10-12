<!DOCTYPE html>
<html>
<head>
    <title>Advanced form save</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<?php
error_reporting(0);

if (isset($_POST['select_multiple_numbers']) && false !== strpos($_POST['select_multiple_numbers'][0], ',')) {
    $_POST['select_multiple_numbers'] = explode(',', $_POST['select_multiple_numbers'][0]);
}

$_POST['agreement'] = isset($_POST['agreement']) ? 'on' : 'off';
foreach ($_POST as $key => $value) {
	unset($_POST[$key]);
	$_POST[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
ksort($_POST);
echo str_replace('>', '', var_export($_POST, true)) . "\n";
if (isset($_FILES['about']) && file_exists($_FILES['about']['tmp_name'])) {
    echo htmlspecialchars($_FILES['about']['name'], ENT_QUOTES, 'UTF-8') . "\n";
    echo htmlspecialchars(file_get_contents($_FILES['about']['tmp_name'], ENT_QUOTES, 'UTF-8'));
} else {
    echo "no file";
}
?>
</body>
</html>
