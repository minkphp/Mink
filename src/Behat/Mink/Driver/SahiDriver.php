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
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
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
     * @param string $browserName browser to start (firefox, safari, ie, etc...)
     * @param Client $client      Sahi client instance
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
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets driver's current session.
     *
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Starts driver.
     */
    public function start()
    {
        $this->client->start($this->browserName);
        $this->started = true;
    }

    /**
     * Checks whether driver is started.
     *
     * @return Boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Stops driver.
     */
    public function stop()
    {
        $this->client->stop();
        $this->started = false;
    }

    /**
     * Resets driver.
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
     * Visit specified URL.
     *
     * @param string $url url of the page
     */
    public function visit($url)
    {
        $this->client->navigateTo($url, true);
    }

    /**
     * Returns current URL address.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->evaluateScript('document.URL');
    }

    /**
     * Reloads current page.
     */
    public function reload()
    {
        $this->visit($this->getCurrentUrl());
    }

    /**
     * Moves browser forward 1 page.
     */
    public function forward()
    {
        $this->executeScript('history.forward()');
    }

    /**
     * Moves browser backward 1 page.
     */
    public function back()
    {
        $this->executeScript('history.back()');
    }

    /**
     * Sets HTTP Basic authentication parameters
     *
     * @param string|Boolean $user     user name or false to disable authentication
     * @param string         $password password
     *
     * @throws UnsupportedDriverActionException
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('HTTP Basic authentication is not supported by %s', $this);
    }

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException
     */
    public function switchToWindow($name = null)
    {
        throw new UnsupportedDriverActionException('Window management is broken in Sahi, so %s does not support switching into windows', $this);
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException
     */
    public function switchToIFrame($name = null)
    {
        throw new UnsupportedDriverActionException('Sahi does not have ability to switch into iFrames, so %s does not support it too', $this);
    }

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request headers manipulation is not supported by %s', $this);
    }

    /**
     * Returns last response headers.
     *
     * @return array
     *
     * @throws UnsupportedDriverActionException
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response headers manipulation is not supported by %s', $this);
    }

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
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
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getCookie($name)
    {
        try {
            return urldecode($this->evaluateScript(sprintf('_sahi._cookie("%s")', $name)));
        } catch (ConnectionException $e) {}
    }

    /**
     * Returns last response status code.
     *
     * @return integer
     *
     * @throws UnsupportedDriverActionException
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code reading is not supported by %s', $this);
    }

    /**
     * Returns last response content.
     *
     * @return string
     */
    public function getContent()
    {
        $html = $this->evaluateScript('document.getElementsByTagName("html")[0].innerHTML');
        $html = $this->removeSahiInjectionFromText($html);

        return "<html>\n$html\n</html>";
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @param string $xpath
     *
     * @return array array of NodeElements
     */
    public function find($xpath)
    {
        $jsXpath = $this->prepareXPath($xpath);
        $function = <<<JS
(function(){
    var count = 0;
    while (_sahi._byXPath("({$jsXpath})["+(count+1)+"]")) count++;
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
     * Returns element's tag name by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     */
    public function getTagName($xpath)
    {
        return strtolower($this->client->findByXPath($this->prepareXPath($xpath))->getName());
    }

    /**
     * Returns element's text by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     */
    public function getText($xpath)
    {
        return $this->removeSahiInjectionFromText(
            $this->client->findByXPath($this->prepareXPath($xpath))->getText()
        );
    }

    /**
     * Returns element's html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     */
    public function getHtml($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getHTML();
    }

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param string $xpath
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute($xpath, $name)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getAttr($name);
    }

    /**
     * Returns element's value by it's XPath query.
     *
     * @param string $xpath
     *
     * @return mixed
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
     * Sets element's value by it's XPath query.
     *
     * @param string $xpath
     * @param string $value
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
     * Checks checkbox by it's XPath query.
     *
     * @param string $xpath
     */
    public function check($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->check();
    }

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param string $xpath
     */
    public function uncheck($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->uncheck();
    }

    /**
     * Checks whether checkbox checked located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     */
    public function isChecked($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isChecked();
    }

    /**
     * Selects option from select field located by it's XPath query.
     *
     * @param string  $xpath
     * @param string  $value
     * @param Boolean $multiple
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
     * Clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     */
    public function click($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->click();
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     */
    public function doubleClick($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->doubleClick();
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     */
    public function rightClick($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->rightClick();
    }

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param string $xpath
     * @param string $path
     */
    public function attachFile($xpath, $path)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->setFile($path);
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     */
    public function isVisible($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isVisible();
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     */
    public function mouseOver($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->mouseOver();
    }

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     */
    public function focus($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->focus();
    }

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     */
    public function blur($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->blur();
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyPress(
            $char, strtoupper($modifier)
        );
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyDown(
            $char, strtoupper($modifier)
        );
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string $xpath
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->keyUp(
            $char, strtoupper($modifier)
        );
    }

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $from = $this->client->findByXPath($this->prepareXPath($sourceXpath));
        $to   = $this->client->findByXPath($this->prepareXPath($destinationXpath));

        $from->dragDrop($to);
    }

    /**
     * Executes JS script.
     *
     * @param string $script
     */
    public function executeScript($script)
    {
        $this->client->getConnection()->executeJavascript($script);
    }

    /**
     * Evaluates JS script.
     *
     * @param string $script
     *
     * @return mixed
     */
    public function evaluateScript($script)
    {
        return $this->client->getConnection()->evaluateJavascript($script);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param integer $time      time in milliseconds
     * @param string  $condition JS condition
     */
    public function wait($time, $condition)
    {
        $this->client->wait($time, $condition);
    }

    /**
     * Selects specific radio option.
     *
     * @param string $xpath xpath to one of the radio buttons
     * @param string $value value to be set
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
     * @param string $xpath
     *
     * @return string
     */
    private function prepareXPath($xpath)
    {
        return strtr($xpath, array('"' => '\\"'));
    }

    /**
     * Removes injected by Sahi code.
     *
     * @param string $string
     *
     * @return string
     */
    private function removeSahiInjectionFromText($string)
    {
        $string = preg_replace(array(
            '/<\!--SAHI_INJECT_START--\>.*\<\!--SAHI_INJECT_END--\>/sU',
            '/\<script\>\/\*\<\!\[CDATA\[\*\/\/\*----\>\*\/__sahi.*\<\!--SAHI_INJECT_END--\>/sU'
        ), '', $string);

        $string = str_replace('/*<![CDATA[*//*---->*/__sahiDebugStr__="";__sahiDebug__=function(s){__sahiDebugStr__+=(s+"\n");};/*--*//*]]>*/ /*<![CDATA[*//*---->*/_sahi.createCookie(\'sahisid\', _sahi.sid);_sahi.loadXPathScript()/*--*//*]]>*/ /*<![CDATA[*//*---->*/eval(_sahi.sendToServer("/_s_/dyn/Player_script/script.js"));/*--*//*]]>*/ ', '', $string);

        return $string;
    }
}
