<?php

namespace Behat\Mink\Tests\Driver\Css;

use Behat\Mink\Tests\Driver\TestCase;

class HoverTest extends TestCase
{
    /**
     * @group mouse-events
     */
    public function testMouseOverHover()
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));
        $page = $this->getSession()->getPage();

        $page->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $page->findById('action-square')->mouseOver();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     * @depends testMouseOverHover
     */
    public function testClickHover()
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));
        $page = $this->getSession()->getPage();

        $page->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $page->findById('action-square')->click();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     * @depends testMouseOverHover
     */
    public function testDoubleClickHover()
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));
        $page = $this->getSession()->getPage();

        $page->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $page->findById('action-square')->doubleClick();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     * @depends testMouseOverHover
     */
    public function testRightClickHover()
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));
        $page = $this->getSession()->getPage();

        $page->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $page->findById('action-square')->rightClick();
        $this->assertActionSquareHeight(200);
    }

    public function tearDown()
    {
        if ('testRightClickHover' === $this->getName(false)) {
            $this->getSession()->getPage()->findById('action-square')->click();
        }

        parent::tearDown();
    }

    private function assertActionSquareHeight($expected)
    {
        $this->assertEquals(
            $expected,
            $this->getSession()->evaluateScript("return window.$('#action-square').height();"),
            'Mouse is located over the object when mouse-related action is performed'
        );
    }
}
