<?php

namespace Behat\Mink\Tests\Selector;

use Behat\Mink\Selector\CssSelector;
use PHPUnit\Framework\TestCase;

class CssSelectorTest extends TestCase
{
    public function testSelector()
    {
        $selector = new CssSelector();

        $this->assertEquals('descendant-or-self::h3', $selector->translateToXPath('h3'));
        $this->assertEquals('descendant-or-self::h3/span', $selector->translateToXPath('h3 > span'));

        $expectation = "descendant-or-self::h3/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' my_div ')]";
        $this->assertEquals($expectation, $selector->translateToXPath('h3 > .my_div'));
    }

    public function testThrowsForArrayLocator()
    {
        $this->expectException('\InvalidArgumentException');
        $selector = new CssSelector();

        $selector->translateToXPath(array('h3'));
    }
}
