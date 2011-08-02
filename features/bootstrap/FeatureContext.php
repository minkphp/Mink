<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\Pending;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

if (file_exists(__DIR__ . '/../../autoload.php')) {
    require_once __DIR__ . '/../../autoload.php';
} else {
    require_once __DIR__ . '/../../autoload.php.dist';
}

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('mink', new Behat\Mink\Behat\Context\MinkContext($parameters));
    }
}
