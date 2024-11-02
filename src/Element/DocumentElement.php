<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

/**
 * Document element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class DocumentElement extends TraversableElement
{
    /**
     * Returns XPath for handled element.
     *
     * @return string
     */
    public function getXpath()
    {
        return '//html';
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
     * @return bool
     */
    public function hasContent(string $content)
    {
        return $this->has('named', array('content', $content));
    }
}
