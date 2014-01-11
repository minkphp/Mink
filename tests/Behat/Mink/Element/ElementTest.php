<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use Mockery as m;
use Mockery\MockInterface;

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
     * @var SelectorsHandler|MockInterface
     */
    protected $selectors;

    protected function setUp()
    {
        $this->session  = $this->getSessionWithMockedDriver();
        $this->selectors = $this->session->getSelectorsHandler();
    }

    protected function getSessionWithMockedDriver()
    {
        $driver = m::mock('Behat\Mink\Driver\DriverInterface');
        $driver->shouldReceive('setSession')->once();

        $selectors = m::mock('Behat\Mink\Selector\SelectorsHandler');
        $session = new Session($driver, $selectors);

        $selectors->shouldReceive('xpathLiteral')->andReturnUsing(function ($s) {
            return $s;
        });

        return $session;
    }

    protected function mockNamedFinder($xpath, array $results, $locator, $times = 2)
    {
        if (!is_array($results[0])) {
            $results = array($results, array());
        }

        $this->session->getDriver()
            ->shouldReceive('find')
            ->with('//html' . $xpath)
            ->times($times)
            ->andReturnValues($results);

        $this->selectors
            ->shouldReceive('selectorToXpath')
            ->with('named', $locator)
            ->times($times)
            ->andReturn($xpath);
    }
}
