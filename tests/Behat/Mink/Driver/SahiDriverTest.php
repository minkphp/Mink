<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink;

require_once 'JavascriptDriverTest.php';

class SahiDriverTest extends JavascriptDriverTest
{
    protected static function registerMinkSessions(Mink $mink)
    {
        $mink->registerSession('sahi', static::initSahiSession($_SERVER['WEB_FIXTURES_BROWSER']));

        parent::registerMinkSessions($mink);
    }

    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('sahi');
    }
}
