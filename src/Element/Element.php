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
use Behat\Mink\Session;

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
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->driver = $session->getDriver();
        $this->elementFinder = $session->getElementFinder();
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
    public function has(string $selector, $locator)
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

    public function waitFor($timeout, callable $callback)
    {
        $start = microtime(true);
        $end = $start + $timeout;

        do {
            $result = call_user_func($callback, $this);

            if ($result) {
                break;
            }

            usleep(10000);
        } while (microtime(true) < $end);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? current($items) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(string $selector, $locator)
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
