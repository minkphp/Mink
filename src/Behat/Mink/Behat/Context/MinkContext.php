<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Exception\ElementNotFoundException;

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
        assertEquals(
            parse_url($this->locatePath($page), PHP_URL_PATH),
            parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH)
        );
    }

    /**
     * @Then /^the url should match "(?P<pattern>(?:[^"]|\\")*)"$/
     */
    public function assertUrlRegExp($pattern)
    {
        $pattern = str_replace('\\"', '"', $pattern);
        if (preg_match('/^\/.*\/$/', $pattern)) {
            assertRegExp($pattern, parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH));
        } else {
            $this->assertPageAddress($pattern);
        }
    }

    /**
     * @Then /^the response status code should be (?P<code>\d+)$/
     */
    public function assertResponseStatus($code)
    {
        assertEquals($this->getSession()->getStatusCode(), $code);
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsText($text)
    {
        $text = str_replace('\\"', '"', $text);
        assertRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getPlainText());
    }

    /**
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsText($text)
    {
        $text = str_replace('\\"', '"', $text);
        assertNotRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getPlainText());
    }

    /**
     * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseContains($text)
    {
        $text = str_replace('\\"', '"', $text);
        assertRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getContent());
    }

    /**
     * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseNotContains($text)
    {
        $text = str_replace('\\"', '"', $text);
        assertNotRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getContent());
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementContainsText($element, $text)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        $text = str_replace('\\"', '"', $text);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertContains($text, preg_replace('/\s+/', ' ', str_replace("\n", '', $node->getPlainText())));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementContains($element, $value)
    {
        $node  = $this->getSession()->getPage()->find('css', $element);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertContains($value, preg_replace('/\s+/', ' ', str_replace("\n", '', $node->getText())));
    }

    /**
     * @Then /^(?:|I )should see "(?P<element>[^"]*)" element$/
     */
    public function assertElementOnPage($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }
    }

    /**
     * @Then /^(?:|I )should not see "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotOnPage($element)
    {
        assertNull($this->getSession()->getPage()->find('css', $element));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should link to (?P<href>.*)$/
     */
    public function assertElementHref($element, $href)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        $hrefParts  = parse_url($href);
        $href       = array_merge(parse_url($this->getParameter('base_url')), $hrefParts);

        assertEquals($href['scheme'].'://'.$href['host'].$href['path'], $node->getAttribute('href'));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should have a "(?P<attribute>[a-zA-Z\-\_]*)" attribute of "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementAttributeValue($element, $attribute, $value)
    {
        $node  = $this->getSession()->getPage()->find('css', $element);
        $value = str_replace('\\"', '"', $value);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertEquals($value, $node->getAttribute($attribute));
    }

    /**
     * @Then /^print last response$/
     */
    public function printLastResponse()
    {
        $this->printDebug($this->getSession()->getPage()->getContent());
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
            throw new ElementNotFoundException('field', $field);
        }

        assertContains($value, $field->getValue());
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
            throw new ElementNotFoundException('field', $field);
        }

        assertNotContains($value, $field->getValue());
    }

    /**
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
     */
    public function assertCheckboxChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $field    = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertTrue($field->isChecked());
    }

    /**
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $checkbox = str_replace('\\"', '"', $checkbox);
        $field    = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertFalse($field->isChecked());
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
