<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

class CheckboxTest extends TestCase
{
    public function testManipulate()
    {
        $this->getSession()->visit($this->pathTo('advanced_form.html'));

        $checkbox = $this->getAssertSession()->fieldExists('agreement');

        $this->assertFalse($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());

        $checkbox->check();

        $this->assertTrue($checkbox->getValue());
        $this->assertTrue($checkbox->isChecked());

        $checkbox->uncheck();

        $this->assertFalse($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());
    }

    public function testSetValue()
    {
        $this->getSession()->visit($this->pathTo('advanced_form.html'));

        $checkbox = $this->getAssertSession()->fieldExists('agreement');

        $this->assertFalse($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());

        $checkbox->setValue(true);

        $this->assertTrue($checkbox->getValue());
        $this->assertTrue($checkbox->isChecked());

        $checkbox->setValue(false);

        $this->assertFalse($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());
    }

    public function testCheckboxMultiple()
    {
        $this->getSession()->visit($this->pathTo('/multicheckbox_form.html'));

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
}
