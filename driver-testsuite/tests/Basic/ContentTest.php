<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class ContentTest extends TestCase
{
    public function testOuterHtml()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $page = $this->getSession()->getPage();
        $element = $page->find('css', '.travers');

        $this->assertNotNull($element);

        $this->assertEquals(
            "<div class=\"travers\">\n            <div class=\"sub\">el1</div>\n".
            "            <div class=\"sub\">el2</div>\n            <div class=\"sub\">\n".
            "                <a href=\"some_url\">some <strong>deep</strong> url</a>\n".
            "            </div>\n        </div>",
            $element->getOuterHtml()
        );
    }

    /**
     * @dataProvider getAttributeDataProvider
     */
    public function testGetAttribute($attributeName, $attributeValue)
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $element = $this->getSession()->getPage()->findById('attr-elem[' . $attributeName . ']');

        $this->assertNotNull($element);
        $this->assertSame($attributeValue, $element->getAttribute($attributeName));
    }

    public function getAttributeDataProvider()
    {
        return array(
            array('with-value', 'some-value'),
            array('without-value', ''),
            array('with-empty-value', ''),
            array('with-missing', null),
        );
    }

    public function testJson()
    {
        $this->getSession()->visit($this->pathTo('/json.php'));
        $this->assertContains(
            '{"key1":"val1","key2":234,"key3":[1,2,3]}',
            $this->getSession()->getPage()->getContent()
        );
    }

    public function testHtmlDecodingNotPerformed()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/html_decoding.html'));
        $page = $session->getPage();

        $span = $page->find('css', 'span');
        $input = $page->find('css', 'input');

        $this->assertNotNull($span);
        $this->assertNotNull($input);

        $expectedHtml = '<span custom-attr="&amp;">some text</span>';
        $this->assertContains($expectedHtml, $page->getHtml(), '.innerHTML is returned as-is');
        $this->assertContains($expectedHtml, $page->getContent(), '.outerHTML is returned as-is');

        $this->assertEquals('&', $span->getAttribute('custom-attr'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getAttribute('value'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getValue(), 'node value is decoded');
    }

    public function testGetContentReturnsHTMLFromServer()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/html_decoding.html'));

        $webFixtureContent = file_get_contents(__DIR__ . '/web-fixtures/html_decoding.html');
        $this->assertEquals($webFixtureContent, $session->getPage()->getContent());
    }
}
