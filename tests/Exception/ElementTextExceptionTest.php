<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Exception\ElementTextException;
use Behat\Mink\Tests\BaseTestCase;

class ElementTextExceptionTest extends BaseTestCase
{
    public function testExceptionToString()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $element = $this->getElementMock();

        $driver->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $driver->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://localhost/test'));

        $element->expects($this->any())
            ->method('getText')
            ->will($this->returnValue("Hello world\nTest\n"));

        $expected = <<<'TXT'
Text error

+--[ HTTP/1.1 200 | http://localhost/test | %s ]
|
|  Hello world
|  Test
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver));

        $exception = new ElementTextException('Text error', $driver, $element);

        $this->assertEquals($expected, $exception->__toString());
    }

    private function getElementMock()
    {
        return $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
