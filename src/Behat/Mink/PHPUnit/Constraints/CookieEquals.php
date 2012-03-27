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
 * Constraint that asserts that the cookie with given name have given value.
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
class CookieEquals extends PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var string
     */
    protected $cookieValue;

    /**
     * @param string  $cookieName
     * @param string  $cookieValue
     */
    public function __construct($cookieName, $cookieValue)
    {
        $this->cookieName = $cookieName;
        $this->cookieValue = $cookieValue;
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
        return $this->cookieValue === $other;
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
        return sprintf('value of cookie "%s" is "%s"', $this->cookieName, $this->cookieValue);
    }
}
