<?php

namespace Behat\Mink\Element;

use Behat\Mink\Session,
    Behat\Mink\Driver\DriverInterface;

abstract class Element implements ElementInterface
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function find($selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? current($items) : null;
    }

    public function findAll($selector, $locator)
    {
        return $this->getSession()->getDriver()->find(
            $this->getSession()->getSelectorsHandler()->selectorToXpath($selector, $locator)
        );
    }

    public function findField($locator)
    {
        return $this->find('named', array(
            'field', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    public function findLink($locator)
    {
        return $this->find('named', array(
            'link', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    public function findButton($locator)
    {
        return $this->find('named', array(
            'button', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    public function findById($id)
    {
        $id = $this->getSession()->getSelectorsHandler()->xpathLiteral($id);

        return $this->find('xpath', "//*[@id=$id]");
    }

    public function hasSelector($selector, $locator)
    {
        return null !== $this->find($selector, $locator);
    }

    public function hasContent($content)
    {
        return $this->hasSelector('named', array(
            'content', $this->getSession()->getSelectorsHandler()->xpathLiteral($content)
        ));
    }

    public function hasLink($locator)
    {
        return null !== $this->findLink($locator);
    }

    public function hasButton($locator)
    {
        return null !== $this->findButton($locator);
    }

    public function hasField($locator)
    {
        return null !== $this->findField($locator);
    }

    public function hasCheckedField($locator)
    {
        $field = $this->findField($locator);

        return null !== $field && $field->isChecked();
    }

    public function hasUncheckedField($locator)
    {
        $field = $this->findField($locator);

        return null !== $field && !$field->isChecked();
    }

    public function hasSelect($locator)
    {
        return $this->hasSelector('named', array(
            'select', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    public function hasTable($locator)
    {
        return $this->hasSelector('named', array(
            'table', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }
}
