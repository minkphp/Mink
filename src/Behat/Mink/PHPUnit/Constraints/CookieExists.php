<?php
namespace Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_Constraint;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Constraint that asserts that the cookie with given name exists.
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
class CookieExists extends PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @param string  $cookieName
     */
    public function __construct($cookieName)
    {
        $this->cookieName = $cookieName;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    protected function matches($other)
    {
        return null !== $other;
    }

    /**
     * Returns the description of the failure
     *
     * @param mixed $other  evaluated value or object
     *
     * @return string
     */
    protected function failureDescription($other)
    {
        return $this->toString();
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf('cookie with name "%s" exists', $this->cookieName);
    }
}
