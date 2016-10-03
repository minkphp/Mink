<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Element\DocumentElement;

class DocumentElementTest extends ElementTest
{
    /**
     * Page.
     *
     * @var DocumentElement
     */
    private $document;

    protected function setUp()
    {
        parent::setUp();
        $this->document = new DocumentElement($this->driver, $this->elementFinder);
    }

    public function testFindAll()
    {
        $xpath = 'h3[a]';
        $css = 'h3 > a';

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder
            ->expects($this->exactly(2))
            ->method('findAll')
            ->will($this->returnValueMap(array(
                array('xpath', $xpath, '//html', array($node, $node)),
                array('css', $css, '//html', array()),
            )));

        $this->assertEquals(array($node, $node), $this->document->findAll('xpath', $xpath));
        $this->assertCount(0, $this->document->findAll('css', $css));
    }

    public function testFind()
    {
        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node4 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $xpath = 'h3[a]';
        $css = 'h3 > a';

        $this->elementFinder
            ->expects($this->exactly(3))
            ->method('findAll')
            ->will($this->onConsecutiveCalls(
                array($node2, $node3, $node4),
                array($node1, $node2),
                array()
            ));

        $this->assertSame($node2, $this->document->find('xpath', $xpath));
        $this->assertSame($node1, $this->document->find('css', $css));
        $this->assertNull($this->document->find('xpath', $xpath));
    }

    public function testFindField()
    {
        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node1, $node2, $node3), array()));

        $this->assertSame($node1, $this->document->findField('some field'));
        $this->assertNull($this->document->findField('some field'));
    }

    public function testFindLink()
    {
        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('link', 'some link'), '//html')
            ->will($this->onConsecutiveCalls(array($node1, $node2, $node3), array()));

        $this->assertSame($node1, $this->document->findLink('some link'));
        $this->assertNull($this->document->findLink('some link'));
    }

    public function testFindButton()
    {
        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('button', 'some button'), '//html')
            ->will($this->onConsecutiveCalls(array($node1, $node2, $node3), array()));

        $this->assertEquals($node1, $this->document->findButton('some button'));
        $this->assertNull($this->document->findButton('some button'));
    }

    public function testFindById()
    {
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('id', 'some-item-2'), '//html')
            ->will($this->onConsecutiveCalls(array($node2, $node3), array()));

        $this->assertSame($node2, $this->document->findById('some-item-2'));
        $this->assertEquals(null, $this->document->findById('some-item-2'));
    }

    public function testHasSelector()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('xpath', 'some xpath', '//html')
            ->will($this->onConsecutiveCalls(array($node, $node), array()));

        $this->assertTrue($this->document->has('xpath', 'some xpath'));
        $this->assertFalse($this->document->has('xpath', 'some xpath'));
    }

    public function testHasContent()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('content', 'some content'), '//html')
            ->will($this->onConsecutiveCalls(array($node, $node), array()));

        $this->assertTrue($this->document->hasContent('some content'));
        $this->assertFalse($this->document->hasContent('some content'));
    }

    public function testHasLink()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('link', 'some link'), '//html')
            ->will($this->onConsecutiveCalls(array($node, $node), array()));

        $this->assertTrue($this->document->hasLink('some link'));
        $this->assertFalse($this->document->hasLink('some link'));
    }

    public function testHasButton()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('button', 'some button'), '//html')
            ->will($this->onConsecutiveCalls(array($node, $node), array()));

        $this->assertTrue($this->document->hasButton('some button'));
        $this->assertFalse($this->document->hasButton('some button'));
    }

    public function testHasField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node, $node), array()));

        $this->assertTrue($this->document->hasField('some field'));
        $this->assertFalse($this->document->hasField('some field'));
    }

    public function testHasCheckedField()
    {
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->elementFinder->expects($this->exactly(3))
            ->method('findAll')
            ->with('named', array('field', 'some checkbox'), '//html')
            ->will($this->onConsecutiveCalls(array($checkbox), array(), array($checkbox)));

        $this->assertTrue($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
    }

    public function testHasUncheckedField()
    {
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->elementFinder->expects($this->exactly(3))
            ->method('findAll')
            ->with('named', array('field', 'some checkbox'), '//html')
            ->will($this->onConsecutiveCalls(array($checkbox), array(), array($checkbox)));

        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertTrue($this->document->hasUncheckedField('some checkbox'));
    }

    public function testHasSelect()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('select', 'some select field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->assertTrue($this->document->hasSelect('some select field'));
        $this->assertFalse($this->document->hasSelect('some select field'));
    }

    public function testHasTable()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('table', 'some table'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->assertTrue($this->document->hasTable('some table'));
        $this->assertFalse($this->document->hasTable('some table'));
    }

    public function testClickLink()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('click');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('link', 'some link'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->clickLink('some link');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->clickLink('some link');
    }

    public function testClickButton()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('press');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('button', 'some button'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->pressButton('some button');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->pressButton('some button');
    }

    public function testFillField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('setValue')
            ->with('some val');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->fillField('some field', 'some val');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->fillField('some field', 'some val');
    }

    public function testCheckField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('check');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->checkField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->checkField('some field');
    }

    public function testUncheckField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('uncheck');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->uncheckField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->uncheckField('some field');
    }

    public function testSelectField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('selectOption')
            ->with('option2');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->selectFieldOption('some field', 'option2');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->selectFieldOption('some field', 'option2');
    }

    public function testAttachFileToField()
    {
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('attachFile')
            ->with('/path/to/file');

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->with('named', array('field', 'some field'), '//html')
            ->will($this->onConsecutiveCalls(array($node), array()));

        $this->document->attachFileToField('some field', '/path/to/file');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->attachFileToField('some field', '/path/to/file');
    }

    public function testGetContent()
    {
        $expects = 'page content';
        $this->driver
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($expects));

        $this->assertEquals($expects, $this->document->getContent());
    }

    public function testGetText()
    {
        $expects = 'val1';
        $this->driver
            ->expects($this->once())
            ->method('getText')
            ->with('//html')
            ->will($this->returnValue($expects));

        $this->assertEquals($expects, $this->document->getText());
    }

    public function testGetHtml()
    {
        $expects = 'val1';
        $this->driver
            ->expects($this->once())
            ->method('getHtml')
            ->with('//html')
            ->will($this->returnValue($expects));

        $this->assertEquals($expects, $this->document->getHtml());
    }

    public function testGetOuterHtml()
    {
        $expects = 'val1';
        $this->driver
            ->expects($this->once())
            ->method('getOuterHtml')
            ->with('//html')
            ->will($this->returnValue($expects));

        $this->assertEquals($expects, $this->document->getOuterHtml());
    }
}
