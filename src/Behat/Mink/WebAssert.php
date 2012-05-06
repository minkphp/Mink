<?php

namespace Behat\Mink;

use Behat\Mink\Exception\ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException,
    Behat\Mink\Exception\ResponseTextException,
    Behat\Mink\Exception\ElementHtmlException,
    Behat\Mink\Exception\ElementTextException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink web assertions tool.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class WebAssert
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function addressEquals($page)
    {
        $expected = $this->cleanScripnameFromPath(parse_url($page, PHP_URL_PATH));
        $actual   = $this->getCurrentUrlPath();

        if ($actual !== $expected) {
            $message = sprintf('Current page is "%s", but "%s" expected.', $actual, $expected);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function addressNotEquals($page)
    {
        $expected = $this->cleanScripnameFromPath(parse_url($page, PHP_URL_PATH));
        $actual   = $this->getCurrentUrlPath();

        if ($actual === $expected) {
            $message = sprintf('Current page is "%s", but should not be.', $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function addressMatches($regex)
    {
        $actual = $this->getCurrentUrlPath();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('Current page "%s" does not match the regex "%s".', $actual, $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function statusCodeEquals($code)
    {
        $actual = $this->session->getStatusCode();

        if (intval($code) !== intval($actual)) {
            $message = sprintf('Current response status code is %d, but %d expected.', $actual, $code);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function statusCodeNotEquals($code)
    {
        $actual = $this->session->getStatusCode();

        if (intval($code) === intval($actual)) {
            $message = sprintf('Current response status code is %d, but should not be.', $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function pageTextContains($text)
    {
        $actual = $this->session->getPage()->getText();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" was not found anywhere in the text of the current page.', $text);
            throw new ResponseTextException($message, $this->session);
        }
    }

    public function pageTextNotContains($text)
    {
        $actual = $this->session->getPage()->getText();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" appears in the text of this page, but it should not.', $text);
            throw new ResponseTextException($message, $this->session);
        }
    }

    public function pageTextMatches($regex)
    {
        $actual = $this->session->getPage()->getText();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was not found anywhere in the text of the current page.', $regex);
            throw new ResponseTextException($message, $this->session);
        }
    }

    public function pageTextNotMatches($regex)
    {
        $actual = $this->session->getPage()->getText();

        if (preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was found in the text of the current page, but it should not.', $regex);
            throw new ResponseTextException($message, $this->session);
        }
    }

    public function responseContains($text)
    {
        $actual = $this->session->getPage()->getContent();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page.', $text);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function responseNotContains($text)
    {
        $actual = $this->session->getPage()->getContent();
        $regex  = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" appears in the HTML response of this page, but it should not.', $text);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function responseMatches($regex)
    {
        $actual = $this->session->getPage()->getContent();

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was not found anywhere in the HTML response of the page.', $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function responseNotMatches($regex)
    {
        $actual = $this->session->getPage()->getContent();

        if (preg_match($regex, $actual)) {
            $message = sprintf('The pattern %s was found in the HTML response of the page, but it should not.', $regex);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function elementsCount($selectorType, $selector, $count)
    {
        $nodes = $this->session->getPage()->findAll($selectorType, $selector);

        if (intval($count) === count($nodes)) {
            $message = sprintf('%d elements matching %s "%s" found on the page, but should be %d.', count($nodes), $selectorType, $selector, $count);
            throw new ExpectationException($message, $this->session);
        }

        return $node;
    }

    public function elementExists($selectorType, $selector)
    {
        $node = $this->session->getPage()->find($selectorType, $selector);

        if (null === $node) {
            throw new ElementNotFoundException($this->session, 'element', $selectorType, $selector);
        }

        return $node;
    }

    public function elementNotExists($selectorType, $selector)
    {
        $node = $this->session->getPage()->find($selectorType, $selector);

        if (null !== $node) {
            $message = sprintf('An element matching %s "%s" appears on this page, but it should not.', $selectorType, $selector);
            throw new ExpectationException($message, $this->session);
        }

        return $node;
    }

    public function elementContainsText($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getText();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" was not found in the text of the element matching %s "%s".', $text, $selectorType, $selector);
            throw new ElementTextException($message, $this->session, $node);
        }
    }

    public function elementNotContainsText($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getText();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" appears in the text of the element matching %s "%s", but it should not.', $text, $selectorType, $selector);
            throw new ElementTextException($message, $this->session, $node);
        }
    }

    public function elementContains($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getContent();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" was not found in the HTML of the element matching %s "%s".', $text, $selectorType, $selector);
            throw new ElementTextException($message, $this->session, $node);
        }
    }

    public function elementNotContains($selectorType, $selector, $text)
    {
        $element = $this->elementExists($selectorType, $selector);
        $actual  = $element->getContent();
        $regex   = '/'.preg_quote($text, '/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The string "%s" appears in the HTML of the element matching %s "%s", but it should not.', $text, $selectorType, $selector);
            throw new ElementTextException($message, $this->session, $node);
        }
    }

    public function fieldExists($field)
    {
        $node = $this->session->getPage()->findField($field);

        if (null === $node) {
            throw new ElementNotFoundException($this->session, 'form field', 'id|name|label|value', $field);
        }

        return $node;
    }

    public function fieldNotExists($field)
    {
        $node = $this->session->getPage()->findField($field);

        if (null !== $node) {
            $message = sprintf('A field "%s" appears on this page, but it should not.', $field);
            throw new ExpectationException($message, $this->session);
        }

        return $node;
    }

    public function fieldValueEquals($field, $text)
    {
        $node   = $this->fieldExists($field);
        $actual = $node->getValue();
        $regex  = '/^'.preg_quote($text, '$/').'/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $text);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function fieldValueNotEquals($field, $text)
    {
        $node   = $this->fieldExists($field);
        $actual = $node->getValue();
        $regex  = '/^'.preg_quote($text, '$/').'/ui';

        if (preg_match($regex, $actual)) {
            $message = sprintf('The field "%s" value is "%s", but should not be.', $field, $actual);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function checkboxChecked($field)
    {
        $node = $this->fieldExists($field);

        if (!$node->isChecked()) {
            $message = sprintf('Checkbox "%s" is not checked, but it should be', $field);
            throw new ExpectationException($message, $this->session);
        }
    }

    public function checkboxNotChecked($field, $text)
    {
        $node = $this->fieldExists($field);

        if ($node->isChecked()) {
            $message = sprintf('Checkbox "%s" is checked, but it should not be', $field);
            throw new ExpectationException($message, $this->session);
        }
    }

    protected function getCurrentUrlPath()
    {
        return $this->cleanScripnameFromPath(
            parse_url($this->session->getCurrentUrl(), PHP_URL_PATH)
        );
    }

    protected function cleanScripnameFromPath($path)
    {
        return preg_replace('/^\/[^\.\/]+\.php/', '', $path);
    }
}
