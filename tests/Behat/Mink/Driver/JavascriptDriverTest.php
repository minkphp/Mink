<?php

namespace Tests\Behat\Mink\Driver;

require_once __DIR__ . '/GeneralDriverTest.php';

abstract class JavascriptDriverTest extends GeneralDriverTest
{
    public function testIFrame()
    {
        $this->getSession()->visit($this->pathTo('/iframe.php'));
        $page = $this->getSession()->getPage();

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());

        $this->getSession()->switchToIFrame('subframe');

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('iFrame div text', $el->getText());

        $this->getSession()->switchToIFrame();

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());
    }

    public function testWindow()
    {
        $this->getSession()->visit($this->pathTo('/window.php'));
        $session = $this->getSession();
        $page    = $session->getPage();

        $page->clickLink('Popup #1');
        $session->switchToWindow(null);

        $page->clickLink('Popup #2');
        $session->switchToWindow(null);

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());

        $session->switchToWindow('popup_1');
        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Popup#1 div text', $el->getText());

        $session->switchToWindow('popup_2');
        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Popup#2 div text', $el->getText());

        $session->switchToWindow(null);
        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());
    }

    public function testGetWindowNames()
    {
        $this->getSession()->visit($this->pathTo('/window.php'));
        $session = $this->getSession();
        $page    = $session->getPage();

        $windowName = $this->getSession()->getWindowName();

        $this->assertNotNull($windowName);

        $page->clickLink('Popup #1');
        $page->clickLink('Popup #2');

        $windowNames = $this->getSession()->getWindowNames();

        $this->assertNotNull($windowNames[0]);
        $this->assertNotNull($windowNames[1]);
        $this->assertNotNull($windowNames[2]);
    }

    public function testAriaRoles()
    {
        $this->getSession()->visit($this->pathTo('/aria_roles.php'));

        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === false');
        $this->getSession()->getPage()->pressButton('Toggle');
        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === true');

        $this->getSession()->getPage()->clickLink('Go to Index');
        $this->assertEquals($this->pathTo('/index.php'), $this->getSession()->getCurrentUrl());
    }

    /**
     * Tests, that `wait` method returns check result after exit.
     */
    public function testWaitReturnValue()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $found = $this->getSession()->wait(5000, '$("#draggable").length == 1');
        $this->assertTrue($found);
    }

    /**
     * @group mouse-events
     */
    public function testClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testDoubleClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testRightClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->rightClick();
        $this->assertEquals('right clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testFocus()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $focusBlurDetector = $this->getSession()->getPage()->find('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->focus();
        $this->assertEquals('focused', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     * @depends testFocus
     */
    public function testBlur()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $focusBlurDetector = $this->getSession()->getPage()->find('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->blur();
        $this->assertEquals('blured', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     */
    public function testMouseOver()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));
        $mouseOverDetector = $this->getSession()->getPage()->find('css', '.elements div#mouseover-detector');
        $this->assertEquals('no mouse action detected', $mouseOverDetector->getText());

        $mouseOverDetector->mouseOver();
        $this->assertEquals('mouse overed', $mouseOverDetector->getText());
    }

    /**
     * @dataProvider provideKeyboardEventsModifiers
     */
    public function testKeyboardEvents($modifier, $eventProperties)
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $input1 = $this->getSession()->getPage()->find('css', '.elements input.input.first');
        $input2 = $this->getSession()->getPage()->find('css', '.elements input.input.second');
        $input3 = $this->getSession()->getPage()->find('css', '.elements input.input.third');
        $event  = $this->getSession()->getPage()->find('css', '.elements .text-event');

        $input1->keyDown('u', $modifier);
        $this->assertEquals('key downed:' . $eventProperties, $event->getText());

        $input2->keyPress('r', $modifier);
        $this->assertEquals('key pressed:114 / ' . $eventProperties, $event->getText());

        $input3->keyUp(78, $modifier);
        $this->assertEquals('key upped:78 / ' . $eventProperties, $event->getText());
    }

    public function provideKeyboardEventsModifiers()
    {
        return array(
            'none' => array(null, '0 / 0 / 0 / 0'),
            'alt' => array('alt', '1 / 0 / 0 / 0'),
            'ctrl' => array('ctrl', '0 / 1 / 0 / 1'), // jQuery considers ctrl as being a metaKey in the normalized event
            'shift' => array('shift', '0 / 0 / 1 / 0'),
            'meta' => array('meta', '0 / 0 / 0 / 1'),
        );
    }

    public function testWait()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $this->getSession()->getPage()->findById('waitable')->click();
        $this->getSession()->wait(3000, '$("#waitable").has("div").length > 0');
        $this->assertEquals('arrived', $this->getSession()->getPage()->find('css', '#waitable > div')->getText());

        $this->getSession()->getPage()->findById('waitable')->click();
        $this->getSession()->wait(3000, 'false');
        $this->assertEquals('timeout', $this->getSession()->getPage()->find('css', '#waitable > div')->getText());
    }

    public function testVisibility()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $clicker   = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $invisible = $this->getSession()->getPage()->find('css', '#invisible');

        $this->assertFalse($invisible->isVisible());
        $this->assertTrue($clicker->isVisible());
    }

    public function testDragDrop()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $draggable = $this->getSession()->getPage()->find('css', '#draggable');
        $droppable = $this->getSession()->getPage()->find('css', '#droppable');

        $draggable->dragTo($droppable);
        $this->assertEquals('Dropped!', $droppable->find('css', 'p')->getText());
    }

    public function testIssue193()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue193.html'));

        $session->getPage()->selectFieldOption('options-without-values', 'Two');
        $this->assertEquals('Two', $session->getPage()->findById('options-without-values')->getValue());

        $session->getPage()->selectFieldOption('options-with-values', 'two');
        $this->assertEquals('two', $session->getPage()->findById('options-with-values')->getValue());
    }

    public function testIssue225()
    {
        $this->getSession()->visit($this->pathTo('/issue225.php'));
        $this->getSession()->getPage()->pressButton('Créer un compte');
        $this->getSession()->wait(5000, '$("#panel").text() != ""');

        $this->assertContains('OH AIH!', $this->getSession()->getPage()->getText());
    }

    /**
     * 'change' event should be fired after selecting an <option> in a <select>
     */
    public function testIssue255()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue255.php'));

        $session->getPage()->selectFieldOption('foo_select', 'Option 3');

        $session->wait(2000, '$("#output_foo_select").text() != ""');
        $this->assertEquals('onChangeSelect', $session->getPage()->find('css', '#output_foo_select')->getText());
    }

    /**
     * @dataProvider provideExecutedScript
     */
    public function testExecuteScript($script)
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->getSession()->executeScript($script);

        sleep(1);

        $heading = $this->getSession()->getPage()->find('css', 'h1');
        $this->assertEquals('Hello world', $heading->getText());
    }

    public function provideExecutedScript()
    {
        return array(
            array('document.querySelector("h1").textContent = "Hello world"'),
            array('document.querySelector("h1").textContent = "Hello world";'),
            array('function () {document.querySelector("h1").textContent = "Hello world";}()'),
            array('function () {document.querySelector("h1").textContent = "Hello world";}();'),
            array('(function () {document.querySelector("h1").textContent = "Hello world";})()'),
            array('(function () {document.querySelector("h1").textContent = "Hello world";})();'),
        );
    }

    /**
     * @dataProvider provideEvaluatedScript
     */
    public function testEvaluateJavascript($script)
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->assertSame(2, $this->getSession()->evaluateScript($script));
    }

    public function provideEvaluatedScript()
    {
        return array(
            array('1 + 1'),
            array('1 + 1;'),
            array('return 1 + 1'),
            array('return 1 + 1;'),
            array('function () {return 1+1;}()'),
            array('(function () {return 1+1;})()'),
            array('return function () { return 1+1;}()'),
            array('return (function () {return 1+1;})()'),
        );
    }

    public function testWindowMaximize()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $session = $this->getSession();
        $driver = $session->getDriver();

        $driver->maximizeWindow();
        $driver->wait(1000, false);

        $script = "return Math.abs(screen.availHeight - window.outerHeight) <= 100;";

        $this->assertTrue($session->evaluateScript($script));
    }
}
