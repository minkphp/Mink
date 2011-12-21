<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

/**
 * @group selenium2driver
 */
class Selenium2DriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('webdriver');
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
