<?php

namespace Tests\Behat\Mink\Selector\Xpath;

use Behat\Mink\Selector\Xpath\Manipulator;

/**
 * @group unittest
 */
class ManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getPrependedXpath
     */
    public function testPrepend($prefix, $xpath, $expectedXpath)
    {
        $manipulator = new Manipulator();

        $this->assertEquals($expectedXpath, $manipulator->prepend($xpath, $prefix));
    }

    public function getPrependedXpath()
    {
        return array(
            'simple' => array(
                'some_xpath',
                'some_tag1',
                'some_xpath/some_tag1',
            ),
            'union' => array(
                'some_xpath',
                'some_tag1 | some_tag2',
                'some_xpath/some_tag1 | some_xpath/some_tag2',
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
                "some_tag1 | some_tag2[@foo =\n 'bar|'']\n | some_tag3[foo | bar]",
                "some_xpath/some_tag1 | some_xpath/some_tag2[@foo =\n 'bar|''] | some_xpath/some_tag3[foo | bar]",
            ),
        );
    }
}
