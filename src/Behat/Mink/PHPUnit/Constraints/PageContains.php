<?php
/**
 * Mink
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
namespace Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_Constraint_StringContains,
    PHPUnit_Util_Type;

/**
 * Constraint that asserts that the page contains a given string.
 *
 * @package Mink
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 */
class PageContains extends PHPUnit_Framework_Constraint_StringContains
{
    /**
     * Returns the description of the failure
     *
     * @param mixed $other  evaluated value or object
     *
     * @return string
     */
    protected function failureDescription($other)
    {
        return 'page text ' . $this->toString();
    }
}
