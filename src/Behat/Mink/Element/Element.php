<?php

namespace Behat\Mink\Element;

use Behat\Mink\Session;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base element.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Element implements ElementInterface
{
    private $session;

    /**
     * Initialize element.
     *
     * @param   Behat\Mink\Session  $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Returns element session.
     *
     * @return  Behat\Mink\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::findAll()
     */
    public function has($selector, $locator)
    {
        return null !== $this->find($selector, $locator);
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::find()
     */
    public function find($selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? current($items) : null;
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::findAll()
     */
    public function findAll($selector, $locator)
    {
        $xpath = $this->getSession()->getSelectorsHandler()->selectorToXpath($selector, $locator);

        // add parent xpath before element selector
        if (0 === strpos($xpath, '/')) {
            $xpath = $this->getXpath().$xpath;
        } else {
            $xpath = $this->getXpath().'/'.$xpath;
        }

        return $this->getSession()->getDriver()->find($xpath);
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::getText()
     */
    public function getText()
    {
        return $this->getSession()->getDriver()->getText($this->getXpath());
    }

    /**
     * @see     Behat\Mink\Element\ElementInterface::getHtml()
     */
    public function getHtml()
    {
        return $this->getSession()->getDriver()->getHtml($this->getXpath());
    }
}
