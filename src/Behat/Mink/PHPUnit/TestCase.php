<?php

namespace Behat\Mink\PHPUnit;

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
 * Mink TestCase.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Mink instance.
     *
     * @var     Behat\Mink\Mink
     */
    private static $minkInstance;

    /**
     * Initializes mink instance if not instantiated yet.
     */
    public static function setUpBeforeClass()
    {
        if (null === self::$minkInstance) {
            self::$minkInstance = new Mink();
        }
    }

    /**
     * Registers missing sessions.
     */
    protected function setUp()
    {
        $this->registerSessions($this->getMink());
    }

    /**
     * Reset started sessions.
     */
    protected function teardown()
    {
        $this->getMink()->resetSessions();
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        if (null === self::$minkInstance) {
            throw new \RuntimeException(
                'Mink is not initialized. Forgot to call parent context setUpBeforeClass()?'
            );
        }

        return self::$minkInstance;
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
     * Registers Mink sessions on it's initialization.
     *
     * @param   Behat\Mink\Mink     $mink   Mink manager instance
     */
    protected function registerSessions(Mink $mink)
    {
        if (!$mink->hasSession('goutte')) {
            $mink->registerSession('goutte', static::initGoutteSession());
            $mink->setDefaultSessionName('goutte');
        }

        if (!$mink->hasSession('sahi')) {
            $mink->registerSession('sahi', static::initSahiSession());
        }
    }

    /**
     * Initizalizes and returns new GoutteDriver session.
     *
     * @param   array   $zendConfig         zend config parameters
     * @param   array   $serverParameters   server parameters
     *
     * @return  Behat\Mink\Session
     */
    protected static function initGoutteSession(array $zendConfig = array(), array $serverParameters = array())
    {
        return new Session(new GoutteDriver(new GoutteClient($zendConfig, $serverParameters)));
    }

    /**
     * Initizalizes and returns new SahiDriver session.
     *
     * @param   string  $browser    browser name to use (default = firefox)
     * @param   array   $sid        sahi SID
     * @param   string  $host       sahi proxy host
     * @param   integer $port       port number
     *
     * @return  Behat\Mink\Session
     */
    protected static function initSahiSession($browser = 'firefox', $sid = null, $host = 'localhost', $port = 9999)
    {
        return new Session(new SahiDriver($browser, new SahiClient(new SahiConnection($sid, $host, $port))));
    }
}
