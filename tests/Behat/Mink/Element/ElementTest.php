<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Session;
use Behat\Mink\Selector\SelectorsHandler;

/**
 * @group unittest
 */
abstract class ElementTest extends \PHPUnit_Framework_TestCase
{
    protected function getSessionWithMockedDriver()
    {
        $driver     = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $selectors  = new SelectorsHandler();
        $session    = new Session($driver, $selectors);

        return $session;
    }
}
