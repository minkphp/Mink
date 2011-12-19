<?php

namespace Tests\Behat\Mink\Driver;

require_once 'JavascriptDriverTest.php';

/**
 * @group seleniumdriver
 */
class Selenium2DriverTest extends JavascriptDriverTest
{
    protected function setUp()
    {
        $this->getMink()->setDefaultSessionName('selenium2');
    }

    public function testMouseEvents()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $clicker = $this->getSession()->getPage()->find('css', '.elements div#clicker');

        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());

        //$clicker->mouseOver();
        //$this->assertEquals('mouse overed', $clicker->getText());
    }

    public function testDragDrop() {}
    
    public function testKeyboardEvents() {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $input1 = $this->getSession()->getPage()->find('css', '.elements input.input.first');
        $input2 = $this->getSession()->getPage()->find('css', '.elements input.input.second');
        $input3 = $this->getSession()->getPage()->find('css', '.elements input.input.third');
        $event  = $this->getSession()->getPage()->find('css', '.elements .text-event');

        // $input1->keyDown('u');
        // $this->assertEquals('key downed:0', $event->getText());

        // $input1->keyDown('u', 'alt');
        // $this->assertEquals('key downed:1', $event->getText());

        $input2->keyPress('r');
        $this->assertEquals('key pressed:114 / 0', $event->getText());

        $input2->keyPress('r', 'alt');
        $this->assertEquals('key pressed:114 / 1', $event->getText());

        // $input3->keyUp(78);
        // $this->assertEquals('key upped:78 / 0', $event->getText());

        // $input3->keyUp(78, 'alt');
        // $this->assertEquals('key upped:78 / 1', $event->getText());
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
        $multiSelect = $page->findField('select_multiple_numbers[]');
        $sex         = $page->findField('sex');
        $maillist    = $page->findField('mail_list');
        $agreement   = $page->findField('agreement');
        $about       = $page->findField('about');

        $this->assertNotNull($firstname);
        $this->assertNotNull($lastname);
        $this->assertNotNull($email);
        $this->assertNotNull($select);
        $this->assertNotNull($multiSelect);
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

        $select->selectOption('thirty');
        $this->assertEquals('30', $select->getValue());

        $sex->selectOption('m');
        $this->assertEquals('m', $sex->getValue());
        //$about->attachFile(__DIR__ . '/web-fixtures/some_file.txt');

        $multiSelect->selectOption('one', true);
        $multiSelect->selectOption('three', true);

        $button = $page->findButton('Register');

        $page->fillField('first_name', 'Foo "item"');
        $page->fillField('last_name', 'Bar');
        $page->fillField('Your email:', 'ever.zet@gmail.com');

        $this->assertEquals('Foo "item"', $firstname->getValue());
        $this->assertEquals('Bar', $lastname->getValue());

        $button->press();

        $space = ' ';
        $this->assertContains(<<<OUT
array (
  'first_name' = 'Foo "item"',
  'last_name' = 'Bar',
  'email' = 'ever.zet@gmail.com',
  'select_number' = '30',
  'sex' = 'm',
  'select_multiple_numbers' =$space
  array (
    0 = '1',
    1 = '3',
  ),
  'agreement' = 'on',
)
no file
OUT
            , $page->getContent()
        );
    }
}
