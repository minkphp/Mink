<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

class SeleniumDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('selenium');
    }
}
