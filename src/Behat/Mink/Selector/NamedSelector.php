<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Selector;

/**
 * Named selectors engine. Uses registered XPath selectors to create new expressions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NamedSelector implements SelectorInterface
{
    private $replacements = array(
        // simple replacements
        '%tagTextMatch%' => 'contains(normalize-space(string(.)), %locator%)',
        '%labelTextMatch%' => './@id = //label[%tagTextMatch%]/@for',
        '%idMatch%' => './@id = %locator%',
        '%valueMatch%' => 'contains(./@value, %locator%)',
        '%idOrValueMatch%' => '(%idMatch% or %valueMatch%)',
        '%idOrNameMatch%' => '(%idMatch% or ./@name = %locator%)',
        '%placeholderMatch%' => './@placeholder = %locator%',
        '%titleMatch%' => 'contains(./@title, %locator%)',
        '%altMatch%' => 'contains(./@alt, %locator%)',

        // complex replacements
        '%fieldMatch%' => '(%idOrNameMatch% or %labelTextMatch% or %placeholderMatch%)',
        '%fieldFilter%' => 'self::input | self::textarea | self::select',
        '%notFieldTypeFilter%' => "not(%buttonTypeFilter% or ./@type = 'hidden')",
        '%buttonMatch%' => '%idOrNameMatch% or %valueMatch% or %titleMatch%',
        '%buttonTypeFilter%' => "./@type = 'submit' or ./@type = 'image' or ./@type = 'button' or ./@type = 'reset'",
        '%linkMatch%' => '(%idMatch% or %tagTextMatch% or %titleMatch% or contains(./@rel, %locator%))',
        '%imgAltMatch%' => './/img[%altMatch%]',
    );

    private $selectors = array(
        'fieldset' => <<<XPATH
.//fieldset
[(%idMatch% or .//legend[%tagTextMatch%])]
XPATH

        ,'field' => <<<XPATH
.//*
[%fieldFilter%][%notFieldTypeFilter%][%fieldMatch%]
|
.//label[%tagTextMatch%]//.//*[%fieldFilter%][%notFieldTypeFilter%]
XPATH

        ,'link' => <<<XPATH
.//a
[./@href][(%linkMatch% or %imgAltMatch%)]
|
.//*
[./@role = 'link'][(%idOrValueMatch% or %titleMatch% or %tagTextMatch%)]
XPATH

        ,'button' => <<<XPATH
.//input
[%buttonTypeFilter%][(%buttonMatch%)]
|
.//input
[./@type = 'image'][%altMatch%]
|
.//button
[(%buttonMatch% or %tagTextMatch%)]
|
.//*
[./@role = 'button'][(%buttonMatch% or %tagTextMatch%)]
XPATH

        ,'link_or_button' => <<<XPATH
.//a
[./@href][(%linkMatch% or %imgAltMatch%)]
|
.//input
[%buttonTypeFilter%][(%idOrValueMatch% or %titleMatch%)]
|
.//input
[./@type = 'image'][%altMatch%]
|
.//button
[(%idOrValueMatch% or %titleMatch% or %tagTextMatch%)]
|
.//*
[(./@role = 'button' or ./@role = 'link')][(%idOrValueMatch% or %titleMatch% or %tagTextMatch%)]
XPATH

        ,'content' => <<<XPATH
./descendant-or-self::*
[%tagTextMatch%]
XPATH

        ,'select' => <<<XPATH
.//select
[%fieldMatch%]
|
.//label[%tagTextMatch%]//.//select
XPATH

        ,'checkbox' => <<<XPATH
.//input
[./@type = 'checkbox'][%fieldMatch%]
|
.//label[%tagTextMatch%]//.//input[./@type = 'checkbox']
XPATH

        ,'radio' => <<<XPATH
.//input
[./@type = 'radio'][%fieldMatch%]
|
.//label[%tagTextMatch%]//.//input[./@type = 'radio']
XPATH

        ,'file' => <<<XPATH
.//input
[./@type = 'file'][%fieldMatch%]
|
.//label[%tagTextMatch%]//.//input[./@type = 'file']
XPATH

        ,'optgroup' => <<<XPATH
.//optgroup
[contains(./@label, %locator%)]
XPATH

        ,'option' => <<<XPATH
.//option
[(./@value = %locator% or %tagTextMatch%)]
XPATH

        ,'table' => <<<XPATH
.//table
[(%idMatch% or contains(.//caption, %locator%))]
XPATH
    );

    /**
     * Creates selector instance.
     */
    public function __construct()
    {
        foreach ($this->replacements as $from => $to) {
            $this->replacements[$from] = strtr($to, $this->replacements);
        }

        foreach ($this->selectors as $alias => $selector) {
            $this->selectors[$alias] = strtr($selector, $this->replacements);
        }
    }

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
