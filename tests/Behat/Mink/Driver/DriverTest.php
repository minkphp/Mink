<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Session;

abstract class DriverTest extends \PHPUnit_Framework_TestCase
{
    protected static $host;
    protected static $session;

    public static function setUpBeforeClass()
    {
        static::$host = $_SERVER['WEB_FIXTURES_HOST'];

        static::$session = new Session(static::configureDriver(), new SelectorsHandler());
        static::$session->start();
    }

    public static function tearDownAfterClass()
    {
        static::$session->stop();
    }

    public function teardown()
    {
        static::$session->reset();
    }

    protected static function configureDriver() {}
}
