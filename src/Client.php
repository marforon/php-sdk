<?php

namespace Locardi\PhpSdk;

use Locardi\PhpSdk\Authentication\JwtAuthentication;
use Locardi\PhpSdk\Exception\ClientException;
use Locardi\PhpSdk\HttpClient\Client as HttpClient;
use Locardi\PhpSdk\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class Client
{
    private $endpoint;

    private $auth;

    private $httpClient;

    private $serializer;

    public function __construct($endpoint, JwtAuthentication $auth, HttpClient $httpClient, SerializerInterface $serializer)
    {
        $this->endpoint = $endpoint;
        $this->auth = $auth;
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function send(array $data)
    {
        $content = $this
            ->serializer
            ->serialize($data)
        ;

        $request = new Request(
            array(), // query
            array(), // request
            array(), // attributes
            array(), //cookies
            array(), // files
            array(), // server
            $content
        );

        $request->setMethod(Request::METHOD_POST);

        $request
            ->headers
            ->set('Content-Type', $this->serializer->getHttpHeaderContentType())
        ;

        $request
            ->headers
            ->set('Content-Length', strlen($content))
        ;

        $this
            ->auth
            ->updateRequest($request)
        ;

        $response = $this
            ->httpClient
            ->send($this->endpoint, $request)
        ;

        switch ($response->getStatusCode()) {
            case 201:
                // all good
                break;
            case 400: // bad request
                throw new ClientException(sprintf('Bad request. %s.', $response->getContent()));
            case 401: // unauthorized
                throw new ClientException('Unauthorized');
            case 404:
                throw new ClientException(sprintf('API %s not found.', $this->endpoint));
            default:
                throw new ClientException(sprintf('API response with status code %s.', $response->getStatusCode()));
        }
    }
}