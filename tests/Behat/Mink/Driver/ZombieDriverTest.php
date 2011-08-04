<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

class ZombieDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('zombie');
    }
}
