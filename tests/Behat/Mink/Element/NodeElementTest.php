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

    public function testHasClassWithoutArgument()
    {
        $node = new NodeElement('input_tag', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getAttribute')
            ->with('input_tag', 'class')
            ->will($this->returnValue(null));

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

    public function testSetValueWrapsException()
    {
        $node = new NodeElement('link_or_button', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('setValue')
            ->with('link_or_button', 'new_val')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by link_or_button\nAn error happened in the driver");
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

    public function testClickWrapsException()
    {
        $node = new NodeElement('link_or_button', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('click')
            ->with('link_or_button')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by link_or_button\nAn error happened in the driver");
        $node->click();
    }

    public function testPress()
    {
        $node = new NodeElement('link_or_button', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('click')
            ->with('link_or_button');

        $node->press();
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

    public function testRightClickWrapsException()
    {
        $node = new NodeElement('elem', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('rightClick')
            ->with('elem')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by elem\nAn error happened in the driver");
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

    public function testDoubleClickWrapsException()
    {
        $node = new NodeElement('elem', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('doubleClick')
            ->with('elem')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by elem\nAn error happened in the driver");
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

    public function testCheckWrapsException()
    {
        $node = new NodeElement('elem', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('check')
            ->with('elem')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by elem\nAn error happened in the driver");
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

    public function testUncheckWrapsException()
    {
        $node = new NodeElement('elem', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('uncheck')
            ->with('elem')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by elem\nAn error happened in the driver");
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

    /**
     * @expectedException \Behat\Mink\Exception\ElementNotFoundException
     */
    public function testSelectOptionNotFound()
    {
        $node = new NodeElement('select', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getTagName')
            ->with('select')
            ->will($this->returnValue('select'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('find')
            ->with('select/option')
            ->will($this->returnValue(array()));

        $this->selectors
            ->expects($this->once())
            ->method('selectorToXpath')
            ->with('named', array('option', 'item1'))
            ->will($this->returnValue('option'));

        $node->selectOption('item1');
    }

    public function testSelectOptionOtherTag()
    {
        $node = new NodeElement('div', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('getTagName')
            ->with('div')
            ->will($this->returnValue('div'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('selectOption')
            ->with('div', 'item1', false);

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

    public function testGetParent()
    {
        $node = new NodeElement('elem', $this->session);
        $parent = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session->getDriver()
            ->expects($this->once())
            ->method('find')
            ->with('elem/..')
            ->will($this->returnValue(array($parent)));

        $this->selectors
            ->expects($this->once())
            ->method('selectorToXpath')
            ->with('xpath', '..')
            ->will($this->returnValue('..'));

        $this->assertSame($parent, $node->getParent());
    }

    public function testAttachFile()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('attachFile')
            ->with('elem', 'path');

        $node->attachFile('path');
    }

    public function testAttachFileWrapsException()
    {
        $node = new NodeElement('elem', $this->session);
        $exception = new \Exception('An error happened in the driver');

        $this->session->getDriver()
            ->expects($this->once())
            ->method('attachFile')
            ->with('elem', 'path')
            ->will($this->throwException($exception));

        $this->setExpectedException('Behat\Mink\Exception\ElementException', "Exception thrown by elem\nAn error happened in the driver");
        $node->attachFile('path');
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

    public function testDragTo()
    {
        $node = new NodeElement('some_tag1', $this->session);

        $target = $this->getMock('Behat\Mink\Element\ElementInterface');
        $target->expects($this->any())
            ->method('getXPath')
            ->will($this->returnValue('some_tag2'));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('dragTo')
            ->with('some_tag1', 'some_tag2');

        $node->dragTo($target);
    }

    public function testKeyPress()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('keyPress')
            ->with('elem', 'key');

        $node->keyPress('key');
    }

    public function testKeyDown()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('keyDown')
            ->with('elem', 'key');

        $node->keyDown('key');
    }

    public function testKeyUp()
    {
        $node = new NodeElement('elem', $this->session);

        $this->session->getDriver()
            ->expects($this->once())
            ->method('keyUp')
            ->with('elem', 'key');

        $node->keyUp('key');
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
        $xpath = "some_tag1 | some_tag2[@foo =\n 'bar|'']\n | some_tag3[foo | bar]";
        $expectedPrefixed = "some_xpath/some_tag1 | some_xpath/some_tag2[@foo =\n 'bar|''] | some_xpath/some_tag3[foo | bar]";

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

    public function testFindAllParentUnion()
    {
        $node = new NodeElement('some_xpath | another_xpath', $this->session);
        $xpath = "some_tag1 | some_tag2";
        $expectedPrefixed = "(some_xpath | another_xpath)/some_tag1 | (some_xpath | another_xpath)/some_tag2";

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
