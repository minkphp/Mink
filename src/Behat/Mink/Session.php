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
use Behat\Mink\Element\DocumentElement;

/**
 * Mink session.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Session
{
    private $driver;
    private $page;

    /**
     * @var Element\ElementFinder
     */
    private $elementFinder;

    /**
     * Initializes session.
     *
     * @param DriverInterface    $driver
     * @param ElementFinder|null $elementFinder
     */
    public function __construct(DriverInterface $driver, ElementFinder $elementFinder = null)
    {
        if (null === $elementFinder) {
            $elementFinder = new ElementFinder($driver);
        }

        $this->driver = $driver;
        $this->elementFinder = $elementFinder;
        $this->page = new DocumentElement($driver, $elementFinder);
    }

    /**
     * Checks whether session (driver) was started.
     *
     * @return Boolean
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
     */
    public function start()
    {
        $this->driver->start();
    }

    /**
     * Stops session driver.
     */
    public function stop()
    {
        $this->driver->stop();
    }

    /**
     * Restart session driver.
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
     * Visit specified URL.
     *
     * @param string $url url of the page
     */
    public function visit($url)
    {
        $this->driver->visit($url);
    }

    /**
     * Sets HTTP Basic authentication parameters
     *
     * @param string|Boolean $user     user name or false to disable authentication
     * @param string         $password password
     */
    public function setBasicAuth($user, $password = '')
    {
        $this->driver->setBasicAuth($user, $password);
    }

    /**
     * Sets specific request header.
     *
     * @param string $name
     * @param string $value
     */
    public function setRequestHeader($name, $value)
    {
        $this->driver->setRequestHeader($name, $value);
    }

    /**
     * Returns all response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->driver->getResponseHeaders();
    }

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     */
    public function setCookie($name, $value = null)
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
    public function getCookie($name)
    {
        return $this->driver->getCookie($name);
    }

    /**
     * Returns response status code.
     *
     * @return integer
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
     * Return the names of all open windows
     *
     * @return array Array of all open window's names.
     */
    public function getWindowNames()
    {
        return $this->driver->getWindowNames();
    }

    /**
     * Return the name of the currently active window
     *
     * @return string The name of the current window.
     */
    public function getWindowName()
    {
        return $this->driver->getWindowName();
    }

    /**
     * Reloads current session page.
     */
    public function reload()
    {
        $this->driver->reload();
    }

    /**
     * Moves backward 1 page in history.
     */
    public function back()
    {
        $this->driver->back();
    }

    /**
     * Moves forward 1 page in history.
     */
    public function forward()
    {
        $this->driver->forward();
    }

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     */
    public function switchToWindow($name = null)
    {
        $this->driver->switchToWindow($name);
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     */
    public function switchToIFrame($name = null)
    {
        $this->driver->switchToIFrame($name);
    }

    /**
     * Execute JS in browser.
     *
     * @param string $script javascript
     */
    public function executeScript($script)
    {
        $this->driver->executeScript($script);
    }

    /**
     * Execute JS in browser and return it's response.
     *
     * @param string $script javascript
     *
     * @return string
     */
    public function evaluateScript($script)
    {
        return $this->driver->evaluateScript($script);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param integer $time      time in milliseconds
     * @param string  $condition JS condition
     *
     * @return boolean
     */
    public function wait($time, $condition = 'false')
    {
        return $this->driver->wait($time, $condition);
    }

    /**
     * Set the dimensions of the window.
     *
     * @param integer $width  set the window width, measured in pixels
     * @param integer $height set the window height, measured in pixels
     * @param string  $name   window name (null for the main window)
     */
    public function resizeWindow($width, $height, $name = null)
    {
        $this->driver->resizeWindow($width, $height, $name);
    }
}
