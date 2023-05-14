<?php

namespace Behat\Mink\Tests\Selector;

use Behat\Mink\Selector\PartialNamedSelector;

class PartialNamedSelectorTest extends AbstractNamedSelector
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
