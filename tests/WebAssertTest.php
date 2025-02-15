<?php

namespace Behat\Mink\Tests;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;
use Behat\Mink\Tests\Helper\Stringer;
use Behat\Mink\WebAssert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebAssertTest extends TestCase
{
    /**
     * @var Session&MockObject
     */
    private $session;
    /**
     * @var WebAssert
     */
    private $assert;

    /**
     * @before
     */
    public function prepareSession(): void
    {
        $this->session = $this->getMockBuilder('Behat\\Mink\\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->session->expects($this->any())
            ->method('getDriver')
            ->will($this->returnValue($this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock()));

        $this->assert = new WebAssert($this->session);
    }

    public function testAddressEquals()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://example.com/script.php/sub/url?param=true#webapp/nav'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressEquals('/sub/url#webapp/nav');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->addressEquals('sub_url');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current page is "/sub/url#webapp/nav", but "sub_url" expected.'
        );
    }

    public function testAddressEqualsEmptyPath()
    {
        $this->session
            ->expects($this->once())
            ->method('getCurrentUrl')
            ->willReturn('http://example.com')
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressEquals('/');
        });
    }

    public function testAddressEqualsEndingInScript()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://example.com/script.php'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressEquals('/script.php');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->addressEquals('/');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current page is "/script.php", but "/" expected.'
        );
    }

    public function testAddressNotEquals()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://example.com/script.php/sub/url'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressNotEquals('sub_url');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->addressNotEquals('/sub/url');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current page is "/sub/url", but should not be.'
        );
    }

    public function testAddressNotEqualsEndingInScript()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://example.com/script.php'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressNotEquals('/');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->addressNotEquals('/script.php');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current page is "/script.php", but should not be.'
        );
    }

    public function testAddressMatches()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://example.com/script.php/sub/url'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->addressMatches('/su.*rl/');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->addressMatches('/suburl/');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current page "/sub/url" does not match the regex "/suburl/".'
        );
    }

    public function testCookieEquals()
    {
        $this->session->
            expects($this->any())->
            method('getCookie')->
            will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->cookieEquals('foo', 'bar');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->cookieEquals('bar', 'foo');
            },
            'Behat\Mink\Exception\ExpectationException',
            'Cookie "bar" value is "baz", but should be "foo".'
        );
    }

    public function testCookieExists()
    {
        $this->session->
            expects($this->any())->
            method('getCookie')->
            will($this->returnValueMap(
                array(
                    array('foo', '1'),
                    array('bar', null),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->cookieExists('foo');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->cookieExists('bar');
            },
            'Behat\Mink\Exception\ExpectationException',
            'Cookie "bar" is not set, but should be.'
        );
    }

    public function testStatusCodeEquals()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(200))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->statusCodeEquals(200);
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->statusCodeEquals(404);
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current response status code is 200, but 404 expected.'
        );
    }

    public function testStatusCodeNotEquals()
    {
        $this->session
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(404))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->statusCodeNotEquals(200);
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->statusCodeNotEquals(404);
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current response status code is 404, but should not be.'
        );
    }

    public function testResponseHeaderEquals()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderEquals('foo', 'bar');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderEquals('bar', 'foo');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current response header "bar" is "baz", but "foo" expected.'
        );
    }

    public function testResponseHeaderNotEquals()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderNotEquals('foo', 'baz');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderNotEquals('bar', 'baz');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Current response header "bar" is "baz", but should not be.'
        );
    }

    public function testResponseHeaderContains()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderContains('foo', 'ba');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderContains('bar', 'bz');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "bz" was not found anywhere in the "bar" response header.'
        );
    }

    public function testResponseHeaderNotContains()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderNotContains('foo', 'bz');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderNotContains('bar', 'ba');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "ba" was found in the "bar" response header, but it should not.'
        );
    }

    public function testResponseHeaderContainsObjectWithToString()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
              array(
                array('foo', 'bar'),
                array('bar', 'baz'),
              )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderContains('foo', new Stringer('ba'));
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderContains('bar', 'bz');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "bz" was not found anywhere in the "bar" response header.'
        );
    }

    public function testResponseHeaderNotContainsObjectWithToString()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will(
                $this->returnValueMap(
                    array(
                        array('foo', 'bar'),
                        array('bar', 'baz'),
                    )
                )
            );

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderNotContains('foo', new Stringer('bz'));
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderNotContains('bar', 'ba');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "ba" was found in the "bar" response header, but it should not.'
        );
    }

    public function testResponseHeaderMatches()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderMatches('foo', '/ba(.*)/');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderMatches('bar', '/b[^a]/');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The pattern "/b[^a]/" was not found anywhere in the "bar" response header.'
        );
    }

    public function testResponseHeaderNotMatches()
    {
        $this->session
            ->expects($this->any())
            ->method('getResponseHeader')
            ->will($this->returnValueMap(
                array(
                    array('foo', 'bar'),
                    array('bar', 'baz'),
                )
            ));

        $this->assertCorrectAssertion(function () {
            $this->assert->responseHeaderNotMatches('foo', '/bz/');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseHeaderNotMatches('bar', '/b[ab]z/');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The pattern "/b[ab]z/" was found in the text of the "bar" response header, but it should not.'
        );
    }

    public function testPageTextContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue("Some  page\n\ttext"))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->pageTextContains('PAGE text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->pageTextContains('html text');
            },
            'Behat\\Mink\\Exception\\ResponseTextException',
            'The text "html text" was not found anywhere in the text of the current page.'
        );
    }

    public function testPageTextNotContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue("Some  html\n\ttext"))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->pageTextNotContains('PAGE text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->pageTextNotContains('HTML text');
            },
            'Behat\\Mink\\Exception\\ResponseTextException',
            'The text "HTML text" appears in the text of this page, but it should not.'
        );
    }

    public function testPageTextMatches()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue('Some page text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->pageTextMatches('/PA.E/i');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->pageTextMatches('/html/');
            },
            'Behat\\Mink\\Exception\\ResponseTextException',
            'The pattern /html/ was not found anywhere in the text of the current page.'
        );
    }

    public function testPageTextNotMatches()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue('Some html text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->pageTextNotMatches('/PA.E/i');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->pageTextNotMatches('/HTML/i');
            },
            'Behat\\Mink\\Exception\\ResponseTextException',
            'The pattern /HTML/i was found in the text of the current page, but it should not.'
        );
    }

    public function testResponseContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some page text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseContains('PAGE text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseContains('html text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "html text" was not found anywhere in the HTML response of the current page.'
        );
    }

    public function testResponseNotContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some html text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseNotContains('PAGE text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseNotContains('HTML text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "HTML text" appears in the HTML response of this page, but it should not.'
        );
    }

    public function testResponseContainsObjectWithToString()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some page text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseContains(new Stringer('PAGE text'));
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseContains('html text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "html text" was not found anywhere in the HTML response of the current page.'
        );
    }

    public function testResponseNotContainsObjectWithToString()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some html text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseNotContains(new Stringer('PAGE text'));
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseNotContains('HTML text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "HTML text" appears in the HTML response of this page, but it should not.'
        );
    }

    public function testResponseContainsCount()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some page text with page in it twice'))
        ;

        $this->assertCorrectAssertion('responseContainsCount', array('page', 2));
        $this->assertWrongAssertion(
            'responseContainsCount',
            array('page', 3),
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "page" was not found the expected 3 times in the HTML response of the current page.'
        );
    }

    public function testResponseMatches()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some page text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseMatches('/PA.E/i');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseMatches('/html/');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The pattern /html/ was not found anywhere in the HTML response of the page.'
        );
    }

    public function testResponseNotMatches()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('getContent')
            ->will($this->returnValue('Some html text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->responseNotMatches('/PA.E/i');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->responseNotMatches('/HTML/i');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The pattern /HTML/i was found in the HTML response of the page, but it should not.'
        );
    }

    public function testElementsCount()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('findAll')
            ->with('css', 'h2 > span')
            ->will($this->returnValue(array(1, 2)))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementsCount('css', 'h2 > span', 2);
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementsCount('css', 'h2 > span', 3);
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            '2 elements matching css "h2 > span" found on the page, but should be 3.'
        );
    }

    public function testElementExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(4))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->onConsecutiveCalls(1, null, 1, null))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementExists('css', 'h2 > span');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementExists('css', 'h2 > span');
            },
            'Behat\\Mink\\Exception\\ElementNotFoundException',
            'Element matching css "h2 > span" not found.'
        );

        $this->assertCorrectAssertion(function () use ($page) {
            $this->assert->elementExists('css', 'h2 > span', $page);
        });
        $this->assertWrongAssertion(
            function () use ($page) {
                $this->assert->elementExists('css', 'h2 > span', $page);
            },
            'Behat\\Mink\\Exception\\ElementNotFoundException',
            'Element matching css "h2 > span" not found.'
        );
    }

    public function testElementExistsWithArrayLocator()
    {
        $container = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session->expects($this->never())
            ->method('getPage')
        ;

        $container
            ->expects($this->exactly(2))
            ->method('find')
            ->with('named', array('element', 'Test'))
            ->will($this->onConsecutiveCalls(1, null))
        ;

        $this->assertCorrectAssertion(function () use ($container) {
            $this->assert->elementExists('named', array('element', 'Test'), $container);
        });
        $this->assertWrongAssertion(
            function () use ($container) {
                $this->assert->elementExists('named', array('element', 'Test'), $container);
            },
            'Behat\\Mink\\Exception\\ElementNotFoundException',
            'Element with named "element Test" not found.'
        );
    }

    public function testElementNotExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(4))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->onConsecutiveCalls(null, 1, null, 1))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementNotExists('css', 'h2 > span');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementNotExists('css', 'h2 > span');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'An element matching css "h2 > span" appears on this page, but it should not.'
        );

        $this->assertCorrectAssertion(function () use ($page) {
            $this->assert->elementNotExists('css', 'h2 > span', $page);
        });
        $this->assertWrongAssertion(
            function () use ($page) {
                $this->assert->elementNotExists('css', 'h2 > span', $page);
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'An element matching css "h2 > span" appears on this page, but it should not.'
        );
    }

    /**
     * @dataProvider getArrayLocatorFormats
     */
    public function testElementNotExistsArrayLocator(string $selector, array $locator, string $expectedMessage)
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->once())
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->once())
            ->method('find')
            ->with($selector, $locator)
            ->will($this->returnValue(1))
        ;

        $this->assertWrongAssertion(
            function () use ($selector, $locator) {
                $this->assert->elementNotExists($selector, $locator);
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            $expectedMessage
        );
    }

    public function getArrayLocatorFormats()
    {
        return array(
            'named' => array(
                'named',
                array('button', 'Test'),
                'An button matching locator "Test" appears on this page, but it should not.',
            ),
            'custom' => array(
                'custom',
                array('test', 'foo'),
                'An element matching custom "test foo" appears on this page, but it should not.',
            ),
        );
    }

    public function testElementTextContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue('element text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementTextContains('css', 'h2 > span', 'text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementTextContains('css', 'h2 > span', 'html');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "html" was not found in the text of the element matching css "h2 > span".'
        );
    }

    public function testElementTextNotContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getText')
            ->will($this->returnValue('element text'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementTextNotContains('css', 'h2 > span', 'html');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementTextNotContains('css', 'h2 > span', 'text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The text "text" appears in the text of the element matching css "h2 > span", but it should not.'
        );
    }

    public function testElementContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getHtml')
            ->will($this->returnValue('element html'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementContains('css', 'h2 > span', 'html');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementContains('css', 'h2 > span', 'text');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "text" was not found in the HTML of the element matching css "h2 > span".'
        );
    }

    public function testElementNotContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getHtml')
            ->will($this->returnValue('element html'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementNotContains('css', 'h2 > span', 'text');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementNotContains('css', 'h2 > span', 'html');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The string "html" appears in the HTML of the element matching css "h2 > span", but it should not.'
        );
    }

    public function testElementAttributeContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('hasAttribute')
            ->will($this->returnValue(true))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('name')
            ->will($this->returnValue('foo'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementAttributeContains('css', 'h2 > span', 'name', 'foo');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementAttributeContains('css', 'h2 > span', 'name', 'bar');
            },
            'Behat\\Mink\\Exception\\ElementHtmlException',
            'The text "bar" was not found in the attribute "name" of the element matching css "h2 > span".'
        );
    }

    public function testElementAttributeExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->method('hasAttribute')
            ->with('name')
            ->will($this->onConsecutiveCalls(true, false))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementAttributeExists('css', 'h2 > span', 'name');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementAttributeExists('css', 'h2 > span', 'name');
            },
            'Behat\\Mink\\Exception\\ElementHtmlException',
            'The attribute "name" was not found in the element matching css "h2 > span".'
        );
    }

    public function testElementAttributeNotExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->method('hasAttribute')
            ->with('name')
            ->will($this->onConsecutiveCalls(false, true))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementAttributeNotExists('css', 'h2 > span', 'name');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementAttributeNotExists('css', 'h2 > span', 'name');
            },
            'Behat\\Mink\\Exception\\ElementHtmlException',
            'The attribute "name" was found in the element matching css "h2 > span".'
        );
    }

    public function testElementAttributeNotContains()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('find')
            ->with('css', 'h2 > span')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('hasAttribute')
            ->will($this->returnValue(true))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('name')
            ->will($this->returnValue('foo'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->elementAttributeNotContains('css', 'h2 > span', 'name', 'bar');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->elementAttributeNotContains('css', 'h2 > span', 'name', 'foo');
            },
            'Behat\\Mink\\Exception\\ElementHtmlException',
            'The text "foo" was found in the attribute "name" of the element matching css "h2 > span".'
        );
    }

    public function testFieldExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('findField')
            ->with('username')
            ->will($this->onConsecutiveCalls($element, null))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->fieldExists('username');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldExists('username');
            },
            'Behat\\Mink\\Exception\\ElementNotFoundException',
            'Form field with id|name|label|value "username" not found.'
        );
    }

    public function testFieldNotExists()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('findField')
            ->with('username')
            ->will($this->onConsecutiveCalls(null, $element))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->fieldNotExists('username');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldNotExists('username');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'A field "username" appears on this page, but it should not.'
        );
    }

    public function testFieldValueEquals()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(4))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(4))
            ->method('findField')
            ->with('username')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(4))
            ->method('getValue')
            ->will($this->returnValue('234'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->fieldValueEquals('username', '234');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldValueEquals('username', '235');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The field "username" value is "234", but "235" expected.'
        );
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldValueEquals('username', '23');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The field "username" value is "234", but "23" expected.'
        );
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldValueEquals('username', '');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The field "username" value is "234", but "" expected.'
        );
    }

    public function testFieldValueEqualsBadUsage()
    {
        $page = $this->createMock(DocumentElement::class);

        $element = $this->createMock(NodeElement::class);

        $this->session
            ->expects($this->once())
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->once())
            ->method('findField')
            ->with('username')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(array('235')))
        ;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Field value assertions cannot be used for multi-select fields as they have multiple values.');

        $this->assert->fieldValueEquals('username', '234');
    }

    public function testFieldValueNotEquals()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(4))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(4))
            ->method('findField')
            ->with('username')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(4))
            ->method('getValue')
            ->will($this->returnValue('235'))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->fieldValueNotEquals('username', '234');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->fieldValueNotEquals('username', '235');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'The field "username" value is "235", but it should not be.'
        );
        $this->assertCorrectAssertion(function () {
            $this->assert->fieldValueNotEquals('username', '23');
        });
        $this->assertCorrectAssertion(function () {
            $this->assert->fieldValueNotEquals('username', '');
        });
    }

    public function testFieldValueNotEqualsBadUsage()
    {
        $page = $this->createMock(DocumentElement::class);

        $element = $this->createMock(NodeElement::class);

        $this->session
            ->expects($this->once())
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->once())
            ->method('findField')
            ->with('username')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(array('235')))
        ;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Field value assertions cannot be used for multi-select fields as they have multiple values.');

        $this->assert->fieldValueNotEquals('username', '234');
    }

    public function testCheckboxChecked()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('findField')
            ->with('remember_me')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->checkboxChecked('remember_me');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->checkboxChecked('remember_me');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Checkbox "remember_me" is not checked, but it should be.'
        );
    }

    public function testCheckboxNotChecked()
    {
        $page = $this->getMockBuilder('Behat\\Mink\\Element\\DocumentElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $element = $this->getMockBuilder('Behat\\Mink\\Element\\NodeElement')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->session
            ->expects($this->exactly(2))
            ->method('getPage')
            ->will($this->returnValue($page))
        ;

        $page
            ->expects($this->exactly(2))
            ->method('findField')
            ->with('remember_me')
            ->will($this->returnValue($element))
        ;

        $element
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(false, true))
        ;

        $this->assertCorrectAssertion(function () {
            $this->assert->checkboxNotChecked('remember_me');
        });
        $this->assertWrongAssertion(
            function () {
                $this->assert->checkboxNotChecked('remember_me');
            },
            'Behat\\Mink\\Exception\\ExpectationException',
            'Checkbox "remember_me" is checked, but it should not be.'
        );
    }

    /**
     * @param callable(): void $callback
     */
    private function assertCorrectAssertion(callable $callback): void
    {
        try {
            $callback();
        } catch (ExpectationException $e) {
            $this->fail('Correct assertion should not throw an exception: '.$e->getMessage());
        }
    }

    /**
     * @param callable(): void $callback
     */
    private function assertWrongAssertion(callable $callback, string $exceptionClass, string $exceptionMessage): void
    {
        if ('Behat\Mink\Exception\ExpectationException' !== $exceptionClass && !is_subclass_of($exceptionClass, 'Behat\Mink\Exception\ExpectationException')) {
            throw new \LogicException('Wrong expected exception for the failed assertion. It should be a Behat\Mink\Exception\ExpectationException.');
        }

        try {
            $callback();
            $this->fail('Wrong assertion should throw an exception');
        } catch (ExpectationException $e) {
            $this->assertInstanceOf($exceptionClass, $e);
            $this->assertSame($exceptionMessage, $e->getMessage());
        }
    }
}
