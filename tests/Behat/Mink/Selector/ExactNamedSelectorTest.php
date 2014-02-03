<?php

namespace Tests\Behat\Mink\Selector;

use Behat\Mink\Selector\ExactNamedSelector;

require_once __DIR__ . '/NamedSelectorTest.php';

/**
 * @group unittest
 */
class ExactNamedSelectorTest extends NamedSelectorTest
{
    protected function getSelector()
    {
        return new ExactNamedSelector();
    }

    protected function allowPartialMatch()
    {
        return false;
    }
}
