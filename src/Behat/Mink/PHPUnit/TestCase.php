<?php

namespace Behat\Mink\PHPUnit;

use Goutte\Client as GoutteClient,
    Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\SeleniumDriver,
    Behat\Mink\Driver\Selenium2Driver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer,
    Behat\Mink\Exception\ResponseTextException;

use Selenium\Client as SeleniumClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

use Behat\Mink\PHPUnit\Constraints\PageContains as PageContainsConstraint;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

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
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
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
     * @return Mink
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
     * @param string|null name of the session OR active session will be used
     *
     * @return Session
     */
    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * Checks, that page contains specified text
     *
     * @param Session $session
     * @param string  $text     text to look for
     * @param string  $message  optional message to show on fail
     *
     * @throws ResponseTextException
     *
     * @return void
     */
    public static function assertPageContainsText(Session $session, $text, $message = null)
    {
      $text = str_replace('\\"', '"', $text);
      $haystack = $session->getPage()->getText();

      $message = $message ?:
        sprintf('The text "%s" was not found anywhere in the text of the page', $text);

      $constraint = new PageContainsConstraint($text, false);
      self::assertThat($haystack, $constraint, $message);
    }

    /**
     * Registers Mink sessions on it's initialization.
     *
     * @param Mink $mink Mink manager instance
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
     * @param array $serverParameters server parameters
     *
     * @return Session
     */
    protected static function initGoutteSession(array $serverParameters = array())
    {
        return new Session(new GoutteDriver(new GoutteClient($serverParameters)));
    }

    /**
     * Initizalizes and returns new SahiDriver session.
     *
     * @param string  $browser browser name to use (default = firefox)
     * @param array   $sid     sahi SID
     * @param string  $host    sahi proxy host
     * @param integer $port    port number
     *
     * @return Session
     */
    protected static function initSahiSession($browser = 'firefox', $sid = null, $host = 'localhost', $port = 9999)
    {
        return new Session(new SahiDriver($browser, new SahiClient(new SahiConnection($sid, $host, $port))));
    }

    /**
     * Initizalizes and returns new ZombieDriver session.
     *
     * @param string  $host       zombie.js server host
     * @param integer $port       port number
     * @param Boolean $autoServer use bundled with driver server or manually started one
     * @param string  $nodeBin    path to node binary
     *
     * @return Session
     */
    protected static function initZombieSession($host = '127.0.0.1', $port = 8124,
                                                $autoServer = true, $nodeBin = 'node')
    {
        $server = $autoServer ? new ZombieServer($host, $port, $nodeBin) : null;
        if (null === $server) {
            return new Session(new ZombieDriver($host, $port));
        }

        return new Session(new ZombieDriver($server));
    }

    /**
     * Initizalizes and returns new SeleniumDriver session.
     *
     * @param string  $browser browser info
     * @param string  $baseUrl selenium start url
     * @param string  $host    selenium server server host
     * @param integer $port    port number
     *
     * @return Session
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
     * @param string $browser browser name
     * @param string $host    selenium server server host
     *
     * @return Session
     */
    protected static function initWebdriverSession($browser = 'firefox',
                                                   $host = 'http://localhost:4444/wd/hub')
    {
        return new Session(new Selenium2Driver($browser, null, $host));
    }
}
