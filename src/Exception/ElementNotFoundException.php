<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Exception;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;

/**
 * Exception thrown when an expected element is not found.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ElementNotFoundException extends ExpectationException
{
    /**
     * Initializes exception.
     *
     * @param DriverInterface|Session $driver   driver instance
     * @param string|null             $type     element type
     * @param string|null             $selector element selector type
     * @param string|null             $locator  element locator
     */
    public function __construct($driver, ?string $type = null, ?string $selector = null, ?string $locator = null)
    {
        $message = '';

        if (null !== $type) {
            $message .= ucfirst($type);
        } else {
            $message .= 'Tag';
        }

        if (null !== $locator) {
            if (null === $selector || in_array($selector, array('css', 'xpath'))) {
                $selector = 'matching '.($selector ?: 'locator');
            } else {
                $selector = 'with '.$selector;
            }
            $message .= ' '.$selector.' "'.$locator.'"';
        }

        $message .= ' not found.';

        parent::__construct($message, $driver);
    }
}
