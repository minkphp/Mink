<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class VisibilityTest extends TestCase
{
    public function testVisibility()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $clicker   = $this->getSession()->getPage()->find('css', '.elements div#clicker');
        $invisible = $this->getSession()->getPage()->find('css', '#invisible');

        $this->assertNotNull($clicker);
        $this->assertNotNull($invisible);

        $this->assertFalse($invisible->isVisible());
        $this->assertTrue($clicker->isVisible());
    }
}
