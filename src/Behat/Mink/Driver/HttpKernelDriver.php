<?php

namespace Behat\Mink\Driver;

use Symfony\Component\HttpKernel\Client;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Symfony2 HttpKernel driver.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class HttpKernelDriver extends GoutteDriver
{
    /**
     * Initializes Goutte driver.
     *
     * @param Client $client HttpKernel client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * {@inheritdoc}
     *
     * removes "*.php/" from urls and then passes it to GoutteDriver::visit().
     */
    public function visit($url)
    {
        $url = preg_replace('/^(https?\:\/\/[^\/]+)(\/[^\/]+\.php)?/', '$1', $url);

        parent::visit($url);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->getClient()->getKernel()->shutdown();
        $this->getClient()->getKernel()->boot();

        parent::reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setBasicAuth($user, $password)
    {
        $this->getClient()->setServerParameter('PHP_AUTH_USER', $user);
        $this->getClient()->setServerParameter('PHP_AUTH_PW', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestHeader($name, $value)
    {
        switch (strtolower($name)) {
            case 'accept':
                $name = 'HTTP_ACCEPT';
                break;
            case 'accept-charset':
                $name = 'HTTP_ACCEPT_CHARSET';
                break;
            case 'accept-encoding':
                $name = 'HTTP_ACCEPT_ENCODING';
                break;
            case 'accept-language':
                $name = 'HTTP_ACCEPT_LANGUAGE';
                break;
            case 'connection':
                $name = 'HTTP_CONNECTION';
                break;
            case 'host':
                $name = 'HTTP_HOST';
                break;
            case 'user-agent':
                $name = 'HTTP_USER_AGENT';
                break;
            case 'authorization':
                $name = 'PHP_AUTH_DIGEST';
                break;
        }

        $this->getClient()->setServerParameter($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHeaders()
    {
        $headers         = array();
        $responseHeaders = trim($this->getClient()->getResponse()->headers->__toString());

        foreach (explode("\r\n", $responseHeaders) as $header) {
            list($name, $value) = array_map('trim', explode(':', $header, 2));

            if (isset($headers[$name])) {
                $headers[$name]   = array($headers[$name]);
                $headers[$name][] = $value;
            } else {
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getClient()->getResponse()->getStatusCode();
    }
}
