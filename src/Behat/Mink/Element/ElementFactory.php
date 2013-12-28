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

/**
 * Factory for element node.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ElementFactory
{
    public function createNodeElement($xpath, DriverInterface $driver, SelectorsHandler $selectorsHandler)
    {
        return new NodeElement($xpath, $driver, $selectorsHandler, $this);
    }
}
