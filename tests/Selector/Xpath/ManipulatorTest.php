<?php

namespace Behat\Mink\Tests\Selector\Xpath;

use Behat\Mink\Selector\Xpath\Manipulator;
use PHPUnit\Framework\TestCase;

class ManipulatorTest extends TestCase
{
    /**
     * @dataProvider getPrependedXpath
     */
    public function testPrepend(string $prefix, string $xpath, string $expectedXpath)
    {
        $manipulator = new Manipulator();

        $this->assertEquals($expectedXpath, $manipulator->prepend($xpath, $prefix));
    }

    public static function getPrependedXpath()
    {
        return array(
            'simple' => array(
                'some_xpath',
                'some_tag1',
                'some_xpath/some_tag1',
            ),
            'with slash' => array(
                'some_xpath',
                '/some_tag1',
                'some_xpath/some_tag1',
            ),
            'union' => array(
                'some_xpath',
                'some_tag1 | some_tag2',
                'some_xpath/some_tag1 | some_xpath/some_tag2',
            ),
            'wrapped union' => array(
                'some_xpath',
                '(some_tag1 | some_tag2)/some_child',
                '(some_xpath/some_tag1 | some_xpath/some_tag2)/some_child',
            ),
            'multiple wrapped union' => array(
                'some_xpath',
                '( ( some_tag1 | some_tag2)/some_child | some_tag3)/leaf',
                '( ( some_xpath/some_tag1 | some_xpath/some_tag2)/some_child | some_xpath/some_tag3)/leaf',
            ),
            'parent union' => array(
                'some_xpath | another_xpath',
                'some_tag1 | some_tag2',
                '(some_xpath | another_xpath)/some_tag1 | (some_xpath | another_xpath)/some_tag2',
            ),
            'complex condition' => array(
                'some_xpath',
                'some_tag1 | some_tag2[@foo = "bar|"] | some_tag3[foo | bar]',
                'some_xpath/some_tag1 | some_xpath/some_tag2[@foo = "bar|"] | some_xpath/some_tag3[foo | bar]',
            ),
            'multiline' => array(
                'some_xpath',
                "some_tag1 | some_tag2[@foo =\n 'bar|']\n | some_tag3[foo | bar]",
                "some_xpath/some_tag1 | some_xpath/some_tag2[@foo =\n 'bar|'] | some_xpath/some_tag3[foo | bar]",
            ),
            'containing pipe' => array(
                'some_xpath',
                "some_tag[(contains(normalize-space(string(.)), 'foo|bar') | other_tag[contains(./@some_attribute, 'foo|bar')])]",
                "some_xpath/some_tag[(contains(normalize-space(string(.)), 'foo|bar') | other_tag[contains(./@some_attribute, 'foo|bar')])]",
            ),
            // Invalid XPath queries should be handled gracefully to let the DOMQuery report a proper failure for them later
            'unclosed string literal single quote' => array(
                'some_xpath',
                "some_tag1 | some_tag2[@foo = 'bar]",
                "some_xpath/some_tag1 | some_tag2[@foo = 'bar]",
            ),
            'unclosed string literal double quote' => array(
                'some_xpath',
                'some_tag1 | some_tag2[@foo = "bar]',
                'some_xpath/some_tag1 | some_tag2[@foo = "bar]',
            ),
            'unclosed bracket' => array(
                'some_xpath',
                'some_tag1 | some_tag2[@foo = "bar"',
                'some_xpath/some_tag1 | some_tag2[@foo = "bar"',
            ),
        );
    }
}
