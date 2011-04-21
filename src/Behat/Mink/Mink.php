<?php

namespace Behat\Mink;

use Behat\Mink\Driver\DriverInterface,
    Behat\Mink\Selector\SelectorsHandler;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Mink
{
    private $selectorsHandler;
    private $defaultDriverName;
    private $currentDriverName;
    private $drivers = array();

    /**
     * Initializes mink.
     *
     * @param   Behat\Mink\Selector\SelectorsHandler    $selectorsHandler
     */
    public function __construct(SelectorsHandler $selectorsHandler = null)
    {
        if (null === $selectorsHandler) {
            $selectorsHandler = new SelectorsHandler();
        }

        $this->selectorsHandler = $selectorsHandler;
    }

    /**
     * Stops all started drivers.
     */
    public function __destruct()
    {
        $this->stopDrivers();
    }

    /**
     * Registers new driver.
     *
     * @param   string                              $name       driver alias name
     * @param   Behat\Mink\Driver\DriverInterface   $driver     driver instance
     * @param   Boolean                             $isDefault  should this driver be the default one?
     */
    public function registerDriver($name, DriverInterface $driver, $isDefault = false)
    {
        $name = strtolower($name);

        $this->drivers[$name] = $driver;

        if ($isDefault) {
            $this->defaultDriverName = $name;
        }
    }

    /**
     * Switches mink to specific driver.
     *
     * @param   string  $name   driver alias name
     */
    public function switchToDriver($name)
    {
        $name = strtolower($name);

        if (!isset($this->drivers[$name])) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is not registered.', $name));
        }

        if (null !== $this->currentDriverName && $name === $this->currentDriverName) {
            return;
        }

        $this->currentDriverName = $name;
    }

    /**
     * Switches mink to default driver.
     */
    public function switchToDefaultDriver()
    {
        if (null === $this->defaultDriverName) {
            throw new \RuntimeException('Default driver is not defined.');
        }

        $this->switchToDriver($this->defaultDriverName);
    }

    /**
     * Resets driver (between tests for example).
     */
    public function resetDriver()
    {
        $this->getDriver()->reset();
    }

    /**
     * Stop all started drivers. 
     */
    public function stopDrivers()
    {
        foreach ($this->drivers as $driver) {
            if ($driver->isStarted()) {
                $driver->stop();
            }
        }
    }

    /**
     * Returns mink session, initialized with current (or default) driver.
     *
     * @return  Behat\Mink\Session
     */
    public function getSession()
    {
        static $session;

        if (null === $session || $session->getDriver() !== $this->getDriver()) {
            $session = new Session($this->getDriver(), $this->selectorsHandler);
        }

        return $session;
    }

    /**
     * Returns current (or default) driver.
     *
     * @return  Behat\Mink\Driver\DriverInterface
     */
    private function getDriver()
    {
        if (null === $this->currentDriverName) {
            $this->switchToDefaultDriver();
        }

        if (!$this->drivers[$this->currentDriverName]->isStarted()) {
            $this->drivers[$this->currentDriverName]->start();
        }

        return $this->drivers[$this->currentDriverName];
    }
}
