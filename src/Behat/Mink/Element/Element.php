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

/**
 * Base element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Element implements ElementInterface
{
    /**
     * Driver.
     *
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ElementFinder
     */
    private $elementFinder;

    /**
     * Initialize element.
     *
     * @param DriverInterface  $driver
     * @param ElementFinder    $elementFinder
     */
    public function __construct(DriverInterface $driver, ElementFinder $elementFinder)
    {
        $this->driver = $driver;
        $this->elementFinder = $elementFinder;
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

        return count($items) ? $items[0] : null;
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
        return $this->elementFinder->findAll($selector, $locator, $this->getXpath());
    }

    /**
     * Returns element text (inside tag).
     *
     * @return string
     */
    public function getText()
    {
        return $this->getDriver()->getText($this->getXpath());
    }

    /**
     * Returns element html.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->getDriver()->getHtml($this->getXpath());
    }
}
