<?php

namespace Tests\Behat\Mink\Driver;

require_once 'GeneralDriverTest.php';

abstract class JavascriptDriverTest extends GeneralDriverTest
{
    public function testAriaRoles()
    {
        $this->getSession()->visit($this->pathTo('/aria_roles.php'));

        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === false');
        $this->getSession()->getPage()->pressButton('Toggle');
        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === true');

        $this->getSession()->getPage()->clickLink('Go to Index');
        $this->assertEquals($this->pathTo('/index.php'), $this->getSession()->getCurrentUrl());
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

        $clicker->rightClick();
        $this->assertEquals('right clicked', $clicker->getText());

        $clicker->focus();
        $this->assertEquals('focused', $clicker->getText());

        $clicker->blur();
        $this->assertEquals('blured', $clicker->getText());

        $clicker->mouseOver();
        $this->assertEquals('mouse overed', $clicker->getText());
    }

    public function testKeyboardEvents()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $input1 = $this->getSession()->getPage()->find('css', '.elements input.input.first');
        $input2 = $this->getSession()->getPage()->find('css', '.elements input.input.second');
        $input3 = $this->getSession()->getPage()->find('css', '.elements input.input.third');
        $event  = $this->getSession()->getPage()->find('css', '.elements .text-event');

        $input1->keyDown('u');
        $this->assertEquals('key downed:0', $event->getText());

        $input1->keyDown('u', 'alt');
        $this->assertEquals('key downed:1', $event->getText());

        $input2->keyPress('r');
        $this->assertEquals('key pressed:114 / 0', $event->getText());

        $input2->keyPress('r', 'alt');
        $this->assertEquals('key pressed:114 / 1', $event->getText());

        $input3->keyUp(78);
        $this->assertEquals('key upped:78 / 0', $event->getText());

        $input3->keyUp(78, 'alt');
        $this->assertEquals('key upped:78 / 1', $event->getText());
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
}
