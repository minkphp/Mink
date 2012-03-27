<?php
namespace Behat\Mink\PHPUnit\Constraints;

use PHPUnit_Framework_Constraint_StringContains,
    PHPUnit_Util_Type;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
