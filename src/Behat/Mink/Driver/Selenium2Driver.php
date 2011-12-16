<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException;

use Selenium\Client as SeleniumClient,
    Selenium\Locator as SeleniumLocator,
    Selenium\Exception as SeleniumException,
    \WebDriver as WebDriver;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Selenium2 driver.
 *
 * @author Pete Otaqui <pete@otaqui.com>
 */
class Selenium2Driver implements DriverInterface
{

    /**
     * The current Mink session
     * @var Behat\Mink\Session
     */
    private $session;

    /**
     * Whether the browser has been started
     * @var Boolean
     */
    private $started = false;

    /**
     * The WebDriver instance
     * @var WebDriver
     */ 
    private $webDriver;

    /**
     * Instantiates the driver.
     *
     * @param string    $browser Browser name
     * @param array     $desiredCapabilities The desired capabilities
     * @param string    $wdHost The WebDriver host
     */
    public function __construct($browserName = 'firefox', $desiredCapabilities = NULL, $wdHost = 'http://localhost:4444/wd/hub')
    {
        $this->setBrowserName($browserName);
        $this->setDesiredCapabilities($desiredCapabilities);
        $this->setWebDriver( new WebDriver($wdHost) );
    }

    public function setBrowserName($browserName = 'firefox') {
        $this->browserName = $browserName;
    }

    public function setDesiredCapabilities($desiredCapabilities = NULL) {
        if ( $desiredCapabilities === NULL ) {
            $desiredCapabilities = self::getDefaultCapabilities();
        }
        $this->desiredCapabilities = $desiredCapabilities;
    }

    public function setWebDriver($webDriver) {
        $this->webDriver = $webDriver;
    }


    protected static function getDefaultCapabilities() {
        return array();
    }


    /**
     * @see Behat\Mink\Driver\DriverInterface::setSession()
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Starts driver.
     */
    public function start()
    {
        $this->started = true;
        $this->wdSession = $this->webDriver->session($this->browserName, $this->desiredCapabilities);
    }

    /**
     * Checks whether driver is started.
     *
     * @return  Boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Stops driver.
     */
    public function stop()
    {
        $this->started = false;
        $this->wdSession->close();
    }

    /**
     * Resets driver.
     */
    public function reset()
    {
        $this->stop();
        $this->start();
    }

    /**
     * Visit specified URL.
     *
     * @param   string  $url    url of the page
     */
    public function visit($url)
    {
        $this->wdSession->open($url);
    }

    /**
     * Returns current URL address.
     *
     * @return  string
     */
    public function getCurrentUrl()
    {
        return $this->wdSession->url();
    }

    /**
     * Reloads current page.
     */
    function reload()
    {
        $this->wdSession->refresh();
    }

    /**
     * Moves browser forward 1 page.
     */
    function forward()
    {
        $this->wdSession->forward();
    }

    /**
     * Moves browser backward 1 page.
     */
    function back()
    {
        $this->wdSession->back();
    }

    /**
     * Sets HTTP Basic authentication parameters
     *
     * @param   string|false    $user       user name or false to disable authentication
     * @param   string          $password   password
     */
    function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Sets specific request header on client.
     *
     * @param   string  $name
     * @param   string  $value
     */
    function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Returns last response headers.
     *
     * @return  array
     */
    function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Sets cookie.
     *
     * @param   string  $name
     * @param   string  $value
     */
    function setCookie($name, $value = null)
    {
        $cookieArray = array(
            'name' => $name,
            'value' => $value,
        );
        $this->wdSession->setCookie(json_encode($cookieArray));
    }

    /**
     * Returns cookie by name.
     *
     * @param   string  $name
     *
     * @return  string|null
     */
    function getCookie($name)
    {
        $cookies = json_decode($this->wdSession->getAllCookies());
        return $cookies[$name];
    }

    /**
     * Returns last response status code.
     *
     * @return  integer
     */
    function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Returns last response content.
     *
     * @return  string
     */
    function getContent()
    {
        return $this->wdSession->source();
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @param   string  $xpath
     *
     * @return  array           array of Behat\Mink\Element\NodeElement
     */
    function find($xpath)
    {
        $elements = array();
        $nodes = $this->wdSession->elements('xpath', $xpath);
        foreach ( $nodes as $i => $node ) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i+1), $this->session);
        }
        return $elements;
    }

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getTagName($xpath)
    {
        $node = $this->wdSession->element('xpath', $xpath);
        return $node->name();
    }

    /**
     * Returns element's text by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getText($xpath)
    {
        $node = $this->wdSession->element('xpath', $xpath);
        return $node->text();
    }

    /**
     * Returns element's html by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getHtml($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    public function getAttribute($xpath, $attr)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Returns element's value by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    function getValue($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Sets element's value by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     */
    function setValue($xpath, $value)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function check($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function uncheck($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Checks whether checkbox checked located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isChecked($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Selects option from select field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     * @param   Boolean $multiple
     */
    function selectOption($xpath, $value, $multiple = false)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function click($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function doubleClick($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function rightClick($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $path
     */
    function attachFile($xpath, $path)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isVisible($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param   string  $xpath
     */
    function mouseOver($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Brings focus to element.
     *
     * @param   string  $xpath
     */
    function focus($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Removes focus from element.
     *
     * @param   string  $xpath
     */
    function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Presses specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyPress($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyDown($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyUp($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Drag one element onto another.
     *
     * @param   string  $sourceXpath
     * @param   string  $destinationXpath
     */
    function dragTo($sourceXpath, $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Executes JS script.
     *
     * @param   string  $script
     */
    function executeScript($script)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Evaluates JS script.
     *
     * @param   string  $script
     *
     * @return  mixed           script return value
     */
    function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param   integer $time       time in milliseconds
     * @param   string  $condition  JS condition
     */
    function wait($time, $condition)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }
}
