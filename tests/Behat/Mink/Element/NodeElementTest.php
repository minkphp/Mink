<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\NodeElement;

require_once 'ElementTest.php';

class NodeElementTest extends ElementTest
{
    private $session;

    protected function setUp()
    {
        $this->session = $this->getSessionWithMockedDriver();
    }

    public function testGetXpath()
    {
        $node = new NodeElement('some custom xpath', $this->session);

        $this->assertEquals('some custom xpath', $node->getXpath());
        $this->assertNotEquals('not some custom xpath', $node->getXpath());
    }

    public function testGetText()
    {
        $node = new NodeElement('text_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getText')
            ->with('text_tag')
            ->will($this->returnValue('val1'));

        $this->assertEquals('val1', $node->getText());
    }

    public function testGetValue()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getValue')
            ->with('input_tag')
            ->will($this->returnValue('val1'));

        $this->assertEquals('val1', $node->getValue());
    }

    public function testSetValue()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('setValue')
            ->with('input_tag', 'new_val');

        $node->setValue('new_val');
    }

    public function testClick()
    {
        $node = new NodeElement('link_or_button', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('click')
            ->with('link_or_button');

        $node->click();
    }

    public function testGetTagName()
    {
        $node = new NodeElement('html//h3', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getTagName')
            ->with('html//h3')
            ->will($this->returnValue('h3'));

        $this->assertEquals('h3', $node->getTagName());
    }

    public function testIsVisible()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('isVisible')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isVisible());
        $this->assertFalse($node->isVisible());
    }

    public function testIsChecked()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isChecked());
        $this->assertFalse($node->isChecked());
    }

    public function testTriggerEvent()
    {
        $node = new NodeElement('some_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('triggerEvent')
            ->with('some_tag', 'onClick');

        $node->triggerEvent('onClick');
    }

    public function dragTo()
    {
        $node = new NodeElement('some_tag1', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('triggerEvent')
            ->with('some_tag1', 'some_tag3');

        $node->dragTo(new NodeElement('some_tag2', $this->session));
    }
}
