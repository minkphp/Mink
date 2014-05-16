<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

class RadioTest extends TestCase
{
    protected function setUp()
    {
        $this->getSession()->visit($this->pathTo('radio.html'));
    }

    public function testIsChecked()
    {
        $option = $this->getSession()->getPage()->findById('first');
        $option2 = $this->getSession()->getPage()->findById('second');

        $this->assertNotNull($option);
        $this->assertNotNull($option2);

        $this->assertTrue($option->isChecked());
        $this->assertFalse($option2->isChecked());

        $option2->selectOption('updated');

        $this->assertFalse($option->isChecked());
        $this->assertTrue($option2->isChecked());
    }

    public function testSelectOption()
    {
        $option = $this->getSession()->getPage()->findById('first');
        $this->assertNotNull($option);

        $this->assertEquals('set', $option->getValue());

        $option->selectOption('updated');

        $this->assertEquals('updated', $option->getValue());

        $option->selectOption('set');

        $this->assertEquals('set', $option->getValue());
    }

    public function testSetValue()
    {
        $option = $this->getSession()->getPage()->findById('first');
        $this->assertNotNull($option);

        $this->assertEquals('set', $option->getValue());

        $option->setValue('updated');

        $this->assertEquals('updated', $option->getValue());
        $this->assertFalse($option->isChecked());
    }

    public function testSameNameInMultipleForms()
    {
        $option1 = $this->getSession()->getPage()->findById('reused_form1');
        $option2 = $this->getSession()->getPage()->findById('reused_form2');

        $this->assertNotNull($option1);
        $this->assertNotNull($option2);

        $this->assertEquals('test2', $option1->getValue());
        $this->assertEquals('test3', $option2->getValue());

        $option1->selectOption('test');

        $this->assertEquals('test', $option1->getValue());
        $this->assertEquals('test3', $option2->getValue());
    }
}
