<?php

namespace Behat\Mink\Tests;

use PHPUnit\Framework\TestCase;

/**
 * This is a BC Layer to support phpunit 4.8 needed for php <= 5.5.
 */
if (class_exists('PHPUnit_Runner_Version')
    && version_compare(\PHPUnit_Runner_Version::id(), '5', '<')) {
    class BaseTestCase extends TestCase
    {
        public static function assertIsArray($value, $message = '')
        {
            self::assertTrue(is_array($value), $message);
        }

        public function expectException($exception, $message = null)
        {
            $this->setExpectedException($exception, $message);
        }
    }
} else {
    class BaseTestCase extends TestCase
    {
        public function assertScalar($value, $message = '')
        {
            $this->assertTrue(is_scalar($value), $message);
        }
    }
}
