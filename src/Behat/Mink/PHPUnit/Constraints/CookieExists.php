<?php
/**
 * Mink
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
namespace Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_Constraint;

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
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf('cookie with name "%s" exists', $this->cookieName);
    }
}
