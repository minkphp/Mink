<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class ErrorHandlingTest extends TestCase
{
    public function testVisitErrorPage()
    {
        $this->getSession()->visit($this->pathTo('/500.php'));

        $this->assertContains('Sorry, a server error happened', $this->getSession()->getPage()->getContent(), 'Drivers allow loading pages with a 500 status code');
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

    public function testGetOuterHtmlNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->setExpectedException('Behat\Mink\Exception\DriverException');
        $this->getSession()->getDriver()->getOuterHtml('//html/.//invalid');
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
}
