<?php

namespace Behat\Mink\Integration;

use Symfony\Component\Finder\Finder;

use Behat\Behat\Context\ClosuredContextInterface as ClosuredContext,
    Behat\Behat\Context\TranslatedContextInterface as TranslatedContext,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\Pending;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

use Goutte\Client as GoutteClient;

use Behat\SahiClient\Connection as SahiConnection,
    Behat\SahiClient\Client as SahiClient;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

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
class MinkContext extends BehatContext implements ClosuredContext,  TranslatedContext
{
    public $parameters = array();

    /**
     * Initializes Mink environment.
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = array_merge(
            array(
                'default_session'   => 'goutte',
                'start_url'         => '',
                'browser'           => 'firefox',
                'goutte'            => array(),
                'sahi'              => array(),
            ),
            $parameters
        );
        $world = $this;

        $world->sessions  = array();
        $world->getPathTo = function($path) use($world) {
            $startUrl = rtrim($world->parameters['start_url'], '/') . '/';

            return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
        };
    }

    /**
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        static $mink;

        if (null === $mink) {
            $mink = new Mink();
            $this->registerMinkSessions($mink);
        }

        return $mink;
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
     * Registers sessions on mink.
     *
     * @param   Behat\Mink\Mink $mink
     */
    private function registerMinkSessions(Mink $mink)
    {
        $browser = $this->parameters['browser'];

        $config  = $this->parameters['goutte'];
        $goutte = new GoutteClient(
            isset($config['zend_config'])       ? $config['zend_config']        : array(),
            isset($config['server_parameters']) ? $config['server_parameters']  : array()
        );
        $mink->registerSession('goutte', new Session(new GoutteDriver($goutte)));

        $config = $this->parameters['sahi'];
        $client = new SahiClient(new SahiConnection(
            isset($config['sid'])   ? $config['sid']    : uniqid(),
            isset($config['host'])  ? $config['host']   : 'localhost',
            isset($config['port'])  ? $config['port']   : 9999
        ));
        $mink->registerSession('sahi', new Session(new SahiDriver($browser, $client)));

        foreach ($this->sessions as $name => $session) {
            $mink->registerSession($name, $session);
        }
    }

    public function getStepDefinitionResources()
    {
        $finder = new Finder();

        return $finder->files()->name('*.php')->in(__DIR__ . '/steps');
    }

    public function getHookDefinitionResources()
    {
        return array(__DIR__ . '/support/hooks.php');
    }

    public function getTranslationResources()
    {
        $finder = new Finder();

        return $finder->files()->name('*.xliff')->in(__DIR__ . '/steps/i18n');
    }

    public function __call($name, array $args)
    {
        if (isset($this->$name) && is_callable($this->$name)) {
            return call_user_func_array($this->$name, $args);
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Call to undefined method ' . get_class($this) . '::' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_ERROR
            );
        }
    }
}
