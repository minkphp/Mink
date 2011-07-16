<?php

namespace Tests\Behat\Mink\Driver;

require_once 'GeneralDriverTest.php';

abstract class JavascriptDriverTest extends GeneralDriverTest
{
    public function testAriaRoles()
    {
        static::$session->visit(static::$host . '/aria_roles.php');

        static::$session->wait(5000, '$("#toggle-element").is(":visible") === false');
        static::$session->getPage()->pressButton('Toggle');
        static::$session->wait(5000, '$("#toggle-element").is(":visible") === true');

        static::$session->getPage()->clickLink('Go to Index');
        $this->assertEquals(static::$host . '/index.php', static::$session->getCurrentUrl());
    }

    public function testMouseEvents()
    {
        static::$session->visit(static::$host . '/js_test.php');

        $clicker = static::$session->getPage()->find('css', '.elements div#clicker');

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
        static::$session->visit(static::$host . '/js_test.php');

        $input1 = static::$session->getPage()->find('css', '.elements input.input.first');
        $input2 = static::$session->getPage()->find('css', '.elements input.input.second');
        $input3 = static::$session->getPage()->find('css', '.elements input.input.third');
        $event  = static::$session->getPage()->find('css', '.elements .text-event');

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

    public function testVisibility()
    {
        static::$session->visit(static::$host . '/js_test.php');

        $clicker   = static::$session->getPage()->find('css', '.elements div#clicker');
        $invisible = static::$session->getPage()->find('css', '#invisible');

        $this->assertFalse($invisible->isVisible());
        $this->assertTrue($clicker->isVisible());
    }
}
