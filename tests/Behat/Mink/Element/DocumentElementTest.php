<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use Mockery as m;
use Mockery\MockInterface;

require_once 'ElementTest.php';

/**
 * @group unittest
 */
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
        $this->document = new DocumentElement($this->session);
    }

    public function testGetSession()
    {
        $this->assertEquals($this->session, $this->document->getSession());
    }

    public function testFindAll()
    {
        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('//html/h3[a]')
            ->twice()
            ->andReturn(array(2, 3, 4), array(1, 2), array());

        $xpath = 'h3[a]';
        $this->selectors->
            shouldReceive('selectorToXpath')
            ->with('xpath', $xpath)
            ->once()
            ->andReturn($xpath);
        $this->assertEquals(3, count($this->document->findAll('xpath', $xpath)));

        $css = 'h3 > a';
        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('css', $css)
            ->once()
            ->andReturn($xpath);
        $this->assertEquals(2, count($this->document->findAll('css', $css)));
    }

    public function testFind()
    {
        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('//html/h3[a]')
            ->times(3)
            ->andReturn(array(2, 3, 4), array(1, 2), array());

        $xpath = 'h3[a]';
        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('xpath', $xpath)
            ->twice()
            ->andReturn($xpath);
        $this->assertEquals(2, $this->document->find('xpath', $xpath));

        $css = 'h3 > a';
        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('css', $css)
            ->once()
            ->andReturn($xpath);
        $this->assertEquals(1, $this->document->find('css', $css));

        $this->assertNull($this->document->find('xpath', $xpath));
    }

    public function testFindField()
    {
        $this->mockNamedFinder(
            '//field',
            array('field1', 'field2', 'field3'),
            array('field', 'some field')
        );

        $this->assertEquals('field1', $this->document->findField('some field'));
        $this->assertEquals(null, $this->document->findField('some field'));
    }

    public function testFindLink()
    {
        $this->mockNamedFinder(
            '//link',
            array('link1', 'link2', 'link3'),
            array('link', 'some link')
        );

        $this->assertEquals('link1', $this->document->findLink('some link'));
        $this->assertEquals(null, $this->document->findLink('some link'));
    }

    public function testFindButton()
    {
        $this->mockNamedFinder(
            '//button',
            array('button1', 'button2', 'button3'),
            array('button', 'some button')
        );

        $this->assertEquals('button1', $this->document->findButton('some button'));
        $this->assertEquals(null, $this->document->findButton('some button'));
    }

    public function testFindById()
    {
        $xpath = '//*[@id=some-item-2]';

        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('//html' . $xpath)
            ->twice()
            ->andReturn(array('id2', 'id3'), array());

        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('xpath', $xpath)
            ->twice()
            ->andReturn($xpath);

        $this->assertEquals('id2', $this->document->findById('some-item-2'));
        $this->assertEquals(null, $this->document->findById('some-item-2'));
    }

    public function testHasSelector()
    {
        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('//html/some xpath')
            ->twice()
            ->andReturn(array('id2', 'id3'), array());

        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('xpath', 'some xpath')
            ->twice()
            ->andReturn('some xpath');

        $this->assertTrue($this->document->has('xpath', 'some xpath'));
        $this->assertFalse($this->document->has('xpath', 'some xpath'));
    }

    public function testHasContent()
    {
        $this->mockNamedFinder(
            '//some content',
            array('item1', 'item2'),
            array('content', 'some content')
        );

        $this->assertTrue($this->document->hasContent('some content'));
        $this->assertFalse($this->document->hasContent('some content'));
    }

    public function testHasLink()
    {
        $this->mockNamedFinder(
            '//link',
            array('link1', 'link2', 'link3'),
            array('link', 'some link')
        );

        $this->assertTrue($this->document->hasLink('some link'));
        $this->assertFalse($this->document->hasLink('some link'));
    }

    public function testHasButton()
    {
        $this->mockNamedFinder(
            '//button',
            array('button1', 'button2', 'button3'),
            array('button', 'some button')
        );

        $this->assertTrue($this->document->hasButton('some button'));
        $this->assertFalse($this->document->hasButton('some button'));
    }

    public function testHasField()
    {
        $this->mockNamedFinder(
            '//field',
            array('field1', 'field2', 'field3'),
            array('field', 'some field')
        );

        $this->assertTrue($this->document->hasField('some field'));
        $this->assertFalse($this->document->hasField('some field'));
    }

    public function testHasCheckedField()
    {
        $checkbox = m::mock('Behat\Mink\Element\NodeElement');
        $checkbox->shouldReceive('isChecked')->twice()->andReturn(true, false);

        $this->mockNamedFinder(
            '//field',
            array(array($checkbox), array(), array($checkbox)),
            array('field', 'some checkbox'),
            3
        );

        $this->assertTrue($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
    }

    public function testHasUncheckedField()
    {
        $checkbox = m::mock('Behat\Mink\Element\NodeElement');
        $checkbox->shouldReceive('isChecked')->twice()->andReturn(true, false);

        $this->mockNamedFinder(
            '//field',
            array(array($checkbox), array(), array($checkbox)),
            array('field', 'some checkbox'),
            3
        );

        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertTrue($this->document->hasUncheckedField('some checkbox'));
    }

    public function testHasSelect()
    {
        $this->mockNamedFinder(
            '//select',
            array('select'),
            array('select', 'some select field')
        );

        $this->assertTrue($this->document->hasSelect('some select field'));
        $this->assertFalse($this->document->hasSelect('some select field'));
    }

    public function testHasTable()
    {
        $this->mockNamedFinder(
            '//table',
            array('table'),
            array('table', 'some table')
        );

        $this->assertTrue($this->document->hasTable('some table'));
        $this->assertFalse($this->document->hasTable('some table'));
    }

    public function testClickLink()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('click')->once();

        $this->mockNamedFinder(
            '//link',
            array($node),
            array('link', 'some link')
        );

        $this->document->clickLink('some link');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->clickLink('some link');
    }

    public function testClickButton()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('press')->once();

        $this->mockNamedFinder(
            '//button',
            array($node),
            array('button', 'some button')
        );

        $this->document->pressButton('some button');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->pressButton('some button');
    }

    public function testFillField()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('setValue')->with('some val')->once();

        $this->mockNamedFinder(
            '//field',
            array($node),
            array('field', 'some field')
        );

        $this->document->fillField('some field', 'some val');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->fillField('some field', 'some val');
    }

    public function testCheckField()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('check')->once();

        $this->mockNamedFinder(
            '//field',
            array($node),
            array('field', 'some field')
        );

        $this->document->checkField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->checkField('some field');
    }

    public function testUncheckField()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('uncheck')->once();

        $this->mockNamedFinder(
            '//field',
            array($node),
            array('field', 'some field')
        );

        $this->document->uncheckField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->uncheckField('some field');
    }

    public function testSelectField()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('selectOption')->with('option2', false)->once();

        $this->mockNamedFinder(
            '//field',
            array($node),
            array('field', 'some field')
        );

        $this->document->selectFieldOption('some field', 'option2');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->selectFieldOption('some field', 'option2');
    }

    public function testAttachFileToField()
    {
        $node = m::mock('Behat\Mink\Element\NodeElement');
        $node->shouldReceive('attachFile')->with('/path/to/file')->once();

        $this->mockNamedFinder(
            '//field',
            array($node),
            array('field', 'some field')
        );

        $this->document->attachFileToField('some field', '/path/to/file');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->attachFileToField('some field', '/path/to/file');
    }

    public function testGetContent()
    {
        $expects = 'page content';
        $this->session->getDriver()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($expects);

        $this->assertEquals($expects, $this->document->getContent());
    }

    public function testGetText()
    {
        $expects = 'val1';
        $this->session->getDriver()
            ->shouldReceive('getText')
            ->with('//html')
            ->once()
            ->andReturn($expects);

        $this->assertEquals($expects, $this->document->getText());
    }
}
