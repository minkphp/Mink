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

        return $session;
    }

    protected function mockNamedFinder($xpath, array $results, $locator, $times = 2)
    {
        if (!is_array($results[0])) {
            $results = array($results, array());
        }

        // In case of empty results, a second call will be done using the partial selector
        $processedResults = array();
        foreach ($results as $result) {
            $processedResults[] = $result;
            if (empty($result)) {
                $processedResults[] = $result;
                $times++;
            }
        }

        $returnValue = call_user_func_array(array($this, 'onConsecutiveCalls'), $processedResults);

        $this->session->getDriver()
            ->expects($this->exactly($times))
            ->method('find')
            ->with('//html' . $xpath)
            ->will($returnValue);

        $this->selectors
            ->expects($this->exactly($times))
            ->method('selectorToXpath')
            ->with($this->logicalOr('named_exact', 'named_partial'), $locator)
            ->will($this->returnValue($xpath));
    }
}
