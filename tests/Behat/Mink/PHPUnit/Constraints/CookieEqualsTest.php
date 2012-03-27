<?php
/**
 * Mink
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
namespace Test\Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_ExpectationFailedException;

use Behat\Mink\PHPUnit\Constraints\CookieEquals;

/**
 * @group unittest
 */
class CookieEqualsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieEquals::__construct
     * @covers Behat\Mink\PHPUnit\Constraints\CookieEquals::toString
     */
    public function testToString()
    {
        $constr = new CookieEquals('foo', 'bar');
        $this->assertEquals('value of cookie "foo" is "bar"', $constr->toString());
    }

    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieEquals::matches
     */
    public function testMatches()
    {
        $constr = new CookieEquals('foo', 'bar');
        $this->assertTrue($constr->evaluate('bar', '', true));
        $this->assertFalse($constr->evaluate('baz', '', true));
    }

    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieEquals::failureDescription
     */
    public function testFailureDescription()
    {
        $constr = new CookieEquals('foo', 'bar');
        try
        {
            $constr->evaluate('baz', '%DESC%');
            $this->fail('PHPUnit_Framework_ExpectationFailedException exception expected');
        }
        catch (PHPUnit_Framework_ExpectationFailedException $e)
        {
            $this->assertContains('%DESC%', $e->getMessage(),
                'Failed asserting that exception message contains "%DESC%"');
        }
    }
}
