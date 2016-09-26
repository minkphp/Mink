<!DOCTYPE html>
<html>
<head>
    <title>Headers page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
<?php
foreach($_SERVER as $serverKey => $serverValue){
  echo sprintf("<p>%s=%s</p>", $serverKey, $serverValue);
}
?>
</body>
</html>
