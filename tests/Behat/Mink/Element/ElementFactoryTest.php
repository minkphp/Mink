<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\ElementFactory;

/**
 * @group unittest
 */
class ElementFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNodeElement()
    {
        $driver = $this->getMock('Behat\Mink\Driver\DriverInterface');
        $selectorsHandler = $this->getMock('Behat\Mink\Selector\SelectorsHandler');

        $factory = new ElementFactory();

        $element = $factory->createNodeElement('test', $driver, $selectorsHandler);

        $this->assertInstanceOf('Behat\Mink\Element\NodeElement', $element);
        $this->assertEquals('test', $element->getXpath());
    }
}
