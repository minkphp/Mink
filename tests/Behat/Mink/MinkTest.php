<?php

namespace Tests\Behat\Mink;

use Behat\Mink\Mink;

class MinkTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->mink = new Mink();
    }

    public function testRegisterSession()
    {
        $session = $this->getSessionMock();

        $this->assertFalse($this->mink->hasSession('not_registered'));
        $this->assertFalse($this->mink->hasSession('js'));
        $this->assertFalse($this->mink->hasSession('my'));

        $this->mink->registerSession('my', $session);

        $this->assertTrue($this->mink->hasSession('my'));
        $this->assertFalse($this->mink->hasSession('not_registered'));
        $this->assertFalse($this->mink->hasSession('js'));
    }

    public function testSessionAutostop()
    {
        $session1 = $this->getSessionMock();
        $session2 = $this->getSessionMock();
        $this->mink->registerSession('my1', $session1);
        $this->mink->registerSession('my2', $session2);

        $session1
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
        $session1
            ->expects($this->once())
            ->method('stop');
        $session2
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
        $session2
            ->expects($this->never())
            ->method('stop');

        unset($this->mink);
    }

    public function testNotStartedSession()
    {
        $session = $this->getSessionMock();

        $session
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
        $session
            ->expects($this->once())
            ->method('start');

        $this->mink->registerSession('mock_session', $session);
        $this->assertSame($session, $this->mink->getSession('mock_session'));

        $this->setExpectedException('InvalidArgumentException');

        $this->mink->getSession('not_registered');
    }

    public function testGetAlreadyStartedSession()
    {
        $session = $this->getSessionMock();

        $session
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
        $session
            ->expects($this->never())
            ->method('start');

        $this->mink->registerSession('mock_session', $session);
        $this->assertSame($session, $this->mink->getSession('mock_session'));
    }

    public function testSetActiveSessionName()
    {
        $this->assertNull($this->mink->getActiveSessionName());

        $session = $this->getSessionMock();
        $this->mink->registerSession('session_name', $session);
        $this->mink->setActiveSessionName('session_name');

        $this->assertEquals('session_name', $this->mink->getActiveSessionName());

        $this->setExpectedException('InvalidArgumentException');

        $this->mink->setActiveSessionName('not_registered');
    }

    public function testGetActiveSession()
    {
        $session1 = $this->getSessionMock();
        $session2 = $this->getSessionMock();

        $this->assertNotSame($session1, $session2);

        $this->mink->registerSession('session_1', $session1);
        $this->mink->registerSession('session_2', $session2);
        $this->mink->setActiveSessionName('session_2');

        $this->assertSame($session1, $this->mink->getSession('session_1'));
        $this->assertSame($session2, $this->mink->getSession('session_2'));
        $this->assertSame($session2, $this->mink->getSession());

        $this->mink->setActiveSessionName('session_1');

        $this->assertSame($session1, $this->mink->getSession());
    }

    public function testGetNoActiveSession()
    {
        $session1 = $this->getSessionMock();

        $this->mink->registerSession('session_1', $session1);

        $this->setExpectedException('InvalidArgumentException');

        $this->mink->getSession();
    }

    private function getSessionMock()
    {
        return $this->getMockBuilder('Behat\Mink\Session')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
