<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementHtmlException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ElementHtmlExceptionTest extends TestCase
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
            ->method('getOuterHtml')
            ->will($this->returnValue("<div>\n    <h1>Hello world</h1>\n    <p>Test</p>\n</div>"));

        $expected = <<<'TXT'
Html error

+--[ HTTP/1.1 200 | http://localhost/test | %s ]
|
|  <div>
|      <h1>Hello world</h1>
|      <p>Test</p>
|  </div>
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver));

        $exception = new ElementHtmlException('Html error', $driver, $element);

        $this->assertEquals($expected, $exception->__toString());
    }

    /**
     * @return NodeElement&MockObject
     */
    private function getElementMock()
    {
        return $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
