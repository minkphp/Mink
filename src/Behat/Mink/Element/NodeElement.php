<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Page element node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NodeElement extends TraversableElement
{
    private $xpath;

    /**
     * Initializes node element.
     *
     * @param string  $xpath   element xpath
     * @param Session $session session instance
     */
    public function __construct($xpath, Session $session)
    {
        $this->xpath = $xpath;

        parent::__construct($session);
    }

    /**
     * Returns XPath for handled element.
     *
     * @return string
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * Returns parent element to the current one.
     *
     * @return NodeElement
     */
    public function getParent()
    {
        return $this->find('xpath', '..');
    }

    /**
     * Returns current node tag name.
     *
     * The value is always returned in lowercase to allow an easy comparison.
     *
     * @return string
     */
    public function getTagName()
    {
        return strtolower($this->getDriver()->getTagName($this->getXpath()));
    }

    /**
     * Returns the value of the form field or option element.
     *
     * For checkbox fields, the value is a boolean indicating whether the checkbox is checked.
     * For radio buttons, the value is the value of the selected button in the radio group
     *      or null if no button is selected.
     * For single select boxes, the value is the value of the selected option.
     * For multiple select boxes, the value is an array of selected option values.
     * for file inputs, the return value is undefined given that browsers don't allow accessing
     *      the value of file inputs for security reasons. Some drivers may allow accessing the
     *      path of the file set in the field, but this is not required if it cannot be implemented.
     * For textarea elements and all textual fields, the value is the content of the field.
     * Form option elements, the value is the value of the option (the value attribute or the text
     *      content if the attribute is not set).
     *
     * Calling this method on other elements than form fields or option elements is not allowed.
     *
     * @return string|bool|array
     */
    public function getValue()
    {
        return $this->getDriver()->getValue($this->getXpath());
    }

    /**
     * Sets the value of the form field.
     *
     * Calling this method on other elements than form fields is not allowed.
     *
     * @param string|bool|array $value
     *
     * @see NodeElement::getValue for the format of the value for each type of field
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->getDriver()->setValue($this->getXpath(), $value);
        return $this;
    }

    /**
     * Checks whether element has attribute with specified name.
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasAttribute($name)
    {
        return null !== $this->getDriver()->getAttribute($this->getXpath(), $name);
    }

    /**
     * Returns specified attribute value.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute($name)
    {
        return $this->getDriver()->getAttribute($this->getXpath(), $name);
    }

    /**
     * Checks whether an element has a named CSS class
     *
     * @param string $className Name of the class
     *
     * @return boolean
     */
    public function hasClass($className)
    {
        if ($this->hasAttribute('class')) {
            return in_array($className, explode(' ', $this->getAttribute('class')));
        }

        return false;
    }

    /**
     * Clicks current node.
     *
     * @return $this
     */
    public function click()
    {
        $this->getDriver()->click($this->getXpath());
        return $this;
    }

    /**
     * Presses current button.
     *
     * @return $this
     */
    public function press()
    {
        return $this->click();
    }

    /**
     * Double-clicks current node.
     *
     * @return $this
     */
    public function doubleClick()
    {
        $this->getDriver()->doubleClick($this->getXpath());
        return $this;
    }

    /**
     * Right-clicks current node.
     *
     * @return $this
     */
    public function rightClick()
    {
        $this->getDriver()->rightClick($this->getXpath());
        return $this;
    }

    /**
     * Checks current node if it's a checkbox field.
     *
     * @return $this
     */
    public function check()
    {
        $this->getDriver()->check($this->getXpath());
        return $this;
    }

    /**
     * Unchecks current node if it's a checkbox field.
     *
     * @return $this
     */
    public function uncheck()
    {
        $this->getDriver()->uncheck($this->getXpath());
        return $this;
    }

    /**
     * Checks whether current node is checked if it's a checkbox or radio field.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @return Boolean
     */
    public function isChecked()
    {
        return (Boolean) $this->getDriver()->isChecked($this->getXpath());
    }

    /**
     * Selects specified option for select field or specified radio button in the group
     *
     * If the current node is a select box, this selects the option found by its value or
     * its text.
     * If the current node is a radio button, this selects the radio button with the given
     * value in the radio button group of the current node.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @param string  $option
     * @param Boolean $multiple whether the option should be added to the selection for multiple selects
     *
     * @return $this
     * @throws ElementNotFoundException when the option is not found in the select box
     */
    public function selectOption($option, $multiple = false)
    {
        if ('select' !== $this->getTagName()) {
            $this->getDriver()->selectOption($this->getXpath(), $option, $multiple);

            return $this;
        }

        $opt = $this->find('named', array(
            'option', $this->getSelectorsHandler()->xpathLiteral($option)
        ));

        if (null === $opt) {
            throw $this->elementNotFound('select option', 'value|text', $option);
        }

        $this->getDriver()->selectOption($this->getXpath(), $opt->getValue(), $multiple);
        return $this;
    }

    /**
     * Checks whether current node is selected if it's a option field.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @return Boolean
     */
    public function isSelected()
    {
        return (Boolean) $this->getDriver()->isSelected($this->getXpath());
    }

    /**
     * Attach file to current node if it's a file input.
     *
     * Calling this method on any other elements than file input is not allowed.
     *
     * @param string $path path to file (local)
     *
     * @return $this
     */
    public function attachFile($path)
    {
        $this->getDriver()->attachFile($this->getXpath(), $path);
        return $this;
    }

    /**
     * Checks whether current node is visible on page.
     *
     * @return Boolean
     */
    public function isVisible()
    {
        return (Boolean) $this->getDriver()->isVisible($this->getXpath());
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @return $this
     */
    public function mouseOver()
    {
        $this->getDriver()->mouseOver($this->getXpath());
        return $this;
    }

    /**
     * Drags current node onto other node.
     *
     * @param ElementInterface $destination other node
     *
     * @return $this
     */
    public function dragTo(ElementInterface $destination)
    {
        $this->getDriver()->dragTo($this->getXpath(), $destination->getXpath());
        return $this;
    }

    /**
     * Brings focus to element.
     *
     * @return $this
     */
    public function focus()
    {
        $this->getDriver()->focus($this->getXpath());
        return $this;
    }

    /**
     * Removes focus from element.
     * @return $this
     */
    public function blur()
    {
        $this->getDriver()->blur($this->getXpath());
        return $this;
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return $this
     */
    public function keyPress($char, $modifier = null)
    {
        $this->getDriver()->keyPress($this->getXpath(), $char, $modifier);
        return $this;
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return $this
     */
    public function keyDown($char, $modifier = null)
    {
        $this->getDriver()->keyDown($this->getXpath(), $char, $modifier);
        return $this;
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return $this
     */
    public function keyUp($char, $modifier = null)
    {
        $this->getDriver()->keyUp($this->getXpath(), $char, $modifier);
        return $this;
    }

    /**
     * Submits the form.
     *
     * Calling this method on anything else than form elements is not allowed.
     *
     * @return $this
     */
    public function submit()
    {
        $this->getDriver()->submitForm($this->getXpath());
        return $this;
    }
}
