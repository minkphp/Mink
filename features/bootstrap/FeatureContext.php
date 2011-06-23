<?php

use Behat\Mink\Integration\MinkContext;

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
class FeatureContext extends MinkContext
{
}
