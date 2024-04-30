<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink;

use Behat\Mink\Element\Element;
use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementHtmlException;
use Behat\Mink\Exception\ElementTextException;

/**
 * Mink web assertions tool.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class WebAssert
{
    /**
     * @var Session
     */
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
     * @return void
     *
     * @throws ExpectationException
     */
    public function addressEquals(string $page)
    {
        $expected = $this->cleanUrl($page);
        $actual = $this->getCurrentUrlPath();

        $this->assert($actual === $expected, sprintf('Current page is "%s", but "%s" expected.', $actual, $expected));
    }

    /**
     * Checks that current session address is not equals to provided one.
     *
     * @param string $page
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function addressNotEquals(string $page)
    {
        $expected = $this->cleanUrl($page);
        $actual = $this->getCurrentUrlPath();

        $this->assert($actual !== $expected, sprintf('Current page is "%s", but should not be.', $actual));
    }

    /**
     * Checks that current session address matches regex.
     *
     * @param string $regex
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function addressMatches(string $regex)
    {
        $actual = $this->getCurrentUrlPath();
        $message = sprintf('Current page "%s" does not match the regex "%s".', $actual, $regex);

        $this->assert((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that specified cookie exists and its value equals to a given one.
     *
     * @param string $name  cookie name
     * @param string $value cookie value
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function cookieEquals(string $name, string $value)
    {
        $this->cookieExists($name);

        $actualValue = $this->session->getCookie($name);
        $message = sprintf('Cookie "%s" value is "%s", but should be "%s".', $name, $actualValue, $value);

        $this->assert($actualValue == $value, $message);
    }

    /**
     * Checks that specified cookie exists.
     *
     * @param string $name cookie name
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function cookieExists(string $name)
    {
        $message = sprintf('Cookie "%s" is not set, but should be.', $name);
        $this->assert($this->session->getCookie($name) !== null, $message);
    }

    /**
     * Checks that current response code equals to provided one.
     *
     * @param int $code
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function statusCodeEquals(int $code)
    {
        $actual = $this->session->getStatusCode();
        $message = sprintf('Current response status code is %d, but %d expected.', $actual, $code);

        $this->assert(intval($code) === intval($actual), $message);
    }

    /**
     * Checks that current response code not equals to provided one.
     *
     * @param int $code
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function statusCodeNotEquals(int $code)
    {
        $actual = $this->session->getStatusCode();
        $message = sprintf('Current response status code is %d, but should not be.', $actual);

        $this->assert(intval($code) !== intval($actual), $message);
    }

    /**
     * Checks that current response header equals value.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderEquals(string $name, string $value)
    {
        $actual = $this->session->getResponseHeader($name);
        $message = sprintf('Current response header "%s" is "%s", but "%s" expected.', $name, $actual, $value);

        $this->assert($value === $actual, $message);
    }

    /**
     * Checks that current response header does not equal value.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderNotEquals(string $name, string $value)
    {
        $actual = $this->session->getResponseHeader($name);
        $message = sprintf('Current response header "%s" is "%s", but should not be.', $name, $actual);

        $this->assert($value !== $actual, $message);
    }

    /**
     * Checks that current response header contains value.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderContains(string $name, string $value)
    {
        $actual = (string) $this->session->getResponseHeader($name);
        $message = sprintf('The text "%s" was not found anywhere in the "%s" response header.', $value, $name);

        $this->assert(false !== stripos($actual, $value), $message);
    }

    /**
     * Checks that current response header does not contain value.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderNotContains(string $name, string $value)
    {
        $actual = (string) $this->session->getResponseHeader($name);
        $message = sprintf('The text "%s" was found in the "%s" response header, but it should not.', $value, $name);

        $this->assert(false === stripos($actual, $value), $message);
    }

    /**
     * Checks that current response header matches regex.
     *
     * @param string $name
     * @param string $regex
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderMatches(string $name, string $regex)
    {
        $actual = (string) $this->session->getResponseHeader($name);
        $message = sprintf('The pattern "%s" was not found anywhere in the "%s" response header.', $regex, $name);

        $this->assert((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that current response header does not match regex.
     *
     * @param string $name
     * @param string $regex
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseHeaderNotMatches(string $name, string $regex)
    {
        $actual = (string) $this->session->getResponseHeader($name);
        $message = sprintf(
            'The pattern "%s" was found in the text of the "%s" response header, but it should not.',
            $regex,
            $name
        );

        $this->assert(!preg_match($regex, $actual), $message);
    }

    /**
     * Checks that current page contains text.
     *
     * @param string $text
     *
     * @return void
     *
     * @throws ResponseTextException
     */
    public function pageTextContains(string $text)
    {
        $actual = $this->session->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual) ?? $actual;
        $regex = '/'.preg_quote($text, '/').'/ui';
        $message = sprintf('The text "%s" was not found anywhere in the text of the current page.', $text);

        $this->assertResponseText((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that current page does not contain text.
     *
     * @param string $text
     *
     * @return void
     *
     * @throws ResponseTextException
     */
    public function pageTextNotContains(string $text)
    {
        $actual = $this->session->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual) ?? $actual;
        $regex = '/'.preg_quote($text, '/').'/ui';
        $message = sprintf('The text "%s" appears in the text of this page, but it should not.', $text);

        $this->assertResponseText(!preg_match($regex, $actual), $message);
    }

    /**
     * Checks that current page text matches regex.
     *
     * @param string $regex
     *
     * @return void
     *
     * @throws ResponseTextException
     */
    public function pageTextMatches(string $regex)
    {
        $actual = $this->session->getPage()->getText();
        $message = sprintf('The pattern %s was not found anywhere in the text of the current page.', $regex);

        $this->assertResponseText((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that current page text does not match regex.
     *
     * @param string $regex
     *
     * @return void
     *
     * @throws ResponseTextException
     */
    public function pageTextNotMatches(string $regex)
    {
        $actual = $this->session->getPage()->getText();
        $message = sprintf('The pattern %s was found in the text of the current page, but it should not.', $regex);

        $this->assertResponseText(!preg_match($regex, $actual), $message);
    }

    /**
     * Checks that page HTML (response content) contains text.
     *
     * @param string $text
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseContains(string $text)
    {
        $actual = $this->session->getPage()->getContent();
        $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page.', $text);

        $this->assert(stripos($actual, (string) $text) !== false, $message);
    }

    /**
     * Checks that page HTML (response content) does not contains text.
     *
     * @param string $text
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseNotContains(string $text)
    {
        $actual = $this->session->getPage()->getContent();
        $message = sprintf('The string "%s" appears in the HTML response of this page, but it should not.', $text);

        $this->assert(stripos($actual, (string) $text) === false, $message);
    }

    /**
     * Checks that page HTML (response content) matches regex.
     *
     * @param string $regex
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseMatches(string $regex)
    {
        $actual = $this->session->getPage()->getContent();
        $message = sprintf('The pattern %s was not found anywhere in the HTML response of the page.', $regex);

        $this->assert((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that page HTML (response content) does not matches regex.
     *
     * @param string $regex
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function responseNotMatches(string $regex)
    {
        $actual = $this->session->getPage()->getContent();
        $message = sprintf('The pattern %s was found in the HTML response of the page, but it should not.', $regex);

        $this->assert(!preg_match($regex, $actual), $message);
    }

    /**
     * Checks that there is specified number of specific elements on the page.
     *
     * @param string                $selectorType element selector type (css, xpath)
     * @param string|array          $selector     element selector
     * @param int                   $count        expected count
     * @param ElementInterface|null $container    document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function elementsCount(string $selectorType, $selector, int $count, ?ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $nodes = $container->findAll($selectorType, $selector);

        $message = sprintf(
            '%d %s found on the page, but should be %d.',
            count($nodes),
            $this->getMatchingElementRepresentation($selectorType, $selector, count($nodes) !== 1),
            $count
        );

        $this->assert(intval($count) === count($nodes), $message);
    }

    /**
     * Checks that specific element exists on the current page.
     *
     * @param string                $selectorType element selector type (css, xpath)
     * @param string|array          $selector     element selector
     * @param ElementInterface|null $container    document to check against
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    public function elementExists(string $selectorType, $selector, ?ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->find($selectorType, $selector);

        if (null === $node) {
            if (is_array($selector)) {
                $selector = implode(' ', $selector);
            }

            throw new ElementNotFoundException($this->session->getDriver(), 'element', $selectorType, $selector);
        }

        return $node;
    }

    /**
     * Checks that specific element does not exist on the current page.
     *
     * @param string                $selectorType element selector type (css, xpath)
     * @param string|array          $selector     element selector
     * @param ElementInterface|null $container    document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function elementNotExists(string $selectorType, $selector, ?ElementInterface $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->find($selectorType, $selector);

        $message = sprintf(
            'An %s appears on this page, but it should not.',
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assert(null === $node, $message);
    }

    /**
     * Checks that specific element contains text.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $text         expected text
     *
     * @return void
     *
     * @throws ElementTextException
     * @throws ElementNotFoundException
     */
    public function elementTextContains(string $selectorType, $selector, string $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual = $element->getText();
        $regex = '/'.preg_quote($text, '/').'/ui';

        $message = sprintf(
            'The text "%s" was not found in the text of the %s.',
            $text,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElementText((bool) preg_match($regex, $actual), $message, $element);
    }

    /**
     * Checks that specific element does not contains text.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $text         expected text
     *
     * @return void
     *
     * @throws ElementTextException
     * @throws ElementNotFoundException
     */
    public function elementTextNotContains(string $selectorType, $selector, string $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual = $element->getText();
        $regex = '/'.preg_quote($text, '/').'/ui';

        $message = sprintf(
            'The text "%s" appears in the text of the %s, but it should not.',
            $text,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElementText(!preg_match($regex, $actual), $message, $element);
    }

    /**
     * Checks that specific element contains HTML.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $html         expected text
     *
     * @return void
     *
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementContains(string $selectorType, $selector, string $html)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual = $element->getHtml();
        $regex = '/'.preg_quote($html, '/').'/ui';

        $message = sprintf(
            'The string "%s" was not found in the HTML of the %s.',
            $html,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement((bool) preg_match($regex, $actual), $message, $element);
    }

    /**
     * Checks that specific element does not contains HTML.
     *
     * @param string       $selectorType element selector type (css, xpath)
     * @param string|array $selector     element selector
     * @param string       $html         expected text
     *
     * @return void
     *
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementNotContains(string $selectorType, $selector, string $html)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual = $element->getHtml();
        $regex = '/'.preg_quote($html, '/').'/ui';

        $message = sprintf(
            'The string "%s" appears in the HTML of the %s, but it should not.',
            $html,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement(!preg_match($regex, $actual), $message, $element);
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
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementAttributeExists(string $selectorType, $selector, string $attribute)
    {
        $element = $this->elementExists($selectorType, $selector);

        $message = sprintf(
            'The attribute "%s" was not found in the %s.',
            $attribute,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement($element->hasAttribute($attribute), $message, $element);

        return $element;
    }

    /**
     * Checks that an attribute does not exist in an element.
     *
     * @param string       $selectorType
     * @param string|array $selector
     * @param string       $attribute
     *
     * @return NodeElement
     *
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementAttributeNotExists(string $selectorType, $selector, string $attribute)
    {
        $element = $this->elementExists($selectorType, $selector);

        $message = sprintf(
            'The attribute "%s" was found in the %s.',
            $attribute,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement(!$element->hasAttribute($attribute), $message, $element);

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
     * @return void
     *
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementAttributeContains(string $selectorType, $selector, string $attribute, string $text)
    {
        $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
        $actual = (string) $element->getAttribute($attribute);
        $regex = '/'.preg_quote($text, '/').'/ui';

        $message = sprintf(
            'The text "%s" was not found in the attribute "%s" of the %s.',
            $text,
            $attribute,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement((bool) preg_match($regex, $actual), $message, $element);
    }

    /**
     * Checks that an attribute of a specific elements does not contain text.
     *
     * @param string       $selectorType
     * @param string|array $selector
     * @param string       $attribute
     * @param string       $text
     *
     * @return void
     *
     * @throws ElementHtmlException
     * @throws ElementNotFoundException
     */
    public function elementAttributeNotContains(string $selectorType, $selector, string $attribute, string $text)
    {
        $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
        $actual = (string) $element->getAttribute($attribute);
        $regex = '/'.preg_quote($text, '/').'/ui';

        $message = sprintf(
            'The text "%s" was found in the attribute "%s" of the %s.',
            $text,
            $attribute,
            $this->getMatchingElementRepresentation($selectorType, $selector)
        );

        $this->assertElement(!preg_match($regex, $actual), $message, $element);
    }

    /**
     * Checks that specific field exists on the current page.
     *
     * @param string                  $field     field id|name|label|value
     * @param TraversableElement|null $container document to check against
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    public function fieldExists(string $field, ?TraversableElement $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->findField($field);

        if (null === $node) {
            throw new ElementNotFoundException($this->session->getDriver(), 'form field', 'id|name|label|value', $field);
        }

        return $node;
    }

    /**
     * Checks that specific field does not exist on the current page.
     *
     * @param string                  $field     field id|name|label|value
     * @param TraversableElement|null $container document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function fieldNotExists(string $field, ?TraversableElement $container = null)
    {
        $container = $container ?: $this->session->getPage();
        $node = $container->findField($field);

        $this->assert(null === $node, sprintf('A field "%s" appears on this page, but it should not.', $field));
    }

    /**
     * Checks that specific field have provided value.
     *
     * @param string                  $field     field id|name|label|value
     * @param string                  $value     field value
     * @param TraversableElement|null $container document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function fieldValueEquals(string $field, string $value, ?TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);

        $actual = $node->getValue();

        if (\is_array($actual)) {
            throw new \LogicException('Field value assertions cannot be used for multi-select fields as they have multiple values.');
        }

        $actual = (string) $actual;
        $regex = '/^'.preg_quote($value, '/').'$/ui';

        $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $value);

        $this->assert((bool) preg_match($regex, $actual), $message);
    }

    /**
     * Checks that specific field have provided value.
     *
     * @param string                  $field     field id|name|label|value
     * @param string                  $value     field value
     * @param TraversableElement|null $container document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function fieldValueNotEquals(string $field, string $value, ?TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);
        $actual = $node->getValue();

        if (\is_array($actual)) {
            throw new \LogicException('Field value assertions cannot be used for multi-select fields as they have multiple values.');
        }

        $actual = (string) $actual;
        $regex = '/^'.preg_quote($value, '/').'$/ui';

        $message = sprintf('The field "%s" value is "%s", but it should not be.', $field, $actual);

        $this->assert(!preg_match($regex, $actual), $message);
    }

    /**
     * Checks that specific checkbox is checked.
     *
     * @param string                  $field     field id|name|label|value
     * @param TraversableElement|null $container document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function checkboxChecked(string $field, ?TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);

        $this->assert($node->isChecked(), sprintf('Checkbox "%s" is not checked, but it should be.', $field));
    }

    /**
     * Checks that specific checkbox is unchecked.
     *
     * @param string                  $field     field id|name|label|value
     * @param TraversableElement|null $container document to check against
     *
     * @return void
     *
     * @throws ExpectationException
     */
    public function checkboxNotChecked(string $field, ?TraversableElement $container = null)
    {
        $node = $this->fieldExists($field, $container);

        $this->assert(!$node->isChecked(), sprintf('Checkbox "%s" is checked, but it should not be.', $field));
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
    protected function cleanUrl(string $url)
    {
        $parts = parse_url($url);
        $fragment = empty($parts['fragment']) ? '' : '#'.$parts['fragment'];
        $path = empty($parts['path']) ? '/' : $parts['path'];

        return preg_replace('/^\/[^\.\/]+\.php\//', '/', $path).$fragment;
    }

    /**
     * Asserts a condition.
     *
     * @param bool   $condition
     * @param string $message   Failure message
     *
     * @return void
     *
     * @throws ExpectationException when the condition is not fulfilled
     */
    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            return;
        }

        throw new ExpectationException($message, $this->session->getDriver());
    }

    /**
     * Asserts a condition involving the response text.
     *
     * @param bool   $condition
     * @param string $message   Failure message
     *
     * @return void
     *
     * @throws ResponseTextException when the condition is not fulfilled
     */
    private function assertResponseText(bool $condition, string $message): void
    {
        if ($condition) {
            return;
        }

        throw new ResponseTextException($message, $this->session->getDriver());
    }

    /**
     * Asserts a condition on an element.
     *
     * @param bool    $condition
     * @param string  $message   Failure message
     * @param Element $element
     *
     * @return void
     *
     * @throws ElementHtmlException when the condition is not fulfilled
     */
    private function assertElement(bool $condition, string $message, Element $element): void
    {
        if ($condition) {
            return;
        }

        throw new ElementHtmlException($message, $this->session->getDriver(), $element);
    }

    /**
     * Asserts a condition involving the text of an element.
     *
     * @param bool    $condition
     * @param string  $message   Failure message
     * @param Element $element
     *
     * @return void
     *
     * @throws ElementTextException when the condition is not fulfilled
     */
    private function assertElementText(bool $condition, string $message, Element $element): void
    {
        if ($condition) {
            return;
        }

        throw new ElementTextException($message, $this->session->getDriver(), $element);
    }

    /**
     * @param string       $selectorType
     * @param string|array $selector
     * @param bool         $plural
     *
     * @return string
     */
    private function getMatchingElementRepresentation(string $selectorType, $selector, bool $plural = false): string
    {
        $pluralization = $plural ? 's' : '';

        if (in_array($selectorType, array('named', 'named_exact', 'named_partial'))
            && is_array($selector) && 2 === count($selector)
        ) {
            return sprintf('%s%s matching locator "%s"', $selector[0], $pluralization, $selector[1]);
        }

        if (is_array($selector)) {
            $selector = implode(' ', $selector);
        }

        return sprintf('element%s matching %s "%s"', $pluralization, $selectorType, $selector);
    }
}
