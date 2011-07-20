<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Driver\Zombie\Connection;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Zombie (JS) driver.
 *
 * @author      Pascal Cremer <b00gizm@gmail.com>
 */
class ZombieDriver implements DriverInterface
{
    /**
     * @var boolean
     */
    private $started = false;

    /**
     * @var array
     */
    private $nativeRefs = array();

    /**
     * @var ZombieConnection
     */
    private $conn = null;


    /**
     * Constructor
     *
     * @param    Connection  $conn  A connection object or NULL
     */
    public function __construct(Connection $conn = null)
    {
        if (null === $conn) {
            $conn = new Connection('127.0.0.1', 8124);
        }

        $this->conn = $conn;
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
        $this->started = false;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reset()
     */
    public function reset() {}

    /**
     * @see     Behat\Mink\Driver\DriverInterface::visit()
     */
    public function visit($url)
    {
        //$url = json_encode($url);

        $js = <<<JS
browser.visit("{$url}", function(err) {
  if (err) {
    stream.end(JSON.stringify(err.stack));
  } else {
    stream.end();
  }
});
JS;
        $out = $this->conn->socketSend($js);

        if (!empty($out)) {
          throw new DriverException(sprintf("Could not load resource for URL '%s'", $url));
        }
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCurrentUrl()
     */
    public function getCurrentUrl()
    {
        return $this->conn->socketJSON('browser.location.toString()');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reload()
     */
    public function reload()
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::forward()
     */
    public function forward()
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::back()
     */
    public function back()
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setBasicAuth()
     */
    public function setBasicAuth($user, $password)
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setRequestHeader()
     */
    public function setRequestHeader($name, $value)
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getResponseHeaders()
     */
    public function getResponseHeaders()
    {
        return (array)$this->conn->socketJSON('browser.lastResponse.headers');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setCookie()
     */
    public function setCookie($name, $value = null)
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCookie()
     */
    public function getCookie($name)
    {
        // TODO: Implement me!
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getStatusCode()
     */
    public function getStatusCode()
    {
        return (int)$this->conn->socketJSON('browser.statusCode');
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getContent()
     */
    public function getContent()
    {
      return html_entity_decode($this->conn->socketJSON('browser.html()'));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::find()
     */
    public function find($xpath)
    {
        $xpathEncoded = json_encode($xpath);
        $js =<<<JS
var refs = [];
browser.xpath("{$xpath}").value.forEach(function(node) {
  pointers.push(node);
  refs.push(pointers.length - 1);
});
stream.end(JSON.stringify(refs));
JS;
        $refs = (array)json_decode($this->conn->socketSend($js));

        $elements = array();
        foreach ($refs as $i => $ref) {
            $subXpath = sprintf('(%s)[%d]', $xpath, $i + 1);
            $this->nativeRefs[md5($subXpath)] = $ref;
            $elements[] = new NodeElement($subXpath, $this->session);
        }

        return $elements;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getTagName()
     */
    public function getTagName($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return null;
        }

        return strtolower($this->conn->socketJSON("{$ref}.tagName"));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getText()
     */
    public function getText($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return null;
        }

        return $this->conn->socketJSON("{$ref}.textContent");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getHtml()
     */
    public function getHtml($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return null;
        }

        return $this->conn->socketJSON("{$ref}.innerHTML");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getAttribute()
     */
    public function getAttribute($xpath, $name)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return null;
        }

        $out = $this->conn->socketJSON("{$ref}.getAttribute('{$name}')");
        return empty($out) ? null : $out;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getValue()
     */
    public function getValue($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return null;
        }

        $js = <<<JS
var node = {$ref},
    tagName = node.tagName,
    value = null;
if (tagName == "INPUT") {
  var type = node.getAttribute('type');
  if (type == "checkbox") {
    value = node.checked;
  } else if (type == "radio") {
    var name = node.getAttribute('name');
    if (name) {
      var field = browser.field("input[type='radio'][name='" + name + "']:checked");
      if (field) {
        value = field.value;
      }
    }
  } else {
    value = node.value;
  }
} else if (tagName == "TEXTAREA") {
  value = node.text;
} else if (tagName == "SELECT") {
  var idx = node.selectedIndex;
  value = node.options.item(idx).value;
}
stream.end(JSON.stringify(value));
JS;
        return json_decode($this->conn->socketSend($js));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setValue()
     */
    public function setValue($xpath, $value)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $value = json_encode($value);

        $js = <<<JS
var node = {$ref},
    tagName = node.tagName;
if (tagName == "TEXTAREA") {
  node.textContent = {$value};
} else {
  var type = node.getAttribute('type');
  if (type == "checkbox") {
    {$value} ? browser.check(node) : browser.uncheck(node);
  } else if (type == "radio") {
    browser.choose(node);
  } else {
    browser.fill(node, {$value});
  }
}
stream.end();
JS;
        $this->conn->socketSend($js);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::check()
     */
    public function check($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $this->conn->socketSend("browser.check({$ref});stream.end();");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::uncheck()
     */
    public function uncheck($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $this->conn->socketSend("browser.uncheck({$ref});stream.end();");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isChecked()
     */
    public function isChecked($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return false;
        }

        return (boolean)$this->conn->socketJSON("{$ref}.checked");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::selectOption()
     */
    public function selectOption($xpath, $value)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $value = json_encode($value);
        $js = <<<JS
var node = {$ref},
    tagName = node.tagName;
if (tagName == "SELECT") {
  browser.select(node, {$value});
} else if (tagName == "INPUT") {
  var type = node.getAttribute('type');
  if (type == "radio") {
    browser.choose(node);
  }
}
stream.end();
JS;
        $this->conn->socketSend($js);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::click()
     */
    public function click($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $js = <<<JS
var node = {$ref},
    tagName = node.tagName
    type = node.getAttribute('type') || null;
if (tagName == "BUTTON" || (tagName == "INPUT" && (type == "button" || type == "submit"))) {
  browser.pressButton(node.value, function(err) {
    if (err) {
      stream.end(JSON.stringify(err.stack));
    } else {
      stream.end();
    }
  });
} else {
  browser.fire("click", node, function(err) {
    if (err) {
      assstream.end(JSON.stringify(err.stack));
    } else {
      stream.end();
    }
  });
}
JS;
        $this->conn->socketSend($js);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::doubleClick()
     */
    public function doubleClick($xpath)
    {
        $this->triggerBrowserEvent("dblclick", $xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::rightClick()
     */
    public function rightClick($xpath)
    {
        $this->triggerBrowserEvent("contextmenu", $xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::attachFile()
     */
    public function attachFile($xpath, $path)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $path = json_encode($path);
        $this->conn->socketSend("browser.attach({$ref}, {$path});stream.end();");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isVisible()
     */
    public function isVisible($xpath)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        // This is kind of a workaround, because the current version of
        // Zombie.js does not fully support the DOMElement's style attribute
        $hiddenXpath = json_encode("./ancestor-or-self::*[contains(@style, 'display:none') or contains(@style, 'display: none')]");
        return (0 == (int)$this->conn->socketJSON("browser.xpath({$hiddenXpath}, {$ref}).value.length"));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::mouseOver()
     */
    public function mouseOver($xpath)
    {
        $this->triggerBrowserEvent("mouseover", $xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::focus()
     */
    public function focus($xpath)
    {
        $this->triggerBrowserEvent("focus", $xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::blur()
     */
    public function blur($xpath)
    {
        $this->triggerBrowserEvent("blur", $xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        $this->triggerKeyEvent("keypress", $xpath, $char, $modifier);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyDown()
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        $this->triggerKeyEvent("keydown", $xpath, $char, $modifier);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyUp()
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        $this->triggerKeyEvent("keyup", $xpath, $char, $modifier);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::dragTo()
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $this->triggerBrowserEvent("mousedown", $sourceXpath, array(), array(
          "button" => 0, "which" => 1, "pageX" => 0, "pageY" => 0
        ));
        $this->triggerBrowserEvent("mousemove", $sourceXpath, array(), array(
          "button" => 0, "which" => 1, "pageX" => 1, "pageY" => 1
        ));
        $this->triggerBrowserEvent("mousemove", $destinationXpath, array(), array(
          "button" => 0, "which" => 1, "pageX" => 1, "pageY" => 1
        ));
        $this->triggerBrowserEvent("mouseup", $destinationXpath, array(), array(
          "button" => 0, "which" => 1, "pageX" => 1, "pageY" => 1
        ));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::executeScript()
     */
    public function executeScript($script)
    {
        $script = json_encode($script);
        $this->conn->socketSend("browser.evaluate({$script})");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::evaluateScript()
     */
    public function evaluateScript($script)
    {
        $script = json_encode($script);
        return $this->conn->socketJSON("browser.evaluate({$script})");
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::wait()
     */
    public function wait($time, $condition)
    {
      // Because of its nature, the Zombie.js browser only waits a long as there
      // there are events in the event loop. As soon as it's empty, it calls
      // the callback. So there's no need to wait for a specific time or
      // condition
      $this->conn->socketSend("browser.wait(function() { stream.end(); });");
    }

    /**
     * Triggers (fires) a Zombie.js
     *  browser event
     *
     *
     * @param   string  $event  The name of the event
     * @param   string  $xpath  The xpath of the element to trigger this event
     * @param   array   $opts   Additional event options (key-value)
     * @param   array   $attrs  Additional event attributes (key-value)
     */
    protected function triggerBrowserEvent($event, $xpath, array $opts = array(), array $attrs = array())
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        // Merge event attributes with event options
        if (!empty($attrs)) {
            $mergedAttrs = array_merge(
              (isset($opt["attributes"]) ? $opt["attributes"] : array()), $attrs
            );

            if (!empty($mergedAttrs)) {
              $opts["attributes"] = $mergedAttrs;
            }
        }

        // Encode options array
        $opts = !empty($opts) ? json_encode($opts) : "{}";

        $js = <<<JS
browser.fire("{$event}", {$ref}, {$opts}, function(err) {
  if (err) {
    stream.end(JSON.stringify(err.stack));
  } else {
    stream.end();
  }
});
JS;
        $out = $this->conn->socketSend($js);
        if (!empty($out)) {
            throw new DriverException(sprintf("Error while processing event '%s'", $event));
        }
    }

    /**
     * Triggers a keyboard event
     *
     * @param   string  $type      The event name
     * @param   string  $xpath     The xpath of the element to trigger this event on
     * @param   mixed   $char      could be either char ('b') or char-code (98)
     * @param   string  $modifier  keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    protected function triggerKeyEvent($name, $xpath, $char, $modifier)
    {
        if (!$ref = $this->getNativeRefForXPath($xpath)) {
            return;
        }

        $char = is_numeric($char) ? $char : ord($char);

        $isCtrlKeyArg  = ($modifier == 'ctrl')  ? "true" : "false";
        $isAltKeyArg   = ($modifier == 'alt')   ? "true" : "false";
        $isShiftKeyArg = ($modifier == 'shift') ? "true" : "false";
        $isMetaKeyArg  = ($modifier == 'meta')  ? "true" : "false";

        $js = <<<JS
var node = {$ref},
    window = browser.window,
    e = window.document.createEvent("UIEvents");
e.initUIEvent("{$name}", true, true, window, 1);
e.ctrlKey = {$isCtrlKeyArg};
e.altKey = {$isAltKeyArg};
e.shiftKey = {$isShiftKeyArg};
e.metaKey = {$isMetaKeyArg};
e.keyCode = {$char};
node.dispatchEvent(e);
stream.end();
JS;
        $this->conn->socketSend($js);
    }

    /**
    * Tries to fetch a native reference to a node that might have been cached
     * by the server. If it can't be found, the method performs a search.
     *
     * Searching the native reference by the MD5 hash of its xpath feels kinda
     * hackish, but it'll boost performance and prevents a lot of boilerplate
     * Javascript code.
     *
     * @param   string  $xpath
     *
     * @return  string|null
     */
    protected function getNativeRefForXPath($xpath)
    {
        $hash = md5($xpath);
        if (!isset($this->nativeRefs[$hash])) {
            $res = $this->find($xpath);
            if (1 > count($res)) {
                return null;
            }
        }

        return sprintf('pointers[%s]', $this->nativeRefs[$hash]);
    }
}

