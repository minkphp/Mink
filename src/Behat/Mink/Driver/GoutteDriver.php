<?php

namespace Behat\Mink\Driver;

use Goutte\Client,
    Symfony\Component\DomCrawler\Crawler,
    Symfony\Component\DomCrawler\Field\ChoiceFormField;

use Behat\Mink\Session,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\DriverException,
    Behat\Mink\Exception\ElementNotFoundException;

class GoutteDriver implements DriverInterface
{
    private $session;
    private $client;
    private $forms = array();

    public function __construct(Client $client = null)
    {
        if (null === $client) {
            $client = new Client();
        }

        $this->client = $client;
        $this->client->followRedirects(true);
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function visit($url)
    {
        $this->client->request('GET', $url);
        $this->forms = array();
    }

    public function reset()
    {
        $this->client->restart();
        $this->forms = array();
    }

    public function getCurrentUrl()
    {
        return $this->client->getRequest()->getUri();
    }

    public function getResponseHeaders()
    {
        return $this->client->getResponse()->getHeaders();
    }

    public function getStatusCode()
    {
        return $this->client->getResponse()->getStatus();
    }

    public function getContent()
    {
        return $this->client->getResponse()->getContent();
    }

    public function find($xpath)
    {
        $nodes = $this->getCrawler()->filterXPath($xpath);

        $elements = array();
        foreach ($nodes as $i => $node) {
            $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $i + 1), $this->session);
        }

        return $elements;
    }

    public function getTagName($xpath)
    {
        return $this->getCrawlerNode($this->getCrawler()->filterXPath($xpath)->eq(0))->nodeName;
    }

    public function getText($xpath)
    {
        return $this->getCrawler()->filterXPath($xpath)->eq(0)->text();
    }

    public function getAttribute($xpath, $attr)
    {
        $value = $this->getCrawler()->filterXPath($xpath)->eq(0)->attr($attr);

        return '' !== $value ? $value : null;
    }

    public function getValue($xpath)
    {
        $field = $this->getField($xpath);
        $value = $field->getValue();

        if ($field instanceof ChoiceFormField && 'checkbox' === $field->getType()) {
            $value = '1' == $value;
        }

        return $value;
    }

    public function setValue($xpath, $value)
    {
        $this->getField($xpath)->setValue($value);
    }

    public function check($xpath)
    {
        $this->getField($xpath)->tick();
    }

    public function uncheck($xpath)
    {
        $this->getField($xpath)->untick();
    }

    public function selectOption($xpath, $value)
    {
        $this->getField($xpath)->select($value);
    }

    public function click($xpath)
    {
        if (!count($nodes = $this->getCrawler()->filterXPath($xpath))) {
            throw new ElementNotFoundException('link or button', $xpath);
        }
        $node = $nodes->eq(0);

        if ('a' === $this->getCrawlerNode($node)->nodeName) {
            $this->client->click($node->link());
        } else {
            $buttonForm = $node->form();
            foreach ($this->forms as $form) {
                if ($buttonForm->getFormNode()->getLineNo() === $form->getFormNode()->getLineNo()) {
                    $buttonForm = $form;

                    break;
                }
            }
            $this->client->submit($buttonForm);
        }

        $this->forms = array();
    }

    public function isChecked($xpath)
    {
        return true === $this->getValue($xpath);
    }

    public function attachFile($xpath, $path)
    {
        $this->getField($xpath)->upload($path);
    }

    public function executeScript($script)
    {
        throw new DriverException('JS execution is not supported by ' . get_class($this));
    }

    public function evaluateScript($script)
    {
        throw new DriverException('JS execution is not supported by ' . get_class($this));
    }

    public function isVisible($xpath)
    {
        throw new DriverException('isVisible is not supported by ' . get_class($this));
    }

    public function triggerEvent($xpath, $event)
    {
        throw new DriverException('triggerEvent is not supported by ' . get_class($this));
    }

    public function dragTo($sourceXpath, $destinationXpath)
    {
        throw new DriverException('dragTo is not supported by ' . get_class($this));
    }

    private function getCrawlerNode(Crawler $crawler, $num = 0)
    {
        foreach ($crawler as $i => $node) {
            if ($num == $i) {
                return $node;
            }
        }

        return null;
    }

    private function getField($xpath)
    {
        if (!count($crawler = $this->getCrawler()->filterXPath($xpath))) {
            throw new ElementNotFoundException('field', $xpath);
        }

        $fieldNode  = $this->getCrawlerNode($crawler);
        $formNode   = $fieldNode;

        do {
            // use the ancestor form element
            if (null === $formNode = $formNode->parentNode) {
                throw new ElementNotFoundException('form');
            }
        } while ('form' != $formNode->nodeName);

        // check if form already exists
        foreach ($this->forms as $form) {
            if ($formNode->getLineNo() === $form->getFormNode()->getLineNo()) {
                return $form[$fieldNode->getAttribute('name')];
            }
        }

        // find form button
        $buttonNode = $this->findFormButton($formNode);
        if (null === $buttonNode) {
            throw new ElementNotFoundException('form submit button');
        }

        // init form
        $button = new Crawler($buttonNode, $this->client->getRequest()->getUri());
        $this->forms[] = $form = $button->form();

        return $form[$fieldNode->getAttribute('name')];
    }

    private function findFormButton(\DOMNode $form)
    {
        $document   = new \DOMDocument('1.0', 'UTF-8');
        $node       = $document->importNode($form, true);
        $root       = $document->appendChild($document->createElement('_root'));

        $root->appendChild($node);
        $xpath = new \DOMXPath($document);

        foreach ($xpath->query('descendant::input | descendant::button', $root) as $node) {
            if ('button' == $node->nodeName || ('input' == $node->nodeName && in_array($node->getAttribute('type'), array('submit', 'button', 'image')))) {
                return $node;
            }
        }

        return null;
    }

    private function getCrawler()
    {
        $crawler = $this->client->getCrawler();

        if (null === $crawler) {
            throw new DriverException('Crawler can\'t be initialized. Did you opened some page?');
        }

        return $crawler;
    }
}
