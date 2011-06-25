<?php

namespace Behat\Mink\Behat\Context;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Mink\Mink,
    Behat\Mink\Exception\ElementNotFoundException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat context for Mink.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FormContext extends BehatContext
{
    /**
     * @When /^(?:|I )press "(?P<button>[^"]*)"$/
     */
    public function pressButton($button)
    {
        $this->getSession()->getPage()->clickButton($button);
    }

    /**
     * @When /^(?:|I )follow "(?P<link>[^"]*)"$/
     */
    public function clickLink($link)
    {
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * @When /^(?:|I )fill in "(?P<field>[^"]*)" with "(?P<value>[^"]*)"$/
     * @When /^(?:|I )fill in "(?P<value>[^"]*)" for "(?P<field>[^"]*)"$/
     */
    public function fillField($field, $value)
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @When /^(?:|I )fill in the following:$/
     */
    public function fillFields(TableNode $fields)
    {
        foreach ($fieldsTable->getRowsHash() as $field => $value) {
            $this->fillField($field, $value);
        }
    }

    /**
     * @When /^(?:|I )select "(?P<option>[^"]*)" from "(?P<select>[^"]*)"$/
     */
    public function selectOption($select, $option)
    {
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @When /^(?:|I )check "(?P<option>[^"]*)"$/
     */
    public function checkOption($option)
    {
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * @When /^(?:|I )uncheck "(?P<option>[^"]*)"$/
     */
    public function uncheckOption($option)
    {
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>[^"]*)"$/
     */
    public function attachFileToField($field, $path)
    {
        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    /**
     * @Then /^the "(?P<field>[^"]*)" field should contain "(?P<value>[^"]*)"$/
     */
    public function assertFieldContains($value)
    {
        $field = $this->getSession()->getPage()->findField($field);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertContains($value, $field->getValue());
    }

    /**
     * @Then /^the "(?P<field>[^"]*)" field should not contain "(?P<value>[^"]*)"$/
     */
    public function assertFieldNotContains($value)
    {
        $field = $this->getSession()->getPage()->findField($field);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertNotContains($value, $field->getValue());
    }

    /**
     * @Then /^the "(?P<checkbox>[^"]*)" checkbox should be checked$/
     */
    public function assertCheckboxChecked($checkbox)
    {
        $field = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertTrue($field->isChecked());
    }

    /**
     * @Then /^the "(?P<checkbox>[^"]*)" checkbox should not be checked$/
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $field = $this->getSession()->getPage()->findField($checkbox);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        assertFalse($field->isChecked());
    }
}
