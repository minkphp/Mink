<?php

namespace Behat\Mink\Selector;

class NamedSelector implements SelectorInterface
{
    private $selectors = array(
        'field' => <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][((./@id = %locator% or ./@name = %locator%) or ./@id = //label[contains(normalize-space(string(.)), %locator%)]/@for)] | .//label[contains(normalize-space(string(.)), %locator%)]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH
        ,'link' => <<<XPATH
.//a[./@href][(((./@id = %locator% or contains(normalize-space(string(.)), %locator%)) or contains(./@title, %locator%)) or .//img[contains(./@alt, %locator%)])]
XPATH
        ,'button' => <<<XPATH
.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][(./@id = %locator% or contains(./@value, %locator%))] | .//input[./@type = 'image'][contains(./@alt, %locator%)] | .//button[((./@id = %locator% or contains(./@value, %locator%)) or contains(normalize-space(string(.)), %locator%))] | .//input[./@type = 'image'][contains(./@alt, %locator%)]
XPATH
        ,'content' => <<<XPATH
./descendant-or-self::*[contains(normalize-space(.), %locator%)]
XPATH
        ,'select' => <<<XPATH
.//select[((./@id = %locator% or ./@name = %locator%) or ./@id = //label[contains(normalize-space(string(.)), %locator%)]/@for)] | .//label[contains(normalize-space(string(.)), %locator%)]//.//select
XPATH
        ,'table' => <<<XPATH
.//table[(./@id = %locator% or contains(.//caption, %locator%))]
XPATH
    );

    public function registerNamedXpath($name, $xpath)
    {
        $this->selectors[$name] = $xpath;
    }

    public function translateToXPath($locator)
    {
        if (2 < count($locator)) {
            throw new \InvalidArgumentException('NamedSelector expects array(name, locator) as argument');
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
