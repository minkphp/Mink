<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Selector\SelectorsHandler;

/**
 * @group unittest
 */
abstract class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DriverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * Selectors.
     *
     * @var SelectorsHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectors;

    protected function setUp()
    {
        $this->driver  = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $this->selectors = $this->getMockBuilder('Behat\Mink\Selector\SelectorsHandler')->getMock();
    }

    protected function mockNamedFinder($xpath, array $results, $locator, $times = 2)
    {
        if (!is_array($results[0])) {
            $results = array($results, array());
        }

        $returnValue = call_user_func_array(array($this, 'onConsecutiveCalls'), $results);

        $this->driver
            ->expects($this->exactly($times))
            ->method('find')
            ->with('//html' . $xpath)
            ->will($returnValue);

        $this->selectors
            ->expects($this->exactly($times))
            ->method('selectorToXpath')
            ->with('named', $locator)
            ->will($this->returnValue($xpath));
    }
}
