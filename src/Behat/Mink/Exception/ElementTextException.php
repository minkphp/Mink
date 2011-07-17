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
 * Mink's element text exception.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ElementTextException extends ElementHtmlException
{
    /**
     * Returns exception message with additional context info.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getMessage()."\n\n"
             . $this->getResponseInfo()
             . $this->pipeString($this->trimString($this->element->getText()) . "\n");
    }
}
