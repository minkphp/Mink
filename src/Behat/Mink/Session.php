<?php

namespace Behat\Mink;

use Behat\Mink\Driver\DriverInterface,
    Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Element\DocumentElement;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink session.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Session
{
    private $driver;
    private $page;
    private $selectorsHandler;

    /**
     * Initialize session.
     *
     * @param   Behat\Mink\Driver\DriverInterface       $driver             driver instance
     * @param   Behat\Mink\Selector\SelectorsHandler    $selectorsHandler   selectors handler
     */
    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler = null)
    {
        $driver->setSession($this);

        if (null === $selectorsHandler) {
            $selectorsHandler = new SelectorsHandler();
        }

        $this->driver           = $driver;
        $this->page             = new DocumentElement($this);
        $this->selectorsHandler = $selectorsHandler;
    }

    /**
     * Checks whether session (driver) was started.
     *
     * @return  Boolean
     */
    public function isStarted()
    {
        return $this->driver->isStarted();
    }

    /**
     * Starts session driver.
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
     * Reset session driver.
     */
    public function reset()
    {
        $this->driver->reset();
    }

    /**
     * Returns session driver.
     *
     * @return  Behat\Mink\Driver\DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns page element.
     *
     * @return  Behat\Mink\Element\DocumentElement
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Returns selectors handler.
     *
     * @return  Behat\Mink\Selector\SelectorsHandler
     */
    public function getSelectorsHandler()
    {
        return $this->selectorsHandler;
    }

    /**
     * Visit specified URL.
     *
     * @param   string  $url    url of the page
     */
    public function visit($url)
    {
        $this->driver->visit($url);
    }

    /**
     * Sets specific request header.
     *
     * @param   string  $name
     * @param   string  $value
     */
    public function setRequestHeader($name, $value)
    {
        $this->driver->setRequestHeader($name, $value);
    }

    /**
     * Returns all response headers.
     *
     * @return  array
     */
    public function getResponseHeaders()
    {
        return $this->driver->getResponseHeaders();
    }

    /**
     * Returns response status code.
     *
     * @return  integer
     */
    public function getStatusCode()
    {
        return $this->driver->getStatusCode();
    }

    /**
     * Returns current URL address.
     *
     * @return  string
     */
    public function getCurrentUrl()
    {
        return $this->driver->getCurrentUrl();
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
     * Execute JS in browser.
     *
     * @param   string  $script     javascript
     */
    public function executeScript($script)
    {
        $this->driver->executeScript($script);
    }

    /**
     * Execute JS in browser and return it's response.
     *
     * @param   string  $script     javascript
     *
     * @return  string              response
     */
    public function evaluateScript($script)
    {
        return $this->driver->evaluateScript($script);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param   integer $time       time in milliseconds
     * @param   string  $condition  JS condition
     */
    public function wait($time, $condition)
    {
        $this->driver->wait($time, $condition);
    }
}
