<?php

namespace Tests\Behat\Mink\Driver;

require_once 'GeneralDriverTest.php';

abstract class HeadlessDriverTest extends GeneralDriverTest
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

    public function testHeaders()
    {
        static::$session->setRequestHeader('Accept-Language', 'fr');
        static::$session->visit(static::$host . '/headers.php');

        $this->assertContains('[HTTP_ACCEPT_LANGUAGE] => fr', static::$session->getPage()->getContent());
    }
}
