<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Driver interface.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface DriverInterface
{
    /**
     * Sets driver's current session.
     *
     * @param   Behat\Mink\Session  $session
     */
    function setSession(Session $session);

    /**
     * Starts driver.
     */
    function start();

    /**
     * Checks whether driver is started.
     *
     * @return  Boolean
     */
    function isStarted();

    /**
     * Stops driver.
     */
    function stop();

    /**
     * Resets driver.
     */
    function reset();

    /**
     * Visit specified URL.
     *
     * @param   string  $url    url of the page
     */
    function visit($url);

    /**
     * Returns current URL address.
     *
     * @return  string
     */
    function getCurrentUrl();

    /**
     * Returns last response headers.
     *
     * @return  array
     */
    function getResponseHeaders();

    /**
     * Returns last response status code.
     *
     * @return  integer
     */
    function getStatusCode();

    /**
     * Returns last response content.
     *
     * @return  string
     */
    function getContent();

    /**
     * Finds elements with specified XPath query.
     *
     * @param   string  $xpath
     *
     * @return  array           array of Behat\Mink\Element\NodeElement
     */
    function find($xpath);

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getTagName($xpath);

    /**
     * Returns element's text by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getText($xpath);

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    function getAttribute($xpath, $attr);

    /**
     * Returns element's value by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    function getValue($xpath);

    /**
     * Sets element's value by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     */
    function setValue($xpath, $value);

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function check($xpath);

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function uncheck($xpath);

    /**
     * Checks whether checkbox checked located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isChecked($xpath);

    /**
     * Selects option from select field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     */
    function selectOption($xpath, $value);

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function click($xpath);

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function rightClick($xpath);

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $path
     */
    function attachFile($xpath, $path);

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isVisible($xpath);

    /**
     * Simulates a mouse over on the element.
     *
     * @param   string  $xpath
     */
    function mouseOver($xpath);

    /**
     * Brings focus to element.
     *
     * @param   string  $xpath
     */
    function focus($xpath);

    /**
     * Removes focus from element.
     *
     * @param   string  $xpath
     */
    function blur($xpath);

    /**
     * Trigger specific event on element located by XPath query.
     *
     * @param   string  $xpath
     * @param   string  $event  event name
     */
    function triggerEvent($xpath, $event);

    /**
     * Drag one element onto another.
     *
     * @param   string  $sourceXpath
     * @param   string  $destinationXpath
     */
    function dragTo($sourceXpath, $destinationXpath);

    /**
     * Executes JS script.
     *
     * @param   string  $script
     */
    function executeScript($script);

    /**
     * Evaluates JS script.
     *
     * @param   string  $script
     *
     * @return  mixed           script return value
     */
    function evaluateScript($script);

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param   integer $time       time in milliseconds
     * @param   string  $condition  JS condition
     */
    function wait($time, $condition);
}
