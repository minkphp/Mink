<?php

namespace Behat\Mink\Driver;

use Behat\SahiClient\Client,
    Behat\SahiClient\Exception\ConnectionException;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sahi (JS) driver.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SahiDriver implements DriverInterface
{
    private $started = false;
    private $browserName;
    private $client;
    private $session;

    /**
     * Initializes Sahi driver.
     *
     * @param   string                      $browserName    browser to start (firefox, safari, ie, etc...)
     * @param   Behat\SahiClient\Client     $client         Sahi client instance
     */
    public function __construct($browserName, Client $client = null)
    {
        if (null === $client) {
            $client = new Client();
        }

        $this->client      = $client;
        $this->browserName = $browserName;
    }

    /**
     * Returns Sahi client instance.
     *
     * @return  Behat\SahiClient\Client
     */
    public function getClient()
    {
        return $this->client;
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
        $this->client->start($this->browserName);
        $this->started = true;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isStarted()
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::stop()
     */
    public function stop()
    {
        $this->client->stop();
        $this->started = false;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reset()
     */
    public function reset()
    {
        try {
            $this->executeScript(
                '(function(){var c=document.cookie.split(";");for(var i=0;i<c.length;i++){var e=c[i].indexOf("=");var n=e>-1?c[i].substr(0,e):c[i];document.cookie=n+"=;expires=Thu, 01 Jan 1970 00:00:00 GMT";}})()'
            );
        } catch(\Exception $e) {}
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::visit()
     */
    public function visit($url)
    {
        $this->client->navigateTo($url, true);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCurrentUrl()
     */
    public function getCurrentUrl()
    {
        return $this->evaluateScript('document.URL');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reload()
     */
    public function reload()
    {
        $this->visit($this->getCurrentUrl());
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::forward()
     */
    public function forward()
    {
        $this->executeScript('history.forward()');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::back()
     */
    public function back()
    {
        $this->executeScript('history.back()');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setBasicAuth()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('HTTP Basic authentication is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setRequestHeader()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getResponseHeaders()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers manipulation is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setCookie()
     */
    public function setCookie($name, $value = null)
    {
        if (null === $value) {
            try {
                $this->executeScript(sprintf('_sahi._deleteCookie("%s")', $name));
            } catch (ConnectionException $e) {}
        } else {
            $value = str_replace('"', '\\"', $value);
            $this->executeScript(sprintf('_sahi._createCookie("%s", "%s")', $name, $value));
        }
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCookie()
     */
    public function getCookie($name)
    {
        try {
            return $this->evaluateScript(sprintf('_sahi._cookie("%s")', $name));
        } catch (ConnectionException $e) {}
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getStatusCode()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code reading is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getContent()
     */
    public function getContent()
    {
        $html = $this->evaluateScript('document.getElementsByTagName("html")[0].innerHTML');

        $html = preg_replace(array(
            '/<\!--SAHI_INJECT_START--\>.*\<\!--SAHI_INJECT_END--\>/sU',
            '/\<script\>\/\*\<\!\[CDATA\[\*\/\/\*----\>\*\/__sahi.*\<\!--SAHI_INJECT_END--\>/sU'
        ), '', $html);

        return "<html>\n$html\n</html>";
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::find()
     */
    public function find($xpath)
    {
        $function = <<<JS
(function(){
    var count = 0;
    while (_sahi._byXPath("({$xpath})["+(count+1)+"]")) count++;
    return count;
})()
JS;
        $count = intval($this->evaluateScript($function));
        $elements = array();
        for ($i = 0; $i < $count; $i++) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i + 1), $this->session);
        }

        return $elements;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getTagName()
     */
    public function getTagName($xpath)
    {
        return strtolower($this->client->findByXPath($this->prepareXPath($xpath))->getName());
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getText()
     */
    public function getText($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getText();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getHtml()
     */
    public function getHtml($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getHTML();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getAttribute()
     */
    public function getAttribute($xpath, $name)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getAttr($name);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getValue()
     */
    public function getValue($xpath)
    {
        $xpath = $this->prepareXPath($xpath);
        $tag   = $this->getTagName($xpath);
        $type  = $this->getAttribute($xpath, 'type');
        $value = null;

        if ('radio' === $type) {
            $name = $this->getAttribute($xpath, 'name');

            if (null !== $name) {
                $function = <<<JS
(function(){
    for (var i = 0; i < document.forms.length; i++) {
        if (document.forms[i].elements['{$name}']) {
            var form  = document.forms[i];
            var elements = form.elements['{$name}'];
            var value = elements[0].value;
            for (var f = 0; f < elements.length; f++) {
                var item = elements[f];
                if (item.checked) {
                    return item.value;
                }
            }
            return value;
        }
    }
    return null;
})()
JS;

                return $this->evaluateScript($function);
            }
        } elseif ('checkbox' === $type) {
            return $this->client->findByXPath($xpath)->isChecked();
        } elseif ('select' === $tag && 'multiple' === $this->getAttribute($xpath, 'multiple')) {
            $name = $this->getAttribute($xpath, 'name');

            $function = <<<JS
(function(){
    for (var i = 0; i < document.forms.length; i++) {
        if (document.forms[i].elements['{$name}']) {
            var form = document.forms[i];
            var node = form.elements['{$name}'];
            var options = [];
            for (var i = 0; i < node.options.length; i++) {
                if (node.options[ i ].selected) {
                    options.push(node.options[ i ].value);
                }
            }
            return options.join(",");
        }
    }
    return '';
})()
JS;
            $value = $this->evaluateScript($function);

            if ('' === $value) {
                return array();
            } else {
                return explode(',', $value);
            }
        }

        return $this->client->findByXPath($xpath)->getValue();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setValue()
     */
    public function setValue($xpath, $value)
    {
        $type = $this->getAttribute($xpath, 'type');

        if ('radio' === $type) {
            $this->selectRadioOption($xpath, $value);
        } elseif ('checkbox' === $type) {
            if ((Boolean) $value) {
                $this->client->findByXPath($this->prepareXPath($xpath))->check();
            } else {
                $this->client->findByXPath($this->prepareXPath($xpath))->uncheck();
            }
        } else {
            $this->client->findByXPath($this->prepareXPath($xpath))->setValue($value);
        }
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::check()
     */
    public function check($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->check();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::uncheck()
     */
    public function uncheck($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->uncheck();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isChecked()
     */
    public function isChecked($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isChecked();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::selectOption()
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        $type = $this->getAttribute($xpath, 'type');

        if ('radio' === $type) {
            $this->selectRadioOption($xpath, $value);
        } else {
            $this->client->findByXPath($this->prepareXPath($xpath))->choose($value, $multiple);
        }
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::click()
     */
    public function click($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->click();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::doubleClick()
     */
    public function doubleClick($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->doubleClick();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::rightClick()
     */
    public function rightClick($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->rightClick();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::attachFile()
     */
    public function attachFile($xpath, $path)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->setFile($path);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isVisible()
     */
    public function isVisible($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isVisible();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::mouseOver()
     */
    public function mouseOver($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->mouseOver();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::focus()
     */
    public function focus($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->focus();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::blur()
     */
    public function blur($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->blur();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyPress(
            $char, strtoupper($modifier)
        );
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyDown(
            $char, strtoupper($modifier)
        );
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyUp(
            $char, strtoupper($modifier)
        );
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::dragTo()
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $from = $this->client->findByXPath($sourceXpath);
        $to   = $this->client->findByXPath($destinationXpath);

        $from->dragDrop($to);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::executeScript()
     */
    public function executeScript($script)
    {
        $this->client->getConnection()->executeJavascript($script);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::evaluateScript()
     */
    public function evaluateScript($script)
    {
        return $this->client->getConnection()->evaluateJavascript($script);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::wait()
     */
    public function wait($time, $condition)
    {
        $this->client->wait($time, $condition);
    }

    /**
     * Selects specific radio option.
     *
     * @param   string  $xpath  xpath to one of the radio buttons
     * @param   string  $value  value to be set
     */
    private function selectRadioOption($xpath, $value)
    {
        $name = $this->getAttribute($this->prepareXPath($xpath), 'name');

        if (null !== $name) {
            $function = <<<JS
(function(){
    for (var i = 0; i < document.forms.length; i++) {
        if (document.forms[i].elements['{$name}']) {
            var form  = document.forms[i];
            var elements = form.elements['{$name}'];
            var value = elements[0].value;
            for (var f = 0; f < elements.length; f++) {
                var item = elements[f];
                if ("{$value}" == item.value) {
                    item.checked = true;
                }
            }
        }
    }
})()
JS;

            $this->executeScript($function);
        }
    }

    /**
     * Prepare XPath to be sent via Sahi proxy.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    private function prepareXPath($xpath)
    {
        return strtr($xpath, array('"' => '\\"'));
    }
}
