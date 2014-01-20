<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Exception;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

/**
 * Mink's element attribute exception.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class ElementAttributeException extends ElementHtmlException
{
    /**
     * Initializes exception.
     *
     * @param string      $message   optional message
     * @param Session     $session   session instance
     * @param NodeElement $element   element
     * @param \Exception  $exception expectation exception
     */
    public function __construct($message, Session $session, NodeElement $element, \Exception $exception = null)
    {
        parent::__construct($message, $session, $element, $exception);
    }

    /**
     * Returns exception message with additional context info.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $pageText = $this->pipeString($this->trimString($this->element->getAttribute()) . "\n");
            $string   = sprintf("%s\n\n%s%s", $this->getMessage(), $this->getResponseInfo(), $pageText);
        } catch (\Exception $e) {
            return $this->getMessage();
        }

        return $string;
    }
}
