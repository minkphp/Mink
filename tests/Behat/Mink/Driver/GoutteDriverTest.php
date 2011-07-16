<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\GoutteDriver;

require_once 'HeadlessDriverTest.php';

class GoutteDriverTest extends HeadlessDriverTest
{
    protected static function configureDriver()
    {
        return new GoutteDriver();
    }
}
