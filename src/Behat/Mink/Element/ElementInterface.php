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
 * Element interface.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface ElementInterface
{
    /**
     * Returns XPath for handled element.
     *
     * @return  string
     */
    function getXpath();

    /**
     * Returns element's session.
     *
     * @return  Behat\Mink\Session
     */
    function getSession();

    /**
     * Finds first element with specified selector.
     *
     * @param   string  $selector   selector engine name
     * @param   string  $locator    selector locator
     *
     * @return  Behat\Mink\Element\NodeElement|null
     */
    function find($selector, $locator);

    /**
     * Finds all elements with specified selector.
     *
     * @param   string  $selector   selector engine name
     * @param   string  $locator    selector locator
     *
     * @return  array
     */
    function findAll($selector, $locator);

    /**
     * Checks whether element with specified selector exists.
     *
     * @param   string  $selector   selector engine name
     * @param   string  $locator    selector locator
     *
     * @return  Boolean
     */
    function hasSelector($selector, $locator);
}
