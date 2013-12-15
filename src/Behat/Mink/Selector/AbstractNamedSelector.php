<?php

namespace Behat\Mink\Selector;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Named selectors engine. Uses registered XPath selectors to create new expressions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class AbstractNamedSelector implements SelectorInterface
{
    protected $selectors = array();

    /**
     * Registers new XPath selector with specified name.
     *
     * @param string $name  name for selector
     * @param string $xpath xpath expression
     */
    public function registerNamedXpath($name, $xpath)
    {
        $this->selectors[$name] = $xpath;
    }

    /**
     * Translates provided locator into XPath.
     *
     * @param string|array $locator selector name or array of (selector_name, locator)
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function translateToXPath($locator)
    {
        if (2 < count($locator)) {
            throw new \InvalidArgumentException('PartialSelector expects array(name, locator) as argument');
        }

        if (2 == count($locator)) {
            $selector   = $locator[0];
            $locator    = $locator[1];
        } else {
            $selector   = (string) $locator;
            $locator    = null;
        }

        if (!isset($this->selectors[$selector])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown named selector provided: "%s". Expected one of (%s)',
                $selector,
                implode(', ', array_keys($this->selectors))
            ));
        }

        $xpath = $this->selectors[$selector];

        if (null !== $locator) {
            $xpath = strtr($xpath, array('%locator%' => $locator));
        }

        return $xpath;
    }
}
