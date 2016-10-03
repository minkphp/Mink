<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\ElementFinder;

abstract class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DriverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var ElementFinder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $elementFinder;

    protected function setUp()
    {
        $this->driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $this->elementFinder = $this->getMockBuilder('Behat\Mink\Element\ElementFinder')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
