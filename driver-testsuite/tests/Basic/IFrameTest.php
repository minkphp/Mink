<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class IFrameTest extends TestCase
{
    public function testIFrame()
    {
        $this->getSession()->visit($this->pathTo('/iframe.php'));
        $page = $this->getSession()->getPage();

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());

        $this->getSession()->switchToIFrame('subframe');

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('iFrame div text', $el->getText());

        $this->getSession()->switchToIFrame();

        $el = $page->find('css', '#text');
        $this->assertNotNull($el);
        $this->assertSame('Main window div text', $el->getText());
    }
}
