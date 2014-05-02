<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Mink;
use Behat\Mink\Session;

abstract class GeneralDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mink session manager.
     *
     * @var Mink
     */
    private static $mink;

    /**
     * Initializes mink instance.
     */
    public static function setUpBeforeClass()
    {
        self::$mink = new Mink(array('sess' => new Session(static::getDriver())));
    }

    /**
     * Creates driver instance.
     *
     * @return DriverInterface
     * @throws \RuntimeException
     */
    protected static function getDriver()
    {
        throw new \RuntimeException('Please implement "getDriver" method in your test case first');
    }

    /**
     * Returns session.
     *
     * @return Session
     */
    public function getSession()
    {
        return self::$mink->getSession('sess');
    }

    protected function tearDown()
    {
        self::$mink->resetSessions();
    }

    protected function onNotSuccessfulTest(\Exception $e)
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }

    public function testRedirect()
    {
        $this->getSession()->visit($this->pathTo('/redirector.php'));
        $this->assertEquals($this->pathTo('/redirect_destination.php'), $this->getSession()->getCurrentUrl());
    }

    /**
     * @group issue130
     */
    public function testIssue130()
    {
        $this->getSession()->visit($this->pathTo('/issue130.php?p=1'));
        $page = $this->getSession()->getPage();

        $page->clickLink('Go to 2');
        $this->assertEquals($this->pathTo('/issue130.php?p=1'), $page->getText());
    }

    /**
     * @group issue131
     */
    public function testIssue131()
    {
        $this->getSession()->visit($this->pathTo('/issue131.php'));
        $page = $this->getSession()->getPage();

        $page->selectFieldOption('foobar', 'Gimme some accentués characters');

        $this->assertEquals('1', $page->findField('foobar')->getValue());
    }

    /**
     * @group issue140
     */
    public function testIssue140()
    {
        $this->getSession()->visit($this->pathTo('/issue140.php'));

        $this->getSession()->getPage()->fillField('cookie_value', 'some:value;');
        $this->getSession()->getPage()->pressButton('Set cookie');

        $this->getSession()->visit($this->pathTo('/issue140.php?show_value'));
        $this->assertEquals('some:value;', $this->getSession()->getCookie('tc'));
        $this->assertEquals('some:value;', $this->getSession()->getPage()->getText());
    }

    /**
     * @group issue211
     */
    public function testIssue211()
    {
        $this->getSession()->visit($this->pathTo('/issue211.php'));
        $field = $this->getSession()->getPage()->findField('Téléphone');

        $this->assertNotNull($field);
    }

    public function testIssue212()
    {
        $session = $this->getSession();

        $session->visit($this->pathTo('/issue212.php'));
        $page = $session->getPage();

        $field = $page->findById('poney-button');
        $this->assertNotNull($field);
        $this->assertEquals('poney', $field->getValue());
    }

    public function testCookie()
    {
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));
        $this->assertContains('Previous cookie: NO', $this->getSession()->getPage()->getText());
        $this->assertNull($this->getSession()->getCookie('srvr_cookie'));

        $this->getSession()->setCookie('srvr_cookie', 'client cookie set');
        $this->getSession()->reload();
        $this->assertContains('Previous cookie: client cookie set', $this->getSession()->getPage()->getText());
        $this->assertEquals('client cookie set', $this->getSession()->getCookie('srvr_cookie'));

        $this->getSession()->setCookie('srvr_cookie', null);
        $this->getSession()->reload();
        $this->assertContains('Previous cookie: NO', $this->getSession()->getPage()->getText());

        $this->getSession()->visit($this->pathTo('/cookie_page1.php'));
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));

        $this->assertContains('Previous cookie: srv_var_is_set', $this->getSession()->getPage()->getText());
        $this->getSession()->setCookie('srvr_cookie', null);
        $this->getSession()->reload();
        $this->assertContains('Previous cookie: NO', $this->getSession()->getPage()->getText());
    }

    /**
     * @dataProvider cookieWithPathsDataProvider
     */
    public function testCookieWithPaths($cookieRemovalMode)
    {
        // start clean
        $session = $this->getSession();
        $session->visit($this->pathTo('/sub-folder/cookie_page2.php'));
        $this->assertContains('Previous cookie: NO', $session->getPage()->getText());

        // cookie from root path is accessible in sub-folder
        $session->visit($this->pathTo('/cookie_page1.php'));
        $session->visit($this->pathTo('/sub-folder/cookie_page2.php'));
        $this->assertContains('Previous cookie: srv_var_is_set', $session->getPage()->getText());

        // cookie from sub-folder overrides cookie from root path
        $session->visit($this->pathTo('/sub-folder/cookie_page1.php'));
        $session->visit($this->pathTo('/sub-folder/cookie_page2.php'));
        $this->assertContains('Previous cookie: srv_var_is_set_sub_folder', $session->getPage()->getText());

        if ($cookieRemovalMode == 'session_reset') {
            $session->reset();
        } elseif ($cookieRemovalMode == 'cookie_delete') {
            $session->setCookie('srvr_cookie', null);
        }

        // cookie is removed from all paths
        $session->visit($this->pathTo('/sub-folder/cookie_page2.php'));
        $this->assertContains('Previous cookie: NO', $session->getPage()->getText());
    }

    public function cookieWithPathsDataProvider()
    {
        return array(
            array('session_reset'),
            array('cookie_delete'),
        );
    }

    public function testReset()
    {
        $this->getSession()->visit($this->pathTo('/cookie_page1.php'));
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));
        $this->assertContains('Previous cookie: srv_var_is_set', $this->getSession()->getPage()->getText());

        $this->getSession()->reset();
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));

        $this->assertContains('Previous cookie: NO', $this->getSession()->getPage()->getText());

        $this->getSession()->setCookie('srvr_cookie', 'test_cookie');
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));
        $this->assertContains('Previous cookie: test_cookie', $this->getSession()->getPage()->getText());
        $this->getSession()->reset();
        $this->getSession()->visit($this->pathTo('/cookie_page2.php'));
        $this->assertContains('Previous cookie: NO', $this->getSession()->getPage()->getText());

        $this->getSession()->setCookie('client_cookie1', 'some_val');
        $this->getSession()->setCookie('client_cookie2', 123);
        $this->getSession()->visit($this->pathTo('/session_test.php'));
        $this->getSession()->visit($this->pathTo('/cookie_page1.php'));

        $this->getSession()->visit($this->pathTo('/print_cookies.php'));
        $this->assertContains(
            "'client_cookie1' = 'some_val'",
            $this->getSession()->getPage()->getText()
        );
        $this->assertContains(
            "'client_cookie2' = '123'",
            $this->getSession()->getPage()->getText()
        );
        $this->assertContains(
            "_SESS' = ",
            $this->getSession()->getPage()->getText()
        );
        $this->assertContains(
            " 'srvr_cookie' = 'srv_var_is_set'",
            $this->getSession()->getPage()->getText()
        );

        $this->getSession()->reset();
        $this->getSession()->visit($this->pathTo('/print_cookies.php'));
        $this->assertContains('array ( )', $this->getSession()->getPage()->getText());
    }

    public function testHttpOnlyCookieIsDeleted()
    {
        $this->getSession()->restart();
        $this->getSession()->visit($this->pathTo('/cookie_page3.php'));
        $this->assertEquals('Has Cookie: false', $this->getSession()->getPage()->findById('cookie-status')->getText());

        $this->getSession()->reload();
        $this->assertEquals('Has Cookie: true', $this->getSession()->getPage()->findById('cookie-status')->getText());

        $this->getSession()->restart();
        $this->getSession()->visit($this->pathTo('/cookie_page3.php'));
        $this->assertEquals('Has Cookie: false', $this->getSession()->getPage()->findById('cookie-status')->getText());
    }

    public function testSessionPersistsBetweenRequests()
    {
        $this->getSession()->visit($this->pathTo('/session_test.php'));
        $this->assertNotNull($node = $this->getSession()->getPage()->find('css', '#session-id'));
        $sessionId = $node->getText();

        $this->getSession()->visit($this->pathTo('/session_test.php'));
        $this->assertNotNull($node = $this->getSession()->getPage()->find('css', '#session-id'));
        $this->assertEquals($sessionId, $node->getText());

        $this->getSession()->visit($this->pathTo('/session_test.php?login'));
        $this->assertNotNull($node = $this->getSession()->getPage()->find('css', '#session-id'));
        $this->assertNotEquals($sessionId, $newSessionId = $node->getText());

        $this->getSession()->visit($this->pathTo('/session_test.php'));
        $this->assertNotNull($node = $this->getSession()->getPage()->find('css', '#session-id'));
        $this->assertEquals($newSessionId, $node->getText());
    }

    public function testPageControlls()
    {
        $this->getSession()->visit($this->pathTo('/randomizer.php'));
        $number1 = $this->getSession()->getPage()->find('css', '#number')->getText();

        $this->getSession()->reload();
        $number2 = $this->getSession()->getPage()->find('css', '#number')->getText();

        $this->assertNotEquals($number1, $number2);

        $this->getSession()->visit($this->pathTo('/links.php'));
        $this->getSession()->getPage()->clickLink('Random number page');

        $this->assertEquals($this->pathTo('/randomizer.php'), $this->getSession()->getCurrentUrl());

        $this->getSession()->back();
        $this->assertEquals($this->pathTo('/links.php'), $this->getSession()->getCurrentUrl());

        $this->getSession()->forward();
        $this->assertEquals($this->pathTo('/randomizer.php'), $this->getSession()->getCurrentUrl());
    }

    public function testElementsTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $page = $this->getSession()->getPage();

        $this->assertNotNull($page->find('css', 'h1'));
        $this->assertEquals('Extremely useless page', $page->find('css', 'h1')->getText());
        $this->assertEquals('h1', $page->find('css', 'h1')->getTagName());

        $this->assertNotNull($page->find('xpath', '//div/strong[3]'));
        $this->assertEquals('pariatur', $page->find('xpath', '//div/strong[3]')->getText());
        $this->assertEquals('super-duper', $page->find('xpath', '//div/strong[3]')->getAttribute('class'));
        $this->assertTrue($page->find('xpath', '//div/strong[3]')->hasAttribute('class'));

        $this->assertNotNull($page->find('xpath', '//div/strong[2]'));
        $this->assertEquals('veniam', $page->find('xpath', '//div/strong[2]')->getText());
        $this->assertEquals('strong', $page->find('xpath', '//div/strong[2]')->getTagName());
        $this->assertNull($page->find('xpath', '//div/strong[2]')->getAttribute('class'));
        $this->assertFalse($page->find('xpath', '//div/strong[2]')->hasAttribute('class'));

        $strongs = $page->findAll('css', 'div#core > strong');
        $this->assertCount(3, $strongs);
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

    public function testVeryDeepElementsTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $page = $this->getSession()->getPage();

        $footer = $page->find('css', 'footer');
        $this->assertNotNull($footer);

        $searchForm = $footer->find('css', 'form#search-form');
        $this->assertNotNull($searchForm);
        $this->assertEquals('search-form', $searchForm->getAttribute('id'));

        $searchInput = $searchForm->findField('Search site...');
        $this->assertNotNull($searchInput);
        $this->assertEquals('text', $searchInput->getAttribute('type'));

        $searchInput = $searchForm->findField('Search site...');
        $this->assertNotNull($searchInput);
        $this->assertEquals('text', $searchInput->getAttribute('type'));

        $profileForm = $footer->find('css', '#profile');
        $this->assertNotNull($profileForm);

        $profileFormDiv = $profileForm->find('css', 'div');
        $this->assertNotNull($profileFormDiv);

        $profileFormDivLabel = $profileFormDiv->find('css', 'label');
        $this->assertNotNull($profileFormDivLabel);

        $profileFormDivParent = $profileFormDivLabel->getParent();
        $this->assertNotNull($profileFormDivParent);

        $profileFormDivParent = $profileFormDivLabel->getParent();
        $this->assertEquals('something', $profileFormDivParent->getAttribute('data-custom'));

        $profileFormInput = $profileFormDivLabel->findField('user-name');
        $this->assertNotNull($profileFormInput);
        $this->assertEquals('username', $profileFormInput->getAttribute('name'));
    }

    public function testDeepTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $traversDiv = $this->getSession()->getPage()->findAll('css', 'div.travers');

        $this->assertCount(1, $traversDiv);
        $traversDiv = $traversDiv[0];

        $subDivs = $traversDiv->findAll('css', 'div.sub');
        $this->assertCount(3, $subDivs);

        $this->assertTrue($subDivs[2]->hasLink('some deep url'));
        $this->assertFalse($subDivs[2]->hasLink('come deep url'));
        $subUrl = $subDivs[2]->findLink('some deep url');
        $this->assertNotNull($subUrl);

        $this->assertRegExp('/some_url$/', $subUrl->getAttribute('href'));
        $this->assertEquals('some deep url', $subUrl->getText());
        $this->assertEquals('some <strong>deep</strong> url', $subUrl->getHtml());

        $this->assertTrue($subUrl->has('css', 'strong'));
        $this->assertFalse($subUrl->has('css', 'em'));
        $this->assertEquals('deep', $subUrl->find('css', 'strong')->getText());
    }

    public function testLinks()
    {
        $this->getSession()->visit($this->pathTo('/links.php'));
        $page = $this->getSession()->getPage();
        $link = $page->findLink('Redirect me to');

        $this->assertNotNull($link);
        $this->assertRegExp('/redirector\.php$/', $link->getAttribute('href'));
        $link->click();

        $this->assertEquals($this->pathTo('/redirect_destination.php'), $this->getSession()->getCurrentUrl());

        $this->getSession()->visit($this->pathTo('/links.php'));
        $page = $this->getSession()->getPage();
        $link = $page->findLink('basic form image');

        $this->assertNotNull($link);
        $this->assertRegExp('/basic_form\.php$/', $link->getAttribute('href'));
        $link->click();

        $this->assertEquals($this->pathTo('/basic_form.php'), $this->getSession()->getCurrentUrl());

        $this->getSession()->visit($this->pathTo('/links.php'));
        $page = $this->getSession()->getPage();
        $link = $page->findLink("Link with a ");

        $this->assertNotNull($link);
        $this->assertRegExp('/links\.php\?quoted$/', $link->getAttribute('href'));
        $link->click();

        $this->assertEquals($this->pathTo('/links.php?quoted'), $this->getSession()->getCurrentUrl());
    }

    public function testJson()
    {
        $this->getSession()->visit($this->pathTo('/json.php'));
        $this->assertContains(
            '{"key1":"val1","key2":234,"key3":[1,2,3]}',
            $this->getSession()->getPage()->getContent()
        );
    }

    public function testBasicForm()
    {
        $this->getSession()->visit($this->pathTo('/basic_form.php'));

        $page = $this->getSession()->getPage();
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

        $page->findButton('Reset')->click();

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());

        $firstname->setValue('Konstantin');
        $page->fillField('last_name', 'Kudryashov');

        $page->findButton('Save')->click();

        if ($this->safePageWait(5000, 'document.getElementById("first") !== null')) {
            $this->assertEquals('Anket for Konstantin', $page->find('css', 'h1')->getText());
            $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
            $this->assertEquals('Lastname: Kudryashov', $page->find('css', '#last')->getText());
        }
    }

    /**
     * @dataProvider formSubmitWaysDataProvider
     */
    public function testFormSubmitWays($submitVia)
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/basic_form.php'));
        $page = $session->getPage();

        $firstname = $page->findField('first_name');
        $this->assertNotNull($firstname);
        $firstname->setValue('Konstantin');

        $page->findButton($submitVia)->click();

        if ($this->safePageWait(5000, 'document.getElementById("first") !== null')) {
            $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
        } else {
            $this->fail('Form was never submitted');
        }
    }

    public function formSubmitWaysDataProvider()
    {
        return array(
            array('Save'),
            array('input-type-image'),
            array('button-without-type'),
            array('button-type-submit'),
        );
    }

    public function testFormSubmit()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/basic_form.php'));

        $page = $session->getPage();
        $page->findField('first_name')->setValue('Konstantin');

        $page->find('xpath', 'descendant-or-self::form[1]')->submit();

        if ($this->safePageWait(5000, 'document.getElementById("first") !== null')) {
            $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
        };
    }

    public function testFormSubmitWithoutButton()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/form_without_button.html'));

        $page = $session->getPage();
        $page->findField('first_name')->setValue('Konstantin');

        $page->find('xpath', 'descendant-or-self::form[1]')->submit();

        if ($this->safePageWait(5000, 'document.getElementById("first") !== null')) {
            $this->assertEquals('Firstname: Konstantin', $page->find('css', '#first')->getText());
        };
    }

    protected function safePageWait($time, $condition)
    {
        try {
            $ret = $this->getSession()->wait($time, $condition);
        } catch (UnsupportedDriverActionException $e) {
            return true;
        }

        return $ret;
    }

    public function testBasicGetForm()
    {
        $this->getSession()->visit($this->pathTo('/basic_get_form.php'));

        $page = $this->getSession()->getPage();
        $this->assertEquals('Basic Get Form Page', $page->find('css', 'h1')->getText());

        $search = $page->findField('q');
        $this->assertNotNull($search);
        $search->setValue('some#query');
        $page->pressButton('Find');

        $this->assertNotNull($div = $page->find('css', 'div'));
        $this->assertEquals('some#query', $div->getText());
    }

    public function testMultiselect()
    {
        $this->getSession()->visit($this->pathTo('/multiselect_form.php'));
        $page = $this->getSession()->getPage();
        $this->assertEquals('Multiselect Test', $page->find('css', 'h1')->getText());

        $select      = $page->findField('select_number');
        $multiSelect = $page->findField('select_multiple_numbers[]');

        $this->assertNotNull($select);
        $this->assertNotNull($multiSelect);

        $this->assertEquals('20', $select->getValue());
        $this->assertSame(array(), $multiSelect->getValue());

        $select->selectOption('thirty');
        $this->assertEquals('30', $select->getValue());

        $multiSelect->selectOption('one', true);

        $this->assertSame(array('1'), $multiSelect->getValue());

        $multiSelect->selectOption('three', true);

        $this->assertEquals(array('1', '3'), $multiSelect->getValue());

        $button = $page->findButton('Register');
        $this->assertNotNull($button);
        $button->press();

        $space = ' ';
        $out = <<<OUT
  'agreement' = 'off',
  'select_multiple_numbers' =$space
  array (
    0 = '1',
    1 = '3',
  ),
  'select_number' = '30',
OUT;
        $this->assertContains($out, $page->getContent());
    }

    /**
     * @dataProvider testElementSelectedStateCheckDataProvider
     */
    public function testElementSelectedStateCheck($selectName, $optionValue, $optionText)
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/multiselect_form.php'));
        $select = $session->getPage()->findField($selectName);

        $option = $select->find('named', array('option', $optionValue));
        $this->assertNotNull($option);

        $this->assertFalse($option->isSelected());
        $select->selectOption($optionText);
        $this->assertTrue($option->isSelected());
    }

    public function testElementSelectedStateCheckDataProvider()
    {
        return array(
            array('select_number', '30', 'thirty'),
            array('select_multiple_numbers[]', '2', 'two'),
        );
    }

    public function testAdvancedForm()
    {
        $this->getSession()->visit($this->pathTo('/advanced_form.php'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'ever');
        $page->fillField('last_name', 'zet');

        $page->pressButton('Register');

        $this->assertContains('no file', $page->getContent());

        $this->getSession()->visit($this->pathTo('/advanced_form.php'));

        $page = $this->getSession()->getPage();
        $this->assertEquals('ADvanced Form Page', $page->find('css', 'h1')->getText());

        $firstname   = $page->findField('first_name');
        $lastname    = $page->findField('lastn');
        $email       = $page->findField('Your email:');
        $select      = $page->findField('select_number');
        $sex         = $page->findField('sex');
        $maillist    = $page->findField('mail_list');
        $agreement   = $page->findField('agreement');
        $notes       = $page->findField('notes');
        $about       = $page->findField('about');

        $this->assertNotNull($firstname);
        $this->assertNotNull($lastname);
        $this->assertNotNull($email);
        $this->assertNotNull($select);
        $this->assertNotNull($sex);
        $this->assertNotNull($maillist);
        $this->assertNotNull($agreement);
        $this->assertNotNull($notes);

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());
        $this->assertEquals('your@email.com', $email->getValue());
        $this->assertEquals('20', $select->getValue());
        $this->assertEquals('w', $sex->getValue());
        $this->assertEquals('original notes', $notes->getValue());

        $this->assertTrue($maillist->getValue());
        $this->assertFalse($agreement->getValue());

        $this->assertTrue($maillist->isChecked());
        $this->assertFalse($agreement->isChecked());

        $agreement->check();
        $this->assertTrue($agreement->isChecked());

        $maillist->uncheck();
        $this->assertFalse($maillist->isChecked());

        $select->selectOption('thirty');
        $this->assertEquals('30', $select->getValue());

        $sex->selectOption('m');
        $this->assertEquals('m', $sex->getValue());

        $notes->setValue('new notes');
        $this->assertEquals('new notes', $notes->getValue());

        $about->attachFile($this->mapRemoteFilePath(__DIR__ . '/web-fixtures/some_file.txt'));

        $button = $page->findButton('Register');
        $this->assertNotNull($button);

        $page->fillField('first_name', 'Foo "item"');
        $page->fillField('last_name', 'Bar');
        $page->fillField('Your email:', 'ever.zet@gmail.com');

        $this->assertEquals('Foo "item"', $firstname->getValue());
        $this->assertEquals('Bar', $lastname->getValue());

        $button->press();

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<OUT
array (
  'agreement' = 'on',
  'email' = 'ever.zet@gmail.com',
  'first_name' = 'Foo "item"',
  'last_name' = 'Bar',
  'notes' = 'new notes',
  'select_number' = '30',
  'sex' = 'm',
  'submit' = 'Register',
)
1 uploaded file
OUT;
            $this->assertContains($out, $page->getContent());
        }
    }

    public function testCheckboxMultiple()
    {
        $this->getSession()->visit($this->pathTo('/multicheckbox_form.php'));

        $page = $this->getSession()->getPage();
        $this->assertEquals('Multicheckbox Test', $page->find('css', 'h1')->getText());

        $updateMail  = $page->find('css', '[name="mail_types[]"][value="update"]');
        $spamMail    = $page->find('css', '[name="mail_types[]"][value="spam"]');

        $this->assertNotNull($updateMail);
        $this->assertNotNull($spamMail);

        $this->assertTrue($updateMail->getValue());
        $this->assertFalse($spamMail->getValue());

        $this->assertTrue($updateMail->isChecked());
        $this->assertFalse($spamMail->isChecked());

        $updateMail->uncheck();
        $this->assertFalse($updateMail->isChecked());
        $this->assertFalse($spamMail->isChecked());

        $spamMail->check();
        $this->assertFalse($updateMail->isChecked());
        $this->assertTrue($spamMail->isChecked());
    }

    public function testMultiInput()
    {
        $this->getSession()->visit($this->pathTo('/multi_input_form.php'));
        $page = $this->getSession()->getPage();
        $this->assertEquals('Multi input Test', $page->find('css', 'h1')->getText());

        $first = $page->findField('First');
        $second = $page->findField('Second');
        $third = $page->findField('Third');

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertNotNull($third);

        $this->assertEquals('tag1', $first->getValue());
        $this->assertSame('tag2', $second->getValue());
        $this->assertEquals('tag1', $third->getValue());

        $first->setValue('tag2');
        $this->assertEquals('tag2', $first->getValue());
        $this->assertSame('tag2', $second->getValue());
        $this->assertEquals('tag1', $third->getValue());

        $second->setValue('one');

        $this->assertEquals('tag2', $first->getValue());
        $this->assertSame('one', $second->getValue());

        $third->setValue('tag3');

        $this->assertEquals('tag2', $first->getValue());
        $this->assertSame('one', $second->getValue());
        $this->assertEquals('tag3', $third->getValue());

        $button = $page->findButton('Register');
        $this->assertNotNull($button);
        $button->press();

        $space = ' ';
        $out = <<<OUT
  'tags' =$space
  array (
    0 = 'tag2',
    1 = 'one',
    2 = 'tag3',
  ),
OUT;
        $this->assertContains($out, $page->getContent());
    }

    /**
     * Map remote file path.
     *
     * @param string $file File path.
     *
     * @return string
     */
    protected function mapRemoteFilePath($file)
    {
        if (!isset($_SERVER['TEST_MACHINE_BASE_PATH']) || !isset($_SERVER['DRIVER_MACHINE_BASE_PATH'])) {
            return $file;
        }

        return preg_replace('/^' . preg_quote($_SERVER['TEST_MACHINE_BASE_PATH'], '/') . '/', $_SERVER['DRIVER_MACHINE_BASE_PATH'], $file, 1);
    }

    public function testAdvancedFormSecondSubmit()
    {
        $this->getSession()->visit($this->pathTo('/advanced_form.php'));
        $page = $this->getSession()->getPage();

        $button = $page->findButton('Login');
        $this->assertNotNull($button);
        $button->press();

        $toSearch = array(
            "'agreement' = 'off',",
            "'submit' = 'Login',",
            'no file',
        );

        $pageContent = $page->getContent();

        foreach ($toSearch as $searchString) {
            $this->assertContains($searchString, $pageContent);
        }
    }

    public function testSubmitEmptyTextarea()
    {
        $this->getSession()->visit($this->pathTo('/empty_textarea.php'));
        $page = $this->getSession()->getPage();

        $page->pressButton('Save');

        $toSearch = array(
            "'textarea' = '',",
            "'submit' = 'Save',",
            'no file',
        );

        $pageContent = $page->getContent();

        foreach ($toSearch as $searchString) {
            $this->assertContains($searchString, $pageContent);
        }
    }

    public function testHtml5FormInputAttribute()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();

        $firstName = $page->findField('first_name');
        $lastName = $page->findField('last_name');

        $this->assertNotNull($firstName);
        $this->assertNotNull($lastName);

        $this->assertEquals('not set', $lastName->getValue());
        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $this->assertEquals('Doe', $lastName->getValue());

        $page->pressButton('Submit in form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<OUT
  'first_name' = 'John',
  'last_name' = 'Doe',
OUT;
            $this->assertContains($out, $page->getContent());
            $this->assertNotContains('other_field', $page->getContent());
        }
    }

    public function testHtml5FormButtonAttribute()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();

        $firstName = $page->findField('first_name');
        $lastName = $page->findField('last_name');

        $this->assertNotNull($firstName);
        $this->assertNotNull($lastName);

        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $page->pressButton('Submit outside form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<OUT
  'first_name' = 'John',
  'last_name' = 'Doe',
  'submit_button' = 'test',
OUT;
            $this->assertContains($out, $page->getContent());
        }
    }

    public function testHtml5FormOutside()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();

        $field = $page->findField('other_field');

        $this->assertNotNull($field);

        $field->setValue('hello');

        $page->pressButton('Submit separate form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<OUT
  'other_field' = 'hello',
OUT;
            $this->assertContains($out, $page->getContent());
            $this->assertNotContains('first_name', $page->getContent());
        }

    }

    /**
     * @dataProvider setBasicAuthDataProvider
     */
    public function testSetBasicAuth($user, $pass, $pageText)
    {
        $session = $this->getSession();

        $session->setBasicAuth($user, $pass);

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertContains($pageText, $session->getPage()->getContent());
    }

    public function setBasicAuthDataProvider()
    {
        return array(
            array('mink-user', 'mink-password', 'is authenticated'),
            array('', '', 'is not authenticated'),
        );
    }

    public function testResetBasicAuth()
    {
        $session = $this->getSession();

        $session->setBasicAuth('mink-user', 'mink-password');

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertContains('is authenticated', $session->getPage()->getContent());

        $session->setBasicAuth(false);

        $session->visit($this->pathTo('/headers.php'));

        $this->assertNotContains('PHP_AUTH_USER', $session->getPage()->getContent());
    }

    public function testResetWithBasicAuth()
    {
        $session = $this->getSession();

        $session->setBasicAuth('mink-user', 'mink-password');

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertContains('is authenticated', $session->getPage()->getContent());

        $session->reset();

        $session->visit($this->pathTo('/headers.php'));

        $this->assertNotContains('PHP_AUTH_USER', $session->getPage()->getContent());
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

    public function testStatuses()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->assertEquals(200, $this->getSession()->getStatusCode());
        $this->assertEquals($this->pathTo('/index.php'), $this->getSession()->getCurrentUrl());

        $this->getSession()->visit($this->pathTo('/404.php'));

        $this->assertEquals($this->pathTo('/404.php'), $this->getSession()->getCurrentUrl());
        $this->assertEquals(404, $this->getSession()->getStatusCode());
        $this->assertEquals('Sorry, page not found', $this->getSession()->getPage()->getContent());
    }

    public function testVisitErrorPage()
    {
        $this->getSession()->visit($this->pathTo('/500.php'));

        $this->assertContains('Sorry, a server error happened', $this->getSession()->getPage()->getContent(), 'Drivers allow loading pages with a 500 status code');
    }

    public function testHeaders()
    {
        $this->getSession()->setRequestHeader('Accept-Language', 'fr');
        $this->getSession()->visit($this->pathTo('/headers.php'));

        $this->assertContains('[HTTP_ACCEPT_LANGUAGE] => fr', $this->getSession()->getPage()->getContent());
    }

    public function testResetHeaders()
    {
        $session = $this->getSession();

        $session->setRequestHeader('X-Mink-Test', 'test');
        $session->visit($this->pathTo('/headers.php'));

        $this->assertContains('[HTTP_X_MINK_TEST] => test', $session->getPage()->getContent(), 'The custom header should be sent', true);

        $session->reset();

        $session->visit($this->pathTo('/headers.php'));

        $this->assertNotContains('[HTTP_X_MINK_TEST] => test', $session->getPage()->getContent(), 'The custom header should not be sent after resetting', true);
    }

    public function testResponseHeaders()
    {
        $this->getSession()->visit($this->pathTo('/response_headers.php'));

        $headers = $this->getSession()->getResponseHeaders();

        $lowercasedHeaders = array();
        foreach ($headers as $name => $value) {
            $lowercasedHeaders[str_replace('_', '-', strtolower($name))] = $value;
        }

        $this->assertArrayHasKey('x-mink-test', $lowercasedHeaders);
    }

    public function testScreenshot()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Testing screenshots requires the GD extension');
        }

        $this->getSession()->visit($this->pathTo('/index.php'));

        $screenShot = $this->getSession()->getScreenshot();

        $this->assertInternalType('string', $screenShot);
        $this->assertFalse(base64_decode($screenShot, true), 'The returned screenshot should not be base64-encoded');

        $img = imagecreatefromstring($screenShot);

        if (false === $img) {
            $this->fail('The screenshot should be a valid image');
        }

        imagedestroy($img);
    }

    public function testCheckInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $element = $this->getSession()->getPage()->findById('user-name');

        $this->assertNotNull($element);

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->check($element->getXpath());
    }

    public function testCheckNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->check('//html/.//invalid');
    }

    public function testUncheckInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $element = $this->getSession()->getPage()->findById('user-name');

        $this->assertNotNull($element);

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->uncheck($element->getXpath());
    }

    public function testUncheckNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->uncheck('//html/.//invalid');
    }

    public function testSelectOptionInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $element = $this->getSession()->getPage()->findById('user-name');

        $this->assertNotNull($element);

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->selectOption($element->getXpath(), 'test');
    }

    public function testSelectOptionNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->selectOption('//html/.//invalid', 'test');
    }

    public function testAttachFileInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $element = $this->getSession()->getPage()->findById('user-name');

        $this->assertNotNull($element);

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->attachFile($element->getXpath(), __FILE__);
    }

    public function testAttachFileNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->attachFile('//html/.//invalid', __FILE__);
    }

    public function testSubmitFormInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));
        $element = $this->getSession()->getPage()->findById('core');

        $this->assertNotNull($element);

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->submitForm($element->getXpath());
    }

    public function testSubmitFormNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->submitForm('//html/.//invalid');
    }

    public function testGetTagNameNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getTagName('//html/.//invalid');
    }

    public function testGetTextNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getText('//html/.//invalid');
    }

    public function testGetHtmlNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getHtml('//html/.//invalid');
    }

    public function testGetValueNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getValue('//html/.//invalid');
    }

    public function testSetValueNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->setValue('//html/.//invalid', 'test');
    }

    public function testIsSelectedNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->isSelected('//html/.//invalid');
    }

    public function testIsCheckedNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->isChecked('//html/.//invalid');
    }

    public function testIsVisibleNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->isVisible('//html/.//invalid');
    }

    public function testClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->click('//html/.//invalid');
    }

    public function testDoubleClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->doubleClick('//html/.//invalid');
    }

    public function testRightClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->rightClick('//html/.//invalid');
    }

    public function testGetAttributeNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getAttribute('//html/.//invalid', 'id');
    }

    public function testMouseOverFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->mouseOver('//html/.//invalid');
    }

    public function testFocusFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->focus('//html/.//invalid');
    }

    public function testBlurFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->blur('//html/.//invalid');
    }

    public function testKeyPressNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->keyPress('//html/.//invalid', 'a');
    }

    public function testKeyDownNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->keyDown('//html/.//invalid', 'a');
    }

    public function testKeyUpNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->keyUp('//html/.//invalid', 'a');
    }

    protected function pathTo($path)
    {
        return $_SERVER['WEB_FIXTURES_HOST'].$path;
    }
}
