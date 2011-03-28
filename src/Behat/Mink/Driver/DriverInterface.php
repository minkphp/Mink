<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session;

interface DriverInterface
{
    function setSession(Session $session);

    function visit($url);
    function reset();
    function getCurrentUrl();

    function getResponseHeaders();
    function getStatusCode();
    function getContent();

    function executeScript($script);
    function evaluateScript($script);

    function find($xpath);
    function getTagName($xpath);
    function getText($xpath);
    function getAttribute($xpath, $attr);
    function getValue($xpath);
    function setValue($xpath, $value);

    function check($xpath);
    function uncheck($xpath);
    function selectOption($xpath, $value);
    function click($xpath);

    function isChecked($xpath);
    function isVisible($xpath);

    function attachFile($xpath, $path);
    function triggerEvent($xpath, $event);
    function dragTo($sourceXpath, $destinationXpath);
}
