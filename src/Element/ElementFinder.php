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
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Selector\Xpath\Manipulator;

class ElementFinder
{
    private $driver;
    private $selectorsHandler;
    private $xpathManipulator;

    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler = null, Manipulator $xpathManipulator = null)
    {
        $this->driver = $driver;
        $this->selectorsHandler = $selectorsHandler ?: new SelectorsHandler();
        $this->xpathManipulator = $xpathManipulator ?: new Manipulator();
    }

    /**
     * @param string       $selector
     * @param string|array $locator
     * @param string       $parentXpath
     *
     * @return NodeElement[]
     */
    public function findAll($selector, $locator, $parentXpath)
    {
        if ('named' === $selector) {
            $items = $this->findAll('named_exact', $locator, $parentXpath);
            if (empty($items)) {
                $items = $this->findAll('named_partial', $locator, $parentXpath);
            }

            return $items;
        }

        $xpath = $this->selectorsHandler->selectorToXpath($selector, $locator);
        $xpath = $this->xpathManipulator->prepend($xpath, $parentXpath);

        $elements = array();

        foreach ($this->driver->find($xpath) as $elementXpath) {
            $elements[] = new NodeElement($elementXpath, $this->driver, $this);
        }

        return $elements;
    }
}
