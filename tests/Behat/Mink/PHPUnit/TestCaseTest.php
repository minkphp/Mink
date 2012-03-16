<?php

namespace Test\Behat\Mink\PHPUnit;

use Behat\Mink\PHPUnit\TestCase,
    Behat\Mink\Session;

/**
 * @group unittest
 */
class TestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Behat\Mink\PHPUnit\TestCase::assertPageContainsText
     * @expectedException Behat\Mink\Exception\ResponseTextException
     */
    public function testAssertPageContainsTextFail()
    {
        $page = $this->getMock('stdClass', array('getText'));
        $page->expects($this->any())->method('getText')->will($this->returnValue('foo bar baz'));

        $session = $this->getMockBuilder('Behat\Mink\Session')->setMethods(array('getPage'))->
            disableOriginalConstructor()->getMock();
        $session->expects($this->any())->method('getPage')->will($this->returnValue($page));

        TestCase::assertPageContainsText($session, 'bad');
    }

    /**
     * @covers Behat\Mink\PHPUnit\TestCase::assertPageContainsText
     */
    public function testAssertPageContainsTextSuccess()
    {
        $page = $this->getMock('stdClass', array('getText'));
        $page->expects($this->any())->method('getText')->will($this->returnValue('foo bar baz'));

        $session = $this->getMockBuilder('Behat\Mink\Session')->setMethods(array('getPage'))->
            disableOriginalConstructor()->getMock();
        $session->expects($this->any())->method('getPage')->will($this->returnValue($page));

        TestCase::assertPageContainsText($session, 'bar');
    }
}
