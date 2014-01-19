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
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLocator()
    {
        $namedSelector = new NamedSelector();

        $namedSelector->translateToXPath(array('foo', 'bar', 'baz'));
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
        $fieldCount = 12; // fields without `type` attribute
        $fieldCount += 4; // fields with `type=checkbox` attribute
        $fieldCount += 4; // fields with `type=radio` attribute
        $fieldCount += 4; // fields with `type=file` attribute

        // Fixture file,  selector name,  locator,  expected number of matched elements
        return array(
            'fieldset' => array('test.html', 'fieldset', 'fieldset-text', 2),

            'field (name/placeholder/label)' => array('test.html', 'field', 'the-field', $fieldCount),
            'field (input, with-id)' => array('test.html', 'field', 'the-field-input', 1),
            'field (textarea, with-id)' => array('test.html', 'field', 'the-field-textarea', 1),
            'field (select, with-id)' => array('test.html', 'field', 'the-field-select', 1),
            'field (input type=submit, with-id) ignored' => array('test.html', 'field', 'the-field-submit-button', 0),
            'field (input type=image, with-id) ignored' => array('test.html', 'field', 'the-field-image-button', 0),
            'field (input type=button, with-id) ignored' => array('test.html', 'field', 'the-field-button-button', 0),
            'field (input type=reset, with-id) ignored' => array('test.html', 'field', 'the-field-reset-button', 0),
            'field (input type=hidden, with-id) ignored' => array('test.html', 'field', 'the-field-hidden', 0),

            'link (with-href)' => array('test.html', 'link', 'link-text', 5),
            'link (without-href) ignored' => array('test.html', 'link', 'bad-link-text', 0),
            'link* (role=link)' => array('test.html', 'link', 'link-role-text', 4),

            'button (input, name/value/title)' => array('test.html', 'button', 'button-text', 25),
            'button (type=image, with-alt)' => array('test.html', 'button', 'button-alt-text', 1),
            'button (input type=submit, with-id)' => array('test.html', 'button', 'input-submit-button', 1),
            'button (input type=image, with-id)' => array('test.html', 'button', 'input-image-button', 1),
            'button (input type=button, with-id)' => array('test.html', 'button', 'input-button-button', 1),
            'button (input type=reset, with-id)' => array('test.html', 'button', 'input-reset-button', 1),
            'button (button type=submit, with-id)' => array('test.html', 'button', 'button-submit-button', 1),
            'button (button type=image, with-id)' => array('test.html', 'button', 'button-image-button', 1),
            'button (button type=button, with-id)' => array('test.html', 'button', 'button-button-button', 1),
            'button (button type=reset, with-id)' => array('test.html', 'button', 'button-reset-button', 1),
            'button* (role=button, name/value/title)' => array('test.html', 'button', 'button-role-text', 12),
            'button* (role=button type=submit, with-id)' => array('test.html', 'button', 'role-button-submit-button', 1),
            'button* (role=button type=image, with-id)' => array('test.html', 'button', 'role-button-image-button', 1),
            'button* (role=button type=button, with-id)' => array('test.html', 'button', 'role-button-button-button', 1),
            'button* (role=button type=reset, with-id)' => array('test.html', 'button', 'role-button-reset-button', 1),

            'link_or_button (with-href)' => array('test.html', 'link_or_button', 'link-text', 5),
            'link_or_button (without-href) ignored' => array('test.html', 'link_or_button', 'bad-link-text', 0),
            'link_or_button* (role=link)' => array('test.html', 'link_or_button', 'link-role-text', 4),

            // bug in selector: 17 instead of 25, because 8 buttons with `name` attribute were not matched
            'link_or_button (input, name/value/title)' => array('test.html', 'link_or_button', 'button-text', 17),
            'link_or_button (type=image, with-alt)' => array('test.html', 'link_or_button', 'button-alt-text', 1),
            'link_or_button (input type=submit, with-id)' => array('test.html', 'link_or_button', 'input-submit-button', 1),
            'link_or_button (input type=image, with-id)' => array('test.html', 'link_or_button', 'input-image-button', 1),
            'link_or_button (input type=button, with-id)' => array('test.html', 'link_or_button', 'input-button-button', 1),
            'link_or_button (input type=reset, with-id)' => array('test.html', 'link_or_button', 'input-reset-button', 1),
            'link_or_button (button type=submit, with-id)' => array('test.html', 'link_or_button', 'button-submit-button', 1),
            'link_or_button (button type=image, with-id)' => array('test.html', 'link_or_button', 'button-image-button', 1),
            'link_or_button (button type=button, with-id)' => array('test.html', 'link_or_button', 'button-button-button', 1),
            'link_or_button (button type=reset, with-id)' => array('test.html', 'link_or_button', 'button-reset-button', 1),

            // bug in selector: 8 instead of 12, because 4 buttons with `name` attribute were not matched
            'link_or_button* (role=button, name/value/title)' => array('test.html', 'link_or_button', 'button-role-text', 8),
            'link_or_button* (role=button type=submit, with-id)' => array('test.html', 'link_or_button', 'role-button-submit-button', 1),
            'link_or_button* (role=button type=image, with-id)' => array('test.html', 'link_or_button', 'role-button-image-button', 1),
            'link_or_button* (role=button type=button, with-id)' => array('test.html', 'link_or_button', 'role-button-button-button', 1),
            'link_or_button* (role=button type=reset, with-id)' => array('test.html', 'link_or_button', 'role-button-reset-button', 1),

            // 3 matches, because matches every HTML node in path: html > body > div
            'content' => array('test.html', 'content', 'content-text', 3),

            'select (name/placeholder/label)' => array('test.html', 'select', 'the-field', 4),
            'select (with-id)' => array('test.html', 'select', 'the-field-select', 1),

            'checkbox (name/placeholder/label)' => array('test.html', 'checkbox', 'the-field', 4),
            'checkbox (with-id)' => array('test.html', 'checkbox', 'the-field-checkbox', 1),

            'radio (name/placeholder/label)' => array('test.html', 'radio', 'the-field', 4),
            'radio (with-id)' => array('test.html', 'radio', 'the-field-radio', 1),

            'file (name/placeholder/label)' => array('test.html', 'file', 'the-field', 4),
            'file (with-id)' => array('test.html', 'file', 'the-field-file', 1),

            'optgroup' => array('test.html', 'optgroup', 'group-label', 1),

            'option' => array('test.html', 'option', 'option-value', 2),

            'table' => array('test.html', 'table', 'the-table', 2),
        );
    }
}
