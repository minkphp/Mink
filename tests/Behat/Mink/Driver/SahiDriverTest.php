<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink;

require_once 'JavascriptDriverTest.php';

/**
 * @group sahidriver
 */
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

    /**
     * @group issue131
     */
    public function testIssue131()
    {
        $this->getSession()->visit($this->pathTo('/issue131.php'));
        $page = $this->getSession()->getPage();

        $page->selectFieldOption('foobar', 'Gimme some accentués characters');
    }

    public function testPrepareXPath()
    {
        $driver = $this->getSession()->getDriver();

        // Make the method accessible for testing purposes
        $method = new \ReflectionMethod(
          'Behat\Mink\Driver\SahiDriver', 'prepareXPath'
        );
        $method->setAccessible(true);

        $this->assertEquals('No quotes', $method->invokeArgs($driver, array('No quotes')));
        $this->assertEquals("Single quote'", $method->invokeArgs($driver, array("Single quote'")));
        $this->assertEquals('Double quote\"', $method->invokeArgs($driver, array('Double quote"')));
    }

    // Sahi doesn't support iFrames switching
    public function testIFrame() {}

    // Sahi doesn't support window switching
    public function testWindow() {}
}
