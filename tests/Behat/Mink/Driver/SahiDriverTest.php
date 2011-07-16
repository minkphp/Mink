<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\SahiDriver;

require_once 'JavascriptDriverTest.php';

class SahiDriverTest extends JavascriptDriverTest
{
    protected static function configureDriver()
    {
        return new SahiDriver($_SERVER['WEB_FIXTURES_BROWSER']);
    }
}
