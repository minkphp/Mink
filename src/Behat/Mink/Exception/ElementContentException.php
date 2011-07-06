<?php

namespace Behat\Mink\Exception;

use Behat\Mink\Session,
    Behat\Mink\Element\Element;

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
class ElementContentException extends ExpectationFailedException
{
    /**
     * Element instance.
     *
     * @var     Behat\Mink\Element\Element
     */
    protected $element;

    /**
     * Initializes exception.
     *
     * @param   Behat\Mink\Session  $session    session instance
     * @param   Element             $element    element
     * @param   Exception           $exception  expectation exception
     * @param   string              $message    optional message
     */
    public function __construct(Session $session, Element $element, \Exception $exception, $message = null)
    {
        $this->element = $element;

        parent::__construct($session, $exception, $message);
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
             . $this->pipeString($this->trimString($this->element->getText()) . "\n");
    }
}
