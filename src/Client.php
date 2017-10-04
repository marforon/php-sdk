<?php

namespace Locardi\PhpSdk;

use Locardi\PhpSdk\Api\ApiInterface;
use Locardi\PhpSdk\Authentication\JwtAuthentication;
use Locardi\PhpSdk\Exception\ClientException;
use Locardi\PhpSdk\HttpClient\CurlClient;
use Locardi\PhpSdk\HttpClient\HttpClientInterface;
use Locardi\PhpSdk\Serializer\SerializerInterface;
use Zend\Diactoros\Request;

class Client
{
    private $endpoint;

    private $auth;

    private $client;

    private $serializer;

    public function __construct(
        $endpoint,
        JwtAuthentication $auth,
        HttpClientInterface $client,
        SerializerInterface $serializer
    ) {
        $this->endpoint = $endpoint;
        $this->auth = $auth;
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function send(ApiInterface $api, array $data)
    {
        $url = sprintf('%s%s', $this->endpoint, $api->getPath());

        $response = $this
            ->client
            ->send($url, $this->buildRequest($api, $data))
        ;

        if ($this->client instanceof CurlClient) {
            if ($response->getStatusCode() == 401) {
                $response = $this
                    ->client
                    ->send($url, $this->buildRequest($api, $data, true))
                ;
            }
        } else {
            $count = $this
                ->auth
                ->increaseUsageCounter()
            ;
            if ($count >= 5) {
                $this
                    ->auth
                    ->destroy()
                ;
            }
        }

        switch ($response->getStatusCode()) {
            case 200:
            case 201:
                // all good
                break;
            case 400: // bad request
                throw new ClientException(sprintf('Bad request. %s.', $response->getBody()));
            case 401: // unauthorized
                // it might be that the token is wrong or no longer valid
                throw new ClientException('Unauthorized, the token could not be validated.');
            case 404:
                throw new ClientException(sprintf('API %s not found.', $this->endpoint));
            default:
                throw new ClientException(sprintf('API response with status code %s.', $response->getStatusCode()));
        }
    }

    private function buildRequest(ApiInterface $api, array $data, $forceNewToken = false)
    {
        $content = $this
            ->serializer
            ->serialize($data)
        ;

        $headers = [
            'Content-Type' => $this->serializer->getHttpHeaderContentType(),
            'Content-Length' => strlen($content),
        ];

        $headers = array_merge($headers, $this->auth->getAuthHeaders($forceNewToken));

        $request = new Request(null, $api->getMethod(), 'php://temp', $headers);

        $request
            ->getBody()
            ->write($content)
        ;

        return $request;
    }
}