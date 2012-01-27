<?php

namespace Behat\Mink\Driver\Goutte;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\History;
use Goutte\Client as BaseClient;

/**
 * Goutte extension point.
 */
class Client extends BaseClient
{
    public function __construct(array $zendConfig = array(), array $server = array(), History $history = null, CookieJar $cookieJar = null)
    {
        if (null === $cookieJar) {
            $cookieJar = new CookieJar();
        }

        parent::__construct($zendConfig, $server, $history, $cookieJar);
    }

    protected function createClient(Request $request)
    {
        // create new request without content body
        return parent::createClient(new Request(
            $request->getUri(),
            $request->getMethod(),
            $request->getParameters(),
            $request->getFiles(),
            $request->getCookies(),
            $request->getServer()
        ));
    }
}
