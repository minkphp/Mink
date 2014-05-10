<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class ChangeEventTest extends TestCase
{
    /**
     * 'change' event should be fired after selecting an <option> in a <select>
     *
     * TODO check whether this test is redundant with other change event tests.
     */
    public function testIssue255()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue255.php'));

        $session->getPage()->selectFieldOption('foo_select', 'Option 3');

        $session->wait(2000, '$("#output_foo_select").text() != ""');
        $this->assertEquals('onChangeSelect', $session->getPage()->find('css', '#output_foo_select')->getText());
    }

    public function testIssue178()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue178.html'));

        $session->getPage()->findById('source')->setValue('foo');
        $this->assertEquals('foo', $session->getPage()->findById('target')->getText());
    }

    /**
     * @dataProvider setValueChangeEventDataProvider
     * @group change-event-detector
     */
    public function testSetValueChangeEvent($elementId, $elementValue)
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $input = $page->findById($elementId);
        $this->assertNull($page->findById($elementId.'-result'));

        $input->setValue($elementValue);
        $this->assertElementChangeCount($elementId);
    }

    public function setValueChangeEventDataProvider()
    {
        return array(
            'input default' => array('the-input-default', 'some value'),
            'input text' => array('the-input-text', 'some value'),
            'input email' => array('the-email', 'some value'),
            'select' => array('the-select', '30'),
            'textarea' => array('the-textarea', 'some value'),
            'file' => array('the-file', 'some value'),
        );
    }

    /**
     * @dataProvider selectOptionChangeEventDataProvider
     * @group change-event-detector
     */
    public function testSelectOptionChangeEvent($elementId, $elementValue)
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $input = $page->findById($elementId);
        $this->assertNull($page->findById($elementId.'-result'));

        $input->selectOption($elementValue);
        $this->assertElementChangeCount($elementId);
    }

    public function selectOptionChangeEventDataProvider()
    {
        return array(
            'select' => array('the-select', '30'),
            'radio' => array('the-radio-m', 'm'),
        );
    }

    /**
     * @group change-event-detector
     */
    public function testCheckChangeEvent()
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $checkbox = $page->findById('the-unchecked-checkbox');
        $this->assertNull($page->findById('the-unchecked-checkbox-result'));

        $checkbox->check();
        $this->assertElementChangeCount('the-unchecked-checkbox');
    }

    /**
     * @group change-event-detector
     */
    public function testUncheckChangeEvent()
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $checkbox = $page->findById('the-checked-checkbox');
        $this->assertNull($page->findById('the-checked-checkbox-result'));

        $checkbox->uncheck();
        $this->assertElementChangeCount('the-checked-checkbox');
    }

    private function assertElementChangeCount($elementId)
    {
        $counterElement = $this->getSession()->getPage()->findById($elementId.'-result');
        $actualCount = null === $counterElement ? 0 : $counterElement->getText();

        $this->assertEquals('1', $actualCount);
    }
}
