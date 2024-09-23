<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Driver;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;

/**
 * Core driver.
 * All other drivers should extend this class for future compatibility.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class CoreDriver implements DriverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        throw new UnsupportedDriverActionException('Starting the driver is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        throw new UnsupportedDriverActionException('Checking the driver state is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        throw new UnsupportedDriverActionException('Stopping the driver is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        throw new UnsupportedDriverActionException('Resetting the driver is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function visit(string $url)
    {
        throw new UnsupportedDriverActionException('Visiting an url is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl()
    {
        throw new UnsupportedDriverActionException('Getting the current url is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        throw new UnsupportedDriverActionException('Getting the page content is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $xpath)
    {
        $elements = array();

        foreach ($this->findElementXpaths($xpath) as $xpath) {
            $elements[] = new NodeElement($xpath, $this->session);
        }

        return $elements;
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @see find()
     *
     * @param string $xpath
     *
     * @return string[] The XPath of the matched elements
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     */
    protected function findElementXpaths(string $xpath)
    {
        throw new UnsupportedDriverActionException('Finding elements is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getTagName(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the tag name is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getText(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element text is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element inner HTML is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getOuterHtml(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element outer HTML is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $xpath, string $name)
    {
        throw new UnsupportedDriverActionException('Getting the element attribute is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the field value is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(string $xpath, string $value)
    {
        throw new UnsupportedDriverActionException('Setting the field value is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function check(string $xpath)
    {
        throw new UnsupportedDriverActionException('Checking a checkbox is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function uncheck(string $xpath)
    {
        throw new UnsupportedDriverActionException('Unchecking a checkbox is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function isChecked(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the state of a checkbox is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption(string $xpath, string $value, bool $multiple = false)
    {
        throw new UnsupportedDriverActionException('Selecting an option is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function click(string $xpath)
    {
        throw new UnsupportedDriverActionException('Clicking on an element is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function attachFile(string $xpath, string $path)
    {
        throw new UnsupportedDriverActionException('Attaching a file in an input is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        throw new UnsupportedDriverActionException('Page reloading is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function forward()
    {
        throw new UnsupportedDriverActionException('Forward action is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function back()
    {
        throw new UnsupportedDriverActionException('Backward action is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Basic auth setup is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function switchToWindow(?string $name = null)
    {
        throw new UnsupportedDriverActionException('Windows management is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function switchToIFrame(?string $name = null)
    {
        throw new UnsupportedDriverActionException('iFrames management is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestHeader(string $name, string $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers are not available from %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function setCookie(string $name, ?string $value = null)
    {
        throw new UnsupportedDriverActionException('Cookies manipulation is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookie(string $name)
    {
        throw new UnsupportedDriverActionException('Cookies are not available from %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code is not available from %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshot()
    {
        throw new UnsupportedDriverActionException('Screenshots are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getWindowNames()
    {
        throw new UnsupportedDriverActionException('Listing all window names is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getWindowName()
    {
        throw new UnsupportedDriverActionException('Listing this window name is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function doubleClick(string $xpath)
    {
        throw new UnsupportedDriverActionException('Double-clicking is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function rightClick(string $xpath)
    {
        throw new UnsupportedDriverActionException('Right-clicking is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible(string $xpath)
    {
        throw new UnsupportedDriverActionException('Element visibility check is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function isSelected(string $xpath)
    {
        throw new UnsupportedDriverActionException('Element selection check is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function mouseOver(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function focus(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function blur(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function keyPress(string $xpath, string $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function keyDown(string $xpath, string $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function keyUp(string $xpath, string $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function dragTo(string $sourceXpath, string $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function executeScript(string $script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluateScript(string $script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function wait(int $timeout, string $condition)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function resizeWindow(int $width, int $height, ?string $name = null)
    {
        throw new UnsupportedDriverActionException('Window resizing is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function maximizeWindow(?string $name = null)
    {
        throw new UnsupportedDriverActionException('Window maximize is not supported by %s', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(string $xpath)
    {
        throw new UnsupportedDriverActionException('Form submission is not supported by %s', $this);
    }
}

