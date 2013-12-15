<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\PartialSelector;

/**
 * @group unittest
 */
class PartialSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterXpath()
    {
        $selector = new PartialSelector();

        $selector->registerNamedXpath('some', 'my_xpath');
        $this->assertEquals('my_xpath', $selector->translateToXPath('some'));

        $this->setExpectedException('InvalidArgumentException');

        $selector->translateToXPath('custom');
    }
}
