<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Exception\ElementNotFoundException;
use PHPUnit\Framework\TestCase;

class ElementNotFoundExceptionTest extends TestCase
{
    /**
     * @dataProvider provideExceptionMessage
     */
    public function testBuildMessage($message, $type, $selector = null, $locator = null)
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();

        $exception = new ElementNotFoundException($driver, $type, $selector, $locator);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function provideExceptionMessage()
    {
        return array(
            array('Tag not found.', null),
            array('Field not found.', 'field'),
            array('Tag matching locator "foobar" not found.', null, null, 'foobar'),
            array('Tag matching css "foobar" not found.', null, 'css', 'foobar'),
            array('Field matching xpath "foobar" not found.', 'Field', 'xpath', 'foobar'),
            array('Tag with name "foobar" not found.', null, 'name', 'foobar'),
        );
    }

    /**
     * @group legacy
     */
    public function testConstructWithSession()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $session = $this->getMockBuilder('Behat\Mink\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->any())
            ->method('getDriver')
            ->will($this->returnValue($driver));

        $exception = new ElementNotFoundException($session);

        $this->assertEquals('Tag not found.', $exception->getMessage());
    }
}
