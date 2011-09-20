<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink;

require_once 'JavascriptDriverTest.php';

class WebDriverDriverTest extends JavascriptDriverTest
{
    protected static function registerMinkSessions(Mink $mink)
    {
        $mink->registerSession('webdriver',
               static::initWebDriverSession($_SERVER['WEB_FIXTURES_BROWSER']));

        parent::registerMinkSessions($mink);
    }

    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('webdriver');
    }
}
