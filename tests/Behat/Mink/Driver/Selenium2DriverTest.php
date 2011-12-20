<?php

namespace Tests\Behat\Mink\Driver;

require_once 'SeleniumDriverTest.php';

/**
 * @group selenium2driver
 */
class Selenium2DriverTest extends SeleniumDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('selenium2');
    }

}
