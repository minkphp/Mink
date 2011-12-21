<?php

namespace Behat\Mink\PHPUnit;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\SeleniumDriver,
    Behat\Mink\Driver\Selenium2Driver,
    Behat\Mink\Driver\Zombie\Connection as ZombieConnection,
    Behat\Mink\Driver\Zombie\Server as ZombieServer;

use Goutte\Client as GoutteClient;

use Selenium\Client as SeleniumClient;

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
    private static $minkTestCaseMinkInstance;

    /**
     * Initializes mink instance.
     */
    public static function setUpBeforeClass()
    {
        self::$minkTestCaseMinkInstance = new Mink();
        static::registerMinkSessions(self::$minkTestCaseMinkInstance);
    }

    /**
     * Destroys mink instance.
     */
    public static function tearDownAfterClass()
    {
        if (null !== self::$minkTestCaseMinkInstance) {
            self::$minkTestCaseMinkInstance->stopSessions();
            self::$minkTestCaseMinkInstance = null;
        }
    }

    /**
     * Reset started sessions.
     */
    protected function tearDown()
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
        if (null === self::$minkTestCaseMinkInstance) {
            throw new \RuntimeException(
                'Mink is not initialized. Forgot to call parent context setUpBeforeClass()?'
            );
        }

        return self::$minkTestCaseMinkInstance;
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
    protected static function registerMinkSessions(Mink $mink)
    {
        if (!$mink->hasSession('goutte')) {
            $mink->registerSession('goutte', static::initGoutteSession());
            $mink->setDefaultSessionName('goutte');
        }

        if (!$mink->hasSession('sahi')) {
            $mink->registerSession('sahi', static::initSahiSession());
        }

        if (!$mink->hasSession('zombie')) {
            $mink->registerSession('zombie', static::initZombieSession());
        }

        if (!$mink->hasSession('selenium')) {
            $mink->registerSession('selenium', static::initSeleniumSession());
        }

        if (!$mink->hasSession('webdriver')) {
            $mink->registerSession('webdriver', static::initWebdriverSession());
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
        $zendConfig = array_merge(array('encodecookies' => false), $zendConfig);

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

    /**
     * Initizalizes and returns new ZombieDriver session.
     *
     * @param   string  $host           zombie.js server host
     * @param   integer $port           port number
     * @param   Boolean $autoServer     use bundled with driver server or manually started one
     * @param   string  $nodeBin        path to node binary
     *
     * @return  Behat\Mink\Session
     */
    protected static function initZombieSession($host = '127.0.0.1', $port = 8124,
                                                $autoServer = true, $nodeBin = 'node')
    {
        $connection = new ZombieConnection($host, $port);
        $server     = $autoServer ? new ZombieServer($host, $port, $nodeBin) : null;

        return new Session(new ZombieDriver($connection, $server, $autoServer));
    }

    /**
     * Initizalizes and returns new SeleniumDriver session.
     *
     * @param   string  $browser        browser info
     * @param   string  $baseUrl        selenium start url
     * @param   string  $host           selenium server server host
     * @param   integer $port           port number
     *
     * @return  Behat\Mink\Session
     */
    protected static function initSeleniumSession($browser = '*firefox',
                                                  $baseUrl = 'http://localhost',
                                                  $host = '127.0.0.1', $port = 4444)
    {
        return new Session(new SeleniumDriver($browser, $baseUrl, new SeleniumClient($host, $port)));
    }

    /**
     * Initizalizes and returns new Selenium2Driver session.
     *
     * @param   string  $browser        browser name
     * @param   string  $host           selenium server server host
     *
     * @return  Behat\Mink\Session
     */
    protected static function initWebdriverSession($browser = 'firefox',
                                                   $host = 'http://localhost:4444/wd/hub')
    {
        return new Session(new Selenium2Driver($browser, null, $host));
    }
}
