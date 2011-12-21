<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Event\SuiteEvent;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\SeleniumDriver,
    Behat\Mink\Driver\Selenium2Driver;

use Goutte\Client as GoutteClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

use Behat\Mink\Driver\Zombie\Connection as ZombieConnection,
    Behat\Mink\Driver\Zombie\Server as ZombieServer;

use Selenium\Client as SeleniumClient;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink context for Behat BDD tool.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkContext extends BaseMinkContext
{
    private static $mink;
    private static $parameters;

    /**
     * {@inheritdoc}
     */
    public function getMink()
    {
        if (null === self::$mink) {
            throw new \RuntimeException(
                'Mink is not initialized. Doing something weird in SuiteHook? You should not!'
            );
        }

        return self::$mink;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return self::$parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        if (!isset(self::$parameters[$name])) {
            return;
        }

        return self::$parameters[$name];
    }

    /**
     * Initializes Mink instance and sessions.
     *
     * @param   Behat\Behat\Event\SuiteEvent $event
     *
     * @BeforeSuite
     */
    public static function initMinkSessions(SuiteEvent $event)
    {
        self::$parameters = static::mergeConfigWithDefaults(
            static::getDefaultParameters(), $event->getContextParameters()
        );

        if (null === self::$mink) {
            self::$mink = new Mink();
        }

        static::registerMinkSessions(self::$mink, self::$parameters);
    }

    /**
     * Stops started Mink sessions.
     *
     * @AfterSuite
     */
    public static function stopMinkSessions()
    {
        self::$mink->stopSessions();
        self::$mink = null;
    }

    /**
     * Registers Mink sessions on it's initialization.
     *
     * @param   Behat\Mink\Mink     $mink   Mink manager instance
     */
    protected static function registerMinkSessions(Mink $mink, array $parameters)
    {
        if (!$mink->hasSession('goutte')) {
            $params = $parameters['goutte'];
            $mink->registerSession('goutte', static::initGoutteSession(
                $params['zend_config'], $params['server_parameters']
            ));
        }

        if (!$mink->hasSession('sahi')) {
            $params = $parameters['sahi'];
            $mink->registerSession('sahi', static::initSahiSession(
                $parameters['browser'], $params['sid'], $params['host'], $params['port']
            ));
        }

        if (!$mink->hasSession('zombie')) {
            $params = $parameters['zombie'];
            $mink->registerSession('zombie', static::initZombieSession(
                $params['host'], $params['port'], $params['auto_server'], $params['node_bin']
            ));
        }

        if (!$mink->hasSession('selenium')) {
            $params  = $parameters['selenium'];
            $browser = isset($params['browser']) ? $params['browser'] : '*'.$parameters['browser'];
            $mink->registerSession('selenium', static::initSeleniumSession(
                $browser, $parameters['base_url'], $params['host'], $params['port']
            ));
        }

        if (!$mink->hasSession('webdriver')) {
            $params  = $parameters['webdriver'];
            $browser = $parameters['browser'];
            $mink->registerSession('webdriver', static::initWebdriverSession(
                $browser, $params['host']
            ));
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
     * @param   Boolean $autoServer     use bundled with driver automatically startable server
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

    /**
     * Returns list of default parameters.
     *
     * @return  array
     */
    protected static function getDefaultParameters()
    {
        return array(
            'default_session'    => 'goutte',
            'javascript_session' => 'sahi',
            'base_url'           => 'http://localhost',
            'show_cmd'           => static::getDefaultShowCmd(),
            'show_tmp_dir'       => sys_get_temp_dir(),
            'browser'            => 'firefox',
            'goutte' => array(
                'zend_config'       => array(),
                'server_parameters' => array()
            ),
            'sahi' => array(
                'sid'  => null,
                'host' => 'localhost',
                'port' => 9999
            ),
            'zombie' => array(
                'host'          => '127.0.0.1',
                'port'          => 8124,
                'node_bin'      => 'node',
                'auto_server'   => true
            ),
            'selenium' => array(
                'host' => 'localhost',
                'port' => 4444
            ),
            'webdriver' => array(
                'host' => 'http://localhost:4444/wd/hub'
            ),
        );
    }

    /**
     * Returns default show command.
     *
     * @return  string
     */
    protected static function getDefaultShowCmd()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'explorer.exe $s';
        }

        switch(PHP_OS) {
            case 'Darwin':
                return 'open %s';
            case 'Linux':
            case 'FreeBSD':
                return 'xdg-open %s';
        }

        return null;
    }

    /**
     * Merge two arrays into first one with overwrites.
     *
     * @param   array   $defaults
     * @param   array   $configs
     *
     * @return  array
     */
    protected static function mergeConfigWithDefaults($defaults, $configs)
    {
        foreach($configs as $key => $val) {
            if(array_key_exists($key, $defaults) && is_array($val)) {
                $defaults[$key] = static::mergeConfigWithDefaults($defaults[$key], $configs[$key]);
            } elseif (is_numeric($key)) {
                $defaults[] = $val;
            } else {
                $defaults[$key] = $val;
            }
        }

        return $defaults;
    }
}
