<?php

namespace Behat\Mink\Behat\Context;

use Behat\Mink\Mink;

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
 * Navigation actions context.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NavigationContext extends ActionsContext
{
    /**
     * @Given /^(?:|I )am on (?P<page>.+)$/
     * @When /^(?:|I )go to (?P<page>.+)$/
     */
    public function visit($page)
    {
        $this->getSession()->visit($this->locatePath($page));
    }

    /**
     * @Then /^(?:|I )should be on (?P<page>.+)$/
     */
    public function assertPageAddress($page)
    {
        assertEquals(
            parse_url($this->locatePath($page), PHP_URL_PATH),
            parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH)
        );
    }

    /**
     * @Then /^the url should match (?P<pattern>.+)$/
     */
    public function assertUrlRegExp($pattern)
    {
        if (preg_match('/^\/.*\/$/', $pattern)) {
            assertRegExp($pattern, parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH));
        } else {
            $this->assertPageAddress($pattern);
        }
    }

    /**
     * @Then /the response status code should be (?P<code>\d+)/
     */
    public function assertResponseStatus($code)
    {
        assertEquals($this->getSession()->getStatusCode(), $code);
    }
}
