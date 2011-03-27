<?php

namespace Behat\Mink\Selector;

interface SelectorInterface
{
    function translateToXPath($locator);
}
