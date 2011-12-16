<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

/**
 * @group seleniumdriver
 */
class Selenium2DriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('selenium2');
    }

    public function testMouseEvents()
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
    
    public function testKeyboardEvents() {} // split up into seperate tests
    
    public function _testKeyPressEvents()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $input1 = $this->getSession()->getPage()->find('css', '.elements input.input.first');
        $input2 = $this->getSession()->getPage()->find('css', '.elements input.input.second');
        $input3 = $this->getSession()->getPage()->find('css', '.elements input.input.third');
        $event  = $this->getSession()->getPage()->find('css', '.elements .text-event');

        $input2->keyPress('r');
        $this->assertEquals('key pressed:114 / 0', $event->getText());

        sleep(100);
        $input2->keyPress('r', 'alt');
        $this->assertEquals('key pressed:114 / 1', $event->getText());
    }

    public function testDeepTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $traversDiv = $this->getSession()->getPage()->findAll('css', 'div.travers');

        $this->assertEquals(1, count($traversDiv));
        $traversDiv = $traversDiv[0];

        $subDivs = $traversDiv->findAll('css', 'div.sub');
        $this->assertEquals(3, count($subDivs));

        $this->assertTrue($subDivs[2]->hasLink('some deep url'));
        $this->assertFalse($subDivs[2]->hasLink('come deep url'));
        $subUrl = $subDivs[2]->findLink('some deep url');
        $this->assertNotNull($subUrl);

        $base = $_SERVER['WEB_FIXTURES_HOST'] . '/';
        $this->assertEquals($base.'some_url', $subUrl->getAttribute('href'));
        $this->assertEquals('some deep url', $subUrl->getText());
        //$this->assertEquals('some <strong>deep</strong> url', $subUrl->getHtml());
        $this->assertTrue($subUrl->has('css', 'strong'));
        $this->assertFalse($subUrl->has('css', 'em'));
        $this->assertEquals('deep', $subUrl->find('css', 'strong')->getText());
    }

}
