<?php

namespace Behat\Mink\Element;

use Behat\Mink\Exception\ElementNotFoundException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Actions holder element.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class ActionableElement extends Element
{
    /**
     * Finds link with specified locator.
     *
     * @param   string  $locator    link id, title, text or image alt
     *
     * @return  Behat\Mink\Element\NodeElement|null
     */
    abstract public function findLink($locator);

    /**
     * Finds button (input[type=submit|image|button], button) with specified locator.
     *
     * @param   string  $locator    button id, value or alt
     *
     * @return  Behat\Mink\Element\NodeElement|null
     */
    abstract public function findButton($locator);

    /**
     * Finds field (input, textarea, select) with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @return  Behat\Mink\Element\NodeElement|null
     */
    abstract public function findField($locator);

    /**
     * Clicks link with specified locator.
     *
     * @param   string  $locator    link id, title, text or image alt
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function clickLink($locator)
    {
        $link = $this->findLink($locator);

        if (null === $link) {
            throw new ElementNotFoundException('link', $locator);
        }

        $this->getSession()->getDriver()->click($link->getXpath());
    }

    /**
     * Clicks button (input[type=submit|image|button], button) with specified locator.
     *
     * @param   string  $locator    button id, value or alt
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function clickButton($locator)
    {
        $button = $this->findButton($locator);

        if (null === $button) {
            throw new ElementNotFoundException('button', $locator);
        }

        $this->getSession()->getDriver()->click($button->getXpath());
    }

    /**
     * Fills in field (input, textarea, select) with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillField($locator, $value)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        $this->getSession()->getDriver()->setValue($field->getXpath(), $value);
    }

    /**
     * Checks checkbox with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function checkField($locator)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        $this->getSession()->getDriver()->check($field->getXpath());
    }

    /**
     * Unchecks checkbox with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function uncheckField($locator)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        $this->getSession()->getDriver()->uncheck($field->getXpath());
    }

    /**
     * Selects option from select field with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function selectFieldOption($locator, $value)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        $this->getSession()->getDriver()->selectOption($field->getXpath(), $value);
    }

    /**
     * Attach file to file field with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @throws  Behat\Mink\Exception\ElementNotFoundException
     */
    public function attachFileToField($locator, $path)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException('field', $field);
        }

        $this->getSession()->getDriver()->attachFile($field->getXpath(), $path);
    }
}
