<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Driver;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\KeyModifier;
use Behat\Mink\Session;

/**
 * Core driver.
 * All other drivers should extend this class for future compatibility.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class CoreDriver implements DriverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @return void
     *
     * @final since 1.11
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return void
     */
    public function start()
    {
        throw new UnsupportedDriverActionException('Starting the driver is not supported by %s', $this);
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        throw new UnsupportedDriverActionException('Checking the driver state is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function stop()
    {
        throw new UnsupportedDriverActionException('Stopping the driver is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function reset()
    {
        throw new UnsupportedDriverActionException('Resetting the driver is not supported by %s', $this);
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public function visit($url)
    {
        throw new UnsupportedDriverActionException('Visiting an url is not supported by %s', $this);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        throw new UnsupportedDriverActionException('Getting the current url is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function reload()
    {
        throw new UnsupportedDriverActionException('Page reloading is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function forward()
    {
        throw new UnsupportedDriverActionException('Forward action is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function back()
    {
        throw new UnsupportedDriverActionException('Backward action is not supported by %s', $this);
    }

    /**
     * @param string|false $user
     * @param string       $password
     *
     * @return void
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Basic auth setup is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function switchToWindow($name = null)
    {
        throw new UnsupportedDriverActionException('Windows management is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function switchToIFrame($name = null)
    {
        throw new UnsupportedDriverActionException('iFrames management is not supported by %s', $this);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers are not available from %s', $this);
    }

    /**
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function setCookie($name, $value = null)
    {
        throw new UnsupportedDriverActionException('Cookies manipulation is not supported by %s', $this);
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getCookie($name)
    {
        throw new UnsupportedDriverActionException('Cookies are not available from %s', $this);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code is not available from %s', $this);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        throw new UnsupportedDriverActionException('Getting the page content is not supported by %s', $this);
    }

    /**
     * @return string
     */
    public function getScreenshot()
    {
        throw new UnsupportedDriverActionException('Screenshots are not supported by %s', $this);
    }

    /**
     * @return string[]
     */
    public function getWindowNames()
    {
        throw new UnsupportedDriverActionException('Listing all window names is not supported by %s', $this);
    }

    /**
     * @return string
     */
    public function getWindowName()
    {
        throw new UnsupportedDriverActionException('Listing this window name is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return NodeElement[]
     *
     * @final since 1.11
     */
    public function find($xpath)
    {
        $elements = array();

        foreach ($this->findElementXpaths($xpath) as $xpath) {
            $elements[] = new NodeElement($xpath, $this->session);
        }

        return $elements;
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @see find()
     *
     * @param string $xpath
     *
     * @return string[] The XPath of the matched elements
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    protected function findElementXpaths($xpath)
    {
        throw new UnsupportedDriverActionException('Finding elements is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     */
    public function getTagName($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the tag name is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     */
    public function getText($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element text is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     */
    public function getHtml($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element inner HTML is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     */
    public function getOuterHtml($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element outer HTML is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute($xpath, $name)
    {
        throw new UnsupportedDriverActionException('Getting the element attribute is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string|bool|array|null
     */
    public function getValue($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the field value is not supported by %s', $this);
    }

    /**
     * @param string            $xpath
     * @param string|bool|array $value
     *
     * @return void
     */
    public function setValue($xpath, $value)
    {
        throw new UnsupportedDriverActionException('Setting the field value is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function check($xpath)
    {
        throw new UnsupportedDriverActionException('Checking a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function uncheck($xpath)
    {
        throw new UnsupportedDriverActionException('Unchecking a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     */
    public function isChecked($xpath)
    {
        throw new UnsupportedDriverActionException('Getting the state of a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $value
     * @param bool   $multiple
     *
     * @return void
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        throw new UnsupportedDriverActionException('Selecting an option is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     */
    public function isSelected($xpath)
    {
        throw new UnsupportedDriverActionException('Element selection check is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function click($xpath)
    {
        throw new UnsupportedDriverActionException('Clicking on an element is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function doubleClick($xpath)
    {
        throw new UnsupportedDriverActionException('Double-clicking is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function rightClick($xpath)
    {
        throw new UnsupportedDriverActionException('Right-clicking is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $path
     *
     * @return void
     */
    public function attachFile($xpath, $path)
    {
        throw new UnsupportedDriverActionException('Attaching a file in an input is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     */
    public function isVisible($xpath)
    {
        throw new UnsupportedDriverActionException('Element visibility check is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function mouseOver($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function focus($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string      $xpath
     * @param string|int  $char
     * @param string|null $modifier
     *
     * @phpstan-param KeyModifier::*|null $modifier
     *
     * @return void
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string      $xpath
     * @param string|int  $char
     * @param string|null $modifier
     *
     * @phpstan-param KeyModifier::*|null $modifier
     *
     * @return void
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string      $xpath
     * @param string|int  $char
     * @param string|null $modifier
     *
     * @phpstan-param KeyModifier::*|null $modifier
     *
     * @return void
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @return void
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $script
     *
     * @return void
     */
    public function executeScript($script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param string $script
     *
     * @return mixed
     */
    public function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param int    $timeout
     * @param string $condition
     *
     * @return bool
     */
    public function wait($timeout, $condition)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param int         $width
     * @param int         $height
     * @param string|null $name
     *
     * @return void
     */
    public function resizeWindow($width, $height, $name = null)
    {
        throw new UnsupportedDriverActionException('Window resizing is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function maximizeWindow($name = null)
    {
        throw new UnsupportedDriverActionException('Window maximize is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     */
    public function submitForm($xpath)
    {
        throw new UnsupportedDriverActionException('Form submission is not supported by %s', $this);
    }
}
