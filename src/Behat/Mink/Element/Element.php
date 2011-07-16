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
        return $this->getSession()->getDriver()->find(
            $this->getSession()->getSelectorsHandler()->selectorToXpath($selector, $locator)
        );
    }

    /**
     * Returns element text.
     *
     * @return  string|null
     */
    abstract public function getText();

    /**
     * Returns element text with trimmed tags and non-printable chars.
     *
     * @return  string|null
     */
    public function getPlainText()
    {
        $text = $this->getText();

        if (null !== $text) {
            $text = str_replace("\n", ' ', $text);
            $text = preg_replace('/\<br *\/?\>/i', "\n", $text);
            $text = strip_tags($text);
            $text = preg_replace('/ {2,}/', ' ', $text);

            return $text;
        }
    }
}
