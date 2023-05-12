<?php

namespace Behat\Mink\Tests\Selector;

use Behat\Mink\Selector\PartialNamedSelector;
use PHPUnit\Framework\TestCase;

class PartialNamedSelectorTest extends TestCase
{
    use NamedSelectorTestTrait;

    protected function getSelector()
    {
        return new PartialNamedSelector();
    }

    protected function allowPartialMatch()
    {
        return true;
    }
}
