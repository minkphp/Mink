<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink;

require_once 'JavascriptDriverTest.php';

/**
 * @group seleniumdriver
 */
class SeleniumDriverTest extends JavascriptDriverTest
{
    protected static function registerMinkSessions(Mink $mink)
    {
        $mink->registerSession('selenium', static::initSeleniumSession(
            '*'.$_SERVER['WEB_FIXTURES_BROWSER'], $_SERVER['WEB_FIXTURES_HOST']
        ));

        parent::registerMinkSessions($mink);
    }

    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('selenium');
    }

    public function testMouseEvents() {} // Right click and blur are not supported

    public function testOtherMouseEvents()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');

        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());

        $clicker->mouseOver();
        $this->assertEquals('mouse overed', $clicker->getText());
    }
}
