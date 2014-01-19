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
use Behat\Mink\Session;

/**
 * Driver interface.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface DriverInterface
{
    /**
     * Sets driver's current session.
     *
     * @param Session $session
     */
    public function setSession(Session $session);

    /**
     * Starts driver.
     */
    public function start();

    /**
     * Checks whether driver is started.
     *
     * @return Boolean
     */
    public function isStarted();

    /**
     * Stops driver.
     */
    public function stop();

    /**
     * Resets driver.
     */
    public function reset();

    /**
     * Visit specified URL.
     *
     * @param string $url url of the page
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function visit($url);

    /**
     * Returns current URL address.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getCurrentUrl();

    /**
     * Reloads current page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function reload();

    /**
     * Moves browser forward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function forward();

    /**
     * Moves browser backward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function back();

    /**
     * Sets HTTP Basic authentication parameters
     *
     * @param string|Boolean $user     user name or false to disable authentication
     * @param string         $password password
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setBasicAuth($user, $password);

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function switchToWindow($name = null);

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function switchToIFrame($name = null);

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setRequestHeader($name, $value);

    /**
     * Returns last response headers.
     *
     * @return array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getResponseHeaders();

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setCookie($name, $value = null);

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getCookie($name);

    /**
     * Returns last response status code.
     *
     * @return integer
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getStatusCode();

    /**
     * Returns last response content.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getContent();

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending
     *                on driver (e.g., image/png, image/jpeg)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getScreenshot();

    /**
     * Return the names of all open windows.
     *
     * @return array array of all open windows
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getWindowNames();

    /**
     * Return the name of the currently active window.
     *
     * @return string the name of the current window
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getWindowName();

    /**
     * Finds elements with specified XPath query.
     *
     * @param string $xpath
     *
     * @return NodeElement[]
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function find($xpath);

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getTagName($xpath);

    /**
     * Returns element's text by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getText($xpath);

    /**
     * Returns element's html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getHtml($xpath);

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param string $xpath
     * @param string $name
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getAttribute($xpath, $name);

    /**
     * Returns element's value by it's XPath query.
     *
     * @param string $xpath
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getValue($xpath);

    /**
     * Sets element's value by it's XPath query.
     *
     * @param string $xpath
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setValue($xpath, $value);

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function check($xpath);

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function uncheck($xpath);

    /**
     * Checks whether checkbox checked located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function isChecked($xpath);

    /**
     * Selects option from select field located by it's XPath query.
     *
     * @param string  $xpath
     * @param string  $value
     * @param Boolean $multiple
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function selectOption($xpath, $value, $multiple = false);

    /**
     * Checks whether select option, located by it's XPath query, is selected.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function isSelected($xpath);

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function click($xpath);

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function doubleClick($xpath);

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function rightClick($xpath);

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param string $xpath
     * @param string $path
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function attachFile($xpath, $path);

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function isVisible($xpath);

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function mouseOver($xpath);

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function focus($xpath);

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function blur($xpath);

    /**
     * Presses specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyPress($xpath, $char, $modifier = null);

    /**
     * Pressed down specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyDown($xpath, $char, $modifier = null);

    /**
     * Pressed up specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyUp($xpath, $char, $modifier = null);

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function dragTo($sourceXpath, $destinationXpath);

    /**
     * Executes JS script.
     *
     * @param string $script
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function executeScript($script);

    /**
     * Evaluates JS script.
     *
     * @param string $script
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function evaluateScript($script);

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param integer $time      time in milliseconds
     * @param string  $condition JS condition
     *
     * @return boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function wait($time, $condition);

    /**
     * Set the dimensions of the window.
     *
     * @param integer $width  set the window width, measured in pixels
     * @param integer $height set the window height, measured in pixels
     * @param string  $name   window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function resizeWindow($width, $height, $name = null);

    /**
     * Maximize the window if it is not maximized already
     *
     * @param string $name window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function maximizeWindow($name = null);

     /**
     * Submits the form.
     *
     * @param string $xpath Xpath.
      *
      * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function submitForm($xpath);
}
