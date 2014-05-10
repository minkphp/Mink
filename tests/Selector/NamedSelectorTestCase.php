<?php

namespace Behat\Mink\Tests\Selector;

use Behat\Mink\Selector\NamedSelector;
use PHPUnit\Framework\TestCase;

abstract class NamedSelectorTestCase extends TestCase
{
    public function testRegisterXpath()
    {
        $selector = $this->getSelector();

        $selector->registerNamedXpath('some', 'my_xpath');
        $this->assertEquals('my_xpath', $selector->translateToXPath('some'));

        $this->expectException('InvalidArgumentException');

        $selector->translateToXPath('custom');
    }

    public function testInvalidLocator()
    {
        $this->expectException('\InvalidArgumentException');

        $namedSelector = $this->getSelector();

        $namedSelector->translateToXPath(array('foo', 'bar', 'baz'));
    }

    /**
     * @dataProvider getSelectorTests
     */
    public function testSelectors(string $fixtureFile, string $selector, string $locator, int $expectedExactCount, ?int $expectedPartialCount = null)
    {
        $expectedCount = $this->allowPartialMatch() && null !== $expectedPartialCount
            ? $expectedPartialCount
            : $expectedExactCount;

        // Don't use "loadHTMLFile" due HHVM 3.3.0 issue.
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $html = file_get_contents(__DIR__.'/fixtures/'.$fixtureFile);
        \assert($html !== false);
        $dom->loadHTML($html);

        $namedSelector = $this->getSelector();

        $xpath = $namedSelector->translateToXPath(array($selector, $locator));

        $domXpath = new \DOMXPath($dom);
        $nodeList = $domXpath->query($xpath);

        $this->assertNotFalse($nodeList, 'The XPath should be valid.');
        $this->assertEquals($expectedCount, $nodeList->length);
    }

    /**
     * @dataProvider getLateRegisteredReplacements
     *
     * @param array<string, string> $replacements
     * @param array<string, string> $selectors
     */
    public function testLateRegisteredReplacements(string $fixtureFile, array $replacements, array $selectors, string $selector, string $locator, int $expectedExactCount, ?int $expectedPartialCount = null)
    {
        $expectedCount = $this->allowPartialMatch() && null !== $expectedPartialCount
            ? $expectedPartialCount
            : $expectedExactCount;

        // Don't use "loadHTMLFile" due HHVM 3.3.0 issue.
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $html = file_get_contents(__DIR__.'/fixtures/'.$fixtureFile);
        \assert($html !== false);
        $dom->loadHTML($html);

        $namedSelector = $this->getSelector();

        foreach ($replacements as $from => $to) {
            $namedSelector->registerReplacement($from, $to);
        }

        foreach ($selectors as $from => $to) {
            $namedSelector->registerNamedXpath($from, $to);
        }

        $xpath = $namedSelector->translateToXPath(array($selector, $locator));

        $domXpath = new \DOMXPath($dom);
        $nodeList = $domXpath->query($xpath);

        $this->assertNotFalse($nodeList, 'The XPath should be valid.');
        $this->assertEquals($expectedCount, $nodeList->length);
    }

    public static function getSelectorTests()
    {
        $fieldCount = 8; // fields without `type` attribute
        $fieldCount += 4; // fields with `type=checkbox` attribute
        $fieldCount += 4; // fields with `type=radio` attribute
        $fieldCount += 4; // fields with `type=file` attribute

        // Fixture file,  selector name,  locator,  expected number of exact matched elements, expected number of partial matched elements if different
        return array(
            'fieldset' => array('test.html', 'fieldset', 'fieldset-text', 2, 3),

            'field (name/placeholder/label)' => array('test.html', 'field', 'the-field', $fieldCount),
            'field (input, with-id)' => array('test.html', 'field', 'the-field-input', 1),
            'field (textarea, with-id)' => array('test.html', 'field', 'the-field-textarea', 1),
            'field (select, with-id)' => array('test.html', 'field', 'the-field-select', 1),
            'field (input type=submit, with-id) ignored' => array('test.html', 'field', 'the-field-submit-button', 0),
            'field (input type=image, with-id) ignored' => array('test.html', 'field', 'the-field-image-button', 0),
            'field (input type=button, with-id) ignored' => array('test.html', 'field', 'the-field-button-button', 0),
            'field (input type=reset, with-id) ignored' => array('test.html', 'field', 'the-field-reset-button', 0),
            'field (input type=hidden, with-id) ignored' => array('test.html', 'field', 'the-field-hidden', 0),

            'link (with-href)' => array('test.html', 'link', 'link-text', 5, 9),
            'link (without-href) ignored' => array('test.html', 'link', 'bad-link-text', 0),
            'link* (role=link)' => array('test.html', 'link', 'link-role-text', 4, 7),

            'button (input, name/value/title)' => array('test.html', 'button', 'button-text', 25, 42),
            'button (type=image, with-alt)' => array('test.html', 'button', 'button-alt-text', 1, 2),
            'button (input type=submit, with-id)' => array('test.html', 'button', 'input-submit-button', 1),
            'button (input type=image, with-id)' => array('test.html', 'button', 'input-image-button', 1),
            'button (input type=button, with-id)' => array('test.html', 'button', 'input-button-button', 1),
            'button (input type=reset, with-id)' => array('test.html', 'button', 'input-reset-button', 1),
            'button (button type=submit, with-id)' => array('test.html', 'button', 'button-submit-button', 1),
            'button (button type=image, with-id)' => array('test.html', 'button', 'button-image-button', 1),
            'button (button type=button, with-id)' => array('test.html', 'button', 'button-button-button', 1),
            'button (button type=reset, with-id)' => array('test.html', 'button', 'button-reset-button', 1),
            'button* (role=button, name/value/title)' => array('test.html', 'button', 'button-role-text', 12, 20),
            'button* (role=button type=submit, with-id)' => array('test.html', 'button', 'role-button-submit-button', 1),
            'button* (role=button type=image, with-id)' => array('test.html', 'button', 'role-button-image-button', 1),
            'button* (role=button type=button, with-id)' => array('test.html', 'button', 'role-button-button-button', 1),
            'button* (role=button type=reset, with-id)' => array('test.html', 'button', 'role-button-reset-button', 1),

            'link_or_button (with-href)' => array('test.html', 'link_or_button', 'link-text', 5, 9),
            'link_or_button (without-href) ignored' => array('test.html', 'link_or_button', 'bad-link-text', 0),
            'link_or_button* (role=link)' => array('test.html', 'link_or_button', 'link-role-text', 4, 7),

            // bug in selector: 17 instead of 25 and 34 instead of 42, because 8 buttons with `name` attribute were not matched
            'link_or_button (input, name/value/title)' => array('test.html', 'link_or_button', 'button-text', 17, 34),
            'link_or_button (type=image, with-alt)' => array('test.html', 'link_or_button', 'button-alt-text', 1, 2),
            'link_or_button (input type=submit, with-id)' => array('test.html', 'link_or_button', 'input-submit-button', 1),
            'link_or_button (input type=image, with-id)' => array('test.html', 'link_or_button', 'input-image-button', 1),
            'link_or_button (input type=button, with-id)' => array('test.html', 'link_or_button', 'input-button-button', 1),
            'link_or_button (input type=reset, with-id)' => array('test.html', 'link_or_button', 'input-reset-button', 1),
            'link_or_button (button type=submit, with-id)' => array('test.html', 'link_or_button', 'button-submit-button', 1),
            'link_or_button (button type=image, with-id)' => array('test.html', 'link_or_button', 'button-image-button', 1),
            'link_or_button (button type=button, with-id)' => array('test.html', 'link_or_button', 'button-button-button', 1),
            'link_or_button (button type=reset, with-id)' => array('test.html', 'link_or_button', 'button-reset-button', 1),

            // bug in selector: 8 instead of 12 and 16 instead of 20, because 4 buttons with `name` attribute were not matched
            'link_or_button* (role=button, name/value/title)' => array('test.html', 'link_or_button', 'button-role-text', 8, 16),
            'link_or_button* (role=button type=submit, with-id)' => array('test.html', 'link_or_button', 'role-button-submit-button', 1),
            'link_or_button* (role=button type=image, with-id)' => array('test.html', 'link_or_button', 'role-button-image-button', 1),
            'link_or_button* (role=button type=button, with-id)' => array('test.html', 'link_or_button', 'role-button-button-button', 1),
            'link_or_button* (role=button type=reset, with-id)' => array('test.html', 'link_or_button', 'role-button-reset-button', 1),

            // 3 matches, because matches every HTML node in path: html > body > div
            'content' => array('test.html', 'content', 'content-text', 1, 4),
            'content with quotes' => array('test.html', 'content', 'some "quoted" content', 1, 3),

            'select (name/label)' => array('test.html', 'select', 'the-field', 3),
            'select (with-id)' => array('test.html', 'select', 'the-field-select', 1),

            'checkbox (name/label)' => array('test.html', 'checkbox', 'the-field', 3),
            'checkbox (with-id)' => array('test.html', 'checkbox', 'the-field-checkbox', 1),

            'radio (name/label)' => array('test.html', 'radio', 'the-field', 3),
            'radio (with-id)' => array('test.html', 'radio', 'the-field-radio', 1),

            'file (name/label)' => array('test.html', 'file', 'the-field', 3),
            'file (with-id)' => array('test.html', 'file', 'the-field-file', 1),

            'optgroup' => array('test.html', 'optgroup', 'group-label', 1, 2),

            'option' => array('test.html', 'option', 'option-value', 2, 3),

            'table' => array('test.html', 'table', 'the-table', 2, 3),

            'id' => array('test.html', 'id', 'bad-link-text', 1),
            'id or name' => array('test.html', 'id_or_name', 'the-table', 2),
        );
    }

    public static function getLateRegisteredReplacements()
    {
        // The following tests all use `test.html` from the fixtures directory.
        return array(
            // Search for `testSpanWithRole` using locator `link`.
            //
            // Selectors:
            // testSpanWithRole => `.//span[%testRoleMatch%]`
            //
            // Replacements:
            // %testRoleMatch%  => `contains(./@role, %locator%)`
            // %locator%        => `link`
            //
            // Result:
            // .//span[%testRoleMatch%]
            // .//span[contains(./@role, %locator%)]
            // .//span[contains(./@role, 'link')]
            //
            // Expectation:
            // Exactly three results will match with an attribute of `role="link"`
            'lateSelectorUsingLateReplacement' => array(
                'file' => 'test.html',
                'replacements' => array(
                    '%testRoleMatch%' => 'contains(./@role, %locator%)'
                ),
                'selectors' => array(
                    'testSpanWithRole' => './/span[%testRoleMatch%]'
                ),
                'selector' => 'testSpanWithRole',
                'locator' => 'link',
                'expectedExactCount' => 3,
                'expectedPartialCount' => 3
            ),

            // Search for `testIdMatch` using locator `late-registered-input-with-matching-name`.
            //
            // Selectors:
            // testIdOrNameMatch    => `.//input[%testIdOrNameMatch%]`
            //
            // Replacements:
            // %testIdMatch%       => `./@id = %locator%`,
            // %testNameMatch%     => `contains(./@name, %locator%)`,
            // %testIdOrNameMatch% => `(%testIdMatch% or %testNameMatch%)`,
            // %locator%           => `late-registered-input-with-matching-name`
            //
            // Result:
            // .//input[%testIdOrNameMatch%]
            // .//input[(%testIdMatch% or %testNameMatch%)]
            // .//input[./@id = %locator% or contains(./@name, %locator%)]
            // .//input[./@id = 'late-registered-input-with-matching-name' or contains(./@name, 'late-registered-input-with-matching-name')]
            //
            // Expectation:
            // Exactly one result is found with a matching id or name.
            'lateSelectorUsingRecursiveLateReplacementMatchingId' => array(
                'file' => 'test.html',
                'replacements' => array(
                    '%testIdMatch%' => './@id = %locator%',
                    '%testNameMatch%' => 'contains(./@name, %locator%)',
                    '%testIdOrNameMatch%' => '(%testIdMatch% or %testNameMatch%)',
                ),
                'selectors' => array(
                    'testIdOrNameMatch' => './/input[%testIdOrNameMatch%]'
                ),
                'selector' => 'testIdOrNameMatch',
                'locator' => 'late-registered-input-with-matching-name',
                'expectedExactCount' => 1,
                'expectedPartialCount' => 1
            ),

            // Search for `testIdMatch` using locator `late-registered-input`.
            //
            // Selectors:
            // testIdOrNameMatch    => `.//input[%testIdOrNameMatch%]`
            //
            // Replacements:
            // %testIdMatch%       => `./@id = %locator%`,
            // %testNameMatch%     => `@name = %locator%`,
            // %testIdOrNameMatch% => `(%testIdMatch% or %testNameMatch%)`,
            // %locator%           => `late-registered-input`
            //
            // Result:
            // .//input[%testIdOrNameMatch%]
            // .//input[(%testIdMatch% or %testNameMatch%)]
            // .//input[(./@id = %locator% or %testNameMatch%)]
            // .//input[./@id = %locator% or @name = %locator%]
            // .//input[./@id = 'late-registered-input' or @name = 'late-registered-input']
            //
            // Expectation:
            // Exactly one result will match with an id of `late-registered-input`.
            // Exactly one result will match with a name of `late-registered-input`.
            'lateSelectorUsingRecursiveLateReplacementMatchingBoth' => array(
                'file' => 'test.html',
                'replacements' => array(
                    '%testIdMatch%' => './@id = %locator%',
                    '%testNameMatch%' => '@name = %locator%',
                    '%testIdOrNameMatch%' => '(%testIdMatch% or %testNameMatch%)',
                ),
                'selectors' => array(
                    'testIdOrNameMatch' => './/input[%testIdOrNameMatch%]'
                ),
                'selector' => 'testIdOrNameMatch',
                'locator' => 'late-registered-input',
                'expectedExactCount' => 2,
                'expectedPartialCount' => 2
            ),
        );
    }

    /**
     * @return NamedSelector
     */
    abstract protected function getSelector();

    /**
     * @return bool
     */
    abstract protected function allowPartialMatch();
}
