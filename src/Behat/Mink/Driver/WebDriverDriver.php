<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException;

use WebDriver\WebDriver;

/*
 * (C) Zimride, Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * WebDriver (JS) driver.
 *
 * @author Mathias Gug <mathias@zimride.com>
 */
class WebDriverDriver implements DriverInterface
{
    private $browserName = null;
    private $webDriverClient = null;
    private $webDriverSession = null;
    private $session;

    /**
     * Initializes WebDriver driver.
     *
     * @param string $browserName browser to start (firefox, safari, ie, etc...)
     */
    public function __construct($browserName)
    {
        $this->webDriverClient = new WebDriver();
        $this->browserName = $browserName;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setSession()
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::start()
     */
    public function start()
    {
        $this->webDriverSession = $this->webDriverClient
                                       ->session($this->browserName);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isStarted()
     */
    public function isStarted()
    {
        return !is_null($this->webDriverSession);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::stop()
     */
    public function stop()
    {
        $this->webDriverSession->close();
        $this->webDriverSession = null;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reset()
     */
    public function reset()
    {
        $this->webDriverSession->deleteAllCookies();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::visit()
     */
    public function visit($url)
    {
        $this->webDriverSession->open($url);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCurrentUrl()
     */
    public function getCurrentUrl()
    {
        return $this->webDriverSession->url();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reload()
     */
    public function reload()
    {
        $this->webDriverSession->refresh();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::forward()
     */
    public function forward()
    {
        $this->webDriverSession->forward();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::back()
     */
    public function back()
    {
        $this->webDriverSession->back();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setBasicAuth()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setRequestHeader()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getResponseHeaders()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setCookie()
     */
    public function setCookie($name, $value = null)
    {
        $this->webDriverSession->setCookie(array("name" => $name,
                                                 "value" => $value));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCookie()
     */
    public function getCookie($name)
    {
        $allCookies = $this->webDriverSession->getAllCookies();
        $myCookie = null;
        foreach ( $allCookies['value'] as $cookie ) {
            if ($cookie['name'] === $name) {
                $myCookie = $cookie;
                break;
            }
        }
        return $myCookie;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getStatusCode()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getContent()
     */
    public function getContent()
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::find()
     */
    public function find($xpath)
    {
        $nodes = $this->webDriverSession->elements('xpath', $xpath);
        $elements = array();
        foreach ($nodes as $i => $node) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i + 1), 
                                          $this->session);
        }
        return $elements;
   }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getTagName()
     */
    public function getTagName($xpath)
    {
        return $this->webDriverSession->element('xpath', $xpath)->name();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getText()
     */
    public function getText($xpath)
    {
        $text = $this->webDriverSession->element('xpath', $xpath)->text();
        $text = str_replace("\n", ' ', $text);
        $text = preg_replace('/ {2,}/', ' ', $text);
        return $text;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getHtml()
     */
    public function getHtml($xpath)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getAttribute()
     */
    public function getAttribute($xpath, $name)
    {
        $attr = $this->webDriverSession->element('xpath', $xpath)
            ->attribute($name);
        if ($attr == '') {
            $attr = null;    
        };
        return $attr;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getValue()
     */
    public function getValue($xpath)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setValue()
     */
    public function setValue($xpath, $value)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::check()
     */
    public function check($xpath)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::uncheck()
     */
    public function uncheck($xpath)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isChecked()
     */
    public function isChecked($xpath)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::selectOption()
     */
    public function selectOption($xpath, $value)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::click()
     */
    public function click($xpath)
    {
        $this->webDriverSession->element('xpath', $xpath)
                               ->click();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::doubleClick()
     */
    public function doubleClick($xpath)
    {
        $this->mouseOver($xpath);
        $this->webDriverSession->doubleclick();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::rightClick()
     */
    public function rightClick($xpath)
    {
        $this->mouseOver($xpath);
        $this->webDriverSession->click(array("button" => 2));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::attachFile()
     */
    public function attachFile($xpath, $path)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isVisible()
     */
    public function isVisible($xpath)
    {
        return $this->webDriverSession->element('xpath', $xpath)->displayed();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::mouseOver()
     */
    public function mouseOver($xpath)
    {
        $element = $this->webDriverSession->element('xpath', $xpath);
        $this->webDriverSession->moveto(array("element" => $element->getID()));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::focus()
     */
    public function focus($xpath)
    {
        throw new UnsupportedDriverActionException('Focus actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::blur()
     */
    public function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Focus actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::dragTo()
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $sourceElement = $this->webDriverSession->element('xpath', 
                                                          $sourceXpath);
        $this->webDriverSession->moveto(array("element" => $sourceElement->getID()));
        $this->webDriverSession->buttondown();
        $destinationElement = $this->webDriverSession->element('xpath', 
                                                            $destinationXpath);
        $this->webDriverSession->moveto(array("element" => $destinationElement->getID()));
        $this->webDriverSession->buttonup();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::executeScript()
     */
    public function executeScript($script)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::evaluateScript()
     */
    public function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::wait()
     */
    public function wait($time, $condition)
    {
        // support $time
        // NB: execute is synchronous
        $conditionResult = $this->webDriverSession->execute($condition);
    }

    /**
     * Selects specific radio option.
     *
     * @param   string  $xpath  xpath to one of the radio buttons
     * @param   string  $value  value to be set
     */
    private function selectRadioOption($xpath, $value)
    {
        throw new UnsupportedDriverActionException('', $this);
    }

}
