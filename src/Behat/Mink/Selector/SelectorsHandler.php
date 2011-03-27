<?php

namespace Behat\Mink\Selector;

use Behat\Mink\Selector\SelectorInterface;

class SelectorsHandler
{
    private $selectors;

    public function __construct(array $selectors = array())
    {
        $this->registerSelector('named', new NamedSelector());

        foreach ($selectors as $name => $selector) {
            $this->registerSelector($name, $selector);
        }
    }

    public function registerSelector($name, SelectorInterface $selector)
    {
        $this->selectors[$name] = $selector;
    }

    public function isSelectorRegistered($name)
    {
        return isset($this->selectors[$name]);
    }

    public function getSelector($name)
    {
        if (!$this->isSelectorRegistered($name)) {
            throw new \InvalidArgumentException("Selector \"$name\" is not registered.");
        }

        return $this->selectors[$name];
    }

    public function selectorToXpath($selector, $locator)
    {
        if ('xpath' === $selector) {
            return $locator;
        }

        return $this->getSelector($selector)->translateToXPath($locator);
    }

    public function xpathLiteral($s)
    {
        if (false === strpos($s, "'")) {
            return sprintf("'%s'", $s);
        }

        if (false === strpos($s, '"')) {
            return sprintf('"%s"', $s);
        }

        $string = $s;
        $parts = array();
        while (true) {
            if (false !== $pos = strpos($string, "'")) {
                $parts[] = sprintf("'%s'", substr($string, 0, $pos));
                $parts[] = "\"'\"";
                $string = substr($string, $pos + 1);
            } else {
                $parts[] = "'$string'";
                break;
            }
        }

        return sprintf("concat(%s)", implode($parts, ','));
    }
}
