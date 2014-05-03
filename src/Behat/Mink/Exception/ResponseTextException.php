<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Exception;

/**
 * Exception thrown when an expectation on the response text fails.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ResponseTextException extends ExpectationException
{
    /**
     * Returns exception message with additional context info.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $pageText = $this->pipeString($this->trimString($this->getSession()->getPage()->getText()) . "\n");
            $string   = sprintf("%s\n\n%s%s", $this->getMessage(), $this->getResponseInfo(), $pageText);
        } catch (\Exception $e) {
            return $this->getMessage();
        }

        return $string;
    }
}
