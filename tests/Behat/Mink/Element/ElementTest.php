<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Session;
use Behat\Mink\Selector\SelectorsHandler;

/**
 * @group unittest
 */
abstract class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Session.
     *
     * @var Session
     */
    protected $session;

    /**
     * Selectors.
     *
     * @var SelectorsHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectors;

    protected function setUp()
    {
        $this->session  = $this->getSessionWithMockedDriver();
        $this->selectors = $this->session->getSelectorsHandler();
    }

    protected function getSessionWithMockedDriver()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver
            ->expects($this->once())
            ->method('setSession');

        $selectors = $this->getMockBuilder('Behat\Mink\Selector\SelectorsHandler')->getMock();
        $session = new Session($driver, $selectors);

        $selectors
            ->expects($this->any())
            ->method('xpathLiteral')
            ->will($this->returnArgument(0));

        return $session;
    }

    protected function mockNamedFinder($xpath, array $results, $locator, $times = 2)
    {
        if (!is_array($results[0])) {
            $results = array($results, array());
        }

        $returnValue = call_user_func_array(array($this, 'onConsecutiveCalls'), $results);

        $this->session->getDriver()
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
