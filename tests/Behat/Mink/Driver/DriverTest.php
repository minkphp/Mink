<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Selector\SelectorsHandler,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Session;

abstract class DriverTest extends \PHPUnit_Framework_TestCase
{
    protected static $host;
    protected static $session;

    public static function setUpBeforeClass()
    {
        static::$host    = $_SERVER['WEB_FIXTURES_HOST'];
        static::$session = new Session(static::configureDriver(), new SelectorsHandler());
        static::$session->start();
        static::$session->visit(static::$host . '/index.php');
    }

    public static function tearDownAfterClass()
    {
        static::$session->stop();
    }

    public function setUp()
    {
        static::$session->reset();
    }

    protected static function configureDriver() {}

    public function testRedirect()
    {
        static::$session->visit(static::$host . '/redirector.php');
        $this->assertEquals(static::$host . '/redirect_destination.php', static::$session->getCurrentUrl());
    }

    public function testCookie()
    {
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getPlainText());

        static::$session->visit(static::$host . '/cookie_page1.php');
        static::$session->visit(static::$host . '/cookie_page2.php');
        $this->assertContains('Previous cookie: srv_var_is_set', static::$session->getPage()->getPlainText());
        static::$session->reload();
        $this->assertContains('Previous cookie: NO', static::$session->getPage()->getPlainText());
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

    public function testIndexPage()
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
        $lastname->setValue('Kudryashov');

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
