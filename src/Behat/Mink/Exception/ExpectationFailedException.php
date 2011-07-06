<?php

namespace Behat\Mink\Exception;

use Behat\Mink\Session;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink "expectation failed" exception.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ExpectationFailedException extends Exception
{
    /**
     * Initializes exception.
     *
     * @param   Behat\Mink\Session  $session    session instance
     * @param   Exception           $exception  expectation exception
     * @param   string              $message    optional message
     */
    public function __construct(Session $session, \Exception $exception, $message = null)
    {
        parent::__construct($message ?: $exception->getMessage(), $session);
    }

    /**
     * Returns exception message with additional context info.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getMessage()." on page:\n\n"
             . $this->getResponseInfo()
             . $this->pipeString($this->trimBody($this->getSession()->getPage()->getContent()) . "\n");
    }
}
