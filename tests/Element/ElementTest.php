<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\ElementFinder;
use Behat\Mink\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ElementTest extends TestCase
{
    /**
     * Session.
     *
     * @var Session
     */
    protected $session;

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
        $this->session = $this->createStub(Session::class);
        $this->session->method('getDriver')->willReturn($this->driver);
        $this->session->method('getElementFinder')->willReturn($this->elementFinder);
    }
}
