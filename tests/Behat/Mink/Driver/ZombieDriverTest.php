<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\ZombieDriver;

require_once 'JavascriptDriverTest.php';

class ZombieDriverTest extends JavascriptDriverTest
{
    protected static function configureDriver()
    {
        return new ZombieDriver();
    }
}

