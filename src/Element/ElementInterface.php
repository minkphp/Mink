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

/**
 * Element interface.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface ElementInterface
{
    /**
     * Returns XPath for handled element.
     *
     * @return string
     */
    public function getXpath();

    /**
     * Returns element's session.
     *
     * @return Session
     *
     * @deprecated Accessing the session from the element is deprecated as of 1.6 and will be impossible in 2.0.
     */
    public function getSession();

    /**
     * Checks whether element with specified selector exists inside the current element.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return bool
     *
     * @see ElementInterface::findAll for the supported selectors
     */
    public function has(string $selector, $locator);

    /**
     * Checks if an element still exists in the DOM.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Waits for a value to be available and returns it.
     *
     * A falsy value returned by the callback is considered not found and will
     * retry after some waiting time, until a value is found or the timeout is
     * reached.
     * When the timeout is reached, the falsy value of the last attempt is returned.
     *
     * @template T
     *
     * @param int|float           $timeout  Maximal allowed waiting time in seconds.
     * @param callable(static): T $callback Callback, which result is both used as waiting condition and returned.
     *                                      Will receive reference to `this element` as first argument.
     *
     * @return mixed
     *
     * @phpstan-return T
     */
    public function waitFor($timeout, callable $callback);

    /**
     * Finds first element with specified selector inside the current element.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement|null
     *
     * @see ElementInterface::findAll for the supported selectors
     */
    public function find(string $selector, $locator);

    /**
     * Finds all elements with specified selector inside the current element.
     *
     * Valid selector engines are named, xpath, css, named_partial and named_exact.
     *
     * 'named' is a pseudo selector engine which prefers an exact match but
     * will return a partial match if no exact match is found.
     * 'xpath' is a pseudo selector engine supported by SelectorsHandler.
     *
     * More selector engines can be registered in the SelectorsHandler.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement[]
     *
     * @see NamedSelector for the locators supported by the named selectors
     */
    public function findAll(string $selector, $locator);

    /**
     * Returns element text (inside tag).
     *
     * @return string
     */
    public function getText();

    /**
     * Returns element inner html.
     *
     * @return string
     */
    public function getHtml();
}
