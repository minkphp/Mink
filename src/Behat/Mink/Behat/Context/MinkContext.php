<?php

namespace Behat\Mink\Behat\Context;

use Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

use Goutte\Client as GoutteClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink context for Behat testing tool.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkContext extends BehatContext implements TranslatedContextInterface
{
    private static $minkInstance;
    private $parameters;

    /**
     * Initializes Mink environment.
     *
     * @param   array   $parameters     list of context parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = array_merge(array(
            'default_session' => 'goutte',
            'base_url'        => 'http://localhost',
            'browser'         => 'firefox',
            'goutte' => array(
                'zend_config'       => array(),
                'server_parameters' => array()
            ),
            'sahi' => array(
                'sid'  => uniqid(),
                'host' => 'localhost',
                'port' => 9999
            )
        ), $parameters);

        if (null === self::$minkInstance) {
            self::$minkInstance = new Mink();
            $this->registerSessions(self::$minkInstance);
            self::$minkInstance->setDefaultSessionName($this->getParameter('default_session'));
        }

        foreach ($this->getStepsContexts() as $context) {
            $this->addSubcontext($context);
        }
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
        $startUrl = rtrim($this->getParameter('base_url'), '/') . '/';

        return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return self::$minkInstance;
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
        return $this->getMink()->getSession($name);
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

    /**
     * Returns step definition subcontexts.
     *
     * @return  array
     */
    protected function getStepsContexts()
    {
        return array(
            new NavigationContext($this),
            new PageContext($this),
            new FormContext($this)
        );
    }

    /**
     * Registers Mink sessions on it's initialization.
     *
     * @param   Behat\Mink\Mink     $mink   Mink manager instance
     */
    protected function registerSessions(Mink $mink)
    {
        $mink->registerSession('goutte', new Session($this->initGoutteDriver($this->getParameter('goutte'))));
        $mink->registerSession('sahi',   new Session($this->initSahiDriver(
            $this->getParameter('browser'), $this->getParameter('sahi')
        )));
    }

    /**
     * Initizalizes and returns new GoutteDriver instance.
     *
     * @param   array   $connection     connection settings
     *
     * @return  Behat\Mink\Driver\GoutteDriver
     */
    protected function initGoutteDriver(array $connection)
    {
        return new GoutteDriver(
            new GoutteClient($connection['zend_config'], $connection['server_parameters'])
        );
    }

    /**
     * Initizalizes and returns new SahiDriver instance.
     *
     * @param   string  $browser        browser name to use (default = firefox)
     * @param   array   $connection     connection settings
     *
     * @return  Behat\Mink\Driver\SahiDriver
     */
    protected function initSahiDriver($browser, array $connection)
    {
        return new SahiDriver(
            $browser,
            new SahiClient(new SahiConnection($connection['sid'], $connection['host'], $connection['port']))
        );
    }

    /**
     * @BeforeScenario
     */
    public function prepareMinkSession($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $session  = $this->getParameter('default_session');

        foreach ($scenario->getTags() as $tag) {
            if ('javascript' === $tag) {
                $session = 'sahi';
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        $this->getMink()->setDefaultSessionName($session);
    }

    /**
     * Returns list of definition translation resources paths.
     *
     * @return  array
     */
    public function getTranslationResources()
    {
        return array(
            __DIR__ . '/translations/ru.xliff',
            __DIR__ . '/translations/fr.xliff',
            __DIR__ . '/translations/ja.xliff',
            __DIR__ . '/translations/es.xliff',
        );
    }
}
