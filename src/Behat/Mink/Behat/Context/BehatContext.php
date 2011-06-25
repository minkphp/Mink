<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Context\BehatContext as BaseContext;

use Behat\Mink\Mink;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat context for Mink.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class BehatContext extends BaseContext
{
    private $mink;
    private $parameters = array();

    /**
     * Initializes Mink environment.
     */
    public function __construct(Mink $mink, array $parameters = array())
    {
        $this->mink = $mink;
        $this->parameters = array_merge(array('start_url' => 'http://localhost'), $parameters);
    }

    /**
     * Locates url based on provided path.
     *
     * @param   string  $path
     *
     * @return  string
     */
    public function locatePath($path)
    {
        $startUrl = rtrim($this->getParameter('start_url'), '/') . '/';

        return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return $this->mink;
    }

    /**
     * Returns current Mink session.
     *
     * @param   string|null name of the session OR active session will be used
     *
     * @return  Behat\Mink\Session
     */
    public function getSession($name = null)
    {
        return $this->mink->getSession($name);
    }

    /**
     * Returns all context parameters.
     *
     * @return  array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns context parameter.
     *
     * @param   string  $name
     *
     * @return  mixed
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }
}
