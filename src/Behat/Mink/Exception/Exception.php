<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Exception;

use Behat\Mink\Driver\DriverInterface;

/**
 * Mink base exception class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Exception extends \Exception
{
    private $driver;

    /**
     * Initializes Mink exception.
     *
     * @param string          $message
     * @param DriverInterface $driver
     * @param integer         $code
     * @param \Exception      $previous
     */
    public function __construct($message, DriverInterface $driver = null, $code = 0, \Exception $previous = null)
    {
        $this->driver = $driver;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return DriverInterface
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * Prepends every line in a string with pipe (|).
     *
     * @param string $string
     *
     * @return string
     */
    protected function pipeString($string)
    {
        return '|  ' . strtr($string, array("\n" => "\n|  "));
    }

    /**
     * Removes response header/footer, letting only <body /> content and trim it.
     *
     * @param string  $string response content
     * @param integer $count  trim count
     *
     * @return string
     */
    protected function trimBody($string, $count = 1000)
    {
        $string = preg_replace(array('/^.*<body>/s', '/<\/body>.*$/s'), array('<body>', '</body>'), $string);
        $string = $this->trimString($string, $count);

        return $string;
    }

    /**
     * Trims string to specified number of chars.
     *
     * @param string  $string response content
     * @param integer $count  trim count
     *
     * @return string
     */
    protected function trimString($string, $count = 1000)
    {
        $string = trim($string);

        if ($count < mb_strlen($string)) {
            return mb_substr($string, 0, $count - 3) . '...';
        }

        return $string;
    }

    /**
     * Returns response information string.
     *
     * @return string
     */
    protected function getResponseInfo()
    {
        $driver = basename(str_replace('\\', '/', get_class($this->driver)));

        $info = '+--[ ';
        if (!in_array($driver, array('SahiDriver', 'SeleniumDriver', 'Selenium2Driver'))) {
            $info .= 'HTTP/1.1 '.$this->driver->getStatusCode().' | ';
        }
        $info .= $this->driver->getCurrentUrl().' | '.$driver." ]\n|\n";

        return $info;
    }
}
