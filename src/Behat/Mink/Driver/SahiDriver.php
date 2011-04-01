<?php

namespace Behat\Mink\Driver;

use Behat\SahiClient\Client;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\ElementNotFoundException;

class SahiDriver implements DriverInterface
{
    private $started = false;
    private $startUrl;
    private $browserName;
    private $client;
    private $session;

    public function __construct($startUrl, $browserName, Client $client = null)
    {
        if (null === $client) {
            $client = new Client();
        }

        $this->client       = $client;
        $this->startUrl     = $startUrl;
        $this->browserName  = $browserName;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function start()
    {
        $this->client->start($this->browserName);
        $this->reset();
        $this->started = true;
    }

    public function isStarted()
    {
        return $this->started;
    }

    public function stop()
    {
        $this->client->stop();
        $this->started = false;
    }

    public function visit($url)
    {
        $this->client->navigateTo($url);
    }

    public function reset()
    {
        $this->visit($this->startUrl);
    }

    public function getCurrentUrl()
    {
        return $this->client->getConnection()->executeJavascript('document.URL');
    }

    public function getResponseHeaders()
    {
        throw new UnsupportedByDriverException('Response headers is not supported', $this);
    }

    public function getStatusCode()
    {
        throw new UnsupportedByDriverException('Status code is not supported', $this);
    }

    public function getContent()
    {
        $html = $this->client->getConnection()->executeJavascript(
            'document.getElementsByTagName("html")[0].innerHTML'
        );

        $html   = html_entity_decode($html);
        $start  = strpos($html, '<!--SAHI_INJECT_START-->');
        $finish = strpos($html, '<!--SAHI_INJECT_END-->') ;

        if (false !== $start && false !== $finish) {
            $finish += strlen('<!--SAHI_INJECT_END-->') - $start;
            $html    = substr_replace($html, '', $start, $finish);
        }

        return "<html>\n$html\n</html>";
    }

    public function find($xpath)
    {
        $count = intval($this->evaluateScript(
            'document.evaluate("' . $this->prepareXPath($xpath) . '", document, null, 7, null).snapshotLength'
        ));

        $elements = array();
        for ($i = 0; $i < $count; $i++) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i + 1), $this->session);
        }

        return $elements;
    }

    public function getTagName($xpath)
    {
        return strtolower($this->client->findByXPath($this->prepareXPath($xpath))->getName());
    }

    public function getText($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getText();
    }

    public function getAttribute($xpath, $name)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->getAttr($name);
    }

    public function getValue($xpath)
    {
        $xpath  = $this->prepareXPath($xpath);
        $type   = $this->getAttribute($xpath, 'type');
        $value  = null;

        if ('radio' === $type) {
            $name = $this->getAttribute($xpath, 'name');

            if (null !== $name) {
                $function = <<<JS
function(){
    for (var i = 0; i < document.forms.length; i++) {
        if (document.forms[i].{$name}) {
            var form  = document.forms[i];
            var value = form.{$name}[0].value;
            for (var f = 0; f < form.{$name}.length; f++) {
                var item = form.{$name}[f];
                if (item.checked) {
                    return item.value;
                }
            }
            return value;
        }
    }
    return null;
}()
JS;

                return $this->evaluateScript($function);
            }
        } elseif ('checkbox' === $type) {
            return $this->client->findByXPath($xpath)->isChecked();
        }

        return $this->client->findByXPath($xpath)->getValue();
    }

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

    public function check($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->check();
    }

    public function uncheck($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->uncheck();
    }

    public function isChecked($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isChecked();
    }

    public function selectOption($xpath, $value)
    {
        $type = $this->getAttribute($xpath, 'type');

        if ('radio' === $type) {
            $this->selectRadioOption($xpath, $value);
        } else {
            $this->client->findByXPath($this->prepareXPath($xpath))->choose($value);
        }
    }

    public function click($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->click();
    }

    public function rightClick($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->rightClick();
    }

    public function attachFile($xpath, $path)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->setFile($path);
    }

    public function isVisible($xpath)
    {
        return $this->client->findByXPath($this->prepareXPath($xpath))->isVisible();
    }

    public function mouseOver($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->mouseOver();
    }

    public function focus($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->focus();
    }

    public function blur($xpath)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->blur();
    }

    public function triggerEvent($xpath, $event)
    {
        $this->client->findByXPath($this->prepareXPath($xpath))->simulateEvent($event);
    }

    public function dragTo($sourceXpath, $destinationXpath)
    {
        $from = $this->client->findByXPath($sourceXpath);
        $to   = $this->client->findByXPath($destinationXpath);

        $from->dragDrop($to);
    }

    public function executeScript($script)
    {
        $this->evaluateScript($script);
    }

    public function evaluateScript($script)
    {
        return $this->client->getConnection()->executeJavascript($script);
    }

    private function selectRadioOption($xpath, $value)
    {
        $name = $this->getAttribute($this->prepareXPath($xpath), 'name');

        if (null !== $name) {
            $function = <<<JS
function(){
for (var i = 0; i < document.forms.length; i++) {
    if (document.forms[i].{$name}) {
        var form  = document.forms[i];
        var value = form.{$name}[0].value;
        for (var f = 0; f < form.{$name}.length; f++) {
            var item = form.{$name}[f];
            if ("{$value}" == item.value) {
                item.checked = true;
            }
        }
    }
}
}()
JS;

            $this->executeScript($function);
        }
    }

    private function prepareXPath($xpath)
    {
        return strtr($xpath, array('"' => '\\"'));
    }
}
