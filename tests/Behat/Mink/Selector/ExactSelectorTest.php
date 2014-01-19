<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\ExactSelector;

require_once __DIR__ . '/NamedSelectorTest.php';

/**
 * @group unittest
 */
class ExactSelectorTest extends NamedSelectorTest
{
    protected function getSelector()
    {
        return new ExactSelector();
    }

    protected function allowPartialMatch()
    {
        return false;
    }
}
