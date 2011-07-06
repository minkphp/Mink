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
 * Mink "plain text response exception failed" exception.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PlainTextResponseException extends ExpectationFailedException
{
    /**
     * Returns exception message with additional context info.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getMessage()." on page:\n\n"
             . $this->getResponseInfo()
             . $this->pipeString($this->trimString($this->getSession()->getPage()->getPlainText()) . "\n");
    }
}
