<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\PHPUnit\TestCase;

abstract class DriverTest extends TestCase
{
    /**
     * Returns path to test.
     *
     * @param   string  $path
     *
     * @return  string
     */
    protected function pathTo($path)
    {
        return $_SERVER['WEB_FIXTURES_HOST'].$path;
    }
}
