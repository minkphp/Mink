<?php

namespace Tests\Behat\Mink\Driver;

require_once 'HeadlessDriverTest.php';

class GoutteDriverTest extends HeadlessDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('goutte');
    }
}
