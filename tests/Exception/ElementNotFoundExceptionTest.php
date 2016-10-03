<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Exception\ElementNotFoundException;

class ElementNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExceptionMessage
     */
    public function testBuildMessage($message, $type, $selector = null, $locator = null)
    {
        $driver = $this->getMock('Behat\Mink\Driver\DriverInterface');

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
}
