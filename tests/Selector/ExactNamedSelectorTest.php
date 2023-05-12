<?php

namespace Behat\Mink\Tests\Selector;

use Behat\Mink\Selector\ExactNamedSelector;
use PHPUnit\Framework\TestCase;

class ExactNamedSelectorTest extends TestCase
{
    use NamedSelectorTestTrait;

    protected function getSelector()
    {
        return new ExactNamedSelector();
    }

    protected function allowPartialMatch()
    {
        return false;
    }
}
