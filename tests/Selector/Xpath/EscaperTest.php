<?php

namespace Behat\Mink\Tests\Selector\Xpath;

use Behat\Mink\Selector\Xpath\Escaper;
use PHPUnit\Framework\TestCase;

class EscaperTest extends TestCase
{
    /**
     * @dataProvider getXpathLiterals
     */
    public function testXpathLiteral($string, $expected)
    {
        $escaper = new Escaper();

        $this->assertEquals($expected, $escaper->escapeLiteral($string));
    }

    public function getXpathLiterals()
    {
        return array(
            array('some simple string', "'some simple string'"),
            array('some "d-brackets" string', "'some \"d-brackets\" string'"),
            array('some \'s-brackets\' string', "\"some 's-brackets' string\""),
            array(
                'some \'s-brackets\' and "d-brackets" string',
                'concat(\'some \',"\'",\'s-brackets\',"\'",\' and "d-brackets" string\')',
            ),
        );
    }
}
