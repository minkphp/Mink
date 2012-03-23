<?php

namespace Behat\Mink\Driver\Goutte;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar as BaseJar;

/**
 * CookieJar.
 */
class CookieJar extends BaseJar
{
    /**
     * {@inheritdoc}
     */
    public function updateFromResponse(Response $response, $uri = null)
    {
        foreach ($response->getHeader('Set-Cookie', false) as $cookie) {
            $this->set(Cookie::fromString($cookie, $uri));
        }
    }
}
