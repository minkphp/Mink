<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

class ZombieDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        if (!$this->getMink()->hasSession('zombie')) {
            $this->getMink()->registerSession('zombie', static::initZombieSession());
            $this->getMink()->setDefaultSessionName('zombie');
        }
    }
}
