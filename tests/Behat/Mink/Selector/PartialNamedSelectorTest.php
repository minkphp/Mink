<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\PartialNamedSelector;

require_once __DIR__ . '/NamedSelectorTest.php';

/**
 * @group unittest
 */
class PartialNamedSelectorTest extends NamedSelectorTest
{
    protected function getSelector()
    {
        return new PartialNamedSelector();
    }

    protected function allowPartialMatch()
    {
        return true;
    }
}
