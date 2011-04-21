<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\DocumentElement;

require_once 'ElementTest.php';

class DocumentElementTest extends ElementTest
{
    private $session;
    private $document;

    protected function setUp()
    {
        $this->session  = $this->getSessionWithMockedDriver();
        $this->document = new DocumentElement($this->session);
    }

    public function testGetSession()
    {
        $this->assertEquals($this->session, $this->document->getSession());
    }

    public function testFindAll()
    {
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath = 'h3[a]')
            ->will($this->onConsecutiveCalls(array(2, 3, 4), array(1, 2), array()));

        $this->assertEquals(3, count($this->document->findAll('xpath', $xpath)));

        $selector = $this->getMockBuilder('Behat\Mink\Selector\SelectorInterface')->getMock();
        $selector
            ->expects($this->once())
            ->method('translateToXPath')
            ->with($css = 'h3 > a')
            ->will($this->returnValue($xpath));

        $this->session->getSelectorsHandler()->registerSelector('css', $selector);
        $this->assertEquals(2, count($this->document->findAll('css', $css)));
    }

    public function testFind()
    {
        $this->session->getDriver()
            ->expects($this->exactly(3))
            ->method('find')
            ->with($xpath = 'h3[a]')
            ->will($this->onConsecutiveCalls(array(2, 3, 4), array(1, 2), array()));

        $this->assertEquals(2, $this->document->find('xpath', $xpath));

        $selector = $this->getMockBuilder('Behat\Mink\Selector\SelectorInterface')->getMock();
        $selector
            ->expects($this->once())
            ->method('translateToXPath')
            ->with($css = 'h3 > a')
            ->will($this->returnValue($xpath));

        $this->session->getSelectorsHandler()->registerSelector('css', $selector);
        $this->assertEquals(1, $this->document->find('css', $css));

        $this->assertNull($this->document->find('xpath', $xpath));
    }

    public function testFindField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('field1', 'field2', 'field3'), array()));

        $this->assertEquals('field1', $this->document->findField('some field'));
        $this->assertEquals(null, $this->document->findField('some field'));
    }

    public function testFindLink()
    {
        $xpath = <<<XPATH
.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link')) or .//img[contains(./@alt, 'some link')])]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('link1', 'link2', 'link3'), array()));

        $this->assertEquals('link1', $this->document->findLink('some link'));
        $this->assertEquals(null, $this->document->findLink('some link'));
    }

    public function testFindButton()
    {
        $xpath = <<<XPATH
.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][((./@id = 'some button' or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[(((./@id = 'some button' or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('button1', 'button2', 'button3'), array()));

        $this->assertEquals('button1', $this->document->findButton('some button'));
        $this->assertEquals(null, $this->document->findButton('some button'));
    }

    public function testFindById()
    {
        $xpath = <<<XPATH
//*[@id='some-item-2']
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('id2', 'id3'), array()));

        $this->assertEquals('id2', $this->document->findById('some-item-2'));
        $this->assertEquals(null, $this->document->findById('some-item-2'));
    }

    public function testHasSelector()
    {
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath = 'some xpath selector')
            ->will($this->onConsecutiveCalls(array('id2', 'id3'), array()));

        $this->assertTrue($this->document->hasSelector('xpath', $xpath));
        $this->assertFalse($this->document->hasSelector('xpath', $xpath));
    }

    public function testHasContent()
    {
        $xpath = <<<XPATH
./descendant-or-self::*[contains(normalize-space(.), 'some content')]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('item1', 'item2'), array()));

        $this->assertTrue($this->document->hasContent('some content'));
        $this->assertFalse($this->document->hasContent('some content'));
    }

    public function testHasLink()
    {
        $xpath = <<<XPATH
.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link')) or .//img[contains(./@alt, 'some link')])]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('link1', 'link2', 'link3'), array()));

        $this->assertTrue($this->document->hasLink('some link'));
        $this->assertFalse($this->document->hasLink('some link'));
    }

    public function testHasButton()
    {
        $xpath = <<<XPATH
.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][((./@id = 'some button' or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[(((./@id = 'some button' or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('button1', 'button2', 'button3'), array()));

        $this->assertTrue($this->document->hasButton('some button'));
        $this->assertFalse($this->document->hasButton('some button'));
    }

    public function testHasField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('field1', 'field2', 'field3'), array()));

        $this->assertTrue($this->document->hasField('some field'));
        $this->assertFalse($this->document->hasField('some field'));
    }

    public function testHasCheckedField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some checkbox' or ./@name = 'some checkbox') or ./@id = //label[contains(normalize-space(string(.)), 'some checkbox')]/@for) or ./@placeholder = 'some checkbox')] | .//label[contains(normalize-space(string(.)), 'some checkbox')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->session->getDriver()
            ->expects($this->exactly(3))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array($checkbox), array(), array($checkbox)));

        $this->assertTrue($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
    }

    public function testHasUncheckedField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some checkbox' or ./@name = 'some checkbox') or ./@id = //label[contains(normalize-space(string(.)), 'some checkbox')]/@for) or ./@placeholder = 'some checkbox')] | .//label[contains(normalize-space(string(.)), 'some checkbox')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->session->getDriver()
            ->expects($this->exactly(3))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array($checkbox), array(), array($checkbox)));

        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertTrue($this->document->hasUncheckedField('some checkbox'));
    }

    public function testHasSelect()
    {
        $xpath = <<<XPATH
.//select[(((./@id = 'some select field' or ./@name = 'some select field') or ./@id = //label[contains(normalize-space(string(.)), 'some select field')]/@for) or ./@placeholder = 'some select field')] | .//label[contains(normalize-space(string(.)), 'some select field')]//.//select
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('select'), array()));

        $this->assertTrue($this->document->hasSelect('some select field'));
        $this->assertFalse($this->document->hasSelect('some select field'));
    }

    public function testHasTable()
    {
        $xpath = <<<XPATH
.//table[(./@id = 'some table' or contains(.//caption, 'some table'))]
XPATH;

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('table'), array()));

        $this->assertTrue($this->document->hasTable('some table'));
        $this->assertFalse($this->document->hasTable('some table'));
    }

    public function testClickLink()
    {
        $xpath = <<<XPATH
.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link')) or .//img[contains(./@alt, 'some link')])]
XPATH;
        
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('click')
            ->with($xpath);

        $this->document->clickLink('some link');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->clickLink('some link');
    }

    public function testClickButton()
    {
        $xpath = <<<XPATH
.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][((./@id = 'some button' or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[(((./@id = 'some button' or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('click')
            ->with($xpath);

        $this->document->clickButton('some button');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->clickButton('some button');
    }

    public function testFillField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('setValue')
            ->with($xpath, 'some val');

        $this->document->fillField('some field', 'some val');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->fillField('some field', 'some val');
    }

    public function testCheckField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('check')
            ->with($xpath);

        $this->document->checkField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->checkField('some field');
    }

    public function testUncheckField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('uncheck')
            ->with($xpath);

        $this->document->uncheckField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->uncheckField('some field');
    }

    public function testSelectField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('selectOption')
            ->with($xpath, 'option2');

        $this->document->selectFieldOption('some field', 'option2');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->selectFieldOption('some field', 'option2');
    }

    public function testAttachFileToField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getXpath')
            ->will($this->returnValue($xpath));
        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->session->getDriver()
            ->expects($this->once())
            ->method('attachFile')
            ->with($xpath, '/path/to/file');

        $this->document->attachFileToField('some field', '/path/to/file');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->attachFileToField('some field', '/path/to/file');
    }

    public function testGetContent()
    {
        $this->session->getDriver()
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($ret = 'page content'));

        $this->assertEquals($ret, $this->document->getContent());
    }

    public function testGetText()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('getText')
            ->will($this->returnValue($ret = 'page text'));

        $this->session->getDriver()
            ->expects($this->exactly(2))
            ->method('find')
            ->with('/html')
            ->will($this->onConsecutiveCalls(array(), array($node)));

        $this->assertNull($this->document->getText());
        $this->assertEquals($ret, $this->document->getText());
    }
}
