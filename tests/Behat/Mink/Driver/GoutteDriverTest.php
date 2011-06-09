<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Session;

require_once 'DriverTest.php';

class GoutteDriverTest extends DriverTest
{
    public function testStatuses()
    {
        static::$session->visit(static::$host . '/index.php');

        $this->assertEquals(200, static::$session->getStatusCode());
        $this->assertEquals(static::$host . '/index.php', static::$session->getCurrentUrl());

        static::$session->visit(static::$host . '/404.php');

        $this->assertEquals(static::$host . '/404.php', static::$session->getCurrentUrl());
        $this->assertEquals(404, static::$session->getStatusCode());
        $this->assertEquals('Sorry, page not found', static::$session->getPage()->getContent());
    }

    protected static function configureDriver()
    {
        return new GoutteDriver();
    }
}
