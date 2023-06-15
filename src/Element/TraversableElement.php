<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Traversable element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class TraversableElement extends Element
{
    /**
     * Finds element by its id.
     *
     * @param string $id element id
     *
     * @return NodeElement|null
     */
    public function findById(string $id)
    {
        return $this->find('named', array('id', $id));
    }

    /**
     * Checks whether element has a link with specified locator.
     *
     * @param string $locator link id, title, text or image alt
     *
     * @return bool
     */
    public function hasLink(string $locator)
    {
        return null !== $this->findLink($locator);
    }

    /**
     * Finds link with specified locator.
     *
     * @param string $locator link id, title, text or image alt
     *
     * @return NodeElement|null
     */
    public function findLink(string $locator)
    {
        return $this->find('named', array('link', $locator));
    }

    /**
     * Clicks link with specified locator.
     *
     * @param string $locator link id, title, text or image alt
     *
     * @return void
     *
     * @throws ElementNotFoundException
     */
    public function clickLink(string $locator)
    {
        $link = $this->findLink($locator);

        if (null === $link) {
            throw new ElementNotFoundException($this->getDriver(), 'link', 'id|title|alt|text', $locator);
        }

        $link->click();
    }

    /**
     * Checks whether element has a button (input[type=submit|image|button|reset], button) with specified locator.
     *
     * @param string $locator button id, value or alt
     *
     * @return bool
     */
    public function hasButton(string $locator)
    {
        return null !== $this->findButton($locator);
    }

    /**
     * Finds button (input[type=submit|image|button|reset], button) with specified locator.
     *
     * @param string $locator button id, value or alt
     *
     * @return NodeElement|null
     */
    public function findButton(string $locator)
    {
        return $this->find('named', array('button', $locator));
    }

    /**
     * Presses button (input[type=submit|image|button|reset], button) with specified locator.
     *
     * @param string $locator button id, value or alt
     *
     * @return void
     *
     * @throws ElementNotFoundException
     */
    public function pressButton(string $locator)
    {
        $button = $this->findButton($locator);

        if (null === $button) {
            throw new ElementNotFoundException($this->getDriver(), 'button', 'id|name|title|alt|value', $locator);
        }

        $button->press();
    }

    /**
     * Checks whether element has a field (input, textarea, select) with specified locator.
     *
     * @param string $locator input id, name or label
     *
     * @return bool
     */
    public function hasField(string $locator)
    {
        return null !== $this->findField($locator);
    }

    /**
     * Finds field (input, textarea, select) with specified locator.
     *
     * @param string $locator input id, name or label
     *
     * @return NodeElement|null
     */
    public function findField(string $locator)
    {
        return $this->find('named', array('field', $locator));
    }

    /**
     * Fills in field (input, textarea, select) with specified locator.
     *
     * @param string            $locator input id, name or label
     * @param string|bool|array $value   value
     *
     * @return void
     *
     * @throws ElementNotFoundException
     *
     * @see NodeElement::setValue
     */
    public function fillField(string $locator, $value)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value|placeholder', $locator);
        }

        $field->setValue($value);
    }

    /**
     * Checks whether element has a checkbox with specified locator, which is checked.
     *
     * @param string $locator input id, name or label
     *
     * @return bool
     *
     * @see NodeElement::isChecked
     */
    public function hasCheckedField(string $locator)
    {
        $field = $this->findField($locator);

        return null !== $field && $field->isChecked();
    }

    /**
     * Checks whether element has a checkbox with specified locator, which is unchecked.
     *
     * @param string $locator input id, name or label
     *
     * @return bool
     *
     * @see NodeElement::isChecked
     */
    public function hasUncheckedField(string $locator)
    {
        $field = $this->findField($locator);

        return null !== $field && !$field->isChecked();
    }

    /**
     * Checks checkbox with specified locator.
     *
     * @param string $locator input id, name or label
     *
     * @return void
     *
     * @throws ElementNotFoundException
     */
    public function checkField(string $locator)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value', $locator);
        }

        $field->check();
    }

    /**
     * Unchecks checkbox with specified locator.
     *
     * @param string $locator input id, name or label
     *
     * @return void
     *
     * @throws ElementNotFoundException
     */
    public function uncheckField(string $locator)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value', $locator);
        }

        $field->uncheck();
    }

    /**
     * Checks whether element has a select field with specified locator.
     *
     * @param string $locator select id, name or label
     *
     * @return bool
     */
    public function hasSelect(string $locator)
    {
        return $this->has('named', array('select', $locator));
    }

    /**
     * Selects option from select field with specified locator.
     *
     * @param string $locator  input id, name or label
     * @param string $value    option value
     * @param bool   $multiple select multiple options
     *
     * @return void
     *
     * @throws ElementNotFoundException
     *
     * @see NodeElement::selectOption
     */
    public function selectFieldOption(string $locator, string $value, bool $multiple = false)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value', $locator);
        }

        $field->selectOption($value, $multiple);
    }

    /**
     * Checks whether element has a table with specified locator.
     *
     * @param string $locator table id or caption
     *
     * @return bool
     */
    public function hasTable(string $locator)
    {
        return $this->has('named', array('table', $locator));
    }

    /**
     * Attach file to file field with specified locator.
     *
     * @param string $locator input id, name or label
     * @param string $path    path to file
     *
     * @return void
     *
     * @throws ElementNotFoundException
     *
     * @see NodeElement::attachFile
     */
    public function attachFileToField(string $locator, string $path)
    {
        $field = $this->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value', $locator);
        }

        $field->attachFile($path);
    }
}
