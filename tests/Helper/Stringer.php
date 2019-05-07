<?php

namespace Behat\Mink\Tests\Helper;

/**
 * A container class to hold a string.
 */
class Stringer
{

    /**
     * Internal storage.
     *
     * @var string
     */
    private $content;

    /**
     * Stringer constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the wrapped string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }

}
