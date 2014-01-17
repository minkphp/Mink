<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\NodeElement;

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
            ->expects($this->once())
            ->method('getText')
            ->with('text_tag')
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $node->getText());
    }

    public function testHasAttribute()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('input_tag', 'href')
            ->will($this->onConsecutiveCalls(null, 'http://...'));

        $this->assertFalse($node->hasAttribute('href'));
        $this->assertTrue($node->hasAttribute('href'));
    }

    public function testGetAttribute()
    {
        $expected = 'http://...';
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getAttribute')
            ->with('input_tag', 'href')
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $node->getAttribute('href'));
    }

    public function testHasClass()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->exactly(6))
            ->method('getAttribute')
            ->with('input_tag', 'class')
            ->will($this->returnValue('class1 class2'));

        $this->assertTrue($node->hasClass('class1'));
        $this->assertTrue($node->hasClass('class2'));
        $this->assertFalse($node->hasClass('class3'));
    }

    public function testGetValue()
    {
        $expected = 'val1';
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getValue')
            ->with('input_tag')
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $node->getValue());
    }

    public function testSetValue()
    {
        $expected = 'new_val';
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('setValue')
            ->with('input_tag', $expected);

        $node->setValue($expected);
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

    public function testRightClick()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('rightClick')
            ->with('elem');

        $node->rightClick();
    }

    public function testDoubleClick()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('doubleClick')
            ->with('elem');

        $node->doubleClick();
    }

    public function testCheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('check')
            ->with('checkbox_or_radio');

        $node->check();
    }

    public function testUncheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('uncheck')
            ->with('checkbox_or_radio');

        $node->uncheck();
    }

    public function testSelectOption()
    {
        $node = new NodeElement('select', $this->session);
        $option = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $option
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('item1'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getTagName')
            ->with('select')
            ->will($this->returnValue('select'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('find')
            ->with('select/option')
            ->will($this->returnValue(array($option)));

        $this->selectors
            ->expects($this->once())
            ->method('selectorToXpath')
            ->with('named', array('option', 'item1'))
            ->will($this->returnValue('option'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('selectOption')
            ->with('select', 'item1', false);

        $node->selectOption('item1');
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

    public function testIsSelected()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('isSelected')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isSelected());
        $this->assertFalse($node->isSelected());
    }

    public function testFocus()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('focus')
            ->with('some-element');

        $node->focus();
    }

    public function testBlur()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('blur')
            ->with('some-element');

        $node->blur();
    }

    public function testMouseOver()
    {
        $node = new NodeElement('some-element', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('mouseOver')
            ->with('some-element');

        $node->mouseOver();
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

    public function testSubmitForm()
    {
        $node = new NodeElement('some_xpath', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('submitForm')
            ->with('some_xpath');

        $node->submit();
    }

    public function testFindAllUnion()
    {
        $node = new NodeElement('some_xpath', $this->session);
        $xpath = 'some_tag1 | some_tag2[@foo = "bar|"] | some_tag3[foo | bar]';
        $expectedPrefixed = 'some_xpath/some_tag1 | some_xpath/some_tag2[@foo = "bar|"] | some_xpath/some_tag3[foo | bar]';

        $this->session->getDriver()
            ->expects($this->exactly(1))
            ->method('find')
            ->will($this->returnValueMap(array(
                array($expectedPrefixed, array(2, 3, 4)),
            )));

        $this->selectors
            ->expects($this->exactly(1))
            ->method('selectorToXpath')
            ->will($this->returnValueMap(array(
                array('xpath', $xpath, $xpath),
            )));

        $this->assertEquals(3, count($node->findAll('xpath', $xpath)));
    }
}
