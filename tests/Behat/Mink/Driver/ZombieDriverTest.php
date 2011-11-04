<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

class ZombieDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('zombie');
    }

    /**
     * As of 0.10.1, zombie.js doesn't support any response type except html
     */
    public function testJson() {}

    /**
     * As of 0.10.1, zombie.js doesn't support drag'n'drop
     */
    public function testDragDrop() {}
}
