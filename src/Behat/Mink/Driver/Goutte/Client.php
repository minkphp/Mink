<?php

namespace Behat\Mink\Driver\Goutte;

use Symfony\Component\BrowserKit\Response;

use Goutte\Client as BaseClient;

use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Goutte extension point.
 */
class Client extends BaseClient
{
    protected function doRequest($request)
    {
        foreach ($request->getServer() as $key => $val) {
            $key = ucfirst(strtolower(str_replace(array('_', 'HTTP-'), array('-', ''), $key)));
            $this->headers[$key] = $val;
        }

        try {
            $response = parent::doRequest($request);
        } catch (BadResponseException $e) {
            return $this->createResponse($e->getResponse());
        }

        return $response;
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
