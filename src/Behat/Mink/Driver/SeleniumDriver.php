<?php

namespace Behat\Mink\Driver;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\UnsupportedDriverActionException;

use Selenium\Client as SeleniumClient,
    Selenium\Locator as SeleniumLocator,
    Selenium\Exception as SeleniumException;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Selenium driver.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SeleniumDriver implements DriverInterface
{
    /**
     * Default timeout for Selenium (in milliseconds)
     *
     * @var int
     */
    private $timeout = 60000;

    /**
     * The current session
     *
     * @var Behat\Mink\Session
     */
    private $session;

    /**
     * The selenium browser instance
     *
     * @var Selenium\Browser
     */
    private $browser;

    /**
     * Flag indicating if the browser is started
     *
     * @var boolean
     */
    private $started = false;

    /**
     * Instanciates the driver.
     *
     * @param string          $browser Browser name
     * @param string          $baseUrl Base URL for testing
     * @param Selenium\Client $client  The client for getting a browser
     */
    public function __construct($browser, $baseUrl, SeleniumClient $client)
    {
        $this->browser = $client->getBrowser($baseUrl, '*'.$browser);
    }

    /**
     * Returns Selenium browser instance.
     *
     * @return  Selenium\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::setSession()
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::start()
     */
    public function start()
    {
        $this->started = true;
        $this->browser->start();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::isStarted()
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::stop()
     */
    public function stop()
    {
        if (true === $this->started) {
            $this->browser->stop();
        }
        $this->started = false;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::reset()
     */
    public function reset()
    {
        $this->stop();
        $this->start();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::visit()
     */
    public function visit($url)
    {
        $this->browser
            ->open($url)
            ->waitForPageToLoad($this->timeout)
        ;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getCurrentUrl()
     */
    public function getCurrentUrl()
    {
        return $this->browser->getLocation();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::reload()
     */
    public function reload()
    {
        $this->browser->refresh();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::forward()
     */
    public function forward()
    {
        $this->browser
            ->runScript('history.forward()')
            ->waitForPageToLoad($this->timeout)
        ;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::back()
     */
    public function back()
    {
        $this->browser->goBack();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::setBasicAuth()
     */
    public function setBasicAuth($user, $password)
    {
        throw new UnsupportedDriverActionException('Basic Auth is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::setRequestHeader()
     */
    public function setRequestHeader($name, $value)
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getResponseHeaders()
     */
    public function getResponseHeaders()
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::setCookie()
     */
    public function setCookie($name, $value = null)
    {
        $this->browser->createCookie($name.'='.$value, '');
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getCookie()
     */
    public function getCookie($name)
    {
        return $this->browser->getCookieByName($name);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getStatusCode()
     */
    public function getStatusCode()
    {
        throw new UnsupportedDriverActionException('Request header is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getContent()
     */
    public function getContent()
    {
        return $this->browser->getHtmlSource();
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::find()
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
     * @see Behat\Mink\Driver\DriverInterface::getTagName()
     */
    public function getTagName($xpath)
    {
        $nodes = $this->getCrawler()->filterXPath($xpath)->eq(0);
        $nodes->rewind();
        $node = $nodes->current();

        return $node->nodeName;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getText()
     */
    public function getText($xpath)
    {
        return $this->browser->getText(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getHtml()
     */
    public function getHtml($xpath)
    {
        $nodes = $this->getCrawler()->filterXPath($xpath)->eq(0);

        $nodes->rewind();
        $node = $nodes->current();
        $text = $node->C14N();

        // cut the tag itself (making innerHTML out of outerHTML)
        $text = preg_replace('/^\<[^\>]+\>|\<[^\>]+\>$/', '', $text);

        return $text;
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getAttribute()
     */
    public function getAttribute($xpath, $attr)
    {
        return $this->browser->getAttribute(SeleniumLocator::xpath($xpath).'@'.$attr);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::getValue()
     */
    public function getValue($xpath)
    {
        return $this->browser->getValue(SeleniumLocator::xpath($xpath));

    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::setValue()
     */
    public function setValue($xpath, $value)
    {
        $this->browser->type(SeleniumLocator::xpath($xpath), $value);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::check()
     */
    public function check($xpath)
    {
        $this->browser->check(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::uncheck()
     */
    public function uncheck($xpath)
    {
        $this->browser->uncheck(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::selectOption()
     */
    public function selectOption($xpath, $value)
    {
        $this->browser->select(SeleniumLocator::xpath($xpath), 'value='.$value);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::click()
     */
    public function click($xpath)
    {
        $this->browser->click(SeleniumLocator::xpath($xpath));
        try {
            $this->browser->waitForPageToLoad($this->timeout);
        } catch (SeleniumException $e){} // If click loads a new page, then wait for it
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::isChecked()
     */
    public function isChecked($xpath)
    {
        return $this->browser->isChecked(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::attachFile()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function attachFile($xpath, $path)
    {
        throw new UnsupportedDriverActionException('Uploading file is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::doubleClick()
     */
    public function doubleClick($xpath)
    {
        $this->browser->doubleClick(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::rightClick()
     */
    public function rightClick($xpath)
    {
        $this->browser->mouseDownRight(SeleniumLocator::xpath($xpath));
        $this->browser->mouseUpRight(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::mouseOver()
     */
    public function mouseOver($xpath)
    {
        $this->browser->mouseOver(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::focus()
     */
    public function focus($xpath)
    {
        $this->browser->focus(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::blur()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function blur($xpath)
    {
        throw new UnsupportedDriverActionException('Blur is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        switch ($modifier) {
            case 'ctrl':
                throw new UnsupportedDriverActionException('Ctrl key is not supported by %s', $this);
            case 'alt':
                $this->browser->altKeyDown();
                break;
            case 'shift':
                $this->browser->shiftKeyDown();
                break;
            case 'meta':
                $this->browser->metaKeyDown();
                break;
        }

        $this->browser->keyPress(SeleniumLocator::xpath($xpath), $char);

        switch ($modifier) {
            case 'ctrl':
                throw new UnsupportedDriverActionException('Ctrl key is not supported by %s', $this);
            case 'alt':
                $this->browser->altKeyUp();
                break;
            case 'shift':
                $this->browser->shiftKeyUp();
                break;
            case 'meta':
                $this->browser->metaKeyUp();
                break;
        }
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        switch ($modifier) {
            case 'ctrl':
                throw new UnsupportedDriverActionException('Ctrl key is not supported by %s', $this);
            case 'alt':
                $this->browser->altKeyDown();
                break;
            case 'shift':
                $this->browser->shiftKeyDown();
                break;
            case 'meta':
                $this->browser->metaKeyDown();
                break;
        }

        $this->browser->keyDown(SeleniumLocator::xpath($xpath), $char);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::keyPress()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        switch ($modifier) {
            case 'ctrl':
                throw new UnsupportedDriverActionException('Ctrl key is not supported by %s', $this);
            case 'alt':
                $this->browser->altKeyUp();
                break;
            case 'shift':
                $this->browser->shiftKeyUp();
                break;
            case 'meta':
                $this->browser->metaKeyUp();
                break;
        }

        $this->browser->keyUp(SeleniumLocator::xpath($xpath), $char);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::executeScript()
     */
    public function executeScript($script)
    {
        $this->browser->runScript($script);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::evaluateScript()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function evaluateScript($script)
    {
        throw new UnsupportedDriverActionException('Evaluate script is not supported by %s', $this);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::wait()
     */
    public function wait($time, $condition)
    {
        $this->browser->waitForCondition($condition, $time);
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::isVisible()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function isVisible($xpath)
    {
        return $this->browser->isVisible(SeleniumLocator::xpath($xpath));
    }

    /**
     * @see Behat\Mink\Driver\DriverInterface::dragTo()
     *
     * @throws  Behat\Mink\Exception\UnsupportedDriverActionException   action is not supported by this driver
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $this->browser->dragAndDropToObject(SeleniumLocator::xpath($sourceXpath), SeleniumLocator::xpath($destinationXpath));
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
        $crawler = new \Symfony\Component\DomCrawler\Crawler('<html>'.$this->browser->getHtmlSource().'</html>');

        if (null === $crawler) {
            throw new DriverException('Crawler can\'t be initialized. Did you started driver?');
        }

        return $crawler;
    }
}
