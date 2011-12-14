<?php

namespace Behat\Mink\Behat\Context;

use Behat\Gherkin\Node\TableNode;

use Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Mink\Exception\ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException,
    Behat\Mink\Exception\ResponseTextException,
    Behat\Mink\Exception\ElementHtmlException,
    Behat\Mink\Exception\ElementTextException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

use PHPUnit_Framework_ExpectationFailedException as AssertException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Mink context for Behat BDD tool.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class BaseMinkContext extends BehatContext implements TranslatedContextInterface
{
    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    abstract public function getMink();

    /**
     * Returns all context parameters.
     *
     * @return  array
     */
    abstract public function getParameters();

    /**
     * Returns context parameter.
     *
     * @param   string  $name
     *
     * @return  mixed
     */
    abstract public function getParameter($name);

    /**
     * Returns current Mink session.
     *
     * @param   string|null name of the session OR active session will be used
     *
     * @return  Behat\Mink\Session
     */
    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * @BeforeScenario
     */
    public function prepareMinkSessions($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $session  = $this->getParameter('default_session');

        foreach ($scenario->getTags() as $tag) {
            if ('javascript' === $tag) {
                $session = $this->getParameter('javascript_session');
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        if ($scenario->hasTag('insulated')) {
            $this->getMink()->stopSessions();
        } else {
            $this->getMink()->resetSessions();
        }

        $this->getMink()->setDefaultSessionName($session);
    }

    /**
     * Locates url, based on provided path.
     *
     * @param   string  $path
     *
     * @return  string
     */
    public function locatePath($path)
    {
        $startUrl = rtrim($this->getParameter('base_url'), '/') . '/';

        return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * Opens specified page.
     *
     * @Given /^(?:|I )am on "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to "(?P<page>[^"]+)"$/
     */
    public function visit($page)
    {
        $this->getSession()->visit($this->locatePath($page));
    }

    /**
     * Reloads current page.
     *
     * @When /^(?:|I )reload the page$/
     */
    public function reload()
    {
        $this->getSession()->reload();
    }

    /**
     * Moves backward one page in history.
     *
     * @When /^(?:|I )move backward one page$/
     */
    public function back()
    {
        $this->getSession()->back();
    }

    /**
     * Moves forward one page in history
     *
     * @When /^(?:|I )move forward one page$/
     */
    public function forward()
    {
        $this->getSession()->forward();
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @When /^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/
     */
    public function pressButton($button)
    {
        $button = str_replace('\\"', '"', $button);
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/
     */
    public function clickLink($link)
    {
        $link = str_replace('\\"', '"', $link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function fillField($field, $value)
    {
        $field = str_replace('\\"', '"', $field);
        $value = str_replace('\\"', '"', $value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Fills in form fields with provided table.
     *
     * @When /^(?:|I )fill in the following:$/
     */
    public function fillFields(TableNode $fields)
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->fillField($field, $value);
        }
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @When /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function selectOption($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * Selects additional option in select field with specified id|name|label|value.
     *
     * @When /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function additionallySelectOption($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @When /^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/
     */
    public function checkOption($option)
    {
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     *
     * @When /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/
     */
    public function uncheckOption($option)
    {
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * Attaches file to field with specified id|name|label|value.
     *
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function attachFileToField($field, $path)
    {
        $field = str_replace('\\"', '"', $field);
        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    /**
     * Checks, that current page PATH is equal to specified.
     *
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     */
    public function assertPageAddress($page)
    {
        $expected = parse_url($this->locatePath($page), PHP_URL_PATH);
        $expected = preg_replace('/^\/[^\.\/]+\.php/', '', $expected);

        $actual = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
        $actual = preg_replace('/^\/[^\.\/]+\.php/', '', $actual);

        try {
            assertEquals($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('Current page is "%s", but "%s" expected', $actual, $expected);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that current page PATH matches regular expression.
     *
     * @Then /^the (?i)url(?-i) should match "(?P<pattern>(?:[^"]|\\")*)"$/
     */
    public function assertUrlRegExp($pattern)
    {
        $pattern = str_replace('\\"', '"', $pattern);
        if (!preg_match('/^\/.*\/$/', $pattern)) {
            $this->assertPageAddress($pattern);

            return;
        }

        $actual = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);

        try {
            assertRegExp($pattern, $actual);
        } catch (AssertException $e) {
            $message = sprintf('Current page "%s" does not match the pattern "%s"', $actual, $pattern);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that current page response status is equal to specified.
     *
     * @Then /^the response status code should be (?P<code>\d+)$/
     */
    public function assertResponseStatus($code)
    {
        $actual = $this->getSession()->getStatusCode();

        try {
            assertEquals($actual, $code);
        } catch (AssertException $e) {
            $message = sprintf('Current response status code is %d, but %d expected', $actual, $code);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that current page response status is not equal to specified.
     *
     * @Then /^the response status code should not be (?P<code>\d+)$/
     */
    public function assertResponseStatusIsNot($code)
    {
        $actual = $this->getSession()->getStatusCode();

        try {
            assertNotEquals($actual, $code);
        } catch (AssertException $e) {
            $message = sprintf('Current response status code is %d, but should not be', $actual);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that page contains specified text.
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsText($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getText();

        try {
            assertContains($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('The text "%s" was not found anywhere in the text of the current page', $expected);
            throw new ResponseTextException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that page doesn't contains specified text.
     *
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsText($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getText();

        try {
            assertNotContains($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('The text "%s" appears in the text of this page, but it should not.', $expected);
            throw new ResponseTextException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that HTML response contains specified string.
     *
     * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseContains($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getContent();

        try {
            assertContains($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page', $expected);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that HTML response doesn't contains specified string.
     *
     * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseNotContains($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getContent();

        try {
            assertNotContains($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('The string "%s" appears in the HTML response of this page, but it should not.', $expected);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that element with specified CSS contains specified text.
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementContainsText($element, $text)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        $text = str_replace('\\"', '"', $text);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'element', 'css', $element
            );
        }

        try {
            assertContains($text, $node->getText());
        } catch (AssertException $e) {
            $message = sprintf('The text "%s" was not found in the text of the element matching css "%s"', $text, $element);
            throw new ElementTextException($message, $this->getSession(), $node, $e);
        }
    }

    /**
     * Checks, that element with specified CSS contains specified HTML.
     *
     * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementContains($element, $value)
    {
        $node  = $this->getSession()->getPage()->find('css', $element);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'element', 'css', $element
            );
        }

        try {
            assertContains($value, $node->getHtml());
        } catch (AssertException $e) {
            $message = sprintf('The string "%s" was not found in the contents of the element matching css "%s"', $value, $element);
            throw new ElementHtmlException($message, $this->getSession(), $node, $e);
        }
    }

    /**
     * Checks, that element with specified CSS exists on page.
     *
     * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element$/
     */
    public function assertElementOnPage($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'element', 'css', $element
            );
        }
    }

    /**
     * Checks, that element with specified CSS doesn't exists on page.
     *
     * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotOnPage($element)
    {
        try {
            assertNull($this->getSession()->getPage()->find('css', $element));
        } catch (AssertException $e) {
            $message = sprintf('An element matching css "%s" appears on this page, but it should not.', $element);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that form field with specified id|name|label|value has specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldContains($field, $value)
    {
        $field = str_replace('\\"', '"', $field);
        $node  = $this->getSession()->getPage()->findField($field);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertEquals($value, $node->getValue());
        } catch (AssertException $e) {
            $message = sprintf('Form field with id|name|label|value "%s" has "%s" value, but should have "%s"', $field, $node->getValue(), $value);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that form field with specified id|name|label|value doesn't have specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldNotContains($field, $value)
    {
        $field = str_replace('\\"', '"', $field);
        $node  = $this->getSession()->getPage()->findField($field);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertNotEquals($value, $node->getValue());
        } catch (AssertException $e) {
            $message = sprintf('Form field with id|name|label|value "%s" has "%s" value, but it should not have that value', $field, $node->getValue());
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is checked.
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
     */
    public function assertCheckboxChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $node     = $this->getSession()->getPage()->findField($checkbox);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $checkbox
            );
        }

        try {
            assertTrue($node->isChecked());
        } catch (AssertException $e) {
            $message = sprintf('Checkbox with id|name|label|value "%s" is not checked, but it should be', $checkbox);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $node     = $this->getSession()->getPage()->findField($checkbox);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $checkbox
            );
        }

        try {
            assertFalse($node->isChecked());
        } catch (AssertException $e) {
            $message = sprintf('Checkbox with id|name|label|value "%s" is checked, but it should not be', $checkbox);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that (?P<num>\d+) CSS elements exist on the page
     *
     * @Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
     */
    public function assertNumElements($num, $element)
    {
        $nodes = $this->getSession()->getPage()->findAll('css', $element);

        if (null === $nodes) {
            throw new ElementNotFoundException($this->getSession(), 'element: '.$element.' ');
        }

        assertSame((int) $num, count($nodes));
    }

    /**
     * Prints last response to console.
     *
     * @Then /^print last response$/
     */
    public function printLastResponse()
    {
        $this->printDebug(
            $this->getSession()->getCurrentUrl()."\n\n".
            $this->getSession()->getPage()->getContent()
        );
    }

    /**
     * Opens last response content in browser.
     *
     * @Then /^show last response$/
     */
    public function showLastResponse()
    {
        if (null === $this->getParameter('show_cmd')) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: firefox %s")');
        }

        $filename = rtrim($this->getParameter('show_tmp_dir'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->getSession()->getPage()->getContent());
        system(sprintf($this->getParameter('show_cmd'), escapeshellarg($filename)));
    }

    /**
     * Returns list of definition translation resources paths.
     *
     * @return  array
     */
    public function getTranslationResources()
    {
        return array(
            __DIR__ . '/translations/ru.xliff',
            __DIR__ . '/translations/fr.xliff',
            __DIR__ . '/translations/ja.xliff',
            __DIR__ . '/translations/es.xliff',
            __DIR__ . '/translations/nl.xliff',
            __DIR__ . '/translations/pt.xliff',
            __DIR__ . '/translations/sv.xliff',
            __DIR__ . '/translations/de.xliff',
        );
    }
}
