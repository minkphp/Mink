<?php

namespace Behat\Mink\Behat\Context;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Mink\Mink,
    Behat\Mink\Exception\ElementNotFoundException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Page actions context.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PageContext extends ActionsContext
{
    /**
     * @Then /^(?:|I )should see "(?P<text>[^"]*)"$/
     */
    public function assertPageContainsText($text)
    {
        assertRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getPlainText());
    }

    /**
     * @Then /^(?:|I )should not see "(?P<text>[^"]*)"$/
     */
    public function assertPageNotContainsText($text)
    {
        assertNotRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getPlainText());
    }

    /**
     * @Then /^the response should contain "(?P<text>[^"]*)"$/
     */
    public function assertResponseContains($text)
    {
        assertRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getContent());
    }

    /**
     * @Then /^the response should not contain "(?P<text>[^"]*)"$/
     */
    public function assertResponseNotContains($text)
    {
        assertNotRegExp('/'.preg_quote($text, '/').'/', $this->getSession()->getPage()->getContent());
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>[^"]*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementContainsText($element, $text)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertContains($text, preg_replace('/\s+/', ' ', str_replace("\n", '', $node->getPlainText())));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>[^"]*)"$/
     */
    public function assertElementContains($element, $value)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertContains($value, preg_replace('/\s+/', ' ', str_replace("\n", '', $node->getText())));
    }

    /**
     * @Then /^(?:|I )should see "(?P<element>[^"]*)" element$/
     */
    public function assertElementOnPage($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }
    }

    /**
     * @Then /^(?:|I )should not see "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotOnPage($element)
    {
        assertNull($this->getSession()->getPage()->find('css', $element));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should link to (?P<href>.*)$/
     */
    public function assertElementHref($element, $href)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        $hrefParts  = parse_url($href);
        $href       = array_merge(parse_url($this->getParameter('base_url')), $hrefParts);

        assertEquals($href['scheme'].'://'.$href['host'].$href['path'], $node->getAttribute('href'));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should have a "(?P<attribute>[a-zA-Z\-\_]*)" attribute of "(?P<value>[^"]*)"$/
     */
    public function assertElementAttributeValue($element, $attribute, $value)
    {
        $node = $this->getSession()->getPage()->find('css', $element);

        if (null === $node) {
            throw new ElementNotFoundException('element', $element);
        }

        assertEquals($value, $node->getAttribute($attribute));
    }

    /**
     * @Then /^print last response$/
     */
    public function printLastResponse()
    {
        $this->printDebug($this->getSession()->getPage()->getContent());
    }
}
