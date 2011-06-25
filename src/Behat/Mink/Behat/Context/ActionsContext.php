<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Context\BehatContext;

use Behat\Mink\Mink;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink actions context.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class ActionsContext extends BehatContext
{
    private $coreContext;

    /**
     * Initializes Mink environment.
     *
     * @param   Behat\Mink\Behat\Context\MinkContext    $coreContext    core context
     */
    public function __construct(MinkContext $coreContext)
    {
        $this->coreContext = $coreContext;
    }

    /**
     * Locates url, based on provided path.
     *
     * @param   string  $path
     *
     * @return  string
     */
    public function locatePath($path)
    {
        return $this->coreContext->locatePath($path);
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return $this->coreContext->getMink();
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
        return $this->coreContext->getSession($name);
    }

    /**
     * Returns all context parameters.
     *
     * @return  array
     */
    public function getParameters()
    {
        return $this->coreContext->getParameters();
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
        return $this->coreContext->getParameter($name);
    }
}
