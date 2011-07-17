<?php

namespace Tests\Behat\Mink\Driver;

require_once 'DriverTest.php';

abstract class GeneralDriverTest extends DriverTest
{
    public function testRedirect()
    {
        static::$session->visit(static::$host . '/redirector.php');
        $this->assertEquals(static::$host . '/redirect_destination.php', static::$session->getCurrentUrl());
    }

    public function testCookie()
    {
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getText());
        $this->assertNull(static::$session->getCookie('srvr_cookie'));

        static::$session->setCookie('srvr_cookie', 'client cookie set');
        static::$session->reload();
        $this->assertContains('Previous cookie: client cookie set', static::$session->getPage()->getText());
        $this->assertEquals('client cookie set', static::$session->getCookie('srvr_cookie'));

        static::$session->setCookie('srvr_cookie', null);
        static::$session->reload();
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getText());

        static::$session->visit(static::$host . '/cookie_page1.php');
        static::$session->visit(static::$host . '/cookie_page2.php');

        $this->assertContains('Previous cookie: srv_var_is_set', static::$session->getPage()->getText());
        static::$session->setCookie('srvr_cookie', null);
        static::$session->reload();
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getText());
    }

    public function testReset()
    {
        static::$session->visit(static::$host . '/cookie_page1.php');
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: srv_var_is_set', static::$session->getPage()->getText());

        static::$session->reset();
        static::$session->visit(static::$host . '/cookie_page2.php');

        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getText());

        static::$session->setCookie('srvr_cookie', 'test_cookie');
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: test_cookie', static::$session->getPage()->getText());
        static::$session->reset();
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getText());

        static::$session->setCookie('client_cookie1', 'some_val');
        static::$session->setCookie('client_cookie2', 123);
        static::$session->visit(static::$host . '/session_test.php');
        static::$session->visit(static::$host . '/cookie_page1.php');

        static::$session->visit(static::$host . '/print_cookies.php');
        $this->assertContains(
            'Array ( [client_cookie1] => some_val [client_cookie2] => 123 [_SESS] =>',
            static::$session->getPage()->getText()
        );
        $this->assertContains(
            ' [srvr_cookie] => srv_var_is_set )', static::$session->getPage()->getText()
        );

        static::$session->reset();
        static::$session->visit(static::$host . '/print_cookies.php');
        $this->assertContains(
            'Array ( )', static::$session->getPage()->getText()
        );
    }

    public function testHttpOnlyCookieIsDeleted()
    {
        static::$session->restart();
        static::$session->visit(static::$host . '/cookie_page3.php');
        $this->assertEquals('Has Cookie: false', static::$session->getPage()->findById('cookie-status')->getText());

        static::$session->reload();
        $this->assertEquals('Has Cookie: true', static::$session->getPage()->findById('cookie-status')->getText());

        static::$session->restart();
        static::$session->visit(static::$host . '/cookie_page3.php');
        $this->assertEquals('Has Cookie: false', static::$session->getPage()->findById('cookie-status')->getText());
    }

    public function testSessionPersistsBetweenRequests()
    {
        static::$session->visit(static::$host . '/session_test.php');
        $this->assertNotNull($node = static::$session->getPage()->find('css', '#session-id'));
        $sessionId = $node->getText();

        static::$session->visit(static::$host . '/session_test.php');
        $this->assertNotNull($node = static::$session->getPage()->find('css', '#session-id'));
        $this->assertEquals($sessionId, $node->getText());

        static::$session->visit(static::$host . '/session_test.php?login');
        $this->assertNotNull($node = static::$session->getPage()->find('css', '#session-id'));
        $this->assertNotEquals($sessionId, $newSessionId = $node->getText());

        static::$session->visit(static::$host . '/session_test.php');
        $this->assertNotNull($node = static::$session->getPage()->find('css', '#session-id'));
        $this->assertEquals($newSessionId, $node->getText());
    }

    public function testPageControlls()
    {
        static::$session->visit(static::$host . '/randomizer.php');
        $number1 = static::$session->getPage()->find('css', '#number')->getText();

        static::$session->reload();
        $number2 = static::$session->getPage()->find('css', '#number')->getText();

        $this->assertNotEquals($number1, $number2);

        static::$session->visit(static::$host . '/links.php');
        static::$session->getPage()->clickLink('Random number page');

        $this->assertEquals(static::$host . '/randomizer.php', static::$session->getCurrentUrl());

        static::$session->back();
        $this->assertEquals(static::$host . '/links.php', static::$session->getCurrentUrl());

        static::$session->forward();
        $this->assertEquals(static::$host . '/randomizer.php', static::$session->getCurrentUrl());
    }

    public function testElementsTraversing()
    {
        static::$session->visit(static::$host . '/index.php');

        $page = static::$session->getPage();

        $this->assertNotNull($page->find('css', 'h1'));
        $this->assertEquals('Extremely useless page', $page->find('css', 'h1')->getText());
        $this->assertEquals('h1', $page->find('css', 'h1')->getTagName());

        $this->assertNotNull($page->find('xpath', '//p/strong[3]'));
        $this->assertEquals('pariatur', $page->find('xpath', '//p/strong[3]')->getText());
        $this->assertEquals('super-duper', $page->find('xpath', '//p/strong[3]')->getAttribute('class'));
        $this->assertTrue($page->find('xpath', '//p/strong[3]')->hasAttribute('class'));

        $this->assertNotNull($page->find('xpath', '//p/strong[2]'));
        $this->assertEquals('veniam', $page->find('xpath', '//p/strong[2]')->getText());
        $this->assertEquals('strong', $page->find('xpath', '//p/strong[2]')->getTagName());
        $this->assertNull($page->find('xpath', '//p/strong[2]')->getAttribute('class'));
        $this->assertFalse($page->find('xpath', '//p/strong[2]')->hasAttribute('class'));

        $strongs = $page->findAll('css', 'p strong');
        $this->assertEquals(3, count($strongs));
        $this->assertEquals('Lorem', $strongs[0]->getText());
        $this->assertEquals('pariatur', $strongs[2]->getText());

        $element = $page->find('css', '#some-element');

        $this->assertEquals('some very interesting text', $element->getText());
        $this->assertEquals(
            "\n            some <div>very\n            </div>\n".
            "<em>interesting</em>      text\n        ",
            $element->getHtml()
        );

        $this->assertTrue($element->hasAttribute('data-href'));
        $this->assertFalse($element->hasAttribute('data-url'));
        $this->assertEquals('http://mink.behat.org', $element->getAttribute('data-href'));
        $this->assertNull($element->getAttribute('data-url'));
        $this->assertEquals('div', $element->getTagName());
    }

    public function testDeepTraversing()
    {
        static::$session->visit(static::$host . '/index.php');

        $traversDiv = static::$session->getPage()->findAll('css', 'div.travers');

        $this->assertEquals(1, count($traversDiv));
        $traversDiv = $traversDiv[0];

        $subDivs = $traversDiv->findAll('css', 'div.sub');
        $this->assertEquals(3, count($subDivs));

        $this->assertTrue($subDivs[2]->hasLink('some deep url'));
        $this->assertFalse($subDivs[2]->hasLink('come deep url'));
        $subUrl = $subDivs[2]->findLink('some deep url');
        $this->assertNotNull($subUrl);

        $this->assertEquals('some_url', $subUrl->getAttribute('href'));
        $this->assertEquals('some deep url', $subUrl->getText());
        $this->assertEquals('some <strong>deep</strong> url', $subUrl->getHtml());

        $this->assertTrue($subUrl->has('css', 'strong'));
        $this->assertFalse($subUrl->has('css', 'em'));
        $this->assertEquals('deep', $subUrl->find('css', 'strong')->getText());
    }

    public function testLinks()
    {
        static::$session->visit(static::$host . '/links.php');
        $page = static::$session->getPage();
        $link = $page->findLink('Redirect me to');

        $this->assertEquals('redirector.php', $link->getAttribute('href'));
        $link->click();

        $this->assertEquals(static::$host . '/redirect_destination.php', static::$session->getCurrentUrl());

        static::$session->visit(static::$host . '/links.php');
        $page = static::$session->getPage();
        $link = $page->findLink('basic form image');

        $this->assertEquals('/basic_form.php', $link->getAttribute('href'));
        $link->click();

        $this->assertEquals(static::$host . '/basic_form.php', static::$session->getCurrentUrl());
    }

    public function testBasicForm()
    {
        static::$session->visit(static::$host . '/basic_form.php');

        $page = static::$session->getPage();
        $this->assertEquals('Basic Form Page', $page->find('css', 'h1')->getText());

        $firstname  = $page->findField('first_name');
        $lastname   = $page->findField('lastn');

        $this->assertNotNull($firstname);
        $this->assertNotNull($lastname);

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());

        $firstname->setValue('Konstantin');
        $page->fillField('last_name', 'Kudryashov');

        $this->assertEquals('Konstantin', $firstname->getValue());
        $this->assertEquals('Kudryashov', $lastname->getValue());

        $page->findButton('Save')->click();

        $this->assertEquals('Anket for Konstantin', $page->find('css', 'h1')->getText());
        $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
        $this->assertEquals('Lastname: Kudryashov', $page->find('css', '#last')->getText());
    }

    public function testAdvancedForm()
    {
        static::$session->visit(static::$host . '/advanced_form.php');

        $page = static::$session->getPage();
        $this->assertEquals('ADvanced Form Page', $page->find('css', 'h1')->getText());

        $firstname  = $page->findField('first_name');
        $lastname   = $page->findField('lastn');
        $email      = $page->findField('Your email:');
        $select     = $page->findField('select_number');
        $sex        = $page->findField('sex');
        $maillist   = $page->findField('mail_list');
        $agreement  = $page->findField('agreement');
        $about      = $page->findField('about');

        $this->assertNotNull($firstname);
        $this->assertNotNull($lastname);
        $this->assertNotNull($email);
        $this->assertNotNull($select);
        $this->assertNotNull($sex);
        $this->assertNotNull($maillist);
        $this->assertNotNull($agreement);

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());
        $this->assertEquals('your@email.com', $email->getValue());
        $this->assertEquals('20', $select->getValue());
        $this->assertEquals('w', $sex->getValue());

        $this->assertTrue($maillist->getValue());
        $this->assertFalse($agreement->getValue());

        $this->assertTrue($maillist->isChecked());
        $this->assertFalse($agreement->isChecked());

        $agreement->check();
        $this->assertTrue($agreement->isChecked());

        $maillist->uncheck();
        $this->assertFalse($maillist->isChecked());

        $select->selectOption('10');
        $this->assertEquals('10', $select->getValue());

        $sex->selectOption('m');
        $this->assertEquals('m', $sex->getValue());
        $about->attachFile(__DIR__ . '/web-fixtures/some_file.txt');

        $button = $page->findButton('Register');

        $page->fillField('first_name', 'Foo "item"');
        $page->fillField('last_name', 'Bar');

        $this->assertEquals('Foo "item"', $firstname->getValue());
        $this->assertEquals('Bar', $lastname->getValue());

        $button->click();

        $this->assertContains(<<<OUT
Array
(
    [first_name] => Foo "item"
    [last_name] => Bar
    [email] => your@email.com
    [select_number] => 10
    [sex] => m
    [agreement] => on
)
1 uploaded file
OUT
            , $page->getContent()
        );
    }
}