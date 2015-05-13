<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

use Behat\Mink\Session;

/**
 * Document element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class DocumentElement extends TraversableElement
{
    private $xpath;

    /**
     * Initializes node element.
     *
     * @param string  $xpath   element xpath
     * @param Session $session session instance
     */
    public function __construct($xpath, Session $session)
    {
        $this->xpath = $xpath;

        parent::__construct($session);
    }

    /**
     * Returns XPath for handled element.
     *
     * @return string
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * Returns document content.
     *
     * @return string
     */
    public function getContent()
    {
        return trim($this->getDriver()->getContent());
    }

    /**
     * Check whether document has specified content.
     *
     * @param string $content
     *
     * @return Boolean
     */
    public function hasContent($content)
    {
        return $this->has('named', array('content', $content));
    }
}
