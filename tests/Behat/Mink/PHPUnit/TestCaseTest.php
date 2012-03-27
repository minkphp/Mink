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
     * @covers Behat\Mink\PHPUnit\TestCase::assertCookieExists
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function testAssertCookieExistsTextFail()
    {
        $session = $this->getMockBuilder('Behat\Mink\Session')->setMethods(array('getCookie'))->
            disableOriginalConstructor()->getMock();
        $session->expects($this->any())->method('getCookie')->will($this->returnValue(null));

        TestCase::assertCookieExists($session, 'foo');
    }

    /**
     * @covers Behat\Mink\PHPUnit\TestCase::assertCookieExists
     */
    public function testAssertCookieExistsSuccess()
    {
        $session = $this->getMockBuilder('Behat\Mink\Session')->setMethods(array('getCookie'))->
            disableOriginalConstructor()->getMock();
        $session->expects($this->any())->method('getCookie')->will($this->returnValue('bar'));

        TestCase::assertCookieExists($session, 'foo');
    }

    /**
     * @covers Behat\Mink\PHPUnit\TestCase::assertPageContainsText
     * @expectedException PHPUnit_Framework_ExpectationFailedException
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
