<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

class DocumentElementTest extends ElementTestCase
{
    /**
     * Page.
     *
     * @var DocumentElement
     */
    private $document;

    /**
     * @before
     */
    protected function prepareSession(): void
    {
        parent::prepareSession();
        $this->document = new DocumentElement($this->driver, $this->elementFinder);
    }

    public function testFindAll()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $xpath = 'h3[a]';
        $css = 'h3 > a';

        $this->elementFinder
            ->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('xpath', $xpath, '//html', array($node1, $node2)),
                array('css', $css, '//html', array()),
            ));

        $this->assertSame(array($node1, $node2), $this->document->findAll('xpath', $xpath));
        $this->assertCount(0, $this->document->findAll('css', $css));
    }

    public function testFind()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);
        $node3 = $this->createStub(NodeElement::class);
        $node4 = $this->createStub(NodeElement::class);

        $xpath = 'h3[a]';
        $xpath2 = 'h3[b]';
        $css = 'h3 > a';

        $this->elementFinder
            ->expects($this->exactly(3))
            ->method('findAll')
            ->willReturnMap(array(
                array('xpath', $xpath, '//html', array($node2, $node3, $node4)),
                array('css', $css, '//html', array($node1, $node2)),
                array('xpath', $xpath2, '//html', array()),
            ));

        $this->assertSame($node2, $this->document->find('xpath', $xpath));
        $this->assertSame($node1, $this->document->find('css', $css));
        $this->assertNull($this->document->find('xpath', $xpath2));
    }

    public function testFindField()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->assertSame($node1, $this->document->findField('some field'));
        $this->assertNull($this->document->findField('some other field'));
    }

    public function testFindLink()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('link', 'some link'), '//html', array($node1, $node2)),
                array('named', array('link', 'some other link'), '//html', array()),
            ));

        $this->assertSame($node1, $this->document->findLink('some link'));
        $this->assertNull($this->document->findLink('some other link'));
    }

    public function testFindButton()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('button', 'some button'), '//html', array($node1, $node2)),
                array('named', array('button', 'some other button'), '//html', array()),
            ));

        $this->assertSame($node1, $this->document->findButton('some button'));
        $this->assertNull($this->document->findButton('some other button'));
    }

    public function testFindById()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('id', 'some-item-1'), '//html', array($node1, $node2)),
                array('named', array('id', 'some-item-2'), '//html', array()),
            ));

        $this->assertSame($node1, $this->document->findById('some-item-1'));
        $this->assertNull($this->document->findById('some-item-2'));
    }

    public function testHasSelector()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('xpath', 'some xpath', '//html', array($node1, $node2)),
                array('xpath', 'some other xpath', '//html', array()),
            ));

        $this->assertTrue($this->document->has('xpath', 'some xpath'));
        $this->assertFalse($this->document->has('xpath', 'some other xpath'));
    }

    public function testHasContent()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('content', 'some content'), '//html', array($node1, $node2)),
                array('named', array('content', 'some other content'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasContent('some content'));
        $this->assertFalse($this->document->hasContent('some other content'));
    }

    public function testHasLink()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('link', 'some link'), '//html', array($node1, $node2)),
                array('named', array('link', 'some other link'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasLink('some link'));
        $this->assertFalse($this->document->hasLink('some other link'));
    }

    public function testHasButton()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('button', 'some button'), '//html', array($node1, $node2)),
                array('named', array('button', 'some other button'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasButton('some button'));
        $this->assertFalse($this->document->hasButton('some other button'));
    }

    public function testHasField()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasField('some field'));
        $this->assertFalse($this->document->hasField('some other field'));
    }

    public function testHasCheckedField()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node1->method('isChecked')->willReturn(true);
        $node2 = $this->createStub(NodeElement::class);
        $node2->method('isChecked')->willReturn(false);

        $this->elementFinder->expects($this->exactly(3))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some checkbox'), '//html', array($node1, $node2)),
                array('named', array('field', 'some unchecked checkbox'), '//html', array($node2)),
                array('named', array('field', 'some other checkbox'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some other checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some unchecked checkbox'));
    }

    public function testHasUncheckedField()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node1->method('isChecked')->willReturn(true);
        $node2 = $this->createStub(NodeElement::class);
        $node2->method('isChecked')->willReturn(false);

        $this->elementFinder->expects($this->exactly(3))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some checkbox'), '//html', array($node1, $node2)),
                array('named', array('field', 'some unchecked checkbox'), '//html', array($node2)),
                array('named', array('field', 'some other checkbox'), '//html', array()),
            ));

        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertFalse($this->document->hasUncheckedField('some other checkbox'));
        $this->assertTrue($this->document->hasUncheckedField('some unchecked checkbox'));
    }

    public function testHasSelect()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('select', 'some select field'), '//html', array($node1, $node2)),
                array('named', array('select', 'some other select field'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasSelect('some select field'));
        $this->assertFalse($this->document->hasSelect('some other select field'));
    }

    public function testHasTable()
    {
        $node1 = $this->createStub(NodeElement::class);
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('table', 'some table'), '//html', array($node1, $node2)),
                array('named', array('table', 'some other table'), '//html', array()),
            ));

        $this->assertTrue($this->document->hasTable('some table'));
        $this->assertFalse($this->document->hasTable('some other table'));
    }

    public function testClickLink()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('click');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('link', 'some link'), '//html', array($node1, $node2)),
                array('named', array('link', 'some other link'), '//html', array()),
            ));

        $this->document->clickLink('some link');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->clickLink('some other link');
    }

    public function testClickButton()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('press');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('button', 'some button'), '//html', array($node1, $node2)),
                array('named', array('button', 'some other button'), '//html', array()),
            ));

        $this->document->pressButton('some button');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->pressButton('some other button');
    }

    public function testFillField()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('setValue')
            ->with('some val');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->document->fillField('some field', 'some val');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->fillField('some other field', 'some val');
    }

    public function testCheckField()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('check');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->document->checkField('some field');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->checkField('some other field');
    }

    public function testUncheckField()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('uncheck');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->document->uncheckField('some field');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->uncheckField('some other field');
    }

    public function testSelectField()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('selectOption')
            ->with('option2');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->document->selectFieldOption('some field', 'option2');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->selectFieldOption('some other field', 'option2');
    }

    public function testAttachFileToField()
    {
        $node1 = $this->createMock(NodeElement::class);
        $node1->expects($this->once())
            ->method('attachFile')
            ->with('/path/to/file');
        $node2 = $this->createStub(NodeElement::class);

        $this->elementFinder->expects($this->exactly(2))
            ->method('findAll')
            ->willReturnMap(array(
                array('named', array('field', 'some field'), '//html', array($node1, $node2)),
                array('named', array('field', 'some other field'), '//html', array()),
            ));

        $this->document->attachFileToField('some field', '/path/to/file');

        $this->expectException('Behat\Mink\Exception\ElementNotFoundException');

        $this->document->attachFileToField('some other field', '/path/to/file');
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
