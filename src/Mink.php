<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink;

/**
 * Mink session manager.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Mink
{
    /**
     * @var null|string
     */
    private $defaultSessionName;

    /**
     * Sessions.
     *
     * @var Session[]
     */
    private $sessions = array();

    /**
     * Initializes manager.
     *
     * @param array<string, Session> $sessions
     */
    public function __construct(array $sessions = array())
    {
        foreach ($sessions as $name => $session) {
            $this->registerSession($name, $session);
        }
    }

    /**
     * Stops all started sessions.
     */
    public function __destruct()
    {
        $this->stopSessions();
    }

    /**
     * Registers new session.
     *
     * @param string  $name
     * @param Session $session
     *
     * @return void
     */
    public function registerSession(string $name, Session $session)
    {
        $name = strtolower($name);

        $this->sessions[$name] = $session;
    }

    /**
     * Checks whether session with specified name is registered.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSession(string $name)
    {
        return isset($this->sessions[strtolower($name)]);
    }

    /**
     * Sets default session name to use.
     *
     * @param string $name name of the registered session
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setDefaultSessionName(string $name)
    {
        $name = strtolower($name);

        if (!isset($this->sessions[$name])) {
            throw new \InvalidArgumentException(sprintf('Session "%s" is not registered.', $name));
        }

        $this->defaultSessionName = $name;
    }

    /**
     * Returns default session name or null if none.
     *
     * @return null|string
     */
    public function getDefaultSessionName()
    {
        return $this->defaultSessionName;
    }

    /**
     * Returns registered session by its name or default one.
     *
     * @param string|null $name session name
     *
     * @return Session
     *
     * @throws \InvalidArgumentException If the named session is not registered
     */
    public function getSession(?string $name = null)
    {
        return $this->locateSession($name);
    }

    /**
     * Checks whether a named session (or the default session) has already been started.
     *
     * @param string|null $name session name - if null then the default session will be checked
     *
     * @return bool whether the session has been started
     *
     * @throws \InvalidArgumentException If the named session is not registered
     */
    public function isSessionStarted(?string $name = null)
    {
        $session = $this->locateSession($name);

        return $session->isStarted();
    }

    /**
     * Returns session asserter.
     *
     * @param Session|string|null $session session object or name
     *
     * @return WebAssert
     */
    public function assertSession($session = null)
    {
        if (!($session instanceof Session)) {
            $session = $this->getSession($session);
        }

        return new WebAssert($session);
    }

    /**
     * Resets all started sessions.
     *
     * @return void
     */
    public function resetSessions()
    {
        foreach ($this->sessions as $session) {
            if ($session->isStarted()) {
                $session->reset();
            }
        }
    }

    /**
     * Restarts all started sessions.
     *
     * @return void
     */
    public function restartSessions()
    {
        foreach ($this->sessions as $session) {
            if ($session->isStarted()) {
                $session->restart();
            }
        }
    }

    /**
     * Stops all started sessions.
     *
     * @return void
     */
    public function stopSessions()
    {
        foreach ($this->sessions as $session) {
            if ($session->isStarted()) {
                $session->stop();
            }
        }
    }

    /**
     * Returns the named or default session without starting it.
     *
     * @param string|null $name session name
     *
     * @return Session
     *
     * @throws \InvalidArgumentException If the named session is not registered
     */
    protected function locateSession(?string $name = null)
    {
        $name = $name ? strtolower($name) : $this->defaultSessionName;

        if (null === $name) {
            throw new \InvalidArgumentException('Specify session name to get');
        }

        if (!isset($this->sessions[$name])) {
            throw new \InvalidArgumentException(sprintf('Session "%s" is not registered.', $name));
        }

        $session = $this->sessions[$name];

        return $session;
    }
}
