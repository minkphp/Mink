<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink;

use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementHtmlException;
use Behat\Mink\Exception\ElementTextException;
use Behat\Mink\Exception\ElementAttributeException;
use Behat\Mink\Exception\ElementAttributeNotFoundException;

/**
 * Mink web assertions tool.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class WebAssert
{
    protected $session;

    /**
     * Initializes assertion engine.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Checks that current session address is equals to provided one.
     *
     * @param string $page
     *
     * @throws ExpectationException
     */
    public function addressEquals($page)
    {
        $expected = $this->cleanUrl($page);
        $actual   = $this->getCurrentUrlPath();

        if ($actual !== $expected) {
            $message = sprintf('Current page is "%s", but "%s" expected.', $actual, $expected);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that current session address is not equals to provided one.
     *
     * @param string $page
     *
     * @throws ExpectationException
     */
    public function addressNotEquals($page)
    {
        $expected = $this->cleanUrl($page);
        $actual   = $this->getCurrentUrlPath();

        if ($actual === $expected) {
            $message = sprintf('Current page is "%s", but should not be.', $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that current session address matches regex.
     *
     * @param string $regex
     *
     * @throws ExpectationException
     */
    public function addressMatches($regex)
    {
        $actual = $this->getCurrentUrlPath();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('Current page "%s" does not match the regex "%s".', $actual, $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specified cookie exists and its value equals to a given one
     *
     * @param string $name  cookie name
     * @param string $value cookie value
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function cookieEquals($name, $value)
    {
        $this->cookieExists($name);
        $actualValue = $this->session->getCookie($name);
        if ($actualValue != $value) {
            $message = sprintf('Cookie "%s" value is "%s", but should be "%s".', $name, $actualValue, $value);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specified cookie exists
     *
     * @param string $name cookie name
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function cookieExists($name)
    {
        if ($this->session->getCookie($name) === null) {
            $message = sprintf('Cookie "%s" is not set, but should be.', $name);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that current response code equals to provided one.
     *
     * @param integer $code
     *
     * @throws ExpectationException
     */
    public function statusCodeEquals($code)
    {
        $actual = $this->session->getStatusCode();

        if (intval($code) !== intval($actual)) {
            $message = sprintf('Current response status code is %d, but %d expected.', $actual, $code);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that current response code not equals to provided one.
     *
     * @param integer $code
     *
     * @throws ExpectationException
     */
    public function statusCodeNotEquals($code)
    {
        $actual = $this->session->getStatusCode();

        if (intval($code) === intval($actual)) {
            $message = sprintf('Current response status code is %d, but should not be.', $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that current page contains text.
     *
     * @param string $text
     *
     * @throws ResponseTextException
     */
    public function pageTextContains($text)
    {
        $actual = $this->session->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" was not found anywhere in the text of the current page.', $text);
            throw new ResponseTextException($message, $this->session);
        }
    }

    /**
     * Checks that current page does not contains text.
     *
     * @param string $text
     *
     * @throws ResponseTextException
     */
    public function pageTextNotContains($text)
    {
        $actual = $this->session->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" appears in the text of this page, but it should not.', $text);
            throw new ResponseTextException($message, $this->session);
        }
    }

    /**
     * Checks that current page text matches regex.
     *
     * @param string $regex
     *
     * @throws ResponseTextException
     */
    public function pageTextMatches($regex)
    {
        $actual = $this->session->getPage()->getText();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was not found anywhere in the text of the current page.', $regex);
            throw new ResponseTextException($message, $this->session);
        }
    }

    /**
     * Checks that current page text does not matches regex.
     *
     * @param string $regex
     *
     * @throws ResponseTextException
     */
    public function pageTextNotMatches($regex)
    {
        $actual = $this->session->getPage()->getText();

        if (preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was found in the text of the current page, but it should not.', $regex);
            throw new ResponseTextException($message, $this->session);
        }
    }

    /**
     * Checks that page HTML (response content) contains text.
     *
     * @param string $text
     *
     * @throws ExpectationException
     */
    public function responseContains($text)
    {
        $actual = $this->session->getPage()->getContent();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page.', $text);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that page HTML (response content) does not contains text.
     *
     * @param string $text
     *
     * @throws ExpectationException
     */
    public function responseNotContains($text)
    {
        $actual = $this->session->getPage()->getContent();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" appears in the HTML response of this page, but it should not.', $text);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that page HTML (response content) matches regex.
     *
     * @param string $regex
     *
     * @throws ExpectationException
     */
    public function responseMatches($regex)
    {
        $actual = $this->session->getPage()->getContent();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was not found anywhere in the HTML response of the page.', $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that page HTML (response content) does not matches regex.
     *
     * @param $regex
     *
     * @throws ExpectationException
     */
    public function responseNotMatches($regex)
    {
        $actual = $this->session->getPage()->getContent();

        if (preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was found in the HTML response of the page, but it should not.', $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that there is specified number of specific elements on the page.
     *
     * @param string           $selectorType element selector type (css, xpath)
     * @param string|array     $selector     element selector
     * @param integer          $count        expected count
     * @param ElementInterface $container    document to check against
     *
     * @throws ExpectationException
     */
    public function elementsCount($selectorType, $selector, $count, ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $nodes = $container->findAll($selectorType, $selector);

        if (intval($count) !== count($nodes)) {
            $message = sprintf(
                '%d %s found on the page, but should be %d.',
                count($nodes),
                $this->getMatchingElementRepresentation($selectorType, $selector, count($nodes) !== 1),
                $count
            );
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific element exists on the current page.
     *
     * @param string           $selectorType element selector type (css, xpath)
     * @param string|array     $selector     element selector
     * @param ElementInterface $container    document to check against
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    public function elementExists($selectorType, $selector, ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->find($selectorType, $selector);

        if (null === $node) {
            if (is_array($selector)) {
                $selector = implode(' ', $selector);
            }

            throw new ElementNotFoundException($this->session, 'element', $selectorType, $selector);
        }

        return $node;
    }

    /**
     * Checks that specific element does not exists on the current page.
     *
     * @param string           $selectorType element selector type (css, xpath)
     * @param string|array     $selector     element selector
     * @param ElementInterface $container    document to check against
     *
     * @throws ExpectationException
     */
    public function elementNotExists($selectorType, $selector, ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->find($selectorType, $selector);

        if (null !== $node) {
            $message = sprintf(
                'An %s appears on this page, but it should not.',
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific element contains text.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $text         expected text
     *
     * @throws ElementTextException
     */
    public function elementTextContains($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getText();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf(
                'The text "%s" was not found in the text of the %s.',
                $text,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementTextException($message, $this->session, $element);
        }
    }

    /**
     * Checks that specific element does not contains text.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $text         expected text
     *
     * @throws ElementTextException
     */
    public function elementTextNotContains($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getText();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf(
                'The text "%s" appears in the text of the %s, but it should not.',
                $text,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementTextException($message, $this->session, $element);
        }
    }

    /**
     * Checks that specific element contains HTML.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $html         expected text
     *
     * @throws ElementHtmlException
     */
    public function elementContains($selectorType, $selector, $html)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getHtml();
        $regex   = '/'.preg_quote($html, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf(
                'The string "%s" was not found in the HTML of the %s.',
                $html,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementHtmlException($message, $this->session, $element);
        }
    }

    /**
     * Checks that specific element does not contains HTML.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $html         expected text
     *
     * @throws ElementHtmlException
     */
    public function elementNotContains($selectorType, $selector, $html)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getHtml();
        $regex   = '/'.preg_quote($html, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf(
                'The string "%s" appears in the HTML of the %s, but it should not.',
                $html,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementHtmlException($message, $this->session, $element);
        }
    }

    /**
     * Checks that an attribute exists in an element.
     *
     * @param string       $selectorType
     * @param string|array $selector
     * @param string       $attribute
     *
     * @return NodeElement
     *
     * @throws Exception\ElementAttributeNotFoundException
     */
    public function elementAttributeExists($selectorType, $selector, $attribute)
    {
        $element = $this->elementExists($selectorType, $selector);

        if (!$element->hasAttribute($attribute)) {
            $message = sprintf(
                'The attribute "%s" was not found in the %s.',
                $attribute,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementAttributeNotFoundException($message, $this->session, $element);
        }

        return $element;
    }

    /**
     * Checks that an attribute of a specific elements contains text.
     *
     * @param string       $selectorType
     * @param string|array $selector
     * @param string       $attribute
     * @param string       $text
     *
     * @throws ElementAttributeException
     */
    public function elementAttributeContains($selectorType, $selector, $attribute, $text)
    {
        $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
        $actual  = $element->getAttribute($attribute);
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf(
                'The text "%s" was not found in the attribute "%s" of the %s.',
                $text,
                $attribute,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementAttributeException($message, $this->session, $element);
        }
    }

    /**
     * Checks that an attribute of a specific elements does not contain text.
     *
     * @param string       $selectorType
     * @param string|array $selector
     * @param string       $attribute
     * @param string       $text
     *
     * @throws ElementAttributeException
     */
    public function elementAttributeNotContains($selectorType, $selector, $attribute, $text)
    {
        $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
        $actual  = $element->getAttribute($attribute);
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf(
                'The text "%s" was found in the attribute "%s" of the %s.',
                $text,
                $attribute,
                $this->getMatchingElementRepresentation($selectorType, $selector)
            );
            throw new ElementAttributeException($message, $this->session, $element);
        }
    }

    /**
     * Checks that specific field exists on the current page.
     *
     * @param string             $field     field id|name|label|value
     * @param TraversableElement $container document to check against
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    public function fieldExists($field, TraversableElement $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->findField($field);

        if (null === $node) {
            throw new ElementNotFoundException($this->session, 'form field', 'id|name|label|value', $field);
        }

        return $node;
    }

    /**
     * Checks that specific field does not exists on the current page.
     *
     * @param string             $field     field id|name|label|value
     * @param TraversableElement $container document to check against
     *
     * @throws ExpectationException
     */
    public function fieldNotExists($field, TraversableElement $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->findField($field);

        if (null !== $node) {
            $message = sprintf('A field "%s" appears on this page, but it should not.', $field);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific field have provided value.
     *
     * @param string             $field     field id|name|label|value
     * @param string             $value     field value
     * @param TraversableElement $container document to check against
     *
     * @throws ExpectationException
     */
    public function fieldValueEquals($field, $value, TraversableElement $container = null)
    {
        $node   = $this->fieldExists($field, $container);
        $actual = $node->getValue();
        $regex  = '/^'.preg_quote($value, '/').'$/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $value);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific field have provided value.
     *
     * @param string             $field     field id|name|label|value
     * @param string             $value     field value
     * @param TraversableElement $container document to check against
     *
     * @throws ExpectationException
     */
    public function fieldValueNotEquals($field, $value, TraversableElement $container = null)
    {
        $node   = $this->fieldExists($field, $container);
        $actual = $node->getValue();
        $regex  = '/^'.preg_quote($value, '/').'$/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The field "%s" value is "%s", but it should not be.', $field, $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific checkbox is checked.
     *
     * @param string             $field     field id|name|label|value
     * @param TraversableElement $container document to check against
     *
     * @throws ExpectationException
     */
    public function checkboxChecked($field, TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);

        if (!$node->isChecked()) {
            $message = sprintf('Checkbox "%s" is not checked, but it should be.', $field);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Checks that specific checkbox is unchecked.
     *
     * @param string             $field     field id|name|label|value
     * @param TraversableElement $container document to check against
     *
     * @throws ExpectationException
     */
    public function checkboxNotChecked($field, TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);

        if ($node->isChecked()) {
            $message = sprintf('Checkbox "%s" is checked, but it should not be.', $field);
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * Gets current url of the page.
     *
     * @return string
     */
    protected function getCurrentUrlPath()
    {
        return $this->cleanUrl($this->session->getCurrentUrl());
    }

    /**
     * Trims scriptname from the URL.
     *
     * @param string $url
     *
     * @return string
     */
    protected function cleanUrl($url)
    {
        $parts = parse_url($url);
        $fragment = empty($parts['fragment']) ? '' : '#' . $parts['fragment'];

        return preg_replace('/^\/[^\.\/]+\.php/', '', $parts['path']) . $fragment;
    }

    /**
     * @param string       $selectorType
     * @param string|array $selector
     * @param boolean      $plural
     *
     * @return string
     */
    private function getMatchingElementRepresentation($selectorType, $selector, $plural = false)
    {
        $pluralization = $plural ? 's' : '';

        if ('named' === $selectorType && is_array($selector) && 2 === count($selector)) {
            return sprintf('%s%s matching locator "%s"', $selector[0], $pluralization, $selector[1]);
        }

        if (is_array($selector)) {
            $selector = implode(' ', $selector);
        }

        return sprintf('element%s matching %s "%s"', $pluralization, $selectorType, $selector);
    }
}
