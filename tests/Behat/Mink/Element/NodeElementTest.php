<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\NodeElement;
use Mockery as m;

require_once 'ElementTest.php';

/**
 * @group unittest
 */
class NodeElementTest extends ElementTest
{

    public function testGetXpath()
    {
        $node = new NodeElement('some custom xpath', $this->session);

        $this->assertEquals('some custom xpath', $node->getXpath());
        $this->assertNotEquals('not some custom xpath', $node->getXpath());
    }

    public function testGetText()
    {
        $expected = 'val1';
        $node = new NodeElement('text_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getText')
            ->with('text_tag')
            ->once()
            ->andReturn($expected);

        $this->assertEquals($expected, $node->getText());
    }

    public function testHasAttribute()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getAttribute')
            ->with('input_tag', 'href')
            ->twice()
            ->andReturn(null, 'http://...');

        $this->assertFalse($node->hasAttribute('href'));
        $this->assertTrue($node->hasAttribute('href'));
    }

    public function testGetAttribute()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getAttribute')
            ->with('input_tag', 'href')
            ->once()
            ->andReturn('http://...');

        $this->assertEquals('http://...', $node->getAttribute('href'));
    }

    public function testHasClass()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getAttribute')
            ->with('input_tag', 'class')
            ->times(6)
            ->andReturn('class1 class2');

        $this->assertTrue($node->hasClass('class1'));
        $this->assertTrue($node->hasClass('class2'));
        $this->assertFalse($node->hasClass('class3'));
    }

    public function testGetValue()
    {
        $expected = 'val1';
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getValue')
            ->with('input_tag')
            ->once()
            ->andReturn($expected);

        $this->assertEquals($expected, $node->getValue());
    }

    public function testSetValue()
    {
        $expected = 'new_val';
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->shouldReceive('setValue')
            ->with('input_tag', $expected)
            ->once();

        $node->setValue($expected);
    }

    public function testClick()
    {
        $node = new NodeElement('link_or_button', $this->session);

        $this->session->getDriver()
            ->shouldReceive('click')
            ->with('link_or_button')
            ->once();

        $node->click();
    }

    public function testRightClick()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->shouldReceive('rightClick')
            ->with('elem')
            ->once();

        $node->rightClick();
    }

    public function testDoubleClick()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->shouldReceive('doubleClick')
            ->with('elem')
            ->once();

        $node->doubleClick();
    }

    public function testCheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->session);

        $this->session->getDriver()
            ->shouldReceive('check')
            ->with('checkbox_or_radio')
            ->once();

        $node->check();
    }

    public function testUncheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->session);

        $this->session->getDriver()
            ->shouldReceive('uncheck')
            ->with('checkbox_or_radio')
            ->once();

        $node->uncheck();
    }

    public function testSelectOption()
    {
        $node = new NodeElement('select', $this->session);
        $option = m::mock('Behat\Mink\Element\NodeElement');
        $option->shouldReceive('getValue')->once()->andReturn('item1');

        $this->session->getDriver()
            ->shouldReceive('getTagName')
            ->with('select')
            ->once()
            ->andReturn('select');

        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('select/option')
            ->once()
            ->andReturn(array($option));

        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('named', array('option', 'item1'))
            ->once()
            ->andReturn('option');

        $this->session->getDriver()
            ->shouldReceive('selectOption')
            ->with('select', 'item1', false)
            ->once();

        $node->selectOption('item1');
    }

    public function testGetTagName()
    {
        $node = new NodeElement('html//h3', $this->session);

        $this->session->getDriver()
            ->shouldReceive('getTagName')
            ->with('html//h3')
            ->once()
            ->andReturn('h3');

        $this->assertEquals('h3', $node->getTagName());
    }

    public function testIsVisible()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->shouldReceive('isVisible')
            ->with('some_xpath')
            ->twice()
            ->andReturn(true, false);

        $this->assertTrue($node->isVisible());
        $this->assertFalse($node->isVisible());
    }

    public function testIsChecked()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->shouldReceive('isChecked')
            ->with('some_xpath')
            ->twice()
            ->andReturn(true, false);

        $this->assertTrue($node->isChecked());
        $this->assertFalse($node->isChecked());
    }

    public function testIsSelected()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->shouldReceive('isSelected')
            ->with('some_xpath')
            ->twice()
            ->andReturn(true, false);

        $this->assertTrue($node->isSelected());
        $this->assertFalse($node->isSelected());
    }

    public function testFocus()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->shouldReceive('focus')
            ->with('some-element')
            ->once();

        $node->focus();
    }

    public function testBlur()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->shouldReceive('blur')
            ->with('some-element')
            ->once();

        $node->blur();
    }

    public function testMouseOver()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->shouldReceive('mouseOver')
            ->with('some-element')
            ->once();

        $node->mouseOver();
    }

    public function dragTo()
    {
        $node = new NodeElement('some_tag1', $this->session);

        $this->session->getDriver()
            ->shouldReceive('triggerEvent')
            ->with('some_tag1', 'some_tag3')
            ->once();

        $node->dragTo(new NodeElement('some_tag2', $this->session));
    }

    public function testSubmitForm()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->shouldReceive('submitForm')
            ->with('some_xpath')
            ->once();

        $node->submit();
    }
}
