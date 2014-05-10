<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class EventsTest extends TestCase
{
    /**
     * @group mouse-events
     */
    public function testClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertNotNull($clicker);
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testDoubleClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertNotNull($clicker);
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testRightClick()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $this->assertNotNull($clicker);
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->rightClick();
        $this->assertEquals('right clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testFocus()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getSession()->getPage()->find('css', '.elements input#focus-blur-detector');
        $this->assertNotNull($focusBlurDetector);
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
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getSession()->getPage()->find('css', '.elements input#focus-blur-detector');
        $this->assertNotNull($focusBlurDetector);
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->blur();
        $this->assertEquals('blured', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     */
    public function testMouseOver()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $mouseOverDetector = $this->getSession()->getPage()->find('css', '.elements div#mouseover-detector');
        $this->assertNotNull($mouseOverDetector);
        $this->assertEquals('no mouse action detected', $mouseOverDetector->getText());

        $mouseOverDetector->mouseOver();
        $this->assertEquals('mouse overed', $mouseOverDetector->getText());
    }

    /**
     * @dataProvider provideKeyboardEventsModifiers
     */
    public function testKeyboardEvents($modifier, $eventProperties)
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));

        $input1 = $this->getSession()->getPage()->find('css', '.elements input.input.first');
        $input2 = $this->getSession()->getPage()->find('css', '.elements input.input.second');
        $input3 = $this->getSession()->getPage()->find('css', '.elements input.input.third');
        $event  = $this->getSession()->getPage()->find('css', '.elements .text-event');

        $this->assertNotNull($input1);
        $this->assertNotNull($input2);
        $this->assertNotNull($input3);
        $this->assertNotNull($event);

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
}
