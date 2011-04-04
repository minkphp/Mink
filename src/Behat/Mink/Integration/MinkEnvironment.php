<?php

namespace Behat\Mink\Integration;

use Behat\Behat\Environment\Environment;

use Behat\Mink\Mink,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink Behat environment extension
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkEnvironment extends Environment
{
    /**
     * Initializes Mink environment.
     */
    public function __construct()
    {
        $world = $this;

        $world->initGoutteDriver = function() use($world) {
            return new GoutteDriver(
                $world->getParameter('start_url')   ?: 'http://behat.org/'
            );
        };

        $world->initSahiDriver = function() use($world) {
            return new SahiDriver(
                $world->getParameter('start_url')   ?: 'http://behat.org/',
                $world->getParameter('browser')     ?: 'firefox'
            );
        };

        $world->getPathTo = function($path) use($world) {
            return ($world->getParameter('start_url') ?: 'http://behat.org/') . $path;
        };
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        static $mink;

        if (null === $mink) {
            $mink = new Mink();
            $this->registerMinkDrivers($mink);
        }

        return $mink;
    }

    /**
     * Returns current Mink session.
     *
     * @return  Behat\Mink\Session
     */
    public function getSession()
    {
        return $this->getMink()->getSession();
    }

    /**
     * Returns default Mink driver name.
     *
     * @return  string
     */
    public function getDefaultDriverName()
    {
        return $this->getParameter('default_driver') ?: 'goutte';
    }

    /**
     * Returns default javascript driver name.
     *
     * @return  string
     */
    public function getJavascriptDriverName()
    {
        return $this->getParameter('javascript_driver') ?: 'sahi';
    }

    /**
     * Registers drivers on mink.
     *
     * @param   Behat\Mink\Mink $mink
     */
    protected function registerMinkDrivers(Mink $mink)
    {
        foreach ($this->getDrivers() as $driver) {
            $builder = 'init' . ucfirst($driver) . 'Driver';
            $mink->registerDriver($driver, $this->$builder(), $driver === $this->getDefaultDriverName());
        }
    }

    /**
     * Returns drivers list (available_drivers parameter).
     *
     * @return  array
     */
    private function getDrivers()
    {
        return (array) ($this->getParameter('available_drivers') ?: array('goutte', 'sahi'));
    }
}
