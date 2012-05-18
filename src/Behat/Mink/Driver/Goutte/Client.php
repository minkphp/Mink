<?php

namespace Behat\Mink\Driver\Goutte;

use Symfony\Component\BrowserKit\Response;

use Goutte\Client as BaseClient;

use Guzzle\Http\Message\Response as GuzzleResponse,
    Guzzle\Http\Exception\BadResponseException,
    Guzzle\Http\Exception\CurlException;

/**
 * Goutte extension point.
 */
class Client extends BaseClient
{
    protected function doRequest($request)
    {
        $headers = array();
        foreach ($request->getServer() as $key => $val) {
            $key = ucfirst(strtolower(str_replace(array('_', 'HTTP-'), array('-', ''), $key)));
            if (!isset($headers[$key])) {
                $headers[$key] = $val;
            }
        }

        $guzzleRequest = $this->getClient()->createRequest(
            strtoupper($request->getMethod()),
            $request->getUri(),
            array_merge($this->headers, $headers),
            $request->getParameters()
        );

        if ($this->auth !== null) {
            $guzzleRequest->setAuth(
                $this->auth['user'],
                $this->auth['password'],
                $this->auth['type']
            );
        }

        foreach ($this->getCookieJar()->allRawValues($request->getUri()) as $name => $value) {
            $guzzleRequest->addCookie($name, $value);
        }

        if ('POST' == $request->getMethod()) {
            $postFiles = array();
            foreach ($request->getFiles() as $name => $info) {
                if (isset($info['tmp_name']) && '' !== $info['tmp_name']) {
                    $postFiles[$name] = $info['tmp_name'];
                }
            }
            if (!empty($postFiles)) {
                $guzzleRequest->addPostFiles($postFiles);
            }
        }

        $guzzleRequest->setHeader('User-Agent', $this->server['HTTP_USER_AGENT']);

        $guzzleRequest->getCurlOptions()
            ->set(CURLOPT_FOLLOWLOCATION, false)
            ->set(CURLOPT_MAXREDIRS, 0)
            ->set(CURLOPT_TIMEOUT, 30);

        // Let BrowserKit handle redirects
        try {
            $response = $guzzleRequest->send();
        } catch (CurlException $e) {
            if (!strpos($e->getMessage(), 'redirects')) {
                throw $e;
            }

            $response = $e->getResponse();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        return $this->createResponse($response);
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
