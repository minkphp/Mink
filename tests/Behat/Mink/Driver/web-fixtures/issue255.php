<!DOCTYPE html>
<html lang="en">
<head>
    <title>Issue 255</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <script src="js/jquery-1.6.2-min.js"></script>
</head>
<body>
    <form>
        <label for="foo_select">Foo</label>
        <select name="foo_select" id="foo_select">
            <option value="1" selected="selected" type="text">Option 1</option>
            <option value="2" type="text">Option 2</option>
            <option value="3" type="text">Option 3</option>
        </select>
        <input type="checkbox" id="foo_check" />
        <input type="submit" />
        <p id="output_foo_select"></p>
        <p id="output_foo_check"></p>
    </form>

    <script type="text/javascript">
        (function() {
            $('#foo_select').change(function () {
                $('#output_foo_select').text("onChangeSelect");
            });
            $('#foo_check').change(function () {
                $('#output_foo_check').text("onChangeCheck");
            });
        })();
    </script>
</body>
</html>
