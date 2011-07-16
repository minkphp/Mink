<?php

namespace Tests\Behat\Mink\Driver;

require_once 'GeneralDriverTest.php';

abstract class JavascriptDriverTest extends GeneralDriverTest
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
}
