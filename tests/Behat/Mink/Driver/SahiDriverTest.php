<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\SahiDriver,
    Behat\Mink\Session;

require_once 'DriverTest.php';

class SahiDriverTest extends DriverTest
{
    public function testAriaRoles()
    {
        static::$session->visit(static::$host . '/aria_roles.php');

        static::$session->wait(5000, '$("#toggle-element").is(":visible") === false');
        static::$session->getPage()->pressButton('Toggle');
        static::$session->wait(5000, '$("#toggle-element").is(":visible") === true');

        static::$session->getPage()->clickLink('Go to Index');
        $this->assertEquals(static::$host . '/index.php', static::$session->getCurrentUrl());
    }

    protected static function configureDriver()
    {
        return new SahiDriver($_SERVER['WEB_FIXTURES_BROWSER']);
    }
}
