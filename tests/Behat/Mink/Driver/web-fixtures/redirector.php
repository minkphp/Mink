<?php

$base = (isset($_SERVER['WEB_FIXTURES_HOST'])) ? $_SERVER['WEB_FIXTURES_HOST'] : preg_replace('/^(.*)\/redirector.php/U', '\1', $_SERVER['REQUEST_URI']);
header('location: '.$base.'/redirect_destination.php');
