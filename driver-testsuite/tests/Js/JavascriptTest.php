<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class JavascriptTest extends TestCase
{
    public function testAriaRoles()
    {
        $this->getSession()->visit($this->pathTo('/aria_roles.php'));

        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === false');
        $this->getSession()->getPage()->pressButton('Toggle');
        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === true');

        $this->getSession()->getPage()->clickLink('Go to Index');
        $this->assertEquals($this->pathTo('/index.php'), $this->getSession()->getCurrentUrl());
    }

    public function testDragDrop()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $draggable = $this->getSession()->getPage()->find('css', '#draggable');
        $droppable = $this->getSession()->getPage()->find('css', '#droppable');

        $this->assertNotNull($draggable);
        $this->assertNotNull($droppable);

        $draggable->dragTo($droppable);
        $this->assertEquals('Dropped!', $droppable->find('css', 'p')->getText());
    }

    // TODO reevaluate this test as it is passing only for Selenium2Driver
    // test select option without value
    public function testIssue193()
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue193.html'));

        $session->getPage()->selectFieldOption('options-without-values', 'Two');
        $this->assertEquals('Two', $session->getPage()->findById('options-without-values')->getValue());

        $session->getPage()->selectFieldOption('options-with-values', 'two');
        $this->assertEquals('two', $session->getPage()->findById('options-with-values')->getValue());
    }

    // test accentuated char in button
    public function testIssue225()
    {
        $this->getSession()->visit($this->pathTo('/issue225.php'));
        $this->getSession()->getPage()->pressButton('Créer un compte');
        $this->getSession()->wait(5000, '$("#panel").text() != ""');

        $this->assertContains('OH AIH!', $this->getSession()->getPage()->getText());
    }
}
