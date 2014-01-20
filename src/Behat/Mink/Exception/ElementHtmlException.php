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
use Behat\Mink\Element\Element;

/**
 * Mink's element HTML exception.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ElementHtmlException extends ExpectationException
{
    /**
     * Element instance.
     *
     * @var Element
     */
    protected $element;

    /**
     * Initializes exception.
     *
     * @param string     $message   optional message
     * @param Session    $session   session instance
     * @param Element    $element   element
     * @param \Exception $exception expectation exception
     */
    public function __construct($message, Session $session, Element $element, \Exception $exception = null)
    {
        $this->element = $element;

        parent::__construct($message, $session, $exception);
    }

    /**
     * Returns exception message with additional context info.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $pageText = $this->pipeString($this->trimString($this->element->getHtml()) . "\n");
            $string   = sprintf("%s\n\n%s%s", $this->getMessage(), $this->getResponseInfo(), $pageText);
        } catch (\Exception $e) {
            return $this->getMessage();
        }

        return $string;
    }
}
