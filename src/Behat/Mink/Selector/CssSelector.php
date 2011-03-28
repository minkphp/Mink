<?php

namespace Behat\Mink\Selector;

use Symfony\Component\CssSelector\Parser;

class CssSelector implements SelectorInterface
{
    public function translateToXPath($locator)
    {
        return Parser::cssToXpath($locator);
    }
}
