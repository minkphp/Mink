<?php

namespace Behat\Mink\Element;

interface ElementInterface
{
    function getXpath();
    function getSession();
    function find($selector, $locator);
    function findAll($selector, $locator);
}
