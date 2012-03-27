<?php
namespace Test\Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_ExpectationFailedException;

use Behat\Mink\PHPUnit\Constraints\CookieEquals;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
