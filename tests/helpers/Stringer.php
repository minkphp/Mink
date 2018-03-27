<?php

namespace Behat\Mink\Tests\helpers;

/**
 * Class Stringer
 *
 * A container class to hold a string.
 *
 * @package Behat\Mink\Tests\helpers
 */
class Stringer
{

  /**
   * Internal storage.
   *
   * @var string
   */
  protected $content;

  /**
   * Stringer constructor.
   *
   * @param $content
   */
  public function __construct($content)
  {
    $this->content = $content;
  }

  /**
   * Returns in the wrapped string.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->content;
  }

}
