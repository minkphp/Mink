<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\CssSelector;

/**
 * @group unittest
 */
class CssSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testSelector()
    {
        if (!class_exists('Symfony\Component\CssSelector\CssSelector')) {
            $this->markTestSkipped('Symfony2 CssSelector component not installed');
        }

        $selector = new CssSelector();

        $this->assertEquals('descendant-or-self::h3', $selector->translateToXPath('h3'));
        $this->assertEquals('descendant-or-self::h3/span', $selector->translateToXPath('h3 > span'));

        if (interface_exists('Symfony\Component\CssSelector\XPath\TranslatorInterface')) {
            // The rewritten component of Symfony 2.3 checks for attribute existence first for the class.
            $expectation = "descendant-or-self::h3/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' my_div ')]";
        } else {
            $expectation = "descendant-or-self::h3/*[contains(concat(' ', normalize-space(@class), ' '), ' my_div ')]";
        }
        $this->assertEquals($expectation, $selector->translateToXPath('h3 > .my_div'));
    }
}
