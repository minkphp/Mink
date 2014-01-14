<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Exception\ElementException;
use Behat\Mink\Element\Element;

require_once __DIR__ . '/JavascriptDriverTest.php';

abstract class CssDriverTest extends JavascriptDriverTest
{
    public function testTextShownWithHoverPseudoClass()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/css_pseudo_classes.html'));
        $page = $session->getPage();
        
        //grab the elements we're going to use in this test
        $parentElement = $page->findById('parent');
        $childElement = $page->findById('child');
        $resetElement = $page->findById('reset');

        //user starts with their mouse away from any elements we want to test
        $resetElement->mouseOver();

        //before the mouseOver there shouldn't be any text in the child
        $this->assertElementText($childElement, '');

        //interaction - user moves mouse over the parent element
        $parentElement->mouseOver();

        //the child element will be displayed while the mouse is hovered over
        $this->assertElementText($childElement, 'I\'ve been hovered');

        //interaction - user moves mouse away from the parent element
        $resetElement->mouseOver();

        //the child element should no longer contain any text
        $this->assertElementText($childElement, '');
    }

    protected function assertElementText(Element $element, $str)
    {
        $this->assertEquals($str, $element->getText());
    }
}
