<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\TestCase;

class ExpectationExceptionTest extends TestCase
{
    public function testEmptyMessageAndPreviousException()
    {
        $exception = new ExpectationException('', $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock(), new \Exception('Something failed'));

        $this->assertEquals('Something failed', $exception->getMessage());
    }

    public function testExceptionToString()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();

        $driver->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $driver->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://localhost/test'));

        $html = "<html><head><title>Hello</title></head>\n<body>\n<h1>Hello world</h1>\n<p>Test</p>\n</body></html>";
        $driver->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($html));

        $expected = <<<'TXT'
Expectation failure

+--[ HTTP/1.1 200 | http://localhost/test | %s ]
|
|  <body>
|  <h1>Hello world</h1>
|  <p>Test</p>
|  </body>
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver));

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals($expected, $exception->__toString());
    }

    public function testUnsupportedStatusCode()
    {
        $driver = $this->createStub(DriverInterface::class);

        $driver->method('getStatusCode')
            ->willThrowException(new UnsupportedDriverActionException('Status code is not supported.', $driver));
        $driver->method('getCurrentUrl')
            ->willReturn('http://localhost/test');

        $html = "<html><head><title>Hello</title></head>\n<body>\n<h1>Hello world</h1>\n<p>Test</p>\n</body></html>";
        $driver->method('getContent')
            ->willReturn($html);

        $expected = <<<'TXT'
Expectation failure

+--[ http://localhost/test | %s ]
|
|  <body>
|  <h1>Hello world</h1>
|  <p>Test</p>
|  </body>
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver));

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals($expected, $exception->__toString());
    }

    public function testBigContent()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();

        $driver->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $driver->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://localhost/test'));

        $body = str_repeat('a', 1001 - strlen('<body></body>'));

        $html = sprintf("<html><head><title>Hello</title></head>\n<body>%s</body></html>", $body);
        $driver->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($html));

        $expected = <<<'TXT'
Expectation failure

+--[ HTTP/1.1 200 | http://localhost/test | %s ]
|
|  <body>%s</b...
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver), $body);

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals($expected, $exception->__toString());
    }

    public function testExceptionWhileRenderingString()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $driver->expects($this->any())
            ->method('getContent')
            ->will($this->throwException(new \Exception('Broken page')));

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals('Expectation failure', $exception->__toString());
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

        $exception = new ExpectationException('', $session, new \Exception('Something failed'));

        $this->assertEquals('Something failed', $exception->getMessage());
    }
}
