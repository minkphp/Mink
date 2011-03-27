<?php

namespace Behat\Mink;

use Behat\Mink\Driver\DriverInterface,
    Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Element\DocumentElement;

class Session
{
    private $driver;
    private $page;
    private $selectorsHandler;

    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler)
    {
        $this->driver           = $driver;
        $this->page             = new DocumentElement($this);
        $this->selectorsHandler = $selectorsHandler;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getSelectorsHandler()
    {
        return $this->selectorsHandler;
    }

    public function visit($url)
    {
        $this->driver->visit($url);
    }

    public function reset()
    {
        $this->driver->reset();
    }

    public function getResponseHeaders()
    {
        return $this->driver->getResponseHeaders();
    }

    public function getStatusCode()
    {
        return $this->driver->getStatusCode();
    }

    public function getCurrentPath()
    {
        return $this->driver->getCurrentPath();
    }

    public function getCurrentUrl()
    {
        return $this->driver->getCurrentUrl();
    }

    public function executeScript($script)
    {
        $this->driver->executeScript($script);
    }

    public function evaluateScript($script)
    {
        return $this->driver->evaluateScript($script);
    }
}
