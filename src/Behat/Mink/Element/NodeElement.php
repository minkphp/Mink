<?php

namespace Behat\Mink\Element;

use Behat\Mink\Session,
    Behat\Mink\Driver\DriverInterface,
    Behat\Mink\Element\ElementInterface;

class NodeElement extends ActionableElement
{
    private $xpath;

    public function __construct($xpath, Session $session)
    {
        $this->xpath = $xpath;

        parent::__construct($session);
    }

    public function getXpath()
    {
        return $this->xpath;
    }

    public function getText()
    {
        return $this->getSession()->getDriver()->getText($this->getXpath());
    }

    public function getValue()
    {
        return $this->getSession()->getDriver()->getValue($this->getXpath());
    }

    public function setValue($value)
    {
        $this->getSession()->getDriver()->setValue($this->getXpath(), $value);
    }

    public function click()
    {
        $this->getSession()->getDriver()->click($this->getXpath());
    }

    public function getTagName()
    {
        return $this->getSession()->getDriver()->getTagName($this->getXpath());
    }

    public function isVisible()
    {
        return (Boolean) $this->getSession()->getDriver()->isVisible($this->getXpath());
    }

    public function isChecked()
    {
        return (Boolean) $this->getSession()->getDriver()->isChecked($this->getXpath());
    }

    public function triggerEvent($event)
    {
        $this->getSession()->getDriver()->triggerEvent($this->getXpath(), $event);
    }

    public function dragTo(ElementInterface $destination)
    {
        $this->getSession()->getDriver()->dragTo($this->getXpath(), $destination->getXpath());
    }
}
