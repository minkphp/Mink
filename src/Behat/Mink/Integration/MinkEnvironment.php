<?php

namespace Behat\Mink\Integration;

use Behat\Behat\Environment\Environment;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

use Goutte\Client as GoutteClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

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

        $world->sessions  = array();
        $world->getPathTo = function($path) use($world) {
            $startUrl = rtrim($world->getParameter('start_url'), '/') . '/';

            return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
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

        if (isset($this->mink)){
            $mink = $this->mink;
        }

        if (null === $mink) {
            $mink = new Mink();
            $this->registerMinkSessions($mink);
        }

        return $mink;
    }

    /**
     * Returns current Mink session.
     *
     * @param   string|null name of the session OR active session will be used
     *
     * @return  Behat\Mink\Session
     */
    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * Registers sessions on mink.
     *
     * @param   Behat\Mink\Mink $mink
     */
    private function registerMinkSessions(Mink $mink)
    {
        if (null === $this->getParameter('start_url')) {
            throw new \InvalidArgumentException('Specify start_url environment parameter');
        }
        $startUrl = $this->getParameter('start_url');
        $browser  = $this->getParameter('browser') ?: 'firefox';

        $config = $this->getParameter('goutte', array());
        $goutte = new GoutteClient(
            isset($config['zend_config'])       ? $config['zend_config']        : array(),
            isset($config['server_parameters']) ? $config['server_parameters']  : array()
        );
        $mink->registerSession('goutte', new Session(new GoutteDriver($startUrl, $goutte)));

        $config = $this->getParameter('sahi', array());
        $client = new SahiClient(new SahiConnection(
            isset($config['sid'])   ? $config['sid']    : uniqid(),
            isset($config['host'])  ? $config['host']   : 'localhost',
            isset($config['port'])  ? $config['port']   : 9999
        ));
        $mink->registerSession('sahi', new Session(new SahiDriver($startUrl, $browser, $client)));


        foreach ($this->sessions as $name => $session) {
            $mink->registerSession($name, $session);
        }
    }
}
