<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Session;

class GoutteDriverTest extends \PHPUnit_Framework_TestCase
{
    private $session;

    public function setUp()
    {
        $this->host     = $_SERVER['WEB_FIXTURES_HOST'];
        $this->session  = new Session(new GoutteDriver(), new SelectorsHandler());
    }

    public function test200Status()
    {
        $this->session->visit($this->host . '/index.php');

        $this->assertEquals(200, $this->session->getStatusCode());
        $this->assertEquals($this->host . '/index.php', $this->session->getCurrentUrl());
    }

    public function test404Status()
    {
        $this->session->visit($this->host . '/404.php');

        $this->assertEquals($this->host . '/404.php', $this->session->getCurrentUrl());
        $this->assertEquals(404, $this->session->getStatusCode());
        $this->assertEquals('Sorry, page not found', $this->session->getPage()->getContent());
    }

    public function testRedirect()
    {
        $this->session->visit($this->host . '/redirector.php');

        $this->assertEquals($this->host . '/redirect_destination.php', $this->session->getCurrentUrl());
        $this->assertEquals(200, $this->session->getStatusCode());
        $this->assertEquals('You were redirected!', $this->session->getPage()->getContent());
    }

    public function testIndexPage()
    {
        $this->session->visit($this->host . '/index.php');

        $page = $this->session->getPage();

        $this->assertEquals('Extremely useless page', $page->find('css', 'h1')->getText());
        $this->assertEquals('h1', $page->find('css', 'h1')->getTagName());
        $this->assertEquals('pariatur', $page->find('css', 'p strong:last-child')->getText());
        $this->assertEquals('super-duper', $page->find('css', 'p strong:last-child')->getAttribute('class'));
        $this->assertTrue($page->find('css', 'p strong:last-child')->hasAttribute('class'));
        $this->assertEquals('veniam', $page->find('css', 'p strong:nth-child(2)')->getText());
        $this->assertEquals('strong', $page->find('css', 'p strong:nth-child(2)')->getTagName());
        $this->assertEquals(null, $page->find('css', 'p strong:nth-child(2)')->getAttribute('class'));
        $this->assertFalse($page->find('css', 'p strong:nth-child(2)')->hasAttribute('class'));

        $strongs = $page->findAll('css', 'p strong');
        $this->assertEquals(3, count($strongs));
        $this->assertEquals('Lorem', $strongs[0]->getText());
        $this->assertEquals('pariatur', $strongs[2]->getText());
    }

    public function testBasicForm()
    {
        $this->session->visit($this->host . '/basic_form.php');

        $page = $this->session->getPage();
        $this->assertEquals('Basic Form Page', $page->find('css', 'h1')->getText());

        $firstname  = $page->findField('first_name');
        $lastname   = $page->findField('lastn');

        $this->assertNotNull($firstname);
        $this->assertNotNull($lastname);

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());

        $firstname->setValue('Konstantin');
        $lastname->setValue('Kudryashov');

        $this->assertEquals('Konstantin', $firstname->getValue());
        $this->assertEquals('Kudryashov', $lastname->getValue());

        $page->findButton('Save')->click();

        $this->assertEquals('Anket for Konstantin', $page->find('css', 'h1')->getText());
        $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
        $this->assertEquals('Lastname: Kudryashov', $page->find('css', 'span:last-child')->getText());
    }

    public function testAdvancedForm()
    {
        $this->session->visit($this->host . '/advanced_form.php');

        $page = $this->session->getPage();
        $this->assertEquals('ADvanced Form Page', $page->find('css', 'h1')->getText());

        $firstname  = $page->findField('first_name');
        $lastname   = $page->findField('lastn');
        $email      = $page->findField('Your email:');
        $select     = $page->findField('select_number');
        $sex        = $page->findField('sex');
        $maillist   = $page->findField('mail_list');
        $agreement  = $page->findField('agreement');

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

        $button = $page->findButton('Register');

        $page->fillField('first_name', 'Foo');
        $page->fillField('last_name', 'Bar');

        $this->assertEquals('Foo', $firstname->getValue());
        $this->assertEquals('Bar', $lastname->getValue());

        $button->click();

        $this->assertEquals(<<<OUT
Array
(
    [first_name] => Foo
    [last_name] => Bar
    [email] => your@email.com
    [select_number] => 10
    [sex] => m
    [agreement] => 1
)

OUT
            , $page->getContent()
        );
    }
}
