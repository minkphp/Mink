<?php

namespace Behat\Mink\Integration;

use Behat\Behat\Environment\Environment;

use Behat\Mink\Mink,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

class MinkEnvironment extends Environment
{
    public function __construct()
    {
        $world = $this;

        $world->initGoutteDriver = function() use($world) {
            return new GoutteDriver(
                $world->getParameter('start_url')   ?: 'http://behat.org'
            );
        };

        $world->initSahiDriver = function() use($world) {
            return new SahiDriver(
                $world->getParameter('start_url')   ?: 'http://behat.org',
                $world->getParameter('browser')     ?: 'firefox'
            );
        };

        $world->getPathTo = function($path) {
            return $path;
        };
    }

    public function getMink()
    {
        static $mink;

        if (null === $mink) {
            $mink = new Mink();
            $this->registerMinkDrivers($mink);
        }

        return $mink;
    }

    public function getSession()
    {
        return $this->getMink()->getSession();
    }

    protected function registerMinkDrivers(Mink $mink)
    {
        foreach ($this->getDrivers() as $driver) {
            $builder = 'init' . ucfirst($driver) . 'Driver';
            $mink->registerDriver($driver, $builder(), $driver === $this->getDefaultDriver());
        }
    }

    private function getDrivers()
    {
        return (array) ($this->getParameter('available_drivers') ?: array('goutte', 'sahi'));
    }

    private function getDefaultDriver()
    {
        return $this->getParameter('default_driver') ?: 'goutte';
    }

    private function getJavascriptDriver()
    {
        return $this->getParameter('javascript_driver') ?: 'sahi';
    }
}
