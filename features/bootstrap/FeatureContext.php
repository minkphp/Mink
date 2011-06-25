<?php

use Behat\Mink\Behat\Context\MinkContext;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\Pending;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once __DIR__ . '/../../autoload.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->addSubcontext(new MinkContext($parameters));
    }
}
