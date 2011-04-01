<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Session;

require_once 'DriverTest.php';

class SahiDriverTest extends DriverTest
{
    protected static function configureDriver()
    {
        return new SahiDriver(static::$host . '/index.php', $_SERVER['WEB_FIXTURES_BROWSER']);
    }
}
