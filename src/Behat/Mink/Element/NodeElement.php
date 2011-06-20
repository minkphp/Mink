<?php

namespace Behat\Mink\Element;

use Behat\Mink\Session,
    Behat\Mink\Driver\DriverInterface,
    Behat\Mink\Element\ElementInterface;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Page node element.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NodeElement extends Element
{
    private $xpath;

    /**
     * Initializes node element.
     *
     * @param   string                  $xpath      element xpath
     * @param   Behat\Mink\Session      $session    session instance
     */
    public function __construct($xpath, Session $session)
    {
        $this->xpath = $xpath;

        parent::__construct($session);
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::getXpath()
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * Returns element text (inside tag).
     *
     * @return  string|null
     */
    public function getText()
    {
        return $this->getSession()->getDriver()->getText($this->getXpath());
    }

    /**
     * Returns element value.
     *
     * @return  mixed
     */
    public function getValue()
    {
        return $this->getSession()->getDriver()->getValue($this->getXpath());
    }

    /**
     * Checks whether element has attribute with specified name.
     *
     * @param   string  $name
     */
    public function hasAttribute($name)
    {
        return null !== $this->getSession()->getDriver()->getAttribute($this->getXpath(), $name);
    }

    /**
     * Returns specified attribute value.
     *
     * @param   string  $name
     *
     * @return  mixed|null
     */
    public function getAttribute($name)
    {
        return $this->getSession()->getDriver()->getAttribute($this->getXpath(), $name);
    }

    /**
     * Sets node value.
     *
     * @param   string  $value
     */
    public function setValue($value)
    {
        $this->getSession()->getDriver()->setValue($this->getXpath(), $value);
    }

    /**
     * Clicks current node.
     */
    public function click()
    {
        $this->getSession()->getDriver()->click($this->getXpath());
    }

    /**
     * Right-clicks current node.
     */
    public function rightClick()
    {
        $this->getSession()->getDriver()->rightClick($this->getXpath());
    }

    /**
     * Checks current node if it's a checkbox field.
     */
    public function check()
    {
        $this->getSession()->getDriver()->check($this->getXpath());
    }

    /**
     * Unchecks current node if it's a checkbox field.
     */
    public function uncheck()
    {
        $this->getSession()->getDriver()->uncheck($this->getXpath());
    }

    /**
     * Selects current node specified option if it's a select field.
     *
     * @param   string  $option
     */
    public function selectOption($option)
    {
        $this->getSession()->getDriver()->selectOption($this->getXpath(), $option);
    }

    /**
     * Attach file to current node if it's a file input.
     *
     * @param   string  $path   path to file (local)
     */
    public function attachFile($path)
    {
        $this->getSession()->getDriver()->attachFile($this->getXpath(), $path);
    }

    /**
     * Returns current node tag name.
     *
     * @return  string
     */
    public function getTagName()
    {
        return $this->getSession()->getDriver()->getTagName($this->getXpath());
    }

    /**
     * Checks whether current node is visible on page.
     *
     * @return  Boolean
     */
    public function isVisible()
    {
        return (Boolean) $this->getSession()->getDriver()->isVisible($this->getXpath());
    }

    /**
     * Checks whether current node is checked if it's a checkbox field.
     *
     * @return  Boolean
     */
    public function isChecked()
    {
        return (Boolean) $this->getSession()->getDriver()->isChecked($this->getXpath());
    }

    /**
     * Brings focus to element.
     */
    public function focus()
    {
        $this->getSession()->getDriver()->focus($this->getXpath());
    }

    /**
     * Removes focus from element.
     */
    public function blur()
    {
        $this->getSession()->getDriver()->blur($this->getXpath());
    }

    /**
     * Simulates a mouse over on the element.
     */
    public function mouseOver()
    {
        $this->getSession()->getDriver()->mouseOver($this->getXpath());
    }

    /**
     * Triggers specific event on current node.
     *
     * @param   string  $event  event name
     */
    public function triggerEvent($event)
    {
        $this->getSession()->getDriver()->triggerEvent($this->getXpath(), $event);
    }

    /**
     * Drags current node onto other node.
     *
     * @param   ElementInterface    $destination    other node
     */
    public function dragTo(ElementInterface $destination)
    {
        $this->getSession()->getDriver()->dragTo($this->getXpath(), $destination->getXpath());
    }
}
