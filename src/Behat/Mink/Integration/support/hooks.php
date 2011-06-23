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
    $context  = $event->getContext();
    $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
    $session  = $context->parameters['default_session'];

    foreach ($scenario->getTags() as $tag) {
        if ('javascript' === $tag) {
            $session = 'sahi';
        } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
            $session = $matches[1];
        }
    }

    $context->getMink()->setDefaultSessionName($session);
});
