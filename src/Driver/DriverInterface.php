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
use Behat\Mink\Exception\DriverException;
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
     *
     * Once started, the driver should be ready to visit a page.
     *
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * Calling start on a started driver is an undefined behavior. Driver
     * implementations are free to handle it silently or to fail with an
     * exception.
     *
     * @throws DriverException When the driver cannot be started
     */
    public function start();

    /**
     * Checks whether driver is started.
     *
     * @return boolean
     */
    public function isStarted();

    /**
     * Stops driver.
     *
     * Once stopped, the driver should be started again before using it again.
     *
     * Calling any action on a stopped driver is an undefined behavior.
     * The only supported method call after stopping a driver is starting it again.
     *
     * Calling stop on a stopped driver is an undefined behavior. Driver
     * implementations are free to handle it silently or to fail with an
     * exception.
     *
     * @throws DriverException When the driver cannot be closed
     */
    public function stop();

    /**
     * Resets driver state.
     *
     * This should reset cookies, request headers and basic authentication.
     * When possible, the history should be reset as well, but this is not enforced
     * as some implementations may not be able to reset it without restarting the
     * driver entirely. Consumers requiring a clean history should restart the driver
     * to enforce it.
     *
     * Once reset, the driver should be ready to visit a page.
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * Calling reset on a stopped driver is an undefined behavior.
     */
    public function reset();

    /**
     * Visit specified URL.
     *
     * @param string $url url of the page
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function visit(string $url);

    /**
     * Returns current URL address.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCurrentUrl();

    /**
     * Reloads current page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function reload();

    /**
     * Moves browser forward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function forward();

    /**
     * Moves browser backward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function back();

    /**
     * Sets HTTP Basic authentication parameters.
     *
     * @param string|boolean $user     user name or false to disable authentication
     * @param string         $password password
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setBasicAuth($user, $password);

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToWindow(?string $name = null);

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToIFrame(?string $name = null);

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setRequestHeader(string $name, string $value);

    /**
     * Returns last response headers.
     *
     * @return array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getResponseHeaders();

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setCookie(string $name, ?string $value = null);

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCookie(string $name);

    /**
     * Returns last response status code.
     *
     * @return int
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getStatusCode();

    /**
     * Returns last response content.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getContent();

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending
     *                on driver (e.g., image/png, image/jpeg)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getScreenshot();

    /**
     * Return the names of all open windows.
     *
     * @return array array of all open windows
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowNames();

    /**
     * Return the name of the currently active window.
     *
     * @return string the name of the current window
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
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
     * @throws DriverException                  When the operation cannot be done
     */
    public function find(string $xpath);

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getTagName(string $xpath);

    /**
     * Returns element's text by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getText(string $xpath);

    /**
     * Returns element's inner html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getHtml(string $xpath);

    /**
     * Returns element's outer html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getOuterHtml(string $xpath);

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param string $xpath
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getAttribute(string $xpath, string $name);

    /**
     * Returns element's value by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string|bool|array|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::getValue
     */
    public function getValue(string $xpath);

    /**
     * Sets element's value by it's XPath query.
     *
     * @param string            $xpath
     * @param string|bool|array $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::setValue
     */
    public function setValue(string $xpath, string $value);

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::check
     */
    public function check(string $xpath);

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::uncheck
     */
    public function uncheck(string $xpath);

    /**
     * Checks whether checkbox or radio button located by it's XPath query is checked.
     *
     * @param string $xpath
     *
     * @return boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isChecked
     */
    public function isChecked(string $xpath);

    /**
     * Selects option from select field or value in radio group located by it's XPath query.
     *
     * @param string  $xpath
     * @param string  $value
     * @param boolean $multiple
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::selectOption
     */
    public function selectOption(string $xpath, string $value, bool $multiple = false);

    /**
     * Checks whether select option, located by it's XPath query, is selected.
     *
     * @param string $xpath
     *
     * @return boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isSelected
     */
    public function isSelected(string $xpath);

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function click(string $xpath);

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function doubleClick(string $xpath);

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function rightClick(string $xpath);

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param string $xpath
     * @param string $path
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::attachFile
     */
    public function attachFile(string $xpath, string $path);

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isVisible(string $xpath);

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function mouseOver(string $xpath);

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function focus(string $xpath);

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function blur(string $xpath);

    /**
     * Presses specific keyboard key.
     *
     * @param string     $xpath
     * @param string|int $char     could be either char ('b') or char-code (98)
     * @param string     $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyPress(string $xpath, string $char, ?string $modifier = null);

    /**
     * Pressed down specific keyboard key.
     *
     * @param string     $xpath
     * @param string|int $char     could be either char ('b') or char-code (98)
     * @param string     $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyDown(string $xpath, string $char, ?string $modifier = null);

    /**
     * Pressed up specific keyboard key.
     *
     * @param string     $xpath
     * @param string|int $char     could be either char ('b') or char-code (98)
     * @param string     $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyUp(string $xpath, string $char, ?string $modifier = null);

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function dragTo(string $sourceXpath, string $destinationXpath);

    /**
     * Executes JS script.
     *
     * @param string $script
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function executeScript(string $script);

    /**
     * Evaluates JS script.
     *
     * The "return" keyword is optional in the script passed as argument. Driver implementations
     * must accept the expression both with and without the keyword.
     *
     * @param string $script
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function evaluateScript(string $script);

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param int    $timeout   timeout in milliseconds
     * @param string $condition JS condition
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function wait(int $timeout, string $condition);

    /**
     * Set the dimensions of the window.
     *
     * @param int    $width  set the window width, measured in pixels
     * @param int    $height set the window height, measured in pixels
     * @param string $name   window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function resizeWindow(int $width, int $height, ?string $name = null);

    /**
     * Maximizes the window if it is not maximized already.
     *
     * @param string $name window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function maximizeWindow(?string $name = null);

    /**
     * Submits the form.
     *
     * @param string $xpath Xpath.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::submitForm
     */
    public function submitForm(string $xpath);
}

