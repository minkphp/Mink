<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Exception;

use Behat\Mink\Session;

/**
 * Mink's expectation exception.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ExpectationException extends Exception
{
    /**
     * Initializes exception.
     *
     * @param string     $message   optional message
     * @param Session    $session   session instance
     * @param \Exception $exception expectation exception
     */
    public function __construct($message, Session $session, \Exception $exception = null)
    {
        if (!$message && null !== $exception) {
            $message = $exception->getMessage();
        }

        parent::__construct($message, $session);
    }

    /**
     * Returns exception message with additional context info.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $pageText = $this->pipeString($this->trimBody($this->getSession()->getPage()->getContent()) . "\n");
            $string   = sprintf("%s\n\n%s%s", $this->getMessage(), $this->getResponseInfo(), $pageText);
        } catch (\Exception $e) {
            return $this->getMessage();
        }

        return $string;
    }
}
