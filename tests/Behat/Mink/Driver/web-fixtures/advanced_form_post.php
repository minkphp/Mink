<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Advanced form save</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<?php
    error_reporting(0);

    $_POST['agreement'] = ('1' === $_POST['agreement'] || 'on' === $_POST['agreement']) ? 'on' : 'off';
    print_r($_POST);
    if (file_exists($_FILES['about']['tmp_name'])) {
        echo file_get_contents($_FILES['about']['tmp_name']);
    } else {
        echo "no file";
    }

?>
</body>
</html>
