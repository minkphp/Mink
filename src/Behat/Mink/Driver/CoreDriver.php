<?php

/*
 * This file is part of the Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Core driver.
 * All other drivers should extend this class for future compatibility.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class CoreDriver implements DriverInterface
{
    /**
     * Reloads current page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function reload()
    {
        throw new UnsupportedDriverActionException('Page reloading is not supported by %s', $this);
    }

    /**
     * Moves browser forward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function forward()
    {
        throw new UnsupportedDriverActionException('Forward action is not supported by %s', $this);
    }

    /**
     * Moves browser backward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function back()
    {
        throw new UnsupportedDriverActionException('Backward action is not supported by %s', $this);
    }

    /**
     * Sets HTTP Basic authentication parameters.
     *
     * @param string|Boolean $user     user name or false to disable authentication
     * @param string         $password password
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Basic auth setup is not supported by %s', $this);
    }

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function switchToWindow($name = null)
    {
        throw new UnsupportedDriverActionException('Windows management is not supported by %s', $this);
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function switchToIFrame($name = null)
    {
        throw new UnsupportedDriverActionException('iFrames management is not supported by %s', $this);
    }

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * Returns last response headers.
     *
     * @return array
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers are not available from %s', $this);
    }

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function setCookie($name, $value = null)
    {
        throw new UnsupportedDriverActionException('Cookies manipulation is not supported by %s', $this);
    }

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getCookie($name)
    {
        throw new UnsupportedDriverActionException('Cookies are not available from %s', $this);
    }

    /**
     * Returns last response status code.
     *
     * @return integer
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code is not available from %s', $this);
    }

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending on driver (e.g., image/png, image/jpeg)
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getScreenshot()
    {
        throw new UnsupportedDriverActionException('Screenshots are not supported by %s', $this);
    }

    /**
     * Return the names of all open windows.
     *
     * @return array    array of all open windows
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getWindowNames()
    {
        throw new UnsupportedDriverActionException('Listing all window names is not supported by %s', $this);
    }

    /**
     * Return the name of the currently active window.
     *
     * @return string    the name of the current window
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function getWindowName()
    {
        throw new UnsupportedDriverActionException('Listing this window name is not supported by %s', $this);
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function doubleClick($xpath)
    {
        throw new UnsupportedDriverActionException('Double-clicking is not supported by %s', $this);
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function rightClick($xpath)
    {
        throw new UnsupportedDriverActionException('Right-clicking is not supported by %s', $this);
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function isVisible($xpath)
    {
        throw new UnsupportedDriverActionException('Element visibility check is not supported by %s', $this);
    }

    /**
     * Checks whether select option, located by it's XPath query, is selected.
     *
     * @param string $xpath
     *
     * @return Boolean
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function isSelected($xpath)
    {
        throw new UnsupportedDriverActionException('Element selection check is not supported by %s', $this);
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function mouseOver($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function focus($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * Executes JS script.
     *
     * @param string $script
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function executeScript($script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * Evaluates JS script.
     *
     * @param string $script
     *
     * @return mixed
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param integer $time      time in milliseconds
     * @param string  $condition JS condition
     *
     * @return boolean
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function wait($time, $condition)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * Set the dimensions of the window.
     *
     * @param integer $width  set the window width, measured in pixels
     * @param integer $height set the window height, measured in pixels
     * @param string  $name   window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function resizeWindow($width, $height, $name = null)
    {
        throw new UnsupportedDriverActionException('Window resizing is not supported by %s', $this);
    }

    /**
     * Maximize the window if it is not maximized already.
     *
     * @param string $name window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function maximizeWindow($name = null)
    {
        throw new UnsupportedDriverActionException('Window maximize is not supported by %s', $this);
    }

    /**
     * Submits the form.
     *
     * @param string $xpath Xpath.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    public function submitForm($xpath)
    {
        throw new UnsupportedDriverActionException('Form submission is not supported by %s', $this);
    }
}
