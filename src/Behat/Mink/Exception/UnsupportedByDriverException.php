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
class UnsupportedByDriverException extends DriverException
{
    /**
     * Initializes exception.
     *
     * @param   string                              $title      what is unsupported?
     * @param   Behat\Mink\Driver\DriverInterface   $driver     driver instance
     * @param   Exception                           $previous   previous exception
     */
    public function __construct($title, DriverInterface $driver, $previous = null)
    {
        $message = sprintf('%s by %s', $title, get_class($driver));

        parent::__construct($message, 0, $previous);
    }
}
