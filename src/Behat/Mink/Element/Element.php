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
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Selector\Xpath\Manipulator;
use Behat\Mink\Session;

/**
 * Base element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Element implements ElementInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * Driver.
     *
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var SelectorsHandler
     */
    private $selectorsHandler;

    /**
     * @var Manipulator
     */
    private $xpathManipulator;

    /**
     * Initialize element.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->xpathManipulator = new Manipulator();
        $this->session = $session;

        $this->driver = $session->getDriver();
        $this->selectorsHandler = $session->getSelectorsHandler();
    }

    /**
     * Returns element session.
     *
     * @return Session
     *
     * @deprecated Accessing the session from the element is deprecated as of 1.6 and will be impossible in 2.0.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Returns element's driver.
     *
     * @return DriverInterface
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns selectors handler.
     *
     * @return SelectorsHandler
     */
    protected function getSelectorsHandler()
    {
        return $this->selectorsHandler;
    }

    /**
     * Checks whether element with specified selector exists.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return Boolean
     */
    public function has($selector, $locator)
    {
        return null !== $this->find($selector, $locator);
    }

    /**
     * Checks if an element is still valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        return 1 === count($this->getDriver()->find($this->getXpath()));
    }

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
    public function waitFor($timeout, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = microtime(true);
        $end = $start + $timeout / 1000.0;

        do {
            $result = call_user_func($callback, $this);

            if ($result) {
                break;
            }

            usleep(100000);
        } while (microtime(true) < $end);

        return $result;
    }

    /**
     * Finds first element with specified selector.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement|null
     */
    public function find($selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? current($items) : null;
    }

    /**
     * Finds all elements with specified selector.
     *
     * Valid selector engines are named, xpath, css, named_partial and named_exact.
     *
     * 'named' is a pseudo selector engine which prefers an exact match but
     * will return a partial match if no exact match is found.
     *
     * 'xpath' is a pseudo selector engine supported by SelectorsHandler.
     *
     * Full selector engines implement SelectorInterface and are instantiated
     * by a SelectorsHandler.
     *
     * @param string       $selector selector engine name
     * @param string|array $locator  selector locator
     *
     * @return NodeElement[]
     */
    public function findAll($selector, $locator)
    {
        if ('named' === $selector) {
            $items = $this->findAll('named_exact', $locator);
            if (empty($items)) {
                $items = $this->findAll('named_partial', $locator);
            }

            return $items;
        }

        $xpath = $this->getSelectorsHandler()->selectorToXpath($selector, $locator);
        $xpath = $this->xpathManipulator->prepend($xpath, $this->getXpath());

        return $this->getDriver()->find($xpath);
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return $this->getDriver()->getText($this->getXpath());
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        return $this->getDriver()->getHtml($this->getXpath());
    }

    /**
     * Returns element outer html.
     *
     * @return string
     */
    public function getOuterHtml()
    {
        return $this->getDriver()->getOuterHtml($this->getXpath());
    }

    /**
     * Builds an ElementNotFoundException
     *
     * This is an helper to build the ElementNotFoundException without
     * needing to use the deprecated getSession accessor in child classes.
     *
     * @param string      $type
     * @param string|null $selector
     * @param string|null $locator
     *
     * @return ElementNotFoundException
     */
    protected function elementNotFound($type, $selector = null, $locator = null)
    {
        return new ElementNotFoundException($this->session, $type, $selector, $locator);
    }
}
