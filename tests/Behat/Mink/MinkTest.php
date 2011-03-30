<?php

namespace Tests\Behat\Mink;

use Behat\Mink\Mink;

class MinkTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->mink = new Mink();
    }

    public function testSwitchToNotStartedDriver()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
        $driver
            ->expects($this->once())
            ->method('start');
        $driver
            ->expects($this->once())
            ->method('reset');

        $this->mink->registerDriver('everzet', $driver);
        $this->mink->switchToDriver('everzet');
        $this->mink->resetDriver();

        $this->setExpectedException('InvalidArgumentException');
        $this->mink->switchToDriver('undefined');
    }

    public function testSwitchToStartedDriver()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
        $driver
            ->expects($this->never())
            ->method('start');
        $driver
            ->expects($this->once())
            ->method('reset');

        $this->mink->registerDriver('everzet', $driver);
        $this->mink->switchToDriver('everzet');
        $this->mink->resetDriver();

        $this->setExpectedException('RuntimeException');
        $this->mink->switchToDefaultDriver();
    }

    public function testSwitchToDefaultDriver()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
        $driver
            ->expects($this->once())
            ->method('reset');

        $this->mink->registerDriver('everzet', $driver, true);
        $this->mink->resetDriver();
    }

    public function testSessionCreation()
    {
        $driver1 = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver1
            ->expects($this->exactly(2))
            ->method('isStarted')
            ->will($this->returnValue(true));
        $driver2 = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver2
            ->expects($this->exactly(4))
            ->method('isStarted')
            ->will($this->returnValue(true));

        $this->mink->registerDriver('driver1', $driver1, true);
        $this->mink->registerDriver('driver2', $driver2);

        $session = $this->mink->getSession();
        $this->assertInstanceOf('Behat\Mink\Session', $session);
        $this->assertSame($session, $this->mink->getSession());

        $this->mink->switchToDriver('driver2');
        $this->assertNotSame($session, $this->mink->getSession());
        $this->assertSame($this->mink->getSession(), $this->mink->getSession());
    }
}
