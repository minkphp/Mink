<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Element;

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
     * Checks whether element with specified selector exists.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return Boolean
     */
    public function has($selector, $locator);

    /**
     * Checks if an element is still valid.
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Waits for an element(-s) to appear and returns it.
     *
     * @param int      $timeout  Maximal allowed waiting time in milliseconds.
     * @param callable $callback Callback, which result is both used as waiting condition and returned.
     *                           Will receive reference to `this element` as first argument.
     *
     * @return mixed
     * @throws \InvalidArgumentException When invalid callback given.
     */
    public function waitFor($timeout, $callback);

    /**
     * Finds first element with specified selector.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement|null
     */
    public function find($selector, $locator);

    /**
     * Finds all elements with specified selector.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement[]
     */
    public function findAll($selector, $locator);

    /**
     * Returns element text (inside tag).
     *
     * @return string
     */
    public function getText();

    /**
     * Returns element html.
     *
     * @return string
     */
    public function getHtml();
}
