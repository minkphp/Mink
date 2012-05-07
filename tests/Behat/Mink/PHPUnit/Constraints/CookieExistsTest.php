<?php
namespace Test\Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_ExpectationFailedException;

use Behat\Mink\PHPUnit\Constraints\CookieExists;

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
class CookieExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieExists::__construct
     * @covers Behat\Mink\PHPUnit\Constraints\CookieExists::toString
     */
    public function testToString()
    {
        $constr = new CookieExists('foo');
        $this->assertEquals('cookie with name "foo" exists', $constr->toString());
    }

    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieExists::matches
     */
    public function testMatches()
    {
        $constr = new CookieExists('foo');
        $this->assertTrue($constr->evaluate('bar', '', true));
        $this->assertFalse($constr->evaluate(null, '', true));
    }

    /**
     * @covers Behat\Mink\PHPUnit\Constraints\CookieExists::failureDescription
     */
    public function testFailureDescription()
    {
        $constr = new CookieExists('foo');
        try
        {
            $constr->evaluate(null, '%DESC%');
            $this->fail('PHPUnit_Framework_ExpectationFailedException exception expected');
        }
        catch (PHPUnit_Framework_ExpectationFailedException $e)
        {
            $this->assertContains('%DESC%', $e->getMessage(),
                'Failed asserting that exception message contains "%DESC%"');
        }
    }
}
