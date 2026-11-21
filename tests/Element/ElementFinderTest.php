<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\ElementFinder;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Selector\Xpath\Manipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ElementFinderTest extends TestCase
{
    /**
     * @var MockObject&DriverInterface
     */
    private $driver;

    /**
     * @var SelectorsHandler&MockObject
     */
    private $selectorsHandler;

    /**
     * @var Manipulator&MockObject
     */
    private $manipulator;

    /**
     * @var ElementFinder
     */
    private $finder;

    protected function setUp(): void
    {
        $this->driver = $this->createMock(DriverInterface::class);
        $this->selectorsHandler = $this->createMock(SelectorsHandler::class);
        $this->manipulator = $this->createMock(Manipulator::class);

        $this->finder = new ElementFinder($this->driver, $this->selectorsHandler, $this->manipulator);
    }

    public function testNotFound()
    {
        $this->selectorsHandler->expects($this->once())
            ->method('selectorToXpath')
            ->with('css', 'h3 > a')
            ->will($this->returnValue('css_xpath'));

        $this->manipulator->expects($this->once())
            ->method('prepend')
            ->with('css_xpath', 'parent_xpath')
            ->will($this->returnValue('full_xpath'));

        $this->driver->expects($this->once())
            ->method('find')
            ->with('full_xpath')
            ->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->finder->findAll('css', 'h3 > a', 'parent_xpath'));
    }

    public function testFound()
    {
        $this->selectorsHandler->expects($this->once())
            ->method('selectorToXpath')
            ->with('css', 'h3 > a')
            ->will($this->returnValue('css_xpath'));

        $this->manipulator->expects($this->once())
            ->method('prepend')
            ->with('css_xpath', 'parent_xpath')
            ->will($this->returnValue('full_xpath'));

        $this->driver->expects($this->once())
            ->method('find')
            ->with('full_xpath')
            ->will($this->returnValue(array('element1', 'element2')));

        $results = $this->finder->findAll('css', 'h3 > a', 'parent_xpath');

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NodeElement::class, $results);
        $this->assertEquals('element1', $results[0]->getXpath());
        $this->assertEquals('element2', $results[1]->getXpath());
    }

    public function testNamedFound()
    {
        $this->selectorsHandler->expects($this->once())
            ->method('selectorToXpath')
            ->with('named_exact', 'test')
            ->will($this->returnValue('named_xpath'));

        $this->manipulator->expects($this->once())
            ->method('prepend')
            ->with('named_xpath', 'parent_xpath')
            ->will($this->returnValue('full_xpath'));

        $this->driver->expects($this->once())
            ->method('find')
            ->with('full_xpath')
            ->will($this->returnValue(array('element1', 'element2')));

        $results = $this->finder->findAll('named', 'test', 'parent_xpath');

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NodeElement::class, $results);
        $this->assertEquals('element1', $results[0]->getXpath());
        $this->assertEquals('element2', $results[1]->getXpath());
    }

    public function testNamedPartialFallback()
    {
        $this->selectorsHandler->expects($this->exactly(2))
            ->method('selectorToXpath')
            ->will($this->returnValueMap(array(
                array('named_exact', 'test', 'named_xpath'),
                array('named_partial', 'test', 'partial_xpath'),
            )));

        $this->manipulator->expects($this->exactly(2))
            ->method('prepend')
            ->willReturnMap(array(
                array('named_xpath', 'parent_xpath', 'full_xpath'),
                array('partial_xpath', 'parent_xpath', 'full_partial_xpath'),
            ));

        $this->driver->expects($this->exactly(2))
            ->method('find')
            ->willReturnMap(array(
                array('full_xpath', array()),
                array('full_partial_xpath', array('element1', 'element2')),
            ));

        $results = $this->finder->findAll('named', 'test', 'parent_xpath');

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NodeElement::class, $results);
        $this->assertEquals('element1', $results[0]->getXpath());
        $this->assertEquals('element2', $results[1]->getXpath());
    }
}
