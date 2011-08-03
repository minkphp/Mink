<?php

namespace Tests\Behat\Mink\Driver;

require_once 'HeadlessDriverTest.php';

class GoutteDriverTest extends HeadlessDriverTest
{
    protected function setUp()
    {
        if (!$this->getMink()->hasSession('goutte')) {
            $this->getMink()->registerSession('goutte', static::initGoutteSession());
            $this->getMink()->setDefaultSessionName('goutte');
        }
    }
}
