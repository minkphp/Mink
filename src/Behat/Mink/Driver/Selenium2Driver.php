<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException;

use Selenium\Client as SeleniumClient,
    Selenium\Locator as SeleniumLocator,
    Selenium\Exception as SeleniumException,
    \WebDriver as WebDriver;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Selenium2 driver.
 *
 * @author Pete Otaqui <pete@otaqui.com>
 */
class Selenium2Driver implements DriverInterface
{

    /**
     * The current Mink session
     * @var Behat\Mink\Session
     */
    private $session;

    /**
     * Whether the browser has been started
     * @var Boolean
     */
    private $started = false;

    /**
     * The WebDriver instance
     * @var WebDriver
     */ 
    private $webDriver;

    /**
     * Instantiates the driver.
     *
     * @param string    $browser Browser name
     * @param array     $desiredCapabilities The desired capabilities
     * @param string    $wdHost The WebDriver host
     */
    public function __construct($browserName = 'firefox', $desiredCapabilities = NULL, $wdHost = 'http://localhost:4444/wd/hub')
    {
        $this->setBrowserName($browserName);
        $this->setDesiredCapabilities($desiredCapabilities);
        $this->setWebDriver( new WebDriver($wdHost) );
    }

    public function setBrowserName($browserName = 'firefox') {
        $this->browserName = $browserName;
    }

    public function setDesiredCapabilities($desiredCapabilities = NULL) {
        if ( $desiredCapabilities === NULL ) {
            $desiredCapabilities = self::getDefaultCapabilities();
        }
        $this->desiredCapabilities = $desiredCapabilities;
    }

    public function setWebDriver($webDriver) {
        $this->webDriver = $webDriver;
    }


    protected static function getDefaultCapabilities() {
        return array('browserName'=>'firefox', 'version'=>'8', 'platform'=>'ANY', 'browserVersion'=>'8', 'browser'=>'firefox');
    }

    protected function simulateEvent($xpath, $eventType, $initType, $eventName, $eventOptions = NULL) {
        
        if ( is_array($eventOptions) ) {
            $argumentString = ',' . implode(',', $eventOptions);
        } elseif ( is_string($eventOptions) ) {
            $argumentString = ',' . $eventOptions;
        } else {
            $argumentString = '';
        }
        
        $script = <<<"JS"
            var evt = document.createEvent('$eventType'),
                ele = {{ELEMENT}};
            evt.init{$initType}Event('$eventName' $argumentString);
            ele.dispatchEvent(evt);
JS;
        $this->executeJsOnXpath($xpath, $script);
    }

    protected function executeJsOnXpath($xpath, $script) {
        $element = $this->wdSession->element('xpath', $xpath);
        $elementID = $element->getID();
        $subscript = "arguments[0]";
        $script = str_replace('{{ELEMENT}}', $subscript, $script);
        //echo "\n SCRIPT:\n$script\n";
        return $this->wdSession->execute(array('script'=>$script, 'args'=>array(array('ELEMENT'=>$elementID))));
    }

    /**
     * Returns a crawler instance (got from the client)
     * @return [type]
     */
    protected function getCrawler() {
        return new \Symfony\Component\DomCrawler\Crawler($this->wdSession->source());
    }


    /**
     * @see Behat\Mink\Driver\DriverInterface::setSession()
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
        $this->wdSession = $this->webDriver->session($this->browserName, $this->desiredCapabilities);
        if ( !$this->wdSession ) {
            throw new \Behat\Mink\Exception\DriverException('Could not connect to a Selenium 2 / WebDriver server');
        }
        $this->started = true;
    }

    /**
     * Checks whether driver is started.
     *
     * @return  Boolean
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
        if ( !$this->wdSession ) {
            throw new \Behat\Mink\Exception\DriverException('Could not connect to a Selenium 2 / WebDriver server');
        }
        $this->started = false;
        $this->wdSession->close();
    }

    /**
     * Resets driver.
     */
    public function reset()
    {
        $this->wdSession->deleteAllCookies();
    }

    /**
     * Visit specified URL.
     *
     * @param   string  $url    url of the page
     */
    public function visit($url)
    {
        $this->wdSession->open($url);
    }

    /**
     * Returns current URL address.
     *
     * @return  string
     */
    public function getCurrentUrl()
    {
        $url = $this->wdSession->url();
        return $url;
    }

    /**
     * Reloads current page.
     */
    function reload()
    {
        $this->wdSession->refresh();
    }

    /**
     * Moves browser forward 1 page.
     */
    function forward()
    {
        $this->wdSession->forward();
    }

    /**
     * Moves browser backward 1 page.
     */
    function back()
    {
        $this->wdSession->back();
    }

    /**
     * Sets HTTP Basic authentication parameters
     *
     * @param   string|false    $user       user name or false to disable authentication
     * @param   string          $password   password
     */
    function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Basic Auth is not supported by %s', $this);
    }

    /**
     * Sets specific request header on client.
     *
     * @param   string  $name
     * @param   string  $value
     */
    function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * Returns last response headers.
     *
     * @return  array
     */
    function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Response header is not supported by %s', $this);
    }

    /**
     * Sets cookie.
     *
     * @param   string  $name
     * @param   string  $value
     */
    function setCookie($name, $value = null)
    {
        if ( $value !== null ) {
            $cookieArray = array(
                'name' => $name,
                'value' => (string) $value,
                'secure' => false, // thanks, chibimagic!
            );
            $this->wdSession->setCookie($cookieArray);
        } else {
            $this->wdSession->deleteCookie($name);
        }
    }

    /**
     * Returns cookie by name.
     *
     * @param   string  $name
     *
     * @return  string|null
     */
    function getCookie($name)
    {
        $cookies = $this->wdSession->getAllCookies();
        foreach ( $cookies as $cookie ) {
            if ( $cookie['name'] === $name ) {
                return $cookie['value'];
            }
        }
        return null;
    }

    /**
     * Returns last response status code.
     *
     * @return  integer
     */
    function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Status code is not supported by %s', $this);
    }

    /**
     * Returns last response content.
     *
     * @return  string
     */
    function getContent()
    {
        return $this->wdSession->source();
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @param   string  $xpath
     *
     * @return  array           array of Behat\Mink\Element\NodeElement
     */
    function find($xpath)
    {
        $elements = array();
        $nodes = $this->wdSession->elements('xpath', $xpath);
        foreach ( $nodes as $i => $node ) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i+1), $this->session);
        }
        if ( !empty($elements) ) {
            return $elements;
        } else {
            return null;
        }
    }

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getTagName($xpath)
    {
        $node = $this->wdSession->element('xpath', $xpath);
        return $node->name();
    }

    /**
     * Returns element's text by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getText($xpath)
    {
        $node = $this->wdSession->element('xpath', $xpath);
        $text = $node->text();
        $text = (string)str_replace(array("\r", "\r\n", "\n"), ' ', $text);
        return $text;
    }

    /**
     * Returns element's html by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  string
     */
    function getHtml($xpath)
    {
        return $this->executeJsOnXpath($xpath, 'return {{ELEMENT}}.innerHTML;');
    }

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    public function getAttribute($xpath, $attr)
    {
        $attribute = $this->wdSession->element('xpath', $xpath)->attribute($attr);
        if ( $attribute !== '' ) {
            return $attribute;
        } else {
            return null;
        }
    }

    /**
     * Returns element's value by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  mixed
     */
    function getValue($xpath)
    {
        $script = <<<JS

    var node = {{ELEMENT}},
        tagName = node.tagName;

    if (tagName == "INPUT") {
        var type = node.getAttribute('type');
        if (type == "checkbox") {
            value = "boolean:" + node.checked;
        } else if (type == "radio") {
            var name = node.getAttribute('name');
            if (name) {
                var fields = window.document.getElementsByName(name);
                var i, l = fields.length;
                for (i = 0; i < l; i++) {
                    var field = fields.item(i);
                    if (field.checked) {
                        value = "string:" + field.value;
                    }
                }
            }
        } else {
            value = "string:" + node.value;
        }
    } else if (tagName == "TEXTAREA") {
      value = "string:" + node.text;
    } else if (tagName == "SELECT") {
      var idx = node.selectedIndex;
      value = "string:" + node.options.item(idx).value;
    } else {
      value = "string:" + node.getAttribute('value');
    }
    return value;
JS;
        $value = $this->executeJsOnXpath($xpath, $script);
        if (null === $value) {
            return null;
        } elseif (preg_match('/^string:(.*)$/', $value, $vars)) {
            return $vars[1];
        } elseif (preg_match('/^boolean:(.*)$/', $value, $vars)) {
            return 'true' === strtolower($vars[1]);
        }
    }

    /**
     * Sets element's value by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     */
    function setValue($xpath, $value)
    {
        $valueEscaped = str_replace('"', '\"', $value);
        $this->executeJsOnXpath($xpath, '{{ELEMENT}}.value="'.$valueEscaped.'";');
    }

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function check($xpath)
    {
        $this->executeJsOnXpath($xpath, '{{ELEMENT}}.checked = true');
    }

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param   string  $xpath
     */
    function uncheck($xpath)
    {
        $this->executeJsOnXpath($xpath, '{{ELEMENT}}.checked = false');
    }

    /**
     * Checks whether checkbox checked located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isChecked($xpath)
    {
        return $this->wdSession->element('xpath', $xpath)->selected();
    }

    /**
     * Selects option from select field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $value
     * @param   Boolean $multiple
     */
    function selectOption($xpath, $value, $multiple = false)
    {
        $valueEscaped = str_replace('"', '\"', $value);
        $multipleJS   = $multiple ? 'true' : 'false';

        $script = <<<JS
var node = {{ELEMENT}}
if (node.tagName == 'SELECT') {
    var i, l = node.length;
    for (i = 0; i < l; i++) {
        if (node[i].value == "$valueEscaped") {
            node[i].selected = true;
        } else if (!$multipleJS) {
            node[i].selected = false;
        }
    }
} else {
    var nodes = window.document.getElementsByName(node.getAttribute('name'));
    var i, l = nodes.length;
    for (i = 0; i < l; i++) {
        if (nodes[i].getAttribute('value') == "$valueEscaped") {
            node.checked = true;
        }
    }
}
JS;

        $this->executeJsOnXpath($xpath, $script);
    }

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function click($xpath)
    {
        $this->wdSession->element('xpath', $xpath)->click("");
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function doubleClick($xpath)
    {
        $this->simulateEvent($xpath, 'MouseEvents', 'Mouse', 'dblclick', 'true, true, window, 2, 0, 0, 0, 0, false, false, false, false, 0, null');
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param   string  $xpath
     */
    function rightClick($xpath)
    {
        $this->simulateEvent($xpath, 'MouseEvents', 'Mouse', 'contextmenu', 'true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null');
    }

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param   string  $xpath
     * @param   string  $path
     */
    function attachFile($xpath, $path)
    {
        throw new UnsupportedDriverActionException('Attach File is not supported by %s', $this);
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Boolean
     */
    function isVisible($xpath)
    {
        return $this->wdSession->element('xpath', $xpath)->displayed();
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param   string  $xpath
     */
    function mouseOver($xpath)
    {
        $this->simulateEvent($xpath, 'MouseEvents', 'Mouse', 'mouseover', 'true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null');
    }

    /**
     * Brings focus to element.
     *
     * @param   string  $xpath
     */
    function focus($xpath)
    {
        $this->executeJsOnXpath($xpath, '{{ELEMENT}}.focus();');
    }

    /**
     * Removes focus from element.
     *
     * @param   string  $xpath
     */
    function blur($xpath)
    {
        $this->executeJsOnXpath($xpath, '{{ELEMENT}}.blur();');
    }

    /**
     * Presses specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyPress($xpath, $char, $modifier = null)
    {
        $this->simulateKeyEvent($xpath, 'keypress', $char, $modifier);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyDown($xpath, $char, $modifier = null)
    {
        $this->simulateKeyEvent($xpath, 'keydown', $char, $modifier);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param   string  $xpath
     * @param   mixed   $char       could be either char ('b') or char-code (98)
     * @param   string  $modifier   keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    function keyUp($xpath, $char, $modifier = null)
    {
        $this->simulateKeyEvent($xpath, 'keyup', $char, $modifier);
    }



    protected function simulateKeyEvent($xpath, $type, $char, $modifier) {
        $script = <<<JS
        var element = {{ELEMENT}},
            char = '{$char}'
            modifier = '{$modifier}',
            type = '$type',
            eventObject = document.createEvent('KeyboardEvent'),
            bubbles = true,
            cancelable = true,
            view = window,
            ctrlKey  = (modifier === 'ctrl' ),
            altKey   = (modifier === 'alt'  ),
            shiftKey = (modifier === 'shift'),
            metaKey  = (modifier === 'meta' ),
            keyCode = 0,
            charCode = char.charCodeAt(0);
        
        eventObject.initKeyEvent(type, bubbles, cancelable, view, ctrlKey, altKey, shiftKey, metaKey, keyCode, charCode);
        element.dispatchEvent(eventObject);
JS;
        $this->executeJsOnXpath($xpath, $script);
    }


    /**
     * Drag one element onto another.
     *
     * @param   string  $sourceXpath
     * @param   string  $destinationXpath
     */
    function dragTo($sourceXpath, $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Drag and Drop is not supported by %s', $this);
    }

    /**
     * Executes JS script.
     *
     * @param   string  $script
     */
    function executeScript($script)
    {
        $this->wdSession->execute(array('script'=>$script, 'args'=>array()));
    }

    /**
     * Evaluates JS script.
     *
     * @param   string  $script
     *
     * @return  mixed           script return value
     */
    function evaluateScript($script)
    {
        return $this->wdSession->execute(array('script'=>$script, 'args'=>array()));
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param   integer $time       time in milliseconds
     * @param   string  $condition  JS condition
     */
    function wait($time, $condition)
    {
        $script = "return $condition;";
        $start = 1000 * microtime(true);
        $end = $start + $time;
        $count = 0;
        while ( 1000 * microtime(true) < $end && !$this->wdSession->execute(array('script'=>$script, 'args'=>array())) )
        {
            sleep(0.1);
            if ( $count++ >= 30 ) {
                return false;
            }
        }
    }
}
