<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\KeyModifier;
use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Page element node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NodeElement extends TraversableElement
{
    /**
     * @var string
     */
    private $xpath;

    /**
     * Initializes node element.
     *
     * @param string  $xpath   element xpath
     * @param Session $session session instance
     */
    public function __construct(string $xpath, Session $session)
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
        $parent = $this->find('xpath', '..');

        if ($parent === null) {
            throw new DriverException('Could not find the element parent. Maybe the element has been removed from the page.');
        }

        return $parent;
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
     * @return string|bool|array|null
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
     * @return void
     *
     * @see NodeElement::getValue for the format of the value for each type of field
     */
    public function setValue($value)
    {
        $this->getDriver()->setValue($this->getXpath(), $value);
    }

    /**
     * Checks whether element has an attribute with specified name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name)
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
    public function getAttribute(string $name)
    {
        return $this->getDriver()->getAttribute($this->getXpath(), $name);
    }

    /**
     * Checks whether an element has a named CSS class.
     *
     * @param string $className Name of the class
     *
     * @return bool
     */
    public function hasClass(string $className)
    {
        $class = $this->getAttribute('class');

        if ($class === null) {
            return false;
        }

        $classes = preg_split('/\s+/', $class);

        if ($classes === false) {
            $classes = [];
        }

        return in_array($className, $classes);
    }

    /**
     * Clicks current node.
     *
     * @return void
     */
    public function click()
    {
        $this->getDriver()->click($this->getXpath());
    }

    /**
     * Presses current button.
     *
     * @return void
     */
    public function press()
    {
        $this->click();
    }

    /**
     * Double-clicks current node.
     *
     * @return void
     */
    public function doubleClick()
    {
        $this->getDriver()->doubleClick($this->getXpath());
    }

    /**
     * Right-clicks current node.
     *
     * @return void
     */
    public function rightClick()
    {
        $this->getDriver()->rightClick($this->getXpath());
    }

    /**
     * Checks current node if it's a checkbox field.
     *
     * @return void
     */
    public function check()
    {
        $this->getDriver()->check($this->getXpath());
    }

    /**
     * Unchecks current node if it's a checkbox field.
     *
     * @return void
     */
    public function uncheck()
    {
        $this->getDriver()->uncheck($this->getXpath());
    }

    /**
     * Checks whether current node is checked if it's a checkbox or radio field.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @return bool
     */
    public function isChecked()
    {
        return (bool) $this->getDriver()->isChecked($this->getXpath());
    }

    /**
     * Selects specified option for select field or specified radio button in the group.
     *
     * If the current node is a select box, this selects the option found by its value or
     * its text.
     * If the current node is a radio button, this selects the radio button with the given
     * value in the radio button group of the current node.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @param string $option
     * @param bool   $multiple whether the option should be added to the selection for multiple selects
     *
     * @return void
     *
     * @throws ElementNotFoundException when the option is not found in the select box
     */
    public function selectOption(string $option, bool $multiple = false)
    {
        if ('select' !== $this->getTagName()) {
            $this->getDriver()->selectOption($this->getXpath(), $option, $multiple);

            return;
        }

        $opt = $this->find('named', array('option', $option));

        if (null === $opt) {
            throw new ElementNotFoundException($this->getDriver(), 'select option', 'value|text', $option);
        }

        $optionValue = $opt->getValue();

        \assert(\is_string($optionValue), 'The value of an option element should always be a string.');

        $this->getDriver()->selectOption($this->getXpath(), $optionValue, $multiple);
    }

    /**
     * Checks whether current node is selected if it's a option field.
     *
     * Calling this method on any other elements is not allowed.
     *
     * @return bool
     */
    public function isSelected()
    {
        return (bool) $this->getDriver()->isSelected($this->getXpath());
    }

    /**
     * Attach file to current node if it's a file input.
     *
     * Calling this method on any other elements than file input is not allowed.
     *
     * @param string $path path to file (local)
     *
     * @return void
     */
    public function attachFile(string $path)
    {
        $this->getDriver()->attachFile($this->getXpath(), $path);
    }

    /**
     * Checks whether current node is visible on page.
     *
     * @return bool
     */
    public function isVisible()
    {
        return (bool) $this->getDriver()->isVisible($this->getXpath());
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @return void
     */
    public function mouseOver()
    {
        $this->getDriver()->mouseOver($this->getXpath());
    }

    /**
     * Drags current node onto other node.
     *
     * @param ElementInterface $destination other node
     *
     * @return void
     */
    public function dragTo(ElementInterface $destination)
    {
        $this->getDriver()->dragTo($this->getXpath(), $destination->getXpath());
    }

    /**
     * Brings focus to element.
     *
     * @return void
     */
    public function focus()
    {
        $this->getDriver()->focus($this->getXpath());
    }

    /**
     * Removes focus from element.
     *
     * @return void
     */
    public function blur()
    {
        $this->getDriver()->blur($this->getXpath());
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string|int          $char     could be either char ('b') or char-code (98)
     * @param KeyModifier::*|null $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return void
     */
    public function keyPress($char, ?string $modifier = null)
    {
        $this->getDriver()->keyPress($this->getXpath(), $char, $modifier);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string|int          $char     could be either char ('b') or char-code (98)
     * @param KeyModifier::*|null $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return void
     */
    public function keyDown($char, ?string $modifier = null)
    {
        $this->getDriver()->keyDown($this->getXpath(), $char, $modifier);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string|int          $char     could be either char ('b') or char-code (98)
     * @param KeyModifier::*|null $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @return void
     */
    public function keyUp($char, ?string $modifier = null)
    {
        $this->getDriver()->keyUp($this->getXpath(), $char, $modifier);
    }

    /**
     * Submits the form.
     *
     * Calling this method on anything else than form elements is not allowed.
     *
     * @return void
     */
    public function submit()
    {
        $this->getDriver()->submitForm($this->getXpath());
    }
}
