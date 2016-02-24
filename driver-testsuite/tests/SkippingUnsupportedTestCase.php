<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;

if (version_compare(\PHPUnit_Runner_Version::id(), '5.0.0', '>=')) {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 5+
     *
     * This code should be moved back to \Behat\Mink\Tests\Driver\TestCase when dropping support for
     * PHP 5.5 and older, as PHPUnit 4 won't be needed anymore.
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends \PHPUnit_Framework_TestCase
    {
        protected function onNotSuccessfulTest($e)
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
} else {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 4
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends \PHPUnit_Framework_TestCase
    {
        protected function onNotSuccessfulTest(\Exception $e)
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
}
