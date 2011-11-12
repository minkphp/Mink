<?php

namespace Behat\Mink\Driver;

use Goutte\Client as GoutteClient,
    Symfony\Component\BrowserKit\Client,
    Symfony\Component\BrowserKit\Cookie,
    Symfony\Component\DomCrawler\Crawler,
    Symfony\Component\DomCrawler\Form,
    Symfony\Component\DomCrawler\Field;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException,
    Behat\Mink\Exception\ElementNotFoundException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Goutte (Symfony2) driver.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class GoutteDriver implements DriverInterface
{
    private $session;
    private $client;
    private $forms = array();
    private $started = false;

    /**
     * Initializes Goutte driver.
     *
     * @param   Symfony\Component\BrowserKit\Client $client     BrowserKit client instance
     */
    public function __construct(Client $client = null)
    {
        if (null === $client) {
            $client = new GoutteClient();
        }

        $this->client = $client;
        $this->client->followRedirects(true);
    }

    /**
     * Returns BrowserKit HTTP client instance.
     *
     * @return  Symfony\Component\BrowserKit\Client
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
        $this->client->restart();
        $this->started = false;
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reset()
     */
    public function reset()
    {
        $this->client->restart();
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::visit()
     */
    public function visit($url)
    {
        $this->client->request('GET', $url);
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCurrentUrl()
     */
    public function getCurrentUrl()
    {
        return $this->client->getRequest()->getUri();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::reload()
     */
    public function reload()
    {
        $this->client->reload();
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::forward()
     */
    public function forward()
    {
        $this->client->forward();
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::back()
     */
    public function back()
    {
        $this->client->back();
        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setBasicAuth()
     */
    public function setBasicAuth($user, $password)
    {
        $this->client->setAuth($user, $password);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setRequestHeader()
     */
    public function setRequestHeader($name, $value)
    {
        $this->client->setHeader($name, $value);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getResponseHeaders()
     */
    public function getResponseHeaders()
    {
        return $this->client->getResponse()->getHeaders();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setCookie()
     */
    public function setCookie($name, $value = null)
    {
        $jar = $this->client->getCookieJar();

        if (null === $value) {
            if (null !== $jar->get($name)) {
                $jar->expire($name);
            }

            return;
        }

        $jar->set(new Cookie($name, $value));
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getCookie()
     */
    public function getCookie($name)
    {
        $jar = $this->client->getCookieJar();

        if (null !== $cookie = $jar->get($name)) {
            return $cookie->getValue();
        }
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getStatusCode()
     */
    public function getStatusCode()
    {
        return $this->client->getResponse()->getStatus();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getContent()
     */
    public function getContent()
    {
        return $this->client->getResponse()->getContent();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::find()
     */
    public function find($xpath)
    {
        $nodes = $this->getCrawler()->filterXPath($xpath);

        $elements = array();
        foreach ($nodes as $i => $node) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i + 1), $this->session);
        }

        return $elements;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getTagName()
     */
    public function getTagName($xpath)
    {
        return $this->getCrawlerNode($this->getCrawler()->filterXPath($xpath)->eq(0))->nodeName;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getText()
     */
    public function getText($xpath)
    {
        $text = $this->getCrawler()->filterXPath($xpath)->eq(0)->text();
        $text = str_replace("\n", ' ', $text);
        $text = preg_replace('/ {2,}/', ' ', $text);

        return trim($text);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getHtml()
     */
    public function getHtml($xpath)
    {
        $node = $this->getCrawlerNode($this->getCrawler()->filterXPath($xpath)->eq(0));
        $text = $node->ownerDocument->saveXML($node);

        // cut the tag itself (making innerHTML out of outerHTML)
        $text = preg_replace('/^\<[^\>]+\>|\<[^\>]+\>$/', '', $text);

        return $text;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getAttribute()
     */
    public function getAttribute($xpath, $attr)
    {
        $value = $this->getCrawler()->filterXPath($xpath)->eq(0)->attr($attr);

        return '' !== $value ? $value : null;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::getValue()
     */
    public function getValue($xpath)
    {
        try {
            $field = $this->getFormField($xpath);
        } catch (\InvalidArgumentException $e) {
            return $this->getAttribute($xpath, 'value');
        }

        $value = $field->getValue();

        if ($field instanceof Field\ChoiceFormField && 'checkbox' === $field->getType()) {
            $value = '1' == $value;
        }

        return $value;
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::setValue()
     */
    public function setValue($xpath, $value)
    {
        $this->getFormField($xpath)->setValue($value);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::check()
     */
    public function check($xpath)
    {
        $this->getFormField($xpath)->tick();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::uncheck()
     */
    public function uncheck($xpath)
    {
        $this->getFormField($xpath)->untick();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::selectOption()
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        $field = $this->getFormField($xpath);

        if ($multiple) {
            $oldValue   = (array) $field->getValue();
            $oldValue[] = $value;
            $value      = $oldValue;
        }

        $field->select($value);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::click()
     */
    public function click($xpath)
    {
        if (!count($nodes = $this->getCrawler()->filterXPath($xpath))) {
            throw new ElementNotFoundException(
                $this->session, 'link or button', 'xpath', $xpath
            );
        }
        $node = $nodes->eq(0);
        $type = $this->getCrawlerNode($node)->nodeName;

        if ('a' === $type) {
            $this->client->click($node->link());
        } elseif('input' === $type || 'button' === $type) {
            $form   = $node->form();
            $formId = $this->getFormNodeId($form->getFormNode());

            if (isset($this->forms[$formId])) {
                $this->mergeForms($form, $this->forms[$formId]);
            }

            $this->client->submit($form);
        } else {
            throw new DriverException(sprintf(
                'Goutte driver supports clicking on inputs and links only. But "%s" provided', $type
            ));
        }

        $this->forms = array();
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isChecked()
     */
    public function isChecked($xpath)
    {
        return true === $this->getValue($xpath);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::attachFile()
     */
    public function attachFile($xpath, $path)
    {
        $this->getFormField($xpath)->upload($path);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::doubleClick()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function doubleClick($xpath)
    {
        throw new UnsupportedDriverActionException('Double-clicking is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::rightClick()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function rightClick($xpath)
    {
        throw new UnsupportedDriverActionException('Right-clicking is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::mouseOver()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function mouseOver($xpath)
    {
        throw new UnsupportedDriverActionException('Mouse moving is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::focus()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function focus($xpath)
    {
        throw new UnsupportedDriverActionException('Focus actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::blur()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Focus actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        throw new UnsupportedDriverActionException('Keyboard actions are not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::executeScript()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function executeScript($script)
    {
        throw new UnsupportedDriverActionException('JS scripts execution is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::evaluateScript()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('JS scripts execution is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::wait()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function wait($time, $condition)
    {
        throw new UnsupportedDriverActionException('JS scripts execution is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::isVisible()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function isVisible($xpath)
    {
        throw new UnsupportedDriverActionException('Element visibility check is not supported by %s', $this);
    }

    /**
     * @see     Behat\Mink\Driver\DriverInterface::dragTo()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        throw new UnsupportedDriverActionException('Element dragging is not supported by %s', $this);
    }

    /**
     * Returns form field from XPath query.
     *
     * @param   string  $xpath
     *
     * @return  Symfony\Component\DomCrawler\Field\FormField
     */
    private function getFormField($xpath)
    {
        if (!count($crawler = $this->getCrawler()->filterXPath($xpath))) {
            throw new ElementNotFoundException(
                $this->session, 'form field', 'xpath', $xpath
            );
        }

        $fieldNode = $this->getCrawlerNode($crawler);
        $fieldName = str_replace('[]', '', $fieldNode->getAttribute('name'));
        $formNode  = $fieldNode;

        do {
            // use the ancestor form element
            if (null === $formNode = $formNode->parentNode) {
                throw new \LogicException('The selected node does not have a form ancestor.');
            }
        } while ('form' != $formNode->nodeName);

        $formId = $this->getFormNodeId($formNode);

        // check if form already exists
        if (isset($this->forms[$formId])) {
            return $this->forms[$formId][$fieldName];
        }

        // find form button
        if (null === $buttonNode = $this->findFormButton($formNode)) {
            throw new ElementNotFoundException(
                $this->session, 'form submit button for field with xpath "'.$xpath.'"'
            );
        }

        $this->forms[$formId] = new Form($buttonNode, $this->client->getRequest()->getUri());

        return $this->forms[$formId][$fieldName];
    }

    /**
     * Returns form node unique identifier.
     *
     * @param   \DOMElement $form
     *
     * @return  mixed
     */
    private function getFormNodeId(\DOMElement $form)
    {
        return md5($form->getLineNo() . $form->getNodePath() . $form->nodeValue);
    }

    /**
     * Finds form submit button inside form node.
     *
     * @param   \DOMElement $form
     *
     * @return  \DOMElement
     */
    private function findFormButton(\DOMElement $form)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $node     = $document->importNode($form, true);
        $root     = $document->appendChild($document->createElement('_root'));

        $root->appendChild($node);
        $xpath = new \DOMXPath($document);

        foreach ($xpath->query('descendant::input | descendant::button', $root) as $node) {
            if ('button' == $node->nodeName || in_array($node->getAttribute('type'), array('submit', 'button', 'image'))) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Merges second form values into first one.
     *
     * @param   Form    $to     merging target
     * @param   Form    $from   merging source
     */
    private function mergeForms(Form $to, Form $from)
    {
        foreach ($from->all() as $name => $field) {
            $fieldReflection = new \ReflectionObject($field);
            $nodeReflection  = $fieldReflection->getProperty('node');
            $valueReflection = $fieldReflection->getProperty('value');

            $nodeReflection->setAccessible(true);
            $valueReflection->setAccessible(true);

            if (!($field instanceof Field\InputtoField && in_array(
                $nodeReflection->getValue($field)->getAttribute('type'),
                array('submit', 'button', 'image')
            ))) {
                $valueReflection->setValue(
                    $to[$field->getName()], $valueReflection->getValue($field)
                );
            }
        }
    }

    /**
     * Returns DOMNode from crawler instance.
     *
     * @param   Symfony\Component\DomCrawler\Crawler    $crawler
     * @param   integer                                 $num        number of node from crawler
     *
     * @return  DOMNode
     */
    private function getCrawlerNode(Crawler $crawler, $num = 0)
    {
        foreach ($crawler as $i => $node) {
            if ($num == $i) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Returns crawler instance (got from client).
     *
     * @return  Symfony\Component\DomCrawler\Crawler
     *
     * @throws  Behat\Mink\Exception\DriverException    if can't init crawler (no page is opened)
     */
    private function getCrawler()
    {
        $crawler = $this->client->getCrawler();

        if (null === $crawler) {
            throw new DriverException('Crawler can\'t be initialized. Did you started driver?');
        }

        return $crawler;
    }
}
