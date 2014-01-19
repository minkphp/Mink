<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Exception\ElementException;
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
     * @param string           $xpath element xpath
     * @param DriverInterface  $driver
     * @param ElementFinder    $elementFinder
     */
    public function __construct($xpath, DriverInterface $driver, ElementFinder $elementFinder)
    {
        $this->xpath = $xpath;

        parent::__construct($driver, $elementFinder);
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
     * @return string
     */
    public function getTagName()
    {
        return strtolower($this->getDriver()->getTagName($this->getXpath()));
    }

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getDriver()->getValue($this->getXpath());
    }

    /**
     * Sets node value.
     *
     * @param string $value
     *
     * @throws ElementException When an error occurred, while setting new element value.
     */
    public function setValue($value)
    {
        $this->getDriver()->setValue($this->getXpath(), $value);
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
     */
    public function click()
    {
        $this->getDriver()->click($this->getXpath());
    }

    /**
     * Presses current button.
     */
    public function press()
    {
        $this->click();
    }

    /**
     * Double-clicks current node.
     */
    public function doubleClick()
    {
        $this->getDriver()->doubleClick($this->getXpath());
    }

    /**
     * Right-clicks current node.
     */
    public function rightClick()
    {
        $this->getDriver()->rightClick($this->getXpath());
    }

    /**
     * Checks current node if it's a checkbox field.
     */
    public function check()
    {
        $this->getDriver()->check($this->getXpath());
    }

    /**
     * Unchecks current node if it's a checkbox field.
     */
    public function uncheck()
    {
        $this->getDriver()->uncheck($this->getXpath());
    }

    /**
     * Checks whether current node is checked if it's a checkbox field.
     *
     * @return Boolean
     */
    public function isChecked()
    {
        return (Boolean) $this->getDriver()->isChecked($this->getXpath());
    }

    /**
     * Selects current node specified option if it's a select field.
     *
     * @param string  $option
     * @param Boolean $multiple
     *
     * @throws ElementNotFoundException
     */
    public function selectOption($option, $multiple = false)
    {
        if ('select' !== $this->getTagName()) {
            $this->getDriver()->selectOption($this->getXpath(), $option, $multiple);

            return;
        }

        $opt = $this->find('named', array('option', $option));

        if (null === $opt) {
            throw new ElementNotFoundException($this->getDriver(), 'select option', 'value|text', $option);
        }

        $this->getDriver()->selectOption($this->getXpath(), $opt->getValue(), $multiple);
    }

    /**
     * Checks whether current node is selected if it's a option field.
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
     * @param string $path path to file (local)
     *
     * @throws ElementException When an error occurred, when attaching a file.
     */
    public function attachFile($path)
    {
        $this->getDriver()->attachFile($this->getXpath(), $path);
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
     */
    public function mouseOver()
    {
        $this->getDriver()->mouseOver($this->getXpath());
    }

    /**
     * Drags current node onto other node.
     *
     * @param ElementInterface $destination other node
     */
    public function dragTo(ElementInterface $destination)
    {
        $this->getDriver()->dragTo($this->getXpath(), $destination->getXpath());
    }

    /**
     * Brings focus to element.
     */
    public function focus()
    {
        $this->getDriver()->focus($this->getXpath());
    }

    /**
     * Removes focus from element.
     */
    public function blur()
    {
        $this->getDriver()->blur($this->getXpath());
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyPress($char, $modifier = null)
    {
        $this->getDriver()->keyPress($this->getXpath(), $char, $modifier);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyDown($char, $modifier = null)
    {
        $this->getDriver()->keyDown($this->getXpath(), $char, $modifier);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string|integer $char     could be either char ('b') or char-code (98)
     * @param string         $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyUp($char, $modifier = null)
    {
        $this->getDriver()->keyUp($this->getXpath(), $char, $modifier);
    }

    /**
     * Submits the form.
     */
    public function submit()
    {
        $this->getDriver()->submitForm($this->getXpath());
    }
}
