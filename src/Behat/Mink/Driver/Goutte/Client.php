<?php

namespace Behat\Mink\Driver\Goutte;

use Goutte\Client as BaseClient;
use Symfony\Component\BrowserKit\Response;
use Guzzle\Http\Message\Response as GuzzleResponse;

/**
 * Goutte extension point.
 */
class Client extends BaseClient
{
    protected function doRequest($request)
    {
        foreach ($request->getServer() as $key => $val) {
            $key = ucfirst(strtolower(str_replace(array('_', 'HTTP-'), array('-', ''), $key)));

            if (!isset($this->headers[$key])) {
                $this->headers[$key] = $val;
            }
        }

        return parent::doRequest($request);
    }

    protected function createResponse(GuzzleResponse $response)
    {
        $body        = $response->getBody(true);
        $statusCode  = $response->getStatusCode();
        $headers     = $response->getHeaders()->getAll();
        $contentType = $response->getContentType();

        if (!$contentType || false === strpos($contentType, 'charset=')) {
            if (preg_match('/\<meta[^\>]+charset *= *["\']?([a-zA-Z\-0-9]+)/', $body, $matches)) {
                $contentType .= ';charset='.$matches[1];
            }
        }
        $headers['Content-Type'] = $contentType;

        return new Response($body, $statusCode, $headers);
    }
}
