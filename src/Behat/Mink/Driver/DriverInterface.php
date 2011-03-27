<?php

namespace Behat\Mink\Driver;

interface DriverInterface
{
    function visit($url);
    function reset();

    function getCurrentPath();
    function getCurrentUrl();

    function getResponseHeaders();
    function getStatusCode();
    function getContent();

    function executeScript($script);
    function evaluateScript($script);

    function find($xpath);
    function getTagName($xpath);
    function getText($xpath);
    function getValue($xpath);
    function setValue($xpath, $value);

    function fill($xpath, $value);
    function choose($xpath);
    function check($xpath);
    function uncheck($xpath);
    function selectOption($xpath, $value);
    function click($xpath);

    function isChecked($xpath);
    function isVisible($xpath);
    function isSelected($xpath);

    function attachFile($xpath, $path);
    function triggerEvent($xpath, $event);
    function dragTo($sourceXpath, $destinationXpath);
}
