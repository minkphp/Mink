<?php

namespace Behat\Mink\Exception;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink "element not found" exception.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ElementNotFoundException extends Exception
{
    /**
     * Initializes exception.
     *
     * @param   string      $type       element type
     * @param   string      $locator    element locator
     * @param   Exception   $previous   previous exception
     */
    public function __construct($type = null, $locator = null, $previous = null)
    {
        if (null !== $type) {
            $message = $type . ' ';
        } else {
            $message = 'tag ';
        }

        if (null !== $locator) {
            $message .= 'with locator: "' . $locator . '" ';
        }

        $message .= 'not found';

        parent::__construct($message, 0, $previous);
    }
}
