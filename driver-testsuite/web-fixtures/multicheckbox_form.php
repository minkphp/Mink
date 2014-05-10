<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Multicheckbox Test</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <h1>Multicheckbox Test</h1>

    <form method="POST" action="advanced_form_post.php">
        <input type="checkbox" name="mail_types[]" checked="checked" value="update"/>
        <input type="checkbox" name="mail_types[]" value="spam"/>

        <input type="submit" name="submit" value="Register" />
    </form>
</body>
</html>
