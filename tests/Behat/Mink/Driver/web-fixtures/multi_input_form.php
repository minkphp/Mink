<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Multi input Test</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <h1>Multi input Test</h1>

    <form method="POST" action="advanced_form_post.php">
        <label>
            First
            <input type="text" name="tags[]" value="tag1">
        </label>
        <label>
            Second
            <input type="text" name="tags[]" value="tag2">
        </label>
        <label>
            Third
            <input type="text" name="tags[]" value="tag1">
        </label>

        <input type="submit" name="submit" value="Register" />
    </form>
</body>
</html>
