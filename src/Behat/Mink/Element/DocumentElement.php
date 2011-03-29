<?php

namespace Behat\Mink\Element;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Document element.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class DocumentElement extends ActionableElement
{
    /**
     * @see     Behat\Mink\Element\ElementInterface::getXpath()
     */
    public function getXpath()
    {
        return '/html';
    }

    /**
     * Returns document content.
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->getSession()->getDriver()->getContent();
    }

    /**
     * Check whether document has specified content.
     *
     * @param   string  $content
     *
     * @return  Boolean
     */
    public function hasContent($content)
    {
        return $this->hasSelector('named', array(
            'content', $this->getSession()->getSelectorsHandler()->xpathLiteral($content)
        ));
    }

    /**
     * Returns page text (inside HTML tag).
     *
     * @return  string|null
     */
    public function getText()
    {
        $html = $this->find('xpath', $this->getXpath());

        if (null !== $html) {
            return $html->getText();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findField($locator)
    {
        return $this->find('named', array(
            'field', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function findLink($locator)
    {
        return $this->find('named', array(
            'link', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function findButton($locator)
    {
        return $this->find('named', array(
            'button', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    /**
     * Finds element by it's id.
     *
     * @param   string  $id     element id
     *
     * @return  Behat\Mink\Element\NodeElement|null
     */
    public function findById($id)
    {
        $id = $this->getSession()->getSelectorsHandler()->xpathLiteral($id);

        return $this->find('xpath', "//*[@id=$id]");
    }

    /**
     * Checks whether document has a field (input, textarea, select) with specified locator.
     *
     * @param   string  $locator    input id, name or label
     *
     * @return  Boolean
     */
    public function hasField($locator)
    {
        return null !== $this->findField($locator);
    }

    /**
     * Checks whether document has a link with specified locator.
     *
     * @param   string  $locator    link id, title, text or image alt
     *
     * @return  Boolean
     */
    public function hasLink($locator)
    {
        return null !== $this->findLink($locator);
    }

    /**
     * Checks whether document has a button (input[type=submit|image|button], button) with specified locator.
     *
     * @param   string  $locator    button id, value or alt
     *
     * @return  Boolean
     */
    public function hasButton($locator)
    {
        return null !== $this->findButton($locator);
    }

    /**
     * Checks whether document has a checkbox with specified locator, which is checked.
     *
     * @param   string  $locator    input id, name or label
     *
     * @return  Boolean
     */
    public function hasCheckedField($locator)
    {
        $field = $this->findField($locator);

        return null !== $field && $field->isChecked();
    }

    /**
     * Checks whether document has a checkbox with specified locator, which is unchecked.
     *
     * @param   string  $locator    input id, name or label
     *
     * @return  Boolean
     */
    public function hasUncheckedField($locator)
    {
        $field = $this->findField($locator);

        return null !== $field && !$field->isChecked();
    }

    /**
     * Checks whether document has a select field with specified locator.
     *
     * @param   string  $locator    select id, name or label
     *
     * @return  Boolean
     */
    public function hasSelect($locator)
    {
        return $this->hasSelector('named', array(
            'select', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }

    /**
     * Checks whether document has a table with specified locator.
     *
     * @param   string  $locator    table id or caption
     *
     * @return  Boolean
     */
    public function hasTable($locator)
    {
        return $this->hasSelector('named', array(
            'table', $this->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));
    }
}
