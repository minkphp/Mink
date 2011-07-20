<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

class SahiDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $browser = $_SERVER['WEB_FIXTURES_BROWSER'];

        if (!$this->getMink()->hasSession('sahi')) {
            $this->getMink()->registerSession('sahi', static::initSahiSession($browser));
            $this->getMink()->setDefaultSessionName('sahi');
        }
    }
}
