<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Exception\ElementNotFoundException,
    Behat\Mink\Exception\ExpectationFailedException,
    Behat\Mink\Exception\PlainTextResponseException,
    Behat\Mink\Exception\ElementContentException,
    Behat\Mink\Exception\ElementTextException;

use Goutte\Client as GoutteClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink context for Behat testing tool.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkContext extends BehatContext implements TranslatedContextInterface
{
    private static $minkInstance;
    private $parameters;

    /**
     * Initializes Mink environment.
     *
     * @param   array   $parameters     list of context parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = array_merge(array(
            'default_session' => 'goutte',
            'base_url'        => 'http://localhost',
            'show_cmd'        => null,
            'browser'         => 'firefox',
            'goutte' => array(
                'zend_config'       => array(),
                'server_parameters' => array()
            ),
            'sahi' => array(
                'sid'  => uniqid(),
                'host' => 'localhost',
                'port' => 9999
            )
        ), $parameters);

        if (null === self::$minkInstance) {
            self::$minkInstance = new Mink();
            $this->registerSessions(self::$minkInstance);
        }
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

        return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return self::$minkInstance;
    }

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
     * Returns all context parameters.
     *
     * @return  array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns context parameter.
     *
     * @param   string  $name
     *
     * @return  mixed
     */
    public function getParameter($name)
    {
        if (!isset($this->parameters[$name])) {
            return;
        }

        return $this->parameters[$name];
    }

    /**
     * @Given /^(?:|I )am on "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to "(?P<page>[^"]+)"$/
     */
    public function visit($page)
    {
        $this->getSession()->visit($this->locatePath($page));
    }

    /**
     * @When /^(?:|I )reload the page$/
     */
    public function reload()
    {
        $this->getSession()->reload();
    }

    /**
     * @When /^(?:|I )move backward one page$/
     */
    public function back()
    {
        $this->getSession()->back();
    }

    /**
     * @When /^(?:|I )move forward one page$/
     */
    public function forward()
    {
        $this->getSession()->forward();
    }

    /**
     * @When /^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/
     */
    public function pressButton($button)
    {
        $button = str_replace('\\"', '"', $button);
        $this->getSession()->getPage()->clickButton($button);
    }

    /**
     * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/
     */
    public function clickLink($link)
    {
        $link = str_replace('\\"', '"', $link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
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
     * @When /^(?:|I )fill in the following:$/
     */
    public function fillFields(TableNode $fields)
    {
        foreach ($fieldsTable->getRowsHash() as $field => $value) {
            $this->fillField($field, $value);
        }
    }

    /**
     * @When /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function selectOption($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @When /^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/
     */
    public function checkOption($option)
    {
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * @When /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/
     */
    public function uncheckOption($option)
    {
        $option = str_replace('\\"', '"', $option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function attachFileToField($field, $path)
    {
        $field = str_replace('\\"', '"', $field);
        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    /**
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     */
    public function assertPageAddress($page)
    {
        $expected = parse_url($this->locatePath($page), PHP_URL_PATH);
        $actual   = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);

        try {
            assertEquals($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Current page is "%s", but "%s" expected', $actual, $expected);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the url should match "(?P<pattern>(?:[^"]|\\")*)"$/
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
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Current page "%s" does not match the pattern "%s"', $actual, $pattern);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the response status code should be (?P<code>\d+)$/
     */
    public function assertResponseStatus($code)
    {
        $actual = $this->getSession()->getStatusCode();
        try {
            assertEquals($actual, $code);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Current response status code is %d, but %d expected', $actual, $code);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsText($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getPlainText();

        try {
            assertContains($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The text "%s" was not found anywhere in the text of the current page', $expected);
            throw new PlainTextResponseException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsText($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getPlainText();

        try {
            assertNotContains($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The text "%s" appears in the text of this page, but it should not.', $expected);
            throw new PlainTextResponseException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseContains($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getContent();

        try {
            assertContains($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page', $expected);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseNotContains($text)
    {
        $expected = str_replace('\\"', '"', $text);
        $actual   = $this->getSession()->getPage()->getContent();

        try {
            assertNotContains($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The string "%s" appears in the HTML response of this page, but it should not.', $expected);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
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
            assertContains($text, $node->getPlainText());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The text "%s" was not found in the text of the element matching css "%s"', $text, $element);
            throw new ElementTextException($message, $this->getSession(), $node, $e);
        }
    }

    /**
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
            assertContains($value, $node->getText());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('The string "%s" was not found in the contents of the element matching css "%s"', $value, $element);
            throw new ElementContentException($message, $this->getSession(), $node, $e);
        }
    }

    /**
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
     * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotOnPage($element)
    {
        try {
            assertNull($this->getSession()->getPage()->find('css', $element));
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('An element matching css "%s" appears on this page, but it should not.', $element);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should link to (?P<href>.*)$/
     */
    public function assertElementHref($element, $href)
    {
        $value = $href['scheme'].'://'.$href['host'].$href['path'];
        $this->assertElementAttributeValue($element, 'href', $value);
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should have a "(?P<attribute>[a-zA-Z\-\_]*)" attribute of "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementAttributeValue($element, $attribute, $value)
    {
        $node  = $this->getSession()->getPage()->find('css', $element);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'element', 'css', $element
            );
        }

        $expected = $value;
        $actual   = $node->getAttribute($attribute);

        try {
            assertEquals($expected, $actual);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Element matching css "%s" has "%s=%s", but "%s=%s" was expected', $element, $attribute, $actual, $attribute, $expected);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldContains($field, $value)
    {
        $field = str_replace('\\"', '"', $field);
        $field = $this->getSession()->getPage()->findField($field);
        $value = str_replace('\\"', '"', $value);

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertEquals($value, $field->getValue());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Form field with id|name|label|value "%s" has "%s" value, but should have "%s"', $element, $field->getValue(), $value);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldNotContains($field, $value)
    {
        $field = str_replace('\\"', '"', $field);
        $field = $this->getSession()->getPage()->findField($field);
        $value = str_replace('\\"', '"', $value);

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertNotEquals($value, $field->getValue());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Form field with id|name|label|value "%s" has "%s" value, but it should not have that value', $element, $field->getValue());
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
     */
    public function assertCheckboxChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $field    = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertTrue($field->isChecked());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Checkbox with id|name|label|value "%s" is not checked, but it should be', $element);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $field    = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        try {
            assertFalse($field->isChecked());
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $message = sprintf('Checkbox with id|name|label|value "%s" is checked, but it should not be', $element);
            throw new ExpectationFailedException($message, $this->getSession(), $e);
        }
    }

    /**
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
     * @Then /^show last response$/
     */
    public function showLastResponse()
    {
        if (null === $this->getParameter('show_cmd')) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: firefox %s")');
        }

        $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->getSession()->getPage()->getContent());
        system(sprintf($this->getParameter('show_cmd'), $filename));
    }

    /**
     * @BeforeScenario
     */
    public function prepareMinkSession($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $session  = $this->getParameter('default_session');

        foreach ($scenario->getTags() as $tag) {
            if ('javascript' === $tag) {
                $session = 'sahi';
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        $this->getMink()->setDefaultSessionName($session);
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
        );
    }

    /**
     * Registers Mink sessions on it's initialization.
     *
     * @param   Behat\Mink\Mink     $mink   Mink manager instance
     */
    protected function registerSessions(Mink $mink)
    {
        $mink->registerSession('goutte', new Session(
            $this->initGoutteDriver($this->getParameter('goutte'))
        ));
        $mink->registerSession('sahi',   new Session(
            $this->initSahiDriver($this->getParameter('browser'), $this->getParameter('sahi'))
        ));
    }

    /**
     * Initizalizes and returns new GoutteDriver instance.
     *
     * @param   array   $connection     connection settings
     *
     * @return  Behat\Mink\Driver\GoutteDriver
     */
    protected function initGoutteDriver(array $connection)
    {
        return new GoutteDriver(
            new GoutteClient($connection['zend_config'], $connection['server_parameters'])
        );
    }

    /**
     * Initizalizes and returns new SahiDriver instance.
     *
     * @param   string  $browser        browser name to use (default = firefox)
     * @param   array   $connection     connection settings
     *
     * @return  Behat\Mink\Driver\SahiDriver
     */
    protected function initSahiDriver($browser, array $connection)
    {
        return new SahiDriver(
            $browser,
            new SahiClient(new SahiConnection($connection['sid'], $connection['host'], $connection['port']))
        );
    }
}
