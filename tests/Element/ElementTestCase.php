<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\ElementFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ElementTestCase extends TestCase
{
    /**
     * @var DriverInterface&MockObject
     */
    protected $driver;

    /**
     * @var ElementFinder&MockObject
     */
    protected $elementFinder;

    /**
     * @before
     */
    protected function prepareSession(): void
    {
        $this->driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $this->elementFinder = $this->createMock(ElementFinder::class);
    }
}
