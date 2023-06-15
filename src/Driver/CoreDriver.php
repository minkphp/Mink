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
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\KeyModifier;
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
     * @return void
     *
     * @final since 1.11
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return void
     *
     * @throws DriverException When the driver cannot be started
     */
    public function start()
    {
        throw new UnsupportedDriverActionException('Starting the driver is not supported by %s', $this);
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        throw new UnsupportedDriverActionException('Checking the driver state is not supported by %s', $this);
    }

    /**
     * @return void
     *
     * @throws DriverException When the driver cannot be closed
     */
    public function stop()
    {
        throw new UnsupportedDriverActionException('Stopping the driver is not supported by %s', $this);
    }

    /**
     * @return void
     */
    public function reset()
    {
        throw new UnsupportedDriverActionException('Resetting the driver is not supported by %s', $this);
    }

    /**
     * @param string $url
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function visit(string $url)
    {
        throw new UnsupportedDriverActionException('Visiting an url is not supported by %s', $this);
    }

    /**
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCurrentUrl()
    {
        throw new UnsupportedDriverActionException('Getting the current url is not supported by %s', $this);
    }

    /**
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function reload()
    {
        throw new UnsupportedDriverActionException('Page reloading is not supported by %s', $this);
    }

    /**
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function forward()
    {
        throw new UnsupportedDriverActionException('Forward action is not supported by %s', $this);
    }

    /**
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function back()
    {
        throw new UnsupportedDriverActionException('Backward action is not supported by %s', $this);
    }

    /**
     * @param string|false $user
     * @param string       $password
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setBasicAuth($user, string $password)
    {
        throw new UnsupportedDriverActionException('Basic auth setup is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToWindow(?string $name = null)
    {
        throw new UnsupportedDriverActionException('Windows management is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToIFrame(?string $name = null)
    {
        throw new UnsupportedDriverActionException('iFrames management is not supported by %s', $this);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setRequestHeader(string $name, string $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * @return array<string, string|string[]>
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers are not available from %s', $this);
    }

    /**
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setCookie(string $name, ?string $value = null)
    {
        throw new UnsupportedDriverActionException('Cookies manipulation is not supported by %s', $this);
    }

    /**
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCookie(string $name)
    {
        throw new UnsupportedDriverActionException('Cookies are not available from %s', $this);
    }

    /**
     * @return int
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code is not available from %s', $this);
    }

    /**
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getContent()
    {
        throw new UnsupportedDriverActionException('Getting the page content is not supported by %s', $this);
    }

    /**
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getScreenshot()
    {
        throw new UnsupportedDriverActionException('Screenshots are not supported by %s', $this);
    }

    /**
     * @return string[]
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowNames()
    {
        throw new UnsupportedDriverActionException('Listing all window names is not supported by %s', $this);
    }

    /**
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowName()
    {
        throw new UnsupportedDriverActionException('Listing this window name is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return NodeElement[]
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @final since 1.11
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
     * @throws DriverException                  When the operation cannot be done
     */
    protected function findElementXpaths(string $xpath)
    {
        throw new UnsupportedDriverActionException('Finding elements is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getTagName(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the tag name is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getText(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element text is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getHtml(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element inner HTML is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getOuterHtml(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the element outer HTML is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getAttribute(string $xpath, string $name)
    {
        throw new UnsupportedDriverActionException('Getting the element attribute is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return string|bool|array|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getValue(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the field value is not supported by %s', $this);
    }

    /**
     * @param string            $xpath
     * @param string|bool|array $value
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setValue(string $xpath, $value)
    {
        throw new UnsupportedDriverActionException('Setting the field value is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function check(string $xpath)
    {
        throw new UnsupportedDriverActionException('Checking a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function uncheck(string $xpath)
    {
        throw new UnsupportedDriverActionException('Unchecking a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isChecked(string $xpath)
    {
        throw new UnsupportedDriverActionException('Getting the state of a checkbox is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $value
     * @param bool   $multiple
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function selectOption(string $xpath, string $value, bool $multiple = false)
    {
        throw new UnsupportedDriverActionException('Selecting an option is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isSelected(string $xpath)
    {
        throw new UnsupportedDriverActionException('Element selection check is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function click(string $xpath)
    {
        throw new UnsupportedDriverActionException('Clicking on an element is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function doubleClick(string $xpath)
    {
        throw new UnsupportedDriverActionException('Double-clicking is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function rightClick(string $xpath)
    {
        throw new UnsupportedDriverActionException('Right-clicking is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     * @param string $path
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function attachFile(string $xpath, string $path)
    {
        throw new UnsupportedDriverActionException('Attaching a file in an input is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isVisible(string $xpath)
    {
        throw new UnsupportedDriverActionException('Element visibility check is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function mouseOver(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function focus(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function blur(string $xpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string              $xpath
     * @param string|int          $char
     * @param KeyModifier::*|null $modifier
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyPress(string $xpath, $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string              $xpath
     * @param string|int          $char
     * @param KeyModifier::*|null $modifier
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyDown(string $xpath, $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string              $xpath
     * @param string|int          $char
     * @param KeyModifier::*|null $modifier
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyUp(string $xpath, $char, ?string $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard manipulations are not supported by %s', $this);
    }

    /**
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function dragTo(string $sourceXpath, string $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Mouse manipulations are not supported by %s', $this);
    }

    /**
     * @param string $script
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function executeScript(string $script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param string $script
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function evaluateScript(string $script)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param int    $timeout
     * @param string $condition
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function wait(int $timeout, string $condition)
    {
        throw new UnsupportedDriverActionException('JS is not supported by %s', $this);
    }

    /**
     * @param int         $width
     * @param int         $height
     * @param string|null $name
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function resizeWindow(int $width, int $height, ?string $name = null)
    {
        throw new UnsupportedDriverActionException('Window resizing is not supported by %s', $this);
    }

    /**
     * @param string|null $name
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function maximizeWindow(?string $name = null)
    {
        throw new UnsupportedDriverActionException('Window maximize is not supported by %s', $this);
    }

    /**
     * @param string $xpath
     *
     * @return void
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function submitForm(string $xpath)
    {
        throw new UnsupportedDriverActionException('Form submission is not supported by %s', $this);
    }
}
