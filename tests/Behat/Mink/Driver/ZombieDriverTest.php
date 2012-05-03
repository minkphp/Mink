<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

/**
 * @group zombiedriver
 */
class ZombieDriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('zombie');
    }

    /**
     * As of 0.10.1, zombie.js doesn't support drag'n'drop
     */
    public function testDragDrop() {}

    /**
     * No need in wait method for Zombie
     */
    public function testWait() {}

    /**
     * Zombie.js doesn't handle selects without values
     */
    public function testIssue193() {}

    // Zombie.js doesn't support iFrames switching
    public function testIFrame() {}

    // Zombie.js doesn't support window switching
    public function testWindow() {}
}
