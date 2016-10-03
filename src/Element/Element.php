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
     * @param DriverInterface $driver
     * @param ElementFinder   $elementFinder
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
     * {@inheritdoc}
     */
    public function has($selector, $locator)
    {
        return null !== $this->find($selector, $locator);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return 1 === count($this->getDriver()->find($this->getXpath()));
    }

    /**
     * {@inheritdoc}
     */
    public function waitFor($timeout, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = microtime(true);
        $end = $start + $timeout;

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
     * {@inheritdoc}
     */
    public function find($selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? $items[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($selector, $locator)
    {
        return $this->elementFinder->findAll($selector, $locator, $this->getXpath());
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
}
