<?php

namespace Behat\Mink\Exception;

use Behat\Mink\Element\Element;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A standard way for elements to re-throw exceptions
 *
 * @author Chris Worfolk <xmeltrut@gmail.com>
 */
class ElementException extends Exception
{
    private $element;

    /**
     * Initialises exception.
     *
     * @param Element    $element   optional message
     * @param \Exception $exception exception
     */
    public function __construct(Element $element, \Exception $exception)
    {
        $this->element = $element;

        parent::__construct(sprintf("Exception thrown by %s\n%s",
            $element->getXpath(),
            $exception->getMessage()
        ));
    }

    /**
     * Override default toString so we don't send a full backtrace in verbose mode.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Get the element that caused the exception
     *
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
}
