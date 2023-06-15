<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\ElementFinder;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Element\DocumentElement;

/**
 * Mink session.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Session
{
    /**
     * @var DriverInterface
     */
    private $driver;
    /**
     * @var DocumentElement
     */
    private $page;
    /**
     * @var ElementFinder
     */
    private $elementFinder;
    /**
     * @var SelectorsHandler
     */
    private $selectorsHandler;

    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler = null)
    {
        $this->driver = $driver;
        $this->selectorsHandler = $selectorsHandler ?? new SelectorsHandler();
        $this->elementFinder = new ElementFinder($driver, $this->selectorsHandler);
        $this->page = new DocumentElement($this);

        $driver->setSession($this);
    }

    /**
     * Checks whether session (driver) was started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->driver->isStarted();
    }

    /**
     * Starts session driver.
     *
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * @return void
     */
    public function start()
    {
        $this->driver->start();
    }

    /**
     * Stops session driver.
     *
     * @return void
     */
    public function stop()
    {
        $this->driver->stop();
    }

    /**
     * Restart session driver.
     *
     * @return void
     */
    public function restart()
    {
        $this->driver->stop();
        $this->driver->start();
    }

    /**
     * Reset session driver state.
     *
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * @return void
     */
    public function reset()
    {
        $this->driver->reset();
    }

    /**
     * Returns session driver.
     *
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns page element.
     *
     * @return DocumentElement
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @internal
     */
    public function getElementFinder(): ElementFinder
    {
        return $this->elementFinder;
    }

    /**
     * Returns selectors handler.
     *
     * @return SelectorsHandler
     *
     * @deprecated since 1.11
     */
    public function getSelectorsHandler()
    {
        @trigger_error(sprintf('The method %s is deprecated as of 1.11 and will be removed in 2.0', __METHOD__), E_USER_DEPRECATED);

        return $this->selectorsHandler;
    }

    /**
     * Visit specified URL and automatically start session if not already running.
     *
     * @param string $url url of the page
     *
     * @return void
     */
    public function visit(string $url)
    {
        // start session if needed
        if (!$this->isStarted()) {
            $this->start();
        }

        $this->driver->visit($url);
    }

    /**
     * Sets HTTP Basic authentication parameters.
     *
     * @param string|false $user     user name or false to disable authentication
     * @param string       $password password
     *
     * @return void
     */
    public function setBasicAuth($user, string $password = '')
    {
        $this->driver->setBasicAuth($user, $password);
    }

    /**
     * Sets specific request header.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setRequestHeader(string $name, string $value)
    {
        $this->driver->setRequestHeader($name, $value);
    }

    /**
     * Returns all response headers.
     *
     * @return array<string, string|string[]>
     */
    public function getResponseHeaders()
    {
        return $this->driver->getResponseHeaders();
    }

    /**
     * Returns specific response header.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getResponseHeader(string $name)
    {
        $headers = $this->driver->getResponseHeaders();

        $name = strtolower($name);
        $headers = array_change_key_case($headers, CASE_LOWER);

        if (!isset($headers[$name])) {
            return null;
        }

        return is_array($headers[$name]) ? $headers[$name][0] : $headers[$name];
    }

    /**
     * Sets cookie.
     *
     * Passing null as value will delete the cookie.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function setCookie(string $name, ?string $value = null)
    {
        $this->driver->setCookie($name, $value);
    }

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getCookie(string $name)
    {
        return $this->driver->getCookie($name);
    }

    /**
     * Returns response status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->driver->getStatusCode();
    }

    /**
     * Returns current URL address.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->driver->getCurrentUrl();
    }

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending
     *                on driver (e.g., image/png, image/jpeg)
     */
    public function getScreenshot()
    {
        return $this->driver->getScreenshot();
    }

    /**
     * Return the names of all open windows.
     *
     * @return string[] Array of all open window's names.
     */
    public function getWindowNames()
    {
        return $this->driver->getWindowNames();
    }

    /**
     * Return the name of the currently active window.
     *
     * @return string The name of the current window.
     */
    public function getWindowName()
    {
        return $this->driver->getWindowName();
    }

    /**
     * Reloads current session page.
     *
     * @return void
     */
    public function reload()
    {
        $this->driver->reload();
    }

    /**
     * Moves backward 1 page in history.
     *
     * @return void
     */
    public function back()
    {
        $this->driver->back();
    }

    /**
     * Moves forward 1 page in history.
     *
     * @return void
     */
    public function forward()
    {
        $this->driver->forward();
    }

    /**
     * Switches to specific browser window.
     *
     * @param string|null $name window name (null for switching back to main window)
     *
     * @return void
     */
    public function switchToWindow(?string $name = null)
    {
        $this->driver->switchToWindow($name);
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string|null $name iframe name (null for switching back)
     *
     * @return void
     */
    public function switchToIFrame(?string $name = null)
    {
        $this->driver->switchToIFrame($name);
    }

    /**
     * Execute JS in browser.
     *
     * @param string $script javascript
     *
     * @return void
     */
    public function executeScript(string $script)
    {
        $this->driver->executeScript($script);
    }

    /**
     * Execute JS in browser and return its response.
     *
     * @param string $script javascript
     *
     * @return mixed
     */
    public function evaluateScript(string $script)
    {
        return $this->driver->evaluateScript($script);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param int    $time      time in milliseconds
     * @param string $condition JS condition
     *
     * @return bool
     */
    public function wait(int $time, string $condition = 'false')
    {
        return $this->driver->wait($time, $condition);
    }

    /**
     * Set the dimensions of the window.
     *
     * @param int         $width  set the window width, measured in pixels
     * @param int         $height set the window height, measured in pixels
     * @param string|null $name   window name (null for the main window)
     *
     * @return void
     */
    public function resizeWindow(int $width, int $height, ?string $name = null)
    {
        $this->driver->resizeWindow($width, $height, $name);
    }

    /**
     * Maximize the window if it is not maximized already.
     *
     * @param string|null $name window name (null for the main window)
     *
     * @return void
     */
    public function maximizeWindow(?string $name = null)
    {
        $this->driver->maximizeWindow($name);
    }
}
