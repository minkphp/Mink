<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\NamedSelector;
use Behat\Mink\Selector\SelectorsHandler;

/**
 * @group unittest
 */
class NamedSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterXpath()
    {
        $selector = new NamedSelector();

        $selector->registerNamedXpath('some', 'my_xpath');
        $this->assertEquals('my_xpath', $selector->translateToXPath('some'));

        $this->setExpectedException('InvalidArgumentException');

        $selector->translateToXPath('custom');
    }

    /**
     * @dataProvider getSelectorTests
     */
    public function testSelectors($fixtureFile, $selector, $locator, $expectedCount)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile(__DIR__.'/fixtures/'.$fixtureFile);

        // Escape the locator as Mink 1.x expects the caller of the NamedSelector to handle it
        $selectorsHandler = new SelectorsHandler();
        $locator = $selectorsHandler->xpathLiteral($locator);

        $namedSelector = new NamedSelector();

        $xpath = $namedSelector->translateToXPath(array($selector, $locator));

        $domXpath = new \DOMXPath($dom);
        $nodeList = $domXpath->query($xpath);

        $this->assertEquals($expectedCount, $nodeList->length);
    }

    public function getSelectorTests()
    {
        // Fixture file,  selector name,  locator,  expected number of matched elements
        return array(
            'link' => array('test.html', 'link', 'link', 5),
            'link not found' => array('test.html', 'link', 'Not found', 0),
            'button' => array('test.html', 'button', 'Send', 19),
            'link or button' => array('test.html', 'link_or_button', 'Something else', 2),
        );
    }
}
