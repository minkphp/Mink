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

/**
 * @final
 * @internal
 */
class ElementFinder
{
    /**
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

    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler, ?Manipulator $xpathManipulator = null)
    {
        $this->driver = $driver;
        $this->selectorsHandler = $selectorsHandler;
        $this->xpathManipulator = $xpathManipulator ?? new Manipulator();
    }

    /**
     * @param string|array $locator
     *
     * @return NodeElement[]
     */
    public function findAll(string $selector, $locator, string $parentXpath)
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

        return $this->driver->find($xpath);
    }
}
