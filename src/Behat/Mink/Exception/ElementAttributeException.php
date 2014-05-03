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
 * Exception thrown when an expectation on an attribute of the element fails.
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

    protected function getContext()
    {
        return $this->element->getAttribute();
    }
}
