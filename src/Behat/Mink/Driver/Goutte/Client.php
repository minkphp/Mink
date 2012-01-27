<?php

namespace Behat\Mink\Driver\Goutte;

use Symfony\Component\BrowserKit\Request;
use Goutte\Client as BaseClient;

/**
 * Goutte extension point.
 */
class Client extends BaseClient
{
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
