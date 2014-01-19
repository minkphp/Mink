<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\PartialSelector;

require_once __DIR__ . '/NamedSelectorTest.php';

/**
 * @group unittest
 */
class PartialSelectorTest extends NamedSelectorTest
{
    protected function getSelector()
    {
        return new PartialSelector();
    }

    protected function allowPartialMatch()
    {
        return true;
    }
}
