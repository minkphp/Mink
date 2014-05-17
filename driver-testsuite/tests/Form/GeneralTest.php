<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

class GeneralTest extends TestCase
{
    // test multiple submit buttons
    public function testIssue212()
    {
        $session = $this->getSession();

        $session->visit($this->pathTo('/issue212.html'));
        $page = $session->getPage();

        $field = $page->findById('poney-button');
        $this->assertNotNull($field);
        $this->assertEquals('poney', $field->getValue());
    }

    public function testBasicForm()
    {
        $this->getSession()->visit($this->pathTo('/basic_form.html'));

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
        $session->visit($this->pathTo('/basic_form.html'));
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
        $session->visit($this->pathTo('/basic_form.html'));

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

    public function testAdvancedForm()
    {
        $this->getSession()->visit($this->pathTo('/advanced_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'ever');
        $page->fillField('last_name', 'zet');

        $page->pressButton('Register');

        $this->assertContains('no file', $page->getContent());

        $this->getSession()->visit($this->pathTo('/advanced_form.html'));

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

        $about->attachFile($this->mapRemoteFilePath(__DIR__ . '/../../web-fixtures/some_file.txt'));

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

    public function testMultiInput()
    {
        $this->getSession()->visit($this->pathTo('/multi_input_form.html'));
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

    public function testAdvancedFormSecondSubmit()
    {
        $this->getSession()->visit($this->pathTo('/advanced_form.html'));
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
        $this->getSession()->visit($this->pathTo('/empty_textarea.html'));
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
}
