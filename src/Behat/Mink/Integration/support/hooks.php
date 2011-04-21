<?php

use Behat\Behat\Event\ScenarioEvent;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$hooks->beforeScenario('', function($event) {
    $scenario       = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
    $environment    = $event->getEnvironment();

    $driver = null;
    foreach ($scenario->getTags() as $tag) {
        if ('javascript' === $tag) {
            $driver = 'sahi';
        } elseif (preg_match('/^mink\:([^\n]+)/', $tag, $matches)) {
            $driver = $matches[1];
        }
    }
    if (null !== $driver) {
        $environment->getMink()->switchToDriver($driver);
    } else {
        $environment->getMink()->switchToDefaultDriver();
    }

    $environment->getMink()->resetDriver();
});
