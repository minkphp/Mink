<?php

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// overwrite start_url parameter (this is a hack for Mink internal testing - use behat env params instead)
$world->setParameter('start_url', 'http://test.mink.loc/');

// redefine getPathTo method
$world->getPathTo = function($path) use($world) {
    return $world->getParameter('start_url') . $path;
};
